<?php


namespace Fearless\Tool\Scaffold;

//collection创建

class CreateCollection extends CommonScaffold
{
    public function create($model){
        $name = str_replace('App\\Models\\','',$model);
        $path = app_path('Http/Collections/'.$name.'Collection.php');
        $stub = app('files')->get($this->getStub());
        $stub = $this
            ->replaceClassName($stub,$name)
            ->replaceResource($stub,$name)
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
    protected function replaceClassName(&$stub, string $name)
    {
        $stub = str_replace(
            'DummyCollection',
            $name.'Collection',
            $stub
        );
        return $this;
    }
    /**
     * Replace namespace dummy.
     *
     * @param string $stub
     * @param string $name
     *
     * @return $this
     */
    protected function replaceResource(&$stub, string $name)
    {
        $import = 'use App\Http\Resources\\'.$name.'Resource;';
        $use = $name.'Resource';


        $stub = str_replace(['DummyImportResource', 'DummyResource'], [$import, $use], $stub);

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
        return __DIR__.'/stubs/collection.stub';
    }
}
