<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

include('conexion.php');

$errores = [];
$exito   = "";

// ── GUARDAR NUEVO ARTÍCULO ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $marca  = trim($_POST['marca']  ?? '');
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $stock  = filter_input(INPUT_POST, 'stock',  FILTER_VALIDATE_INT);

    if (empty($nombre))                      $errores[] = "El nombre no puede estar vacío.";
    if (empty($marca))                       $errores[] = "La marca no puede estar vacía.";
    if ($precio === false || $precio < 0)    $errores[] = "El precio debe ser un número válido.";
    if ($stock  === false || $stock  < 0)    $errores[] = "El stock debe ser un número entero válido.";

    if (empty($errores)) {
        $stmt = $conexion->prepare(
            "INSERT INTO articulos (nombre, marca, precio, stock) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdi", $nombre, $marca, $precio, $stock);
        if ($stmt->execute()) {
            $exito = "Artículo <strong>" . htmlspecialchars($nombre) . "</strong> agregado correctamente.";
        } else {
            $errores[] = "Error al guardar en la base de datos.";
        }
    }
}

// ── OBTENER LISTA DE ARTÍCULOS ──────────────────────────────────────
$resultado = $conexion->query("SELECT * FROM articulos ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de administración – Papelería Tony</title>
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
      --ambar:    #854F0B;
      --ambar-clr:#FAEEDA;
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
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      color: var(--azul);
      text-decoration: none;
    }
    .logo span { color: var(--verde); }

    .header-right {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .usuario-badge {
      font-size: 0.8rem;
      color: var(--suave);
    }

    .btn-logout {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--rojo);
      text-decoration: none;
      padding: 0.4rem 1rem;
      border: 0.5px solid #F7C1C1;
      border-radius: 999px;
      background: var(--rojo-clr);
      transition: background 0.2s;
    }
    .btn-logout:hover { background: #F7C1C1; }

    /* ── LAYOUT PRINCIPAL ── */
    main {
      flex: 1;
      max-width: 1100px;
      margin: 0 auto;
      padding: 2.5rem 1.5rem;
      width: 100%;
    }

    .page-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      margin-bottom: 0.3rem;
    }

    .page-subtitle {
      font-size: 0.9rem;
      color: var(--suave);
      margin-bottom: 2rem;
    }

    /* ── STATS ── */
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: var(--blanco);
      border: 0.5px solid var(--borde);
      border-radius: var(--radio);
      padding: 1rem 1.25rem;
    }

    .stat-label {
      font-size: 0.78rem;
      color: var(--suave);
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 0.4rem;
    }

    .stat-valor {
      font-size: 1.6rem;
      font-weight: 500;
      color: var(--texto);
    }

    /* ── GRID DOS COLUMNAS ── */
    .layout-grid {
      display: grid;
      grid-template-columns: 340px 1fr;
      gap: 1.5rem;
      align-items: start;
    }

    @media (max-width: 780px) {
      .layout-grid { grid-template-columns: 1fr; }
    }

    /* ── CARD GENÉRICA ── */
    .card {
      background: var(--blanco);
      border: 0.5px solid var(--borde);
      border-radius: 16px;
      padding: 1.75rem 1.5rem;
    }

    .card-titulo {
      font-family: 'Playfair Display', serif;
      font-size: 1.15rem;
      margin-bottom: 1.25rem;
      padding-bottom: 0.75rem;
      border-bottom: 0.5px solid var(--borde);
    }

    /* ── ALERTAS ── */
    .alerta {
      border-radius: var(--radio);
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      margin-bottom: 1.25rem;
    }
    .alerta-error  { background: var(--rojo-clr);  border: 0.5px solid #F7C1C1; color: var(--rojo);  }
    .alerta-exito  { background: var(--verde-clr); border: 0.5px solid #C0DD97; color: var(--verde); }
    .alerta ul     { padding-left: 1.1rem; }
    .alerta li     { margin-top: 0.25rem; }

    /* ── FORMULARIO AGREGAR ── */
    .campo {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
      margin-bottom: 1rem;
    }

    label {
      font-size: 0.82rem;
      font-weight: 500;
      color: var(--texto);
    }

    input[type="text"],
    input[type="number"] {
      width: 100%;
      height: 40px;
      padding: 0 0.875rem;
      border: 0.5px solid rgba(0,0,0,0.18);
      border-radius: var(--radio);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
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

    .input-grupo { display: flex; align-items: center; }
    .input-prefijo {
      height: 40px;
      padding: 0 0.65rem;
      background: var(--fondo);
      border: 0.5px solid rgba(0,0,0,0.18);
      border-right: none;
      border-radius: var(--radio) 0 0 var(--radio);
      font-size: 0.85rem;
      color: var(--suave);
      display: flex;
      align-items: center;
    }
    .input-grupo input { border-radius: 0 var(--radio) var(--radio) 0; }

    .btn-guardar {
      width: 100%;
      height: 42px;
      background: var(--azul);
      color: white;
      border: none;
      border-radius: 999px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      margin-top: 0.25rem;
    }
    .btn-guardar:hover  { background: #0C447C; transform: translateY(-1px); }
    .btn-guardar:active { transform: translateY(0); }

    /* ── TABLA ── */
    .tabla-wrap {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
    }

    thead th {
      text-align: left;
      font-size: 0.75rem;
      font-weight: 500;
      color: var(--suave);
      text-transform: uppercase;
      letter-spacing: 0.06em;
      padding: 0.6rem 1rem;
      border-bottom: 0.5px solid var(--borde);
      white-space: nowrap;
    }

    tbody tr {
      border-bottom: 0.5px solid var(--borde);
      transition: background 0.15s;
    }

    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: var(--fondo); }

    tbody td {
      padding: 0.75rem 1rem;
      color: var(--texto);
      vertical-align: middle;
    }

    .td-id {
      font-size: 0.8rem;
      color: var(--suave);
      font-weight: 500;
    }

    .td-precio {
      font-weight: 500;
      color: var(--verde);
    }

    .td-stock { text-align: center; }

    .badge-stock {
      display: inline-block;
      padding: 0.2rem 0.6rem;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 500;
    }

    .stock-ok   { background: var(--verde-clr); color: var(--verde); }
    .stock-bajo { background: var(--ambar-clr); color: var(--ambar); }
    .stock-cero { background: var(--rojo-clr);  color: var(--rojo); }

    /* ── ACCIONES TABLA ── */
    .acciones-td {
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }

    .btn-accion {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      font-size: 0.8rem;
      font-weight: 500;
      text-decoration: none;
      padding: 0.3rem 0.75rem;
      border-radius: 999px;
      border: 0.5px solid transparent;
      transition: background 0.15s;
      white-space: nowrap;
    }

    .btn-editar {
      background: var(--azul-clr);
      color: var(--azul);
      border-color: #B5D4F4;
    }
    .btn-editar:hover { background: #B5D4F4; }

    .btn-eliminar {
      background: var(--rojo-clr);
      color: var(--rojo);
      border-color: #F7C1C1;
    }
    .btn-eliminar:hover { background: #F7C1C1; }

    /* ── VACÍO ── */
    .tabla-vacia {
      text-align: center;
      padding: 3rem 1rem;
      color: var(--suave);
      font-size: 0.9rem;
    }

    /* ── BUSCADOR ── */
    .tabla-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1rem;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .buscador {
      position: relative;
      flex: 1;
      max-width: 280px;
    }

    .buscador input {
      width: 100%;
      height: 36px;
      padding: 0 0.75rem 0 2.1rem;
      border: 0.5px solid rgba(0,0,0,0.15);
      border-radius: 999px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.85rem;
      background: var(--fondo);
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .buscador input:focus {
      border-color: var(--azul);
      box-shadow: 0 0 0 3px rgba(24,95,165,0.12);
      background: var(--blanco);
    }

    .buscador-icono {
      position: absolute;
      left: 0.65rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 0.9rem;
      color: var(--suave);
      pointer-events: none;
    }

    #conteo-articulos {
      font-size: 0.8rem;
      color: var(--suave);
      white-space: nowrap;
    }

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
    <div class="header-right">
      <?php if (isset($_SESSION['usuario'])): ?>
        <span class="usuario-badge">👤 <?= htmlspecialchars($_SESSION['usuario']) ?></span>
      <?php endif; ?>
      <a href="logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
  </header>

  <main>
    <h1 class="page-title">Panel de administración</h1>
    <p class="page-subtitle">Gestiona el inventario de Papelería Tony</p>

    <?php
      // Métricas rápidas
      $total_articulos = $resultado->num_rows;
      $resultado->data_seek(0);
      $total_valor = 0;
      $sin_stock   = 0;
      $rows_all    = [];
      while ($r = $resultado->fetch_assoc()) {
          $rows_all[]   = $r;
          $total_valor += $r['precio'] * $r['stock'];
          if ($r['stock'] == 0) $sin_stock++;
      }
    ?>

    <div class="stats">
      <div class="stat-card">
        <div class="stat-label">Artículos</div>
        <div class="stat-valor"><?= $total_articulos ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Valor en inventario</div>
        <div class="stat-valor">$<?= number_format($total_valor, 2) ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Sin stock</div>
        <div class="stat-valor" style="color: <?= $sin_stock > 0 ? 'var(--rojo)' : 'var(--verde)' ?>">
          <?= $sin_stock ?>
        </div>
      </div>
    </div>

    <div class="layout-grid">

      <!-- ── FORMULARIO AGREGAR ── -->
      <div class="card">
        <h2 class="card-titulo">Agregar artículo</h2>

        <?php if (!empty($errores)): ?>
          <div class="alerta alerta-error" role="alert">
            <ul><?php foreach ($errores as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
          </div>
        <?php endif; ?>

        <?php if ($exito): ?>
          <div class="alerta alerta-exito" role="status">✅ <?= $exito ?></div>
        <?php endif; ?>

        <form method="POST" action="admin.php" novalidate>
          <div class="campo">
            <label for="nombre">Nombre del artículo</label>
            <input type="text" id="nombre" name="nombre"
              value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
              placeholder="Ej. Cuaderno universitario" required>
          </div>

          <div class="campo">
            <label for="marca">Marca</label>
            <input type="text" id="marca" name="marca"
              value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>"
              placeholder="Ej. Scribe, Pilot, Faber" required>
          </div>

          <div class="campo">
            <label for="precio">Precio</label>
            <div class="input-grupo">
              <span class="input-prefijo">$</span>
              <input type="number" id="precio" name="precio"
                value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>"
                min="0" step="0.01" placeholder="0.00" required>
            </div>
          </div>

          <div class="campo">
            <label for="stock">Stock inicial</label>
            <input type="number" id="stock" name="stock"
              value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>"
              min="0" step="1" placeholder="0" required>
          </div>

          <button type="submit" class="btn-guardar">+ Guardar artículo</button>
        </form>
      </div>

      <!-- ── TABLA DE ARTÍCULOS ── -->
      <div class="card">
        <div class="tabla-header">
          <h2 class="card-titulo" style="margin:0; border:none; padding:0;">Lista de artículos</h2>
          <div class="buscador">
            <span class="buscador-icono">🔍</span>
            <input type="text" id="buscar" placeholder="Buscar artículo..." oninput="filtrarTabla()">
          </div>
        </div>
        <span id="conteo-articulos"><?= $total_articulos ?> artículo<?= $total_articulos !== 1 ? 's' : '' ?></span>

        <div class="tabla-wrap" style="margin-top: 1rem;">
          <?php if (empty($rows_all)): ?>
            <div class="tabla-vacia">📦 No hay artículos registrados aún.</div>
          <?php else: ?>
            <table id="tabla-articulos">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Marca</th>
                  <th>Precio</th>
                  <th>Stock</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rows_all as $row): ?>
                  <tr>
                    <td class="td-id">#<?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td style="color:var(--suave)"><?= htmlspecialchars($row['marca']) ?></td>
                    <td class="td-precio">$<?= number_format($row['precio'], 2) ?></td>
                    <td class="td-stock">
                      <?php
                        $s = (int)$row['stock'];
                        $clase = $s === 0 ? 'stock-cero' : ($s <= 5 ? 'stock-bajo' : 'stock-ok');
                      ?>
                      <span class="badge-stock <?= $clase ?>"><?= $s ?></span>
                    </td>
                    <td>
                      <div class="acciones-td">
                        <a href="editar.php?id=<?= (int)$row['id'] ?>" class="btn-accion btn-editar">✏️ Editar</a>
                        <a href="eliminar.php?id=<?= (int)$row['id'] ?>"
                           class="btn-accion btn-eliminar"
                           onclick="return confirm('¿Seguro que quieres eliminar «<?= htmlspecialchars(addslashes($row['nombre'])) ?>»?')">
                           🗑 Eliminar
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>

  <footer>
    <p>© 2026 Papelería Tony · Taller de Sistemas Operativos · Equipo 5</p>
  </footer>

  <script>
    function filtrarTabla() {
      var q     = document.getElementById('buscar').value.toLowerCase();
      var filas = document.querySelectorAll('#tabla-articulos tbody tr');
      var vis   = 0;
      filas.forEach(function(fila) {
        var texto = fila.textContent.toLowerCase();
        var mostrar = texto.includes(q);
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) vis++;
      });
      document.getElementById('conteo-articulos').textContent =
        vis + ' artículo' + (vis !== 1 ? 's' : '');
    }
  </script>

</body>
</html>
