<?php


namespace Fearless\Tool\Scaffold;

//admin_controller创建
use Illuminate\Support\Str;

class CreateAdminController extends CommonScaffold
{
    public function create($model){
        $name = str_replace('App\\Models\\','',$model);
        $path = app_path('Http/Controllers/Admin/'.$name.'Controller.php');
        $stub = app('files')->get($this->getStub());
        $stub = $this
            ->replaceName($stub,$name)
            ->replaceSpace($stub);
        app('files')->put($path, $stub);
        return $path;
    }
    /**
     * Replace namespace dummy.
     *
     * @param string $stub
     * @param string $name
     *
     * @return $this
     */
    protected function replaceName(&$stub, string $name)
    {
        $stub = str_replace(['Dummy', 'dummy'], [$name, Str::snake($name)], $stub);
        return $this;
    }

    /**
     * Replace spaces.
     *
     * @param string $stub
     *
     * @return mixed
     */
    public function replaceSpace($stub)
    {
        return str_replace(["\n\n\n", "\n    \n"], ["\n\n", ''], $stub);
    }
    /**
     * Get stub path of filter.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/stubs/admin_controller.stub';
    }
}
