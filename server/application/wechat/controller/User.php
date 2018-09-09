<?php
/**会员业务层
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/22/022
 * Time: 10:12
 */

namespace app\wechat\controller;


use think\Controller;
use think\Db;
use think\Request;

class User extends Controller
{
    /**
     * 修改密码
     */
    public function setPassword(){
        if(Request::instance()->isPost()){
            $old_password = input('post.old_password');
            $password = input('post.password');
            $user_id = input('post.user_id');
            if((int) $user_id < 0 ){
                return returnData('','非法请求','100');
            }
            if(!trim($old_password) || !trim($password)){
                return returnData('','请输入','108');
            }
            if(strlen($password) < 6){
                return returnData('','密码至少6位',111);
            }
            $map['id'] = $user_id;
            $map['isdelete'] = 0;
            $user = Db::name('user')->where($map)->find();
            if(!$user){
                return returnData('','无此用户',101);
            }
            if($user['status'] == 0){
                return returnData('','用户已被锁定',102);
            }
            if($user['password'] != md5(md5($old_password).$user['encrypt'])){
                return returnData('','密码错误',103);
            }
            $res['user_id'] = $user['id'];
            /** 修改密码 */
            $update['password'] =md5(md5($password).$user['encrypt']);
            $update['update_time'] = time();
            $param = Db::name('user')->where('id',$user_id)->update($update);
            if(!$param){
                return returnData('','修改失败' ,300);
            }
            return returnData('','修改成功' ,200);
        }
        else{
            return returnData('','非法请求',100);
        }
    }
    /**
     * 获取中奖记录
     */
    public function getGiftList(){
        if(Request::instance()->isPost()){
            $user_id = input('post.user_id');
            if((int) $user_id < 0 ){
                return returnData('','非法请求','100');
            }
            $map['isdelete'] = 0;
            $map['user_id'] = $user_id;
            $list = Db::name('gift_log')
                ->where($map)
                ->field('create_time,order_status,gift_id')
                ->order('id desc')
                ->select();
            if(count($list) > 0){
               foreach($list as &$v){
                   $v['name'] = Db::name('gift')->where('id',$v['gift_id'])->value('name');
                   $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
               }
            }
            return returnData($list,'成功' ,200);
        }
        else{
            return returnData('','非法请求',100);
        }
    }

    /**
     * 获取身体数据
     */
    public function getBody(){
        if(Request::instance()->isPost()){
            $user_id = input('post.user_id');
            if((int) $user_id < 0 ){
                return returnData('','非法请求','100');
            }
            $ym =  input('post.ym');
            $map['user_id'] = (int) $user_id;
            $map['ym'] = str_replace('-','',$ym);
            $list = Db::name('user_body')
                ->where($map)
                ->find();
            if(!$list){
                return returnData('','22' ,300);
            }
           $list['img_list'] = $list['img_list'] ? config('host_name').$list['img_list'] : '';
            return returnData($list,'成功' ,200);
        }
        else{
            return returnData('','非法请求',100);
        }
    }
    /**
     * 获取more数据
     */
    public function getMoreBody(){
        if(Request::instance()->isPost()){
            $user_id = input('post.user_id');
            if((int) $user_id < 0 ){
                return returnData('','非法请求','100');
            }
            $ym =  input('post.ym');
            $map['user_id'] = (int) $user_id;
            $map['ym'] = [
                'lt',
                date('Ym',time())
            ];
            $list = Db::name('user_body')
                ->where($map)
                ->field('id,ym,img_list')
                ->select();
            if(!count($list) > 0){
                return returnData('','22' ,300);
            }
            /** 处理数据 */
            foreach($list as &$v){
                $v['img_list'] = $v['img_list'] ? config('host_name').$v['img_list'] : '';
            }
            return returnData($list,'成功' ,200);
        }
        else{
            return returnData('','非法请求',100);
        }
    }
    /**
     * 修改身体数据
     */
    public function editBody(){
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user_id = input('post.user_id');
            if((int) $user_id < 0 ){
                return returnData('','非法请求','100');
            }
            $id =  input('post.id');
            $map['user_id'] = (int) $user_id;
            $list = Db::name('user_body')
                ->where($map)
                ->update($data['adds']);
            if(!$list){
                return returnData('','22' ,300);
            }

            return returnData(2,'成功' ,200);
        }
        else{
            return returnData('','非法请求',100);
        }
    }
    /**
     * add身体数据
     */
    public function addBody(){
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user_id = input('post.user_id');
            if((int) $user_id < 0 ){
                return returnData('','非法请求','100');
            }
            $data['adds']['user_id'] = (int) $user_id;
            $data['adds']['ym'] = date('Ym',time());
            $list = Db::name('user_body')
                ->insert($data['adds']);
            if(!$list){
                return returnData('','22' ,300);
            }

            return returnData(2,'成功' ,200);
        }
        else{
            return returnData('','非法请求',100);
        }
    }
    /**
     * 修改身体数据
     */
    public function editBodyImg(){
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user_id = input('post.user_id');
            if((int) $user_id < 0 ){
                return json_encode(300);
            }
            $file = $this->request->file('file');
            $path = ROOT_PATH . 'public/tmp/uploads';//echo json_encode($file);exit;
            $info = $file->move($path);
            if (!$info) {
                echo json_encode($file->getError());exit;
            }
            $name = $_SERVER['SCRIPT_NAME'];
            $name = str_replace('/api.php','',$name);
            $data =  '/tmp/uploads/' . $info->getSaveName();
            $insert = [
                'cate'     => 3,
                'name'     => $data,
                'original' => $info->getInfo('name'),
                'domain'   => '',
                'type'     => $info->getInfo('type'),
                'size'     => $info->getInfo('size'),
                'mtime'    => time(),
            ];
            Db::name('File')->insert($insert);
           /** @var  $id */
            //edit
            if(isset($data['id']) && is_numeric($data['id'])){
                $map['id'] = (int) $id;
                $update['img_list'] = '/tmp/uploads/' . $info->getSaveName();
                $list = Db::name('user_body')
                    ->where($map)
                    ->update($update);
            }
            else{
                $res['user_id'] = $user_id;
                $res['ym'] = date('Ym',time());
                /** 查询 */
                $aa = Db::name('user_body')->where($res)->find();
                $img_list = '/tmp/uploads/' . $info->getSaveName();
                if($aa){
                    $list = Db::name('user_body')->where($res)->setField('img_list',$img_list);
                }
                else{
                    $res['img_list'] = '/tmp/uploads/' . $info->getSaveName();
                    $list = Db::name('user_body')->insert($res);
                }


            }

            if(!$list){
                return json_encode(300);
            }

            return json_encode(200);
        }
        else{
            return json_encode(300);
        }
    }
    public function getBodyDetail(){
        if($this->request->isPost()){
            $data = input('post.');
            $id = $data['id'];
            $user_id = input('post.user_id');
            if((int) $user_id < 0 ){
                return returnData('','非法请求','100');
            }
            $list = Db::name('user_body')->find($id);
            if(!$list){
                return returnData('','暂无数据',108);
            }
            /** 处理数据 */
            $list['img_list'] = config('host_name').$list['img_list'];
            return returnData($list);
        }
        else{
            return returnData('','请求错误',101);
        }
    }
    /**
     * top榜
     */
    public function getTop(){
        if(Request::instance()->isPost()){
            $user_id = input('post.user_id');
            $type = input('post.types');
            if((int) $user_id < 0 ){
                return returnData('','非法请求','100');
            }
           if($type != 'total' && $type != 'use' ){
                return returnData(input('post.'),'请求错误',102);
            }

            //select * from (SELECT user_id, SUM(use_num + rest_num) as total FROM tp_goods GROUP BY user_id) ss ORDER BY total DESC  limit 10
                $total_sql = "select * from (SELECT user_id, SUM(use_num + rest_num + order_num) as num FROM tp_goods GROUP BY user_id) ss ORDER BY num DESC  limit 10";
               $use_sql = "select * from (SELECT user_id, SUM(use_num) as num FROM tp_goods GROUP BY user_id) ss ORDER BY num DESC  limit 10";
            if($type == 'total'){
                $sql = $total_sql;
            }
           else{
               $sql = $use_sql;
           }
            $data = Db::query($sql);
            /** 处理数据 */
            if(count($data) > 0){
             foreach($data as &$v){
                 $user = Db::name('user')->field('name,avatar')->find($v['user_id']);
                 $name = (string) $user['name'];
                 $v['name'] = str_replace(mb_substr($name,1,1,'utf-8'),'*',$name) ;
                 $v['avatar'] = $user['avatar'] ? $user['avatar'] : config('__host_name__').'/user/user.jpg';
             }
            }
            return returnData($data,'成功' ,200);
        }
        else{
            return returnData('','非法请求',100);
        }
    }
}