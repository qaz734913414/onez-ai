<?php

/* ========================================================================
 * $Id: ai.script.account.php 8691 2016-09-20 22:54:08Z onez $
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
class onezphp_ai_script_account extends onezphp{
  function __construct(){
    
  }
  function menus_app(){
    $Menu=array (
      array (
        'name' => '登录接口设置',
        'href' => '/login.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
      array (
        'name' => '注册接口设置',
        'href' => '/register.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
      array (
        'name' => '取回密码接口设置',
        'href' => '/findpwd.php',
        'icon' => 'fa fa-fw fa-gear',
      ),
    );
    return $Menu;
  }
  function script_input($data,$person,&$result){
    if(!$data['message']){
      return;
    }
    $item=onez('cache')->get('options');
    $this->check_login($data,$person,$result,$item,'login','登录');
    $this->check_login($data,$person,$result,$item,'register','注册');
    $this->check_login($data,$person,$result,$item,'findpwd','取回密码');
    
  }
  function check_login($data,$person,&$result,$item,$type,$def){
    $api=$item['ai_script_'.$type.'_api'];
    if(!$api){
      return;
    }
    $kw=$item['ai_script_'.$type.'_kw'];
    !$kw && $kw=$def;
    if($data['message']==$kw){
       onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',array(
         'api'=>$api,
         'mode'=>$type,
       ));
       onez('ai')->lock($person['id'],'ai.script.account',$def);
       $this->script_lock($data,$person,$result);
    }
  }
  function script_lock($data,$person,&$result){
    if(!$data['message']){
      return;
    }
    $info=onez('ai.person')->init($person['id'])->info('ai.script.account');
    if(is_null($info) || !$info || !is_array($info)){
      onez('ai.person')->init($person['id'])->attrs_remove('ai.script.account');
      return;
    }
    if($info['mode']=='login'){
      if($info['status']=='username'){
        $info['username']=$data['message'];
        $info['status']='password';
        onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        $result['stop']=1;
        $result['output'][]=array(
          'type'=>'text',
          'message'=>'请输入您的登录密码',
        );
      }elseif($info['status']=='password'){
        $info['password']=$data['message'];
        $api=$info['api'];
        $api=str_replace('${username}',urlencode($info['username']),$api);
        $api=str_replace('${password}',urlencode($info['password']),$api);
        $api=str_replace('${password_md5}',urlencode(md5($info['password'])),$api);
        $data=onez()->post($api);
        $json=json_decode($data,1);
        
        foreach($result['messages'] as $k=>$v){
          $result['messages'][$k]['message']=str_pad('',strlen($info['password']),'*',STR_PAD_LEFT);
        }
        
        if($json['id'] && $json['username']){
          onez('ai.person')->init($person['id'])->attrs_set('userid',$json['id']);
          onez('ai.person')->init($person['id'])->attrs_set('username',$json['username']);
          foreach($json as $k=>$v){
            if(!in_array($k,array('id','username','welcome'))){
              onez('ai.person')->init($person['id'])->attrs_set($k,$v);
            }
          }
          $result['output'][]=array(
            'type'=>'text',
            'message'=>$json['welcome']?$json['welcome']:($json['username'].',您好！欢迎回来！'),
          );
          onez('ai.person')->init($person['id'])->attrs_remove('ai.script.account');
          onez('ai')->unlock($person['id']);
          
          $onez=array();
          $onez['userid']=$json['id'];
          onez('db')->open('person')->update($onez,"id='".$person['id']."'");
        }else{
          $result['output'][]=array(
            'type'=>'text',
            'message'=>'用户名或密码不正确，请重新输入！<br />请输入您的账号：',
          );
          $info['status']='username';
          onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        }
        $result['stop']=1;
      }else{
        $info['status']='username';
        onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        $result['stop']=1;
        $result['output'][]=array(
          'type'=>'text',
          'message'=>'请输入您的登录账号',
        );
      }
    }elseif($info['mode']=='register'){
      if($info['status']=='username'){
        $info['username']=$data['message'];
        $info['status']='password';
        onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        $result['stop']=1;
        $result['output'][]=array(
          'type'=>'text',
          'message'=>'请输入您的登录密码',
        );
      }elseif($info['status']=='password'){
        $info['password']=$data['message'];
        $info['status']='email';
        onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        $result['stop']=1;
        $result['output'][]=array(
          'type'=>'text',
          'message'=>'请输入您的邮箱',
        );
        
        foreach($result['messages'] as $k=>$v){
          $result['messages'][$k]['message']=str_pad('',strlen($info['password']),'*',STR_PAD_LEFT);
        }
        
      }elseif($info['status']=='email'){
        $info['email']=$data['message'];
        $api=$info['api'];
        $api=str_replace('${username}',urlencode($info['username']),$api);
        $api=str_replace('${password}',urlencode($info['password']),$api);
        $api=str_replace('${password_md5}',urlencode(md5($info['password'])),$api);
        $api=str_replace('${email}',urlencode(md5($info['email'])),$api);
        $data=onez()->post($api);
        $json=json_decode($data,1);
        
        if($json['id'] && $json['username']){
          onez('ai.person')->init($person['id'])->attrs_set('userid',$json['id']);
          onez('ai.person')->init($person['id'])->attrs_set('username',$json['username']);
          foreach($json as $k=>$v){
            if(!in_array($k,array('id','username','welcome'))){
              onez('ai.person')->init($person['id'])->attrs_set($k,$v);
            }
          }
          $result['output'][]=array(
            'type'=>'text',
            'message'=>$json['welcome']?$json['welcome']:('恭喜您，注册成功！'),
          );
          onez('ai.person')->init($person['id'])->attrs_remove('ai.script.account');
          onez('ai')->unlock($person['id']);
          $onez=array();
          $onez['userid']=$json['id'];
          onez('db')->open('person')->update($onez,"id='".$person['id']."'");
        }else{
          $result['output'][]=array(
            'type'=>'text',
            'message'=>$json['error']?$json['error']:'注册失败',
          );
          $info['status']='username';
          onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        }
        $result['stop']=1;
      }else{
        $info['status']='username';
        onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        $result['stop']=1;
        $result['output'][]=array(
          'type'=>'text',
          'message'=>'请输入您要注册的登录账号',
        );
      }
    }elseif($info['mode']=='findpwd'){
      if($info['status']=='email'){
        $info['email']=$data['message'];
        $api=$info['api'];
        $api=str_replace('${email}',urlencode($info['email']),$api);
        $data=onez()->post($api);
        $json=json_decode($data,1);
        
        foreach($result['messages'] as $k=>$v){
          $result['messages'][$k]['message']=str_pad('',strlen($info['password']),'*',STR_PAD_LEFT);
        }
        
        if($json['error']){
          $result['output'][]=array(
            'type'=>'text',
            'message'=>$json['error'].'<br />请重新输入您的邮箱：',
          );
          $info['status']='email';
          onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        }else{
          $result['output'][]=array(
            'type'=>'text',
            'message'=>$json['welcome']?$json['welcome']:('密码已成功发送到您的注册邮箱,请查收！'),
          );
          onez('ai.person')->init($person['id'])->attrs_remove('ai.script.account');
          onez('ai')->unlock($person['id']);
        }
        $result['stop']=1;
      }else{
        $info['status']='email';
        onez('ai.person')->init($person['id'])->attrs_set('ai.script.account',$info);
        $result['stop']=1;
        $result['output'][]=array(
          'type'=>'text',
          'message'=>'请输入您的注册邮箱',
        );
      }
    }
  }
}