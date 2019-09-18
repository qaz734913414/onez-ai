<?php

/* ========================================================================
 * $Id: index.php 3173 2016-09-18 07:17:40Z onez $
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
$G['title']='备份与还原';
define('CUR_URL','/cbak/index.php');
$action=onez()->gp('action');
if($action=='delete'){
  $id=onez()->gp('id');
  $file=ONEZ_ROOT.'/cache/cbaks/'.$id.'.php';
  @unlink($file);
  onez()->ok('删除备份文件成功','reload');
}
onez('admin')->header();
?>
<section class="content-header">
  <h1>
    备份与还原
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
      备份与还原
    </li>
  </ol>
</section>
<section class="content">
  <div class="btns" style="padding-bottom: 10px">
    <a href="<?php echo onez()->href('/cbak/save.php')?>" class="btn btn-primary">
      备份当前数据
    </a>
    <a href="<?php echo onez()->href('/cbak/import.php')?>" class="btn btn-success">
      导入外部备份文件
    </a>
  </div>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">
        导入与导出
      </h3>
      <div class="box-tools pull-right">
      </div>
    </div>
    <div class="box-body  table-responsive no-padding">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>
              备份时间
            </th>
            <th>
              文件大小
            </th>
            <th>
              备份说明
            </th>
            <th>
              操作
            </th>
          </tr>
        </thead>
        <tbody>
          <?php
          $glob=glob(ONEZ_ROOT.'/cache/cbaks/*');
          !$glob && $glob=array();
          $record=array();
          foreach($glob as $v){
            list(,$info)=explode('{{ONEZ.AI.BAK}}',onez()->read($v));
            $info=trim($info);
            $info=base64_decode($info);
            $info=unserialize($info);
            $record[]=array(
              'key'=>substr(basename($v),0,-4),
              'summary'=>$info['summary'],
              'hash'=>$info['hash'],
              'time'=>date('Y-m-d H:i:s',$info['time']),
              'size'=>onez('files')->filesize(filesize($v)),
            );
          }
          foreach($record as $rs){?>
          <tr>
            <td>
              <?php echo $rs['time'];?>
            </td>
            <td>
              <?php echo $rs['size'];?>
            </td>
            <td>
              <?php echo $rs['summary'];?>
            </td>
            <td>
              <a href="<?php echo onez()->href('/cbak/download.php?id='.$rs['key'])?>" class="btn btn-xs btn-info">
                下载
              </a>
              <a href="<?php echo onez()->href('/cbak/apply.php?id='.$rs['key'])?>" class="btn btn-xs btn-success">
                恢复
              </a>
              <a href="javascript:void(0)" onclick="onez.del('<?php echo $rs['key'];?>')" class="btn btn-xs btn-danger">
                删除
              </a>
            </td>
          </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?php
onez('admin')->footer();
?>