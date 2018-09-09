<?php
/**
 *
 * 礼品业务层
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/21/021
 * Time: 9:52
 */

namespace app\wechat\controller;


use think\Controller;
use think\Db;
use think\Request;

class Gift extends Controller
{
    private  $model;
    public function __construct()
    {
        parent::__construct();
        /** 处理昨日订单 */
        $map['isdelete'] = 0;
        $map['order_status'] = 1;
        $map['year_months_day'] = [
            'lt',
            date('Ymd',time()) ];
        $update['order_status'] = 2;
        $upadte_type['order_num'] = ['exp','order_num + 1'];
        /** 查询 */
        $list = Db::name('order')->where($map)->field('id,goods_type_id,user_id')->select();
        if(count($list) > 0){
            foreach($list as $v){
                $id = $v['id'];
                $map2['goods_type_id'] = $v['goods_type_id'];
                $map2['user_id'] = $v['user_id'];

                /** 改变状态 */
                Db::name('order')->where('id',$id)->update($update);
                /** 成功数量改变 */
                Db::name('goods')->where($map2)->update($upadte_type);
            }
        }
        $this->model = model('Gift','logic');
    }
    /** 新增订单 */

    public function add(){
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user_id = (int) $data['user_id'];
            if($user_id < 0 ){
                return returnData('','请先登录2',100);
            }
            if(!isset($data['gift_id'])){
                return returnData($data,'参数错误',102);
            }
            /** 检查库存 */
            $num = Db::name('gift')->where('id',$data['gift_id'])->value('total_num');
            if((int) $num < 1){
                return returnData($data,'系统错误',103);
            }
            /** 加入礼物临时表 */
            $queue['user_id'] = $user_id;
            $queue['gift_id'] = $data['gift_id'];
            $res = Db::name('gift_queue')->insert($queue);
            if(!$res){
                return returnData($data,'系统错误',104);
            }
            $param = $this->model->addGift($data);
            if(!$param){
                return returnData($data,'系统错误',105);
            }
            return returnData('','添加成功',200);
        }
        else{
            return returnData('','非法请求',101);
        }
    }
    /**
     * 获取礼品列表
     */
   public function getList(){
       if(Request::instance()->isPost()) {
           $data = input('post.');
           $list = $this->model->getList();
           if(!$list){
               return returnData($this->model->getLastSql(), '失败', 120);
           }
           return returnData($list, '成功', 200);
       }
       else{
           return returnData('','非法请求',101);
       }
   }
    /** 检测是否能抽奖 */
    public function checkGiftChance(){
        if(Request::instance()->isPost()) {
            $data = input('post.');
            $user_id = (int) $data['user_id'];
            if($user_id < 0 ){
                return returnData('','请先登录2',100);
            }
            $map['user_id'] = $user_id;
            $start_time = strtotime(date('Y-m-d',time()));
            $end_time = strtotime('+ 1 day',$start_time);
            $map['create_time'] = [
             'between',
                [
                    $start_time,
                    $end_time
                ]
            ];
            $obj = Db::name('gift_log')->where($map)->find();
            $flag = false;
            if($obj){
              $flag = true;
            }
            return returnData(!$flag, '成功', 200);
        }
        else{
            return returnData('','非法请求',101);
        }
    }
}