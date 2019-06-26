var status = 'true';
$(function () {
    var proj_id = getQueryString('proj_id');
    if (proj_id != '' && proj_id != null) {
        ajax_com({'cmd': 'project/show-info', 'proj_id': proj_id}, function (data) {
            if (data.errno === 0) {
                var data = data.data;
                $("input[name='proj_id']").val(data.proj_id);
                $("input[name='proj_name']").val(data.proj_name);
                $("textarea[name='proj_desc']").text(data.proj_desc);
                $("input[name='proj_git_url']").val(data.proj_git_url);
                $("input[name='proj_local_path']").val(data.proj_local_path);
                $("input[name='proj_user_name']").val(data.proj_user_name);
                $("input[name='proj_user_email']").val(data.proj_user_email);
                var h='';
                $(data.proj_backup_files).each(function (i,v) {
                    h+='<div class="col-sm-12" style="margin-bottom:10px;">\n' +
                        '                                <input type="text" class="form-control" name="proj_backup_files[]"\n' +
                        '                                       placeholder="请输入备份文件路径" style="width:80%;" value="'+v+'">\n' +
                        '                                    <a onclick="del(this)" href="javascript:void(0);"\n' +
                        '                                                                  class="btn btn-danger">删除</a>\n' +
                        '                                </div>';
                })
              $('#backup').append(h);
            } else {
                layer.msg(data.message, {icon: 2});
                setTimeout(function () {
                    parent.location.reload();
                }, 1000)
            }
        })
    }else{
        var h='<div class="col-sm-12" style="margin-bottom:10px;">\n' +
            '                                <input type="text" class="form-control" name="proj_backup_files[]"\n' +
            '                                       placeholder="请输入备份文件路径" style="width:80%;">\n' +
            '                                    <a onclick="del(this)" href="javascript:void(0);"\n' +
            '                                                                  class="btn btn-danger">删除</a>\n' +
            '                                </div>';
        $('#backup').append(h);
    }


    $("#form-member-add").submit(function () {
        if ($("input[name='proj_name']").val() == '') {
            layer.msg("请输入项目名称", {icon: 2});
            return false;
        }
        if ($("textarea[name='proj_desc']").val() == '') {
            layer.msg("请输入项目简介", {icon: 2});
            return false;
        }
        if ($("input[name='proj_git_url']").val() == '') {
            layer.msg("请输入Git 地址", {icon: 2});
            return false;
        }
        if ($("input[name='proj_local_path']").val() == '') {
            layer.msg("请输入本地路径", {icon: 2});
            return false;
        }
        if ($("input[name='proj_user_name']").val() == '') {
            layer.msg("请输入开发者名称", {icon: 2});
            return false;
        }
        if ($("input[name='proj_user_email']").val() == '') {
            layer.msg("请输入开发者邮箱", {icon: 2});
            return false;
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

function add() {
    var html=' <div class="col-sm-12" style="margin-bottom:10px;">\n' +
        '                                <input type="text" class="form-control" name="proj_backup_files[]"\n' +
        '                                       placeholder="请输入备份文件路径" style="width:80%;">\n' +
        '                                    <a onclick="del(this)" href="javascript:void(0);"\n' +
        '                                                                  class="btn btn-danger">删除</a>\n' +
        '                                </div>';
    $('#backup').append(html);
}

function del(obj) {
    $(obj).parent().remove();
}