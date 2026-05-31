<?php
session_start();

// Redirigir si ya está autenticado
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: admin.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['usuario'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    // Credenciales válidas (en producción usa una base de datos + password_hash)
    $usuario_valido   = "24160817@itoaxaca.edu.mx";
    $password_valido  = "24160817";

    if ($user === $usuario_valido && $pass === $password_valido) {
        $_SESSION['login']   = true;
        $_SESSION['usuario'] = $user;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Correo o contraseña incorrectos. Inténtalo de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión – Papelería Tony</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --azul:     #185FA5;
      --azul-clr: #E6F1FB;
      --verde:    #3B6D11;
      --verde-clr:#EAF3DE;
      --rojo:     #A32D2D;
      --rojo-clr: #FCEBEB;
      --texto:    #2C2C2A;
      --suave:    #888780;
      --fondo:    #F7F6F2;
      --blanco:   #ffffff;
      --borde:    rgba(0,0,0,0.08);
      --radio:    12px;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--fondo);
      color: var(--texto);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ── HEADER ── */
    header {
      background: var(--blanco);
      border-bottom: 0.5px solid var(--borde);
      padding: 0 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 64px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      color: var(--azul);
      text-decoration: none;
      letter-spacing: -0.02em;
    }
    .logo span { color: var(--verde); }

    header nav a {
      font-size: 0.875rem;
      color: var(--suave);
      text-decoration: none;
    }
    header nav a:hover { color: var(--azul); }

    /* ── CONTENEDOR CENTRAL ── */
    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem 1.5rem;
    }

    .card {
      background: var(--blanco);
      border: 0.5px solid var(--borde);
      border-radius: 16px;
      padding: 2.5rem 2rem;
      width: 100%;
      max-width: 400px;
    }

    .card-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .card-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.7rem;
      color: var(--texto);
      margin-bottom: 0.4rem;
    }

    .card-header p {
      font-size: 0.9rem;
      color: var(--suave);
    }

    /* ── ALERTA DE ERROR ── */
    .alerta {
      background: var(--rojo-clr);
      border: 0.5px solid #F7C1C1;
      border-radius: var(--radio);
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      color: var(--rojo);
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* ── FORMULARIO ── */
    .campo {
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
      margin-bottom: 1.1rem;
    }

    label {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--texto);
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      height: 42px;
      padding: 0 0.875rem;
      border: 0.5px solid rgba(0,0,0,0.18);
      border-radius: var(--radio);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      color: var(--texto);
      background: var(--fondo);
      transition: border-color 0.2s, box-shadow 0.2s;
      outline: none;
    }

    input:focus {
      border-color: var(--azul);
      box-shadow: 0 0 0 3px rgba(24,95,165,0.12);
      background: var(--blanco);
    }

    /* ── TOGGLE CONTRASEÑA ── */
    .campo-pass { position: relative; }

    .campo-pass input { padding-right: 2.75rem; }

    .toggle-pass {
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: var(--suave);
      font-size: 1rem;
      line-height: 1;
      padding: 0;
    }
    .toggle-pass:hover { color: var(--azul); }

    /* ── BOTÓN SUBMIT ── */
    .btn-submit {
      width: 100%;
      height: 44px;
      background: var(--azul);
      color: white;
      border: none;
      border-radius: 999px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      margin-top: 0.5rem;
    }

    .btn-submit:hover  { background: #0C447C; transform: translateY(-1px); }
    .btn-submit:active { transform: translateY(0); }

    /* ── LINK DE VOLVER ── */
    .volver {
      text-align: center;
      margin-top: 1.25rem;
      font-size: 0.875rem;
      color: var(--suave);
    }
    .volver a { color: var(--azul); text-decoration: none; }
    .volver a:hover { text-decoration: underline; }

    /* ── FOOTER ── */
    footer {
      background: var(--blanco);
      border-top: 0.5px solid var(--borde);
      padding: 1.25rem 2rem;
      text-align: center;
    }
    footer p { font-size: 0.82rem; color: var(--suave); }
  </style>
</head>
<body>

  <header>
    <a href="index.php" class="logo">Papelería <span>Tony</span></a>
    <nav><a href="index.php">← Volver al inicio</a></nav>
  </header>

  <main>
    <div class="card">
      <div class="card-header">
        <h1>Bienvenido</h1>
        <p>Inicia sesión en tu cuenta del sistema</p>
      </div>

      <?php if ($error): ?>
        <div class="alerta" role="alert">
          ⚠️ <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" novalidate>
        <div class="campo">
          <label for="usuario">Correo institucional</label>
          <input
            type="email"
            id="usuario"
            name="usuario"
            placeholder="ejemplo@itoaxaca.edu.mx"
            value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
            required
            autocomplete="email"
          >
        </div>

        <div class="campo">
          <label for="password">Contraseña</label>
          <div class="campo-pass">
            <input
              type="password"
              id="password"
              name="password"
              placeholder="••••••••"
              required
              autocomplete="current-password"
            >
            <button type="button" class="toggle-pass" onclick="togglePass()" aria-label="Mostrar contraseña">
              👁
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit">Ingresar</button>
      </form>

      <p class="volver"><a href="index.php">← Regresar a la página principal</a></p>
    </div>
  </main>

  <footer>
    <p>© 2026 Papelería Tony · Taller de Sistemas Operativos · Equipo 5</p>
  </footer>

  <script>
    function togglePass() {
      var input = document.getElementById('password');
      var btn   = document.querySelector('.toggle-pass');
      if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
      } else {
        input.type = 'password';
        btn.textContent = '👁';
      }
    }
  </script>
</body>
</html>
