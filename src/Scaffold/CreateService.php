<?php


namespace Fearless\Tool\Scaffold;

//service创建
use Illuminate\Support\Str;

class CreateService extends CommonScaffold
{
    public function create($model){
        $name = str_replace('App\\Models\\','',$model);
        $path = app_path('Services/'.$name.'Service.php');
        $stub = app('files')->get($this->getStub());
        $stub = $this
            ->replaceCreate($stub,$model)
            ->replaceUpdate($stub,$model)
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

    protected function build($key){

        return "\t\t\t'".$key."' => \$params['".$key."'],\n";
    }

    public function replaceCreate(&$stub,string $model){
        $fill = current_table_detail((new $model())->getTable());
        $str = '';
        foreach ($fill as $k=>$v){
            if ($v['comment']){
                $str .= $this->build($k);
            }
        }
        $useFillable = $model ?$str : '';

        $stub = str_replace('DummyCreate', $useFillable, $stub);

        return $this;
    }
    public function replaceUpdate(&$stub,string $model){
        $fill = current_table_detail((new $model())->getTable());
        $str = '';
        foreach ($fill as $k=>$v){
            if ($v['comment']){
                $str .= $this->build($k);
            }
        }
        $useFillable = $model ?$str : '';

        $stub = str_replace('DummyUpdate', $useFillable, $stub);

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
        return __DIR__.'/stubs/service.stub';
    }
}
