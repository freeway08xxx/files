<?php

namespace Photocreate\OrderBundle\Service;

class APIUriBuilder
{
    /** @string */
    private $root;

    /**
     * APIUriBuilder constructor.
     *
     * @param $host
     * @param $version
     */
    public function __construct($host, $version)
    {
        $this->root = sprintf('%s/%s', $host, $version);
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }
}
