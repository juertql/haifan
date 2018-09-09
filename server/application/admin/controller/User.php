<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);
load_trait('controller/Jump');
use app\admin\Controller;
use think\Db;

class User extends Controller
{
    use \traits\controller\Jump;
    private $user  ;
    protected static $isdelete = 0;
    public function __construct()
    {
        parent::__construct();
        $this->user = model('User');
        /** 销售 */
        $sale_map['is_sale'] = 1;
        $sale_map['isdelete'] = 0;
        $sale_map['status'] = 1;
        $sale = Db::name('admin_user')->where($sale_map)->field('id,realname')->select();
        /** 餐型 */
        $goods_map['isdelete'] = 0;
        $goods_map['status'] = 1;
        $goods = Db::name('goods_type')->where($goods_map)->field('id,name,spec')->select();
        /** @var  sale */
        $this->view-> sale = $sale;
        /** @var  good */
        $this->view-> goods = $goods;
    }
    use \app\admin\traits\controller\Controller;
    // 方法黑名单
    protected static $blacklist = [];

    protected function filter(&$map)
    {   /**  */
        $sale_id = $this->request->param("sale_id");
        if ($sale_id != '') {
            $map['sale_id'] = $this->request->param("sale_id") ;
        }
        $is_sale = session('is_sale');
        if($is_sale === 1){
            $map['sale_id']   = session(config('rbac.user_auth_key'));
        }
        if ($this->request->param("name")) {
            $map['name'] = ["like", "%" . $this->request->param("name") . "%"];
        }
        //print_r($map);exit;
    }

    //新增会员 不需手机验证

    public function add(){
        //提交
        if($this->request->isPost()){
            $data = input('post.');
            $password = $data['password'];
            $repassword = $data['repassword'];
            if(strlen($password) < 6){
                return ajax_return_adv_error('密码至少6位');
            }
            if($password != $repassword){
                return ajax_return_adv_error('密码不一致');
            }
            $validate = validate('User');
            if(!$validate->check($data)){
                return ajax_return_adv_error($validate->getError());
            }
            else{
                unset($data['repassword']);
                $data['encrypt'] = random(7);//token
                $data['password'] = md5(md5($data['password']).$data['encrypt']);//加密
                $this->user->save($data);
                if($this){
                    return ajax_return_adv('添加成功');
                }else{
                    return ajax_return_adv_error($this->user->getError());
                }
            };
        }
        else{

            return $this->view->fetch('edit');
        }
    }

    // 1018 修改密码
    public function password(){
        if($this->request->isPost()){
            $id = input('post.id/d');
            $data = input('post.');
            $password = $data['password'];
            $repassword = $data['repassword'];
            if(strlen($password) < 6){
                return ajax_return_adv_error('密码至少6位');
            }
            if($password != $repassword){
                return ajax_return_adv_error('密码不一致');
            }
            // token
            $user = Db::name('user')->where('id',$id)->find();
            $encrypt = $user['encrypt'];
            $new_password = md5(md5($password).$encrypt);
            $update['password'] = $new_password;
            //print_r($new_password);exit;
            $res = Db::name('user')->where('id',$id)->update($update);
            if($res){
                return ajax_return_adv('修改成功');
            }else{
                return ajax_return_adv_error($this->user->getError());
            }

        }
        else{
            $id = input('param.id/d');
            $this->view->id = $id;
            return $this->view->fetch();
        }
    }
    /** → 会员导入  ← */

    public function importUser()
    {
        /** 销售不能 */
        $sale_id = session('sale_id');
        if($sale_id != 0){
            return returnData('','无权限',105);
        }
        $file = $this->request->file('file');
        $path = ROOT_PATH . 'public/tmp/uploads/user/';
        $info = $file->move($path);
        if (!$info) {
            return returnData($file->getError(),'导入失败',103);
        }
        $data = 'public/tmp/uploads/user/' . $info->getSaveName();
        //$data = $this->request->root() . '/tmp/uploads/' . $info->getSaveName();
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
        $file = ROOT_PATH .$data;
        $sheet=0;
        $header = [];
        $list = importCG($file,$sheet,$header);
        if(count($list) < 2){
            return returnData('','导入失败',102);
        }
        $arr = [
          '会员名'=>'name'  ,
          '电话'=>'mobile'  ,
          '所属销售' => 'sale_id',
          '送餐地址'=>'address'  ,
          '备注'=>'remark'  ,
          '特殊说明'=>'special'  ,
          '已用'=>'use_food_num',
          '剩余'=>'rest_food_num', 
          '餐类'=>'default_goods_id',
        ];
        $arr1 = [];
        $arr2 = [];
        $arr3 = [];
        /** 处理数据 */
        foreach($list[1] as $c => $b){
            $arr1[$c] = $arr[$b];
        }
         unset($list[1]);
        foreach($list as $k => &$v){
             foreach($v as $q => $a){
                $arr2[$arr1[$q]] = $a;
            }
             $arr3[] = $arr2;
        }
       /** 新增 */
        $validate = validate('User');
        $tag = false;
        $par = false;

        foreach($arr3 as &$v){

            /** 截取密码 */
            $password = substr($v['mobile'],strlen($v['mobile'])-6,6);
            $v['encrypt'] = random(6);//token
            $v['password'] = md5(md5($password).$v['encrypt']);//加密

            if(!$validate->check($v)){
                $par = true;
            }
            else{
                //DebugLog(array2str($v));
                $sale_id = Db::name('admin_user')->where('realname',$v['sale_id'])->value('id');
                if($sale_id){
                    
                    //DebugLog("name:".$v['sale_id']." id:".$sale_id);
                    $v['sale_id'] = $sale_id;
                }
                $v['create_time'] = time(); 
                $res = $this->user->insert($v);
                if($res){
                    $tag  = true;
                    //update goods table
                    $user_id = Db::name("user")->where('mobile',$v['mobile'])->value('id');
                    if($user_id)
                    {
                        $goods_info['goods_type_id'] = $v['default_goods_id'];
                        $goods_info['user_id'] = $user_id;
                        $goods_info['rest_num'] = $v['rest_food_num'];
                        $goods_info['use_num'] = $v['use_food_num'];
                        $goods_info['create_time'] = time();
                        $goods_info['update_time'] = time();
                        $goods_info['order_num'] = 0;
                        $goods_info['isdelete'] = 0;
                        $res = Db::name("goods")->insert($goods_info);
                    }
                }
            }
        }
        //部分成功
        if($par === true && $tag === true){
            return returnData('','部分导入成功',200);
        }
        else if($par === false && $tag === true){
            return returnData('','导入成功',200);
        }
        else{
            return returnData('','导入失败',300);
        }
    }
    public function getOrderCount($user_id){
        if((int)$user_id < 0){
            return false;
        }
        $map['user_id'] = $user_id;
        $rest =  Db::name('goods')->where($map)->sum('rest_num');
        $data['rest'] = ($rest) ? $rest : 0;
        $use =  Db::name('goods')->where($map)->sum('use_num');
        $data['use'] =  $use ? $use : 0;
        $order =  Db::name('goods')->where($map)->sum('order_num');
        $data['order'] =  $order ? $order : 0;
        $data['total'] = (int) $data['use'] + (int)$data['rest'] + (int) $data['order'];
        return $data;
    }
    /**
     * 导出
     * exportOrder
     */

    public function exportUser(){

        $is_sale = session('is_sale');
        $sale_id = $this->request->param("sale_id");
        if ($sale_id != '') {
            $map['sale_id'] = $this->request->param("sale_id") ;
        }
        if($is_sale === 1){
            $map['sale_id']   = session(config('rbac.user_auth_key'));
        }

        /** 获取数据  */
        $map['isdelete'] = 0;
        ;
        $data = Db::name('user')->where($map)->field('name,mobile,sale_id,address,remark,id')->select();

        if(count($data) < 1){
            $this->error('无导出数据');
        }
        /** 处理数据 */
        foreach($data as $k => &$v){
                $sale_name = get_sale($v['sale_id']);
            $v['sale_id'] = $sale_name;
            $count = $this->getOrderCount($v['id']);
            $v['total'] = $count['total'];
            $v['order'] = $count['order'];
            $v['use'] = $count['use'];
            $v['rest'] = $count['rest'];
            $v['order_rest'] = (int)$count['order'] + (int) $count['rest'];
            unset($v['id']);
        }
        $header = [ '会员名', '电话','所属销售', '送餐地址', '备注','总餐','在用','已用','剩余','合计'];
        $name =  date('Y-m-d').'-嗨范会员' ;
        if ($error = \Excel::export($header, $data, $name, '2007')) {
            throw new Exception($error);
        }
    }
	/*public function aaa(){
		var_dump($user_id);die;
		$map['user_id'] = ['in','26,29'];
		$data = Db::name('goods')->where($map)->select();
		foreach($data as $key => $value){
			$n = (int)($value['order_num'] - 2);
			if ($n<0){
			exit;
			}
			else{
				$res = Db::name('goods')->where($map)->update(['order_num' => $n]);
				
				
			
			}
		}
		
		var_dump($data);die;
	}*/
}
