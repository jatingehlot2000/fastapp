<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Dashboard extends Component
{
    public $user;

    public function mount()
    {
        // For now, just set a demo user - remove authentication check
        $this->user = ['name' => 'Admin User', 'logged_in' => true];
    }

    public function logout()
    {
        Session::forget('admin_user');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
