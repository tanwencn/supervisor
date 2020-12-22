<?php


namespace Tanwencn\Supervisor\Contracts;


interface MatchFormatInterface
{
    public function match(&$content, $char, FileMode $mode):array;

    public function format($values):array;
}
