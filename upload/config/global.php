<?php

/* ========================================================================
 * $Id: global.php 881 2016-09-21 01:58:28Z onez $
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
#是否已安装成功

if(onez()->exists('cache')){
  $options=onez('cache')->get('options');
  if($options['is_dbinit']){
    $G['mode']=$options['mode'];
    $cacheFile=ONEZ_ROOT.'/cache/apps/list.php';
    if(!file_exists($cacheFile)){
      $apps=array();
      $record=onez('db')->open('app')->record("enabled=1");
      foreach($record as $rs){
        $apps[$rs['apptype']][$rs['apptoken']]=$rs;
      }
      onez()->write($cacheFile,"<?php
!defined('IN_ONEZ') && exit('Access Denied');
return ".var_export($apps,1).";");
    }
    $G['apps']=include($cacheFile);
    !$G['apps'] && $G['apps']=array();
    foreach($G['apps'] as $apptype=>$apps){
      foreach($apps as $apptoken=>$app){
        if(onez()->exists($apptoken)){
          onez($apptoken)->init();
        }
      }
    }
  }
}