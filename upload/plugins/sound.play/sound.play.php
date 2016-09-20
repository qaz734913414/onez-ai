<?php

/* ========================================================================
 * $Id: sound.play.php 1532 2016-09-14 07:34:54Z onez $
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
class onezphp_sound_play extends onezphp{
  function __construct(){
    
  }
  function sounds(){
    $item=onez('cache')->get('options');
    if(!isset($item['sound_play_sounds'])){
      $item['sound_play_sounds']='newmsg|./sounds/newmsg.mp3|新消息
online|./sounds/online.mp3|用户上线';
    }
    return $item['sound_play_sounds'];
  }
  function options(){
    $options=array();
    $options['sound_play_sounds']=array('label'=>'声音列表','type'=>'textarea','key'=>'sound_play_sounds','hint'=>'','notempty'=>'','value'=>$this->sounds());
    $html=<<<ONEZ
<p>格式为：<code>标识|声音地址</code>，每行填写一个</p>
<p><code>./</code>表示相对插件目录</p>
<p><code>/</code>表示相对网站目录</p>
<p></p>
ONEZ;
    $options['sound_play_sounds_help']=array('label'=>'声音列表说明','type'=>'html','html'=>$html);
    return $options;
  }
  function init(){
    echo onez('ui')->js($this->url.'/js/sound.play.js');
    $sounds=$this->sounds();
    $arr=array();
    foreach(explode("\n",$sounds) as $v){
      $v=trim($v);
      if(!$v){
        continue;
      }
      list($key,$url,$summary)=explode('|',$v);
      if($key && $url){
        if(substr($url,0,2)=='./'){
          $url=$this->url.substr($url,1);
        }elseif(substr($url,0,1)=='/'){
          $url=$this->homepage().$url;
        }
        $arr[$key]=$url;
      }
    }
    echo'<script type="text/javascript">onez.sound.init('.json_encode($arr).');</script>';
  }
}