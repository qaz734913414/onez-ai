<?php

/* ========================================================================
 * $Id: ai.script.guide.php 3335 2016-09-20 17:34:52Z onez $
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
class onezphp_ai_script_guide extends onezphp{
  function __construct(){
    
  }
  function menus_app(){
    $Menu=array (
      array (
        'name' => '设置指引数据',
        'href' => '/setting.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
      array (
        'name' => '其他设置',
        'href' => '/keyword.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
    );
    return $Menu;
  }
  function script_input($data,$person,&$result){
    if(!$data['message']){
      return;
    }
    $keywords=onez('cache')->option('ai_script_guide_keywords',0);
    $guide=onez('cache')->get('guide');
    if($keywords && $guide){
      foreach(explode("\n",$keywords) as $v){
        $v=trim($v);
        if($v){
          list($id,$keyword)=explode('|',$v);
          if($keyword==$data['message']){
            $item=$this->find($guide,$id);
            onez('ai')->lock($person['id'],'ai.script.guide','多级指引');
            $this->parse($item,$person,$result);
          }
        }
      }
    }
  }
  function script_lock($data,$person,&$result){
    
    if(preg_match('/onez\:\/\/([^\/]+)\/select\/id\/([0-9]+)$/i',$data['message'],$mat)){
      $guide=onez('cache')->get('guide');
      $item=$this->find($guide,$mat[2]);
      $this->parse($item,$person,$result,1);
    }
  }
  function parse($item,$person,&$result,$showme=0){
    if(preg_match('/\[onez=([^\]]+)\]/i',$item['name'],$mat)){
      $su=$mat[1];
      $item['name']=preg_replace('/\[onez=([^\]]+)\]/i','',$item['name']);
      $goid=(int)$su;
      if($goid){
        $guide=onez('cache')->get('guide');
        $goitem=$this->find($guide,$goid);
        if($goitem && $item['id']!=$goid){
          $result['input'][]=array(
            'type'=>'text',
            'message'=>$item['name'],
          );
          $this->parse($goitem,$person,$result,1);
          return;
        }
      }elseif($su=='title'){
      }elseif($su){
        $result=onez('ai')->input(array(
          'type'=>'text',
          'message'=>$su,
        ));
      }
    }
    if($item['children']){
      if($showme){
        $result['input'][]=array(
          'type'=>'text',
          'message'=>$item['name'],
        );
        $item['name']='';
      }
      $options=array();
      foreach($item['children'] as $v){
        $name=preg_replace('/\[onez=([^\]]+)\]/i','',$v['name']);
        $options[]=array(
          'token'=>'onez://'.$this->token.'/select/id/'.$v['id'],
          'name'=>$name,
        );
      }
      $result['output'][]=array(
        'type'=>'select',
        'title'=>$item['name'],
        'options'=>$options,
      );
    }else{
      $result['input'][]=array(
        'type'=>'text',
        'message'=>$item['name'],
      );
      $ai_script_guide_end=onez('cache')->option('ai_script_guide_end',0);
      if($ai_script_guide_end){
        $result['output'][]=array(
          'type'=>'text',
          'message'=>$ai_script_guide_end,
        );
      }
      //已结束，解锁
      onez('ai')->unlock($person['id'],'ai.script.guide');
    }
  }
  function find($data,$id){
    foreach($data as $v){
      if($v['id']==$id){
        return $v;
      }elseif($v['children']){
        return $this->find($v['children'],$id);
      }
    }
    return false;
  }
}