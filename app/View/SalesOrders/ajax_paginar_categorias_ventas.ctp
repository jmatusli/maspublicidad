<?php
// Forzar respuesta JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Si hay datos, devolver JSON
if (isset($response)) {
    echo json_encode($response);
} else {
    // Respuesta de error por defecto
    echo json_encode(array(
        'success' => false,
        'message' => 'No data received',
        'error' => 'Missing response data'
    ));
}
exit;
?>
