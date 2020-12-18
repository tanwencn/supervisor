<?php

namespace Tanwencn\Supervisor\Handler;

use League\Flysystem\Filesystem;

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

    public function next()
    {
        $content = '';
        $result = [];
        foreach ($this->read() as $char) {
            $result = $this->match($content, $char);
            if (!empty($result)) break;
        }

        return array_values($result);
    }

    abstract protected function read(): Iterator;

    abstract protected function match(&$content, $char);
}