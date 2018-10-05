<?php declare(strict_types=1);

namespace Logfile;

use Throwable;

class Payload
{
    use DataTrait;

    protected $exception;

    public function __construct(
        Throwable $exception,
        string $id,
        array $tags = [],
        string $release = '',
        array $user = []
    ) {
        $this->exception = $exception;
        $this->id = $id;
        $this->tags = $tags;
        $this->release = $release;
        $this->user = $user;
    }

    /**
     * Get payload data
     *
     * @return array
     */
    public function getData(): array
    {
        $trace = new Stacktrace($this->exception);
        $context = $trace->getFrames();

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

        return [
            'message' => $this->exception->getMessage(),

            'level' => $this->exception->getCode(),
            'level_name' => 'ERROR',

            'extra' => $extra,
            'context' => $context,
        ];
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
}
