<?php

namespace Photocreate\EventBundle\Entity;

use PHPMentors\DomainKata\Entity\EntityInterface;

/**
 * Class PhotoNumbersQuery
 *
 * 写真番号検索クエリ
 *
 * @package Photocreate\EventBundle\Entity
 */
class PhotoNumbersQuery implements EntityInterface
{
    /**
     * @var string
     */
    private $query;

    /**
     * Set query
     *
     * @param integer $query
     * @return PhotoNumbersQuery
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}
