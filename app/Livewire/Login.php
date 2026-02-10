<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $error = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Static credentials check
        if ($this->email === 'admin@gmail.com' && $this->password === 'admin@123') {
            Session::put('admin_user', [
                'email' => $this->email,
                'name' => 'Admin User',
                'logged_in' => true
            ]);

            return redirect()->route('dashboard');
        }

        $this->error = 'Invalid credentials. Try admin@gmail.com / admin@123';
    }

    public function logout()
    {
        Session::forget('admin_user');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('layouts.auth');
    }
}
