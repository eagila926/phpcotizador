<?php

require_once('../../config/config.php');

if (!empty($_POST['producto'])) {
    $suggest = "";
    $search = $_POST['producto'];
    
    // Consulta SQL para buscar productos por descripción (insensible a mayúsculas y minúsculas)
    $sql = "SELECT cod_inven, descripcion FROM activos WHERE descripcion ILIKE :search ORDER BY descripcion ASC";
    
    // Prepara la consulta
    $sth = $conexion->prepare($sql);
    
    // Bind de parámetros
    $sth->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    
    // Ejecuta la consulta
    $sth->execute();
    
    // Recorre los resultados y genera sugerencias
    while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
        $cod_inven = $result['cod_inven'];
        echo '<script>console.log("cod_inven from PHP:", ' . json_encode($cod_inven) . ');</script>';
        $descripcion = $result['descripcion'];
        $suggest .= '<div id="' . htmlspecialchars($descripcion) . ' - ' . $cod_inven . '" class="suggest-element" data-cod_inven="' . $cod_inven . '"><a data="' . htmlspecialchars($descripcion) . '">' . htmlspecialchars($descripcion) . '</a></div>';
    }

    echo $suggest;
} else {
    echo "";
}

?>
