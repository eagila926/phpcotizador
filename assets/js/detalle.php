<?php
// Importa la configuración de la base de datos
require_once('config/config.php');

// Verifica si se ha recibido el código Odoo como parámetro
if (isset($_POST['codOdoo'])) {
    // Obtén el código Odoo enviado por AJAX
    $codOdoo = $_POST['codOdoo'];

        // Crea una conexión a la base de datos utilizando PDO
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta SQL para obtener los detalles del activo por código Odoo
        $sql = "SELECT cod_inven, descripcion, unidad_compra, factor, densidad, tipo
                FROM activos
                WHERE cod_inven = :codOdoo";

        // Prepara la consulta SQL    
        $sth = $conexion->prepare($sql);

        // Asocia el parámetro codOdoo con el valor recibido
        $sth->bindParam(':codOdoo', $codOdoo, PDO::PARAM_INT);

        // Ejecuta la consulta
        $sth->execute();

        // Obtiene los resultados de la consulta
        $resultado = $sth->fetch(PDO::FETCH_ASSOC);

        // Verifica si se encontraron resultados
        if ($resultado) {
            // Convierte los resultados a formato JSON y envíalos como respuesta
            echo json_encode($resultado);
            echo '<script>console.log("activos resumen:", ' . json_encode($resultado) . ');</script>';
        }
} else {
    // Si no se proporcionó un código Odoo válido, envía una respuesta de error
    echo json_encode(array("error" => "Código Odoo no proporcionado"));
    // Si no se encontraron resultados, envía una respuesta vacía o un mensaje de error
    echo json_encode(array("mensaje" => "Activo no encontrado"));
}
?>
