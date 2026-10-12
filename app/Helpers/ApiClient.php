<?php

class ApiClient {
    public static function getBaseUrl() {
        return 'https://sim-mhs-api.vercel.app/api/v1';
    }

    public static function request($method, $endpoint, $data = [], $token = null, $isRetry = false) {
        $url = self::getBaseUrl() . $endpoint;
        $method = strtoupper($method);

        $headers = [
            'Accept: application/json'
        ];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        // Format query parameter jika GET
        if ($method === 'GET') {
            if (!empty($data)) {
                $query = http_build_query($data);
                $url .= (strpos($url, '?') !== false ? '&' : '?') . $query;
            }
        } else {
            $headers[] = 'Content-Type: application/json';
        }

        // Gunakan cURL jika ekstensinya aktif, atau fallback ke stream kontek bawaan PHP jika cURL belum aktif
        if (function_exists('curl_init')) {
            $res = self::requestWithCurl($method, $url, $headers, $data);
        } else {
            $res = self::requestWithStream($method, $url, $headers, $data);
        }

        // Auto-refresh token jika HTTP 401 dan sesi memiliki refreshToken
        if (!$isRetry && isset($res['httpCode']) && $res['httpCode'] === 401 
            && !empty($_SESSION['refreshToken']) 
            && $endpoint !== '/auth/login' 
            && $endpoint !== '/auth/refresh') {
            
            if (self::refreshAccessToken()) {
                return self::request($method, $endpoint, $data, $_SESSION['token'], true);
            }
        }

        return $res;
    }

    private static function refreshAccessToken() {
        if (empty($_SESSION['refreshToken'])) {
            return false;
        }

        $res = self::request('POST', '/auth/refresh', [
            'refreshToken' => $_SESSION['refreshToken']
        ], null, true);

        $newToken = $res['data']['accessToken'] ?? null;
        if (!empty($newToken)) {
            $_SESSION['token'] = $newToken;
            if (!empty($res['data']['refreshToken'])) {
                $_SESSION['refreshToken'] = $res['data']['refreshToken'];
            }
            return true;
        }

        return false;
    }

    private static function requestWithCurl($method, $url, $headers, $data) {
        $ch = curl_init();
        
        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'status'  => false,
                'message' => 'Gagal terhubung ke API: ' . $error
            ];
        }

        return self::parseResponse($response, $httpCode);
    }

    private static function requestWithStream($method, $url, $headers, $data) {
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

        if ($method !== 'GET' && !empty($data)) {
            $options['http']['content'] = json_encode($data);
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

        if ($response === false) {
            return [
                'success' => false,
                'status'  => false,
                'message' => 'Gagal terhubung ke server API Vercel.'
            ];
        }

        return self::parseResponse($response, $httpCode);
    }

    private static function parseResponse($response, $httpCode) {
        $decoded = json_decode($response, true);
        if ($decoded === null) {
            return [
                'success'  => false,
                'status'   => false,
                'httpCode' => $httpCode,
                'message'  => 'Format respons dari API tidak valid.',
                'raw'      => $response
            ];
        }

        $decoded['httpCode'] = $httpCode;
        return $decoded;
    }

    public static function get($endpoint, $params = [], $token = null) {
        return self::request('GET', $endpoint, $params, $token);
    }

    public static function post($endpoint, $data = [], $token = null) {
        return self::request('POST', $endpoint, $data, $token);
    }

    public static function patch($endpoint, $data = [], $token = null) {
        return self::request('PATCH', $endpoint, $data, $token);
    }

    public static function put($endpoint, $data = [], $token = null) {
        return self::request('PUT', $endpoint, $data, $token);
    }

    public static function delete($endpoint, $token = null) {
        return self::request('DELETE', $endpoint, [], $token);
    }
}
