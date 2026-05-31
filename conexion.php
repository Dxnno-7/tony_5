<?php

$conexion = new mysqli(
    "localhost",
    "dev_user",
    "User*2026",
    "Tony_5"
);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

?>
