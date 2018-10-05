<?php declare(strict_types=1);

namespace Logfile;

class Sender
{
    public function send(Payload $payload, string $token): bool
    {
        $handle = curl_init();

        if (!is_resource($handle)) {
            throw new \ErrorException('Failed to start curl session');
        }

        $this->setCurlOptions($handle, $payload, $token);

        $result = curl_exec($handle);
        $info = curl_getinfo($handle);
        $error = curl_error($handle);

        curl_close($handle);

        return $result !== false;
    }

    protected function setCurlOptions($handle, Payload $payload, string $token): void
    {
        curl_setopt_array($handle, [
            CURLOPT_URL => sprintf('https://logfile.co/api/push/%s', $token),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload->getEncodedData(),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
    }
}
