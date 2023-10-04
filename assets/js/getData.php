
<?php

require_once('../../config/config.php');//Recuperar los datos del activo seleccionado
if (isset($_POST['assetId'])) {
    $assetId = $_POST['assetId'];

    // Query to fetch asset data based on assetId
    $sql = "SELECT * FROM activos WHERE cod_inven = :assetId";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':assetId', $assetId, PDO::PARAM_INT);
    $stmt->execute();

    $response = array();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Fetched asset data
        $response['success'] = true;
        $response['asset'] = $row;
    } else {
        $response['success'] = false;
        $response['message'] = 'Asset not found.';
    }

    echo json_encode($response);
} else {
    // Handle the case where assetId is not provided
    $response = array('success' => false, 'message' => 'AssetId not provided.');
    echo json_encode($response);
}

?>