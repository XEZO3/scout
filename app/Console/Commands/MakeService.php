<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    protected $files;
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->makeService($name);
        $this->info("Service {$name} created successfully.");
    }

    protected function makeService($name)
    {
         $path = $this->getPath($name);

        if ($this->files->exists($path)) {
            $this->error("Service {$name} already exists!");
            return;
        }

        $namespace = $this->getNamespace($name);
        $className = class_basename($name);
        $stub = $this->getStub();
        $stub = str_replace('{{namespace}}', $namespace, $stub);
        $stub = str_replace('{{class}}', $className, $stub);

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return app_path('Services/' . Str::replace('\\', '/', $name) . '.php');
    }

    /**
     * Get the namespace for the class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        $string = explode("/",$name);
        $count = count($string)-1;
        unset($string[$count]);
        $namespace = implode("\\",$string);
        return "App\\Services\\".$namespace;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return <<<'STUB'
        <?php
        
        namespace {{namespace}};
        
        class {{class}}
        {
            //
        }
        STUB;
    }
}
