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

function flush_it($message, $show_exception = 1) {
    // Aquí puedes implementar la lógica de tu función flush_it()
    
}

function FETCH_SQL($sql,$show_exception=1)
{
	global $conexion; // Corregido: cambiar $jarvis a $conexion
	try
	{
		$conexion->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$sqlit = $conexion->prepare($sql);
		$sqlit->execute();
	}
	catch(PDOException $exception)
	{
		if($show_exception) flush_it(" <hr>exception: ".$exception->getMessage() . "<br>sql is $sql<hr>",1);
		//echo " <hr>exception: ".$exception->getMessage() . "<br>sql is $sql<hr>";
	}
	//print_r($sqlit);
	return $sqlit;

}
?>
