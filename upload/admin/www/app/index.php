<?php

/* ========================================================================
 * $Id: index.php 4239 2016-09-20 11:00:28Z onez $
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
$G['title']='应用中心';
define('CUR_URL','/app/index.php');
$action=onez()->gp('action');
if($action=='delete'){
  $id=(int)onez()->gp('id');
  onez('db')->open('app')->delete("appid='$id'");
  
  $appid=onez('cache')->option('onez_appid',0);
  $get=array();
  $get['goto']=onez()->cururl();
  if($appid){
    $appkey=onez('cache')->option('onez_appkey',0);
    $get['id']=$id;
    $get['appid']=$appid;
    $get['nonceStr']=uniqid();
    $get['timestamp']=time();
    $get['hash']=md5(md5($appid.$get['goto'].$get['nonceStr'].$get['timestamp']).$appkey);
  }
  $openurl='http://ai.open.onez.cn/uninstall.php?'.http_build_query($get);
  onez()->post($openurl);
  
  @unlink(ONEZ_ROOT.'/cache/apps/list.php');
  onez()->ok('删除应用','reload');
}elseif($action=='enabled'){
  $id=(int)onez()->gp('appid');
  $value=(int)onez()->gp('value');
  onez('db')->open('app')->update(array('enabled'=>$value),"appid='$id'");
  @unlink(ONEZ_ROOT.'/cache/apps/list.php');
  onez()->ok('操作应用','reload');
}
onez('admin')->header();
function _item($rs){
  $html=array();
  
  $html[]='<li class="item">';
    $html[]='<div class="product-img">';
      $html[]='<img src="'.$rs['appicon'].'" alt="'.$rs['appname'].'">';
    $html[]='</div>';
    $html[]='<div class="product-info">';
        $html[]='<a href="'.onez()->href('/app/setting.php?appid='.$rs['appid']).'" class="product-title onez-miniwin">';
          $html[]='<span class="label bg-'.$rs['style'].'">'.$rs['typename'].'</span>';
          $html[]=$rs['appname'];
        $html[]='</a>';
        $html[]='<div class="pull-right">';
        if($rs['enabled']){
          $html[]='<a href="javascript:;" onclick="_enabled('.$rs['appid'].',0)">停用</a>';
        }else{
          $html[]='<a href="javascript:;" onclick="_enabled('.$rs['appid'].',1)">启用</a>';
        }
        $html[]=' | <a href="javascript:;" onclick="onez.del('.$rs['appid'].')">删除</a>';
        $html[]='</div>';
      $html[]='<span class="product-description">';
        $html[]=$rs['summary'];
      $html[]='</span>';
    $html[]='</div>';
  $html[]='</li>';
  
  return implode("\n",$html);
}
?>
<section class="content-header">
  <h1>
    应用中心
  </h1>
  <ol class="breadcrumb">
    <li>
      <a href="<?php echo onez()->href('/')?>">
        <i class="fa fa-dashboard">
        </i>
        管理首页
      </a>
    </li>
    <li class="active">
      应用中心
    </li>
  </ol>
</section>
<?
$appid=onez('cache')->option('onez_appid',0);
$get=array();
$get['goto']=onez()->cururl();
if($appid){
  $appkey=onez('cache')->option('onez_appkey',0);
  $get['appid']=$appid;
  $get['nonceStr']=uniqid();
  $get['timestamp']=time();
  $get['hash']=md5(md5($appid.$get['goto'].$get['nonceStr'].$get['timestamp']).$appkey);
}
$openurl='http://ai.open.onez.cn/index.php?'.http_build_query($get);
?>
<section class="content">
  <div class="btns" style="padding-bottom: 10px">
    <a href="<?=$openurl?>" class="btn btn-success onez-miniwin">
      获取更多应用
    </a>
  </div>
  <!--已开启的应用-->
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">
        已开启的应用
      </h3>
      <div class="box-tools pull-right">
      </div>
    </div>
    <div class="box-body">
      <ul class="products-list product-list-in-box">
<?
$record=onez('db')->open('app')->record("enabled=1");
foreach($record as $rs){
  echo _item($rs);
}?>
              </ul>
    </div>
  </div>
  <!--未开启的应用-->
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">
        未开启的应用
      </h3>
      <div class="box-tools pull-right">
      </div>
    </div>
    <div class="box-body">
      <ul class="products-list product-list-in-box">
<?
$record=onez('db')->open('app')->record("enabled=0");
foreach($record as $rs){
  echo _item($rs);
}?>
              </ul>
    </div>
  </div>
  
  
  
</section>
<script type="text/javascript">
function _enabled(appid,s){
  $.post(window.location.href,{action:'enabled',appid:appid,value:s},function(){
    location.reload();
  });
}
</script>
<?php
onez('admin')->footer();
?>