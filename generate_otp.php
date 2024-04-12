<?php

require __DIR__.'/vendor/autoload.php'; // Autoload files using Composer autoload

use OTPHP\TOTP;

// Replace this secret key with your actual secret key
$secretKey = 'JBSWY3DPEHPK3PXP';

// Create a new TOTP instance
$otp = TOTP::create($secretKey);

// Generate OTP
$otpCode = $otp->now();

// Print OTP to the terminal
echo "Your One-Time Password (OTP): $otpCode\n";
