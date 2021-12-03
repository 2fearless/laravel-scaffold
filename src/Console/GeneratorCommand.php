<?php

namespace Fearless\Tool\Console;

use Illuminate\Console\GeneratorCommand as BaseCommand;
use Illuminate\Support\Str;

abstract class GeneratorCommand extends BaseCommand
{
    protected $baseDirectory;

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->getDefaultNamespace(null);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return guess_class_name($name);
    }

    /**
     * @return string
     */
    protected function getBaseDir()
    {
        if ($this->baseDirectory) {
            return trim(base_path($this->baseDirectory), '/');
        }

        if ($this->hasOption('base') && $this->option('base')) {
            return trim(base_path($this->option('base')), '/');
        }

        return $this->laravel['path'];
    }

    /**
     * @return void
     */
    protected function askBaseDirectory()
    {
        if (! Str::startsWith('App', 'App')) {
            $dir = explode('\\', 'App')[0];

            $this->baseDirectory = trim($this->ask('Please enter the application path',$dir));
        }
    }
}
