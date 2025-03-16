<?php
namespace src;

require_once __DIR__ . '/vendor/autoload.php'; // Asegúrate de tener Composer autoload
use Dotenv\Dotenv;

class AgoraAPI {
    private string $baseUrl;
    private string $apiToken;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $server = getenv('SERVER');
        $port = getenv('PORT');
        $this->baseUrl = "http://{$server}:{$port}/"; 
        $this->apiToken = getenv('API_TOKEN');
    }

    public function connectionApi(string $enpoint = ""): array {
        $url = $this->baseUrl . $enpoint;

        $apiRequest  = curl_init($url);
        curl_setopt($apiRequest, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($apiRequest, CURLOPT_TIMEOUT, 10);
        curl_setopt($apiRequest, CURLOPT_HTTPHEADER, [
            "Api-Token: $this->apiToken",
            "Accept: application/json"
        ]);

        $response = curl_exec($apiRequest);
        $httpCode = curl_getinfo($apiRequest, CURLINFO_HTTP_CODE);
        $error = curl_error($apiRequest);
        curl_close($apiRequest);

        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        } else {
            error_log("Error in GET request: HTTP $httpCode - $error");
            return [];
        }
    }
}
