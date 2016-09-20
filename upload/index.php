<?php

/* ========================================================================
 * $Id: index.php 3008 2016-09-21 02:23:08Z onez $
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



if(onez()->exists('cache')){
  $options=onez('cache')->get('options');
  if(!$options['is_dbinit']){
    onez()->location('super.php');
  }
}

$G['title']='佳蓝人工智能开源框架';
onez('showmessage');
#初始化对话框引擎
$ui=onez('ui')->init();
$ui->heads[]=onez('ui')->less(dirname(__FILE__).'/less/style.less');

$ui->header();

//图标动画
onez('animate.css')->init();
?>
<div class="background fullscreen" style="position: absolute;left:0;top: 0;width: 100%">
</div>
<?=onez('html5.star')->code('.background')?>
<?onez('onezjs')->init()?>
<div class="rows">
  <div class="col-xs-8 col-xs-offset-2 col-md-4 col-md-offset-4 mainbox">
    <p class="logo"><a href="http://www.onez.cn" target="_blank"><img src="images/logo.png" width="280" class="animated bounceInDown" /></a></p>
    <p class="mainBtns">
      <?
      if($G['mode']){
        echo onez($G['mode'])->index_button('电脑版对话');
      }
      ?>
      <a href="admin" class="btn btn-danger" target="_blank">超级管理后台</a>
      <a href="super.php" class="btn btn-warning" target="_blank">开发设计模式</a>
    </p>
    
    <p class="info">
      ✔ 完全开源
      ✔ 免费
      ✔ 支持二次开发
      ✔ Apache开源协议
    </p>
    <p class="info">
      Git地址：<a href="https://github.com/onezcn/onez-ai.git" target="_blank">https://github.com/onezcn/onez-ai.git</a>
    </p>
    <p class="info">
      <a href="http://ai.bbs.onez.cn" class="btn btn-xs btn-primary" target="_blank">论坛交流</a>
      <a href="https://github.com/onezcn/onez-ai.git" class="btn btn-xs btn-primary" target="_blank">Github</a>
      <a href="http://www.oschina.net/p/onez-ai" class="btn btn-xs btn-primary" target="_blank">开源中国</a>
      <a href="http://member.down.admin5.com/php/134166.html" class="btn btn-xs btn-primary" target="_blank">A5下载</a>
      <a href="http://down.chinaz.com/soft/38236.htm" class="btn btn-xs btn-primary" target="_blank">中国站长站</a>
      <a href="http://ai.onez.cn/cache/ai.zip" class="btn btn-xs btn-primary" target="_blank">本地下载</a>
      <a href="http://shang.qq.com/wpa/qunwpa?idkey=f7860dfb3e265264f9f359285de578f4fede15f6e8ef183614cdd11645922463" class="btn btn-xs btn-primary" target="_blank">QQ群:185490966</a>
    </p>
    <p class="info" style="color:#ffff00">
      如有疑问，敬请QQ群或论坛，期待您的加入！！！有问必答！！谢谢！
    </p>
    <p class="info">
      前端演示的功能仅是网页对话，实际功能远非如此，欢迎进一步了解！
    </p>
  </div>
</div>
<script type="text/javascript">
$(function(){
  $('body').attr('scroll','no').css({overflow:'hidden'});
  
});
</script>
<script type="text/javascript" id="onez-report" src="http://xl.onez.cn/vip/download/ai.php?sitetoken=github&mod=/report.php"></script>
<?$ui->footer();#显示底部?>