var status = 'true';
var token=sessionStorage.getItem('token');
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
        if($("select[name='branch']").val()==$("input[name='active_branch']").val()){
            layer.msg("该项目已经在需要切换的分支上", {icon: 2});
            return false;
        }
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
