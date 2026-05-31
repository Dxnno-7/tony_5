<?php
include("conexion.php");

$nombre = $_POST['nombre'];
$marca = $_POST['marca'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];

$conexion->query("INSERT INTO articulos (nombre, marca, precio, stock) 
VALUES ('$nombre', '$marca', '$precio', '$stock')");

echo "Registro guardado <br>";
echo "<a href='admin.php'>Volver</a>";
?>
