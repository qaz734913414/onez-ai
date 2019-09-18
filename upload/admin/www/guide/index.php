<?php

/* ========================================================================
 * $Id: index.php 1266 2016-09-18 11:41:56Z onez $
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
$G['title']='多级指引';
define('CUR_URL','/guide/index.php');
$action=onez()->gp('action');

$record=onez('db')->open('guide')->page("");

$action=onez()->gp('action');
if($action=='treeadmin_save'){
  $data=$_REQUEST['data'];
  $data=onez('treeadmin')->data_ready($data);
  onez('cache')->set('guide',$data);
  onez()->ok('保存多级指引成功','reload');
}

onez('admin')->header();
?>
<section class="content-header">
  <h1>
    多级指引
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
      多级指引
    </li>
  </ol>
</section>
<section class="content">
<?
onez('event')->load('miniwin')->args();
$data=onez('cache')->get('guide');
!$data && $data=array();
echo onez('treeadmin')->load($data)->addattr('ruleid')->addbtn('<button class="btn btn-xs bg-purple" onclick="_setrule({id})">高级</button>')->code();
?>
</section><script type="text/javascript">
function _setrule(id){
  _treeadmin_save(true);
  miniwin('<?php echo onez()->href('/guide/rule.php?id=')?>'+id,'设置执行动作');
}
</script>
<?php
onez('admin')->footer();
?>