/**
 * Created by zxy on 2015/4/7.
 */
(function(){
    var name=document.getElementById('name');
    var num=document.getElementById('num');
    var form_info=document.getElementById('form_info');
    var old_name=name.value;
    var old_num=num.value;
    var ele_ev=function(ele){
        ele.onblur=function(){
            if(ele.value===''){
                ele.style.border='#9b9a9a solid 1px';
            }
            else{
                ele.style.border='#ffffff solid 1px';
            }
        };
        ele.onfocus=function(){
            ele.style.border='#9b9a9a solid 1px';
        }
    };
    ele_ev(name);
    ele_ev(num);
    form_info.onsubmit=function(e){
        var new_name=name.value;
        var new_num=num.value;
        var name_reg=/[\w]{0,10}[\u4e00-\u9fa5]{0,5}/;
        var num_reg=/\d+/;
        if(new_name===old_name&&new_num===old_num){
            name.value='';
            num.value='';
            num.focus();
            name.focus();
            return false;
        }
        else if(new_name===''||!name_reg.test(new_name)){
            name.value='';
            name.focus();
            return false;
        }
        else if(new_num===''||!num_reg.test(new_num)){
            num.value='';
            num.focus();
            return false;
        }
        else{
            return true;
        }
    }
})();
