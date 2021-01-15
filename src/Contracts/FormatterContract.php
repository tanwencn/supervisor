<?php

namespace Tanwencn\Supervisor\Contracts;

interface FormatterContract
{
    public function format($values):array;
}
