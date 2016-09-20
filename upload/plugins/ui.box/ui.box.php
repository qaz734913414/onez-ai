<?php

/* ========================================================================
 * $Id: ui.box.php 2976 2016-09-18 08:01:12Z onez $
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
class onezphp_ui_box extends onezphp{
  var $id=0;
  var $is_table=1;
  var $style='info';
  var $boxList=array();
  function __construct(){
    
  }
  function init($style='info',$is_table=1){
    $this->id++;
    $this->style=$style;
    $this->is_table=$is_table;
    return $this;
  }
  function add($header,$body,$footer='',$current=false){
    $this->boxList[]=array(
      'header'=>$header,
      'body'=>$body,
      'footer'=>$footer,
      'current'=>$current,
    );
    return $this;
  }
  function code(){
    $html=array();
    $n=count($this->boxList);
    if($n==1){
      $box=$this->boxList[0];
      !$box['body'] && $box['body']='<p class="text-center text-gray">暂无任何记录</p>';
      
      $html[]='<div class="box box-'.$this->style.'">';
        $html[]='<div class="box-header with-border">';
          $html[]='<h3 class="box-title">';
            $html[]=$box['header'];
          $html[]='</h3>';
          $html[]='<div class="box-tools pull-right">';
          $html[]='</div>';
        $html[]='</div>';
        
        $html[]='<div class="box-body'.($this->is_table?' table-responsive no-padding':'').'">';
        $html[]=$box['body'];
        $html[]='</div>';
        
        if($box['footer']){
          $html[]='<div class="box-footer clearfix">';
          $html[]=$box['footer'];
          $html[]='</div>';
        }
        
      $html[]='</div>';
    }elseif($n>1){
      $html[]='<div class="nav-tabs-custom">';
        $html[]='<ul class="nav nav-tabs">';
        foreach($this->boxList as $k=>$box){
          $id='box_'.$this->id.'_'.$k;
          $s=$box['current']?' class="active"':'';
          $html[]='<li'.$s.'><a href="#'.$id.'" data-toggle="tab" aria-expanded="true">'.$box['header'].'</a></li>';
        }
        $html[]='</ul>';
        
        $html[]='<div class="tab-content'.($this->is_table?' table-responsive no-padding':'').'">';
        foreach($this->boxList as $k=>$box){
          !$box['body'] && $box['body']='<p class="text-center text-gray">暂无任何记录</p>';
          $id='box_'.$this->id.'_'.$k;
          $s=$box['current']?' active':'';
          $html[]='<div class="tab-pane'.$s.'" id="'.$id.'">';
          $html[]=$box['body'];
          $html[]='</div>';
        }
        $html[]='</div>';
        
      $html[]='</div>';
      if($this->times(1)){
        $html[]=<<<ONEZ
<script type="text/javascript">
$(document).ready(function() {
  if(location.hash) {
    $('a[href=' + location.hash + ']').tab('show');
  }

  $(document.body).on("click", "a[data-toggle]", function(event) {
    location.hash = this.getAttribute("href");
  });
});

$(window).on('popstate', function() {
  var anchor = location.hash || $("a[data-toggle=tab]").first().attr("href");
  $('a[href=' + anchor + ']').tab('show');
});
</script>
ONEZ;
      }
    }
    return implode("\n",$html);
  }
  function show(){
    echo $this->code();
  }
}