var status = 'true';
$(function () {
    var proj_id = getQueryString('proj_id');
    var proj_name = getQueryString('proj_name');
    if (proj_id != '' && proj_id != null) {
        ajax_com({'cmd': 'project/show-pull_logs', 'proj_id': proj_id}, function (data) {
            if (data.errno === 0) {
                var data = data.data;
                $("input[name='proj_name']").val(proj_name);
                $("input[name='active_branch']").val(data.branch);
                $("input[name='proj_id']").val(proj_id);
                /*var h='<select name="commit" class="form-control">';
                $(data.list).each(function (i,v) {
                    h+='<option value="7f82c90" >'+v.current_commit_id+'-'+v.current_commit_data+'</option>';
                })
                h+='</select>';
                $('#loglist').html(h);*/
            } else {
                layer.msg(data.message, {icon: 2});
                setTimeout(function () {
                    parent.location.reload();
                }, 1000)
            }
        })
    }


    $("#form-member-add").submit(function () {
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
