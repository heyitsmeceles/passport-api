<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Google2FA\Google2FA;
use Illuminate\Support\Facades\Crypt;
use OTPHP\TOTP;


class LoginRegisterController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'email' => 'required|string|email:rfc,dns|max:250|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        // if($validate->fails()){
        //     // If validation fails, redirect back to the registration form with errors
        //     return redirect()->route('register.form')->withErrors($validate)->withInput();
        // }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $data['token'] = $user->createToken($request->email)->accessToken;
        $data['user'] = $user;

        $response = [
            'status' => 'success',
            'message' => 'User is created successfully.',
            'data' => $data,
        ];

        return response()->json($response, 201);

        // // If registration is successful, redirect to a different URL (e.g., dashboard)
        // return redirect('/2fa')->with('success', 'User is created successfully.');
    }


    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
     public function login(Request $request)
     {
         $credentials = $request->only('email', 'password');
         
         // Validate login data
         $validator = Validator::make($credentials, [
             'email' => 'required|email',
             'password' => 'required',
         ]);
     
         if ($validator->fails()) {
             return redirect()->route('login')->withErrors($validator)->withInput();
         }
     
         // Attempt to authenticate user
         if (Auth::attempt($credentials)) {
             $user = Auth::user();
     
             // Check if 2FA is enabled for the user
             if ($user->google2fa_secret) {
                 // Generate OTP token
                 $otp = TOTP::create($user->google2fa_secret);
     
                 // Generate OTP code
                 $otpCode = $otp->now();
     
                 // Store the OTP code in the session for verification
                 $request->session()->put('otp_code', $otpCode);
     
                 // If 2FA is enabled, redirect to the 2FA verification page
                 return redirect('/verify-2fa')->with('success', 'User is logged in successfully.');
             }
     
             // If 2FA is not enabled, proceed with the regular login flow
             return redirect('/products')->with('success', 'User is logged in successfully.');
         }
     
         // If authentication fails, redirect back to the login form with an error message
         return redirect()->route('login')->with('error', 'Invalid credentials');
     }
     public function verify2FA(Request $request)
     {
         // Retrieve the OTP code from the session
         $otpCode = $request->session()->get('otp_code');
     
         // Validate the OTP code entered by the user
         $validator = Validator::make($request->all(), [
             'otp' => 'required|numeric',
         ]);
     
         if ($validator->fails()) {
             return redirect('/2fa')->withErrors($validator);
         }
     
         // Compare the OTP code entered by the user with the stored OTP code
         if ($request->otp == $otpCode) {
             // If the OTP codes match, authenticate the user and redirect to the dashboard
             Auth::loginUsingId($request->user()->id);
     
             // Clear the OTP code from the session
             $request->session()->forget('otp_code');
     
             return redirect('/home')->with('success', '2FA verification successful.');
         }
     
         // If the OTP codes do not match, redirect back to the 2FA verification page with an error message
         return redirect('/2fa')->with('error', 'Invalid OTP code.');
     }
          


    // Log out the user from application
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        
        Auth::logout(); // This will log out the user
        
        return redirect()->route('login.form'); // Redirect to the login form
    }

    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Show registration form
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Logic for your landing page
    public function landingPage()
    {
        return redirect('/product'); // Redirect to the '/dashboard' route
    }

   
}
