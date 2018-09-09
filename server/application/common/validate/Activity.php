<?php
namespace app\common\validate;

use think\Validate;

class Activity extends Validate
{
    protected $rule = [
        "name|标题" => "require",
        "status|状态" => "require",
    ];
}
