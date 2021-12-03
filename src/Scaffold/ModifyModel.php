<?php


namespace Fearless\Tool\Scaffold;


use Illuminate\Support\Str;

//模型更新
class ModifyModel extends CommonScaffold
{
    public function create($model){
        $name = str_replace('App\\Models\\','',$model);
        $path = app_path('Models/'.$name.'.php');
        $stub = app('files')->get($this->getStub());
//        dd($stub,$path);
        $stub = $this->replaceNamespace($stub)
            ->replaceClassName($stub,$name)
            ->replaceTable($stub, $name)
            ->replaceFillAble($stub,$model)
            ->replaceCasts($stub,$model)
            ->replacePrimaryKey($stub, 'id')
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
    protected function replaceNamespace(&$stub)
    {
        $stub = str_replace(
            'DummyNamespace',
            'App\\Models',
            $stub
        );
        return $this;
    }
    /**
     * Replace primarykey dummy.
     *
     * @param string $stub
     * @param string $keyName
     *
     * @return $this
     */
    protected function replacePrimaryKey(&$stub, $keyName)
    {
        $modelKey = $keyName == 'id' ? '' : "protected \$primaryKey = '$keyName';\n";

        $stub = str_replace('DummyModelKey', $modelKey, $stub);

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
            'DummyClass',
            $name,
            $stub
        );
        return $this;
    }
    /**
     * Replace soft-deletes dummy.
     *
     * @param string $stub
     * @param bool   $softDeletes
     *
     * @return $this
     */
    protected function replaceSoftDeletes(&$stub, $softDeletes)
    {
        $import = $use = '';

        if ($softDeletes) {
            $import = 'use Illuminate\\Database\\Eloquent\\SoftDeletes;';
            $use = 'use SoftDeletes;';
        }

        $stub = str_replace(['DummyImportSoftDeletesTrait', 'DummyUseSoftDeletesTrait'], [$import, $use], $stub);

        return $this;
    }
    /**
     * Replace datetimeFormatter dummy.
     *
     * @param string $stub
     * @param bool   $softDeletes
     *
     * @return $this
     */
    protected function replaceDatetimeFormatter(&$stub)
    {
        $import = $use = '';

        if (version_compare(app()->version(), '7.0.0') >= 0) {
            $import = 'use App\\Models\\HasDateTimeFormatter;';
            $use = 'use HasDateTimeFormatter;';
        }

        $stub = str_replace(['DummyImportDateTimeFormatterTrait', 'DummyUseDateTimeFormatterTrait'], [$import, $use], $stub);

        return $this;
    }
    /**
     * Get namespace of giving class full name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace Table name dummy.
     *
     * @param string $stub
     * @param string $name
     *
     * @return $this
     */
    protected function replaceTable(&$stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        $table = "protected \$table = '".Str::plural(strtolower($class))."';\n";

        $stub = str_replace('DummyModelTable', $table, $stub);

        return $this;
    }
    /**
     * Replace timestamps dummy.
     *
     * @param string $stub
     * @param bool   $timestamps
     *
     * @return $this
     */
    protected function replaceTimestamp(&$stub, $timestamps)
    {
        $useTimestamps = $timestamps ? '' : "public \$timestamps = false;\n";

        $stub = str_replace('DummyTimestamp', $useTimestamps, $stub);

        return $this;
    }

    protected function fillExcept($key,$item){
        $except = ['created_at','updated_at','deleted_at'];
        if ($item['id']){
            return '';
        }
        if (in_array($key,$except)){
            return '';
        }
        return $key;
    }
    protected function castsExcept($key,$item){
        $except = ['created_at','updated_at','deleted_at'];
        if ($item['id']){
            return '';
        }
        if (in_array($key,$except)){
            return '';
        }
        return '"'.$key.'"=>"'. $item['usage'].'",';
    }

    /**
     * Replace fillable dummy.
     *
     * @param string $stub
     * @param string $model
     * @param array   $timestamps
     *
     * @return $this
     */
    protected function replaceFillAble(&$stub, $model = '')
    {
        $arr = [];
        $tb = current_table_detail((new $model())->getTable());
        $fill = '';
        foreach ($tb as $k=>$v){
            $add_fill = $this->fillExcept($k,$v);
            if ($add_fill){
//                $arr[] = $add_fill;
                $fill .= "\t\t'".$add_fill."',\n";
            }
        }


        $useFillable = $model ?"protected \$fillable = [\n".$fill."\t];\n" : '';

        $stub = str_replace('DummyFillAble', $useFillable, $stub);

        return $this;
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
    protected function replaceCasts(&$stub, $model = '')
    {
        $str = "[\n";
        $tb = current_table_detail((new $model())->getTable());
        foreach ($tb as $k=>$v){
            $add_fill = $this->castsExcept($k,$v);
            if ($add_fill){
                $str .= "\t\t".$add_fill."\n";
            }
        }

        $str .= "\t]";
        $useFillable = $model ?"protected \$casts = ".$str.";\n" : '';

        $stub = str_replace('DummyCasts', $useFillable, $stub);

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
     * Get stub path of model.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/stubs/model.stub';
    }
}
