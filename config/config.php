<?php
// Configuración de la base de datos
$dbhost = 'localhost';
$dbname = 'cotizador';
$dbuser = 'postgres';
$dbpass = 'PG240819ea';
$dbport = '5433';

try {
    $conexion = new PDO("pgsql:host=$dbhost;dbname=$dbname;port=$dbport", $dbuser, $dbpass);
    // Habilitar excepciones en caso de errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<script>console.log("Conexión exitosa a PostgreSQL");</script>';
} catch (PDOException $e) {
    echo "Error de conexión a PostgreSQL: " . $e->getMessage();
}


$activos = array();
?>

