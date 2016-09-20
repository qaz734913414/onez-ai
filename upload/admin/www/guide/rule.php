<?php

/* ========================================================================
 * $Id: rule.php 2401 2016-09-18 10:49:42Z onez $
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
define('CUR_URL','/guide/index.php');

$id=(int)onez()->gp('id');
$item=onez('db')->open('guide')->one("guideid='$id'");
$G['title']='设置关联动作';
$btnname='保存修改';

$arr=array();
$arr['deviceid']=$deviceid;
$arr['groupid']=$groupid;
$arr['input_type']='ai.input.text';
$arr['doit']=array();
$item['doit'] && $arr['doit']=json_decode($item['doit'],1);

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;

#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));

#处理提交
$action=onez()->gp('action');
if($action=='save'){
  if($item){
    $onez['doit']=$_REQUEST['doit'];
    onez('db')->open('guide')->update($onez,"guideid='$id'");
  }else{
    $onez=array(
      'guideid'=>$id,
      'doit'=>$_REQUEST['doit'],
    );
    onez('db')->open('guide')->insert($onez);
  }
  onez()->ok('操作成功','close');
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
          <?=$G['title']?>
        </h3>
        <div class="box-tools pull-right">
        </div>
      </div>
      <div class="box-body">
        <?php echo $form->code();?>
        <?=onez('ai.admin')->doit($arr)?>
      </div>
      <div class="box-footer clearfix">
        <button type="submit" class="btn btn-primary">
          <?php echo $btnname;?>
        </button>
      </div>
    </div>
    <input type="hidden" name="action" value="save" />
    <input type="hidden" name="doit" id="doit_input" value="" />
  </form>
</section>
<script type="text/javascript">
$(function(){
  $('#form-common').bind('submit',function(){
    //doit信息
    var doit=_doit_get();
    if(typeof doit=='string'){
      onez.alert(doit);
      return false;
    }
    $('#doit_input').val(JSON.stringify(doit));
    onez.formpost(this);
    return false;
  });
});
</script>
<?php
onez('admin')->footer();
?>