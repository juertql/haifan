<?php
namespace app\common\validate;

use think\Validate;

class Gift extends Validate
{
    protected $rule = [
        "name|名字" => "require",
        "type|类型" => "require",
        "total_num|库存" => "require",
        "chance|中奖概率" => "require",
        "num|数量" => "require",
    ];
}
