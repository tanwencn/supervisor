<?php

namespace Tanwencn\Supervisor\MacthForamt;

use Tanwencn\Supervisor\Contracts\MatchFormatInterface;

class PositiveLaravelLog implements MatchFormatInterface
{
    use LaravelLog;

    public function match(&$content, $char, $mode): array
    {
        $content .= $char;
        $data = [];
        $match = preg_match_all($this->reg, $content, $result, PREG_OFFSET_CAPTURE);
        if($char === false){
            $this->offset = $mode->ftell();
            $data = array_column(array_column($result, 0), 0);
            unset($data[0]);
            $data[] = $content;
        }
        if ($match > 1) {
            $offset = $result[0][1][1];
            $this->offset += $offset;
            $mode->seek();
            $data = array_column(array_column($result, 0), 0);
            unset($data[0]);
            $data[] = substr($content, 0, $offset);
        }
        return $data;
    }
}
