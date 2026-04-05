<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('admin.dashboard'), navigate: true);
        }
    }

    public function login(): void
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:1',
        ], [
            'email.required'    => 'E-posta adresi zorunludur.',
            'email.email'       => 'Geçerli bir e-posta giriniz.',
            'password.required' => 'Şifre zorunludur.',
        ]);

        // Kullanıcıyı bul
        $user = User::where('email', $this->email)->first();

        // Kullanıcı yoksa genel hata
        if (!$user) {
            $this->addError('email', 'E-posta veya şifre hatalı.');
            return;
        }

        // Hesap kilitli mi?
        if ($user->isLocked()) {
            $remaining = now()->diffInMinutes($user->locked_until) + 1;
            $this->addError('email', "Hesabınız kilitlendi. {$remaining} dakika sonra tekrar deneyiniz.");
            return;
        }

        // Giriş dene
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $user->incrementFailedAttempts();

            $attemptsLeft = 5 - $user->fresh()->failed_login_attempts;

            if ($attemptsLeft <= 0) {
                $this->addError('email', 'Çok fazla hatalı giriş. Hesabınız 15 dakika kilitlendi.');
            } else {
                $this->addError('email', "E-posta veya şifre hatalı. ({$attemptsLeft} hak kaldı)");
            }

            return;
        }

        // Başarılı giriş
        $user->resetFailedAttempts();
        session()->regenerate();

        $this->redirect(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.guest', [
                'title' => 'Giriş - SiteWatch',
            ]);
    }
}
