<?php

namespace  Fearless\Tool\Support;

use BenSampo\Enum\FlaggedEnum;

abstract class BaseEnum extends FlaggedEnum
{
    /**
     * 获取枚举数组
     *
     * @return array
     */
    public static function getSelectArray(): array
    {
        $array = static::asArray();
        $selectArray = [];
        foreach ($array as $key => $value) {
            if ($key != 'None') {
                $selectArray[] = [
                    'value' => $value,
                    'description' => static::getDescription($value),
                ];
            }
        }
        return $selectArray;
    }

    /**
     * 获取枚举数组描述
     *
     * @param array $keys
     * @return array
     */
    public static function getDescriptions(array $keys): array
    {
        $descriptionArray = [];
        foreach ($keys as $key) {
            $descriptionArray[] = [
                'value' => $key,
                'description' => static::getDescription($key),
            ];
        }
        return $descriptionArray;
    }

    /**
     * 获取枚举数组值
     *
     * @return array
     */
    public static function getMigrationEnum(): array
    {
        $array = static::asArray();
        $enum = [];
        foreach ($array as $key => $value) {
            if ($key != 'None') {
                $enum[] = $value;
            }
        }
        return $enum;
    }

    /**
     * 获取数据库枚举注释
     *
     * @return string
     */
    public static function getMigrationComment(): string
    {
        $array = static::asArray();
        $comment = '';
        foreach ($array as $key => $value) {
            if ($key != 'None') {
                $comment .= $value.'-'.static::getDescription($value).';';
            }
        }
        return  substr($comment,0,strlen($comment)-1);
    }
}
