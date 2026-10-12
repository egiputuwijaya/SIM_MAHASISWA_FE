<?php
// API Proxy mengarahkan request langsung ke API online Vercel
$targetBaseUrl = 'https://sim-mhs-api.vercel.app/api/v1';

$endpoint = $_GET['endpoint'] ?? '';
if (empty($endpoint)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Endpoint missing']);
    exit;
}

$url = $targetBaseUrl . $endpoint;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$headers = [];
$incomingHeaders = function_exists('getallheaders') ? getallheaders() : [];
foreach ($incomingHeaders as $name => $value) {
    if (!in_array(strtolower($name), ['host', 'content-length'])) {
        $headers[] = "$name: $value";
    }
}

$input = in_array($method, ['POST', 'PUT', 'PATCH']) ? file_get_contents('php://input') : null;

if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($input !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
} else {
    $options = [
        'http' => [
            'method'        => $method,
            'header'        => implode("\r\n", $headers),
            'ignore_errors' => true,
            'timeout'       => 30
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false
        ]
    ];
    if ($input !== null) {
        $options['http']['content'] = $input;
    }
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    $httpCode = 200;
    if (isset($http_response_header)) {
        foreach ($http_response_header as $hdr) {
            if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/i', $hdr, $m)) {
                $httpCode = (int)$m[1];
            }
        }
    }
}

http_response_code($httpCode ?: 200);
header('Content-Type: application/json');
echo $response;
