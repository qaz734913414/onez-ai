<?php

/* ========================================================================
 * $Id: ai.php 20184 2016-09-21 01:53:54Z onez $
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
class onezphp_ai extends onezphp{
  var $device;
  function __construct(){
    
  }
  function init($device=false){
    $this->device=$device;
    return $this;
  }
  function info($key=false){
    global $G;
    if($key=='name'){
      $value=onez('cache')->option('myname',0);
      !$value && $value='佳蓝机器人';
    }elseif($key=='avatar'){
      $value=onez('cache')->option('myavatar',0);
      !$value && $value=$this->url.'/images/avatar.png';
    }else{
      $value=onez('cache')->option($key,0);
    }
    return $value;
  }
  function js(){
    $udid=onez()->gp('udid');
    
    $url=$this->url;
    $view=$this->view('');
    $device=$this->device->token;
    
    $post=$_REQUEST;
    unset($post['_view']);
    $post=json_encode($post);
    
    $today=date('Y-n-j');
    
    $name=var_export($this->info('name'),1);
    $avatar=var_export($this->info('avatar'),1);
    
    echo <<<ONEZ
<script type="text/javascript">
var _onez_ai_udid='$udid';
var _onez_ai_url='$url';
var _onez_ai_view='$view';
var _onez_ai_device='$device';
var _onez_ai_post=$post;
var _onez_ai_name=$name;
var _onez_ai_avatar=$avatar;
var _today='$today';
</script>
ONEZ;
    echo onez('ui')->js($this->url.'/js/ai.js');
    echo $this->device->js();
  }
  //启动
  function start($initdata=array()){
    $result=array();
    $udid=$initdata['udid'];
    if(strpos($udid,'onez://udid/')!==false){
      list(,,$udid)=explode('/',$udid);
    }
    if($udid){
      $person=onez('db')->open('person')->one("udid='$udid'");
      if(!$person){
        $udid='';
      }else{
        $onez=array();
        $onez['lasttime']=time();
        $onez['lastip']=onez()->ip();
        $onez['times']=$person['times']+1;
        $onez['lastdata']=serialize($initdata);
        onez('db')->open('person')->update($onez,"id='$person[id]'");
      }
    }
    #新用户
    if(!$udid){
      $udid=uniqid();
      $onez=array();
      $onez['deviceid']=(int)$initdata['deviceid'];
      $onez['udid']=$udid;
      $onez['firstip']=onez()->ip();
      $onez['firsttime']=time();
      $onez['lasttime']=time();
      $onez['lastip']=onez()->ip();
      $onez['times']=1;
      $onez['initdata']=serialize($initdata);
      $onez['lastdata']=serialize($initdata);
      onez('db')->open('person')->insert($onez);
    }
    $result['udid']=$udid;
    
    
    onez('ai.person')->init($udid)->groupids_update();
    return $result;
  }
  function person($key,$value=false){
    if(!$this->person_info){
      $this->person_info=array();
    }
    if($value===false){
      return $this->person_info[$key];
    }else{
      $this->person_info[$key]=$value;
    }
  }
  //给目标增加一个标签
  function tags_add($udid,$tagname){
    $person_tags=onez('db')->open('person_tags')->one("udid='$udid' and tagname='$tagname'");
    if($person_tags){
      return $this;
    }
    $onez=array();
    $onez['udid']=$udid;
    $onez['tagname']=$tagname;
    $onez['time']=time();
    onez('db')->open('person_tags')->insert($onez);
    
    onez('ai.person')->init($udid)->groupids_update();
    return $this;
  }
  //给目标移除一个标签
  function tags_remove($udid,$tagname){
    $person_tags=onez('db')->open('person_tags')->one("udid='$udid' and tagname='$tagname'");
    if(!$person_tags){
      return $this;
    }
    onez('db')->open('person_tags')->delete("udid='$udid' and tagname='$tagname'");
    
    onez('ai.person')->init($udid)->groupids_update();
    
    return $this;
  }
  //判断是否符合标签规则
  function tags_match($tags,$group){
    !$tags && $tags=array();
    $groupTags=explode(',',$group['tags']);
    
    switch($group['type']){
      #当目标同时拥有所有蓝色标签时
      case'all':
        foreach($groupTags as $tag){
          if($tag=='所有目标'){
            return true;
          }
          if(!in_array($tag,$tags)){
            return false;
          }
        }
        return true;
        break;
      #当目标有任意一个蓝色标签时
      case'one':
        foreach($groupTags as $tag){
          if($tag=='所有目标'){
            return true;
          }
          if(in_array($tag,$tags)){
            return true;
          }
        }
        return false;
        break;
      #当目标没有任何一个以下蓝色标签时
      case'allnot':
        foreach($groupTags as $tag){
          if($tag=='所有目标'){
            return false;
          }
          if(in_array($tag,$tags)){
            return false;
          }
        }
        return true;
        break;
    }
    return false;
  }
  //标记某条消息已处理
  function reply($msgid,$replyid,$status='reply'){
    if(!$msgid){
      return $this;
    }
    $onez=array();
    $onez['status']=$status;
    $onez['replyid']=(int)$replyid;
    $onez['replytime']=time();
    $onez['askinfo']='';
    onez('db')->open('history')->update($onez,"id='$msgid'");
    
    $this->person('is_reply',1);
    return $this;
  }
  //翻译特殊语言
  function tpl($tpl,$message){
    $text=trim($tpl);
    $vars=array();
    $s='【([^】]+)】';
    if(@preg_match_all("/$s/is",$text,$mat)){
      $reg=@preg_replace("/$s/is",'(.+?)',$text);
      if(@preg_match_all("/$reg/isU",$message,$mat2)){
        
        foreach($mat[1] as $k=>$m){
          $vars[$mat[1][$k]]=$mat2[$k+1][0];
        }
      }
    }
    return $vars;
  }
  function lock($person_id,$app,$name){
    if(!onez()->exists($app)){
      return;
    }
    onez('ai.person')->init($person_id)->attrs_set('lock',$app)->attrs_set('lockname',$name);
    return $this;
  }
  function unlock($person_id){
    onez('ai.person')->init($person_id)->attrs_remove('lock')->attrs_remove('lockname');
    return $this;
  }
  //请求ai发送一条消息
  function aisay($data){
    global $G;
    $result=array('status'=>'ok');
    #目标信息识别
    if(!$data['udid']){
      $result['status']='error';
      $result['message']='无效请求[udid]';
      return $result;
    }
    $udid=$data['udid'];
    $person=onez('db')->open('person')->one("udid='$data[udid]'");
    #目标不存在
    if(!$person){
      $result['status']='error';
      $result['message']='无效请求[person]';
      return $result;
    }
    onez('ai.person')->init($data['udid'])->tell($data,$result);
    return $result;
  }
  //发送一条信息
  function input($data){
    global $G;
    $result=array('status'=>'ok');
    #目标信息识别
    if(!$data['udid']){
      $result['status']='error';
      $result['message']='无效请求[udid]';
      return $result;
    }
    $udid=$data['udid'];
    $person=onez('db')->open('person')->one("udid='$data[udid]'");
    #目标不存在
    if(!$person){
      $result['status']='error';
      $result['message']='无效请求[person]';
      return $result;
    }
    
    $result['messages']=array();
    if(!$data['auto'] && strpos($data['message'],'onez://')!==0){
      $onez=array();
      $onez['udid']=$data['udid'];
      $onez['status']='ask';
      $onez['replyid']='0';
      $onez['you']='ai';
      $onez['action']='person';
      $onez['type']=$data['type'];
      $onez['deviceid']=$person['deviceid'];
      $onez['data']=serialize($data);
      $onez['text']=$data['message'];
      $onez['time']=time();
      $onez['ip']=onez()->ip();
      $onez['isread']=1;
      $msgid=$G['msgid']=onez('db')->open('history')->insert($onez);
      
      $tmpid=$data['tmpid'];
      unset($data['tmpid']);
      $msg=$this->msg_format($msgid,1);
      $msg['tmpid']=$tmpid;
      $result['messages'][]=$msg;
      $this->person('msgid',$msgid);
    }else{
      if($data['auto']){
        //是否已打招呼
        $hello=onez('ai.person')->init($data['udid'])->info('hello');
        onez('ai.person')->init($data['udid'])->attrs_set('hello','1');
      }
    }
    //锁定模式
    $lock=onez('ai.person')->init($data['udid'])->info('lock');
    if($lock){
      if($data['message']=='exit'||$data['message']=='quit'||$data['message']=='退出'){
        $lockname=onez('ai.person')->init($data['udid'])->info('lockname');
        $result['output'][]=array(
          'sender'=>$lock,
          'type'=>'text',
          'message'=>'已退出'.$lockname.'模式',
        );
        onez('ai.person')->init($data['udid'])->attrs_remove('lock')->attrs_remove('lockname');
        
      }else{
        onez($lock)->script_lock($data,$person,$result);
      }
    }else{
      if($data['message'] && preg_match('/onez\:\/\/([^\/]+)\//i',$data['message'],$mat)){
        $apptoken=$mat[1];
        if(onez()->exists($apptoken)){
          onez($apptoken)->script_lock($data,$person,$result);
        }
      }
    }
    
    
    if(!$result['output']){
      if($G['apps']['script']){
        foreach($G['apps']['script'] as $apptoken=>$app){
          if(onez()->exists($apptoken)){
            onez($apptoken)->script_input($data,$person,$result);
            if($result['stop']){
              break;
            }
          }
        }
      }
    }
    
    if($hello){
      return $result;
    }
    
    if($G['mode'] && !$result['output'] && !$result['stop']){
      if($data['type']=='text'){
        onez('ai.function.text.prev')->parse($data['message']);
      }
      $result=onez($G['mode'])->input($data,$person,$result);
    }
    if($result['input']){
      $input=end($result['input']);
      //foreach($result['input'] as $input){
        $onez=array();
        $onez['udid']=$data['udid'];
        $onez['status']='ask';
        $onez['replyid']='0';
        $onez['you']='ai';
        $onez['action']='person';
        $onez['type']=$input['type'];
        $onez['deviceid']=$person['deviceid'];
        $onez['data']=serialize($input);
        $onez['text']=$input['message'];
        $onez['time']=time();
        $onez['ip']=onez()->ip();
        $onez['isread']=1;
        $msgid=$G['msgid']=onez('db')->open('history')->insert($onez);
        
        $msg=$this->msg_format($msgid,1);
        $result['messages'][]=$msg;
      //}
      unset($result['input']);
    }
    if($result['output']){
      //多个回复，随机取一条
      if(count($result['output'])>1){
        $output=$result['output'][array_rand($result['output'])];
      }else{
        $output=$result['output'][0];
      }
      unset($result['output']);
      
      $onez=array();
      $onez['udid']=$data['udid'];
      $onez['status']='reply';
      $onez['replyid']=$msgid;
      $onez['you']=$output['sender'];
      $onez['action']='ai';
      $onez['type']=$output['type'];
      $onez['deviceid']=$person['deviceid'];
      
      unset($output['sender']);
      $onez['data']=serialize($output);
      $onez['text']=$output['message'];
      $onez['time']=time();
      $onez['ip']=onez()->ip();
      $onez['isread']=1;
      $msgid_ai=onez('db')->open('history')->insert($onez);
      
      $msg=$this->msg_format($msgid_ai,1);
      $result['messages'][]=$msg;
    }
    return $result;
    /*
    $result['messages']=array();
    if(!$data['auto']){
      $onez=array();
      $onez['udid']=$data['udid'];
      $onez['status']='ask';
      $onez['replyid']='0';
      $onez['you']='ai';
      $onez['action']='person';
      $onez['type']=$data['type'];
      $onez['deviceid']=$person['deviceid'];
      $onez['data']=serialize($data);
      $onez['text']=$data['message'];
      $onez['time']=time();
      $onez['ip']=onez()->ip();
      $onez['isread']=1;
      $msgid=$G['msgid']=onez('db')->open('history')->insert($onez);
      
      $tmpid=$data['tmpid'];
      unset($data['tmpid']);
      $msg=$this->msg_format($msgid,1);
      $msg['tmpid']=$tmpid;
      $result['messages'][]=$msg;
      $this->person('msgid',$msgid);
    }
    
    $onez=array();
    $onez['lasttime']=time();
    onez('db')->open('person')->update($onez,"udid='{$data['udid']}'");
    
    
    $this->person_info=json_decode($person['lastdata'],1);
    #目标的标签
    $person_tags=onez('db')->open('person_tags')->record("udid='$data[udid]'");
    foreach($person_tags as $rs){
      if($rs['tagname']=='pause'||$rs['tagname']=='暂停'){
        return $result;
      }
      $this->person_info['tags'][]=$rs['tagname'];
    }
    $this->person('udid',$data['udid']);
    
    
    $messages=$result['messages'];
    $result['messages']=array();
    
    $noreply=array();
    
    if($person['mode']=='auto'){
      if(strlen($person['groupids'])>0){
        $deviceid=(int)$data['deviceid'];
        //遍历规则分类
        $groups=onez('db')->open('rules_group')->record("groupid in ($person[groupids])");
        foreach($groups as $group){
          if($group['script_noreply'] && !in_array($group['script_noreply'],$noreply)){
            $noreply[]=$group['script_noreply'];
          }
          //遍历分类内的规则
          $rules=onez('db')->open('rules')->record("deviceid='$deviceid' and groupid='$group[groupid]' order by step");
          foreach($rules as $rule){
            $match=onez($rule['input_type'])->match($data,json_decode($rule['rule'],1));
            if($match){
              $this->person('groupid',$group['groupid']);
              $this->person('ruleid',$rule['ruleid']);
              $doit=$this->doit($data,json_decode($rule['doit'],1));
              if($doit && is_array($doit)){
                $result=array_merge($result,$doit);
              }
            }
          }
        }
      }
      if(!$data['auto'] && onez('db')->open('history')->rows("udid='$udid' and isread=0")==0){
        list($dtoken)=explode('|',$noreply[array_rand($noreply)]);
        onez($dtoken)->request($data['message'],$person['id']);
      }
      //取出记录中的新消息
      $T=onez('db')->open('history')->record("udid='$udid' and isread=0 order by id");
      $msgids=array();
      foreach($T as $rs){
        $msgids[]=$rs['id'];
      }
      $result['messages']=$this->msg_format($T);
      if($msgids){
        $msgids=implode(',',$msgids);
        $onez=array();
        $onez['isread']=1;
        $onez['readtime']=time();
        onez('db')->open('history')->update($onez,"id in ($msgids)");
      }
      
      
      //多个回复，随机取一条
      if(count($result['messages'])>1){
        $result['messages']=array($result['messages'][array_rand($result['messages'])]);
      }
    }
    $result['messages']=array_merge($messages,$result['messages']);
    return $result;
    */
  }
  //处理信息
  function doit($data,$doit){
    $result=array();
    if(!$doit || !is_array($doit)){
      return false;
    }
    $udid=$this->person('udid').'';
    if(!$udid){
      if($data['person_id']){
        $person=onez('ai.person')->init($data['person_id']);
        $udid=$person->udid;
      }
    }
    if(!$udid){
      $person_id=(int)onez()->gp('person_id');
      if($person_id){
        $person=onez('ai.person')->init($person_id);
        $udid=$person->udid;
      }
    }
    $person=onez('ai.person')->init($udid);
    foreach($doit as $v){
      if($v['type']=='addtag'){
        $this->tags_add($udid,$v['tagname']);
      }elseif($v['type']=='removetag'){
        $this->tags_remove($udid,$v['tagname']);
      }elseif($v['type']=='setattr'){
        onez('ai.attr')->doit($result,$v,$person->id);
      }elseif($v['type']=='script'){
        onez($v['script'])->doit($result,$v,$person->id);
      }elseif($v['type']=='output'){
        onez('ai.output')->doit($result,$v,$person->id);
      }
    }
    return $result;
  }
  //推送一条信息
  function push($udid,$msg,$text='',$extra=array(),$status='push'){
    global $G;
    $result=array();
    $person=onez('db')->open('person')->one("udid='$udid'");
    #目标不存在
    if(!$person){
      $result['status']='error';
      $result['message']='无效请求[person]';
      return $result;
    }
    
    $onez=array();
    $onez['udid']=$udid;
    $onez['you']='ai';
    $onez['action']='ai';
    $onez['type']=$msg['type'];
    $onez['deviceid']=$person['deviceid'];
    $onez['data']=serialize($msg);
    $onez['text']=$text?$text:$msg['message'];
    $onez['time']=time();
    $onez['ip']=onez()->ip();
    $onez['isread']=0;
    
    $onez['status']='ai';
    $onez['replyid']=0;
    $onez['replytime']=0;
    
    if($extra && is_array($extra)){
      $onez=array_merge($onez,$extra);
    }
    $result['msgid']=onez('db')->open('history')->insert($onez);
    if($status=='reply'){
      $msgid=$this->person('msgid');
      if(!$msgid){
        $msgid=(int)onez()->gp('msgid');
      }
      if(!$msgid){
        $msgid=$G['msgid'];
      }
      $this->reply($msgid,$result['msgid'],'auto');
    }
    return $result;
  }
  //自动学习
  function study($msgid,$answer,$p,$reply=0){
    global $G;
    $person=onez('ai.person')->init($p);
    $groupid=(int)onez()->gp('groupid');
    if(!$groupid){
      $tags=$person->info('tags');
      //遍历规则分类
      $groups=onez('db')->open('rules_group')->record("");
      foreach($groups as $group){
        //判断是否符合标签规则
        if(!onez('ai')->tags_match($tags,$group)){
          continue;
        }
        $groupid=$group['groupid'];
      }
    }
    if(!$groupid){
      return $this;
    }
    !$groupid && onez('showmessage')->error('没有符合此用户的规则分类，请先去后台增加相应的分类');
    $text=$msgid;
    if(is_numeric($msgid)){
      $history=$this->msg_format($msgid,1);
      if(!$history['message']){
        return $this;
      }
      $text=$history['message'];
    }
    
    $onez=array();
    $onez['groupid']=$groupid;
    $onez['deviceid']=$person->person['deviceid'];
    $onez['summary']=$text;
    $onez['input_type']='ai.input.text';
    $onez['input_typename']='文本';
    $onez['rule']=json_encode(array(
      'same'=>$text,
    ));
    $onez['doit']=json_encode(array(
      array(
        'type'=>'output',
        'output'=>'ai.output.text',
        'text'=>'输出<code>文本</code>数据<code>[value]='.$answer.'</code>;',
        'value'=>$answer,
      ),
    ));
    
    $onez['updatetime']=time();
    $onez['hash']=md5($onez['groupid'].$onez['deviceid'].$onez['rule'].$onez['doit']);
    
    if(onez('db')->open('rules')->rows("hash='{$onez['hash']}'")>0){
      return $this;
    }
    $onez['userid']=$G['userid'];
    $onez['add_info']='<code>系统</code>';
    $onez['addtype']='system';
    $onez['addtime']=time();
    onez('db')->open('rules')->insert($onez);
    
    if($reply){
      
      $msg=array(
        'type'=>'text',
        'pos'=>'you',
        'time'=>time(),
        'message'=>$answer,
      );
      onez('ai')->push($person->udid,$msg,$text,array(
        'you'=>$G['grade'].'-'.$G['userid'],
      ));
      onez()->output($result);
    }
    return $this;
  }
  //读取未读消息
  function newmsg($udid,$msgid=0){
    if($msgid==0){
      $T=onez('db')->open('history')->record("udid='$udid' and isread=0 order by id");
    }else{
      $T=onez('db')->open('history')->record("udid='$udid' and id>$msgid order by id");
    }
    
    $this->msg_format($T);
    return $T;
  }
  //读取消息记录
  function history($udid,$msgid=0){
    //$page=max(1,$page);
    //$pagesize=10;
    if($msgid){
      $xxx.=" and id<$msgid";
    }
    $T=onez('db')->open('history')->record("udid='$udid'$xxx order by id desc limit 10");
    $this->msg_format($T);
    return $T;
  }
  //格式化消息
  function msg_format(&$T,$is_id=0){
    if($is_id){
      $T=onez('db')->open('history')->record("id='$T'");
      $this->msg_format($T);
      return $T[0];
    }
    $msgs=array();
    foreach($T as $rs){
      $msg=unserialize($rs['data']);
      unset($msg['tmpid']);
      $msg['msgid']=$rs['id'];
      $msg['time']=$rs['time'];
      $msg['action']=$rs['action'];
      $msg['status']=$rs['status'];
      $msg['you']=$rs['you'];
      $msgs[]=$msg;
    }
    $T=$msgs;
    return $T;
  }
  //设置已读
  function isread($msgids){
    if($msgids){
      $onez=array();
      $onez['isread']=1;
      $onez['readtime']=time();
      onez('db')->open('history')->update($onez,"id in ($msgids)");
    }
    return $this;
  }
}