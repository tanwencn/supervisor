<?php

namespace Tanwencn\Supervisor;

use Illuminate\Support\Collection;
use Tanwencn\Supervisor\Mode\FilesystemAscMode;
use Tanwencn\Supervisor\Mode\FilesystemDescMode;
use Tanwencn\Supervisor\Mode\FilesystemMode;
use Tanwencn\Supervisor\Mode\PositiveStreamMode;
use Tanwencn\Supervisor\Mode\ReverseStreamMode;
use Tanwencn\Supervisor\MacthForamt\PositiveLaravelLog;
use Tanwencn\Supervisor\MacthForamt\ReverseLaravelLog;

class Resolever implements \Serializable
{
    protected $config;

    protected $filesystem;
    protected $model;

    protected $container;

    protected $name;

    static $defaultAdapter = [
        PositiveStreamMode::class => PositiveLaravelLog::class,
        ReverseStreamMode::class => ReverseLaravelLog::class
    ];

    public function __construct($config, $modes, $name)
    {
        $this->config = $config;

        $this->mode = $modes[$config['mode']];

        $this->name = $name;
    }

    public function serialize()
    {
        return serialize(get_object_vars($this));
    }

    public function unserialize($data)
    {
        $values = unserialize($data);
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }

        foreach ((array)$this->container as $container) {
            $container->bootstrap($this->filesystem());
        }
    }

    /**
     *
     * @return object League\Flysystem\Filesystem
     */
    protected function filesystem()
    {
        if (!$this->filesystem)
            $this->filesystem = \Storage::disk($this->config['disk'])->getDriver();

        return $this->filesystem;
    }

    public function container($path)
    {
        $code = $this->name . base64_encode($path);
        if (isset($this->container[$code])) return $this->container[$code];

        return $this->container[$code] = $this->newContainer($path);
    }

    protected function newContainer($path)
    {
        $class = $this->mode;

        if($class == FilesystemMode::class){
            $class = strtolower(data_get($this->config, 'order', ''))=='desc'?FilesystemDescMode::class:FilesystemAscMode::class;
        }

        if (!class_exists($class))
            throw new \InvalidArgumentException("{$class} it's not found.");

        $container = new $class($path, $this->config);
        $container->bootstrap($this->filesystem());

        return $container;
    }

    /**
     * Files By Path
     *
     * @param string $path
     * @return array
     */
    public function files($path = '/')
    {
        $contents = $this->filesystem()->listContents($path, false);

        return Collection::make($contents)
            ->filter(function ($item) {
                return $item['type'] == 'dir' || $item['extension'] == 'log';
            })
            ->sortByDesc(function ($value) {
                return ($value['type'] == 'dir' ? 1 : 0) . $value['timestamp'];
            })
            ->map(function ($values) {
                return array_merge([
                    'resolver' => $this->name,
                    'isLeaf' => $values['type'] == 'file',
                    'code' => base64_encode($values['path'])
                ], $values);
            })
            ->values()
            ->all();
    }
}
