<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestAdminAccess extends Command
{
    protected $signature = 'test:admin-access {email=admin@bee.com}';
    protected $description = 'Test admin access for a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        $this->info("User: {$user->name} ({$user->email})");
        $this->info("is_admin: " . ($user->is_admin ? 'YES' : 'NO'));
        $this->info("status: {$user->status}");

        if ($user->is_admin) {
            $this->info("✅ User has admin access!");
        } else {
            $this->error("❌ User does NOT have admin access!");
        }

        return 0;
    }
}

