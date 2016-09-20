(function(ai) {
  ai.max_msgid=0;
  ai.min_msgid=-1;
  //输出一条消息
  ai.print=function(msg,orderby){
    if(typeof msg.msgid!='undefined'){
      if($('.msg-item[data-msgid="'+msg.msgid+'"]').length>0){
        return;
      }
      msg.msgid=parseInt(msg.msgid);
    }else{
      msg.msgid=0;
    }
    if(msg.msgid>ai.max_msgid){
      ai.max_msgid=msg.msgid;
    }
    if(ai.min_msgid==-1){
      ai.min_msgid=msg.msgid;
    }
    if(msg.msgid<ai.min_msgid){
      ai.min_msgid=msg.msgid;
    }
    if(typeof orderby=='undefined'){
      orderby='asc';
    }
    if(typeof msg.time=='undefined'){
      msg.time='';
    }
    if(typeof msg.action=='undefined'){
      msg.action='ai';
    }
    if(msg.action=='ai'){
      msg.pos='me';
      msg.nick=ai.name+'('+msg.you+')';
    }else{
      msg.pos='you';
      msg.nick=ai.person.name;
    }
    if(typeof msg.type!='undefined' && msg.type=='image'){
      msg.message='<img src="'+msg.message+'" class="image" />';
    }
    var div=$('<div class="msg-item msg-pos-'+msg.pos+'" />').attr('data-msgid',msg.msgid);
    
    var time=ai.time(msg.time);
    
    var usr=$('<div class="msg-item-usr" />').html(msg.nick+'\t'+time);
    usr.appendTo(div);
    var message=$('<div class="msg-item-message" />').html(msg.message);
    message.appendTo(div);
    
    
    var asrule=$('#asrule').is(':checked');
    if(asrule && msg.action!='ai'){
      if(msg.status=='ask'){
        $('<a href="javascript:;" class="_link _link_reply btn btn-xs btn-danger" onclick="onez.ai.reply(\''+msg.msgid+'\')">未处理</a>').appendTo(message);
        div.addClass('is-ask');
      }
    }
    if(orderby=='asc'){
      div.appendTo('#showbox');
      if(asrule && $('#_link_reply_doing').length<1){
        if($('#_link_reply_doing').length<1){
          if(message.find('._link_reply').length>0){
            onez.ai.reply(msg.msgid);
          }
        }
      }
      ai.tobottom();
    }else{
      div.prependTo('#showbox');
    }
    
    ai.active();
    
    div.find('img').bind('load',ai.tobottom);
  };
  ai.doit_reset=function(){
    if(typeof ai.persons[ai.person_id]!='undefined'){
      _deviceid=ai.persons[ai.person_id].person.deviceid;
      _personid=ai.person_id;
    }
  };
  ai.doit=function(){
    
  };
  //滚动到最底部
  ai.tobottom=function(){
    $('#showbox').scrollTop($('#showbox')[0].scrollHeight);
  };
  //回复一条
  ai.reply=function(msgid){
    $('#_link_reply_doing').remove();
    var msgdiv=$('.msg-item[data-msgid="'+msgid+'"]');
    var div=$('<div id="_link_reply_doing" />').attr('data-msgid',msgid).ani('bounceInUp');
    //var h3=$('<h3 />').html('文本内容').appendTo(div);
    var p=$('<p />').html($.trim(msgdiv.find('.msg-item-message').clone().find('._link').remove().end().text())).appendTo(div);
    var btns=$('<div class="btns"></div>').appendTo(div);
    var p2=$('<p class="help" />').html('您也可以通过直接回复快速存为“文本一对一”规则').appendTo(div);
    $('<button class="btn btn-primary">存为自定义规则</button>').click(function(){
      miniwin(_onez_ai_view_device+'design&msgid='+msgid+'&person_id='+ai.person_id+'&text='+encodeURIComponent(p.text()),'存为规则');
      div.remove();
      msgdiv.removeClass('reply-doing');
    }).appendTo(btns);
    $('<button class="btn btn-warning">忽略</button>').click(function(){
      
      ai.post('cancel',{msgid:msgid},function(data){
        
      });
      div.remove();
      msgdiv.removeClass('reply-doing is-ask').find('._link_reply').remove();
    }).appendTo(btns);
    $('<button class="btn btn-warning">忽略所有</button>').click(function(){
      
      ai.post('cancel',{msgid:'all',person_id:ai.person_id},function(data){
        
      });
      div.remove();
      $('.msg-item').removeClass('reply-doing is-ask');
      $('._link_reply').remove();
    }).appendTo(btns);
    $('<button class="btn btn-danger">关闭</button>').click(function(){
      div.remove();
      msgdiv.removeClass('reply-doing');
    }).appendTo(btns);
    div.appendTo('body');
    msgdiv.addClass('reply-doing');
  };
  //发送当前输入区的内容
  ai.sendinput=function(){
    if(ai.person_id==0){
      alert('请先选择用户');
      return;
    }
    var message=$.trim($('#inputbox').val());
    if(message.length<1){
      return;
    }
    var msg={
      type:'text',
      message:message,
    }
    $('#inputbox').val('').get(0).focus();
    msg.person_id=ai.person_id;
    var asrule=$('#asrule').is(':checked');
    if(asrule){
      var _link_reply_doing=$('#_link_reply_doing');
      if(_link_reply_doing.length>0){
        msg.msgid=_link_reply_doing.attr('data-msgid');
        msg.asrule=1;
        _link_reply_doing.remove();
        $('.msg-item[data-msgid="'+msg.msgid+'"]').removeClass('reply-doing is-ask').find('._link_reply').remove();
      }
    }
    ai.send(msg);
  };
  ai.recv(ai.print);
  ai.send(function(msg){
    ai.post('send',msg,function(data){
      if(typeof data.messages!='undefined'){
        for(var i=0;i<data.messages.length;i++){
          var msg=data.messages[i];
          ai.print(msg);
        }
      }
    });
  });
  //调整窗口后跳到最底部
  onez.resize(ai.tobottom);
  
  
  ai.person_udid='';
  ai.persons={};
  ai.person={};
  ai.Person=function(udid,box){
    var _this_=this;
    if(typeof box=='undefined'){
      box='#userlist';
    }
    
    if(typeof udid=='object'){
      this.person=udid;
      this.udid=this.person.udid;
      this.id=this.person.udid;
      udid=this.udid;
    }else{
      this.udid=udid;
      this.person={};
      this.id=udid;
    }
    
    this.box=box;
    this.name='';
    this.mode='auto';
    this.online=1;
    this.setMode=function(mode){
      _this_.mode=mode;
      if(mode=='user'){
        $('#btn-mode').removeClass('btn-info').addClass('btn-warning').html('当前为人工模式');
      }else{
        $('#btn-mode').removeClass('btn-warning').addClass('btn-info').html('当前为自动模式');
      }
    };
    this.update=function(person){
      
      for(var k in person){
        _this_.person[k]=person[k];
      }
      
      if(typeof person.avatar!='undefined'){
        _this_.ui.find('.onez-person-avatar').css({'background-image':'url('+person.avatar+')'});
      }
      if(typeof person.nickname!='undefined'){
        _this_.name=person.nickname;
        _this_.ui.find('.onez-person-nickname').html(person.nickname);
      }
      if(typeof person.summary!='undefined'){
        _this_.ui.find('.onez-person-summary').html(person.summary);
      }
      if(typeof person.status!='undefined'){
        _this_.ui.find('.onez-person-status').html(person.status);
      }
    };
    this.show=function(){
      var person=_this_.person;
      if(_this_.ui){
        _this_.ui.remove();
      }
      _this_.setMode(person.mode);
      _this_.ui=$('<div class="onez-person"></div>');
      $('<div class="onez-person-avatar"></div>').appendTo(_this_.ui);
      $('<div class="onez-person-nickname"></div>').appendTo(_this_.ui);
      $('<div class="onez-person-summary"></div>').appendTo(_this_.ui);
      $('<div class="onez-person-status"></div>').appendTo(_this_.ui);
      _this_.ui.attr('data-person',_this_.id);
      if(_this_.box=='#userlist'){
        _this_.ui.prependTo(_this_.box)
      }else if(_this_.box=='#lastlist'){
        _this_.ui.appendTo(_this_.box)
      }
      
      
      _this_.ui.click(function(){
        ai.open(_this_.id);
      });
      
      if(typeof person.avatar!='undefined'){
        _this_.ui.find('.onez-person-avatar').css({'background-image':'url('+person.avatar+')'});
      }
      if(typeof person.nickname!='undefined'){
        _this_.name=person.nickname;
        _this_.ui.find('.onez-person-nickname').html(person.nickname);
      }
      if(typeof person.summary!='undefined'){
        _this_.ui.find('.onez-person-summary').html(person.summary);
      }
      if(typeof person.status!='undefined'){
        _this_.ui.find('.onez-person-status').html(person.status);
      }
      
      if(_this_.box=='#userlist'){
        if($('#justlist .onez-person[data-person="'+_this_.id+'"]').length<1){
          _this_.ui.clone().click(function(){
            ai.open(_this_.id);
          }).prependTo('#justlist');
        }
      }
    };
    this.remove=function(){
      if(ai.person_udid==_this_.udid){
        ai.close();
      }
      $('[data-person="'+_this_.udid+'"]').remove();
      if(typeof ai.persons[_this_.udid]!='undefined'){
        delete ai.persons[_this_.udid];
      }
    };
    this.input=function(text){
      if(ai.person_udid==_this_.udid){
        
      }
    };
    this.newmsg=0;
    this.print=function(msg){
      $('[data-person="'+_this_.udid+'"] .badge').remove();
      if(ai.person_udid==_this_.udid){
        ai.print(msg,'asc');
        _this_.newmsg=0;
      }else{
        _this_.newmsg++;
        $('<span class="badge">'+_this_.newmsg+'</span>').appendTo('[data-person="'+_this_.udid+'"] .onez-person-status');
      }
      if(msg.status=='ask'){
        onez.sound.play('newmsg');
      }
    };
    
    if(typeof _this_.person.udid=='undefined'){
      ai.post('person_info',{udid:_this_.udid},function(data){
        _this_.person=data;
        _this_.show();
      });
    }else{
      _this_.show();
    }
  };
  //打开对话框
  ai.open=function(person_udid){
    if(typeof ai.persons[person_udid]=='undefined'){
      return;
    }
    var person=ai.persons[person_udid];
    if(person_udid==ai.person_udid){
      $('.onez-person[data-person="'+person_udid+'"]').ani('shake');
      return;
    }
    if(ai.person_udid!=''){
      ai.close();
    }
    ai.is_last=false;
    ai.is_busy=false;
    ai.max_msgid=0;
    ai.min_msgid=0-1;
    ai.person=person;
    person.newmsg=0;
    $('.onez-person[data-person="'+person_udid+'"]').addClass('current');
    $('.onez-person[data-person="'+person_udid+'"]').ani('rubberBand');
    $('.onez-person[data-person="'+person_udid+'"] .badge').remove();
    ai.person_udid=person_udid;
    
    ai.post('person_history',{udid:ai.person_udid},function(data){
      if(data.messages){
        for(var i=0;i<data.messages.length;i++){
          var msg=data.messages[i];
          ai.print(msg,'desc');
        }
      }
      if(data.tip){
        $('.onez-tip').html(data.tip);
      }
      $('#inputbox').attr('disabled',false).removeClass('disabled');
      $(window).trigger('resize');
      $('.onez-btns').show();
      $('#inputbox').val('').get(0).focus();
      ai.moreinfo();
    });
  };
  ai.info_format=function(key,value){
    if(typeof value=='object' || typeof value=='undefined'){
      return'';
    }
    if(key=='deviceid'||key=='udid'){
      return'';
    }
    if(typeof value=='string'){
      if(value.indexOf('http://')!=-1 || value.indexOf('https://')!=-1){
        value='<a href="'+value+'" target="_blank">'+value+'</a>';
      }
    }
    var html='';
    html+='<dt>'+key+':</dt>';
    html+='<dd>'+value+'</dd>';
    return html;
  };
  ai.moreinfo=function(){
    ai.post('person_more',{udid:ai.person_udid},function(data){
      var html='';
      if(data.info){
        var info=data.info;
        if(info.tags){
          html+='<p class="tags">';
          for(var i=0;i<info.tags.length;i++){
            html+='<span class="btn btn-xs btn-primary">'+info.tags[i]+'</span>';
          }
          html+='</p>';
        }
        
        html+='<h3>智能属性</h3>';
        html+='<dl class="dl-horizontal">';
        for(var k in info){
          if(k=='tags' || k=='initinfo' || k=='lastinfo' || k=='device'){
            continue;
          }
          html+=ai.info_format(k,info[k]);
        }
        html+='</dl>';
        
        if(data.info.lastinfo){
          html+='<h3>本次信息</h3>';
          html+='<dl class="dl-horizontal">';
          for(var k in data.info.lastinfo){
            html+=ai.info_format(k,data.info.lastinfo[k]);
          }
          html+='</dl>';
        }
        if(data.info.initinfo){
          html+='<h3>首次信息</h3>';
          html+='<dl class="dl-horizontal">';
          for(var k in data.info.initinfo){
            html+=ai.info_format(k,data.info.initinfo[k]);
          }
          html+='</dl>';
        }
      }
      $('#moreinfo').html(html);
      
    });
  };
  //关闭当前对话框
  ai.close=function(){
    $('#showbox,#inputbox,#moreinfo').empty();
    $('#inputbox').attr('disabled',true).val('请先选择用户').addClass('disabled');
    $('.onez-tip,.onez-btns').hide();
    $(window).trigger('resize');
    if(ai.person_udid==''){
      return;
    }
    if(typeof ai.persons[ai.person_udid]!='undefined'){
      $('.onez-person[data-person="'+ai.person_udid+'"]').removeClass('current');
    }
    ai.person={};
    ai.person_udid='';
  };
  //启动第一步，读取未读消息
  ai.newmsg=function(callback){
    ai.post('newmsg',{person_ids:ai.person_ids.join(','),udid:ai.person_udid,msgid:ai.max_msgid},function(data){
      if(data.messages){
        var newmsgids=[];
        for(var i=0;i<data.messages.length;i++){
          var msg=data.messages[i];
          newmsgids.push(msg.msgid);
          ai.print(msg);
        }
        if(newmsgids.length>0){
          ai.isread(newmsgids);
        }
        
      }
      if(typeof callback=='function'){
        callback();
      }
    });
  };
  //设置消息已读
  ai.isread=function(msgids){
    ai.post('isread',{msgids:msgids.join(',')},function(data){
    });
  };
  
  
  
  //启动
  $('#sendbtn').click(ai.sendinput);
  $('#inputbox').keydown(function(e){
    var key = e.which ? e.which : e.keyCode;
    if(key==13){
      ai.sendinput();
      e.cancelBubble=true;
      e.preventDefault();
      e.stopPropagation();
    }
  });
  //ai.newmsg(ai.history);
  ai.postobj.grade=_onez_ai_grade;
  ai.close();
  ai.init();
  ai.update(ai.newmsg);
  
  //自动加载历史记录
  ai.is_last=false;
  ai.is_busy=false;
  $('#showbox').scroll(function(){
    if(ai.is_last || ai.is_busy){
      return;
    }
    if($(this).scrollTop()==0){
      ai.is_busy=true;
      ai.post('person_history',{udid:ai.person_udid,msgid:ai.min_msgid},function(data){
        if(data.messages){
          if(data.messages.length<1){
            ai.is_last=true;//没有更多了
          }
          var last=$('.msg-item:eq(0)');
          
          for(var i=0;i<data.messages.length;i++){
            var msg=data.messages[i];
            ai.print(msg,'desc');
          }
          //停留在原来的位置
          if(last.length>0){
            $('#showbox').scrollTop(last.offset().top-$('#showbox').offset().top);
          }
        }else{
          ai.is_last=true;//没有更多了
        }
        ai.is_busy=false;
      });
      setTimeout(function(){
        ai.is_busy=false;
      },10000);
    }
  });
  $('#btn-lastlist').click(function(){
    ai.post('lastlist',{},function(data){
      $('#lastlist').empty();
      if(data.persons){
        for(var i=0;i<data.persons.length;i++){
          var person=data.persons[i];
          var udid=person['udid'];
          
          
          //新用户
          if(typeof ai.persons[udid]=='undefined'){
            ai.persons[udid]=new ai.Person(person,'#lastlist');
          }else{
            ai.persons[udid].show();
          }
        }
      }
    });
  });
  $('#btn-mode').click(function(){
    if(ai.person_udid==''){
      return;
    }
    if(typeof ai.persons[ai.person_udid]=='undefined'){
      return;
    }
    var p=ai.persons[ai.person_udid];
    if(p.mode=='auto'){
      p.setMode('user');
    }else if(p.mode=='user'){
      p.setMode('auto');
    }
  });
  //是否开启onez.websocket
  if(typeof onez.websocket!='undefined'){
    ai.active=function(){};//延长ajax周期
    ai.updateList=[];
  }
})(onez.ai);

onez.websocket=onez.websocket||{};
(function(ws,ai){
  ws.msg=function(msg){
    if(typeof msg.type!='undefined'){
      console.log(msg);
    }
    if(typeof msg.mod!='undefined'){
      if(msg.mod=='input'){//输入监测
        var udid=msg.udid;
        if(typeof ai.persons[udid]!='undefined'){
          ai.persons[udid].input(msg.text);
        }
      }else if(msg.mod=='msg'){//新消息
        var udid=msg.udid;
        if(typeof ai.persons[udid]!='undefined'){
          ai.persons[udid].print(msg);
        }
      }
    }
  };
  ws.is_first=true;
  ws.update=function(uids){
    for(var k in ai.persons){
      ai.persons[k].online=0;
    }
    for(var i=0;i<uids.length;i++){
      var uid=uids[i];
      if(uid.indexOf('-')!=-1){
        continue;
      }
      
      //新用户
      if(typeof ai.persons[uid]=='undefined'){
        ai.persons[uid]=new ai.Person(uid,'#userlist');
        if(!ws.is_first && $('#userlist .onez-person[data-person="'+uid+'"]').length<1){
          onez.sound.play('online');
        }
      }else{
        ai.persons[uid].show();
      }
      ai.persons[uid].online=1;
    }
    //离线用户
    for(var k in ai.persons){
      if(ai.persons[k].online==0){
        $('#userlist .onez-person[data-person="'+k+'"]').remove();
      }
    }
    ws.is_first=false;
  };
})(onez.websocket,onez.ai);