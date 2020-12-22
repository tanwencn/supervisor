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
        preg_match('/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/', $content, $result);
        if (!empty($result)) $result[] = $content;
        return $result;
    }

    public function format($values): array
    {
        unset($values[0]);
        return $this->formatAlias($values);
    }
}
