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

    abstract protected function matchExpres(&$content, $char);

    protected function match(&$content, $char)
    {
        $expres = $this->config['regular']['expres'];
        if ($expres != 'json') {
            if ($expres == 'default') {
                $this->config['regular']['expres'] = '/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/';
                if (empty($this->config['regular']['output']))
                    $this->config['regular']['output'] = ['date', 'env', 'level', 'code', 'more'];
            }
            return $this->matchExpres($content, $char);
        }

        $content = $char . $content;
        if (empty($content)) return [];
        if (strpos($content, '{') === false && strpos($content, '}') === false) return [];
        $data = json_decode($content, true, 512);
        if (json_last_error() !== JSON_ERROR_NONE) return [];
        foreach ($data as $key => $val) {
            $collect = collect($val);
            if ($collect->count() > 1) $val = $collect->toJson(JSON_UNESCAPED_UNICODE);
            $data[$key] = $val;
        }

        return $data;
    }

    public function __construct(string $path, array $config)
    {
        $this->path = $path;
        // $config['regular'] = array_merge([
        //     'expres' => '/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/',
        //     'output' => ['date', 'env', 'level', 'code', 'more']
        // ], $config['regular']);
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

        $output = data_get($this->config, 'regular.output', []);
        if (!empty($output)) {
            $header = array_slice($output, 0, count($values));
            $val = array_slice(array_values($values), 0, count($output));
            $values = array_combine($header, $val);
        }
        return array_merge(['supervisor-id' => $this->line], $values);
    }
}
