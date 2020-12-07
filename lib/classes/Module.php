<?php

namespace ArrayIterator;

/**
 * Class Module
 * @package ArrayIterator
 */
class Module
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
     * @var bool
     */
    protected $valid;

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
        $this->valid = $this->info['name'] ?? '';
        $this->valid = !empty($this->valid) && is_string($this->valid);
    }

    /**
     * @return mixed
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getName()
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
     * @return bool
     */
    public function isSiteWide(): bool
    {
        return (bool)($this->info['site_wide'] ?? false);
    }
}
