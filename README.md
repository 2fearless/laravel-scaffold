##使用方法
```json
修改 'composer.json' 文件
"require" : {
    "fearless/laravel-scaffold": "1.*"
}
 "repositories" :[
        {
            "type" : "git" ,
            "url" : "git@e.coding.net:qiujitech/scar/scar.git"
        }
    ],
composer update
```
###迁移文件字段注释生成
```bash
php artisan comment
```
+ 写入表迁移的字段根据提示选择输入
+ 获取结果复制到字段注释
```json 
{"comment":"标题",//注释内容
 "enum":0,//表示该字段不生成枚举
 "filter":{"type":"like"},//过滤器表示该字段使用where like查询,0表示字段不使用过滤器
 "request":{
 "rule":"required",//验证器验证规则
 "create":"1",//新增场景
 "update":"1"//更新场景
},"resource":"1"//是否写入资源}


{"enum":[
  {
    "key":"DAILY",//枚举常量名
    "value":0,//枚举常量值
    "comment":"日报"//枚举注释
  },
  {
    "key":"WEEKLY",
    "value":1,
    "comment":"周报"
  },
  {
    "key":"MONTHLY",
    "value":2,
    "comment":"月报"
  }]}
```
###生成该表的各种层, `type=all`代表生成全部
```bash
php artisan build {model} {type}
type = [
        model 写入模型基础配置($fillable,$casts)
        filter 创建过滤器
        collection 创建collection
        admin_con 创建后台控制器
        api_con 创建前台控制器
        enum 创建枚举
        request 创建验证器
        resource 创建资源
        service 创建服务
   ]
```
