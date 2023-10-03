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
    $inventarioHtml .= '<td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editarActivoModal' . $activo['cod_inven'] . '"><i class="fas fa-pen" title="Editar"></i></button></td>';
    $inventarioHtml .= '<td>' . $activo['cod_inven'] . '</td>';
    $inventarioHtml .= '<td>' . $activo['descripcion'] . '</td>'; 
    $inventarioHtml .= '<td>' . $activo['unidad_compra'] . '</td>';
    $inventarioHtml .= '<td>' . $activo['factor'] . '</td>';
    $inventarioHtml .= '<td>' . $activo['densidad'] . '</td>';
    $inventarioHtml .= '<td>' . $activo['stock'] . '</td>';
    $inventarioHtml .= '</tr>';

}

$html = str_replace('{tablaInventario}', $inventarioHtml, $html);

echo $html;

?>


