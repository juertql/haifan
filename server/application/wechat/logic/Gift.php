<?php
/**
 *礼品数据层
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/21/021
 * Time: 10:00
 */

namespace app\wechat\logic;


use think\Db;
use think\Exception;
use think\Model;

class Gift extends Model
{
    /**
     * 新增
     * @param string $field
     * @return false
     */
    public function addGift($map){

       Db::startTrans();
        try{
            /** 查询临时表 */
            $obj = Db::name('gift_queue')->where($map)->limit(1)->find();
           if(!$obj){
               $this->error = '请求错误1'; return false;
           }
            /** 获取库存 */
            $gift_id = $obj['gift_id'];
            $total_num = $this->where('id',$gift_id)->value('total_num');
            if( (int) $total_num < 1) {
                $this->error = '请求错误2'; return false;
            }
            /** 减少库存 */
            $gift_update['total_num'] = ['exp','total_num - 1'];
            $res = $this->where('id',$gift_id)->update($gift_update);
            /** 获奖记录 */
            $map['create_time'] = time();
            $result = Db::name('gift_log')->insert($map);
            /** 删除临时表 */
            Db::name('gift_queue')->delete($obj['id']);
            Db::commit();
        }
        catch(Exception $e){
            Db::rollback();
            $this->error = $e->getMessage(); return false;
        }

           return true;
    }
    /**
     * 获取礼品列表
     */
    public function getList($field = 'id,name,chance,type,num'){
       $map['status'] = 1;
       $map['isdelete'] = 0;
       $map['total_num'] = [
           'gt',0
       ];
        $data  = $this->where($map)->field($field)->select();
       return $data;
    }
}