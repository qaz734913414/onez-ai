<?php

/* ========================================================================
 * $Id: ai.function.noreply.php 718 2016-09-20 14:22:50Z onez $
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
class onezphp_ai_function_noreply extends onezphp{
  function __construct(){
    
  }
  function parse($data,$person,&$output){
    $message=$data['message'];
    $noreply_content=onez('cache')->option('noreply_content',0);
    if($noreply_content){
      $output[]=array(
        'sender'=>'ai',
        'type'=>'text',
        'message'=>$noreply_content,
      );
    }
    if(!$data['auto'] || $message!='hello'){
      $noreply_app=onez('cache')->option('noreply_app',0);
      if($noreply_app && onez()->exists($noreply_app)){
        $r=onez($noreply_app)->request($message,$person['id']);
        if($r){
          $output[]=$r;
        }
      }
    }
  }
}