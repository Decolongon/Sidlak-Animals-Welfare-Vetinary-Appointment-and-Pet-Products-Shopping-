<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use App\Models\Ecommerce\Cart;
use App\Models\Ecommerce\Address;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Woenel\Prpcmblmts\Models\PhilippineCity;
use Woenel\Prpcmblmts\Models\PhilippineBarangay;
use Filament\Pages\Auth\Register as RegistrationPage;

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



                        ]),

                    // Step::make('Address')
                    //     ->schema([
                    //         Select::make('city')
                    //             ->label('Select City')
                    //             ->required()
                    //             ->options(
                    //                 PhilippineCity::where('province_code', '0645')
                    //                     ->get()
                    //                     ->pluck('name', 'code')
                    //                     ->toArray()

                    //             )
                    //             ->live()
                    //             ->optionsLimit(5)
                    //             ->afterStateUpdated(function ($set) {
                    //                 $set('barangay', null); // Reset barangay when city changes
                    //             })
                    //             ->searchable(),

                    //         Select::make('barangay')
                    //             ->label('Select Barangay')
                    //             ->required()
                    //             ->options(

                    //                 function ($get) {
                    //                     $city = $get('city');
                    //                     if (! $city) {
                    //                         return [];
                    //                     }
                    //                     return PhilippineBarangay::where('city_code', $city)
                    //                         ->get()
                    //                         ->pluck('name', 'id')
                    //                         ->toArray();
                    //                 }

                    //             )
                    //             ->optionsLimit(5)
                    //             ->searchable()
                    //     ]),

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
       // $this->createAddress($sanitizedData, $user);

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
            // 'city' => strip_tags($data['city']),
            // 'barangay' => strip_tags($data['barangay']),
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

    protected function createAddress(array $data, ?User $user = null): Address
    {
        $brgyName = PhilippineBarangay::find($data['barangay'])->name;
        $cityName = PhilippineCity::where('code', $data['city'])->value('name');
        return Address::create([
            'user_id' => $user->id,
            'city' => $cityName,
            'barangay' => $brgyName,
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
