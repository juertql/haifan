<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;

class UserSale extends Controller
{
    private $user  ;
    protected static $isdelete = 0;
    public function __construct()
    {
        parent::__construct();
        $this->user = model('User');
    }
    use \app\admin\traits\controller\Controller;
    // 方法黑名单
    protected static $blacklist = [];

    protected function filter(&$map)
    {
        if ($this->request->param("name")) {
            $map['name'] = ["like", "%" . $this->request->param("name") . "%"];
        }
    }

    /** 销售 */
    public function index(){
        // 列表过滤器，生成查询Map对象
        $map = [];
        if ($this->request->param("sale_id")) {
            $sale_id =  $this->request->param("sale_id") ;
        }
        $map['isdelete'] = 0;
        // 查询字段
        $field = 'update_time';
        $listRows = 10;
        // 分页查询
        $list = $this->user
            ->field($field,true)
            ->where($map)->order('id desc')->paginate($listRows, false, ['query' => $this->request->get()]);
        // 数据处理
        /*foreach($list as &$v){

        }*/
        // 模板赋值显示
        $this->view->assign('list', $list);
        $this->view->assign("page", $list->render());
        $this->view->assign("count", $list->total());
        $this->view->assign("sale_id",  $sale_id );
        $this->view->assign('numPerPage', $listRows);
        return $this->view->fetch();
    }

    /** 新增 */
    public function add(){
        $data = input('post.');//rint_r($data);exit;
        $ids = $data['id'];
        $sale_id = $data['sale_id'];
        $update['sale_id'] = $sale_id;
        $res = Db::name('user')->where('id in('.$ids.')')->update($update);
        if(!$res){
            return returnData('','添加失败',100);
        }
        return returnData('','添加成功',200);
    }
    //删除
    public function delete(){
        $data = input('post.');//rint_r($data);exit;
        $ids = $data['id'];
        $update['sale_id'] = 0;
        $res = Db::name('user')->where('id',$ids)->update($update);
        if(!$res){
            return returnData('','删除失败',100);
        }
        return returnData('','删除成功',200);
    }
}
