<?php

class shopKmgtmPluginHTTPTransport
{
    public static function load($url, $method, $params)
    {
        if (!extension_loaded('curl')) {
            if (ini_get('allow_url_fopen')) {
                $default_socket_timeout = @ini_set('default_socket_timeout', 10);
                if ($method == 'POST') {
                    $opts = array(
                        'http' =>
                            array(
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $params
                            )
                    );
                    $context = stream_context_create($opts);
                    $result = file_get_contents($url, false, $context); // POST
                } else {
                    $result = file_get_contents($url . '?' . http_build_query($params)); // GET
                }

                @ini_set('default_socket_timeout', $default_socket_timeout);
            } else {
                throw new waException('Curl extension not loaded');
            }
        } else {
            if (!function_exists('curl_init') || !($ch = curl_init())) {
                throw new waException("Can't init curl");
            }

            if (curl_errno($ch) != 0) {
                $error = "Can't init curl";
                $error .= ": " . curl_errno($ch) . " - " . curl_error($ch);
                throw new waException($error);
            }
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            @curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            @curl_setopt($ch, CURLOPT_HEADER, 0);
            if ($method == 'POST') {
                @curl_setopt($ch, CURLOPT_POST, 1);
                @curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                @curl_setopt($ch, CURLOPT_URL, $url);
            } else {
                @curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
            }

            $result = @curl_exec($ch);

            curl_close($ch);
        }

        return $result;
    }
}