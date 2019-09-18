<?php

/* ========================================================================
 * $Id: ai.attr.php 534 2016-09-18 08:15:24Z onez $
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
class onezphp_ai_attr extends onezphp{
  function __construct(){
    
  }
  function doit(&$result,$v,$person_id=0){
    if($v['attrname']){
      $attr=onez('db')->open('attrs')->one("subject='$v[attrname]'");
    }elseif($v['attrid']){
      $attr=onez('db')->open('attrs')->one("attrid='$v[attrid]'");
    }else{
      return;
    }
    list($atoken)=explode('|',$attr['type']);
    $v['attr']=$attr;
    onez($atoken)->doit($result,$v,onez('ai.person')->init($person_id));
  }
}