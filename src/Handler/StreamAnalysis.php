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

        return $this->formatRow($result);
    }

    abstract protected function read(): iterable;

    abstract protected function match(&$content, $char);

    protected function formatRow($values)
    {
        $values = array_map(function ($val) {
            return mb_convert_encoding($val, "UTF-8");
        }, $values);
        return array_combine(array_slice(['date', 'env', 'level', 'description', 'fullText'], 0, count($values)), array_values($values));
    }
}
