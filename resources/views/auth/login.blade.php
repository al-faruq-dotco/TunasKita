<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TunasKita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f7f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-box {
            background: white;
            padding: 35px;
            border-radius: 15px;
            width: 330px;
            box-shadow: 0 5px 20px #0001;
        }

        h2 {
            color: #287a43;
            text-align: center;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 7px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #287a43;
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <h2>🌱 TunasKita</h2>
        <p style="text-align:center">Masuk ke akun Anda</p>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Login</button>
            <p style="text-align: center; margin-top: 15px; font-size: 14px;">
                Belum punya akun? <a href="{{ url('/register') }}" style="color: #2e7d32; font-weight: 600;">Daftar di
                    sini</a>
            </p>
        </form>
    </div>

</body>

</html>
