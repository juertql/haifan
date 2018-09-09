<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17/017
 * Time: 16:03
 */

namespace app\wechat\logic;


use think\Db;
use think\Model;

class Order extends  Model
{
    /**
     * 获取某月订单
     * @param $user_id
     * @param $month
     */
    public function getMonthOrder($user_id,$month){
       if((int) $user_id < 0 || !$month){
           return false;
       }
        $map['user_id'] = $user_id;
        $map['year_months'] =  str_replace('-','',$month);
        $map['order_status'] =  [
            'neq','3'
        ];
        $data = $this->where($map)->field('id,sn,order_status,year_months,year_months_day')->select();
        return $data;
    }

    /**
     * 订单详情
     * @param $user
     * @param $sn
     */
    public function getOrderDetail($user,$sn){
        if((int) $user < 0 || !$sn){
            return false;
        }
        $map['user_id'] = $user;
        $map['sn'] = $sn;
        $data = $this->where($map)->field('id,mobile,goods_type_id,order_status,name,remark,address,address1,address2,year_months_day,give_food_time')->find();
        /** 处理数据 */
        $data['type_name'] = Db::name('goods_type')->where('id',$data['goods_type_id'])->value('name');
        $data['type_img'] = config('__host_name__') .Db::name('goods_type')->where('id',$data['goods_type_id'])->value('img');
        $data['address1'] =  $data['address1'] == '' ? '' : json_decode($data['address1'],true);
        //$data['create_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * 统计
     * total
     * rest
     * use
     * @param $user_id
     */
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