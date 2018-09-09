<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;

class UserBody extends Controller
{
    private $model  ;
    public function __construct()
    {
        parent::__construct();
        $this->model = model('UserBody');
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
        //print_r($list);exit;
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
            $ym = str_replace('-','',$data['ym']);
            //判断是否有
            $map_ym['user_id'] = $data['user_id'];
            $map_ym['ym'] = $ym;
            $body = $this->model->where($map_ym)->find();
            if($body){
                return ajax_return_adv_error('当月已有');
            }
            $data['ym'] = $ym;
            $validate = validate('UserBody');
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

            $map['user_id'] =  $this->request->param("user_id") ;
            $this->view->user_id = $map['user_id'];
            return $this->view->fetch('edit');
        }
    }
    /** 编辑 */
    public function edit(){
        if($this->request->isPost()){
            $data = input('post.');//return $data;
            $ym = str_replace('-','',$data['ym']);
            //判断是否有
            $map_ym['user_id'] = $data['user_id'];
            $map_ym['ym'] = $ym;
            $body = $this->model->where($map_ym)->find();
            if($body){
                return ajax_return_adv_error('当月已有');
            }
            $data['ym'] = $ym;
            unset($data['user_id']);
            $validate = validate('UserBody');
            if(!$validate->check($data)){
                return ajax_return_adv_error($validate->getError());
            }
            else{
                $this->model->where('id = '. $data['id'])->update($data);
                if($this){
                    return ajax_return_adv('添加成功');
                }else{
                    return ajax_return_adv_error($this->model->getError());
                }
            };
        }
        else{
            $map['user_id'] =  $this->request->param("user_id");
            $id =  $this->request->param("id");
            $vo = $this->model->find($id);
            $this->view->user_id = $map['user_id'];
            $this->view->vo = $vo;
            return $this->view->fetch('edit');
        }
    }
}
