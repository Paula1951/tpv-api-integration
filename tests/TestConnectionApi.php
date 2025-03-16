<?php
require_once __DIR__ . '/../src/AgoraAPI.php';

use src\AgoraAPI;

$agoraAPI = new AgoraAPI();
$response = $agoraAPI->connectionApi();

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);