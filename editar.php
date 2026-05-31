<?php
session_start();

// Proteger la página: solo usuarios autenticados
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

include("conexion.php");

// Validar que se recibió un ID numérico
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: admin.php");
    exit;
}

// Obtener artículo con consulta preparada
$stmt = $conexion->prepare("SELECT * FROM articulos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$row = $resultado->fetch_assoc();

if (!$row) {
    header("Location: admin.php");
    exit;
}

$exito = false;
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $marca  = trim($_POST['marca']  ?? '');
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $stock  = filter_input(INPUT_POST, 'stock',  FILTER_VALIDATE_INT);

    // Validaciones básicas
    if (empty($nombre))         $errores[] = "El nombre no puede estar vacío.";
    if (empty($marca))          $errores[] = "La marca no puede estar vacía.";
    if ($precio === false || $precio < 0) $errores[] = "El precio debe ser un número válido.";
    if ($stock  === false || $stock  < 0) $errores[] = "El stock debe ser un número entero válido.";

    if (empty($errores)) {
        $upd = $conexion->prepare(
            "UPDATE articulos SET nombre=?, marca=?, precio=?, stock=? WHERE id=?"
        );
        $upd->bind_param("ssdii", $nombre, $marca, $precio, $stock, $id);

        if ($upd->execute()) {
            $exito = true;
            // Refrescar los datos mostrados
            $row['nombre'] = $nombre;
            $row['marca']  = $marca;
            $row['precio'] = $precio;
            $row['stock']  = $stock;
        } else {
            $errores[] = "Error al actualizar. Intenta de nuevo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar artículo – Papelería Tony</title>
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

    /* ── MAIN ── */
    main {
      flex: 1;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 3rem 1.5rem;
    }

    .card {
      background: var(--blanco);
      border: 0.5px solid var(--borde);
      border-radius: 16px;
      padding: 2.5rem 2rem;
      width: 100%;
      max-width: 480px;
    }

    /* ── BREADCRUMB ── */
    .breadcrumb {
      font-size: 0.8rem;
      color: var(--suave);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    .breadcrumb a { color: var(--azul); text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }

    /* ── TÍTULO ── */
    .card-header { margin-bottom: 1.75rem; }

    .card-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      color: var(--texto);
      margin-bottom: 0.3rem;
    }

    .badge-id {
      display: inline-block;
      background: var(--azul-clr);
      color: var(--azul);
      font-size: 0.75rem;
      font-weight: 500;
      padding: 0.2rem 0.65rem;
      border-radius: 999px;
    }

    /* ── ALERTAS ── */
    .alerta {
      border-radius: var(--radio);
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      margin-bottom: 1.25rem;
    }

    .alerta-error {
      background: var(--rojo-clr);
      border: 0.5px solid #F7C1C1;
      color: var(--rojo);
    }

    .alerta-exito {
      background: var(--verde-clr);
      border: 0.5px solid #C0DD97;
      color: var(--verde);
    }

    .alerta ul { padding-left: 1.1rem; }
    .alerta li { margin-top: 0.25rem; }

    /* ── FORMULARIO ── */
    .grid-dos {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    @media (max-width: 440px) {
      .grid-dos { grid-template-columns: 1fr; }
    }

    .campo {
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
      margin-bottom: 1.1rem;
    }

    .campo.full { grid-column: 1 / -1; }

    label {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--texto);
    }

    .hint {
      font-size: 0.75rem;
      color: var(--suave);
      margin-top: 0.2rem;
    }

    input[type="text"],
    input[type="number"] {
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

    input.invalido {
      border-color: var(--rojo);
      box-shadow: 0 0 0 3px rgba(163,45,45,0.1);
    }

    /* ── PREFIJO $ ── */
    .input-grupo {
      display: flex;
      align-items: center;
    }

    .input-prefijo {
      height: 42px;
      padding: 0 0.75rem;
      background: var(--fondo);
      border: 0.5px solid rgba(0,0,0,0.18);
      border-right: none;
      border-radius: var(--radio) 0 0 var(--radio);
      font-size: 0.9rem;
      color: var(--suave);
      display: flex;
      align-items: center;
    }

    .input-grupo input {
      border-radius: 0 var(--radio) var(--radio) 0;
    }

    /* ── ACCIONES ── */
    .acciones {
      display: flex;
      gap: 0.75rem;
      margin-top: 0.5rem;
    }

    .btn {
      flex: 1;
      height: 44px;
      border: none;
      border-radius: 999px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s, transform 0.15s;
    }

    .btn:hover  { transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }

    .btn-primary {
      background: var(--azul);
      color: white;
    }
    .btn-primary:hover { background: #0C447C; }

    .btn-cancelar {
      background: var(--fondo);
      color: var(--suave);
      border: 0.5px solid rgba(0,0,0,0.12);
    }
    .btn-cancelar:hover { color: var(--texto); }

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
    <nav><a href="admin.php">← Volver al panel</a></nav>
  </header>

  <main>
    <div class="card">

      <div class="breadcrumb">
        <a href="admin.php">Panel</a>
        <span>›</span>
        <a href="admin.php">Artículos</a>
        <span>›</span>
        <span>Editar</span>
      </div>

      <div class="card-header">
        <h1>Editar artículo</h1>
        <span class="badge-id">ID #<?= htmlspecialchars($id) ?></span>
      </div>

      <?php if ($exito): ?>
        <div class="alerta alerta-exito" role="status">
          ✅ Artículo actualizado correctamente.
        </div>
      <?php endif; ?>

      <?php if (!empty($errores)): ?>
        <div class="alerta alerta-error" role="alert">
          <strong>Corrige los siguientes errores:</strong>
          <ul>
            <?php foreach ($errores as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="editar.php?id=<?= (int)$id ?>" novalidate>

        <div class="grid-dos">

          <div class="campo full">
            <label for="nombre">Nombre del artículo</label>
            <input
              type="text"
              id="nombre"
              name="nombre"
              value="<?= htmlspecialchars($row['nombre']) ?>"
              placeholder="Ej. Cuaderno universitario"
              required
            >
          </div>

          <div class="campo full">
            <label for="marca">Marca</label>
            <input
              type="text"
              id="marca"
              name="marca"
              value="<?= htmlspecialchars($row['marca']) ?>"
              placeholder="Ej. Scribe, Pilot, Faber"
              required
            >
          </div>

          <div class="campo">
            <label for="precio">Precio</label>
            <div class="input-grupo">
              <span class="input-prefijo">$</span>
              <input
                type="number"
                id="precio"
                name="precio"
                value="<?= htmlspecialchars($row['precio']) ?>"
                min="0"
                step="0.01"
                placeholder="0.00"
                required
              >
            </div>
            <span class="hint">Precio en pesos mexicanos</span>
          </div>

          <div class="campo">
            <label for="stock">Stock</label>
            <input
              type="number"
              id="stock"
              name="stock"
              value="<?= htmlspecialchars($row['stock']) ?>"
              min="0"
              step="1"
              placeholder="0"
              required
            >
            <span class="hint">Unidades disponibles</span>
          </div>

        </div>

        <div class="acciones">
          <a href="admin.php" class="btn btn-cancelar">Cancelar</a>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>

      </form>
    </div>
  </main>

  <footer>
    <p>© 2026 Papelería Tony · Taller de Sistemas Operativos · Equipo 5</p>
  </footer>

</body>
</html>
