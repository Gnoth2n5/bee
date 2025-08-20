<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin {email=admin@example.com} {password=password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo user admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("Tạo admin user với email: {$email}");

        // Tạo hoặc cập nhật user admin
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin User',
                'password' => bcrypt($password),
                'email_verified_at' => now(),
                'is_admin' => 1
            ]
        );

        $this->info("✅ Admin user đã được tạo/cập nhật!");
        $this->info("📋 Thông tin admin:");
        $this->info("   - ID: {$user->id}");
        $this->info("   - Name: {$user->name}");
        $this->info("   - Email: {$user->email}");
        $this->info("   - Password: {$password}");
        $this->info("   - is_admin: " . ($user->is_admin ? 'Yes' : 'No'));
        $this->info("");
        $this->info("🔗 Admin có thể đăng nhập tại:");
        $this->info("   http://127.0.0.1:8000/login");
        $this->info("");
        $this->info("🔗 Sau khi đăng nhập, admin có thể truy cập:");
        $this->info("   http://127.0.0.1:8000/admin/payments");

        return 0;
    }
}

