(function(onez){
  onez.Sound=function(key,url){
    var _this_=this;
    this.key=key;
    this.url=url;
    this.play=function(){
      if($('#sound-'+_this_.key).length<1){
        return;
      }
      $('#sound-'+_this_.key).get(0).play();
    };
    $('<audio id="sound-'+key+'" src="'+url+'" controls="controls" hidden="true"  />').appendTo('body');
  };
  onez.sound={
    sounds:{},
    init:function(arr){
      for(var k in arr){
        onez.sound.add(k,arr[k]);
      }
    },
    add:function(key,url){
      var sound=new onez.Sound(key,url);
      onez.sound.sounds[key]=sound;
    },
    play:function(key){
      if(typeof onez.sound.sounds[key]!='undefined'){
        onez.sound.sounds[key].play();
      }
    }
  };
})(onez);