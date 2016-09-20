<?php

/* ========================================================================
 * $Id: ai.ui.custom.php 838 2016-09-20 17:12:58Z onez $
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
class onezphp_ai_ui_custom extends onezphp{
  function __construct(){
    
  }
  function init(){
    global $G;
    if(strpos($_GET['_view'],'ai.device.dialog/dialog')===false){
      return;
    }
    $G['dialog.header']=htmlspecialchars_decode(onez('cache')->option('ai_ui_custom_header',0));
    $G['dialog.left']=htmlspecialchars_decode(onez('cache')->option('ai_ui_custom_left',0));
    $G['dialog.right']=htmlspecialchars_decode(onez('cache')->option('ai_ui_custom_right',0));
    $G['dialog.footer']=htmlspecialchars_decode(onez('cache')->option('ai_ui_custom_footer',0));
  }
  function menus_app(){
    $Menu=array (
      array (
        'name' => '界面设置',
        'href' => '/setting.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
    );
    return $Menu;
  }
}