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
            $values = [$prefix.'\\', self::slug($prefix).'/'];
        }

        [$namespace, $path] = $values;

        return base_path(str_replace([$namespace, '\\'], [$path, '/'], $class)).'.php';
    }
}
