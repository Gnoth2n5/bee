<?php

namespace App\Livewire;

use Livewire\Component;

class SimpleTest extends Component
{
    public $message = 'Hello';

    public function test()
    {
        $this->message = 'Test successful!';
        \Log::info('SimpleTest test() called');
    }

    public function render()
    {
        return view('livewire.simple-test');
    }
}