<?php

return array (
  'module' => 'admin',
  'menu' => 
  array (
    0 => 'add',
    1 => 'forbid',
    2 => 'resume',
    3 => 'delete',
    4 => 'recyclebin',
    5 => 'saveorder',
  ),
  'create_config' => true,
  'controller' => 'Activity',
  'title' => '活动列表',
  'form' => 
  array (
    0 => 
    array (
      'title' => 'ID',
      'name' => 'id',
      'type' => 'text',
      'option' => '',
      'default' => '',
      'search_type' => 'text',
      'validate' => 
      array (
        'datatype' => '',
        'nullmsg' => '',
        'errormsg' => '',
      ),
    ),
    1 => 
    array (
      'title' => '标题',
      'name' => 'name',
      'type' => 'text',
      'option' => '',
      'default' => '',
      'search_type' => 'text',
      'require' => '1',
      'validate' => 
      array (
        'datatype' => '*',
        'nullmsg' => '标题必须',
        'errormsg' => '',
      ),
    ),
    2 => 
    array (
      'title' => '状态',
      'name' => 'status',
      'type' => 'radio',
      'option' => '',
      'default' => '1',
      'search_type' => 'text',
      'require' => '1',
      'validate' => 
      array (
        'datatype' => '*',
        'nullmsg' => '',
        'errormsg' => '',
      ),
    ),
  ),
  'table_engine' => 'InnoDB',
  'table_name' => '',
  'field' => 
  array (
    0 => 
    array (
      'name' => '',
      'type' => 'varchar(255)',
      'default' => 'NULL',
      'comment' => '',
      'extra' => '',
    ),
  ),
  'model' => '1',
  'auto_timestamp' => '1',
  'validate' => '1',
);
