<?php declare(strict_types=1);

namespace Logfile;

use Throwable;

class Logfile
{
    use DataTrait;

    protected $token;

    protected $sender;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->sender = new Sender();
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
        $payload = Payload::createFromException($exception);
        $payload->setTags($this->getTags());
        $payload->setUser($this->getUser());
        $payload->setRelease($this->getRelease());
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
        $this->sender->send($payload, $this->getToken());
        return $payload->getId();
    }
}
