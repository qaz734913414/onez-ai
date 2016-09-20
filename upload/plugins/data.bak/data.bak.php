<?php

/* ========================================================================
 * $Id: data.bak.php 1430 2016-09-20 12:49:16Z onez $
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
class onezphp_data_bak extends onezphp{
  function __construct(){
    
  }
  function button($tables,$name='数据',$upid=''){
    $url1=$this->view('export&tables='.$tables.'&name='.urlencode($name).'&upid='.$upid);
    $url2=$this->view('import&tables='.$tables.'&name='.urlencode($name).'&upid='.$upid);
    $html=<<<ONEZ
      <div class="btn-group">
        <button type="button" class="btn btn-info" data-toggle="dropdown" aria-expanded="false">
          导入与导出
        </button>
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li><a href="$url1" class="onez-miniwin">导出当前$name</a></li>
          <li><a href="$url2" class="onez-miniwin">导入外部$name</a></li>
        </ul>
      </div>
ONEZ;
    return $html;
  }
  function export(){
    $tables=onez()->gp('tables');
    $tables=explode(',',$tables);
    $name=onez()->gp('name');
    !$name && $name='数据';
    $upid=onez()->gp('upid');
    include(dirname(__FILE__).'/php/export.php');
  }
  function import(){
    $tables=onez()->gp('tables');
    $tables=explode(',',$tables);
    $name=onez()->gp('name');
    !$name && $name='数据';
    $upid=onez()->gp('upid');
    include(dirname(__FILE__).'/php/import.php');
  }
}