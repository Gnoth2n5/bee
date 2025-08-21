<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestVipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:vip {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test VIP functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id') ?? 7;
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("Testing VIP for user: {$user->name} (ID: {$user->id})");

        $activeSubscription = $user->activeSubscription();
        $isVip = $user->isVip();
        $isPremium = $user->isPremium();

        $this->line("Active subscription: " . ($activeSubscription ? 'Yes' : 'No'));
        $this->line("Is VIP: " . ($isVip ? 'Yes' : 'No'));
        $this->line("Is Premium: " . ($isPremium ? 'Yes' : 'No'));

        if ($activeSubscription) {
            $this->line("Subscription details:");
            $this->line("- Type: {$activeSubscription->subscription_type}");
            $this->line("- Status: {$activeSubscription->status}");
            $this->line("- Start: {$activeSubscription->start_date}");
            $this->line("- End: {$activeSubscription->end_date}");
            $this->line("- Amount: {$activeSubscription->amount} VNĐ");
            $this->line("- Remaining days: {$activeSubscription->getRemainingDays()}");
        }

        return 0;
    }
}
