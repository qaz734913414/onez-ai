<?php

/* ========================================================================
 * $Id: plugins.php 4437 2016-09-20 09:23:40Z onez $
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
$G['title']='插件更新检测';
$action=onez()->gp('action');
if($action=='check'){
  $result=onez('fetch')->check();
  onez('cache')->set('plugins.status',$result);
  onez('showmessage')->success('检测完成',onez()->href('/plugins.php'));
}elseif($action=='update'){
  $token=onez()->gp('token');
  
  onez('fetch')->get($token,1);
  $result=onez('fetch')->check();
  onez('cache')->set('plugins.status',$result);
  onez('showmessage')->success('更新完成',onez()->href('/plugins.php'));
}elseif($action=='updateall'){
  
  $plugins=onez('cache')->get('plugins.status');
  $plugins=$plugins['plugins'];
  !$plugins && $plugins=array();
  
  foreach($plugins as $token=>$item){
    if($item['status']=='change'){
      onez('fetch')->get($token,1);
    }
  }
  $result=onez('fetch')->check();
  onez('cache')->set('plugins.status',$result);
  
  onez('showmessage')->success('更新完成',onez()->href('/plugins.php'));
}

define('CUR_URL',onez()->href('/plugins.php'));

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
  <div class="btns" style="padding-bottom: 10px">
    <a href="<?php echo onez()->href('/plugins.php?action=check')?>" class="btn btn-success">
      检测更新
    </a>
  </div>
<div class="row">
  <div class="col-lg-12">
<?

  $box=onez('ui.box')->init('info');
  $plugins=onez('cache')->get('plugins.status');
  $plugins=$plugins['plugins'];
  !$plugins && $plugins=array();
  $record=$keyArr1=$keyArr2=array();
  foreach(_get_all_plugins() as $ptoken){
    if($plugins[$ptoken]){
      $item=$plugins[$ptoken];
      $item['token']=$ptoken;
      if($item['status']=='change'){
        $item['level']='888';
        $item['status_name']='<span class="text text-green">'.$item['status_name'].'</span>';
        
        $html=array();
        $html[]='<a href="'.onez()->href('/plugins.php?action=update&token='.$ptoken).'" class="btn btn-xs btn-info">';
        $html[]='立即更新';
        $html[]='</a>';
        $item['doit']=implode("\n",$html);
        
        $hasNew++;
        
      }elseif($item['status']=='error'){
        $item['level']='777';
        $item['status_name']='<span class="text text-red">'.$item['status_name'].'</span>';
      }elseif($item['status']=='none'){
        $item['level']='0';
        $item['status_name']='<span class="text text-gray">'.$item['status_name'].'</span>';
      }
      $item['lasttime']=date('Y-m-d H:i:s',$item['lasttime']);
    }else{
      $item=array(
        'level'=>999,
        'token'=>$ptoken,
        'name'=>'---',
        'lasttime'=>'---',
        'status_name'=>'---',
        'doit'=>'---',
      );
    }
    $record[]=$item;
    $keyArr1[]=$item['level'];
    $keyArr2[]=$item['token'];
  }
  array_multisort($keyArr1,SORT_NUMERIC,SORT_DESC,$keyArr2,SORT_ASC,$record);
  $box->add('插件更新检测',_text($record));
  
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
            ->add('插件标识','token')
            ->add('插件名称','name')
            ->add('最后检测时间','lasttime')
            ->add('状态','status_name')
            ->add('操作','doit')
          ;
    return $table->code();
  }
  
  $box->show();
?>
  <?if($hasNew){?>
  <div class="btns" style="padding-bottom: 10px">
    <a href="<?php echo onez()->href('/plugins.php?action=updateall')?>" class="btn btn-info">
      更新所有插件
    </a>
  </div>
  <?}?>
</div>
</section>
<?
onez('admin')->footer();