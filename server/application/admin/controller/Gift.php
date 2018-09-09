<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;

class Gift extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $map['status'] = 1;
        $map['isdelete'] = 0;
        $type = Db::name('goods_type')->where($map)->field('id,name')->select();
        $this->view->type = $type;
    }

    use \app\admin\traits\controller\Controller;
    // 方法黑名单
    protected static $blacklist = [];

    /**
     * 中奖记录
     * @return string
     */
    public function giftLog(){
        // 列表过滤器，生成查询Map对象
        $map = [];
        $map['order_status'] = 1;
        if ($this->request->param("order_status")) {
            $map['order_status'] = $this->request->param("order_status");
        }
        if(input("param.start_time") && input("param.end_time")){
            $map['create_time'] = ['between time',[strtotime(input("param.start_time")),strtotime(input("param.end_time"))]];
        }
        $map['isdelete'] = 0;
        $map['isdelete'] = 0;
        // 查询字段
        $field = 'update_time';
        $listRows = 10;

        // 分页查询
        $list = Db::name('gift_log')
            ->field($field,true)
            ->where($map)->order('id desc')->paginate($listRows, false, ['query' => $this->request->get()]);

        // 模板赋值显示
        $this->view->assign('list', $list);
        $this->view->assign("page", $list->render());
        $this->view->assign("count", $list->total());
        $this->view->assign('numPerPage', $listRows);
        return $this->view->fetch('gift_log');
    }

    /**
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function changeStatus(){
        $data = input('post.');
        $ids = $data['id'];
        $status = $data['status'];

        $update['order_status'] = $status;
        $update['update_time'] = time();
        /** @var  $res */
        $gift_log = Db::name('gift_log')->field('user_id,gift_id,order_status')->find($ids);
        if($gift_log['order_status'] !== 1){
            return returnData('','请求失败',102);
        }
        if($status == 3){
            /** 修改礼物记录 */
            $ss = Db::name('gift_log')->where('id',$ids)->update($update);
            if(!$ss){
                return returnData('','取消失败',102);
            }
            return returnData('','取消成功',200);
        }
        $user_id = $gift_log['user_id'];
        Db::startTrans();
        try{
            $detail = Db::name('gift')->find($gift_log['gift_id']) ;
            $type = $detail['type'];
            //虚拟商品
            if($type === 1){
              /** 增加库存 */
                $goods_type_id = $detail['goods_type_id'];
                $map1['user_id'] = $user_id;
                $map1['goods_type_id'] = $goods_type_id;
                $up1['rest_num'] = ['exp','rest_num + 1'];
                $up1['update_time'] = time();
                Db::name('goods')->where($map1)->update($up1);
                /** 修改礼物记录 */
                Db::name('gift_log')->where('id',$ids)->update($update);
            }
            //现实商品
            if($type === 2){
                $date = date('Ymd',time());
                /** 查询该用户有无待送订单 */
                $order_map['user_id'] = $user_id;
                $order_map['order_status'] = 1;
                $order_map['other'] = '';
                $order_map['year_months_day'] = [
                    'gt',
                    $date
                    ];
               $order = Db::name('order')->where($order_map)->min('year_months_day');
                if(!$order){
                    return returnData( Db::name('order')->getLastSql(),'无订单绑定',100);
                }
               $order_map['year_months_day'] = $order;
                $update_order['other'] = '赠送'.$detail['name'];
                $update_order['update_time'] = time();
                $res = Db::name('order')->where($order_map)->update($update_order);
                /** 修改礼物记录 */
                Db::name('gift_log')->where('id',$ids)->update($update);
            }
            else{
                return returnData('','发送失败',103);
            }
            Db::commit();
        }
        catch(Exception $e){
            Db::rollback();
            return returnData('','发送失败',100);
        }
        return returnData($order,'发送成功',200);
    }
}
