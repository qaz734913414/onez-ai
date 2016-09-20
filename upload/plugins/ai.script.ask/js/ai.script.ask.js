(function(ai){
  ai.ai_script_ask_mysec=0;
  ai.ai_script_ask_send=function(){
    ai.ai_script_ask_mysec++;
    if(ai.ai_script_ask_mysec<ai_script_ask_sec){
      return;
    }
    ai.randomsort=function(a,b) {  
      return Math.random()>.5 ? -1 : 1;  
    }  
    ai.ai_script_ask_mysec=0;
    if(typeof ai.ai_script_ask_waits=='undefined' || ai.ai_script_ask_waits.length<1){
      ai.ai_script_ask_waits=[];
      for(var i=0;i<ai_script_ask_words.length;i++){
        ai.ai_script_ask_waits.push(ai_script_ask_words[i]);
      }
      if(ai_script_ask_rand){
        ai.ai_script_ask_waits=ai.ai_script_ask_waits.sort(ai.randomsort);  
      }
    }
    var word=ai.ai_script_ask_waits.shift();
    if(ai.ai_script_ask_waits.length<1){
      if(!ai_script_ask_repeat){
        clearInterval(ai.ai_script_ask_timer);
        ai.ai_script_ask_timer=null;
      }
    }
    ai.post('aisay',{type:'text',message:word},function(data){
      
      if(typeof data.messages!='undefined'){
        for(var i=0;i<data.messages.length;i++){
          var msg=data.messages[i];
          ai.print(msg);
        }
        onez.sound.play('newmsg');
      }
    });
  };
  ai.ai_script_ask_first=function(){
    ai.ai_script_ask_mysec=ai_script_ask_sec;
    ai.ai_script_ask_send();
  };
  $('body').bind('ai-ready',function(msg){
    //首次
    if(ai_script_ask_first>0){
      setTimeout('onez.ai.ai_script_ask_first()',ai_script_ask_first*1000);
    }
    //其他
    if(ai_script_ask_sec>0){
      ai.ai_script_ask_timer=setInterval('onez.ai.ai_script_ask_send()',1000);
      $('body').bind('ai-print',function(msg){
        ai.ai_script_ask_mysec=0;
      });
    }
  });

})(onez.ai);