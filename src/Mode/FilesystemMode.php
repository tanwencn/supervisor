<?php

namespace Tanwencn\Supervisor\Mode;

use League\Flysystem\Filesystem;
use Tanwencn\Supervisor\Contracts\FileModeContract;

abstract class FilesystemMode implements FileModeContract
{
    /**
     * var resource
     */
    protected $stream;

    protected $path;

    protected $config;

    protected $line = 0;

    protected $offset = 0;

    abstract protected function seek();

    abstract protected function read(): iterable;

    abstract protected function match(&$content, $char);

    public function __construct(string $path, array $config)
    {
        $this->path = $path;
        $config['regular'] = array_merge([
            'expres' => '/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/',
            'output' => ['date', 'env', 'level', 'code', 'more']
        ], $config);
        $this->config = $config;
    }

    public function bootstrap(Filesystem $filesystem)
    {
        if (!$this->stream) {
            $this->stream = $filesystem->readStream($this->path);
            $this->seek();
        }
    }

    public function next(): array
    {
        $content = '';
        $result = [];
        foreach ($this->read() as $char) {
            $result = array_filter($this->match($content, $char));
            if (!empty($result)) break;
        }

        $this->line++;

        return $this->output($result);
    }

    protected function output($values)
    {
        if (empty($values)) return $values;
        $values = array_map(function ($val) {
            return mb_convert_encoding($val, "UTF-8");
        }, $values);

        $output = $this->config['regular']['output'];
        if (empty($output)) return $values;
        $header = array_slice($output, 0, count($values));
        $val = array_slice(array_values($values), 0, count($output));
        $values = array_combine($header, $val);

        return array_merge(['id' => $this->line], $values);
    }
}
