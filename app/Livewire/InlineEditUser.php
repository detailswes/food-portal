<?php

namespace App\Http\Livewire;
use Filament\Forms\Concerns\InteractsWithForms;

use Livewire\Component;
use App\Models\User;

class InlineEditUser extends Component
{
    public $userId;
    public $name;
    public $email;

    protected $rules = [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255',
    ];

    public function mount($userId)
    {
        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save()
    {
        $this->validate();

        $user = User::findOrFail($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->emit('userUpdated'); // Emit an event to notify Filament if necessary
    }

    public function render()
    {
        return view('livewire.inline-edit-user');
    }
}
