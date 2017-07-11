<?php

namespace Photocreate\EventBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class PhotoNumbersStringToHttpQueryStringTransformer
 *
 * @package Photocreate\MemberBundle\Form\DataTransformer
 */
class PhotoNumbersStringToHttpQueryStringTransformer implements DataTransformerInterface
{
    /**
     * @var string
     *
     * a: 「全角」英数字を「半角」に変換します。
     */
    const CONVERT_OPTION_ALPHA = 'a';

    /**
     * @param mixed $query
     *
     * @return string
     */
    public function transform($query): string
    {
        // TODO:  & -> 改行への置換でいい
        $photoNumbersArray  = explode("&", $query);
        $photoNumbersString = implode("\r\n", $photoNumbersArray);

        return $photoNumbersString;
    }

    /**
     * 改行区切りで GET パラメーターの形式に変換する
     *
     * input: 836-24217994\r\n836-24217995\r\n836-24217994
     * output:photo_numbers[]=836-24217994&photo_numbers[]=836-24217995&photo_numbers[]=836-24217994
     *
     * @param mixed $photoNumbersString
     *
     * @return string
     */
    public function reverseTransform($photoNumbersString): string
    {
        $photoNumbersString = mb_convert_kana($photoNumbersString, self::CONVERT_OPTION_ALPHA);
        $photoNumbersString = trim($photoNumbersString);
        $photoNumbersAsArray = explode("\r\n", $photoNumbersString);
        $photoNumbersAsArray = array_map('urlencode', $photoNumbersAsArray);

        $photoNumbers = [];
        foreach ($photoNumbersAsArray as $photoNumber) {
            $photoNumbers[] = 'photo_numbers[]='.$photoNumber;
        }

        return implode($photoNumbers, '&');
    }
}
