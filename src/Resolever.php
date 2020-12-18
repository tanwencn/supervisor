<?php

namespace Tanwencn\Supervisor;

use Illuminate\Support\Collection;
use Tanwencn\Supervisor\Contracts\AnalysisInterface;
use Tanwencn\Supervisor\Handler\PositiveAnalysis;
use Tanwencn\Supervisor\Handler\ReverseAnalysis;

class Resolever
{
    protected $config;

    protected $filesystem;

    protected $handler;

    public function __construct($config, $handler)
    {
        $this->config = $config;
        $handler = array_merge([
            'reverse' => ReverseAnalysis::class,
            'positive' => PositiveAnalysis::class,
        ], $handler);

        $this->handler = $handler[$config['resolver']];
    }

    protected function filesystem()
    {
        if (!$this->filesystem)
            $this->filesystem = \Storage::disk($this->config['disk'])->getDriver();

        return $this->filesystem;
    }

    public function handler()
    {
        if (is_string($this->handler)) {
            $class = $this->handler;
            $this->handler = new $class();
            $this->handler->bootstrap($this->filesystem);
        }
        if (!$this->handler instanceof AnalysisInterface)
            throw new \InvalidArgumentException("{$class} it's not an implementation of AnalysisInterface.");

        return $this->handler;
    }

    public function files($path = '/')
    {
        $contents = $this->filesystem->listContents($path, false);

        return Collection::make($contents)
            ->filter(function ($item) {
                return $item['type'] == 'dir' || $item['extension'] == 'log';
            })
            ->values()
            ->all();
    }

    public function results()
    {
    }
}