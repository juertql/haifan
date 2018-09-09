<?php
namespace app\common\model;

use think\Model;

class WebBaseSite extends Model
{
    // 指定表名,不含前缀
    protected $name = 'web_base_site';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
}
