var status = 'true';
$(function () {
    var user_id = getQueryString('uid');
            if (user_id != '' && user_id != null) {
                ajax_com({"c": 'user/show-user_detail', 'user_id': user_id}, function (data) {
                    if (data.errno === 0) {
                        var data = data.data;
                        $("input[name='user_id']").val(user_id);
                        $("input[name='user_acc']").val(data.user_acc);
                    } else {
                        layer.msg(data.message, {icon: 2});
                        setTimeout(function () {
                            parent.location.reload();
                        }, 1000)
                    }
                })
            } 


    $("#form-member-add").submit(function () {
        if ($("input[name='user_acc']").val() == '') {
            layer.msg("请输入账户", {icon: 2});
            return false;
        }
        if (user_id == '' || user_id == null) {
            if ($("input[name='user_pwd']").val() == '') {
                layer.msg("请输入账户登录密码", {icon: 2});
                return false;
            }
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