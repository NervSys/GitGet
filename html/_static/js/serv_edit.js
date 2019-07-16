var status = 'true';
$(function () {
    var srv_id = getQueryString('uid');
            if (srv_id != '' && srv_id != null) {
                ajax_com({'cmd': 'server/show-serv_detail', 'srv_id': srv_id}, function (data) {
                    if (data.errno === 0) {
                        var data = data.data;
                        $("input[name='srv_id']").val(srv_id);
                        $("input[name='srv_ip']").val(data.srv_ip);
                        $("input[name='srv_port']").val(data.srv_port);
                        $("input[name='srv_desc']").val(data.srv_desc);
                        $("input[name='srv_name']").val(data.srv_name);
                    } else {
                        layer.msg(data.message, {icon: 2});
                        setTimeout(function () {
                            parent.location.reload();
                        }, 1000)
                    }
                })
            } 


    $("#form-member-add").submit(function () {
        if ($("input[name='srv_ip']").val() == '') {
            layer.msg("请输入服务器ip", {icon: 2});
            return false;
        }
        if ($("input[name='srv_name']").val() == '') {
            layer.msg("请输入服务器域名", {icon: 2});
            return false;
        }
        if($("input[name='srv_port']").val() == ''){
            $("input[name='srv_port']").val(0);
        }
        if (status == 'true') {
            status = 'false';
            ajax_com($("form").serialize(), function (data) {
                if (data.errno === 0) {
                    layer.msg(data.message, {icon: 1});
                    setTimeout(function () {
                        parent.location.reload();
                    }, 1000)
                } else {
                    status = true;
                    layer.msg(data.message, {icon: 2});
                }
            })
        } else layer.msg("正在处理中，请稍后……", {icon: 16});
        return false;
    })
})