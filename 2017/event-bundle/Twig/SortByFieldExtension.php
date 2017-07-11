<?php

namespace Photocreate\EventBundle\Twig;

use Photocreate\EventBundle\Exception\InvalidArgumentException;

/**
 * Class TransformerExtension
 *
 * @package Photocreate\MemberBundle\Twig
 */
class SortByFieldExtension extends \Twig_Extension
{
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('sort_by_from_date', [$this, 'sortByFromDate'])
        );
    }

    public function sortByFromDate($content, $direction = 'asc')
    {
        $fromDate = [];
        foreach ($content as $key => $row) {
            $fromDate[$key]  = $row['from_date'];
        }

        $sortOrder = SORT_ASC;
        if ($direction == 'desc') {
            $sortOrder = SORT_DESC;
        }

        array_multisort($fromDate, $sortOrder, $content);

        return $content;
    }
}