<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestAdminMiddleware extends Command
{
    protected $signature = 'test:admin-middleware {email}';
    protected $description = 'Test admin middleware with different methods';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        $this->info("Testing admin middleware for: {$user->name} ({$user->email})");
        $this->info("is_admin field: " . ($user->is_admin ? 'YES' : 'NO'));

        $hasAdminRole = $user->hasRole('admin');
        $this->info("has admin role: " . ($hasAdminRole ? 'YES' : 'NO'));

        $this->info("\n=== Test Results ===");

        // Test field only
        $fieldAccess = $user->is_admin;
        $this->info("Field only (is_admin): " . ($fieldAccess ? '✅ PASS' : '❌ FAIL'));

        // Test role only
        $roleAccess = $hasAdminRole;
        $this->info("Role only (hasRole): " . ($roleAccess ? '✅ PASS' : '❌ FAIL'));

        // Test both
        $bothAccess = $user->is_admin || $hasAdminRole;
        $this->info("Both (field OR role): " . ($bothAccess ? '✅ PASS' : '❌ FAIL'));

        $this->info("\n=== Middleware Usage ===");
        $this->info("Current middleware: admin (checks both field and role)");
        $this->info("Alternative: admin.flexible:field (checks only is_admin field)");
        $this->info("Alternative: admin.flexible:role (checks only admin role)");
        $this->info("Alternative: admin.flexible:both (checks both, same as current)");

        return 0;
    }
}

