<?php
namespace app\common\validate;

use think\Validate;

class UserBody extends Validate
{
    protected $rule = [
        "ym|年月" => "require",
        "height|身高" => "require",
        "weight|体重" => "require",
    ];
}
