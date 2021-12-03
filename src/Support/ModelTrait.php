<?php

namespace  Fearless\Tool\Support;

trait ModelTrait
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    /**
     * 获取添加时间
     * @param $value
     * @return mixed
     */
    public function getCreatedAtAttribute($value)
    {
        return $value ? (string)$value : '';
    }

    /**
     * 获取更新时间
     * @param $value
     * @return mixed
     */
    public function getUpdatedAtAttribute($value)
    {
        return $value ? (string)$value : '';
    }
}
