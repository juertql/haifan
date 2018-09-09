<?php
namespace app\common\model;

use think\Db;
use think\Model;

class User extends Model
{
    // 指定表名,不含前缀
    protected $name = 'user';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    public function getOrderCount($user_id){
        if((int)$user_id < 0){
            return false;
        }
        $map['user_id'] = $user_id;
        $rest =  Db::name('goods')->where($map)->sum('rest_num');
        $data['rest'] = ($rest) ? $rest : 0;
        $use =  Db::name('goods')->where($map)->sum('use_num');
        $data['use'] =  $use ? $use : 0;
        $order =  Db::name('goods')->where($map)->sum('order_num');
        $data['order'] =  $order ? $order : 0;
        $data['total'] = (int) $data['use'] + (int)$data['rest'] + (int) $data['order'];
        return $data;
    }

}
