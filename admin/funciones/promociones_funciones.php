<?php
// admin/promociones_funciones.php

function obtenerServiciosActivos(PDO $conn) {
    $stmt = $conn->query("SELECT id_servicio, nombre_servicio FROM servicios WHERE activo = TRUE ORDER BY nombre_servicio");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerPromociones(PDO $conn) {
    $stmt = $conn->query("SELECT * FROM promociones ORDER BY valido_desde DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function crearPromocion(PDO $conn, $datos) {
    $sql = "INSERT INTO promociones (
                nombre_promocion, descripcion, tipo_promocion, descuento, precio_promocional, 
                servicios_incluidos, valido_desde, valido_hasta, imagen_promocion, activa
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)";
    $stmt = $conn->prepare($sql);

    // Convierte array servicios a formato PostgreSQL array text (e.g. '{1,2,3}')
    $serviciosStr = '{' . implode(',', $datos['servicios_incluidos']) . '}';

    return $stmt->execute([
        $datos['nombre_promocion'],
        $datos['descripcion'],
        $datos['tipo_promocion'],
        $datos['descuento'] ?: null,
        $datos['precio_promocional'] ?: null,
        $serviciosStr,
        $datos['valido_desde'],
        $datos['valido_hasta'],
        $datos['imagen_promocion'] ?: null
    ]);
}

function eliminarPromocion(PDO $conn, int $id) {
    $stmt = $conn->prepare("DELETE FROM promociones WHERE id_promocion = ?");
    return $stmt->execute([$id]);
}

function obtenerPromocionPorID(PDO $conn, int $id) {
    $stmt = $conn->prepare("SELECT * FROM promociones WHERE id_promocion = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function actualizarPromocion(PDO $conn, array $datos) {
    $sql = "UPDATE promociones SET
            nombre_promocion = ?, descripcion = ?, tipo_promocion = ?, descuento = ?, 
            precio_promocional = ?, servicios_incluidos = ?, valido_desde = ?, valido_hasta = ?, 
            imagen_promocion = ?
            WHERE id_promocion = ?";
    $stmt = $conn->prepare($sql);

    $serviciosStr = '{' . implode(',', $datos['servicios_incluidos']) . '}';

    return $stmt->execute([
        $datos['nombre_promocion'],
        $datos['descripcion'],
        $datos['tipo_promocion'],
        $datos['descuento'] ?: null,
        $datos['precio_promocional'] ?: null,
        $serviciosStr,
        $datos['valido_desde'],
        $datos['valido_hasta'],
        $datos['imagen_promocion'],
        $datos['id_promocion']
    ]);
}