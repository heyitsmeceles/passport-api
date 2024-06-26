<?php

namespace App\Http\Controllers\Auth;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{

public function show(Request $request)
{
    return view('auth.2fa');
}

public function generateOTP()
{
    // Retrieve the authenticated user
    $user = Auth::user();

    // Get the secret key from the user model
    $secretKey = $user->google2fa_secret;

    // Initialize the Google2FA class
    $google2fa = new Google2FA();

    // Generate the OTP using the secret key
    $otp = $google2fa->getCurrentOtp($secretKey);

    // Print the OTP to the terminal or log it
    echo "Generated OTP: $otp\n";
}

public function showEnableForm()
    {
        return view('auth.enable-2fa');
    }

    public function enable(Request $request)
    {
        $user = $request->user();
        $user->enableTwoFactorAuthentication(); // Implement this method in User model

        return redirect()->route('verify-2fa-form')->with('status', 'Two-factor authentication enabled successfully. Please verify.');
    }

    public function showVerifyForm()
    {
        return view('auth.verify-2fa');
    }

    public function verify(Request $request)
    {
        $user = $request->user();
        $otp = $request->input('otp');

        if ($user->verifyTwoFactorAuthentication($otp)) {
            $request->session()->put('2fa_passed', true);

            $products = Product::all();
            $userName = Auth::user()->name;
            $email = Auth::user()->email;

            return view('index', compact('products', 'userName', 'email')); // Redirect to intended page after successful 2FA
        }

        return redirect()->route('verify-2fa-form')->withErrors(['otp' => 'Invalid OTP. Please try again.']);
    }

// public function verify(Request $request)
// {
//     $request->validate([
//         'one_time_password' => 'required|string',
//     ]);

//     $user_id = $request->session()->get('2fa:user:id');
//     $remember = $request->session()->get('2fa:auth:remember', false);
//     $attempt = $request->session()->get('2fa:auth:attempt', false);

//     if (!$user_id || !$attempt) {
//         return redirect()->route('login');
//     }

//     $user = User::find($user_id);

//     if (!$user || !$user->uses_two_factor_auth) {
//         return redirect()->route('login');
//     }

//     $google2fa = new Google2FA();
//     $otp_secret = $user->google2fa_secret;

//     if (!$google2fa->verifyKey($otp_secret, $request->one_time_password)) {
//         throw ValidationException::withMessages([
//           'one_time_password' => [__('The one time password is invalid.')],
//         ]);
//     }

//     $guard = config('auth.defaults.guard');
//     $credentials = [$user->getAuthIdentifierName() => $user->getAuthIdentifier(), 'password' => $user->getAuthPassword()];
    
//     if ($remember) {
//         $guard = config('auth.defaults.remember_me_guard', $guard);
//     }
    
//     if ($attempt) {
//         $guard = config('auth.defaults.attempt_guard', $guard);
//     }
    
//     if (Auth::guard($guard)->attempt($credentials, $remember)) {
//         $request->session()->remove('2fa:user:id');
//         $request->session()->remove('2fa:auth:remember');
//         $request->session()->remove('2fa:auth:attempt');
    
//         return redirect()->intended('/');
//     }
    
//     return redirect()->route('login')->withErrors([
//         'password' => __('The provided credentials are incorrect.'),
//     ]);
// }
}
