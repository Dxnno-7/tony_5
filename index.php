<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Tony – Equipo 5</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --azul:     #185FA5;
      --azul-clr: #E6F1FB;
      --verde:    #3B6D11;
      --verde-clr:#EAF3DE;
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
      letter-spacing: -0.02em;
    }

    .logo span {
      color: var(--verde);
    }

    nav {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    nav a {
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--suave);
      padding: 0.45rem 1rem;
      border-radius: 999px;
      transition: background 0.2s, color 0.2s;
    }

    nav a:hover { background: var(--fondo); color: var(--texto); }

    nav a.btn-login {
      background: var(--azul);
      color: white;
    }
    nav a.btn-login:hover { background: #0C447C; }

    /* ── HERO ── */
    .hero {
      background: var(--blanco);
      border-bottom: 0.5px solid var(--borde);
      padding: 5rem 2rem;
      text-align: center;
    }

    .hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2rem, 5vw, 3.2rem);
      color: var(--texto);
      margin-bottom: 1rem;
      line-height: 1.15;
    }

    .hero h1 em {
      font-style: normal;
      color: var(--azul);
    }

    .hero p {
      font-size: 1.1rem;
      color: var(--suave);
      max-width: 480px;
      margin: 0 auto 2rem;
      line-height: 1.7;
    }

    .hero-btns {
      display: flex;
      gap: 0.75rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-block;
      padding: 0.65rem 1.5rem;
      border-radius: 999px;
      font-size: 0.95rem;
      font-weight: 500;
      text-decoration: none;
      transition: transform 0.15s, box-shadow 0.15s;
    }

    .btn:hover { transform: translateY(-1px); }

    .btn-primary {
      background: var(--azul);
      color: white;
    }

    .btn-secondary {
      background: var(--verde-clr);
      color: var(--verde);
      border: 0.5px solid #C0DD97;
    }

    /* ── SECCIÓN GENÉRICA ── */
    .seccion {
      max-width: 960px;
      margin: 4rem auto;
      padding: 0 2rem;
      width: 100%;
    }

    .seccion-titulo {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      margin-bottom: 1.5rem;
      color: var(--texto);
    }

    .seccion-titulo span {
      color: var(--azul);
    }

    /* ── TARJETAS DE PRODUCTOS ── */
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .tarjeta {
      background: var(--blanco);
      border: 0.5px solid var(--borde);
      border-radius: var(--radio);
      padding: 1.5rem 1.25rem;
      transition: border-color 0.2s, transform 0.2s;
    }

    .tarjeta:hover {
      border-color: rgba(24,95,165,0.3);
      transform: translateY(-2px);
    }

    .tarjeta-icono {
      font-size: 2rem;
      margin-bottom: 0.75rem;
    }

    .tarjeta h3 {
      font-size: 1rem;
      font-weight: 500;
      margin-bottom: 0.4rem;
    }

    .tarjeta p {
      font-size: 0.875rem;
      color: var(--suave);
      line-height: 1.6;
    }

    /* ── MISIÓN Y VISIÓN ── */
    .mv-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    @media (max-width: 600px) {
      .mv-grid { grid-template-columns: 1fr; }
    }

    .mv-card {
      background: var(--blanco);
      border: 0.5px solid var(--borde);
      border-radius: var(--radio);
      padding: 1.5rem;
    }

    .mv-card .etiqueta {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      padding: 0.25rem 0.75rem;
      border-radius: 999px;
      margin-bottom: 0.75rem;
    }

    .etiqueta-azul  { background: var(--azul-clr);  color: var(--azul); }
    .etiqueta-verde { background: var(--verde-clr); color: var(--verde); }

    .mv-card p {
      font-size: 0.95rem;
      color: var(--suave);
      line-height: 1.7;
    }

    /* ── ESPACIADOR ── */
    .flex-grow { flex: 1; }

    /* ── FOOTER ── */
    footer {
      background: var(--blanco);
      border-top: 0.5px solid var(--borde);
      padding: 1.5rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 0.5rem;
    }

    footer p {
      font-size: 0.85rem;
      color: var(--suave);
    }

    footer a {
      font-size: 0.85rem;
      color: var(--azul);
      text-decoration: none;
    }

    footer a:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <!-- ENCABEZADO -->
  <header>
    <a href="index.php" class="logo">Papelería <span>Tony</span></a>
    <nav>
      <a href="index.php">Inicio</a>
      <a href="#productos">Productos</a>
      <a href="#nosotros">Nosotros</a>
      <a href="login.php" class="btn-login">Iniciar sesión</a>
    </nav>
  </header>

  <!-- HERO -->
  <section class="hero">
    <h1>Tu papelería de<br><em>confianza en el ITO</em></h1>
    <p>Artículos escolares y de oficina al mejor precio. Siempre cerca de ti, siempre listos para ayudarte.</p>
    <div class="hero-btns">
      <a href="#productos" class="btn btn-primary">Ver productos</a>
      <a href="login.php"  class="btn btn-secondary">Acceder a mi cuenta</a>
    </div>
  </section>

  <!-- PRODUCTOS -->
  <section class="seccion" id="productos">
    <h2 class="seccion-titulo">Nuestros <span>productos</span></h2>
    <div class="grid">
      <div class="tarjeta">
        <div class="tarjeta-icono">✏️</div>
        <h3>Material escolar</h3>
        <p>Lápices, plumas, cuadernos, carpetas y todo lo que necesitas para clase.</p>
      </div>
      <div class="tarjeta">
        <div class="tarjeta-icono">🖨️</div>
        <h3>Impresiones</h3>
        <p>Servicio de impresión en blanco y negro o a color, tamaños carta y oficio.</p>
      </div>
      <div class="tarjeta">
        <div class="tarjeta-icono">📦</div>
        <h3>Material de oficina</h3>
        <p>Grapas, clips, folders, sobres y accesorios para tu espacio de trabajo.</p>
      </div>
      <div class="tarjeta">
        <div class="tarjeta-icono">🎨</div>
        <h3>Arte y manualidades</h3>
        <p>Pinturas, cartulinas, tijeras, resistol y materiales para proyectos creativos.</p>
      </div>
    </div>
  </section>

  <!-- MISIÓN Y VISIÓN -->
  <section class="seccion" id="nosotros">
    <h2 class="seccion-titulo">Misión y <span>visión</span></h2>
    <div class="mv-grid">
      <div class="mv-card">
        <span class="etiqueta etiqueta-azul">Misión</span>
        <p>Servir a la comunidad estudiantil del ITO ofreciendo productos de calidad a precios accesibles, con atención cercana y confiable.</p>
      </div>
      <div class="mv-card">
        <span class="etiqueta etiqueta-verde">Visión</span>
        <p>Ser la mejor y más reconocida papelería de la región, referente de confianza para estudiantes y profesionales.</p>
      </div>
    </div>
  </section>

  <div class="flex-grow"></div>

  <!-- PIE DE PÁGINA -->
  <footer>
    <p>© 2026 Papelería Tony · Taller de Sistemas Operativos · Equipo 5</p>
    <a href="login.php">Iniciar sesión</a>
  </footer>

</body>
</html>
