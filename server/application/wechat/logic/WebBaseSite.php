<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 19:11
 */

namespace app\wechat\logic;


use think\Model;

class WebBaseSite extends Model
{
    protected $trueTableName = 'tp_web_base_site';

    /**
     *
     * @param $key
     * @return bool|mixed
     */
    public function getValue($key){
     if(!$key){
         $this->error = '参数错误';return false;
     }
        $data = $this->where("name = '$key'")->value('value');
        if(!$data){
            $this->error = $this->getError(); return false;
        }
        return $data;
    }
}