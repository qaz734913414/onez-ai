<?php

/* ========================================================================
 * $Id: upgrade.php 2883 2016-09-20 09:23:40Z onez $
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
$G['title']='更新网站至最新版';

define('CUR_URL',onez()->href('/upgrade.php'));

$form=onez('admin')->widget('form');

$myversion=onez('upgrade')->version();

$siteguid=onez('cache')->option('siteguid',0);
if(!$siteguid){
  $form->add(array('label'=>'产品唯一识别码','type'=>'text','key'=>'siteguid','hint'=>'请向客服索取或到官方管理后台查找','notempty'=>'产品唯一识别码不能为空'));

  $btnname='保存修改';
}else{
  $form->add(array('label'=>'当前版本','type'=>'html','html'=>'<code>'.$myversion['version'].'</code>'));

  $newversion=onez('upgrade')->newversion();
  if($myversion['version']==$newversion['version']){
    $form->add(array('label'=>'系统提示','type'=>'html','html'=>'<code>当前已经是最新版本了！</code>'));
  }else{
    $form->add(array('label'=>'最新版本','type'=>'html','html'=>'<code>'.$newversion['version'].'</code>'));
    $form->add(array('label'=>'更新内容','type'=>'html','html'=>'<pre>'.$newversion['summary'].'</pre>'));
    $form->add(array('label'=>'重要提示','type'=>'html','html'=>'<p style="color:blue"><b>!!!重要!!!</b> <b><code>/plugins</code>内的文件将自动更新为最新版，如果您手动修改过，请务必在更新前备份并移至<code>/myplugins</code>目录</b></p>'));
    $btnname='我已备份完毕, 立即更新';
  }
}


$action=onez()->gp('action');
if($siteguid){
  if($action=='save'){
    onez('upgrade')->upgrade();
    onez('showmessage')->success('更新网站成功',onez()->href('/upgrade.php'));
  }
}

#处理提交
if($onez=$form->submit()){
  if(!$siteguid){
    onez('cache')->option_set($onez);
    onez()->ok('操作成功','reload');
  }
  onez()->ok('更新网站成功','reload');
}

onez('admin')->header();
?>
<section class="content-header">
  <h1>
    <?=$G['title']?>
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
      <?php echo $G['title'];?>
    </li>
  </ol>
</section>
<section class="content">
<div class="row">
  <div class="col-lg-12">
  
<form method="post" id="form-common" class="">
<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title"><?=$G['title']?></h3>
  </div><!-- /.box-header -->
  <div class="box-body">
	  <?=$form->code();?>
  </div><!-- /.box-body -->
  <?if($btnname){?>
  <div class="box-footer">
	  <button class="btn btn-success" type="submit"><?=$btnname?></button>
  </div>
  <?}?>
</div>
<input type="hidden" name="action" value="save">
</form>

  
  </div>
</div>
</section>
<?
if(!$siteguid){
  echo $form->js();
}
onez('admin')->footer();