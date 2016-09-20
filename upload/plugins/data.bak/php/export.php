<?php

/* ========================================================================
 * $Id: export.php 2210 2016-09-20 12:53:36Z onez $
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


$G['title']='导出当前'.$name;
$btnname='确定导出';
$item['summary']=date('Y年n月j日H时i分s秒备份');

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;

#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
$form->add(array('label'=>$name.'描述','type'=>'textarea','key'=>'summary','hint'=>'请填写'.$name.'描述','notempty'=>''));

#处理提交
if($onez=$form->submit()){
  $xxx='';
  if($upid){
    $xxx=" and $upid='".onez()->gp($upid)."'";
  }
  $data=array();
  foreach($tables as $v){
    $data[$v]=onez('db')->open($v)->record("1$xxx");
  }
  $appid=onez('cache')->option('onez_appid',0);
  !$appid && $appid=0;
  $html=array();
  $html[]='<?php exit("Access Denied")?>';
  $data=base64_encode(serialize($data));
  $html[]=base64_encode(serialize(array(
    'type'=>'data',
    'summary'=>$onez['summary'],
    'size'=>strlen($data),
    'hash'=>md5($data),
    'time'=>time(),
  )));
  $html[]=$data;
  
  $filename=$appid.'.'.$groupid.'.'.date('YmdHis').'.data.onez';
  onez('download')->call(implode("{{ONEZ.AI.BAK}}",$html),$filename);
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