<?php

namespace Tanwencn\Supervisor\Console;

use Illuminate\Console\Command;

class TestBuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:testBuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'testBuild';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Build Start...');

        exec('cd ' . __DIR__ . '../../ && npm run prod');

        $this->comment('Publish...');

        $this->callSilent('vendor:publish', ['--tag' => 'supervisor-assets', '--force' => true]);

        $this->info('Build successfully.');
    }
}
