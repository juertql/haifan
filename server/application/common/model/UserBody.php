<?php
namespace app\common\model;

use think\Model;

class UserBody extends Model
{
    // 指定表名,不含前缀
    protected $name = 'user_body';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
   /** 获取商品图集转数组 */
   /* public function getImgListAttr($value){
        return json_decode($value,true);
    }*/
    /** 保存 商品图片转json */
   /* protected  function setImgListAttr($value){
        $value = array_filter($value);
        return json_encode($value);
    }*/
}
