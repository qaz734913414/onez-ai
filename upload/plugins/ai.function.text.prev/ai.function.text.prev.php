<?php

/* ========================================================================
 * $Id: ai.function.text.prev.php 1142 2016-09-20 11:47:54Z onez $
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
class onezphp_ai_function_text_prev extends onezphp{
  function __construct(){
    
  }
  function parse(&$message){
    $text_prev_del=onez('cache')->option('text_prev_del',0);
    $text_prev_replace=onez('cache')->option('text_prev_replace',0);
    $text_prev_preg=onez('cache')->option('text_prev_preg',0);
    #删除多余字符
    if($text_prev_del){
      foreach(explode("\n",$text_prev_del) as $v){
        $v=trim($v);
        if($v){
          $message=str_replace($v,'',$message);
        }
      }
    }
    #替换
    if($text_prev_replace){
      foreach(explode("\n",$text_prev_replace) as $v){
        $v=trim($v);
        if($v){
          list($a,$b)=explode('=',$v);
          if($a && $b){
            $message=str_replace($a,$b,$message);
          }
        }
      }
    }
    #正则
    if($text_prev_preg){
      list($A,$B)=explode("\n",$text_prev_replace);
      if($A && $B){
        $message_old=(string)$message;
        $message=@preg_replace($A,$B,$message);
        if(!$message){
          $message=$message_old;
        }
      }
    }
    
    
  }
}