<?php declare(strict_types=1);

namespace Logfile;

class Sender
{
    protected $host = 'logfile.co';

    protected $scheme = 'https';

    protected $timeout;

    public function __construct(int $timeout = 2)
    {
        $this->timeout = $timeout;
    }

    public function setHost($host): void
    {
        $this->host = $host;
    }

    public function setScheme($scheme): void
    {
        $this->scheme = $scheme;
    }

    public function send(Payload $payload, string $token): bool
    {
        $handle = \curl_init();

        if (!is_resource($handle)) {
            throw new \ErrorException('Failed to start curl session');
        }

        $this->setCurlOptions($handle, $payload, $token);

        $result = \curl_exec($handle);

        \curl_close($handle);

        return $result !== false;
    }

    public function sendAsync(Payload $payload, string $token)
    {
        shell_exec(sprintf(
            'curl -H %s -m %u -d %s %s > /dev/null 2>&1 &',
            escapeshellarg('Content-Type: application/json'),
            $this->timeout,
            escapeshellarg($payload->getEncodedData()),
            escapeshellarg($this->getEndpoint($token))
        ));
    }

    protected function getEndpoint(string $token): string
    {
        return \sprintf('%s://%s/api/push/%s', $this->scheme, $this->host, $token);
    }

    protected function setCurlOptions($handle, Payload $payload, string $token): void
    {
        \curl_setopt_array($handle, [
            CURLOPT_URL => $this->getEndpoint($token),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload->getEncodedData(),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
        ]);
    }
}
