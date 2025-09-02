<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use App\Models\Ecommerce\Cart;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as RegistrationPage;
use Filament\Forms\Components\Wizard\Step;

class Register extends RegistrationPage
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Personal Information')
                        ->schema([

                            TextInput::make('first_name')
                                ->required()
                                ->label('First Name')
                                ->maxLength(255)
                                ->minLength(3),

                            TextInput::make('middle_initial')
                                ->label('Middle Initial')
                                ->maxLength(1),

                            TextInput::make('last_name')
                                ->label('Last Name')
                                ->required()
                                ->minLength(3),

                            // Select::make('gender')
                            //     ->label('Gender')
                            //     ->columnSpanFull()

                        ]),

                    Step::make('Account Information')
                        ->schema([

                            // $this->getNameFormComponent()->label('Username'),
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),

                        ])
                ]),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $sanitizedData = $this->sanitizeData($data);
        //user model
        $user = $this->userData($sanitizedData);
        $this->mergeGuestCartToUser($user);


        // //user proifle model
        // $this->userProfile($user,$sanitizedData);

        // $this->assignRoles($user);
        return $user;
    }


    protected function sanitizeData(array $data): array
    {
        return [
            'first_name' => trim(strip_tags($data['first_name'])),
            'last_name' => trim(strip_tags($data['last_name'])),
            'middle_initial' => trim(strip_tags($data['middle_initial'])),
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'password' => $data['password'],
            // 'gender' => Str::lower(trim(strip_tags($data['gender']))),
            // 'name' => trim(strip_tags($data['name'])),
        ];
    }

    //store user email and password
    protected function userData(array $data): User
    {
        return User::create([
            'name' => Str::title($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
            // 'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
        ]);
    }

    protected function mergeGuestCartToUser(User $user): void
    {
        $sessionId = Session::getId();

        $hasCart = Cart::where('session_id', $sessionId)->exists();

        if ($hasCart) {
            Cart::where('session_id', $sessionId)->update([
                'user_id' => $user->id,
                'session_id' => null,
            ]);

            // Optionally forget any session variables
            Session::forget('cart');
           // session()->flush();
            // if you store cart in session manually
        }
    }
}
