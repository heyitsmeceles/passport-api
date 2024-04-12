<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_secret', // Add two-factor secret column
        'two_factor_recovery_codes', // Add two-factor recovery codes column
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret', // Hide two-factor secret
        'two_factor_recovery_codes', // Hide two-factor recovery codes
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_enabled' => 'boolean', // Cast two-factor enabled to boolean
    ];

    public function generate2faSecret()
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $this->google2fa_secret = $secret;
        $this->save();

        return $secret;
    }

    public function enableTwoFactorAuthentication()
    {
        $this->generate2faSecret();

        // Optionally, send the secret to the user (e.g., via email)
        // $this->sendTwoFactorAuthSecret(); // Implement this method

        return true;
    }

    public function verifyTwoFactorAuthentication($otp)
    {
        $google2fa = new Google2FA();
        return $google2fa->verifyKey($this->google2fa_secret, $otp);
    }
}
