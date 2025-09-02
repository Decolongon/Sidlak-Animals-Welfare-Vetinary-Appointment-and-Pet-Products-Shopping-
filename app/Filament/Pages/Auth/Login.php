<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Pages\Auth\Login as LoginPage;
use Illuminate\Validation\ValidationException;

class Login extends LoginPage
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                // $this->getPasswordConfirmationFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getCredentialsFromFormData(array $data): array
    {


        return [
            'email' => $data['email'],
            'password'  => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('Incorrect email or password'),
        ]);
    }

    //  protected function getAuthenticateFormAction(): Action
    // {
    //     return Action::make('authenticate')
    //         ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
    //         ->submit('authenticate');
    // }
    //   protected function getActions(): array
    // {
    //     return [
    //         $this->getAuthenticateFormAction(),
    //         Action::make('backToHome')
    //             ->label('Back to Home')
    //             ->url(route('page.home'))
    //             ->color('gray'),
    //     ];
    // }


}
