<?php


namespace Tanwencn\Supervisor\Contracts;

use League\Flysystem\Filesystem;


interface AnalysisContract
{
    public function __construct($path, $config);

    public function bootstrap(Filesystem $filesystem);

    public function next():array;
}
