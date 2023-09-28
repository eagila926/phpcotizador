<?php
require_once('config/config.php'); // Incluye el archivo de configuración
include ('include/header.php');

// Recibe los datos del formulario
$nombreFormula = $_POST['nombre'];
$diasTratamiento = $_POST['dias'];
$activosIngresados = json_decode($_POST['activosIngresados'], true); // Decodifica la cadena JSON

// Luego, puedes generar la página de resumen (resumenFormula.html) con los datos
$html = file_get_contents("pages/resumenFormula.html");
$html = str_replace('{menufarmacia}', $menufarmacia, $html);
$html = str_replace('{nombre}', $nombreFormula, $html);
$html = str_replace('{dias}', $diasTratamiento, $html);

// Construye la tabla de activos ingresados
$tableHtml = '';
foreach ($activosIngresados as $activo) {
    $tableHtml .= '<tr>';
    $tableHtml .= '<td>' . $activo['codOdoo'] . '</td>';
    $tableHtml .= '<td>' . $activo['activo'] . '</td>';
    $tableHtml .= '<td>' . $activo['cantidad'] . '</td>';
    $tableHtml .= '<td>' . $activo['unidad'] . '</td>';
    $tableHtml .= '</tr>';
}

$html = str_replace('<!-- Las filas de activos se agregarán aquí con JavaScript -->', $tableHtml, $html);

// Realiza una consulta SQL para obtener los detalles de los activos
$detallesActivos = array();
foreach ($activosIngresados as $activo) {
    $codInven = $activo['codOdoo'];
    $sql = "SELECT cod_inven, descripcion, unidad_compra, factor, densidad, tipo FROM activos WHERE cod_inven = :codInven";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':codInven', $codInven, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $detallesActivos[] = $result;
        echo '<script>console.log(' . json_encode($detallesActivos) . ');</script>';
    }
}
// Construye la tabla de detalles de activos
$detallesHtml = '';
foreach ($detallesActivos as $detalle) {
    $detallesHtml .= '<tr>';
    $detallesHtml .= '<td>' . $detalle['cod_inven'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['descripcion'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['unidad_compra'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['factor'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['densidad'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['tipo'] . '</td>';
    $detallesHtml .= '</tr>';
}

$html = str_replace('<!-- tabla con datos de activos -->', $detallesHtml, $html);

echo $html;
?>
