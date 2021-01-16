<?php

namespace Tanwencn\Supervisor;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
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

    protected $container;

    protected $name;

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
            $this->bootstrap($container);
        }
    }

    public function container($path)
    {
        $code = $this->name . base64_encode($path);
        if (isset($this->container[$code])) return $this->container[$code];

        return $this->container[$code] = $this->newContainer($path);
    }

    protected function bootstrap($container){
        if (method_exists($container, 'bootstrap')) $container->bootstrap();
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
        $this->bootstrap($container);

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
        $method = 'filesBy'. ucfirst($this->config['mode']);
        if(!method_exists($this, $method)) throw new \InvalidArgumentException("{$method} it's not found.");

        return call_user_func([$this, $method], $path);
    }

    protected function filesByFilesystem($path){
        $contents = Storage::disk($this->config['disk'])->getDriver()->listContents($path, false);
        return Collection::make($contents)
            ->filter(function ($item) {
                return $item['type'] == 'dir' || $item['extension'] == $this->config['extension'];
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

    protected function filesByDatabase(){
        $group = array_filter(data_get($this->config, 'group', []));
        if(empty($group)) $group = ['default'=>[]];

        foreach ($group as $key => $val) {
            $group[$key] = [
                'basename' => 'sdafas',
                'resolver' => $this->name,
                'isLeaf' => true,
                'code' => base64_encode($key),
                'description' => !empty($val)?'Display condition by '.json_encode($val).'.':''
            ];
        }

        return array_values($group);
    }
}
