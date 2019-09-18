<?php

/* ========================================================================
 * $Id: ai.script.ask.php 1576 2016-09-20 22:24:42Z onez $
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
class onezphp_ai_script_ask extends onezphp{
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
  function init(){
    global $G;
    if(strpos($_GET['_view'],'ai.device.dialog/dialog')===false){
      return;
    }
    $ai_script_ask_words=onez('cache')->option('ai_script_ask_words',0);
    $words=array();
    foreach(explode("\n",$ai_script_ask_words) as $v){
      $v=trim($v);
      if($v){
        $words[]=$v;
      }
    }
    if(!$words){
      return;
    }
    $ai_script_ask_words=json_encode($words);
    
    $ai_script_ask_first=(int)onez('cache')->option('ai_script_ask_first',0);
    !$ai_script_ask_first && $ai_script_ask_first=1;
    $ai_script_ask_sec=(int)onez('cache')->option('ai_script_ask_sec',0);
    !$ai_script_ask_sec && $ai_script_ask_sec=20;
    
    $ai_script_ask_repeat=(int)onez('cache')->option('ai_script_ask_repeat',0)?'true':'false';
    $ai_script_ask_rand=(int)onez('cache')->option('ai_script_ask_rand',0)?'true':'false';
    $G['footer'].=<<<ONEZ
<script type="text/javascript">
var ai_script_ask_first=$ai_script_ask_first;
var ai_script_ask_sec=$ai_script_ask_sec;
var ai_script_ask_words=$ai_script_ask_words;
var ai_script_ask_repeat=$ai_script_ask_repeat;
var ai_script_ask_rand=$ai_script_ask_rand;
</script>
ONEZ;
    $G['footer'].=onez('ui')->js($this->url.'/js/ai.script.ask.js');
  }
}