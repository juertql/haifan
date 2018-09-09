<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 17:44
 */

namespace app\wechat\controller;


use mailer\lib\Config;
use think\Controller;
use think\Db;
define("TOKEN", "shengqiang");
class Index extends Controller
{
  public function index(){
   /* $user['id'] = 1;
    $user['name'] = 'test';
    session('user',$user);*/
    print_r(session('user'));exit;
    return $this->view->fetch();
    //$this->valid();
  }
  public function valid()
  {
    $echoStr = $_GET["echostr"];
    //valid signature , option
    if($this->checkSignature()){
      echo $echoStr;
      exit;
    }
  }

  private function checkSignature()
  {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];

    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );

    if( $tmpStr == $signature ){
      return true;
    }else{
      return false;
    }
  }
  /**
   * 获取网站配置  get name
   */

  public function getWebValue(){
    if(!input('?get.name')){
      return returnData('','请求错误',101);
    }
    $name = input('get.name');
    $model = model('WebBaseSite','logic');
    $data = $model->getValue($name);
    if(!$data){
      return returnData('',$model->getError(),102);
    }
    /*if($name == ('web_logo'||'web_index_back')){
      $data = config('web_host').$data;
    }*/
    return returnData($data);
  }

  /**
   *  获取轮播图 post
   *  num 条数 默认 2个 ,field 字段
   */

  public function getBanner(){

    if($this->request->isPost()){
      $data = $this->request->post();
      $num = (isset($data['num']) && is_numeric($data['num'])) ? $data['num'] : 3;
      $field = (isset($data['field'])) ? $data['field'] :'id,img_url,title,info';
      //初始条件
      $map['isdelete'] = 0;
      $map['status'] = 1;
      $data = Db::name('banner')->where($map)->field($field)->limit($num)->order('sort,id desc')->select();
      if(!$data){
        return returnData('','暂无数据',108);
      }
      /*foreach($data as $k => $v){
        if($v['url'] != ''){
          $data[$k]['url'] = config('web_host').$v['url'];
        }
      }*/
    return returnData($data);
    }
    else{
      return returnData('','请求错误',101);
    }
  }
}