var onez=onez||{};
onez.alert=function(message,callback){
  if(typeof callback=='undefined'){
    bootbox.alert(message);
  }else{
    bootbox.alert(message,callback);
  }
};
onez.confirm=function(message,callback){
  if(typeof callback=='undefined'){
    bootbox.alert(message);
  }else{
    bootbox.confirm(message,function(r){
      if(r){
        callback();
      }
    });
  }
};
onez.formpost=function(form){
  $.post(window.location.href,$(form).serialize(),function(data){
    if(typeof data.error=='string'){
      onez.alert(data.error);
    }
    if(typeof data.status=='string' && data.status=='success'){
      if(typeof data.goto=='string'){
        if(typeof data.message=='string'){
          onez.alert(data.message,function(){
            if(data.goto=='reload'){
              window.location.reload();
            }else if(data.goto=='close'){
              if(parent==self){
                window.close();
              }else{
                if(typeof parent.closeWin=='function'){
                  parent.closeWin();
                }
              }
            }else{
              window.location.href=data.goto;
            }
          });
        }else{
          if(data.goto=='reload'){
            window.location.reload();
          }else if(data.goto=='close'){
            if(parent==self){
              window.close();
            }else{
              if(typeof parent.closeWin=='function'){
                parent.closeWin();
              }
            }
          }else{
            window.location.href=data.goto;
          }
        }
      }else if(typeof data.message=='string'){
        onez.alert(data.message);
      }
    }
  },'json');
};
onez.del=function(id){
  onez.confirm('您确定要删除这条记录吗？',function(){
    $.post(window.location.href,{action:'delete',id:id},function(data){
      if(typeof data.error=='string'){
        onez.alert(data.error);
      }
      if(typeof data.status=='string' && data.status=='success'){
        if(typeof data.goto=='string'){
          if(typeof data.message=='string'){
            onez.alert(data.message,function(){
              if(data.goto=='reload'){
                window.location.reload();
              }else{
                window.location.href=data.goto;
              }
            });
          }else{
            if(data.goto=='reload'){
              window.location.reload();
            }else{
              window.location.href=data.goto;
            }
          }
        }else if(typeof data.message=='string'){
          onez.alert(data.message);
        }
      }
    },'json');
  });
};
onez.formcheck=function(form,option){
  var o={
    errorElement: 'span', //default input error message container
    errorClass: 'help-block help-block-error', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    ignore: "",  // validate all fields including form hidden input
    highlight: function (element) { // hightlight error inputs
        $(element)
            .closest('.form-group').addClass('has-error'); // set error class to the control group
    },
    unhighlight: function (element) { // revert the change done by hightlight
        $(element)
            .closest('.form-group').removeClass('has-error'); // set error class to the control group
    },
    success: function (label) {
        label
            .closest('.form-group').removeClass('has-error'); // set success class to the control group
    },
    submitHandler: function (form) {
        onez.formpost(form);
    }
  };
  if(typeof option=='object'){
    for(var k in option){
      o[k]=option[k];
    }
  }
  form.validate(o);
};