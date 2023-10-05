<?php
require_once('../../config/config.php');

if (isset($_POST['assetId'])) {
    $assetId = $_POST['assetId'];

    try {
        // Query to fetch asset data based on assetId
        $sql = "SELECT * FROM activos WHERE cod_inven = :assetId";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':assetId', $assetId, PDO::PARAM_INT);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Fetched asset data
            $response = array('success' => true,'asset' => $row);
        } else {
            $response = array('success' => false, 'message' => 'Asset not found.');
        }
    } catch (PDOException $e) {
        // Handle database errors
        $response = array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
    }
} else {
    // Handle the case where assetId is not provided
    $response = array('success' => false, 'message' => 'AssetId not provided.');
}

// Set response header to indicate JSON content
header('Content-Type: application/json');

// Send the JSON response
echo json_encode($response);
