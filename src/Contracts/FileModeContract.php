<?php


namespace Tanwencn\Supervisor\Contracts;

use League\Flysystem\Filesystem;


interface FileModeContract
{
    public function __construct(string $path, array $config);

    public function bootstrap(Filesystem $filesystem);

    public function next():array;
}
