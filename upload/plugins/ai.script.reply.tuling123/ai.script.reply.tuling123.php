<?php

/* ========================================================================
 * $Id: ai.script.reply.tuling123.php 674 2016-09-20 14:56:06Z onez $
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
class onezphp_ai_script_reply_tuling123 extends onezphp{
  function __construct(){
  }
  function menus_app(){
    $Menu=array (
      array (
        'name' => '基本设置',
        'href' => '/setting.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
    );
    return $Menu;
  }
  function request($text,$pid){
    $answer=onez('tuling123')->request($text,$pid);
    
    $text=(string)$answer['text'];
    if(!$text){
      return;
    }
    !$text && $text='很抱歉，系统维护中，请稍候重试！';
    
    return array(
      'sender'=>'ai',
      'type'=>'text',
      'message'=>$text,
    );
  }
}