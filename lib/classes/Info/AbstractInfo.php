<?php
namespace ArrayIterator\Info;

/**
 * Class AbstractInfo
 * @package ArrayIterator\Info
 */
class AbstractInfo
{
    /**
     * @var array
     */
    public $info;

    /**
     * @var string
     */
    protected $path;

    /**
     * Module constructor.
     * @param string $path
     * @param array $info
     */
    public function __construct(
        string $path,
        array $info
    ) {
        $this->path = $path;
        $this->info = $info;
    }

    /**
     * @return mixed
     */
    public function isValid(): bool
    {
        return false;
    }

    public function getName() : string
    {
        return $this->info['name'] ?? '';
    }

    public function getUri(): string
    {
        return $this->info['uri'] ?? '';
    }

    public function getAuthor(): string
    {
        return $this->info['author'] ?? '';
    }

    public function getAuthorUri(): string
    {
        return $this->info['author_uri'] ?? '';
    }

    public function getVersion(): string
    {
        return $this->info['version'] ?? '';
    }

    public function getLicense(): string
    {
        return $this->info['license'] ?? '';
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function __tostring() : string
    {
        return $this->getName();
    }
}
