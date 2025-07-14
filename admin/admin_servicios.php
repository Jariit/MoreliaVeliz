<?php
session_start();
include("../inc/conexion.php");
include("funciones/servicios_funciones.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit;
}

// Carpeta donde guardar imágenes subidas
define('UPLOAD_DIR', 'uploads/servicios/');

// Crear carpeta si no existe
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

$error = null;
$editMode = false;
$servicioEdit = null;

// Procesar eliminar servicio
if (isset($_GET['delete'])) {
    $idEliminar = intval($_GET['delete']);
    eliminarServicio($conn, $idEliminar);
    header("Location: admin_servicios.php");
    exit;
}

// Procesar editar servicio (mostrar formulario con datos)
if (isset($_GET['edit'])) {
    $editMode = true;
    $idEditar = intval($_GET['edit']);
    $servicioEdit = obtenerServicioPorId($conn, $idEditar);
    if (!$servicioEdit) {
        header("Location: admin_servicios.php");
        exit;
    }
}

// Procesar formulario (agregar o actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => $_POST['nombre'] ?? '',
        'descripcion' => $_POST['descripcion'] ?? '',
        'categoria' => $_POST['categoria'] ?? '',
        'esmaltado' => $_POST['esmaltado'] ?? '',
        'precio' => floatval($_POST['precio'] ?? 0),
        'duracion' => intval($_POST['duracion'] ?? 0),
        'extras' => $_POST['extras'] ?? '',
    ];

    if (
        $datos['nombre'] === '' ||
        $datos['categoria'] === '' ||
        $datos['esmaltado'] === '' ||
        $datos['precio'] <= 0 ||
        $datos['duracion'] <= 0
    ) {
        $error = "Complete todos los campos obligatorios correctamente.";
    } else {
        // Procesar subida de imagen (si hay)
        $imagenPath = null;
        if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['imagen_file']['tmp_name'];
            $filename = basename($_FILES['imagen_file']['name']);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $permitidos = ['jpg','jpeg','png','gif'];

            if (!in_array($ext, $permitidos)) {
                $error = "Formato de imagen no permitido. Usa jpg, png, gif.";
            } else {
                $nuevoNombre = uniqid('serv_', true) . '.' . $ext;
                $destino = UPLOAD_DIR . $nuevoNombre;
                if (move_uploaded_file($tmpName, $destino)) {
                    $imagenPath = $destino;
                } else {
                    $error = "Error al subir la imagen.";
                }
            }
        } elseif (!empty($_POST['imagen_url'])) {
            // Si no sube archivo, pero hay URL
            $imagenPath = $_POST['imagen_url'];
        }

        if (!$error) {
            if ($editMode) {
                actualizarServicio($conn, $servicioEdit['id_servicio'], $datos, $imagenPath);
            } else {
                agregarServicio($conn, $datos, $imagenPath);
            }
            header("Location: admin_servicios.php");
            exit;
        }
    }
}

$servicios = obtenerServicios($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin | Servicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

<header class="bg-pink-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold"><i class="fas fa-hand-sparkles mr-2"></i>Servicios - Admin</h1>
        <a href="admin_panel.php" class="hover:underline text-white">
            <i class="fas fa-arrow-left"></i> Volver al panel
        </a>
    </div>
</header>

<main class="max-w-6xl mx-auto py-8 px-4">
    <h2 class="text-2xl font-bold text-pink-700 mb-6">Servicios Registrados</h2>

    <div class="overflow-x-auto mb-10">
        <table class="w-full table-auto text-sm bg-white rounded shadow-md">
            <thead class="bg-pink-100 text-pink-800">
                <tr>
                    <th class="px-4 py-2">Nombre</th>
                    <th>Categoria</th>
                    <th>Esmaltado</th>
                    <th>Precio</th>
                    <th>Duración</th>
                    <th>Activo</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $s): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?= htmlspecialchars($s['nombre_servicio']) ?></td>
                        <td><?= htmlspecialchars($s['categoria']) ?></td>
                        <td><?= htmlspecialchars($s['tipo_esmaltado']) ?></td>
                        <td>$<?= number_format($s['precio'], 2) ?></td>
                        <td><?= $s['duracion_minutos'] ?> min</td>
                        <td><?= $s['activo'] ? '✅' : '❌' ?></td>
                        <td>
                            <?php if ($s['imagen_url']): ?>
                                <img src="<?= htmlspecialchars($s['imagen_url']) ?>" alt="Imagen" class="w-16 h-16 object-cover rounded">
                            <?php else: ?>
                                <span class="text-gray-400">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td class="space-x-2">
                            <a href="?edit=<?= $s['id_servicio'] ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Editar</a>
                            <a href="?delete=<?= $s['id_servicio'] ?>" onclick="return confirm('¿Eliminar este servicio?');" class="text-red-600 hover:underline"><i class="fas fa-trash"></i> Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h2 class="text-xl font-bold text-pink-700 mb-4"><?= $editMode ? 'Editar servicio' : 'Agregar nuevo servicio' ?></h2>

    <?php if ($error): ?>
        <p class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="grid gap-4 bg-white p-6 rounded shadow-md">
        <input type="text" name="nombre" required placeholder="Nombre del servicio" class="border p-2 rounded"
            value="<?= htmlspecialchars($editMode ? $servicioEdit['nombre_servicio'] : ($_POST['nombre'] ?? '')) ?>">

        <textarea name="descripcion" placeholder="Descripción" class="border p-2 rounded"><?= htmlspecialchars($editMode ? $servicioEdit['descripcion'] : ($_POST['descripcion'] ?? '')) ?></textarea>

        <div class="grid grid-cols-2 gap-4">
            <select name="categoria" required class="border p-2 rounded">
                <option value="">-- Categoría --</option>
                <?php 
                $categorias = ['manicure', 'pedicure', 'combo'];
                foreach ($categorias as $cat) {
                    $selected = ($editMode && $servicioEdit['categoria'] == $cat) || (($_POST['categoria'] ?? '') == $cat) ? 'selected' : '';
                    echo "<option value=\"$cat\" $selected>" . ucfirst($cat) . "</option>";
                }
                ?>
            </select>

            <select name="esmaltado" required class="border p-2 rounded">
                <option value="">-- Tipo de esmaltado --</option>
                <?php
                $esmaltados = ['común', 'semipermanente', 'acrílico', 'polygel', 'sin esmaltado'];
                foreach ($esmaltados as $esm) {
                    $selected = ($editMode && $servicioEdit['tipo_esmaltado'] == $esm) || (($_POST['esmaltado'] ?? '') == $esm) ? 'selected' : '';
                    echo "<option value=\"$esm\" $selected>" . ucfirst($esm) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <input type="number" name="precio" step="0.01" placeholder="Precio" required class="border p-2 rounded"
                value="<?= htmlspecialchars($editMode ? $servicioEdit['precio'] : ($_POST['precio'] ?? '')) ?>">

            <input type="number" name="duracion" placeholder="Duración en minutos" required class="border p-2 rounded"
                value="<?= htmlspecialchars($editMode ? $servicioEdit['duracion_minutos'] : ($_POST['duracion'] ?? '')) ?>">
        </div>

        <input type="text" name="extras" placeholder="Extras incluidos (opcional)" class="border p-2 rounded"
            value="<?= htmlspecialchars($editMode ? $servicioEdit['incluye_extras'] : ($_POST['extras'] ?? '')) ?>">

        <label class="block font-medium">Imagen (subir archivo desde tu PC)</label>
        <input type="file" name="imagen_file" accept="image/*" class="border p-2 rounded">

        <label class="block font-medium mt-2">O URL de imagen (opcional)</label>
        <input type="text" name="imagen_url" placeholder="URL de la imagen" class="border p-2 rounded"
            value="<?= htmlspecialchars($editMode ? $servicioEdit['imagen_url'] : ($_POST['imagen_url'] ?? '')) ?>">

        <button type="submit" class="bg-pink-600 text-white py-2 rounded hover:bg-pink-700">
            <i class="fas fa-<?= $editMode ? 'edit' : 'plus-circle' ?>"></i> <?= $editMode ? 'Actualizar' : 'Registrar' ?> Servicio
        </button>
        <?php if ($editMode): ?>
            <a href="admin_servicios.php" class="ml-4 text-gray-600 hover:underline inline-block align-middle">Cancelar edición</a>
        <?php endif; ?>
    </form>
</main>

<footer class="text-center text-gray-500 text-sm mt-12 pb-6">
    &copy; <?= date("Y") ?> Morelia Véliz Beauty Salon - Admin
</footer>
</body>
</html>