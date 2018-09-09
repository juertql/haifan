<?php
/**
 * 产品
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 9:46
 */

namespace app\wechat\controller;
use think\Controller;
use think\Db;

class Goods extends Common
{
    public function __construct()
    {
        parent::__construct();
        /** 处理昨日订单 */
        $map['isdelete'] = 0;
        $map['order_status'] = 1;
        $map['year_months_day'] = [
            'lt',
            date('Ymd',strtotime('+1day'))
        ];
        $update['order_status'] = 2;
        $upadte_type['order_num'] = ['exp', 'order_num - 1'];
        $upadte_type['use_num'] = ['exp', 'use_num + 1'];
        /** 查询 */
        Db::startTrans();
        try{
            $list = Db::name('order')->where($map)->field('id,goods_type_id,user_id')->lock(true)->select();
            
            if (count($list) > 0) {
                $text = '';
                foreach ($list as $v) {
                    $id = $v['id'];
                    $map2['goods_type_id'] = $v['goods_type_id'];
                    $map2['user_id'] = $v['user_id'];

                    /** 改变状态 */
                    Db::name('order')->where('id', $id)->update($update);
                    /** 成功数量改变 */
                    Db::name('goods')->where($map2)->update($upadte_type);
                    $text .= '会员ID：' . (int) $v['user_id'] . ';餐型ID：' . (int) $v['goods_type_id'] . '<br />';
                }
                $text = date('Y-m-d',time()) . '<br />' . $text;
                $path = ROOT_PATH . 'public/tmp/goods_log2.text';
                file_put_contents($path,$text,FILE_APPEND);
            }
            
            Db::commit();
        }
        catch(\Exception $e){
            $path = ROOT_PATH . 'public/tmp/goods_error2.text';
            file_put_contents($path,date('Y-m-d :H:i:s',time()). '<br />' . $e,FILE_APPEND);
           Db::rollback();
        }

    }
  /**
   *  产品列表 post
   */
  public function getGoodsType(){
     if($this->request->isPost()){
       $data = input('post.');
       $map['status'] = 1;
       $map['isdelete'] = 0;
       /** 首页 */
      if(isset($data['is_show'])){
        $map['is_show'] = 1;
      }
       /** 获取默认 */
       $index = 0;
       $user_id = is_numeric(input('post.user_id')) && (int) input('post.user_id') > 0 ? input('post.user_id') : 0;
       if($user_id > 0){
         $default_goods_id = Db::name('user')->where('id = '.$user_id)->value('default_goods_id');
        if($default_goods_id){
          $index = $default_goods_id;
        }
       }
     /*  $goods_index = ROOT_PATH. 'public/tmp/goods.text';
       $index = (int) file_get_contents($goods_index);*/
       $list = Db::name('goods_type')->where($map)->order('field(id,'.$index.')')->select();
       if(!$list){
         return returnData('','暂无数据',108);
       }
       if(count($list) > 0){
         /** 处理数据 */
         foreach($list as &$v){
           $v['name'] = $v['name'].'--'.$v['spec'];
           $v['img'] = config('host_name').$v['img'];
           $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
         }
       }
       return returnData(array_reverse($list));
     }
    else{
      return returnData('','请求错误',101);
    }
  } /**
   *  产品列表 post
   */
  public function getActivity(){
     if($this->request->isPost()){
       $data = input('post.');
       $map['status'] = 1;
       $map['isdelete'] = 0;


       $list = Db::name('activity')->where($map)->order('id desc')->select();
       if(!$list){
         return returnData('','暂无数据',108);
       }
       if(count($list) > 0){
         /** 处理数据 */
         foreach($list as &$v){
           $v['img'] = config('host_name').$v['img'];
           $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
         }
       }
       return returnData(($list));
     }
    else{
      return returnData('','请求错误',101);
    }
  }
  public function getGoodsDetail(){
    if($this->request->isPost()){
      $data = input('post.');
      $id = $data['id'];
      $list = Db::name('goods_type')->find($id);
      if(!$list){
        return returnData('','暂无数据',108);
      }
        /** 处理数据 */
          $list['img'] = config('host_name').$list['img'];
          $list['create_time'] = date('Y-m-d H:i:s',$list['create_time']);
      return returnData($list);
    }
    else{
      return returnData('','请求错误',101);
    }
  }
    public function getAcDetail(){
    if($this->request->isPost()){
      $data = input('post.');
      $id = $data['id'];
      $list = Db::name('activity')->find($id);
      if(!$list){
        return returnData('','暂无数据',108);
      }
        /** 处理数据 */
          $list['img'] = config('host_name').$list['img'];
          $list['create_time'] = date('Y-m-d H:i:s',$list['create_time']);
      return returnData($list);
    }
    else{
      return returnData('','请求错误',101);
    }
  }
}