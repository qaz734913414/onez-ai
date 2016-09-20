<?php

/* ========================================================================
 * $Id: apply.php 3317 2016-09-20 10:54:38Z onez $
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
define('CUR_URL','/cbak/index.php');
$id=onez()->gp('id');
$file=ONEZ_ROOT.'/cache/cbaks/'.$id.'.php';
list(,$info,$data,$guide)=explode('{{ONEZ.AI.BAK}}',onez()->read($file));
$info=trim($info);
$info=base64_decode($info);
$info=unserialize($info);
$info['type']!='site' && onez('showmessage')->error('备份文件有误，请确定是否为整站数据');

$item=array();
$G['title']='恢复数据';
$btnname='确定恢复';

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;

$TableName=array(
  'tags'=>'智能标签',
  'attrs'=>'智能属性',
  'devices'=>'终端',
  'rules_group'=>'规则分类',
  'rules'=>'高级规则',
  'rules_text'=>'文本词库',
  'guide'=>'多级指引',
);

$data=trim($data);
$data=base64_decode($data);
$data=unserialize($data);
$tables=array();
foreach($data as $table=>$item){
  $tables[]='<span class="text-danger"><u>'.($TableName[$table]?$TableName[$table]:$table).'</u></span>';
}


$tables=implode('、',$tables);
              
#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
$form->add(array('label'=>'备注时间','type'=>'html','html'=>'<code>'.date('Y-m-d H:i:s',$info['time']).'</code>'));
$form->add(array('label'=>'文件大小','type'=>'html','html'=>'<code>'.onez('files')->filesize(filesize($file)).'</code>'));
$form->add(array('label'=>'备注说明','type'=>'html','html'=>'<code>'.$info['summary'].'</code>'));
$form->add(array('label'=>'即将被清空的表','type'=>'html','html'=>$tables));

#处理提交
if(onez()->gp('action')=='save'){
  foreach($data as $table=>$item){
    onez('db')->open($table)->delete("1");
    foreach($item as $rs){
      onez('db')->open($table)->insert($rs);
    }
  }
  $guide=trim($guide);
  $guide=base64_decode($guide);
  $guide=unserialize($guide);
  if($guide && is_array($guide)){
    onez('cache')->set('guide',$guide);
  }
  onez()->ok('恢复数据成功',onez()->href('/cbak/index.php'));
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
<div class="callout callout-danger lead">
  <h4>系统提示</h4>
  <p>恢复数据将会清空现有内容，请谨慎操作</p>
</div>
  <form id="form-common" method="post">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">
          <?=$G['title']?>
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
        <a href="<?php echo onez()->href('/cbak/index.php')?>" class="btn btn-default">
          返回
        </a>
      </div>
    </div>
    <input type="hidden" name="action" value="save" />
  </form>
</section>
<?php
echo $form->js();
onez('admin')->footer();
?>