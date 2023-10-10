<?php
require_once('config/config.php');
include ('include/header.php');

// Verifica si el formulario se envió y el botón "Guardar" fue presionado
if (isset($_POST["guardar"])) {
    // Recupera los valores del formulario
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $ciudad = $_POST["ciudad"];
    $pais = $_POST["pais"];
    $username = $_POST["username"];
    $correo = $_POST["correo"];
    $nivel = $_POST["nivel"];
    $contrasena = $_POST["contrasena"]; 
    $activo = isset($_POST["activo"]) ? 1 : 0; // Si el checkbox está marcado, establece activo en 1; de lo contrario, en 0.

    try {
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Preparar la consulta SQL para insertar un nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, apellido, ciudad, pais, username, correo, nivel, contraseña, activo) 
                VALUES (:nombre, :apellido, :ciudad, :pais, :username, :correo, :nivel, :contrasena, :activo)"; // Cambiado a ":contrasena"

        $stmt = $conexion->prepare($sql);

        // Encriptar la contrasena antes de insertarla
        $contrasenaEncriptada = password_hash($contrasena, PASSWORD_BCRYPT); // Cambiado a "contrasena"

        // Bind de parámetros
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':nivel', $nivel);
        $stmt->bindParam(':contrasena', $contrasenaEncriptada); // Cambiado a ":contrasena"
        $stmt->bindParam(':activo', $activo);

        // Ejecutar la consulta
        $stmt->execute();

        // Redireccionar o mostrar un mensaje de éxito
        // header("Location: exito.html");
        echo "Registro exitoso.";

    } catch (PDOException $e) {
        echo "Error al procesar el formulario: " . $e->getMessage();
    }
} else {
    // El formulario no se ha enviado
    echo "El formulario no ha sido enviado.";
}

$html = file_get_contents("pages/registro.html");
$html = str_replace('{menufarmacia}', $menufarmacia, $html);
echo $html;
?>
