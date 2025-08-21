<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    protected $signature = 'list:users';
    protected $description = 'List all users with admin status';

    public function handle()
    {
        $users = User::all(['id', 'name', 'email', 'is_admin', 'status']);

        $this->info('All Users:');
        $this->table(
            ['ID', 'Name', 'Email', 'Admin', 'Status'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->is_admin ? 'YES' : 'NO',
                    $user->status
                ];
            })
        );

        return 0;
    }
}

