<?php

namespace Tanwencn\Supervisor\Mode;

class FilesystemDescMode extends FilesystemMode
{
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

    protected function matchExpres(&$content, $char)
    {
        $content = $char . $content;
        preg_match($this->config['regular'], $content, $result);
        if (!empty($result)) $result[] = $content;
        return $result;
    }

    protected function output($values)
    {
        unset($values[0]);
        return parent::output($values);
    }
}
