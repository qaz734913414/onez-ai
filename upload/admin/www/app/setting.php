<?php

/* ========================================================================
 * $Id: setting.php 1743 2016-09-20 23:16:30Z onez $
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


function _href($method){
  global $G;
  $method=str_replace('?','&',$method);
  return onez()->href('/app/setting.php?appid='.$G['appid'].'&miniwin=1&_mod_child='.$method);
}

$appid=$G['appid']=(int)onez()->gp('appid');
$_mod_child=onez()->gp('_mod_child');

$app=onez('db')->open('app')->one("appid='$appid'");

$G['title']=$app['appname'];

$myapp=onez($app['apptoken']);
$mymenus=array();
if(method_exists($myapp,'menus_app')){
  $menus=$myapp->menus_app();
  foreach($menus as $V){
    $url='';
    if($V['href']){
      $url=_href($V['href']);
    }elseif($V['url']){
      $url=$V['url'];
    }else{
      continue;
    }
    list($f)=explode('?',$V['href']);
    $V['url']=$url;
    $V['token']=$f;
    $mymenus[]=$V;
  }
}
if(!$mymenus){
  onez('showmessage')->error('此应用没有设置选项');
}
if(!$_mod_child){
  $m=current($mymenus);
  parse_str($m['href'],$info);
  $key=key($info);
  if($key){
    unset($info[$key]);
    $_mod_child=str_replace('_','.',$key);
  }
  foreach($info as $k=>$v){
    $_REQUEST[$k]=$v;
  }
}
$_mod_child=str_replace('../','',$_mod_child);

onez('admin')->header();
?>
<section class="content">
  <div class="btns" style="padding-bottom: 10px">
      <?
      if(count($mymenus)>1){
        
      
        foreach($mymenus as $V){
          $s='btn-default';
          if($_mod_child==$V['token'] || $_mod_child==CUR_URL){
            $s='btn-success';
          }
          ?>
  <a href="<?php echo $V['url']?>" class="btn <?=$s?>">
    <?=$V['name']?>
  </a>
      
      <?}}?>
  </div>
  <?
  $appFile=$myapp->path.'/www/'.$_mod_child;
  file_exists($appFile) && include_once($appFile);
  ?>
</section>
<?php
onez('admin')->footer();
?>