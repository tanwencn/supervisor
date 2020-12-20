<?php

namespace Tanwencn\Supervisor\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Supervisor resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Supervisor Assets...');
        $this->callSilent('vendor:publish', ['--tag' => 'supervisor-assets']);

        $this->comment('Publishing Supervisor Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'supervisor-config']);

        $this->info('Supervisor scaffolding installed successfully.');
    }
}
