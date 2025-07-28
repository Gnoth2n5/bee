<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RedirectAfterLogout
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(Logout $event): void
    {
        // Nếu đang ở trang admin, redirect về /login
        if (request()->is('admin/*')) {
            redirect('/login');
        }
    }
}