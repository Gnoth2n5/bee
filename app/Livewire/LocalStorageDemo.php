<?php

namespace App\Livewire;

use Livewire\Component;

class LocalStorageDemo extends Component
{
    public $demoKey = '';
    public $demoValue = '';
    public $currentTheme = 'light';

    public function mount()
    {
        // Lấy theme hiện tại từ localStorage
        $this->currentTheme = 'light'; // Mặc định
    }

    public function saveDemoData()
    {
        if (empty($this->demoKey)) {
            $this->dispatch('showSweetAlert', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Vui lòng nhập key cho dữ liệu demo'
            ]);
            return;
        }

        $this->dispatch('saveToLocalStorage', [
            'key' => $this->demoKey,
            'value' => $this->demoValue
        ]);

        $this->demoKey = '';
        $this->demoValue = '';
    }

    public function toggleTheme()
    {
        $newTheme = $this->currentTheme === 'light' ? 'dark' : 'light';
        $this->currentTheme = $newTheme;

        $this->dispatch('saveToLocalStorage', [
            'key' => 'theme',
            'value' => $newTheme
        ]);
    }

    public function clearDemoData()
    {
        $this->dispatch('clearLocalStorage');
    }

    public function render()
    {
        return view('livewire.local-storage-demo');
    }
}