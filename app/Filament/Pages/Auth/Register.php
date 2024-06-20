<?php

namespace App\Filament\Pages\Auth;

use Carbon\Carbon;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Pages\Auth\Register as BaseRegister;
use Spatie\Permission\Models\Role;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Registered;
use Filament\Notifications\Notification;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Register extends BaseRegister
{
    //protected static ?string $navigationIcon = 'heroicon-o-document-text';

    //protected static string $view = 'filament.pages.auth.register';

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(10);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        $data['email_verified_at'] = Carbon::now();
        $data['status'] = 'Inactive';
//        $data['role'] = 'user';

        $user = $this->getUserModel()::create($data);
        $user->assignRole($data['roles']);
        // app()->bind(
        //     \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        //     \Filament\Listeners\Auth\SendEmailVerificationNotification::class,
        // );
        // event(new Registered($user));

        // Filament::auth()->login($user);

        // session()->regenerate();

        Notification::make()
        ->title('Registration completed successfully. You can sign in after admin approval.')
        ->success()
        ->duration(10000)
        ->send();

        return app(RegistrationResponse::class);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getRoleFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getRoleFormComponent(): Component
    {
        return Select::make('roles')->label('Role')
                       ->preload()
                       ->options(Role::where('name','Agent')->pluck('name', 'name'))
                       ->default('Agent')
                       ->required();
    }
}
