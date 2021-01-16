<?php

namespace Tanwencn\Supervisor\Mode;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Tanwencn\Supervisor\Contracts\FileModeContract;
use Illuminate\Support\Str;

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

    protected $root;

    protected $filesystem;

    abstract protected function seek();

    abstract protected function read(): iterable;

    abstract protected function matchExpres(&$content, $char);

    protected function match(&$content, $char)
    {
        $expres = $this->config['regular'];
        if ($expres != 'json') {
            if ($expres == 'default') {
                $this->config['regular'] = '/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/';
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
        $root = data_get($config, 'root', '');
        // $root = Str::start(data_get($config, 'root', ''), '/');
        // if(Str::endsWith($root, '/')) $root = substr($root, 0, -1);
        $this->path = $root . Str::start($path, '/');
        $this->config = $config;
    }

    public function bootstrap()
    {
        $filesystem = $this->filesystem();
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

        // $output = data_get($this->config, 'regular.output', []);
        // if (!empty($output)) {
        //     $header = array_slice($output, 0, count($values));
        //     $val = array_slice(array_values($values), 0, count($output));
        //     $values = array_combine($header, $val);
        // }
        return array_merge(['supervisorid' => $this->line], $values);
    }

    /**
     *
     * @return object League\Flysystem\Filesystem
     */
    public function filesystem(): Filesystem
    {
        if (!$this->filesystem)
            $this->filesystem = Storage::disk($this->config['disk'])->getDriver();

        return $this->filesystem;
    }
}
