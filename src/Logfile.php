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
        $this->setId(static::uuid4());
        $payload = new Payload($exception, $this->getId(), $this->getTags(), $this->getRelease(), $this->getUser());
        $this->sender->send($payload, $this->getToken());
        return $this->getId();
    }

    /**
     * Get uuid v4
     *
     * @see http://www.php.net/manual/en/function.uniqid.php#94959
     * @return string
     */
    public static function uuid4(): string
    {
        mt_srand();
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
