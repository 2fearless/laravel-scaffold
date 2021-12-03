<?php


namespace Fearless\Tool\Scaffold;


use Illuminate\Support\Str;
//枚举创建
class CreateEnum extends CommonScaffold
{
    public function create($model){
        $name = str_replace('App\\Models\\','',$model);
        $dir = app_path('Enums');
        $this->dir_first($dir);
        $table_detail = current_table_detail((new $model())->getTable());
        $path = '';
        foreach ($table_detail as $k=>$v){
            if ($v['comment']){
                $comment = json_decode($v['comment'],true);
                if ($comment && isset($comment['enum']) && $comment['enum']){
                    $class_name = $name.Str::ucfirst($k);
                    $stub = app('files')->get($this->getStub());
                    $stub = $this
                        ->replaceClassName($stub,$class_name)
                        ->replaceConst($stub,$comment['enum'])
                        ->replaceDescribe($stub,$comment['enum'])
                        ->replaceSpace($stub);
                    $path = app_path('Enums/'.$class_name.'Enum.php');
                    app('files')->put($path, $stub);
                }
            }
        }
        return $path;
    }


    public function replaceConst(&$stub,$enum){
        $str = '';
        foreach ($enum as $v){
            $str .= "\tconst ".$v['key'].' = '.$v['value'].";\n";
        }
        $stub = str_replace('DummyConst', $str, $stub);

        return $this;

    }
    public function replaceDescribe(&$stub,$enum){
        $str = '';
        foreach ($enum as $v){
            $str .= "\t\t\tcase self::".$v['key'].":\n\t\t\t\treturn '".$v['comment']."';\n\t\t\t\tbreak;\n";
        }
        $stub = str_replace('DummyDescribe', $str, $stub);

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
    protected function replaceClassName(&$stub, string $name)
    {
        $stub = str_replace(
            'DummyEnum',
            $name.'Enum',
            $stub
        );
        return $this;
    }
    protected function build($key,$item){
        $except = ['created_at','updated_at','deleted_at'];
        if ($item['id']){
            return '';
        }
        if (in_array($key,$except)){
            return '';
        }
        return "\tpublic function ".$key."(\$".$key."){\n\t\treturn \$this->where('".$key."', \$".$key.");\n\t}\n\n";
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
        return __DIR__.'/stubs/enum.stub';
    }
}
