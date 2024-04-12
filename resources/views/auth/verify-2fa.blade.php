<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
</head>
<body>
    <h1>Verify OTP</h1>

    <div class="container">
        <div class="card">
            <div class="card-header">Verify Two-Factor Authentication</div>
    
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @elseif (session('otp'))
                    <div class="aler alert-danger">
                        {{ session('otp') }}
                    </div>
                @endif
    
            <div class="card-body">
                <form method="POST" action="{{ route('verify-2fa') }}">
                    @csrf
    
                    <div class="form-group">
                        <label for="otp">Enter OTP:</label>
                        <input type="text" id="otp" name="otp" class="form-control" required autofocus>
                    </div>
    
                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>