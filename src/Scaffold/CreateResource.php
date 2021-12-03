<?php


namespace Fearless\Tool\Scaffold;

//resource创建
use Illuminate\Support\Str;

class CreateResource extends CommonScaffold
{
    public function create($model){
        $name = str_replace('App\\Models\\','',$model);
        $path = app_path('Http/Resources/'.$name.'Resource.php');
        $stub = app('files')->get($this->getStub());
        $stub = $this
            ->replaceClassName($stub,$name)
            ->replaceToArray($stub,$model,$name)
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
            'DummyResource',
            $name.'Resource',
            $stub
        );
        return $this;
    }
    protected function build($comment,$key,$name){
        $res = "\n\t\t\t'".$key."' => \$this->".$key.",";
        if ($comment['enum']){
            $res .= "\n\t\t\t'".$key."_text' => \App\\Enums\\".$name.Str::ucfirst($key)."Enum::getDescription(\$this->".$key."),";
        }
        return $res;
    }

    /**
     * Replace casts dummy.
     *
     * @param string $stub
     * @param string $model
     * @param array   $timestamps
     *
     * @return $this
     */
    protected function replaceToArray(&$stub, $model = '',$name)
    {
        $str = '[';
        $fill = current_table_detail((new $model())->getTable());

        foreach ($fill as $k=>$v){
            if ($v['comment']){
                $comment = json_decode($v['comment'],true);
                if ($comment['resource']){
                    $str .= $this->build($comment,$k,$name);
                }
            }
        }



        $useFillable = $model ?$str."\n\t\t];\n" : '';

        $stub = str_replace('DummyColumns', $useFillable, $stub);

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
        return __DIR__.'/stubs/resource.stub';
    }
}
