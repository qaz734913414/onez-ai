<?php

/* ========================================================================
 * $Id: ui.table.php 1410 2016-09-17 22:23:42Z onez $
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
class onezphp_ui_table extends onezphp{
  var $record;
  var $cols=array();
  function __construct(){
    
  }
  function init($record){
    $this->record=$record;
    return $this;
  }
  function add($label,$key,$type='rs'){
    $this->cols[]=array(
      'label'=>$label,
      'key'=>$key,
      'type'=>$type,
    );
    return $this;
  }
  function code(){
    $record=$this->record;
    !$record && $record=array();
    
    $html=array();
    
    $html[]='<table class="table table-striped">';
    
    $html[]='<thead>';
      $html[]='<tr>';
      foreach($this->cols as $v){
        $html[]='<th>'.$v['label'].'</th>';
      }
      $html[]='</tr>';
    $html[]='</thead>';
    
    $html[]='<tbody>';
      foreach($record as $rs){
        $html[]='<tr>';
        foreach($this->cols as $v){
          $html[]='<td>';
          if($v['type']=='html'){
            $html[]=$v['key'];
          }elseif($v['type']=='rs'){
            $html[]=$rs[$v['key']];
          }elseif($v['type']=='function'){
            if(function_exists($v['key'])){
              $func=$v['key'];
              $html[]=$func($rs);
            }
          }
          $html[]='</td>';
        }
        $html[]='</tr>';
      }
    $html[]='</tbody>';
    
    $html[]='</table>';
    
    return implode("\n",$html);
  }
  function show(){
    echo $this->code();
  }
}