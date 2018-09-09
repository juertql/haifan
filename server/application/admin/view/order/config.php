<?php

return array (
  'module' => 'admin',
  'menu' => 
  array (
    0 => 'delete',
  ),
  'create_config' => true,
  'controller' => 'Order',
  'title' => '选餐管理',
  'form' => 
  array (
    0 => 
    array (
      'title' => '订单号',
      'name' => 'sn',
      'type' => 'text',
      'option' => '',
      'default' => '',
      'search' => '1',
      'search_type' => 'text',
      'validate' => 
      array (
        'datatype' => '',
        'nullmsg' => '',
        'errormsg' => '',
      ),
    ),
    2 => 
    array (
      'title' => '名字',
      'name' => 'name',
      'type' => 'text',
      'option' => '',
      'default' => '',
      'search' => '1',
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
      'title' => '状态',
      'name' => 'order_status',
      'type' => 'select',
      'option' => '',
      'default' => '1',
      'search' => '1',
      'search_type' => 'select',
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
);
