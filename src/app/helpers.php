<?php

if (!function_exists('toZenkaku')) {
    function toZenkaku($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        return mb_convert_kana($value, 'N');
    }
}