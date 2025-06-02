<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-language" content="pt-br">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7eee6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 350px;
            width: 100%;
        }
        .login-card .icon {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .login-card h2 {
            text-align: center;
            color: #e0bb9c;
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.7rem;
        }
        .form-label {
            color: #a88b6b;
            font-weight: 500;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #e0bb9c;
            background: #f7eee6;
            color: #6d4c2b;
        }
        .form-control:focus {
            border-color: #e0bb9c;
            box-shadow: 0 0 0 0.1rem #e0bb9c33;
        }
        .btn-login {
            background: #e0bb9c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: #cfa77e;
        }
        ::placeholder {
            color: #bfa88b;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#e0bb9c" class="bi bi-person-vcard-fill" viewBox="0 0 16 16">
                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm9 1.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 0-.5.5M9 8a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4A.5.5 0 0 0 9 8m1 2.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5m-1 2C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 0 2 13h6.96q.04-.245.04-.5M7 6a2 2 0 1 0-4 0 2 2 0 0 0 4 0"/>
            </svg>
        </div>
        <h2>Login</h2>
        <form action="login.php" method="post">
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input class="form-control" type="email" name="email" required placeholder="Digite seu e-mail">
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input class="form-control" type="password" name="senha" required placeholder="Digite sua senha">
            </div>
            <button type="submit" class="btn btn-login w-100">Entrar</button>
        </form>
    </div>
</body>
</html>
