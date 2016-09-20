<?php

/* ========================================================================
 * $Id: treeadmin.php 5194 2016-09-20 15:18:52Z onez $
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
class onezphp_treeadmin extends onezphp{
  var $data=array();
  var $full_data=array();
  var $attrs=array();
  var $btns=array();
  var $index=0;
  function __construct(){
    
  }
  function load($data){
    if(!is_array($data)){
      $data=array();
    }
    $this->data=$data;
    return $this;
  }
  function addattr($attr){
    $this->attrs[]=$attr;
    return $this;
  }
  function addbtn($btn){
    $this->btns[]=$btn;
    return $this;
  }
  function code(){
    $html[]='<script src="'.$this->url.'/js/jquery.nestable.js"></script>';
    $html[]='<link rel="stylesheet" href="'.$this->url.'/css/jquery.nestable.css">';
    $html[]='<button type="button" class="btn btn-success treeadmin-add" onclick="_treeadmin_click()">添加</button>';
    $html[]='<button type="button" class="btn btn-info treeadmin-save" onclick="_treeadmin_save()">保存</button>';
    $html[]='<div class="dd treeadmin">';
    
    $html[]=$this->ol($this->data);
    
    $html[]='</div>';
    
    $index=$this->index;
    
    $attr='';
    if($this->attrs){
      foreach($this->attrs as $b){
        $attr.='li.attr(\'data-'.$b.'\',\'\');';
      }
    }
    $btn='';
    if($this->btns){
      foreach($this->btns as $b){
        $btn.=' '.str_replace('{id}',"'+treeadmin_index+'",$b);
      }
    }
      
    $html[]=<<<ONEZ
<style>
.treeadmin .btn{
  position: relative;
  top: -3px;
}
</style>
<script type="text/javascript">
var treeadmin_index=$index;
function _treeadmin_click(){
  bootbox.prompt({ 
    size: 'small',
    title: "请输入新条目名称", 
    message: "Your message here…", 
    callback: function(result){
      if(result!=null && result.length>0){
        treeadmin_index++;
        var li=$('<li class="dd-item dd3-item" data-id="'+treeadmin_index+'" />');
        li.attr('data-name',result);
        $attr
        $('<div class="dd-handle dd3-handle"> </div>').appendTo(li);
        $('<div class="dd3-content"> <span><code>'+treeadmin_index+'</code> '+result+'</span> <button class="btn btn-xs btn-info" onclick="_treeadmin_edit('+treeadmin_index+')">修改</button> <button class="btn btn-xs btn-danger" onclick="_treeadmin_del('+treeadmin_index+')">删除</button>$btn </div>').appendTo(li);
        $('.treeadmin > ol').append(li);
        _treeadmin_save(true);
      }
    }
  });
  
}
function _treeadmin_edit(id){
  bootbox.prompt({ 
    size: 'small',
    title: "请输入新名称", 
    message: "Your message here…", 
    callback: function(result){
      if(result!=null && result.length>0){
        var li=$('.dd3-item[data-id="'+id+'"]');
        li.attr('data-name',result);
        li.find('>.dd3-content span').html(result);
        _treeadmin_save(true);
      }
    }
  });
  $('.bootbox-input-text').val($('.dd3-item[data-id="'+id+'"]').attr('data-name'));
}
function _treeadmin_del(id){
  var li=$('.dd3-item[data-id="'+id+'"]');
  li.remove();
}
function _treeadmin_save(notip){
  if(typeof notip=='undefined'){
    notip=false;
  }
  $.post(window.location.href,{action:'treeadmin_save',data:$('.treeadmin').nestable('serialize')},function(){
    if(!notip){
      onez.alert('保存成功');
    }
    
  });
}
function _treeadmin_update(){
  $('.treeadmin').nestable();
}
_treeadmin_update();
</script>
ONEZ;
    return implode("\n",$html);
  }
  function ol($arr){
    $html[]='<ol class="dd-list">';
    foreach($arr as $v){
      $this->index=max($this->index,(int)$v['id']);
      $this->full_data[$v['id']]=$v;
      
      
      $attr='';
      if($this->attrs){
        foreach($this->attrs as $b){
          $attr.='  data-'.$b.'="'.$v[$b].'"';
        }
      }
      $btn='';
      if($this->btns){
        foreach($this->btns as $b){
          $btn.=' '.str_replace('{id}',$v['id'],$b);
        }
      }
      
      $html[]='<li class="dd-item dd3-item" data-id="'.$v['id'].'" data-name="'.$v['name'].'"'.$attr.'>';
      $html[]='<div class="dd-handle dd3-handle"> </div>';
      
      
      $html[]='<div class="dd3-content"> <span><code>'.$v['id'].'</code> '.$v['name'].'</span> <button class="btn btn-xs btn-info" onclick="_treeadmin_edit('.$v['id'].')">修改</button> <button class="btn btn-xs btn-danger" onclick="_treeadmin_del('.$v['id'].')">删除</button>'.$btn.'</div>';
      if($v['children']){
        $html[]=$this->ol($v['children']);
      }
      $html[]='</li>';
    }
    $html[]='</ol>';
    return implode("\n",$html);
  }
  function save(){
    $data=array();
    $action=onez()->gp('action');
    if($action=='treeadmin_save'){
      $data=$_REQUEST['data'];
      //$data=json_decode($data,1);
      $data=$this->data_ready($data);
    }
    return $data;
  }
  function data_ready($arr){
    $attrs=array('id','name','children');
    if($this->attrs){
      foreach($this->attrs as $b){
        $attrs[]=$b;
      }
    }
    foreach($arr as $k=>$v){
      if($E=$this->full_data[$v['id']]){
        foreach($E as $a=>$b){
          if(!in_array($a,$attrs)){
            $arr[$k][$a]=$b;
          }
        }
      }
      if($v['children']){
        $arr[$k]['children']=$this->data_ready($v['children']);
      }
    }
    return $arr;
  }
}