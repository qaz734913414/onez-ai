<?php

/* ========================================================================
 * $Id: install.php 685 2016-09-21 00:08:30Z onez $
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
define('CUR_URL','/app/index.php');

$appinfo=onez()->gp('appinfo');
$app=base64_decode($appinfo);
$app=unserialize($app);
$appid=$app['appid'];

onez($app['apptoken']);

$myapp=onez('db')->open('app')->one("appid='$appid'");
if($myapp){
  onez('showmessage')->error('您已经安装过这个应用了',onez()->href('/app/index.php'));
}else{
  $app['installtime']=time();
  unset($app['price']);
  onez('db')->open('app')->insert($app);
  @unlink(ONEZ_ROOT.'/cache/apps/list.php');
  onez('showmessage')->success('恭喜，安装应用成功',onez()->href('/app/index.php'));
}

onez('admin')->header();

onez('admin')->footer();
?>