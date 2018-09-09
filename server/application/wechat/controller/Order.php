<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17/017
 * Time: 15:16
 */

namespace app\wechat\controller;


use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use think\Validate;
include '../application/log/log.php';

class Order extends Controller
{
    private  $model;
        public function __construct()
        {
            parent::__construct();
              /* if(check_user()){
                   return returnData('','请先登录1',100);
               }*/
            $this->model = model('Order','logic');
        }

    /** 新增订单 */

    public function add(){
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user_id = (int) $data['user_id'];
            if($user_id < 0 ){
                return returnData('','请先登录2',100);
            }
            if(!isset($data['year_months_day'])){
                return returnData($data,'参数错误',101);
            }
            $year_months_day = $data['year_months_day'];
            $sale_id = Db::name('user')->where('id',$user_id)->value('sale_id');
            $data['sale_id'] = $sale_id;
            $data['sn'] = time().random(8);
            $data['create_time'] = time();
            $data['year_months'] = substr($year_months_day,0,6);
            $validate = validate('Order');
            if(!$validate->check($data)){
                return returnData($data, $validate->getError(),112);
            }
            $map['user_id'] = $user_id;
            $map['goods_type_id'] =  $data['goods_type_id'];
            $data['address1'] =  json_encode($data['address1']);
            //获取库存
			//print_r($map);die;
            $rest_num = Db::name('goods')->where($map)->value('rest_num');
            if($rest_num < 1){
                return returnData('', '库存不足',113);
            }
            $data['log'] = '会员生成';

            Db::startTrans();
            try{
                
                DebugLog("add order data : ".array2str($data));
                //添加
                $res = Db::name('order')->insert($data);
                //减少库存
                $update['rest_num'] = ['exp','rest_num - 1'];
                $update['order_num'] = ['exp','order_num + 1'];
                $up = Db::name('goods')->where($map)->update($update);
                Db::commit();


            }catch(Exception $e){
                Db::rollback();
                //打印exception log
                DebugLog("add order exception : ".$e->getMessage());
                return returnData('', $e->getMessage(),119);
            }

            return returnData('','添加成功',200);
            /** 检测数据 */
            //Hook::listen('login_after',$user['id'],'login_after');
            /**   */
        }
        else{
            return returnData('','非法请求',101);
        }
    }
    /** 编辑订单 */

    public function edit(){
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user_id = (int) $data['user_id'];
            if($user_id < 0 ){
                return returnData('','请先登录2',100);
            }
            if(!isset($data['sn'])){
                return returnData($data,'参数错误',102);
            }
            if(!isset($data['year_months_day'])){
                return returnData($data,'参数错误',101);
            }
            $year_months_day = $data['year_months_day'];
            $data['year_months'] = substr($year_months_day,0,6);
            $detail = Db::name('order')->where('sn',$data['sn'])->find();
            if(!$detail){
                return returnData($data,'错误',104);
            }
            $validate = validate('Order');
            if(!$validate->scene('edit')->check($data)){
                return returnData($data, $validate->getError(),112);
            }
            $post_goods_type_id = intval($data['goods_type_id']) ;
            $old_goods_type_id = intval($detail['goods_type_id']) ;
            $data['update_time'] = time();
            $data['address1'] =  json_encode($data['address1']);
            $data['id'] = $detail['id'];
            $data['log'] = '编辑'.$detail['year_months_day'].'生成';
            DebugLog("edit order data : ".array2str($data));
            //改变商品类型处理库存
           // return returnData($old_goods_type_id,  $post_goods_type_id,113);
            if($post_goods_type_id != $old_goods_type_id){

                $map['user_id'] = $user_id;
                $map['goods_type_id'] =  $data['goods_type_id'];
                //获取库存
                $rest_num = Db::name('goods')->where($map)->value('rest_num');
                if($rest_num < 1){
                    return returnData('',  '库存不足',113);
                }
                Db::startTrans();
                try{
                    //处理新改变的类型库存
                    $now_update['rest_num'] = ['exp','rest_num - 1'];
                    $now_update['order_num'] = ['exp','order_num + 1'];
                    $now = Db::name('goods')->where($map)->update($now_update);
                    //处理之前类型库存
                    $old_update['rest_num'] = ['exp','rest_num + 1'];
                    $old_update['order_num'] = ['exp','order_num - 1'];
                    $old_map['user_id'] = $user_id;
                    $old_map['goods_type_id'] = $old_goods_type_id;
                    $old = Db::name('goods')->where($old_map)->update($old_update);
                    $res = Db::name('order')->where('sn',$detail['sn'])->update($data);
                    Db::commit();
                }
                catch( Exception $e){
                    Db::rollback();                    DebugLog("edit order exception : ".$e->getMessage());
                    return returnData('', $e->getMessage(),119);
                }
            }
            else{
                $res = Db::name('order')->where('sn',$detail['sn'])->update($data);
                if(!$res){
                    return returnData('','修改失败',120);
                }
            }

            return returnData('','修改成功',200);
            /** 检测数据 */
            //Hook::listen('login_after',$user['id'],'login_after');
            /**   */
        }
        else{
            return returnData('','非法请求',101);
        }
    }
    /** 取消订单 */

    public function cacelOrder(){
        if(Request::instance()->isPost()) {
            $data = input('post.');
            $user_id = (int)$data['user_id'];
            if ($user_id < 0 ) {
                return returnData('', '暂不能取消', 100);
            }
            if (!isset($data['sn'])) {
                return returnData($data, '参数错误', 102);
            }
            $detail = Db::name('order')->where('sn', $data['sn'])->find();
            if (!$detail) {
                return returnData($data, '错误', 104);
            }
            
            DebugLog("cancel order data : ".$data['sn']."  ".$data['user_id']);
            
            //改变商品类型处理库存
            $map['user_id'] = $user_id;
            $map['goods_type_id'] = $detail['goods_type_id'];
            Db::startTrans();
            try {
                //处理新改变的类型库存
                /*$update['rest_num'] = ['exp', 'rest_num + 1'];
                $update['order_num'] = ['exp', 'order_num - 1'];
                $now = Db::name('goods')->where($map)->update($update);*/


                  $ymd = Db::name('order')->where("user_id = $user_id and order_status = 1")->max('year_months_day');
                   $now_y = substr($ymd,0,4);
                   $now_m = substr($ymd,4,2);
                  $now_d = substr($ymd,6,2);
                 //$now_y = date('Y',time());//年
                   // $now_m = date('m',time());//月
                    //$now_d = date('d',time());//日
                    $num = 2;
                    $no_ymd = Db::name('order')->where("user_id = $user_id and order_status in (1,2)")->column('year_months_day');
                    /*$can_arr = $this->to($now_y,$now_m,$now_d,$num,$no_ymd);
                 //获取下个可用日期
                  $ymd1 = $can_arr[0];
                  $order_update['year_months_day'] = $ymd1;
                  $order_update['year_months'] = substr($ymd1,0,6);
                  $order_update['log'] = '取消'.$detail['year_months_day'].'生成';
                //处理之前类型库存
                $order_update['update_time'] = time();
                $order_update['order_status'] = 3;
                $res = Db::name('order')->where('sn', $detail['sn'])->update($order_update);*/
				
				// 修改内容
				$res = Db::name('order')->where('sn', $detail['sn'])->delete();
				
				$update['rest_num'] = ['exp', 'rest_num + 1'];
                $update['order_num'] = ['exp', 'order_num - 1'];
				Db::name('goods')->where($map)->update($update);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();                DebugLog("cancel order exception : ".$e->getMessage());
                return returnData('', $e->getMessage(), 119);
            }
            return returnData('', '修改成功', 200);

            /** 检测数据 */
            //Hook::listen('login_after',$user['id'],'login_after');
            /**   */
        }
        else{
            return returnData('','非法请求',101);
        }
    }
    public function to($now_y,$now_m,$now_d,$num,$no_ymd){

        $path = ROOT_PATH. 'public/tmp/day.text';
        $path = file_get_contents($path);
        $no_week = $path;
        //print_r($no_week);exit;
        $can_arr = [];//接受可用日期
        $date = $this->get_day($now_y,$now_m);
        $to_flag = false;
        //是否跳下页

        if($now_d <= $date){
            $start =  $now_d;
            for($start;$start <= $date;$start++){
                if((int)$start < 10 && (int)$start > 1){
                    $start = "0".(int)$start;
                }
                if(!in_array(($now_y.$now_m.$start),$no_ymd)){

                    if(count($can_arr) < $num){
                        if(stripos($no_week,date('w',strtotime($now_y.$now_m.$start))) === false){
                            array_push($can_arr,($now_y.$now_m.$start));
                        }

                    }

                }
            }
            if(count($can_arr) < $num){
                $to_flag = true;
            }
        }

        //月末 跳下月
        if($to_flag){
            $m = $now_m + 1;
            $y = $now_y;
            //年末 跳 下年
            if($now_m == 12){
                $y = $now_y + 1;
                $m = '01';
            }
            if((int)$m < 10 && (int)$m > 1){
                $m = "0".(int)$m;
            }
            //echo $now_y;exit;
            $date = $this->get_day($y,$m);
            $start = '01';
            for($start;$start <= $date;$start++){
                // echo $start;exit;
                if((int)$start < 10 && (int)$start > 1){
                    $start = "0$start";
                }
                if(!in_array(($y.$m.$start),$no_ymd)){
                    if(count($can_arr) < $num){
                        if(stripos($no_week,date('w',strtotime($y.$m.$start))) === false){
                            array_push($can_arr,($y.$m.$start));
                        }

                    }

                }
            }
        }
        if(count($can_arr) < $num){
            $now_num = $num - count($can_arr);
            $can_arr = array_merge($can_arr,$this->to($y,$m,'01',$now_num,$no_ymd)) ;
        }
        return $can_arr;
    }
    public function get_day( $y,$m )
    {

        $year = $y;
        $month = $m;
        if( in_array($month , array( 1 , 3 , 5 , 7 , 8 , 01 , 03 , 05 , 07 ,'08', 10 , 12)))
        {
            // $text = $year.'年的'.$month.'月有31天';
            $text = '31';
        }
        elseif( $month == 2 )
        {
            if ( $year%400 == 0 || ($year%4 == 0 && $year%100 !== 0) )    //判断是否是闰年
            {
                // $text = $year.'年的'.$month.'月有29天';
                $text = '29';
            }
            else{
                // $text = $year.'年的'.$month.'月有28天';
                $text = '28';
            }
        }
        else{
            // $text = $year.'年的'.$month.'月有30天';
            $text = '30';
        }
        return $text;
    }
    public function set(){
        $path = ROOT_PATH. 'public/tmp/day.text';
        $res = file_get_contents($path);
        return returnData($res,'',200);
    }
    public function setArea(){
        $path = ROOT_PATH. 'public/tmp/area.text';
        $res = file_get_contents($path);
        return returnData($res,'',200);
    }
    public function setA(){
        $path = ROOT_PATH. 'public/tmp/editDay.text';
        $res = file_get_contents($path);
        return returnData($res,'',200);
    }
    /** 统计订单 */

    public function count(){
        if(Request::instance()->isPost()) {
            $data = input('post.');
            $user_id = (int)$data['user_id'];
            if ($user_id < 0) {
                return returnData('', '请先登录2', 100);
            }
            $res = $this->model->getOrderCount($user_id);
            if (!$res) {
                return returnData($data, '错误', 104);
            }
            return returnData($res, '成功', 200);

        }
        else{
            return returnData('','非法请求',101);
        }
    }

    /**
     * 获取某月订单
     */

    public function getMonthOrder(){
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user_id = (int) $data['user_id'];
            if($user_id < 0 ){
                return returnData('','请先登录2',100);
            }
            if(!isset($data['month'])){
                return returnData($data,'参数错误',101);
            }
            $month = $data['month'];
            $list = $this->model->getMonthOrder($user_id,$month);
            if(!$list){
                return returnData($this->model->getLastSql(),'失败',180);
            }
            return returnData($list,'成功',200);
        }
        else{
            return returnData('','非法请求',101);
        }
    }
    /**
     * 获取订单详情
     */

    public function getOrderDetail(){
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user_id = (int) $data['user_id'];
            if($user_id < 0 ){
                return returnData('','请先登录2',100);
            }
            if(!isset($data['sn'])){
                return returnData($data,'参数错误',101);
            }
            $sn = $data['sn'];
            $list = $this->model->getOrderDetail($user_id,$sn);
            if(!$list){
                return returnData($this->model->getLastSql(),'失败',180);
            }
            //暂时处理
            if(stripos($list['address'],'四川省') === false){
                $list['address'] =  '四川省'.$list['address'];
            }
            return returnData($list,'成功',200);
        }
        else{
            return returnData('','非法请求',101);
        }
    }
}