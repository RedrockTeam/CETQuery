/**
 * Created by Ling on 5/13/15.
 */
//(function ($, Vue) {
//    if(!$){console.log('warning: jQuery not loaded!')}
//    if(!Vue){console.log('warning: Vuejs not loaded!')}

    var vmQuery = new Vue({
        el: "#query",
        data: {
            id: '',
            username: "",

            show: true
        },
        methods: {
            query: function(e){
                e.preventDefault();
                var _check = checkInput();
                if('id' === _check){
                    $.Dialog.fail('请输入正确的准考证号!');
                    return false;
                }else if('name' === _check){
                    $.Dialog.fail('请输入正确的姓名!');
                    return false;
                }
                $.Dialog.loading();
                var timer = setTimeout(function(){
                    $.Dialog.fail("网络超时!");
                }, 5000); //timeout

                var $data = $('#form').serialize();
                $.post('/CETQuery/Api/Index/find', $data).success(function(res){
                    clearTimeout(timer);
                    if(res.status == 0){
                        $.Dialog.success("查询成功, 载入中...");
                        setTimeout(function(){
                            vmResult.tname = res.data[6];
                            vmResult.tschool = res.data[5];
                            vmResult.all = res.data[4];
                            vmResult.writing = res.data[3];
                            vmResult.reading = res.data[2];
                            vmResult.listening = res.data[1];
                            vmResult.tid = vmQuery.id;
                            vmResult.ttype = get_testtype(vmQuery.id);
                            vmResult.tname = vmQuery.username;
                            vmQuery.show = false;
                            setTimeout(function(){
                                vmResult.show = true;
                            }, 500);
                        }, 2000);
                    }else{
                        $.Dialog.fail(res.info);
                    }
                });
            }
        }
    });

    var vmResult = new Vue({
        el: "#result",
        data:{
            tname: '',
            tschool: '',
            ttype: '',
            tid: '',
            all: '',
            listening: '',
            reading: '',
            writing: '',

            show: false
        },
        methods: {
            back: function(e){
                e.preventDefault();
                vmResult.show = false;
                setTimeout(function(){
                    vmResult.tname = vmResult.tschool = vmResult.all = vmResult.writing = vmResult.reading = vmResult.listening = vmResult.ttype = vmResult.tname = vmResult.tid = '';
                    vmQuery.show = true;
                }, 500);
            }
        }
    });

    function get_testtype(tid) {
        if(tid.length == 15){
            switch (tid.substr(9, 1)) {
                case "1": return "英语四级";
                case "2": return "英语六级";
                case "3": return "日语四级";
                case "4": return "日语六级";
                case "5": return "德语四级";
                case "6": return "德语六级";
                case "7": return "俄语四级";
                case "8": return "俄语六级";
                case "9": return "法语四级";
                default: return "";
            }
        }
        else if(tid.length == 14)
            return "英语口语";
        return "";
    }

    function checkInput(){
        var tname = vmQuery.username,
            tid = vmQuery.id;

        var idReg = /^\d{14,15}$/,
            nameReg = /^[\u4e00-\u9fa5]{2,}$/;

        if (!nameReg.test(tname)){
            return 'name';
        }

        if (!idReg.test(tid)){
            return 'id';
        }

        if(tid.length == 15 && tid.substr(6, 3) != "142" || get_testtype(tid) == ""){
            return 'id';
        }

        if(tid.length == 14 && tid.substr(0, 3) != "142"  || get_testtype(tid) == ""){
            return 'id';
        }

        return true;
    }
//})(jQuery, Vue);