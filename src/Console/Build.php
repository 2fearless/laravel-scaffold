<?php

namespace Fearless\Tool\Console;

use Fearless\Tool\Scaffold\CreateAdminController;
use Fearless\Tool\Scaffold\CreateApiController;
use Fearless\Tool\Scaffold\CreateCollection;
use Fearless\Tool\Scaffold\CreateEnum;
use Fearless\Tool\Scaffold\CreateFilter;
use Fearless\Tool\Scaffold\CreateRequest;
use Fearless\Tool\Scaffold\CreateResource;
use Fearless\Tool\Scaffold\CreateService;
use Fearless\Tool\Scaffold\ModifyModel;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Build extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build {model} {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '根据模板创建文件';

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
        $model = 'App\\Models\\'.Str::ucfirst($this->argument('model'));
        $type = Str::lower($this->argument('type'));
        if ($type == 'all'){
            $this->model($model);
            $this->filter($model);
            $this->collection($model);
            $this->admin_con($model);
            $this->api_con($model);
            $this->enum($model);
            $this->request($model);
            $this->resource($model);
            $this->service($model);
        }else{
            $this->$type($model);
        }
        return Command::SUCCESS;
    }

    //构建模型
    public function model($model){
        $this->info((new ModifyModel('Models'))->create($model));
    }

    //构建过滤器
    public function filter($model){
        $this->info((new CreateFilter('Filters'))->create($model));
    }

    //构建集合
    public function collection($model){
        $this->info((new CreateCollection('Http\\Collections'))->create($model));
    }

    //构建后台控制器
    public function admin_con($model){
        $this->info((new CreateAdminController('Http\\Controllers\\Admin'))->create($model));
    }

    //构建前台控制器
    public function api_con($model){
        $this->info((new CreateApiController('Http\\Controllers\\Api'))->create($model));
    }

    //构建枚举
    public function enum($model){
        $this->info((new CreateEnum('Enums'))->create($model));
    }

    //构建验证器
    public function request($model){
        $this->info((new CreateRequest('Http\\Requests'))->create($model));
    }

    //构建资源
    public function resource($model){
        $this->info((new CreateResource('Http\\Resources'))->create($model));
    }

    //构建服务层
    public function service($model){
        $this->info((new CreateService('Services'))->create($model));
    }
}
