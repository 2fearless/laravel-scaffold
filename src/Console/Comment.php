<?php

namespace Fearless\Tool\Console;

use Illuminate\Console\Command;

class Comment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '交互式生成格式化字段注释';

    public $arr = [];

    public $setting = "\n【0】不启用\n【1】启用\n";
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->askComment();
        $this->askEnum();
        $this->askFilter();
        $this->askRequest();
        $this->askResource();
        $this->line(json_encode($this->arr,JSON_UNESCAPED_UNICODE));
        return Command::SUCCESS;
    }
    //字段注释
    public function askComment(){
        $this->arr['comment'] = $this->ask('请输入字段备注');
    }
    //enum详情
    public function askEnum(){
        $enum = $this->ask('请输入枚举数量');
        if ($enum == 0){
            $this->arr['enum'] = 0;
        }else{
            $enums = [];
            for($i = 1;$i<$enum+1;$i++){
                $temp['key'] = $this->ask('枚举常量名'.$i);
                $temp['value'] = intval($this->ask('枚举常量值'.$i));
                $temp['comment'] = $this->ask('枚举注释'.$i);
                $enums[] = $temp;
            }
            $this->arr['enum'] = $enums;
        }
    }
    //过滤器详情
    public function askFilter(){
        $filter = $this->ask('请输入是否启用过滤器'.$this->setting,1);
        $this->arr['filter'] = 0;
        $choice = [
            'where',
            'like',
            'between'
        ];
        if ($filter){
            $type = $this->choice('请选择过滤器类型',$choice,0);
            $temp['type'] = $type;
            $this->arr['filter'] = $temp;
        }
    }
    //验证器详情
    public function askRequest(){
        $request = $this->ask('请选择是否启用验证器'.$this->setting,0);
        $this->arr['request'] = 0;
        if ($request){
            $rule = $this->ask('请输入验证规则','required');
            $temp['rule'] = $rule;
            $create = $this->ask('请选择创建场景是否启用'.$this->setting,1);
            $update = $this->ask('请选择更新场景是否启用'.$this->setting,1);
            $temp['create'] = $create;
            $temp['update'] = $update;
            $this->arr['request'] = $temp;
        }
    }
    //是否资源显示
    public function askResource(){
        $this->arr['resource'] = $this->ask("请选择是否启用资源".$this->setting,1);
    }
}
