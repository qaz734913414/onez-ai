<?php

/* ========================================================================
 * $Id: ai.mode.simplify.php 1902 2016-09-20 23:29:12Z onez $
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
class onezphp_ai_mode_simplify extends onezphp{
  function __construct(){
    
  }
  //模式初始化
  function init(){
    global $G;
    $G['index.button']='<button class="btn btn-info" onclick="onez.dialog_open(\''.onez('ai.device.dialog')->view('dialog').'\',\'1260\',\'720\')">前端访客演示</button>';
  }
  //首页按钮
  function index_button($name='电脑版对话'){
    global $G;
    return '<button class="btn btn-info" onclick="'.onez('dialog')->click(onez('ai.device.dialog')->view('dialog'),'1260','720').'">'.$name.'</button>';
  }
  function menus_mode(){
    $Menu=array (
      array (
        'name' => '自动应答设置',
        'href' => '',
        'icon' => 'fa fa-fw fa-newspaper-o',
      ),
      array (
        'name' => '词库管理',
        'url' => onez('ai.function.rules_text')->href('/rules_text/index.php'),
        'icon' => 'fa fa-fw fa-gear',
      ),
      array (
        'name' => '文字预处理',
        'url' => onez('ai.function.text.prev')->href('/setting.php'),
        'icon' => 'fa fa-fw fa-gear',
      ),
      array (
        'name' => '无应答设置',
        'url' => onez('ai.function.noreply')->href('/setting.php'),
        'icon' => 'fa fa-fw fa-gear',
      ),
    );
    return $Menu;
  }
  //用户输入时触发
  function input($data,$person,$result){
    if($data['type']!='text'){
      return $result;
    }
    $message=$data['message'];
    $output=array();
    $record=onez('db')->open('rules_text')->record("question='$message'");
    if($record){
      foreach($record as $rs){
        $output[]=array(
          'sender'=>'ai',
          'type'=>'text',
          'message'=>$rs['answer'],
        );
      }
    }else{//无应答
      onez('ai.function.noreply')->parse($data,$person,$output);
    }
    $result['output']=$output;
    return $result;
  }
}