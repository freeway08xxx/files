<?php

namespace Photocreate\EventBundle\Service;

class APIUriBuilder
{
    /** @string */
    private $root;

    /** @string */
    private $host;

    /** @string */
    private $version;

    /**
     * APIUriBuilder constructor.
     *
     * @param string $host
     * @param string $version
     */
    public function __construct(string $host, string $version)
    {
        $this->root = sprintf('%s/%s', $host, $version);
        $this->host = $host;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
