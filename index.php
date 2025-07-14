<?php
include("inc/conexion.php");

$stmt = $conn->prepare("
  SELECT nombre_promocion, descripcion, imagen_promocion 
  FROM promociones 
  WHERE activa = TRUE 
  ORDER BY valido_desde DESC 
  LIMIT 5
");
$stmt->execute();
$promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Morelia Véliz Beauty Salon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    .carousel-button:hover { color: #db2777; }
  </style>
</head>
<body class="bg-pink-50 font-sans flex flex-col min-h-screen">

  <!-- CABECERA -->
  <header class="bg-pink-600 text-white shadow">
    <div class="container mx-auto flex justify-between items-center p-4">
      <div class="flex items-center space-x-3">
        <img src="inc/assets/logo.jpg" alt="Logo" class="w-10 h-10 rounded-full bg-white"/>
        <span class="text-2xl font-bold">Morelia Véliz Beauty Salon</span>
      </div>
      <nav>
        <ul class="flex gap-6 text-lg font-medium">
          <li><a href="index.php" class="hover:underline">Inicio</a></li>
          <li><a href="servicios.php" class="hover:underline">Servicios</a></li>
          <li><a href="login.php" class="hover:underline">Iniciar Sesión</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- CARRUSEL -->
  <section class="relative mt-8 max-w-6xl mx-auto overflow-hidden rounded-lg shadow-lg">
    <div id="carousel" class="relative w-full">
      <div id="carousel-inner" class="flex transition-transform duration-700">
        <img src="https://picsum.photos/400/300?random=1" alt="Slide 1" class="w-full object-cover h-80 md:h-96"/>
        <img src="https://picsum.photos/400/300?random=1" alt="Slide 2" class="w-full object-cover h-80 md:h-96"/>
        <img src="https://picsum.photos/400/300?random=1" alt="Slide 3" class="w-full object-cover h-80 md:h-96"/>
      </div>
      <button id="prev" class="absolute top-1/2 left-3 -translate-y-1/2 text-white text-3xl carousel-button"><i class="fas fa-chevron-left"></i></button>
      <button id="next" class="absolute top-1/2 right-3 -translate-y-1/2 text-white text-3xl carousel-button"><i class="fas fa-chevron-right"></i></button>
    </div>
  </section>

  <!-- RECOMENDACIONES -->
  <section class="mt-16 px-6 max-w-6xl mx-auto">
    <h2 class="text-3xl font-bold text-pink-700 mb-10 text-center">Recomendaciones</h2>
    <div class="grid gap-8 md:grid-cols-3">
      <div class="bg-white shadow rounded-lg p-6 text-center">
        <i class="fas fa-hand-sparkles text-pink-600 text-4xl mb-4"></i>
        <h3 class="text-xl font-semibold mb-2">Calidad Profesional</h3>
        <p>Materiales premium y personal especializado.</p>
      </div>
      <div class="bg-white shadow rounded-lg p-6 text-center">
        <i class="fas fa-clock text-pink-600 text-4xl mb-4"></i>
        <h3 class="text-xl font-semibold mb-2">Horario Flexible</h3>
        <p>Agenda en los horarios que más se ajusten a ti.</p>
      </div>
      <div class="bg-white shadow rounded-lg p-6 text-center">
        <i class="fas fa-gift text-pink-600 text-4xl mb-4"></i>
        <h3 class="text-xl font-semibold mb-2">Promociones</h3>
        <p>Descuentos exclusivos para usuarios registrados.</p>
      </div>
    </div>
  </section>

  <!-- BANNER PROMOCIONES -->
<section class="mt-20 px-6 max-w-6xl mx-auto">
  <h2 class="text-2xl font-bold text-center text-pink-700 mb-8">Nuestras Promociones</h2>
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($promociones as $promo): ?>
      <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
        <?php if (!empty($promo['imagen_promocion'])): ?>
          <img src="<?= htmlspecialchars($promo['imagen_promocion']) ?>" alt="<?= htmlspecialchars($promo['nombre_promocion']) ?>" class="w-full h-48 object-cover rounded mb-4">
        <?php endif; ?>
        <h3 class="text-lg font-bold text-pink-600 mb-2"><?= htmlspecialchars($promo['nombre_promocion']) ?></h3>
        <p class="text-sm text-gray-600"><?= htmlspecialchars(substr($promo['descripcion'], 0, 100)) ?>...</p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

  <!-- UNETE A LA COMUNIDAD -->
  <section class="mt-20 px-6 py-12 bg-white rounded-lg shadow-lg max-w-4xl mx-auto text-center">
    <h2 class="text-3xl font-extrabold text-pink-700 mb-6">Únete a Nuestra Comunidad</h2>
    <p class="text-lg mb-6 text-gray-700">
      Regístrate para acceder a promociones exclusivas y gestionar tus citas de manera fácil y rápida.
    </p>
    <a href="registro.php" class="bg-pink-600 text-white px-8 py-3 rounded-md font-semibold shadow hover:bg-pink-700 transition">
      <i class="fas fa-user-plus mr-2"></i> Registrarse
    </a>
  </section>

  <!-- FOOTER -->
  <footer class="bg-pink-600 text-white text-center py-8 mt-24">
    <p class="mb-2">&copy; 2025 Morelia Véliz Beauty Salon </p>
    <p class="mb-4"><i class="fas fa-map-marker-alt mr-2"></i>Av. Principal #123, Ciudad, País</p>
    <div class="flex justify-center gap-6 text-xl">
      <a href="#" class="hover:text-pink-300"><i class="fab fa-facebook-f"></i></a>
      <a href="#" class="hover:text-pink-300"><i class="fab fa-instagram"></i></a>
      <a href="#" class="hover:text-pink-300"><i class="fab fa-whatsapp"></i></a>
    </div>
  </footer>

  <!-- SCRIPT CARRUSEL -->
  <script>
    const carouselInner = document.getElementById("carousel-inner");
    const total = carouselInner.children.length;
    let idx = 0;
    document.getElementById("prev").onclick = () => {
      idx = (idx - 1 + total) % total; carouselInner.style.transform = `translateX(-${idx*100}%)`;
    };
    document.getElementById("next").onclick = () => {
      idx = (idx + 1) % total; carouselInner.style.transform = `translateX(-${idx*100}%)`;
    };
    setInterval(() => { idx = (idx + 1) % total; carouselInner.style.transform = `translateX(-${idx*100}%)`; }, 6000);
  </script>

</body>
</html>