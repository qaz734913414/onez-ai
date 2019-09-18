<?php

/* ========================================================================
 * $Id: ai.script.login_register.php 625 2016-09-20 17:28:40Z onez $
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
class onezphp_ai_script_login_register extends onezphp{
  function __construct(){
    
  }
  function menus_app(){
    $Menu=array (
      array (
        'name' => '登录接口设置',
        'href' => '/login.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
      array (
        'name' => '注册接口设置',
        'href' => '/register.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
      array (
        'name' => '取回密码接口设置',
        'href' => '/findpwd.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
    );
    return $Menu;
  }
}