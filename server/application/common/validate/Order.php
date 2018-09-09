<?php
namespace app\common\validate;

use think\Validate;

class Order extends Validate
{
    protected $rule = [
       "sn|定单号" => "require|unique:order",
        "user_id|会员" => "require",
        "address|地址 " => "require",
        "goods_type_id|分类 " => "require",
        "year_months_day|日期 " => "require",
        "year_months|日期 " => "require",
       "sale_id|销售 " => "require",
       "name|名字 " => "require",
       "mobile|电话 " => "require",


        /*"sale_id|所属销售" => "require",*/
        //"status|状态" => "require",
    ];
    protected $msg = [
        'sn.unique' => '订单号唯一',
    ];
    protected $scene = [
      'edit' => [
          'user_id',
          'address',
          'mobile',
          'name',
          'goods_type_id',
          'give_food_time',
          'year_months_day',
      ]
    ];
}
