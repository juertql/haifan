<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 19:04
 */

namespace app\wechat\controller;

use think\Controller;

class Common extends  Controller
{

   public function __construct()
   {
       parent::__construct();
       $model = model('WebBaseSite','logic');
       $web_status = $model->getValue('web_status');
       if(!$web_status){
        $param['code'] = 100;
        $param['msg'] = '站点已关闭';
        echo json_encode($param);exit;
       }

   }
}