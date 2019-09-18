<?php

/* ========================================================================
 * $Id: files.php 1549 2016-09-17 23:24:31Z onez $
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
class onezphp_files extends onezphp{
  var $files=array();
  function __construct(){
    
  }
  function clear(){
    $this->files=array();
  }
  function openzip($zipfile){
  	$zip = zip_open($zipfile);
  	if ($zip) {
  		while ($zip_entry = zip_read($zip)) { 
  			$size=zip_entry_filesize($zip_entry);
  			#if ($size>0) {
  				if (zip_entry_open($zip, $zip_entry, "r")) {
  					$this->files[zip_entry_name($zip_entry)]=zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)); ;
  					zip_entry_close($zip_entry); 
  				}
  			#}
  		}
  		zip_close($zip);
  	}
  }
  function files(){
    return $this->files;
  }
  /**
  * 添加单文件
  * @param undefined $filename
  * @param undefined $data
  * 
  * @return
  */
  function add($filename,$data){
    $this->files[$filename]=$data;
  }
  /**
  * 添加文件数组
  * @param undefined $files
  * 
  * @return
  */
  function addfiles($files=false){
    if($files && is_array($files)){
      foreach($files as $k=>$v){
        $this->add($k,$v);
      }
    }
  }
  function zip(){
    return onez('zip')->zip($this->files);
  }
  function showdown($filename='temp.zip'){
    onez('download')->call($this->zip()->data,$filename);
  }
  function filesize($size){
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2).$units[$i];
  }
  function filetype($filename){
    $o=explode('.',$filename);
    return strtolower($o[count($o)-1]);
  }
}