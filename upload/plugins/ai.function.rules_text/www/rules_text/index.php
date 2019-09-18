<?php

/* ========================================================================
 * $Id: index.php 2560 2016-09-20 12:52:58Z onez $
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
$G['title']='文本应答';
define('CUR_URL',onez('ai.function.rules_text')->href('/rules_text/index.php'));
$groupid=(int)onez()->gp('groupid');
$action=onez()->gp('action');
if($action=='delete'){
  $id=(int)onez()->gp('id');
  onez('db')->open('rules_text')->delete("id='$id' and groupid='$groupid'");
  onez()->ok('删除文本应答成功','reload');
}
$record=onez('db')->open('rules_text')->page("groupid='$groupid'");
onez('admin')->header();
?>
<section class="content-header">
  <h1>
    文本应答
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
      文本应答
    </li>
  </ol>
</section>
<section class="content">
  <div class="btns" style="padding-bottom: 10px">
    <a href="<?php echo onez('ai.function.rules_text')->href('/rules_text/edit.php')?>&groupid=<?php echo $groupid?>" class="btn btn-success">
      添加文本应答
    </a>
    <?=onez('data.bak')->button('rules_text','词库')?>
  </div>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">
        文本应答
      </h3>
      <div class="box-tools pull-right">
      </div>
    </div>
    <div class="box-body  table-responsive no-padding">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>
              问题
            </th>
            <th>
              答案
            </th>
            <th>
              操作
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($record[0] as $rs){?>
          <tr>
            <td>
              <?php echo $rs['question'];?>
            </td>
            <td>
              <?php echo $rs['answer'];?>
            </td>
            <td>
              <a href="<?php echo onez('ai.function.rules_text')->href('/rules_text/edit.php?id='.$rs['id'].'&groupid='.$rs['groupid'])?>" class="btn btn-xs btn-success">
                编辑
              </a>
              <a href="javascript:void(0)" onclick="onez.del('<?php echo $rs['id'];?>')" class="btn btn-xs btn-danger">
                删除
              </a>
            </td>
          </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
    <?php if($record[1]){?>
    <div class="box-footer clearfix">
      <?php echo $record[1];?>
    </div>
    <?php }?>
  </div>
</section>
<?php
onez('admin')->footer();
?>