<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
</head>
<body>
    <h1>Enable OTP</h1>

    <div class="container">
        <div class="card">
            <div class="card-header">Enable Two-Factor Authentication</div>
    
            <div class="card-body">
                <form method="POST" action="{{ route('enable-2fa') }}">
                    @csrf
    
                    <button type="submit" class="btn btn-primary">Enable 2FA</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>