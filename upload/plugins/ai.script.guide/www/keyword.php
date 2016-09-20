<?php

/* ========================================================================
 * $Id: keyword.php 1456 2016-09-20 16:48:40Z onez $
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
$form->add(array('label'=>'命令说明','type'=>'html','html'=>'
<p>格式:<code>编号|关键词</code></p>
<p>示例:<code>1|多级指引</code>，当用户发送“<code>多级指引</code>”时，触发<code>1</code>对应的指引级别</p>
'));
$form->add(array('label'=>'关键词列表：','type'=>'textarea','key'=>'ai_script_guide_keywords','hint'=>'1|多级指引','notempty'=>''));
$form->add(array('label'=>'指引结束提示语：','type'=>'textarea','key'=>'ai_script_guide_end','hint'=>'','notempty'=>''));
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
      其他设置
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