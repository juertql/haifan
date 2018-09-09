<?php
namespace app\common\model;

use think\Model;

class GoodsType extends Model
{
    // 指定表名,不含前缀
    protected $name = 'goods_type';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    //文本域过滤
    protected function setContentAttr($value){
        return htmlspecialchars_decode($value);
    }
}
