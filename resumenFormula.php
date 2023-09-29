<?php
require_once('config/config.php'); // Incluye el archivo de configuración
include('include/header.php');

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
        $result['cantidad'] = $activo['cantidad'];
        $result['unidad'] = $activo['unidad'];
        $detallesActivos[] = $result;
    }
}

// Inicializa la variable $vTotal
$vTotal = 0;

// Calcular $vTotal y asignar $cDiaria
foreach ($detallesActivos as $detalle) {
    // Verificar si 'cantidad' y 'unidad' existen antes de imprimirlos
    $cantidad = isset($detalle['cantidad']) ? $detalle['cantidad'] : '';
    $unidad = isset($detalle['unidad']) ? $detalle['unidad'] : '';

    // Ajuste de la cantidad según la unidad
    switch ($unidad) {
        case 'mg':
        case 'UI':
            $cantidadAjustada = $cantidad / 1000;
            break;
        case 'mcg':
            $cantidadAjustada = $cantidad / 1000000;
            break;
        default:
            $cantidadAjustada = $cantidad; // Por defecto, no se aplica ajuste
    }

    $mfC = $cantidadAjustada * $detalle['factor'];
    $vCap = $mfC * $detalle['densidad'];

    $vTotal += $vCap;
}

// Calcular $cDiaria según el valor de $vTotal
$cDiaria = 1; // Valor predeterminado

if ($vTotal <= 0.68) {
    $cDiaria = 1;
} elseif ($vTotal >= 0.69 && $vTotal <= 0.95) {
    $cDiaria = 1;
} elseif ($vTotal >= 0.96 && $vTotal <= 1.36) {
    $cDiaria = 2;
    $codInven = 1077;
    $sql = "SELECT cod_inven, descripcion, unidad_compra, factor, densidad, tipo FROM activos WHERE cod_inven = :codInven";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':codInven', $codInven, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $detallesActivos[] = $result;
    }
} elseif ($vTotal >= 1.37 && $vTotal <= 1.90) {
    $cDiaria = 2;
} elseif ($vTotal >= 1.91 && $vTotal <= 2.04) {
    $cDiaria = 3;
} elseif ($vTotal >= 2.05 && $vTotal <= 2.85) {
    $cDiaria = 3;
} elseif ($vTotal >= 2.86 && $vTotal <= 3.8) {
    $cDiaria = 4;
} elseif ($vTotal >= 3.81 && $vTotal <= 4.75) {
    $cDiaria = 5;
} elseif ($vTotal >= 4.76 && $vTotal <= 5.7) {
    $cDiaria = 6;
} elseif ($vTotal >= 5.71 && $vTotal <= 6.65) {
    $cDiaria = 7;
} elseif ($vTotal >= 6.66 && $vTotal <= 7.6) {
    $cDiaria = 8;
} elseif ($vTotal >= 7.7 && $vTotal <= 8.55) {
    $cDiaria = 9;
} elseif ($vTotal >= 8.56 && $vTotal <= 9.5) {
    $cDiaria = 10;
}

// Mostrar $cDiaria antes de la tabla
$html = str_replace('{cDiaria}', $cDiaria, $html);

// Construye la tabla de detalles de activos
$detallesHtml = '';
foreach ($detallesActivos as $detalle) {
    $detallesHtml .= '<tr>';
    $detallesHtml .= '<td>' . $detalle['cod_inven'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['descripcion'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['cantidad'] . '</td>'; // Mostrar la cantidad original
    $unidad = isset($detalle['unidad']) ? $detalle['unidad'] : '';
    $detallesHtml .= '<td>' . $unidad . '</td>';
    $detallesHtml .= '<td>' . $detalle['factor'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['densidad'] . '</td>';
    
    // Mostrar $cantidadAjustada específica para este activo
    $detallesHtml .= '<td>' . $cantidadAjustada . '</td>';
    
    $detallesHtml .= '<td>' . $mfC . '</td>';
    $detallesHtml .= '<td>' . $vCap . '</td>';
    $detallesHtml .= '</tr>';
}

// Agrega la variable $vTotal al final de la tabla
$detallesHtml .= '<tr><td colspan="6"></td><td><strong>Total:</strong></td><td>' . $vTotal . '</td></tr>';

$html = str_replace('<!-- tabla con datos de activos -->', $detallesHtml, $html);

echo $html;

?>
