<?php

/* ========================================================================
 * $Id: import.php 2982 2016-09-18 07:40:30Z onez $
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

$groupid=(int)onez()->gp('groupid');
$item=onez('db')->open('rules_group')->one("groupid='$groupid'");

$item=array();
$G['title']='导入外部词库';
$btnname='确定导入';

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;

#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
$form->add(array('label'=>'远程词库文件地址','type'=>'text','key'=>'remoteurl','hint'=>'请填写远程词库文件地址','notempty'=>''));
$form->add(array('label'=>'选择本地词库文件','type'=>'file','key'=>'file','hint'=>'二者请选其一','notempty'=>''));

#处理提交
$action=onez()->gp('action');
if($action=='save'){
  $remoteurl=onez()->gp('remoteurl');
  if($remoteurl){
    $data=@file_get_contents($remoteurl);
  }elseif($_FILES['file']){
    $data=onez()->read($_FILES['file']['tmp_name']);
  }
  !$data && onez('showmessage')->error('远程和本地文件请至少选择一个','javascript:history(-1)');
  $basedata=$data;
  list(,$info,$data)=explode('{{ONEZ.AI.BAK}}',$data);

  $info=trim($info);
  $info=base64_decode($info);
  $info=unserialize($info);
  
  $info['hash']!=md5($data) && onez('showmessage')->error('文件已被篡改，无法恢复','javascript:history(-1)');
  $info['type']!='data' && onez('showmessage')->error('备份文件有误，请确定是否为词库数据(*.data.onezai)');

  $data=trim($data);
  $data=base64_decode($data);
  $data=unserialize($data);
  
  
  $tables=array('rules','rules_text');
  foreach($data as $table=>$item){
    if(in_array($table,$tables)){
      foreach($item as $rs){
        unset($rs['ruleid']);
        unset($rs['id']);
        $rs['groupid']=$groupid;
        onez('db')->open($table)->insert($rs);
      }
    }
  }
  
  onez('showmessage')->success('恭喜！导入外部词库成功');
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
  <form id="form-common" method="post" enctype="multipart/form-data">
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
      </div>
    </div>
    <input type="hidden" name="action" value="save" />
  </form>
</section>
<?php
onez('admin')->footer();
?>