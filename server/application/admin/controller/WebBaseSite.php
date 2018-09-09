<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/20
 * Time: 13:59
 */

namespace app\admin\controller;


use think\Controller;
use think\Db;
use think\Request;

class WebBaseSite extends Controller
{
    public function index(){

        if($this->request->isPost()){

        }
        else{
            $aa =config('host_name').'/tmp/back4.jpg';//echo $aa;exit;
            $path = ROOT_PATH. 'public/tmp/day.text';
            $goods_index = ROOT_PATH. 'public/tmp/goods.text';
            $edit_index = ROOT_PATH. 'public/tmp/editDay.text';
            $area_index = ROOT_PATH. 'public/tmp/area.text';
            $res = file_get_contents($path);
            $index = file_get_contents($goods_index);
            $edit = file_get_contents($edit_index);
            $area = file_get_contents($area_index);

            //获取餐型
            $goods = Db::name('goods_type')->where('isdelete = 0 and status = 1')->field('id,name')->select();
            $this->view->img = $aa;
            $this->view->day = $res;
            $this->view->goods = $goods;
            $this->view->goods_index = $index;
            $this->view->edit = $edit;
            $this->view->area = $area;
            return $this->view->fetch();
        }
    }
     public function set(){
         $data = input('post.day');
        
         $path = ROOT_PATH. 'public/tmp/day.text';
         $res = file_put_contents($path,$data);
         if(!$res){
             returnData('','',122);
         }
         returnData('','',200);
     }
    public function setGoods(){
         $data = input('post.index');
         $path = ROOT_PATH. 'public/tmp/goods.text';
         $res = file_put_contents($path,$data);
         if(!$res){
             returnData('','',122);
         }
         returnData('','',200);
     }
    public function setEdit(){
         $data = input('post.index');
         $path = ROOT_PATH. 'public/tmp/editDay.text';
         $res = file_put_contents($path,$data);
         if(!$res){
             returnData('','',122);
         }
         returnData('','',200);
     }
    public function setArea(){
         $data = input('post.index');
         $path = ROOT_PATH. 'public/tmp/area.text';
         $res = file_put_contents($path,$data);
         if(!$res){
             returnData('','',122);
         }
         returnData('','',200);
     }
}