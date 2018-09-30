<?php declare(strict_types=1);

namespace Logfile;

class Sender
{
    public function send(Payload $payload, $accessToken)
    {
        $handle = curl_init();
        $this->setCurlOptions($handle, $payload, $accessToken);
        $result = curl_exec($handle);
        $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        
        $result = $result === false ?
                    curl_error($handle) :
                    json_decode($result, true);
        
        curl_close($handle);
        $data = $payload->data();
        $uuid = $data['data']['uuid'];
        
        return new Response($statusCode, $result, $uuid);
    }
}