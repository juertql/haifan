<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;

class Goods extends Controller
{   private $model  ;
    public function __construct()
    {
        parent::__construct();
        $this->model = model('Goods');
    }
    use \app\admin\traits\controller\Controller;
    // 方法黑名单
    protected static $blacklist = [];

    /**
     * 列表
     */
    public function index()
    {
        // 列表过滤器，生成查询Map对象
        $map = [];
        if ($this->request->param("user_id")) {
            $map['user_id'] =  $this->request->param("user_id") ;
        }
        // 查询字段
        $field = 'update_time';
        $listRows = 10;
        // 分页查询
        $list = $this->model
            ->field($field,true)
            ->where($map)->order('id desc')->paginate($listRows, false, ['query' => $this->request->get()]);

		
        // 数据处理
        /*foreach($list as &$v){
        }*/
        // 模板赋值显示
        $this->view->assign('list', $list);
        $this->view->assign("page", $list->render());
        $this->view->assign("count", $list->total());
        $this->view->assign('numPerPage', $listRows);
        return $this->view->fetch();
    }
    /** 新增 */
    public function add(){
        if($this->request->isPost()){
            $data = input('post.');//return $data;
            //print_r($data);exit;
            $validate = validate('Goods');
            if(!$validate->check($data)){
                return ajax_return_adv_error($validate->getError());
            }
            else{
                $this->model->save($data);
                if($this){
                    return ajax_return_adv('添加成功');
                }else{
                    return ajax_return_adv_error($this->model->getError());
                }
            };
        }
        else{
            /** 获取已用餐型 */
            $map['user_id'] =  $this->request->param("user_id") ;
            $ids = Db::name('goods')->where($map)->column('goods_type_id');
            $id_s = '';
            if(count($ids) > 0){
               $id_s = implode(',',$ids);
            }
            /** 餐型 */
            $type_map['status'] = 1;
            $type_map['isdelete'] = 0;
            $type_map['id'] = [
                'not in' ,$id_s
            ];
            $type = Db::name('goods_type')->where($type_map)->field('id,name,spec')->select();
            $this->view->user_id = $map['user_id'];
            $this->view->type = $type;
            return $this->view->fetch('edit');
        }
    }
    /**
     * 保存排序
     */
    public function saveNum()
    {
        $param = $this->request->param();
        if (!isset($param['rest_num'])) {
            return ajax_return_adv_error('缺少参数');
        }

        $model = $this->getModel();
        foreach ($param['rest_num'] as $id => $sort) {
            $model->where('id', $id)->update(['rest_num' => $sort]);
        }
        /**  */
        return ajax_return_adv('保存排序成功', 'current');
    }
    /**
     * 删除
     */
    public function delete(){
        $model = $this->getModel();
        $pk = $model->getPk();
        $ids = $this->request->param($pk);
        $where[$pk] = ["in", $ids];
        if (false === $model->where($where)->delete()) {
            return ajax_return_adv_error($model->getError());
        }

        return ajax_return_adv("删除成功");
    }
    public function test(){
        header('content-type:text/html;charset=utf-8');
        $user_id =  input('post.user_id/d');
        $type = input('post.type/d');
        $num =  input('post.num/d');
        ;//自动生成订单数目
        $now_time = 0;//当前日期
        $now_y = date('Y',time());//年
        $now_m = date('m',time());//月
        $now_d = date('d',time());//日
        $now_ym = date('Ym',time());//年月
        $now_ymd = date('Ymd',time());//年月日
        // 1018 该会员最大剩余餐数
        $goods = Db::name('goods')->where("user_id = $user_id and goods_type_id = $type ")->field('rest_num')->find();
        $max_num = $goods['rest_num'];
        if($max_num < $num){
            return returnData($max_num, '超过库存',113);
        }
        $no_ymd = [];
        // 1018 已有订单
        $no_ymd = Db::name('order')->where("user_id = $user_id and order_status in (1,2)")->column('year_months_day');
        $can_arr = $this->to($now_y,$now_m,$now_d,$num,$no_ymd);
        //return returnData($can_arr, '超过库存',114);
        // 1018 处理
        $data = Db::name('user')->where('id = ' . $user_id)->field('address,remark,name,mobile,sale_id')->find();
        $data['user_id'] = $user_id;
        $data['goods_type_id'] = $type;
        $address =  '四川省'.$data['address'];
        $address1[0] = '四川省';
        $address1[1] = substr($address,0,(stripos($address,'市') + 3));
        $address1[2] = substr($address,(stripos($address,'市') + 3),(stripos($address,'区') + 3) - (stripos($address,'市') + 3));
        $data['address1'] = json_encode($address1);
        $data['address2'] = substr($address,(stripos($address,'区') + 3));

        foreach($can_arr as $v){
            $data['sn'] = '1018'.time().random(8);
            $data['log'] = '后台生成';
            $data['create_time'] = time();
            $data['year_months_day'] = $v;
            $data['year_months'] = substr($v,0,6);
            Db::startTrans();
            try{
                //添加
                $res = Db::name('order')->insert($data);
                //减少库存
                $update['rest_num'] = ['exp','rest_num - 1'];
                $update['order_num'] = ['exp','order_num + 1'];
                $map['user_id'] = $user_id;
                $map['goods_type_id'] =  $data['goods_type_id'];
                $up = Db::name('goods')->where($map)->update($update);
                if(!$up){
                    $path = ROOT_PATH . 'public/tmp/auto_goods_error.text';
                    file_put_contents($path,Db::name('goods')->getLastSql().'<br />',FILE_APPEND);
                }
                Db::commit();
            }catch(Exception $e){
                Db::rollback();
                return returnData('', $e->getMessage(),119);
            }
        }
        return returnData('','分配成功',200);
       // $data['year_months'] = substr($year_months_day,0,6);

        //获取本月大于本日的年月日数组
        //获取本年月日期
    }
    public function to($now_y,$now_m,$now_d,$num,$no_ymd){

        $path = ROOT_PATH. 'public/tmp/day.text';
        $path = file_get_contents($path);
        $no_week = $path;
        $can_arr = [];//接受可用日期
        $date = $this->get_day($now_y,$now_m);
        $to_flag = false;
        //是否跳下页
        if($now_d < $date){
            $start =  $now_d + 1;
            for($start;$start <= $date;$start++){
                if((int)$start < 10 && (int)$start > 1){
                    $start = "0$start";
                }
                if(!in_array(($now_y.$now_m.$start),$no_ymd)){

                    if(count($can_arr) < $num){
                        if(stripos($no_week,date('w',strtotime($now_y.$now_m.$start))) === false){
                            array_push($can_arr,($now_y.$now_m.$start));
                        }
                    }
                }
            }
            if(count($can_arr) < $num){
                $to_flag = true;
            }
        }
          if($now_d == $date){
              $to_flag = true;
          }
        //月末 跳下月
        if($to_flag){
            $m = $now_m + 1;
            $y = $now_y;
            //年末 跳 下年
            if($now_m == 12){
                $y = $now_y + 1;
                $m = '01';
            }
            if((int)$m < 10 && (int)$m > 1){
                $m = "0$m";
            }
            //echo $now_y;exit;
            $date = $this->get_day($y,$m);
            $start = '01';
            for($start;$start <= $date;$start++){
                // echo $start;exit;
                if((int)$start < 10 && (int)$start > 1){
                    $start = "0$start";
                }
                if(!in_array(($y.$m.$start),$no_ymd)){
                    if(count($can_arr) < $num){
                        if(stripos($no_week,date('w',strtotime($y.$m.$start))) === false){
                            array_push($can_arr,($y.$m.$start));
                        }
                    }
                }
            }
        }
        if(count($can_arr) < $num){
            $m +=  1;
            //年末 跳 下年
            if($m == 12){
                $y +=  1;
                $m = '01';
            }
            if((int)$m < 10 && (int)$m > 1){
                $m = "0$m";
            }
            $now_num = $num - count($can_arr);
            $can_arr = array_merge($can_arr,$this->to($y,$m,'01',$now_num,$no_ymd)) ;
        }
        return $can_arr;
    }
    public function get_day( $y,$m )
    {

        $year = $y;
        $month = $m;
        if( in_array($month , array( 1 , 3 , 5 , 7 , 8 , 01 , 03 , 05 , 07 ,'08', 10 , 12)))
        {
            // $text = $year.'年的'.$month.'月有31天';
            $text = '31';
        }
        elseif( $month == 2 )
        {
            if ( $year%400 == 0 || ($year%4 == 0 && $year%100 !== 0) )    //判断是否是闰年
            {
                // $text = $year.'年的'.$month.'月有29天';
                $text = '29';
            }
            else{
                // $text = $year.'年的'.$month.'月有28天';
                $text = '28';
            }
        }
        else{
            // $text = $year.'年的'.$month.'月有30天';
            $text = '30';
        }
        return $text;
    }
	
	public function saveNumber(){
		$param = $this->request->param();
		$data = Db::name('goods')->where('id',$param['id'])->find();
		//var_dump($data);
		$map['order_status'] = 1;
		$map['user_id'] = $data['user_id'];
		$map['goods_type_id'] = $data['goods_type_id'];
		
		$order_number = Db::name('order')->where($map)->count();
		
		$order_num = $order_number;
		
		/*if($order_number > $data['order_num']){
			return returnData('','重置失败---订单数已超过下单数',302);
		}*/
		
		
		$rest_num = $data['order_num'] - $order_number + $data['rest_num'];
		
		
		$res = Db::name('goods')->where('id',$param['id'])
		->update([
			'order_num' => $order_num,
			'rest_num' => $rest_num
		]);
		
		if($res){
			return returnData('','重置成功',200);
		}
		else{
			return returnData('','重置失败',500);
		}
	}
}
