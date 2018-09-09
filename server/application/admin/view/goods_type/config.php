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
  'controller' => 'GoodsType',
  'title' => '餐型管理',
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
      'title' => '名字',
      'name' => 'name',
      'type' => 'text',
      'option' => '',
      'default' => '',
      'search' => '1',
      'search_type' => 'text',
      'require' => '1',
      'validate' => 
      array (
        'datatype' => '*',
        'nullmsg' => '',
        'errormsg' => '',
      ),
    ),
    2 => 
    array (
      'title' => '价格',
      'name' => 'price',
      'type' => 'text',
      'option' => '',
      'default' => '',
      'search_type' => 'text',
      'require' => '1',
      'validate' => 
      array (
        'datatype' => '*',
        'nullmsg' => '',
        'errormsg' => '',
      ),
    ),
    3 => 
    array (
      'title' => '说明',
      'name' => 'title',
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
    4 => 
    array (
      'title' => '状态',
      'name' => 'status',
      'type' => 'radio',
      'option' => '',
      'default' => '1',
      'search_type' => 'text',
      'validate' => 
      array (
        'datatype' => '',
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
