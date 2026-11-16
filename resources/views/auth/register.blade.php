<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Customer - TunasKita</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f7f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .card {
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 400px;
        }

        .brand {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 11px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
        }

        button:hover {
            background-color: #256428;
        }

        .error {
            color: #d32f2f;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .login-link {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
        }

        .login-link a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="brand">🌱 TunasKita</div>
        <div class="subtitle">Daftar Akun Customer</div>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ url('/register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap"
                    required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@gmail.com"
                    required>
            </div>

            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" rows="2" placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
            </div>

            <button type="submit">Daftar</button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="{{ url('/login') }}">Login di sini</a>
        </div>
    </div>
</body>

</html>
