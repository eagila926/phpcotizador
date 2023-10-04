<?php 

require_once('config/config.php');
include ('include/header.php');

$html = file_get_contents("pages/inventario.html");
$html = str_replace('{menufarmacia}', $menufarmacia, $html);

$listActivos = array();
// Realizar una consulta SQL para obtener todos los registros de la tabla "activos"
$sql = "SELECT * FROM activos";
$stmt = $conexion->query($sql);

while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $listActivos[] = $result;
}

$inventarioHtml = '';
foreach ($listActivos as $activo) {

    $inventarioHtml .= '<tr>';
    $inventarioHtml .= '<td><button type="button" class="btn btn-primary editar-activo" data-assetid="' . $activo['cod_inven'] . '"><i class="fas fa-plus" title="Editar"></i></button></td>';
    $inventarioHtml .= '<td>' . $activo['cod_inven'] . '</td>';
    $inventarioHtml .= '<td>' . $activo['descripcion'] . '</td>'; 
    $inventarioHtml .= '<td>' . $activo['unidad_compra'] . '</td>';
    $inventarioHtml .= '<td>' . $activo['factor'] . '</td>';
    $inventarioHtml .= '<td>' . $activo['densidad'] . '</td>';
    $inventarioHtml .= '<td>' . $activo['stock'] . '</td>';
    $inventarioHtml .= '</tr>';

}

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



$html = str_replace('{tablaInventario}', $inventarioHtml, $html);

echo $html;

?>


