var status = 'true';
var token = sessionStorage.getItem('token');
$(function () {
    var proj_id = getQueryString('proj_id');
    var proj_name = getQueryString('proj_name');
    if (proj_id != '' && proj_id != null) {
        ajax_com({"c": 'project/show-team_list', 'proj_id': proj_id}, function (data) {
            if (data.errno === 0) {
                var data = data.data;
                $("input[name='proj_name']").val(proj_name);
                $("input[name='proj_id']").val(proj_id);
                var h = '';
                $(data).each(function (i, v) {
                    var s = '';
                    if (v.selected) {
                        s = 'checked';
                    }
                    h += '<input type="checkbox" name="user_ids[]" value="' + v.user_id + '" ' + s + ' style="margin-left:10px;"/>' + v.user_acc;
                })
                $('#teamlist').html(h);
            } else {
                layer.msg(data.message, {icon: 2});
                setTimeout(function () {
                    parent.location.reload();
                }, 1000)
            }
        })
    }


    $("#form-member-add").submit(function () {
        if ($("input[name^='user_ids']:checked").length == 0) {
            layer.msg("请勾选项目管理人员", {icon: 2});
            return false;
        }
        status = 'true';
        if (status == 'true') {
            status = 'false';
            $("input[name='token']").val(token);
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
