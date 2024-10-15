<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Hash;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;

class Registration extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.registration';


    protected static ?string $navigationLabel = 'Register';
  

    public $name;
    public $email;
    public $phone_number;
    public $password;
    public $password_confirmation;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->label('Full Name'),

            TextInput::make('email')
                ->email()
                ->required()
                ->label('Email Address'),

            TextInput::make('telephone')
                ->required()
                ->label('Phone Number'),

            TextInput::make('password')
                ->password()
                ->required()
                ->label('Password')
                ->confirmed(),

            TextInput::make('password_confirmation')
                ->password()
                ->required()
                ->label('Confirm Password'),
        ];
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone_number' => $this->phone_number,
        ]);

        // Log in the user automatically
        auth()->login($user);

        // Redirect to the dashboard
        return redirect()->route('filament.pages.dashboard');
    }

    public static function route(string $path): \Filament\Resources\Pages\PageRegistration
    {
        return static::registerRoute($path);
    }
}