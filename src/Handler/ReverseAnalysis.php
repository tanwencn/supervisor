<?php

namespace Tanwencn\Supervisor\Handler;

class ReverseAnalysis extends StreamAnalysis
{
    protected $offset = 0;

    protected function seek()
    {
        fseek($this->stream, $this->offset, SEEK_END);
    }

    protected function read(): Iterator
    {
        while (ftell($this->stream) > 1) {
            $this->offset-- && $this->seek();
            yield fgetc($this->stream);
        }
    }

    protected function match(&$content, $char)
    {
        $content = $char . $content;
        preg_match('/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/', $content, $result);
        if (!empty($result)) {
            unset($result[0]);
            $result[] = mb_convert_encoding($content, "UTF-8");
        }

        return $result;
    }
}