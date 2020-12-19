<?php

namespace Tanwencn\Supervisor\Handler;

use League\Flysystem\Filesystem;
use Tanwencn\Supervisor\Contracts\AnalysisInterface;

abstract class StreamAnalysis implements AnalysisInterface
{
    /**
     * var resource
     */
    protected $stream;

    protected $path;

    protected $config;

    protected $line = 0;

    public function __construct($path, $config)
    {
        $this->path = $path;
        $this->config = $config;
    }

    public function bootstrap(Filesystem $filesystem)
    {
        if (!$this->stream) {
            $this->stream = $filesystem->readStream($this->path);
            $this->seek();
        }
    }

    abstract protected function seek();

    public function next(): array
    {
        $content = '';
        $result = [];
        foreach ($this->read() as $char) {
            $result = $this->match($content, $char);
            if (!empty($result)) break;
        }

        $this->line++;
        return $this->formatRow($result);
    }

    abstract protected function read(): iterable;

    abstract protected function match(&$content, $char);

    protected function formatRow($values)
    {
        if(empty($values)) return $values;
        $values = array_map(function ($val) {
            return mb_convert_encoding($val, "UTF-8");
        }, $values);
        array_unshift($values, $this->line);
        $values = array_combine(array_slice(['id', 'date', 'env', 'level', 'code', 'fullText'], 0, count($values)), array_values($values));
        return $values;
    }
}
