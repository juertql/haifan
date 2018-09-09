<?php
namespace app\index\controller;

use think\Db;

class Index
{
    public function index()
    {

       /* $ymd = Db::name('order')->where("user_id = 146 and order_status = 1")->max('year_months_day');
        $y = substr($ymd,0,4);
        $m = substr($ymd,4,2);
        $d = substr($ymd,6,2);
        echo $y.'<br/>';
        echo $m.'<br/>';
        echo $d.'<br/>';
        echo $ymd;exit;*/
        return \think\Response::create(\think\Url::build('/admin'), 'redirect');
    }
}
