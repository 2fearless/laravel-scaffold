<?php


namespace Fearless\Tool\Scaffold;

//filter创建
use Illuminate\Support\Str;

class CreateFilter extends CommonScaffold
{
    public function create($model){
        $name = str_replace('App\\Models\\','',$model);
        $dir = app_path('ModelFilters');
        $this->dir_first($dir);
        $path = app_path('Filters/'.$name.'Filter.php');
        $stub = app('files')->get($this->getStub());
        $stub = $this
            ->replaceClassName($stub,$name)
            ->replaceFunction($stub,$model)
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
            'DummyFilter',
            $name.'Filter',
            $stub
        );
        return $this;
    }
    protected function build($filter,$key){
        switch ($filter['type']){
            case 'between':
                return "\tpublic function ".Str::camel($key)."(\$".$key."){\n\t\treturn \$this->whereBetween('".$key."', \$".$key.");\n\t}\n\n";
                break;
            case 'like':
                return "\tpublic function ".Str::camel($key)."(\$".$key."){\n\t\treturn \$this->where('".$key."','like', '%'.\$".$key.".'%');\n\t}\n\n";
                break;
            case 'where':
            default:
            return "\tpublic function ".Str::camel($key)."(\$".$key."){\n\t\treturn \$this->where('".$key."', \$".$key.");\n\t}\n\n";
        }

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
    protected function replaceFunction(&$stub, $model = '')
    {
        $str = '';
        $fill = current_table_detail((new $model())->getTable());

        foreach ($fill as $k=>$v){
            if ($v['comment']){
                $comment = json_decode($v['comment'],true);
                if ($comment['filter']){
                    $str .= $this->build($comment['filter'],$k);
                }
            }
        }



        $useFillable = $model ?$str."\n" : '';

        $stub = str_replace('DummyFunction', $useFillable, $stub);

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
        return __DIR__.'/stubs/filter.stub';
    }
}
