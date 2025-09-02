<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateRecipeEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:generate-embeddings {--force : Force regenerate all embeddings} {--limit=10 : Limit number of recipes to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate embeddings for recipes to enable semantic search';

    protected EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        parent::__construct();
        $this->embeddingService = $embeddingService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Test embedding service
        $testResult = $this->embeddingService->embed("test");
        if (!$testResult) {
            $this->error('Embedding service is not working. Please check connection.');
            return Command::FAILURE;
        }
        $this->info('Embedding service is working!');

        $force = $this->option('force');
        $limit = (int) $this->option('limit');

        $query = Recipe::where('status', 'approved');

        if (!$force) {
            $query->whereNull('embedding_generated_at');
        }

        $recipes = $query->limit($limit)->get();

        if ($recipes->isEmpty()) {
            $this->info('No recipes found to process.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$recipes->count()} recipes...");
        $progressBar = $this->output->createProgressBar($recipes->count());
        $progressBar->start();

        $processed = 0;
        $errors = 0;

        foreach ($recipes as $recipe) {
            try {
                $embedding = $this->embeddingService->embedRecipe($recipe);

                if ($embedding) {
                    $recipe->update([
                        'embedding' => $embedding,
                        'embedding_generated_at' => now()
                    ]);
                    $processed++;
                } else {
                    $errors++;
                    Log::warning("Failed to generate embedding for recipe: {$recipe->id}");
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error("Error generating embedding for recipe {$recipe->id}: " . $e->getMessage());
            }

            $progressBar->advance();

            // Small delay to avoid rate limiting
            usleep(200000); // 0.2 second delay
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Processing completed!");
        $this->info("✅ Successfully processed: {$processed} recipes");

        if ($errors > 0) {
            $this->warn("⚠️  Errors encountered: {$errors} recipes");
        }

        return Command::SUCCESS;
    }
}
