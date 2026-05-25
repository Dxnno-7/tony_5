<?php
include("conexion.php");

$id = $_GET['id'];

$resultado = $conexion->query("SELECT * FROM articulos WHERE id=$id");
$row = $resultado->fetch_assoc();

if ($_POST) {
    $nombre = $_POST['nombre'];
    $marca = $_POST['marca'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $conexion->query("UPDATE articulos SET 
    nombre='$nombre', 
    marca='$marca', 
    precio='$precio', 
    stock='$stock' 
    WHERE id=$id");

    header("Location: admin.php");
}
?>

<form method="POST">
    Nombre: <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>"><br>
    Marca: <input type="text" name="marca" value="<?php echo $row['marca']; ?>"><br>
    Precio: <input type="number" name="precio" value="<?php echo $row['precio']; ?>"><br>
    Stock: <input type="number" name="stock" value="<?php echo $row['stock']; ?>"><br>
    <button type="submit">Actualizar</button>
</form>
