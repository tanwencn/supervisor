<?php

namespace Tanwencn\Supervisor\Handler;

class ReverseAnalysis extends StreamAnalysis
{
    protected $offset = 0;

    protected function seek()
    {
        fseek($this->stream, $this->offset, SEEK_END);
    }

    protected function read(): iterable
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
        return $result;
    }

    protected function formatRow($values)
    {
        unset($values[0]);
        return parent::formatRow($values);
    }
}
