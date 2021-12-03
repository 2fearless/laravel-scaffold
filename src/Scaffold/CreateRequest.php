<?php


namespace Fearless\Tool\Scaffold;

//request创建

class CreateRequest extends CommonScaffold
{
    public function create($model){
        $name = str_replace('App\\Models\\','',$model);
        $path = app_path('Http/Requests/'.$name.'Request.php');
        $stub = app('files')->get($this->getStub());
        $stub = $this
            ->replaceClassName($stub,$name)
            ->replaceRule($stub,$model)
            ->replaceScene($stub,$model)
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
            'DummyRequest',
            $name.'Request',
            $stub
        );
        return $this;
    }
    protected function build($request,$key){

        return "\n\t\t\t'".$key."' => '".$request['rule']."',";
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
    protected function replaceRule(&$stub, $model = '')
    {
        $fill = current_table_detail((new $model())->getTable());
        $str = '[';
        foreach ($fill as $k=>$v){
            if ($v['comment']){
                $comment = json_decode($v['comment'],true);
                if ($comment['request']){
                    $str .= $this->build($comment['request'],$k);
                }
            }
        }
        $useFillable = $model ?$str."\n\t\t];\n" : '';

        $stub = str_replace('DummyRules', $useFillable, $stub);

        return $this;
    }

    public function replaceScene(&$stub, $model = ''){
        $fill = current_table_detail((new $model())->getTable());
        $create = '[';
        $update = '[';
        foreach ($fill as $k=>$v){
            if ($v['comment']){
                $comment = json_decode($v['comment'],true);
                if ($comment['request']){
                    if ($comment['request']['create'] == 1){
                        $create .= "'".$k."',";
                    }
                    if ($comment['request']['update'] == 1){
                        $update .= "'".$k."',";
                    }
                }
            }
        }
        $create .= '],';
        $update .= '],';

        $stub = str_replace(['DummyCreate', 'DummyUpdate'], [$create, $update], $stub);

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
        return __DIR__.'/stubs/request.stub';
    }
}
