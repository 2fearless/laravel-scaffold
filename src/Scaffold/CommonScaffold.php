<?php


namespace Fearless\Tool\Scaffold;


class CommonScaffold
{
    public function __construct($aim_path)
    {
        //目录不存在新建目录
        $dir = app_path($aim_path);
        $this->dir_first($dir);
    }

    public function dir_first($dir){
        if (!app('files')->isDirectory($dir)){
            app('files')->makeDirectory($dir, 0777, true, true);
        }
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
}
