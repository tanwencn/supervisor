<?php

namespace Tanwencn\Supervisor\MacthForamt;

use Tanwencn\Supervisor\Contracts\MacthForamtInterface;

class ReverseLaravelLog implements MacthForamtInterface
{
    use LaravelLog {
        format as formatAlias;
    }

    public function match(&$content, $char): array
    {
        $content = $char . $content;
        preg_match($this->reg, $content, $result);
        if (!empty($result)) $result[] = $content;
        return $result;
    }

    public function format($values): array
    {
        unset($values[0]);
        return $this->formatAlias($values);
    }
}
