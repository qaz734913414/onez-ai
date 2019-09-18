<?php

/* ========================================================================
 * $Id: upgrade.php 6688 2016-09-21 00:55:18Z onez $
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



include_once(dirname(__FILE__).'/init.php');

$item=onez('cache')->get('options');

$G['title']='佳蓝人工智能框架自动升级向导';
#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;


$form->add(array('label'=>'佳蓝通信密钥说明','type'=>'html','html'=>'<code>请务必正确填写，否则可能会升级失败。申请地址：<a href="http://xl.onez.cn/master" target="_blank">点此申请</a><span class="text-gray">(注册后自动分配)</span></code>'));
$form->add(array('label'=>'ONEZ_APPID','type'=>'text','key'=>'onez_appid','hint'=>'','notempty'=>''));
$form->add(array('label'=>'ONEZ_APPKEY','type'=>'text','key'=>'onez_appkey','hint'=>'','notempty'=>''));



#处理提交
if($onez=$form->submit()){
  #唯一识别码
  $onez['siteguid']='7EB1468C-55E6-0B31-0867-410C54CA03DA';
  $onez['mode']='ai.mode.simplify';
  $onez['noreply_app']='ai.script.onezai';
  onez('cache')->option_set($onez);
  $G['options']['onez_appid']=$onez['onez_appid'];
  $G['options']['onez_appkey']=$onez['onez_appkey'];
  $G['options']['siteguid']=$onez['siteguid'];
  $G['options']['mode']=$onez['mode'];
  
  @unlink(ONEZ_ROOT.'/config/version');
  onez()->write(ONEZ_ROOT.'/cache/apps/list.php','<?return array();?>');
  #升级到最新版
  onez('upgrade')->upgrade();
  onez('fetch')->get('fetch',1);
  #优化数据库
  $current=onez('mysql.dbtables')->read();
  //exit($current=onez('mysql.dbtables')->code());
  $DBTables=array();
  #系统自带表
  $sysFile=ONEZ_ROOT.'/config/dbtables.php';
  if(file_exists($sysFile)){
    $dbtables=include($sysFile);
    if($dbtables){
      $DBTables[]=array(
        'group'=>'系统自带',
        'dbtables'=>$dbtables,
      );
    }
  }
  foreach($DBTables as $K=>$V){
    foreach($V['dbtables'] as $tablename=>$table){

      $fields=$fieldNames=array();
      $result='<span class="text-green">正常</span>';
      foreach($table['fields'] as $k=>$v){
        if(!in_array($v['fieldname'],$fields)){
          $fields[]=$v['fieldname'];
          $fieldname=$v['fieldname'];
          if($current[$tablename] && !$current[$tablename]['fields'][$v['fieldname']]){
            $sql=onez('db')->create_field($v);
            $sql && $sqls[]=array(
              'type'=>'query',
              'tablename'=>$tablename,
              'sql'=>'ALTER TABLE `onez_'.$tablename.'` ADD '.$sql,
            );
            $fieldname='<code title="需要追加">'.$fieldname.'</code>';
            $result=str_replace('<span class="text-green">正常</span>','',$result);
            $result && $result.='<br />';
            $result.='<span class="text-red">字段`'.$v['fieldname'].'`不存在，需要追加</span>';
          }
          $fieldNames[]=$fieldname;
        }
      }
      if(!$current[$tablename]){
        $result='<span class="text-red">表不存在，需要创建</span>';
        if($table['summary_create']){
          $result.='<br /><span class="text-red">'.$table['summary_create'].'</span>';
        }
        $sqls[]=array(
          'type'=>'query',
          'tablename'=>$tablename,
          'sql'=>onez('db')->create_mysql($tablename,$table['idname'],$table['fields']),
        );
        if($table['defaults']){
          foreach($table['defaults'] as $v){
            $sqls[]=array(
              'type'=>'insert',
              'table'=>$tablename,
              'values'=>$v,
            );
          }
        }
      }
    }
  }
  foreach($sqls as $sql){
    if($sql['type']=='query'){
      onez('db')->db()->query($sql['sql']);
    }elseif($sql['type']=='insert'){
      onez('db')->open($sql['table'])->insert($sql['values']);
    }
  }
  
  #初始数据
  onez('db')->db()->query("TRUNCATE TABLE onez_app");
  onez('db')->db()->query("INSERT INTO `onez_app` (`appid`, `appname`, `apptoken`, `appicon`, `apptype`, `summary`, `version`, `addtime`, `updatetime`, `enabled`, `style`, `is_system`, `typename`, `installtime`) VALUES(7, '极简对话模式', 'ai.mode.simplify', 'http://ai.open.onez.cn/cache/uploads/2016/09/20/57e08cdcbb641.jpg', 'mode', '实现智能应答机器人功能，可以对访客的提问进行相应的自动回复。', '1.0', 1474333826, 1474333918, 1, 'purple', 0, '模式', 1474387352);");
  onez('db')->db()->query("INSERT INTO `onez_app` (`appid`, `appname`, `apptoken`, `appicon`, `apptype`, `summary`, `version`, `addtime`, `updatetime`, `enabled`, `style`, `is_system`, `typename`, `installtime`) VALUES(10, '官方智能应答接口', 'ai.script.onezai', 'http://ai.open.onez.cn/cache/uploads/2016/09/20/57e14dee468c1.jpg', 'reply', '基于官方云端的智能应答接口，目前仅供测试', '1.0', 1474383385, 1474383719, 1, 'orange', 0, '应答', 1474387405);");
  onez('db')->db()->query("INSERT INTO `onez_app` (`appid`, `appname`, `apptoken`, `appicon`, `apptype`, `summary`, `version`, `addtime`, `updatetime`, `enabled`, `style`, `is_system`, `typename`, `installtime`) VALUES(3, '快速学习', 'ai.script.study', 'http://ai.open.onez.cn/cache/uploads/2016/09/19/57def8f3115eb.jpg', 'script', '通过对话实现快速学习功能', '1.0', 1474229299, 1474230516, 1, 'fuchsia', 0, '脚本', 1474387725);");
  

  @unlink(ONEZ_ROOT.'/cache/apps/list.php');


  ob_clean();
  
  onez('showmessage')->success('升级成功，请立即删除升级文件','index.php');
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
        <button type="submit" class="btn btn-primary" onclick="setTimeout('$(\'.btn\').attr(\'disabled\',true)',100)">
          一键升级到最新版
        </button>
      </div>
    </div>
    <input type="hidden" name="action" value="save" />
  </form>
  <p class="text text-red">注：点击后请耐心等待，请勿重复点击</p>
</section>
<?php
onez('admin')->footer();
?>