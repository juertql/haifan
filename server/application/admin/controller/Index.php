<?php
/**
 * tpAdmin [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://tpadmin.yuan1994.com/
 * @copyright 2016 yuan1994 all rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

//------------------------
// 管理后台首页
//-------------------------

namespace app\admin\controller;

use app\admin\Controller;
use think\Loader;
use think\Session;
use think\Db;

class Index extends Controller
{

    public function index()
    {

        //echo substr(13438763615,-6,6);exit;
        // 读取数据库模块列表生成菜单项
        $nodes = Loader::model('AdminNode', 'logic')->getMenu();

        // 节点转为树
        $tree_node = list_to_tree($nodes);

        // 显示菜单项
        $menu = [];
        $groups_id = [];
        foreach ($tree_node as $module) {
            if ($module['pid'] == 0 && strtoupper($module['name']) == strtoupper($this->request->module())) {
                if (isset($module['_child'])) {
                    foreach ($module['_child'] as $controller) {
                        $group_id = $controller['group_id'];
                        array_push($groups_id, $group_id);
                        $menu[$group_id][] = $controller;
                    }
                }
            }
        }

        // 获取授权节点分组信息
        $groups_id = array_unique($groups_id);
        if (!$groups_id) {
            exception("没有权限");
        }
        $groups = Db::name("AdminGroup")->where(['id' => ['in', $groups_id], 'status' => "1"])->order("sort asc,id asc")->field('id,name,icon')->select();

        $this->view->assign('groups', $groups);
        $this->view->assign('menu', $menu);

        return $this->view->fetch();
    }

    /**
     * 欢迎页
     * @return mixed
     */
    public function welcome()
    {
        header('content-type:text/html;charset=utf-8');

        // 查询 ip 地址和登录地点
        if (Session::get('last_login_time')) {
            $last_login_ip = Session::get('last_login_ip');
            $last_login_loc = \Ip::find($last_login_ip);

            $this->view->assign("last_login_ip", $last_login_ip);
            $this->view->assign("last_login_loc", implode(" ", $last_login_loc));

        }
        $current_login_ip = $this->request->ip();
        $current_login_loc = \Ip::find($current_login_ip);
        /** 查询今天待处理订单 */
        $order_map['isdelete'] = 0;
        $order_map['order_status'] = 1;
        $order_map['year_months_day'] =  date('Ymd',strtotime('+ 1 day',time())) ;
        $todoOrder = Db::name('order')->where($order_map)->count();
        $this->view->assign("current_login_ip", $current_login_ip);
        $this->view->assign("todoOrder", $todoOrder);
        $this->view->assign("current_login_loc", implode(" ", $current_login_loc));
        /** 处理昨日订单 */
        $map['isdelete'] = 0;
        $map['order_status'] = 1;
        $map['year_months_day'] = [
            'lt',
            date('Ymd',strtotime('+1day'))
        ];
        $update['order_status'] = 2;
        $upadte_type['order_num'] = ['exp','order_num - 1'];
        $upadte_type['use_num'] = ['exp','use_num + 1'];
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
                    // 1018 日志记录
                    $log['user_id'] = (int) $v['user_id'];
                    $log['goods_type_id'] = (int) $v['goods_type_id'];
                    $log['num'] = 1;
                    $log['create_time'] = date('Y-m-d',time());
                    $text .= '会员ID：' . (int) $v['user_id'] . ';餐型ID：' . (int) $v['goods_type_id'] . '<br />';
                }
                $text = date('Y-m-d',time()) . '<br />' . $text;
                $path = ROOT_PATH . 'public/tmp/goods_log.text';
                file_put_contents($path,$text,FILE_APPEND);
            }
            Db::commit();
        }
        catch(\Exception $e){
            $path = ROOT_PATH . 'public/tmp/goods_error.text';
            file_put_contents($path,date('Y-m-d :H:i:s',time()). '<br />' . $e,FILE_APPEND);
            Db::rollback();
        }


        /*$goods = Db::name('goods')->select();
        $bb = [];
        $aa = [];
        foreach($goods as $k => $v){
            if(isset($bb[$k]['user_id'])){
                $aa[$k]['user_id'] = $v['user_id'];
            }
            else{
                $bb[$k]['user_id'] = $v['user_id'];
            }
        }
        //print_r(count($aa));exit;
      // $order = Db::name('order')->where('order_status = 2')->select();

        $arr = [];

        foreach($bb as $k => $v){
           $user_id = $v['user_id'];
            if(!isset($arr[$v['user_id']])){
                $num = Db::name('goods')->where('user_id = ' .$v['user_id'])->value('use_num');
                $o_num = Db::name('goods')->where('user_id = ' .$v['user_id'])->value('order_num');
                $o = Db::name('order')->where('order_status = 2 and user_id = '.$v['user_id'])->count();
                $c = Db::name('order')->where('order_status = 1 and user_id = '.$v['user_id'])->count();

                if($c != $o_num ||$o != $num && $v['user_id'] != (74 && 123 && 126 && 139 && 147 && 170 && 232 && 293 && 359)){
                    $arr[$v['user_id']]['use'] = $o;
                    $arr[$v['user_id']]['goods_use'] = $num;
                    $arr[$v['user_id']]['goods_order'] = $o_num;
                    $arr[$v['user_id']]['order'] = $c;
                }
               $up222['use_num'] = $o;
               $up222['order_num'] = $c;
               //Db::name('goods')->where('user_id = ' .$v['user_id'])->update($up222);
            }

        }

print_r($arr);exit;*/
         /** @var  $info */
        // 查询个人信息
        $info = Db::name("AdminUser")->where("id", UID)->find();
        $this->view->assign("info", $info);

        return $this->view->fetch();
    }
}