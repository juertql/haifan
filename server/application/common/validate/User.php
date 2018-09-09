<?php
namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        "name|会员名" => "require|unique:user",
        "mobile|手机号" => "require|unique:user",
        "address|送餐地址 " => "require",
        /*"sale_id|所属销售" => "require",*/
        //"status|状态" => "require",
    ];
    protected $msg = [
        'name.unique' => '会员名唯一',
        'mobile.unique' => '手机号唯一',
    ];
}
