<?php

/* ========================================================================
 * $Id: index.php 6521 2016-09-20 23:19:34Z onez $
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
#当前页地址（导航栏是否选中，和menu.inc.php中的href值一致）
define('CUR_URL','index.php');
$action=onez()->gp('action');
if($action=='mode'){
  $mode=onez()->gp('mode');
  onez($mode);
  $T=onez('db')->open('app')->one("apptype='mode' and enabled=1 and apptoken='$mode' order by appid desc");
  !$T && onez('showmessage')->error('应用不存在或已被删除，请检查',onez()->href('/index.php'));
  onez('cache')->option_set(array('mode'=>$mode));
  onez('showmessage')->success('恭喜！启用'.$T['appname'].'成功',onez()->href('/index.php'));
}

$G['title']='管理首页';
#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;

onez('admin')->header();
onez('admin')->widget('header')
  ->set('title',$G['title'])
  ->show();
echo onez('ui')->css('images/style.css');


$device=onez('db')->open('devices')->one("device_token like 'ai.device.dialog%'");
?>
<section class="content">
  <div class="row">
        <div class="col-md-4">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?=onez('db')->open('person')->rows("deviceid='{$device['deviceid']}'")?> <sup style="font-size: 20px">人</sup></h3>

              <p>前端访客地址</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <?if($device){?>
            <a href="<?=onez('ai.device.dialog')->view('dialog&deviceid='.$device['deviceid'])?>" class="small-box-footer" target="_blank">点击打开 <i class="fa fa-arrow-circle-right"></i></a>
            <?}else{?>
            <a href="<?=onez()->href('/devices/index.php')?>" class="small-box-footer">请先添加终端 <i class="fa fa-arrow-circle-right"></i></a>
            <?}?>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-md-4">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?=onez('db')->open('app')->rows("enabled=1")?> <sup style="font-size: 20px">个</sup></h3>

              <p>应用中心</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="<?=onez()->href('/app/index.php')?>" class="small-box-footer">点击打开 <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-md-4">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>--- <sup style="font-size: 20px"></sup></h3>

              <p>官方论坛</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="http://ai.bbs.onez.cn" target="_blank" class="small-box-footer">点击打开 <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      
    <div class="box box-danger">
      <div class="box-body">
        <code>新版完善中，如有兴趣欢迎加入讨论...</code>
      </div>
    </div>
    
    
  <div class="row">
  
    <div class="col-md-8">
      <div class="box box-info">
        <div class="box-header ">
          <h3 class="box-title">模式选择</h3>
          <div class="box-tools pull-right">
            <a href="<?=onez()->href('/app/index.php')?>">查看更多模式</a>
          </div>
        </div>
        <div class="box-body">
<?
$mode=onez('cache')->option('mode',0);
$T=onez('db')->open('app')->record("apptype='mode' and enabled=1 order by apptoken='$mode' desc,appid desc",7);
if(!$T){
  echo '<a href="'.onez()->href('/app/index.php').'"><h3 class="text-center text-blue">请先从应用中心》获取更多应用安装您喜欢的模式</h3></a>';
}
?>

<ul class="products-list product-list-in-box">

<?
foreach($T as $rs){
?>
                <li class="item">
                  <div class="product-img">
                    <img src="<?=$rs['appicon']?>" style="width:50px;height:50px" alt="Product Image">
                  </div>
                  <div class="product-info">
                    <span class="product-title"><?=$rs['appname']?>
                    <?if($rs['apptoken']==$mode){?>
                      <span class="label label-warning pull-right">当前</span>
                    <?}else{?>
                      <a href="<?=onez()->href('/index.php?action=mode&mode='.$rs['apptoken'])?>" class="label bg-gray pull-right">点击开启</a>
                    <?}?>
                    </span>
                        <span class="product-description">
                          <?=$rs['summary']?>
                        </span>
                  </div>
                </li>
<?}?>
              </ul>


        </div>
      </div>
    </div>
  
    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header ">
          <h3 class="box-title">联系方式</h3>
        </div>
        <div class="box-body">
<ul class="nav nav-pills nav-stacked">
  <li><a href="http://shang.qq.com/wpa/qunwpa?idkey=f7860dfb3e265264f9f359285de578f4fede15f6e8ef183614cdd11645922463" title="佳蓝人工智能开源框架" target="_blank">QQ交流群:<span class="pull-right text-red"><img border="0" src="http://pub.idqqimg.com/wpa/images/group.png" alt="佳蓝人工智能开源框架"></span></a></li>
  <li class="qrcode"><a href="javascript:;">微信公众号:<span class="pull-right text-black"><i class="fa fa-qrcode"></i> czonez</span></a>
  <img src="images/wx-czonez.jpg" style="width:290px;height:290px" />
  </li>
  
  <li><a href="http://ai.bbs.onez.cn/" target="_blank">论坛交流:<span class="pull-right text-black">点此访问</span></a></li>
</ul>
        </div>
      </div>
      
      <div class="box box-info">
        <div class="box-header ">
          <h3 class="box-title">最近更新</h3>
        </div>
<style>
.bbs ul{
  margin:0;
  padding: 0;
  list-style: none;
}
.bbs li{
  line-height: 2;
}
</style>
<div class="box-body bbs">
<script type="text/javascript" src="http://ai.bbs.onez.cn/api.php?mod=js&bid=3"></script>
        </div>
      </div>
    </div>
    
  </div>
    
</section>
<script type="text/javascript">
$(function(){
  $('.qrcode').click(function(){
    $(this).toggleClass('show');
  });
});
</script>
<?


onez('admin')->footer();