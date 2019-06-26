var status = 'true';
$(function () {
    var proj_id = getQueryString('proj_id');
    var proj_name = getQueryString('proj_name');
    if (proj_id != '' && proj_id != null) {
        ajax_com({'cmd': 'project/show-branch', 'proj_id': proj_id}, function (data) {
            if (data.errno === 0) {
                var data = data.data;
                $("input[name='proj_name']").val(proj_name);
                $("input[name='active_branch']").val(data.active_branch);
                $("input[name='proj_id']").val(proj_id);
                var h='<select name="branch" class="form-control">';
                $(data.branch_names).each(function (i,v) {
                    var s='';
                    if(v==data.active_branch){
                        s='selected';
                    }
                    h+='<option value="'+v+'" '+s+'>'+v+'</option>';
                })
                h+='</select>';
                $('#branchlist').html(h);
            } else {
                layer.msg(data.message, {icon: 2});
                setTimeout(function () {
                    parent.location.reload();
                }, 1000)
            }
        })
    }


    $("#form-member-add").submit(function () {
        if ($("input[name='active_branch']").val() == $("select[name='branch']").val()) {
            layer.msg("项目已经在当前分支了", {icon: 2});
            return false;
        }
        status = 'true';
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
