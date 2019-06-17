<?php declare(strict_types=1);

namespace Logfile;

use Throwable;

class Payload
{
    protected $message;

    protected $config;

    protected $id;

    protected $context;

    protected $extra = [];

    public function __construct(string $message, Config $config)
    {
        $this->message = $message;
        $this->config = $config;
        $this->id = $this->uuid4();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setExtra(string $key, $value): void
    {
        $this->extra[$key] = $value;
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

    public static function createFromException(Throwable $exception, Config $config): self
    {
        $payload = new Payload($exception->getMessage(), $config);
        $payload->setExtra('exception', get_class($exception));
        $trace = new Stacktrace($exception);
        $trace->setPath($config->getPath());
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

    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * Get payload data
     *
     * @return array
     */
    public function getData(): array
    {
        $extra = array_merge($this->extra, [
            'id' => $this->getId(),
        ]);

        if ($this->config->hasTags()) {
            $extra['tags'] = $this->config->getTags();
        }

        if ($this->config->hasUser()) {
            $extra['user'] = $this->config->getUser();
        }

        if ($this->config->hasRelease()) {
            $extra['release'] = $this->config->getRelease();
        }

        $data = [
            'message' => $this->getMessage(),
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
        $encoded = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if (JSON_ERROR_UTF8 === json_last_error()) {
            if (is_string($data)) {
                $this->detectAndCleanUtf8($data);
            } elseif (is_array($data)) {
                array_walk_recursive($data, array($this, 'detectAndCleanUtf8'));
            }
            $encoded = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            $error = json_last_error_msg();
            throw new \LogicException(sprintf('Failed to encode json data: %s.', $error));
        }

        return $encoded;
    }

    /**
     * Detect invalid UTF-8 string characters and convert to valid UTF-8.
     * @see https://github.com/Seldaek/monolog/blob/master/src/Monolog/Formatter/NormalizerFormatter.php
     */
    public function detectAndCleanUtf8(&$data)
    {
        if (is_string($data) && !preg_match('//u', $data)) {
            $data = preg_replace_callback(
                '/[\x80-\xFF]+/',
                function ($m) {
                    return utf8_encode($m[0]);
                },
                $data
            );
            $data = str_replace(
                array('¤', '¦', '¨', '´', '¸', '¼', '½', '¾'),
                array('€', 'Š', 'š', 'Ž', 'ž', 'Œ', 'œ', 'Ÿ'),
                $data
            );
        }
    }

    /**
     * Get uuid v4
     *
     * @see http://www.php.net/manual/en/function.uniqid.php#94959
     * @return string
     */
    protected function uuid4(): string
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
