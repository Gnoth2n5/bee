<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FixUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix user roles for users without roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu fix roles cho users...');

        // Lấy tất cả users không có role
        $usersWithoutRoles = User::whereDoesntHave('roles')->get();

        $this->info('Tìm thấy ' . $usersWithoutRoles->count() . ' users không có role');

        $fixedCount = 0;

        foreach ($usersWithoutRoles as $user) {
            try {
                // Gán role user
                $user->assignRole('user');
                $fixedCount++;

                $this->line("✓ Đã gán role cho user: {$user->name} ({$user->email})");
            } catch (\Exception $e) {
                $this->error("✗ Lỗi khi gán role cho user {$user->name}: " . $e->getMessage());
            }
        }

        $this->info("Hoàn thành! Đã fix roles cho {$fixedCount} users.");

        // Hiển thị thống kê
        $totalUsers = User::count();
        $usersWithRoles = User::whereHas('roles')->count();
        $usersWithoutRoles = User::whereDoesntHave('roles')->count();

        $this->table(
            ['Thống kê', 'Số lượng'],
            [
                ['Tổng số users', $totalUsers],
                ['Users có role', $usersWithRoles],
                ['Users không có role', $usersWithoutRoles],
            ]
        );
    }
}
