#laravel脚手架,根据已有模型的表字段生成验证器过滤器等
##使用方法

创建模型并生成迁移
```bash
php artisan create:model Article -m
```
安装扩展
```bash
composer require fearless/laravel-scaffold
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
将json写入迁移文件字段注释
```php
$table->string('title')->comment('{"comment":"标题","enum":0,"filter":{"type":"like"},"request":{"rule":"required","create":"1","update":"1"},"resource":"1"}');
$table->tinyInteger('status')->comment('{"comment":"状态","enum":[{"key":"OPEN","value":1,"comment":"开启"},{"key":"close","value":0,"comment":"关闭"}],"filter":{"type":"where"},"request":{"rule":"required","create":"1","update":"1"},"resource":"1"}');
```
运行迁移

###以下命令生成该表的各种层
```bash
php artisan build
```
输入模型名`Article`执行命令
