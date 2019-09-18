<?php

/* ========================================================================
 * $Id: edit.php 2112 2016-09-18 07:03:48Z onez $
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
define('CUR_URL','/global/index.php');

$group=onez('db')->open('rules_group')->one("is_global='1'");
if(!$group){
  $group=array(
    'subject'=>'全局自动应答设置',
    'type'=>'one',
    'tags'=>'所有目标',
    'is_global'=>1,
  );
  $group['groupid']=$groupid=onez('db')->open('rules_group')->insert($group);
}else{
  $groupid=$group['groupid'];
}

$item=$group;
$G['title']='编辑全局自动应答';
$btnname='保存修改';

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;

#预加载
#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));

$form->add(array('label'=>'无应答脚本','type'=>'form.plugin.child','ptoken'=>'ai.script.reply','key'=>'script_noreply','hint'=>'符合此条件的用户，但是没有应答时调用','notempty'=>''));

#处理提交
if($onez=$form->submit()){
  onez('db')->open('rules_group')->update($onez,"groupid='$groupid'");
  onez()->ok('操作成功',onez()->href('/global/edit.php'));
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
  <form id="form-common" method="post">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">
          <?php echo $G['title'];?>
        </h3>
        <div class="box-tools pull-right">
        </div>
      </div>
      <div class="box-body">
        <?php echo $form->code();?>

      </div>
      <div class="box-footer clearfix">
        <button type="submit" class="btn btn-primary">
          <?php echo $btnname;?>
        </button>
      </div>
    </div>
    <input type="hidden" name="action" value="save" />
  </form>
</section>
<?php
echo $form->js();
onez('admin')->footer();
?>