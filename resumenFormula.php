<?php
include ('include/header.php');
// generarResumenFormula.php

// Recibe los datos del formulario
$nombreFormula = $_POST['nombre'];
$diasTratamiento = $_POST['dias'];
$activosIngresados = json_decode($_POST['activosIngresados'], true); // Decodifica la cadena JSON

// Luego, puedes generar la página de resumen (resumenFormula.html) con los datos
$html = file_get_contents("pages/resumenFormula.html");
$html = str_replace('{menufarmacia}', $menufarmacia, $html);
$html = str_replace('{nombre}', $nombreFormula, $html);
$html = str_replace('{dias}', $diasTratamiento, $html);

// Construye la tabla de activos a partir del arreglo $activosIngresados
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

echo $html;
?>
