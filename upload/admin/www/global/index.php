<?php

/* ========================================================================
 * $Id: index.php 4571 2016-09-18 11:42:52Z onez $
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
$G['title']='全局自动应答';
define('CUR_URL','/global/index.php');
$type=onez()->gp('type');
!$type && $type='text';

$group=onez('db')->open('rules_group')->one("is_global='1'");
if(!$group){
  $group=array(
    'subject'=>'全局自动应答',
    'type'=>'one',
    'tags'=>'所有目标',
    'is_global'=>1,
  );
  $group['groupid']=$groupid=onez('db')->open('rules_group')->insert($group);
}else{
  $groupid=$group['groupid'];
}

$action=onez()->gp('action');
if($action=='delete'){
  $id=onez()->gp('id');
  list($type,$id)=explode('.',$id);
  if($type=='text'){
    $id=(int)$id;
    onez('db')->open('rules_text')->delete("id='$id'");
  }else if($type=='rules'){
    $id=(int)$id;
    onez('db')->open('rules')->delete("ruleid='$id'");
  }
  onez()->ok('删除规则成功','reload');
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
      <?=$G['title']?>
    </li>
  </ol>
</section>
<section class="content">
  <div class="btns" style="padding-bottom: 10px">
    <div class="btn-group">
      <a href="<?php echo onez()->href('/rules_text/edit.php?from=global&groupid='.$groupid)?>" class="btn btn-info onez-miniwin">
        添加文字对话词库
      </a>
      <a href="<?php echo onez()->href('/rules/add_device.php?from=global&groupid='.$groupid)?>" class="btn btn-info onez-miniwin">
        高级录入
      </a>
      <a href="<?php echo onez()->href('/global/edit.php')?>" class="btn btn-info onez-miniwin">
        全局应答设置
      </a>
      <div class="btn-group">
        <button type="button" class="btn btn-info" data-toggle="dropdown" aria-expanded="false">
          导入与导出
        </button>
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li><a href="<?php echo onez()->href('/rules/export.php?groupid='.$groupid)?>" class="onez-miniwin">导出当前词库</a></li>
          <li><a href="<?php echo onez()->href('/rules/import.php?groupid='.$groupid)?>" class="onez-miniwin">导入外部词库</a></li>
        </ul>
      </div>
    </div>
  </div>
  <?
  $box=onez('ui.box')->init('info');
  
  $record=onez('db')->open('rules_text')->page("groupid='$groupid'");
  $box->add('普通文本',_text($record[0]),$record[1],$type=='text');
  
  $record=onez('db')->open('rules')->page("groupid='$groupid' order by step,ruleid");
  $box->add('高级规则',_super($record[0]),$record[1],$type=='rules');
  
  $box->show();
  
  function _table_doit_1($rs){
    $html=array();
    $html[]='<a href="'.onez()->href('/rules_text/edit.php?groupid='.$rs['groupid'].'&from=global&id='.$rs['id']).'" class="btn btn-xs btn-success onez-miniwin">';
    $html[]='编辑';
    $html[]='</a>';
    $html[]='<a href="javascript:void(0)" onclick="onez.del(\'text.'.$rs['id'].'\')" class="btn btn-xs btn-danger">';
    $html[]='删除';
    $html[]='</a>';
    return implode("\n",$html);
  }
  function _text($record){
    $table=onez('ui.table',-1)
            ->init($record)
            ->add('问题','question')
            ->add('回答','answer')
            ->add('操作','_table_doit_1','function')
          ;
    return $table->code();
  }
  
  
  
  function _table_device($rs){
    $device=onez('db')->open('devices')->one("deviceid='$rs[deviceid]'");
    return $device['subject'];
  }
  function _table_doit_2($rs){
    $html=array();
    $html[]='<a href="'.onez()->href('/rules/add_design.php?from=global&id='.$rs['ruleid']).'" class="btn btn-xs btn-success onez-miniwin">';
    $html[]='编辑';
    $html[]='</a>';
    $html[]='<a href="javascript:void(0)" onclick="onez.del(\'rules.'.$rs['ruleid'].'\')" class="btn btn-xs btn-danger">';
    $html[]='删除';
    $html[]='</a>';
    return implode("\n",$html);
  }
  function _super($record){
    $table=onez('ui.table',-1)
            ->init($record)
            ->add('端终','_table_device','function')
            ->add('输入类型','input_typename')
            ->add('操作员','add_info')
            ->add('规则描述','summary')
            ->add('操作','_table_doit_2','function')
          ;
    return $table->code();
  }
  ?>
  
      
<?php
onez('admin')->footer();
?>