<?php

class ApiClient {
    private static function getBaseUrl() {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptPath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return "http://{$host}{$scriptPath}/api_proxy.php?endpoint=/api/v1";
    }

    public static function post($endpoint, $data = [], $token = null) {
        $url = self::getBaseUrl() . $endpoint;
        
        $headers = [
            "Content-Type: application/json",
            "Accept: application/json"
        ];
        
        if ($token) {
            $headers[] = "Authorization: Bearer " . $token;
        }
        
        $options = [
            'http' => [
                'header'  => implode("\r\n", $headers),
                'method'  => 'POST',
                'content' => json_encode($data),
                'ignore_errors' => true
            ]
        ];
        
        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result === false) {
            return ['status' => false, 'message' => 'Tidak dapat terhubung ke server API.'];
        }
        
        return json_decode($result, true);
    }

    public static function get($endpoint, $params = [], $token = null) {
        $url = self::getBaseUrl() . $endpoint;
        
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        
        $headers = [
            "Accept: application/json"
        ];
        
        if ($token) {
            $headers[] = "Authorization: Bearer " . $token;
        }
        
        $options = [
            'http' => [
                'header'  => implode("\r\n", $headers),
                'method'  => 'GET',
                'ignore_errors' => true
            ]
        ];
        
        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result === false) {
            return ['status' => false, 'message' => 'Tidak dapat terhubung ke server API.'];
        }
        
        return json_decode($result, true);
    }
}
