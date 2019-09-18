<?php

/* ========================================================================
 * $Id: setting.php 1437 2016-09-20 22:59:28Z onez $
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

$item=onez('cache')->get('options');
#初始化表单
$form=onez('admin')->widget('form')
  ->set('title',$G['title'])
  ->set('values',$item)
;
$form->add(array('type'=>'hidden','key'=>'action','value'=>'save'));

$form->add(array('label'=>'佳蓝通信密钥说明','type'=>'html','html'=>'<code>用于快速获取云端扩展，实现更多更强大的功能。申请地址：<a href="http://xl.onez.cn/master" target="_blank">点此申请</a><span class="text-gray">(注册后自动分配)</span></code>'));
$form->add(array('label'=>'ONEZ_APPID','type'=>'text','key'=>'onez_appid','hint'=>'','notempty'=>''));
$form->add(array('label'=>'ONEZ_APPKEY','type'=>'text','key'=>'onez_appkey','hint'=>'','notempty'=>''));

#处理提交
if($onez=$form->submit()){
  ob_clean();
  onez('cache')->option_set($onez);
  onez()->ok('操作成功','reload');
}
?>
<form id="form-common" method="post">
<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">
      基本设置
    </h3>
    <div class="box-tools pull-right">
    </div>
  </div>
  <div class="box-body">
        <?php echo $form->code();?>
    
  </div>
    <div class="box-footer clearfix">
      <button type="submit" class="btn btn-primary">
        保存修改
      </button>
    </div>
</div>
    <input type="hidden" name="action" value="save" />
  </form>
<?php
echo $form->js();