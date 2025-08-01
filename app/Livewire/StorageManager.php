<?php

namespace App\Livewire;

use Livewire\Component;

class StorageManager extends Component
{
    public $storageKey = '';
    public $storageValue = '';
    public $storageItems = [];

    protected $listeners = [
        'refreshStorage' => 'loadStorageItems',
        'showStorageAlert' => 'showAlert'
    ];

    public function mount()
    {
        $this->loadStorageItems();
    }

    public function loadStorageItems()
    {
        // Lấy danh sách các item trong localStorage
        $this->storageItems = [];
        
        // Trong thực tế, chúng ta sẽ cần JavaScript để lấy dữ liệu từ localStorage
        // Vì localStorage chỉ có thể truy cập từ client-side
    }

    public function saveItem()
    {
        if (empty($this->storageKey)) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Vui lòng nhập key cho dữ liệu'
            ]);
            return;
        }

        $this->dispatch('saveToStorage', [
            'key' => $this->storageKey,
            'value' => $this->storageValue
        ]);

        $this->storageKey = '';
        $this->storageValue = '';
        $this->loadStorageItems();
    }

    public function deleteItem($key)
    {
        $this->dispatch('deleteFromStorage', ['key' => $key]);
        $this->loadStorageItems();
    }

    public function showAlert($data)
    {
        $this->dispatch('showSweetAlert', $data);
    }

    public function render()
    {
        return view('livewire.storage-manager');
    }
} 