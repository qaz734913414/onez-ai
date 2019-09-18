<?php

/* ========================================================================
 * $Id: upgrade.php 5746 2016-09-20 09:23:31Z onez $
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
define('ONEZ_APPID','10000003');
define('ONEZ_APPKEY','fe0244157c478476459a3debb8401d83');
class onezphp_upgrade extends onezphp{
  var $spliter='================================';
  function __construct(){
    
  }
  function options(){
    $options=array();
    $options['siteguid']=array('label'=>'产品唯一识别码','type'=>'text','key'=>'siteguid','hint'=>'','notempty'=>'');
    return $options;
  }
  function version(){
    if(file_exists(ONEZ_ROOT.'/config/version')){
      $data=onez()->read(ONEZ_ROOT.'/config/version');
    }
    $version=$this->parse($data);
    return end($version);
  }
  function newversion(){
    $post=array(
      'action'=>'check',
      'siteguid'=>onez('cache')->option('siteguid'),
    );
    $mydata=onez()->post('http://xl.onez.cn/api/site.php',http_build_query($post),array(
      'timeout'=>600,
      'headers'=>array(
        'Authorization: '.$this->auth(),
      )
    ));
    $S=json_decode($mydata,1);
    if($S['error']){
      $version[]=array(
        'version'=>'0.0',
        'time'=>time(),
        'summary'=>$S['error'],
      );
    }elseif($S['none']){
      return $this->version();
    }else{
      $version=$this->parse($S['version']);
      return end($version);
    }
  }
  function parse($data){
    $version=array();
    foreach(explode($this->spliter,$data) as $v){
      $v=trim($v);
      if(!$v){
        continue;
      }
      $item=array('version'=>'','time'=>'','summary'=>array());
      $n=0;
      
      foreach(explode("\n",$v) as $vv){
        $vv=trim($vv);
        if(!$vv){
          continue;
        }
        $n++;
        if($n==1){
          $item['version']=$vv;
        }elseif($n==2){
          $item['time']=$vv;
        }else{
          $item['summary'][]=$vv;
        }
      }
      $item['summary']=implode("\n",$item['summary']);
      $version[]=$item;
    }
    if(!$version){
      $version[]=array(
        'version'=>'1.0',
        'time'=>filemtime(ONEZ_ROOT.'/lib/onezphp.php'),
        'summary'=>'首次发布',
      );
    }
    return $version;
  }
  function auth(){
    if(onez()->exists('cache')){
      $onez_appid=onez('cache')->option('onez_appid',0);
      $onez_appkey=onez('cache')->option('onez_appkey',0);
    }
    if(!$onez_appid || !$onez_appkey){
      $onez_appid=$G['onez_appid'];
      $onez_appkey=$G['onez_appkey'];
    }
    if(!$onez_appid || !$onez_appkey){
      if(defined('ONEZ_APPID') && defined('ONEZ_APPKEY')){
        $onez_appid=ONEZ_APPID;
        $onez_appkey=ONEZ_APPKEY;
      }
    }
    if(onez()->exists('cache')){
      if(!$onez_appid || !$onez_appkey){
        $onez_appid=onez('cache')->option('onez_appid');
        $onez_appkey=onez('cache')->option('onez_appkey');
      }
    }
    return $onez_appid.' '.md5($onez_appkey);
  }
  function find($path,$offset=false){
    if($offset===false){
      $offset=strlen($path)+1;
    }
    $files=array();
    $glob=glob("$path/*");
    if($glob){
      foreach($glob as $v){
        if(is_dir($v)){
          $files=array_merge($files,$this->find($v,$offset));
        }else{
          $filename=substr($v,$offset);
          if(strpos($filename,'cache/')===0){
            continue;
          }
          if(strpos($filename,'plugins/')===0){
            continue;
          }
          if(strpos($filename,'myplugins/')===0){
            continue;
          }
          $files[$filename]=md5_file($v);
        }
      }
    }
    return $files;
  }
  function upgrade(){
    $myfiles=$this->find(ONEZ_ROOT);
    $post=array(
      'action'=>'fetch',
      'siteguid'=>onez('cache')->option('siteguid'),
      'myfiles'=>base64_encode(serialize($myfiles)),
    );
    $mydata=onez()->post('http://xl.onez.cn/api/site.php',http_build_query($post),array(
      'timeout'=>600,
      'headers'=>array(
        'Authorization: '.$this->auth(),
      )
    ));
    if(strpos($mydata,'onez')===0){
      $mydata=substr($mydata,4);
      $mydata=gzuncompress($mydata);
      $pos=0;
      $nFileCount = substr($mydata, $pos, 16) ;
      $pos += 16 ;

      $size = substr($mydata, $pos, 16) ;
      $pos += 16 ;

      $info = substr($mydata, $pos, $size-1) ;
      $pos += $size ;

      $info_array = explode("\n", $info) ;

      $c_file = 0 ;
      $c_dir = 0 ;
      
      $files=array();
      $isok=0;
      foreach($info_array as $str_row){
        list($filename, $attr) = explode("|", $str_row);
        if ( substr($attr,0,6)=="[/dir]"){
          continue;
        }
        if(substr($attr,0,5)=="[dir]"){
          //$files[]=array('dir',$filename);
        }else{
          $files[$filename]=substr($mydata, $pos, $attr);
          $pos += $attr ;
        }
      }
      foreach($files as $filename=>$data){
        $file=ONEZ_ROOT.'/'.$filename;
        onez()->write($file,$data);
      }
      //强制更新插件
      $result=onez('fetch')->check();
      onez('cache')->set('plugins.status',$result);
      foreach($result as $token=>$item){
        if($item['status']=='change'){
          onez('fetch')->get($token,1);
        }
      }
      
      $glob=glob(ONEZ_ROOT.'/cache/css/*.css');
      if($glob){
        foreach($glob as $v){
          @unlink($v);
        }
      }
    }else{
      $json=json_decode($mydata,1);
      
      if(onez()->exists('showmessage',0)){
        if($json['errno']){
          onez('showmessage')->error('['.$json['errno'].']'.$json['error']);
        }else{
          onez('showmessage')->error('[1005]读取网站代码出错');
        }
      }else{
        if($json['errno']){
          exit("[{$json['errno']}]:{$json['error']}");
        }else{
          exit("[1005]:读取插件代码有误");
        }
      }
    }
  }
}