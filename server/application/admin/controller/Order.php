<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);
load_trait('controller/Jump');
use app\admin\Controller;
use think\Db;
use think\Exception;

class Order extends Controller
{
    use \app\admin\traits\controller\Controller;
    use \traits\controller\Jump;

    // 方法黑名单
    protected static $blacklist = [];

    protected function filter(&$map)
    {
        if(input("param.start_time") && input("param.end_time")){
            $map['year_months_day'] = [
                'between',
                [
                    str_replace('-','',input("param.start_time")),
                    str_replace('-','',input("param.end_time"))
                ]
            ];
        }
        $is_sale = session('is_sale');

        if($is_sale === 1){
            $map['sale_id']   = session(config('rbac.user_auth_key'));
        }
        else{
            //$map['year_months_day'] = date('Ymd',time()) + 1;
        }
        if ($this->request->param("name") != '') {
            $user_id = Db::table('tp_user')
                ->where(['name' => $this->request->param("name")])
                ->value('id');
            $map['user_id'] = (int)$user_id;
        }
        if ($this->request->param("address_name") != '') {
            $map['name'] = ['like','%'.$this->request->param("address_name").'%'];
        }
        $map['order_status'] = 1;
        if ($this->request->param("order_status")) {
            $map['order_status'] = (int) ($this->request->param("order_status"));
        }
        /*if(isset($map['name'])){
            unset($map['name']);
        }*/
    }
	
	
    public function changStatus(){
        $data = input('post.');
        $ids = $data['id'];
        $status = $data['status'];
        $update['order_status'] = $status;
        $update['update_time'] = time();
        /** @var  $res */

        Db::startTrans();
        try{
            $detail = Db::name('order')->where('id',$ids)->field('user_id,goods_type_id')->find() ;
          /*  //配送成功  处理
            if($status == 2){
                $upadte_type['use_num'] = ['exp','use_num + 1'];
                $upadte_type['order_num'] = ['exp','order_num - 1'];
            }*/
            //取消处理
            if($status == 3){
                $upadte_type['rest_num'] = ['exp','rest_num + 1'];
                $upadte_type['order_num'] = ['exp','order_num - 1'];
            }
            $obg = Db::name('goods')->where($detail)->update($upadte_type);
            $res = Db::name('order')->where('id',$ids)->update($update);
            Db::commit();
        }
        catch(Exception $e){
            Db::rollback();
            return returnData('','失败',100);
        }
        return returnData('','成功',200);
    }
    public function changStatusAll(){
        $data = input('post.');
        $ids = $data['ids'];

        $update['order_status'] = 3;
        $update['update_time'] = time();
        /** @var  $res */

        Db::startTrans();
        try{
            $list = Db::name('order')->where("id in ($ids) ")->select();
            //return returnData($list,Db::name('order')->getLastSql(),100);
            if(count($list) > 0){
                foreach($list as $detail){
                    $upadte_type['rest_num'] = ['exp','rest_num + 1'];
                    $upadte_type['order_num'] = ['exp','order_num - 1'];
                    $obg = Db::name('goods')->where("goods_type_id = " .$detail['goods_type_id']. " and user_id = ".$detail['user_id'])->update($upadte_type);
                    $res = Db::name('order')->where('id',$detail['id'])->update($update);

            }
            }
            Db::commit();
        }
        catch(Exception $e){
            Db::rollback();
            return returnData('',$e->getMessage(),100);
        }
        return returnData('','成功',200);
    }
    /**
     * 导出
     * exportOrder
     */

    public function exportOrder1(){
        //print_r(input('param.'));exit;
        /** 获取数据  */
        $map['isdelete'] = 0;
        if ($this->request->param("order_status")) {
            //$map['order_status'] = 1;
			$map['order_status'] = $this->request->param("order_status");
        }

        if(input("param.start_time") && input("param.end_time")){

            $map['year_months_day'] = [
                'between',
                [
                    str_replace('-','',input("param.start_time")),
                    str_replace('-','',input("param.end_time"))
                ]
            ];
        }
        else{
            $map['year_months_day'] = date('Ymd',strtotime('+ 1 day',time())) ;
        }

        $data = Db::name('order')->where($map)->field('sn,name,mobile,address,year_months_day,goods_type_id,order_status,user_id,other')->select();

        if(count($data) < 1){
            $this->error('无导出数据');
        }
        /** 处理数据 */
        foreach($data as $k => &$v){
            $v['user_id'] = get_remark($v['user_id']);
            $name =  Db::name('goods_type')->where('id',$v['goods_type_id'])->field('name,spec')->find();
            /*if(stripos($v['address'],'省')){
                $v['address'] = substr($v['address'],stripos($v['address'],'省')+3);
            }*/
			 $v['address']= $v['address'];
            $v['year_months_day']  = get_ymd($v['year_months_day']);
            $v['goods_type_id'] = $name['name'] . '--'. $name['spec'];
            $v['order_status'] = $v['order_status'] == 1 ? '未配送' : '其他';
        }

        $header = ['订单号', '会员名', '电话', '送餐地址', '送餐日期',  '餐型', '状态', '备注','其他'];
        $name =  date('Y-m-d').'-选餐订单' ;
        if ($error = \Excel::export($header, $data, $name, '2007')) {
            throw new Exception($error);
        }
    }
    public function exportOrder(){
        //print_r(input('param.'));exit;
        /** 获取数据  */

        $map['isdelete'] = 0;
        $map['order_status'] = 1;

        if(input("param.start_time") && input("param.end_time")){
            $now = strtotime(date('Ymd',time()));
            $start =  str_replace('-','',input("param.start_time"));
            $last =  str_replace('-','',input("param.end_time"));
            //echo date('Ymd',strtotime('-3 day',strtotime($last)));exit;
            if($now < strtotime('-3 day',strtotime($last)) || strtotime($start) <= $now){

                $this->error('仅能导出3天内');
            }
            $map['year_months_day'] = [
                'between',
                [
                    str_replace('-','',input("param.start_time")),
                    str_replace('-','',input("param.end_time"))
                ]
            ];
        }
        else{
            $map['year_months_day'] = date('Ymd',strtotime('+ 1 day',time())) ;
        }
            $map['order_status'] = 1;
        $data = Db::name('order')->where($map)->field('sn,name,mobile,address,year_months_day,goods_type_id,order_status,user_id,other')->select();

        if(count($data) < 1){
            $this->error('无导出数据');
        }
        /** 处理数据 */
        foreach($data as $k => &$v){
            $v['user_id'] = get_remark($v['user_id']);
            $name =  Db::name('goods_type')->where('id',$v['goods_type_id'])->field('name,spec')->find();
           /* if(stripos($v['address'],'省')){
                $v['address'] = substr($v['address'],stripos($v['address'],'省')+3);
            }*/
			 $v['address']= $v['address'];
            $v['year_months_day']  = get_ymd($v['year_months_day']);
            $v['goods_type_id'] = $name['name'] . '--'. $name['spec'];
            $v['order_status'] = $v['order_status'] == 1 ? '未配送' : '其他';
        }
        $header = ['订单号', '会员名', '电话', '送餐地址', '送餐日期', '餐型', '状态', '备注','其他'];
        $name =  date('Y-m-d').'-选餐订单' ;
        if ($error = \Excel::export($header, $data, $name, '2007')) {
            throw new Exception($error);
        }
    }
    /**
     * 保存排序
     */
    public function sendGift()
    {
        $param = $this->request->param();
        if (!isset($param['other'])) {
            return ajax_return_adv_error('缺少参数');
        }

        $model = $this->getModel();
        foreach ($param['other'] as $id => $sort) {
            $model->where('id', $id)->update(['other' => $sort]);
        }
        /**  */
        return ajax_return_adv('赠送成功', 'current');
    }
}
