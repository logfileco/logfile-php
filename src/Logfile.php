<?php declare(strict_types=1);

namespace Logfile;

use Throwable;

class Logfile
{
    protected $token;

    protected $sender;

    protected $config;

    protected $async = false;

    public function __construct(string $token, Config $config = null)
    {
        $this->token = $token;
        $this->sender = new Sender();
        $this->config = $config ?: new Config();
    }

    public function sendAsync(bool $async): void
    {
        $this->async = $async;
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    protected function getToken(): string
    {
        return $this->token;
    }

    /**
     * Capture exception and return the event ID
     *
     * @param Throwable $exception
     * @return string
     */
    public function captureException(Throwable $exception): string
    {
        $payload = Payload::createFromException($exception, $this->getConfig());

        return $this->log($payload);
    }

    /**
     * Log payload
     *
     * @param Payload $payload
     * @return string
     */
    public function log(Payload $payload): string
    {
        $this->sender->{$this->async ? 'sendAsync' : 'send'}($payload, $this->getToken());
        return $payload->getId();
    }
}
