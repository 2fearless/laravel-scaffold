<?php
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
if (!function_exists('current_table_detail')){
    /**
     *  获取表中字段详情
     * @param null $tb 表名
     * @return array
     */
    function current_table_detail($tb = null){
        $db = null;
        $databases = Arr::where(config('database.connections', []), function ($value) {
            $supports = ['mysql'];

            return in_array(strtolower(Arr::get($value, 'driver')), $supports);
        });
        $data = [];

        foreach ($databases as $connectName => $value) {
            if ($db && $db != $value['database']) {
                continue;
            }

            $sql = sprintf('SELECT * FROM information_schema.columns WHERE table_schema = "%s"', $value['database']);

            if ($tb) {
                $p = Arr::get($value, 'prefix');

                $sql .= " AND TABLE_NAME = '{$p}{$tb}'";
            }

            $tmp = DB::connection($connectName)->select($sql);

            $collection = collect($tmp)->map(function ($v) use ($value) {
                if (! $p = Arr::get($value, 'prefix')) {
                    return (array) $v;
                }
                $v = (array) $v;

                $v['TABLE_NAME'] = Str::replaceFirst($p, '', $v['TABLE_NAME']);

                return $v;
            });

            $data = $collection->groupBy('TABLE_NAME')->map(function ($v) {
                return collect($v)->keyBy('COLUMN_NAME')->map(function ($v) {
                    $v['COLUMN_TYPE'] = strtolower($v['COLUMN_TYPE']);
                    $v['DATA_TYPE'] = strtolower($v['DATA_TYPE']);

                    if (Str::contains($v['COLUMN_TYPE'], 'unsigned')) {
                        $v['DATA_TYPE'] .= '@unsigned';
                    }
                    $v['USAGE'] = $v['DATA_TYPE'];
                    if (Str::contains($v['DATA_TYPE'],'int')){
                        $v['USAGE'] = 'integer';
                    }
                    if (Str::contains($v['DATA_TYPE'],'char')){
                        $v['USAGE'] = 'string';
                    }
                    if (Str::contains($v['DATA_TYPE'],'text')){
                        $v['USAGE'] = 'string';
                    }
                    if ($v['DATA_TYPE'] == 'timestamp'){
                        $v['USAGE'] = 'date';
                    }
                    //生成默认注释
                    if (!json_decode($v['COLUMN_COMMENT'],true)){
                        $comment = [
                            "comment" => $v['COLUMN_COMMENT'],
                            "enum" => 0,
                            "resource" => "1",
                            "request" => 0,
                            'filter' => 0
                        ];
                        if ($v['IS_NULLABLE'] == 'NO'){
                            $comment['filter'] = ['type' => 'where'];
                            if ($v['USAGE'] == 'string'){
                                $comment['filter'] = ['type' => 'like'];
                            }
                            $comment['request'] = ["rule"=>"required","create"=>"1","update"=>"1"];
                        }
                        $v['COLUMN_COMMENT'] = json_encode($comment,JSON_UNESCAPED_UNICODE);
                    }



                    return [
                        'type'     => $v['DATA_TYPE'],
                        'default'  => $v['COLUMN_DEFAULT'],
                        'nullable' => $v['IS_NULLABLE'],
                        'key'      => $v['COLUMN_KEY'],
                        'id'       => $v['COLUMN_KEY'] === 'PRI',
                        'comment'  => $v['COLUMN_COMMENT'],
                        'usage'    => $v['USAGE']
                    ];
                })->toArray();
            })->toArray();
        }
        return $data[$tb];
    }
}
if (!function_exists('guess_class_name')){
    function guess_class_name($class){
        if (is_object($class)) {
            $class = get_class($class);
        }

        try {
            if (class_exists($class)) {
                return (new \ReflectionClass($class))->getFileName();
            }
        } catch (\Throwable $e) {
        }

        $class = trim($class, '\\');

        $composer = Fearless\Tool\Support\Composer::parse(base_path('composer.json'));

        $map = collect($composer->autoload['psr-4'] ?? [])->mapWithKeys(function ($path, $namespace) {
            $namespace = trim($namespace, '\\').'\\';

            return [$namespace => [$namespace, $path]];
        })->sortBy(function ($_, $namespace) {
            return strlen($namespace);
        }, SORT_REGULAR, true);

        $prefix = explode($class, '\\')[0];

        if ($map->isEmpty()) {
            if (Str::startsWith($class, 'App\\')) {
                $values = ['App\\', 'app/'];
            }
        } else {
            $values = $map->filter(function ($_, $k) use ($class) {
                return Str::startsWith($class, $k);
            })->first();
        }

        if (empty($values)) {
            $values = [$prefix.'\\', slug($prefix).'/'];
        }

        [$namespace, $path] = $values;

        return base_path(str_replace([$namespace, '\\'], [$path, '/'], $class)).'.php';
    }
}
if (!function_exists('slug')){
    /**
     * @param  string  $name
     * @param  string  $symbol
     * @return mixed
     */
    function slug(string $name, string $symbol = '-')
    {
        $text = preg_replace_callback('/([A-Z])/', function ($text) use ($symbol) {
            return $symbol.strtolower($text[1]);
        }, $name);

        return str_replace('_', $symbol, ltrim($text, $symbol));
    }
}

if (!function_exists('checkMobile')) {
    /**
     * checkMobile.
     *
     * @param  string  $mobile
     * @return string
     */
    function checkMobile(string $mobile)
    {
        return preg_match('/^1[3456789][0-9]{9}$/', $mobile);
    }
}

if (!function_exists('checkIdCard')){
    function checkIdCard($card){
        $city = [
            11 => "北京", 12 => "天津", 13 => "河北", 14 => "山西", 15 => "内蒙古", 21 => "辽宁", 22 => "吉林", 23 => "黑龙江 ",
            31 => "上海", 32 => "江苏", 33 => "浙江", 34 => "安徽", 35 => "福建", 36 => "江西", 37 => "山东", 41 => "河南", 42 => "湖北 ",
            43 => "湖南", 44 => "广东", 45 => "广西", 46 => "海南", 50 => "重庆", 51 => "四川", 52 => "贵州", 53 => "云南", 54 => "西藏 ",
            61 => "陕西", 62 => "甘肃", 63 => "青海", 64 => "宁夏", 65 => "新疆", 71 => "台湾", 81 => "香港", 82 => "澳门", 91 => "国外 "
        ];
        $tip = "";
        $match = "/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/";
        $pass = true;
        if (!$card || !preg_match($match, $card)) {
            //身份证格式错误
            $pass = false;
        } else {
            if (!$city[substr($card, 0, 2)]) {
                //地址错误
                $pass = false;
            } else {
                //18位身份证需要验证最后一位校验位
                if (strlen($card) == 18) {
                    $card = str_split($card);
                    //∑(ai×Wi)(mod 11)
                    //加权因子
                    $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                    //校验位
                    $parity = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
                    $sum = 0;
                    $ai = 0;
                    $wi = 0;
                    for ($i = 0; $i < 17; $i++) {
                        $ai = $card[$i];
                        $wi = $factor[$i];
                        $sum += $ai * $wi;
                    }
                    $last = $parity[$sum % 11];
                    if ($parity[$sum % 11] != $card[17]) {
                        //                        $tip = "校验位错误";
                        $pass = false;
                    }
                } else {
                    $pass = false;
                }
            }
        }
        if (!$pass) {
            return false;
        }/* 身份证格式错误*/
        return true;/* 身份证格式正确*/
    }
}

//------------------ array ------------------
if (!function_exists('array_tree_module')) {
    /**
     * 获取树形结构列表 适用layui树形组件
     * @param $data
     * @param $pid
     * @param int $deep
     * @return array
     */
    function array_tree_module($data, $pid = 0, $deep = 0)
    {
        $tree = [];
        foreach ($data as $row) {
            if ($row['pid'] == $pid) {
                $row['deep'] = $deep;
                if ($deep > 0) {
                    $row['spread'] = false;
                } else {
                    $row['spread'] = true;
                }
                $children = array_tree_module($data, $row['id'], $deep + 1);
                if ($children) {
                    $row['children'] = $children;
                }
                $tree[] = $row;
            }
        }
        return $tree;
    }
}

