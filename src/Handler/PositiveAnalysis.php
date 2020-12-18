<?php

namespace Tanwencn\Supervisor\Handler;

class PositiveAnalysis extends StreamAnalysis
{
    protected function seek()
    {
        fseek($this->stream, $this->offset);
    }

    protected function read(): Iterator
    {
        while (!feof($this->stream)) {
            yield fgets($this->stream);
        }
    }

    protected function match(&$content, $char)
    {
        $content .= $char;
        $data = [];
        $match = preg_match_all('/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/', $content, $result, PREG_OFFSET_CAPTURE);
        if($char === false){
            $this->offset = ftell($this->stream);
            $data = array_column(array_column($result, 0), 0);
            unset($data[0]);
            $data[] = mb_convert_encoding($content, "UTF-8");
        }
        if ($match > 1) {
            $offset = $result[0][1][1];
            $this->offset += $offset;
            $this->seek();
            $data = array_column(array_column($result, 0), 0);
            unset($data[0]);
            $data[] = mb_convert_encoding(substr($content, 0, $offset), "UTF-8");
        }
        return $data;
    }
}