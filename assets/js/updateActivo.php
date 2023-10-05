<?php

require_once('../../config/config.php');//Recuperar los datos del activo seleccionado

//Actualizar los datos 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cod_inven'])) {

    // Handle the POST data sent from the modal form
$cod_inven = $_POST['cod_inven'];
$descripcion = $_POST['descripcion'];
$factor = $_POST['factor'];
$densidad = $_POST['densidad'];
$stock = $_POST['stock'];

// Update the asset data in the database based on $cod_inven
$sql = "UPDATE activos SET descripcion = :descripcion, factor = :factor, densidad = :densidad, stock = :stock WHERE cod_inven = :cod_inven";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':cod_inven', $cod_inven, PDO::PARAM_INT);
$stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
$stmt->bindParam(':factor', $factor, PDO::PARAM_INT);
$stmt->bindParam(':densidad', $densidad, PDO::PARAM_INT);
$stmt->bindParam(':stock', $stock, PDO::PARAM_INT);

$response = array();

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Asset data updated successfully.';
} else {
    $response['success'] = false;
    $response['message'] = 'Failed to update asset data.';
}

echo json_encode($response);

    
}
?>