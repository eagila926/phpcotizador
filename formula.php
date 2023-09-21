<?php 
require_once('config/config.php'); // Incluye el archivo de configuración

include ('include/header.php');

$activosIngresados = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario de cotización
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $dias = $_POST['dias'];

    // Recuperar los activos ingresados desde el campo oculto
    $activosIngresadosJSON = $_POST['activosIngresados'];
    $activosIngresados = json_decode($activosIngresadosJSON, true);

    // Insertar los datos en la tabla formula
    $sql = "INSERT INTO formula (cod_formula, nombre, dias, activos) VALUES (:cod_formula, :nombre, :dias, :activos)";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':cod_formula', $codigo);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':dias', $dias);
    $stmt->bindParam(':activos', $activosIngresados);

    if ($stmt->execute()) {
        // Éxito
        echo "Cotización guardada con éxito.";
    } else {
        // Error
        echo "Error al guardar la cotización.";
    }
}


$html = file_get_contents("pages/formula.html");
$html = str_replace('{menufarmacia}', $menufarmacia, $html);

echo $html;
?>
