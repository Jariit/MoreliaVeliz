<?php
session_start();
include("inc/conexion.php");

$error = "";

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    // Verifica si es admin
    $stmtAdmin = $conn->prepare("SELECT * FROM usuarios_admin WHERE usuario = :usuario");
    $stmtAdmin->execute([':usuario' => $usuario]);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($contrasena, $admin['contrasena'])) {
        $_SESSION['admin'] = $admin['usuario'];
        header("Location: admin/admin_panel.php");
        exit;
    }

    // Verifica si es usuario registrado
    $stmtUser = $conn->prepare("SELECT * FROM clientes_usuarios WHERE telefono = :usuario OR email = :usuario");
    $stmtUser->execute([':usuario' => $usuario]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($contrasena, $user['contrasena'])) {
        $_SESSION['usuario'] = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['usuario_id'] = $user['id'];
        header("Location: usuario_inicio.php");
        exit;
    }

    $error = "Credenciales incorrectas.";
}

// REGISTRO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    // Validar duplicado
    $stmtCheck = $conn->prepare("SELECT * FROM clientes_usuarios WHERE telefono = :tel OR email = :email");
    $stmtCheck->execute([':tel' => $telefono, ':email' => $email]);
    if ($stmtCheck->fetch()) {
        $error = "El teléfono o email ya está registrado.";
    } else {
        // Insertar nuevo usuario
        $stmtInsert = $conn->prepare("INSERT INTO clientes_usuarios (nombre, apellido, telefono, email, contrasena) VALUES (:nombre, :apellido, :telefono, :email, :contrasena)");
        $stmtInsert->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':telefono' => $telefono,
            ':email' => $email,
            ':contrasena' => $contrasena
        ]);

        // Login automático
        $_SESSION['usuario'] = $nombre . ' ' . $apellido;
        $_SESSION['usuario_id'] = $conn->lastInsertId();
        header("Location: usuario_inicio.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión / Registrarse</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen p-4">

  <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-pink-600 mb-4">Bienvenido</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 p-2 text-sm rounded mb-4 text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Navegación Login/Registro -->
    <div class="flex justify-center mb-4">
      <button onclick="mostrarFormulario('login')" class="px-4 py-2 bg-pink-100 text-pink-700 rounded-l hover:bg-pink-200">Iniciar Sesión</button>
      <button onclick="mostrarFormulario('registro')" class="px-4 py-2 bg-pink-100 text-pink-700 rounded-r hover:bg-pink-200">Registrarse</button>
    </div>

    <!-- Formulario Login -->
    <form method="POST" id="formLogin" class="space-y-4">
      <input type="hidden" name="login" value="1">
      <input type="text" name="usuario" placeholder="Teléfono o Email" required class="w-full border p-2 rounded">
      <input type="password" name="contrasena" placeholder="Contraseña" required class="w-full border p-2 rounded">
      <button type="submit" class="w-full bg-pink-500 text-white py-2 rounded hover:bg-pink-600">Ingresar</button>
    </form>

    <!-- Formulario Registro -->
    <form method="POST" id="formRegistro" class="space-y-3 hidden mt-4">
      <input type="hidden" name="registro" value="1">
      <input type="text" name="nombre" placeholder="Nombre" required class="w-full border p-2 rounded">
      <input type="text" name="apellido" placeholder="Apellido" required class="w-full border p-2 rounded">
      <input type="text" name="telefono" placeholder="Teléfono" required class="w-full border p-2 rounded">
      <input type="email" name="email" placeholder="Correo electrónico" required class="w-full border p-2 rounded">
      <input type="password" name="contrasena" placeholder="Contraseña" required class="w-full border p-2 rounded">
      <button type="submit" class="w-full bg-pink-500 text-white py-2 rounded hover:bg-pink-600">Registrarse</button>
    </form>
  </div>

  <script>
    function mostrarFormulario(formulario) {
      document.getElementById("formLogin").classList.add("hidden");
      document.getElementById("formRegistro").classList.add("hidden");
      document.getElementById("form" + formulario.charAt(0).toUpperCase() + formulario.slice(1)).classList.remove("hidden");
    }
  </script>

</body>
</html>

