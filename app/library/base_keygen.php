<?php

namespace app\library;

use ext\keygen;

class base_keygen extends keygen
{
    /**
     * Obscure Crypt Key
     *
     * @param string $key (32 bits)
     *
     * @return string (40 bits)
     */
    public static function obscure(string $key): string
    {
        $unit = str_split($key, 4);
        foreach ($unit as $k => $v) {
            $unit_key = substr($v, 0, 1);
            if (self::valid_kv($k, $unit_key)) {
                $v = strrev($v);
            }
            $unit[$k] = $v . $unit_key;
        }
        $str = implode($unit);

        $key = self::setObscureString($str);

        unset($unit, $k, $v, $unit_key, $str);
        return $key;
    }

    /**
     * Rebuild Crypt Key
     *
     * @param string $key (40 bits)
     *
     * @return string (32 bits)
     */
    public static function rebuild(string $key): string
    {
        $string = self::setRebuildString($key);
        $unit   = str_split($string, 5);
        foreach ($unit as $k => $v) {
            $unit_key  = substr($v, -1, 1);
            $unit_item = substr($v, 0, 4);
            $unit[$k]  = self::valid_kv($k, $unit_key) ? strrev($unit_item) : $unit_item;
        }
        $key = implode($unit);
        unset($unit, $k, $v, $unit_key, $unit_item, $str);
        return $key;
    }

    private static function valid_kv(int $k, string $v): bool
    {
        return 0 === ($k & ord($v));
    }

    private static function setObscureString(string $str): string
    {
        $unit    = str_split($str, 5);
        $unitArr = self::changeArray($unit);
        $str     = mt_rand(1000, 9999) . strrev(implode($unitArr)) . mt_rand(1000, 9999);
        return $str;
    }

    private static function changeArray(array $arr): array
    {
        $tmp                  = $arr[0];
        $arr[0]               = $arr[count($arr) - 1];
        $arr[count($arr) - 1] = $tmp;
        return $arr;
    }

    private static function setRebuildString(string $str): string
    {
        $str     = strrev(substr($str, 4, 40));
        $unit    = str_split($str, 5);
        $unitArr = self::changeArray($unit);
        return implode($unitArr);
    }
}