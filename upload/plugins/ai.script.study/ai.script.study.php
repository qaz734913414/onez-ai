<?php

/* ========================================================================
 * $Id: ai.script.study.php 1369 2016-09-20 15:49:28Z onez $
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
class onezphp_ai_script_study extends onezphp{
  function __construct(){
    
  }
  function menus_app(){
    $Menu=array (
      array (
        'name' => '基本设置',
        'href' => '/setting.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
    );
    return $Menu;
  }
  function script_input($data,$person,&$result){
    if(!$data['message']){
      return;
    }
    $cmd=onez('cache')->option('ai_script_study_cmd',0);
    !$cmd && $cmd='【问题】###【答案】';
    $tip=onez('cache')->option('ai_script_study_tip',0);
    !$tip && $tip='好的，我记下了';
    $var=onez('ai')->tpl($cmd,$data['message']);
    if($var['问题'] && $var['答案']){
      //防止重复
      $record=onez('db')->open('rules_text')->record("question='".$var['问题']."'");
      if($record){
        foreach($record as $rs){
          if($rs['answer']==$var['答案']){
            return;
          }
        }
      }
      $onez=array();
      $onez['question']=$var['问题'];
      $onez['answer']=$var['答案'];
      $onez['time']=time();
      $onez['groupid']=(int)$G['groupid'];
      onez('db')->open('rules_text')->insert($onez);
      $result['stop']=1;
      $result['output'][]=array(
        'sender'=>'ai.script.study',
        'type'=>'text',
        'message'=>$tip,
      );
    }
  }
}