<?php
/**
 * 登录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17/017
 * Time: 10:47
 */

namespace app\wechat\controller;


use think\Controller;
use think\Db;
use think\Request;

class Login extends  Controller
{
    /**
     * 登录
     */
    public function login(){
        if(Request::instance()->isPost()){
            $name = input('post.mobile');
            $password = input('post.password');
            $avatar = input('post.avatar');
            if(!trim($name) || !trim($password)){
                return returnData('','请输入','108');
            }
            $map['name|mobile'] = $name;
            $map['isdelete'] = 0;
            $user = Db::name('user')->where($map)->find();
            if(!$user){
                return returnData('','无此用户',101);
            }
            if($user['status'] == 0){
                return returnData('','用户已被锁定',102);
            }
            if($user['password'] != md5(md5($password).$user['encrypt'])){
                return returnData(md5(md5('154767').$user['encrypt']),'密码错误',103);
            }
            $res['user_id'] = $user['id'];
            $res['address'] = $user['address'];
            $res['name'] = $user['name'];
            $res['mobile'] = $user['mobile'];
            $res['remark'] = $user['remark'];
            if(!$user['avatar']){
                $update['avatar'] = $avatar;
                Db::name('user')->where('id',$user['id'])->update($update) ;
            }
            //保存信息
            //session('user', 'rdgrfgfgh');
            //Hook::listen('login_after',$user['id'],'login_after');
            /** 登录状态 login_key  */
            return returnData($res,'登录成功' ,200);
        }
        else{
            return returnData('','非法请求',100);
        }
    }
}