<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class LoginController extends Controller
{
    


public function authenticated(Request $request, $user)
{
    if ($user->uses_two_factor_auth) {
        $google2fa = new Google2FA();

        if ($request->session()->has('2fa_passed')) {
            $request->session()->forget('2fa_passed');
        }

        $request->session()->put('2fa:user:id', $user->id);
        $request->session()->put('2fa:auth:attempt', true);
        $request->session()->put('2fa:auth:remember', $request->has('remember'));

        $otp_secret = $user->google2fa_secret;
        $one_time_password = $google2fa->getCurrentOtp($otp_secret);

        return redirect()->route('2fa')->with('one_time_password', $one_time_password);
    }

    return redirect()->intended($this->redirectPath());
}
}
