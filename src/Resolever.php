<?php

namespace Tanwencn\Supervisor;

use Illuminate\Support\Collection;
use Tanwencn\Supervisor\Handler\PositiveAnalysis;
use Tanwencn\Supervisor\Handler\ReverseAnalysis;

class Resolever implements \Serializable
{
    protected $config;

    protected $filesystem;

    protected $handler;

    protected $container;

    protected $name;

    public function __construct($config, $handler, $name)
    {
        $this->config = $config;
        $handler = array_merge([
            'reverse' => ReverseAnalysis::class,
            'positive' => PositiveAnalysis::class,
        ], $handler);

        $this->handler = $handler[$config['resolver']];

        $this->name = $name;
    }

    public function serialize()
    {
        return serialize(get_object_vars($this));
    }

    public function unserialize($data)
    {
        $values = unserialize($data);
        foreach ($values as $key=>$value) {
            $this->$key = $value;
        }

        foreach ((array)$this->container as $container){
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
        $code = base64_encode($path);
        if (isset($this->container[$code])) return $this->container[$code];

        return $this->container[$code] = $this->newContainer($path);
    }

    protected function newContainer($path)
    {
        $class = $this->handler;

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
