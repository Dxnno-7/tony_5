<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

include('conexion.php');

$resultado = $conexion->query("SELECT * FROM articulos");
?>

<h1>Panel de Administracion</h1>

<a href="logout.php">Cerrar sesion</a>

<h2>Agregar articulo</h2>
<form action="guardar.php" method="POST">
    Nombre: <input type="text" name="nombre">
    Marca: <input type="text" name="marca">
    Precio: <input type="number" name="precio">
    Stock: <input type="number" name="stock">
    <button type="submit">Guardar</button>
</form>

<h2>Lista de articulos</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Marca</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Acciones</th>
    </tr>

    <?php while($row = $resultado->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['nombre']; ?></td>
        <td><?php echo $row['marca']; ?></td>
        <td><?php echo $row['precio']; ?></td>
        <td><?php echo $row['stock']; ?></td>
        <td>
            <a href="editar.php?id=<?php echo $row['id']; ?>">Editar</a> | 
            <a href="eliminar.php?id=<?php echo $row['id']; ?>" onclick="return confirm('¿Seguro que quieres eliminar este artículo?')">Eliminar</a>
        </td>
    </tr>
    <?php } ?>
</table>
