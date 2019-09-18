<?php

/* ========================================================================
 * $Id: setting.php 505 2016-09-20 15:38:34Z onez $
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

if($action=='treeadmin_save'){
  $data=$_REQUEST['data'];
  $data=onez('treeadmin')->data_ready($data);
  onez('cache')->set('guide',$data);
  onez()->ok('保存成功','reload');
}

onez('event')->load('miniwin')->args();
$data=onez('cache')->get('guide');
!$data && $data=array();
echo onez('treeadmin')->load($data)->code();

onez('admin')->footer();
?>