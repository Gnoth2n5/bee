<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EmbeddingService
{
    /**
     * Hugging Face Inference endpoint for feature extraction using multilingual-e5-large.
     */
    private const ENDPOINT = 'https://router.huggingface.co/hf-inference/models/intfloat/multilingual-e5-large/pipeline/feature-extraction';

    /**
     * Generate embedding(s) for the provided input text(s).
     *
     * @param string|array $inputs
     * @param array|null $context Unused placeholder for future query/context patterns (kept for compatibility)
     * @param bool $normalize Whether to L2-normalize the returned vector(s)
     * @return array|null
     */
    public function embed(string|array $inputs, ?array $context = null, bool $normalize = true): ?array
    {
        try {
            $token = \env('EMBED_API_KEY');
            if ($token === '') {
                Log::error('EMBEDDING_TOKEN is not set');
                return null;
            }

            $payloadInputs = is_array($inputs)
                ? array_values(array_filter($inputs, static fn($t) => is_string($t) && trim($t) !== ''))
                : $inputs;

            if ((is_array($payloadInputs) && empty($payloadInputs)) || (!is_array($payloadInputs) && trim((string) $payloadInputs) === '')) {
                return null;
            }

            $response = Http::timeout(30)
                ->withOptions(['verify' => false])
                ->withToken($token)
                ->acceptJson()
                ->post(self::ENDPOINT, [
                    'inputs' => $payloadInputs,
                ]);

            if (!$response->ok()) {
                Log::warning('Embedding API request failed', [
                    'endpoint' => self::ENDPOINT,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $body = $response->json();

            // When sending a batch, the API typically returns a list aligned with inputs
            if (is_array($inputs)) {
                if (!is_array($body)) {
                    Log::error('Unexpected embedding response format for batch input');
                    return null;
                }
                $results = [];
                foreach ($body as $idx => $item) {
                    $vector = $this->toSentenceVector($item);
                    $results[] = $normalize ? $this->l2Normalize($vector) : $vector;
                }
                return $results;
            }

            // Single input
            $vector = $this->toSentenceVector($body);
            return $normalize ? $this->l2Normalize($vector) : $vector;
        } catch (Exception $e) {
            Log::error('Embedding service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Convert raw HF feature-extraction output to a single sentence vector.
     * The API may return either:
     * - 1D array [dim]
     * - 2D array [tokens][dim] -> we mean-pool across tokens
     */
    private function toSentenceVector(mixed $embedding): array
    {
        if (!is_array($embedding) || empty($embedding)) {
            throw new \RuntimeException('Empty or invalid embedding response.');
        }

        $firstKey = array_key_first($embedding);
        $firstVal = $embedding[$firstKey];

        // 1D already pooled
        if (is_numeric($firstVal)) {
            return array_map(static fn($v) => (float) $v, $embedding);
        }

        // 2D: mean-pool over token axis
        if (is_array($firstVal)) {
            return $this->meanPool($embedding);
        }

        throw new \RuntimeException('Unrecognized embedding shape.');
    }

    /**
     * Mean pooling across token embeddings.
     * @param array $tokenEmbeddings [tokens][dim]
     */
    private function meanPool(array $tokenEmbeddings): array
    {
        $tokenCount = count($tokenEmbeddings);
        if ($tokenCount === 0) {
            return [];
        }

        $dim = is_array($tokenEmbeddings[0]) ? count($tokenEmbeddings[0]) : 0;
        if ($dim === 0) {
            return [];
        }

        $sums = array_fill(0, $dim, 0.0);
        foreach ($tokenEmbeddings as $tokenVector) {
            for ($i = 0; $i < $dim; $i++) {
                $sums[$i] += (float) ($tokenVector[$i] ?? 0.0);
            }
        }

        for ($i = 0; $i < $dim; $i++) {
            $sums[$i] /= max(1, $tokenCount);
        }

        return $sums;
    }

    /**
     * L2-normalize a vector.
     */
    private function l2Normalize(array $vector): array
    {
        $norm = 0.0;
        foreach ($vector as $v) {
            $norm += ((float) $v) * ((float) $v);
        }
        $norm = sqrt($norm);
        if ($norm <= 0.0) {
            return $vector;
        }
        return array_map(static fn($v) => (float) $v / $norm, $vector);
    }

    /**
     * Generate embeddings for multiple texts in batch
     */
    public function batchEmbed(array $texts): array
    {
        return $this->embed($texts) ?? [];
    }

    /**
     * Generate embedding for a recipe
     */
    public function embedRecipe($recipe): ?array
    {
        try {
            // Create comprehensive text representation of recipe
            $text = $this->createRecipeText($recipe);
            return $this->embed($text);
        } catch (Exception $e) {
            Log::error('Recipe embedding error', [
                'recipe_id' => $recipe->id ?? 'unknown',
                'message' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Create comprehensive text representation of recipe for embedding
     */
    private function createRecipeText($recipe): string
    {
        $parts = [];

        // Add title (most important)
        if (!empty($recipe->title)) {
            $parts[] = $recipe->title;
            $parts[] = $recipe->title; // Add twice for emphasis
        }

        // Add summary/description
        if (!empty($recipe->summary)) {
            $parts[] = $recipe->summary;
        }

        if (!empty($recipe->description)) {
            $parts[] = strip_tags($recipe->description);
        }

        // Add ingredients
        if (!empty($recipe->ingredients)) {
            $ingredients = is_array($recipe->ingredients) ? $recipe->ingredients : json_decode($recipe->ingredients, true);
            if (is_array($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    if (is_array($ingredient) && isset($ingredient['name'])) {
                        $parts[] = $ingredient['name'];
                    } elseif (is_string($ingredient)) {
                        $parts[] = $ingredient;
                    }
                }
            }
        }

        // Add categories
        if (method_exists($recipe, 'categories') && $recipe->categories) {
            foreach ($recipe->categories as $category) {
                if (isset($category->name)) {
                    $parts[] = $category->name;
                }
            }
        }

        // Add tags
        if (method_exists($recipe, 'tags') && $recipe->tags) {
            foreach ($recipe->tags as $tag) {
                if (isset($tag->name)) {
                    $parts[] = $tag->name;
                }
            }
        }

        // Add cooking metadata
        if (!empty($recipe->difficulty)) {
            $parts[] = "độ khó " . $recipe->difficulty;
        }

        if (!empty($recipe->cooking_time)) {
            $parts[] = "thời gian nấu " . $recipe->cooking_time . " phút";
        }

        return implode(' ', array_filter($parts));
    }

    /**
     * Prepare text for embedding by cleaning and normalizing
     */
    private function prepareText(string $text): string
    {
        // Remove HTML tags
        $text = strip_tags($text);

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Trim
        $text = trim($text);

        // Limit length (embedding models have token limits)
        if (strlen($text) > 8000) {
            $text = substr($text, 0, 8000);
        }

        return $text;
    }

    /**
     * Check if the service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Test the embedding service
     */
    public function test(): array
    {
        try {
            $testText = "Phở bò là món ăn truyền thống của Việt Nam";
            $embedding = $this->embed($testText);

            if ($embedding && count($embedding) > 0) {
                return [
                    'success' => true,
                    'message' => 'Embedding service hoạt động tốt',
                    'vector_length' => count($embedding),
                    'sample_values' => array_slice($embedding, 0, 5)
                ];
            }

            return [
                'success' => false,
                'message' => 'Không thể tạo embedding'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kiểm tra embedding service: ' . $e->getMessage()
            ];
        }
    }
}