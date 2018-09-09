<?php
/**
 * 统计
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/21/021
 * Time: 16:18
 */

namespace app\admin\controller;


use think\Controller;
use think\Db;

class Count extends  Controller
{
    public function index(){

        $map['isdelete'] = 0;
        $user = Db::name('user')->where($map)->count();
        $rest_num = Db::name('goods')->sum('rest_num');
        $use_num = Db::name('goods')->sum('use_num');
        $eat_num = Db::name('goods')->sum('order_num');
        $total_num = (int) $use_num + (int) $rest_num +(int)$eat_num;
        $this->view->user = $user;
        $this->view->rest_num = $rest_num;
        $this->view->use_num = $use_num;
        $this->view->order_num = $eat_num;
        $this->view->total_num = $total_num;
        return $this->view->fetch();
    }

}