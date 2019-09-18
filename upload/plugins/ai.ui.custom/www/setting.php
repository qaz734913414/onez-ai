<?php

/* ========================================================================
 * $Id: setting.php 1504 2016-09-20 17:11:18Z onez $
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
$form->add(array('label'=>'对话框顶部代码：','type'=>'textarea','key'=>'ai_ui_custom_header','hint'=>'','notempty'=>''));
$form->add(array('label'=>'对话框左侧代码：','type'=>'textarea','key'=>'ai_ui_custom_left','hint'=>'','notempty'=>''));
$form->add(array('label'=>'对话框右侧代码：','type'=>'textarea','key'=>'ai_ui_custom_right','hint'=>'','notempty'=>''));
$form->add(array('label'=>'对话框底部代码：','type'=>'textarea','key'=>'ai_ui_custom_footer','hint'=>'','notempty'=>''));
#处理提交
if($onez=$form->submit()){
  ob_clean();
  foreach($onez as $k){
    $onez[$k]=$_REQUEST[$k];
  }
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