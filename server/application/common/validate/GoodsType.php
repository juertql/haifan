<?php
namespace app\common\validate;

use think\Validate;

class GoodsType extends Validate
{
    protected $rule = [
        "name|名字" => "require",
        "spec|规格" => "require",
        "price|价格" => "require",
    ];
}
