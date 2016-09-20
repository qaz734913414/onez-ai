<?php

/* ========================================================================
 * $Id: tuling123.php 1163 2016-09-14 07:02:54Z onez $
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
class onezphp_tuling123 extends onezphp{
  function __construct(){
    
  }
  function options(){
    $options=array();
    $options['tuling123_appurl']=array('label'=>'图灵API地址','type'=>'text','key'=>'tuling123_appurl','hint'=>'http://www.tuling123.com/openapi/api','notempty'=>'');
    $options['tuling123_appkey']=array('label'=>'图灵APIkey','type'=>'text','key'=>'tuling123_appkey','hint'=>'','notempty'=>'');
    return $options;
  }
  function request($text,$userid=0){
    $options['tuling123_appkey']=onez('cache')->option('tuling123_appkey',0);
    if(!$options['tuling123_appkey']){
      return '';
    }
    $options['tuling123_appurl']=onez('cache')->option('tuling123_appurl',0);
    !$options['tuling123_appurl'] && $options['tuling123_appurl']='http://www.tuling123.com/openapi/api';
    $post=array(
      'key'=>$options['tuling123_appkey'],
      'info'=>$text,
      'loc'=>'',
      'userid'=>$userid,
    );
    $mydata=onez()->post($options['tuling123_appurl'],http_build_query($post),array(
      'timeout'=>600,
    ));
    $json=json_decode($mydata,1);
    return $json;
  }
}