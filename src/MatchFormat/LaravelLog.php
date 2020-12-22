<?php

namespace Tanwencn\Supervisor\MacthForamt;

trait LaravelLog
{
    protected $reg = '/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/';

    public function format($values):array
    {
        if(empty($values)) return [];
        $values = array_map(function ($val) {
            return mb_convert_encoding($val, "UTF-8");
        }, $values);
        array_unshift($values, $this->line);
        $values = array_combine(array_slice(['id', 'date', 'env', 'level', 'code', 'fullText'], 0, count($values)), array_values($values));
        return $values;
    }
}
