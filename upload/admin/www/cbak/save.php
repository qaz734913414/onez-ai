<?php

/* ========================================================================
 * $Id: save.php 2744 2016-09-20 10:53:42Z onez $
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


$item=array();
$item['summary']=date('Y年n月j日H时i分s秒备份');
$G['title']='备份当前数据';
$btnname='确定备份';

#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;
$tables=onez('mysql.dbtables')->tables();

#创建表单项
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));
foreach($tables as $v){
  $n=onez('db')->open($v)->rows("");
  $form->add(array('label'=>'备份表`'.$v.'` (共有<code>'.$n.'</code>条数据)','type'=>'checkbox','key'=>'table_'.$v,'value'=>1));
}
$form->add(array('label'=>'备注说明','type'=>'textarea','key'=>'summary','hint'=>'请填写备注说明','notempty'=>''));

#处理提交
if($onez=$form->submit()){
  set_time_limit(0);
  $data=array();
  foreach($tables as $v){
    if(!$onez['table_'.$v]){
      continue;
    }
    $data[$v]=onez('db')->open($v)->record("");
  }
  $appid=onez('cache')->option('onez_appid',0);
  !$appid && $appid=0;
  $file=ONEZ_ROOT.'/cache/cbaks/'.$appid.'.'.date('YmdHis').'.php';
  $html=array();
  $html[]='<?php exit("Access Denied")?>';
  $data=base64_encode(serialize($data));
  $html[]=base64_encode(serialize(array(
    'type'=>'site',
    'summary'=>$onez['summary'],
    'size'=>strlen($data),
    'hash'=>md5($data),
    'time'=>time(),
  )));
  $html[]=$data;
  $html[]=base64_encode(serialize(onez('cache')->get('guide')));
  onez()->write($file,implode("{{ONEZ.AI.BAK}}",$html));

  onez()->ok('操作成功',onez()->href('/cbak/index.php'));
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