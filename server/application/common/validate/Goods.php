<?php
namespace app\common\validate;

use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        /*"user_id|会员" => "require",*/
        "rest_num|剩余餐数" => "require",
        "order_num|已用餐数" => "require",
    ];
}
