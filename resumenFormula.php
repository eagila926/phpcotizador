<?php
require_once('config/config.php'); // Incluye el archivo de configuración
include('include/header.php');

// Recibe los datos del formulario
$nombreFormula = $_POST['nombre'];
$diasTratamiento = $_POST['dias'];
$activosIngresados = json_decode($_POST['activosIngresados'], true); // Decodifica la cadena JSON

// Luego, puedes gen    |erar la página de resumen (resumenFormula.html) con los datos
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

$html = str_replace('{tablaActivosIngresados}', $tableHtml, $html);

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
    $vCap = $mfC / $detalle['densidad'];

    $vTotal += $vCap;

    $detalle['cantidad'] = $cantidadAjustada;
    
    $detallesActivosModificados[] = $detalle;

}


// Calcular $cDiaria según el valor de $vTotal
$cDiaria = 1; 
$cantidadCap = 0; 
$capacidadCap = 0; 

if ($vTotal <= 0.68) {
    $cDiaria = 1;
    $codInven = 1077;
    $capacidadCap=0.68;
} elseif ($vTotal >= 0.69 && $vTotal <= 0.95) {
    $cDiaria = 1;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 0.96 && $vTotal <= 1.36) {
    $cDiaria = 2;
    $codInven = 1077;
    $capacidadCap=0.68;
} elseif ($vTotal >= 1.37 && $vTotal <= 1.90) {
    $cDiaria = 2;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 1.91 && $vTotal <= 2.04) {
    $cDiaria = 3;
    $codInven = 1077;
    $capacidadCap=0.68;
} elseif ($vTotal >= 2.05 && $vTotal <= 2.85) {
    $cDiaria = 3;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 2.86 && $vTotal <= 3.8) {
    $cDiaria = 4;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 3.81 && $vTotal <= 4.75) {
    $cDiaria = 5;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 4.76 && $vTotal <= 5.7) {
    $cDiaria = 6;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 5.71 && $vTotal <= 6.65) {
    $cDiaria = 7;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 6.66 && $vTotal <= 7.6) {
    $cDiaria = 8;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 7.7 && $vTotal <= 8.55) {
    $cDiaria = 9;
    $codInven = 1078;
    $capacidadCap=0.95;
} elseif ($vTotal >= 8.56 && $vTotal <= 9.5) {
    $cDiaria = 10;
    $codInven = 1078;
    $capacidadCap=0.95;
}

// Realiza una consulta SQL para obtener los detalles del activo según $codInven
$sql = "SELECT cod_inven, descripcion, unidad_compra, factor, densidad, tipo FROM activos WHERE cod_inven = :codInven";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':codInven', $codInven, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $cantidadCap = $cDiaria * $diasTratamiento;
    $result['cantidad'] = $cantidadCap; // Agrega la cantidad calculada al resultado
    $result['unidad'] = 'und'; 
    $detallesActivosModificados[] = $result;
}
//seleccionar exipiente segun los activos de la formula
$contadorProbioticos = 0; // Contador para activos de tipo "PROBIOTICO"

foreach ($detallesActivosModificados as $detalle) {
    $codInven = isset($activo['codOdoo']) ? $activo['codOdoo'] : '';
    $sql = "SELECT cod_inven, descripcion, unidad_compra, factor, densidad, tipo FROM activos WHERE cod_inven = :codInven";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':codInven', $codInven, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $detallesActivos[] = $result;
        
        // Incrementa el contador si el tipo es "PROBIOTICO"
        if ($result['tipo'] === "PROBIOTICO") {
            $contadorProbioticos++;
        }
    }
}

// Lógica para agregar el activo según el contador
if ($contadorProbioticos >= 2) {
    // Agregar el activo con cod_inven 1177
    $sql = "SELECT cod_inven, descripcion, unidad_compra, factor, densidad, tipo FROM activos WHERE cod_inven = 1177";
} else {
    // Agregar el activo con cod_inven 1101
    $sql = "SELECT cod_inven, descripcion, unidad_compra, factor, densidad, tipo FROM activos WHERE cod_inven = 1101";
}

// Realizar la consulta SQL para obtener el activo seleccionado
$stmt = $conexion->prepare($sql);
$stmt->execute();
$activoSeleccionado = $stmt->fetch(PDO::FETCH_ASSOC);

// Agregar el activo seleccionado a $detallesActivos si no existe en el arreglo
if (!in_array($activoSeleccionado, $detallesActivosModificados)) {
    $vtnCapsulas = $vTotal/$cDiaria;
    echo "<script>console.log('AB: " . $vtnCapsulas . "');</script>";
    echo "<script>console.log('AB: " . $capacidadCap . "');</script>";

    $capXcap = $capacidadCap - $vtnCapsulas;

    echo "<script>console.log('AC: " . $capXcap . "');</script>";

    $cantidadExipiente = $capXcap*$activoSeleccionado['densidad'];

    $activoSeleccionado['cantidad'] = number_format($cantidadExipiente, 4);
    $activoSeleccionado['unidad']='g';
    $detallesActivosModificados[] = $activoSeleccionado;
}

// Mostrar $cDiaria antes de la tabla
$html = str_replace('{cDiaria}', $cDiaria, $html);

// Construye la tabla de detalles de activos
$detallesHtml = '';
foreach ($detallesActivosModificados as $detalle) {

    $cantidadAjustada = $detalle['cantidad'];
    $mfC = $cantidadAjustada * $detalle['factor'];
    if ($detalle['densidad'] != 0) {
        $vCap = $mfC / $detalle['densidad'];
    } else {
        // Manejar el caso donde la densidad es cero, por ejemplo, asignando un valor predeterminado
        $vCap = 0;
    }
    $mfF = $mfC * $cantidadCap;

    $detallesHtml .= '<tr>';
    $detallesHtml .= '<td>' . $detalle['cod_inven'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['descripcion'] . '</td>';
    $detallesHtml .= '<td>' . $detalle['cantidad'] . '</td>'; 
    $unidad = isset($detalle['unidad']) ? $detalle['unidad'] : '';
    $detallesHtml .= '<td>' . $unidad . '</td>';
    $detallesHtml .= '<td>' . number_format($detalle['factor'], 2) . '</td>';
    $detallesHtml .= '<td>' . $detalle['densidad'] . '</td>';
    $detallesHtml .= '<td>' . $mfC . '</td>';
    $detallesHtml .= '<td>' . number_format($vCap, 4) . '</td>';
    $detallesHtml .= '<td>' . $mfF . '</td>';
    $detallesHtml .= '</tr>';

    $detalleArray = array(
        'cod_inven' => $detalle['cod_inven'],
        'descripcion' => $detalle['descripcion'],
        'cantidad' => $detalle['cantidad'],
        'unidad' => $unidad,
        'factor' => number_format($detalle['factor'], 2),
        'densidad' => $detalle['densidad'],
        'mfC' => $mfC,
        'vCap' => number_format($vCap, 4),
        'mfF' => $mfF,
    );
    $detallesArray[] = $detalleArray;
}

echo "<script>console.log('Arreglo: " . json_encode($detallesArray) . "');</script>";


// Agrega la variable $vTotal al final de la tabla
$detallesHtml .= '<tr><td colspan="6"></td><td><strong>Total:</strong></td><td>' . number_format($vTotal, 5) . '</td></tr>';

$html = str_replace('{tablaResumen}', $detallesHtml, $html);

$tablaPrecios = '';
foreach($detalleArray as $detalle){


}

echo $html;
?>
