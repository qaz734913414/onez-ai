<?php

/* ========================================================================
 * $Id: menu.inc.php 1009 2016-09-20 09:23:40Z onez $
 * http://ai.onez.cn/
 * Email: www@onez.cn
 * QQ: 6200103
 * ========================================================================
 * Copyright 2016-2016 佳蓝科技.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */


!defined('IN_ONEZ') && exit('Access Denied');
$Menu=array (
  array (
    'name' => '网站基本设置',
    'href' => '',
    'icon' => '',
  ),
  array (
    'name' => '网站参数设置',
    'url' => onez()->href('/options.php'),
    'icon' => '',
  ),
  array (
    'name' => '设置管理账号和密码',
    'url' => onez()->href('/setpwd.php'),
    'icon' => '',
  ),
  array (
    'name' => '数据表安装与升级',
    'url' => onez()->href('/dbtables.php'),
    'icon' => '',
  ),
  array (
    'name' => '插件更新检测',
    'url' => onez()->href('/plugins.php'),
    'icon' => '',
  ),
  array (
    'name' => '更新网站至最新版',
    'url' => onez()->href('/upgrade.php'),
    'icon' => '',
  ),
);
$hasDemo=0;
foreach(_get_all_plugins() as $ptoken){
  if(method_exists(onez($ptoken),'demo')){
    $hasDemo=1;
    break;
  }
}
if($hasDemo){
  $Menu[]=array (
    'name' => '查看演示',
    'url' => onez()->href('/demos.php'),
    'icon' => '',
  );
}

return $Menu;

