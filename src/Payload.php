<?php declare(strict_types=1);

namespace Logfile;

use Throwable;

class Payload implements DataInterface
{
    use DataTrait;

    protected $message;

    protected $id;

    protected $context;

    public function __construct(string $message, string $id)
    {
        $this->message = $message;
        $this->id = $id;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function hasContext(): bool
    {
        return !empty($this->context);
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public static function createFromException(Throwable $exception, string $path = ''): self
    {
        $payload = new Payload($exception->getMessage(), static::uuid4());
        $trace = new Stacktrace($exception);
        $trace->setPath($path);
        $context = $trace->getFrames();
        $payload->setContext($context);
        return $payload;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get payload data
     *
     * @return array
     */
    public function getData(): array
    {
        $extra = [
            'id' => $this->getId(),
        ];

        if ($this->hasTags()) {
            $extra['tags'] = $this->getTags();
        }

        if ($this->hasUser()) {
            $extra['user'] = $this->getUser();
        }

        if ($this->hasRelease()) {
            $extra['release'] = $this->getRelease();
        }

        $data = [
            'message' => $this->message,
            'extra' => $extra,
        ];

        if ($this->hasContext()) {
            $data['context'] = $this->getContext();
        }

        return $data;
    }

    /**
     * Get payload json encoded
     *
     * @return string
     */
    public function getEncodedData(): string
    {
        $data = $this->getData();
        $encoded = json_encode($data);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $error = json_last_error_msg();
            throw new \LogicException(sprintf('Failed to encode json data: %s.', $error));
        }

        return $encoded;
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
