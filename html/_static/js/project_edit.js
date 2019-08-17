var status = 'true';
$(function () {
    var proj_id = 0;
    var id = getQueryString('proj_id');
    if (id != '' && id != null) {
        proj_id = id;
    }
    $("input[name='proj_id']").val(proj_id);
    ajax_com({'cmd': 'project/show-info', 'proj_id': proj_id}, function (data) {
        if (data.errno === 0) {
            var data = data.data;
            $("input[name='proj_name']").val(data.proj_name);
            $("textarea[name='proj_desc']").text(data.proj_desc);
            $("input[name='git_url']").val(data.proj_git_url);
            $("input[name='local_path']").val(data.proj_local_path);
            if (proj_id != 0) {
                $("input[name='git_url']").attr('readonly', 'readonly');
                $("input[name='local_path']").attr('readonly', 'readonly');
                $("#operate").append('<button class="btn btn-danger" type="button" onclick="project_del(this,'+proj_id+')">删除</button>')
            }
            $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",})
            var h = '';
            $(data.proj_backup_files).each(function (i, v) {
                h += '<div class="col-sm-12" style="margin-bottom:10px;">\n' +
                    '                                <input type="text" class="form-control" name="backup_files[]"\n' +
                    '                                       placeholder="请输入备份文件路径" style="width:80%;" value="' + v + '">\n' +
                    '                                    <a onclick="del(this)" href="javascript:void(0);"\n' +
                    '                                                                  class="btn btn-danger">删除</a>\n' +
                    '                                </div>';
            })
            $('#backup').append(h);
            var ht = '';
            $(data.srv_list).each(function (i, v) {
                var s = '';
                if (v.is_check) {
                    s = 'checked';
                }
                ht += '<input type="checkbox" name="srv_ids[]" value="' + v.srv_id + '" ' + s + ' style="margin-left:10px;"/>' + v.srv_name;
            })
            $('#srvlist').html(ht);
        } else {
            layer.msg(data.message, {icon: 2});
            setTimeout(function () {
                parent.location.reload();
            }, 1000)
        }
    })

    $("#form-member-add").submit(function () {
        if ($("input[name='proj_name']").val() == '') {
            layer.msg("请输入项目名称", {icon: 2});
            return false;
        }
        if ($("textarea[name='proj_desc']").val() == '') {
            layer.msg("请输入项目简介", {icon: 2});
            return false;
        }
        if ($("input[name='git_url']").val() == '') {
            layer.msg("请输入Git 地址", {icon: 2});
            return false;
        }
        if ($("input[name='local_path']").val() == '') {
            layer.msg("请输入本地路径", {icon: 2});
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

function project_del(obj, id) {
    layer.confirm('确认要删除吗？', function (index) {
        ajax_com({'cmd':'project/ctrl-del','proj_id':id},function (data) {
            if (data.errno === 0) {
                layer.msg('已删除!',{icon:1,time:1000});
                setTimeout(function () {
                    parent.location.reload();
                }, 1000)
            } else {
                layer.msg(data.message,{icon:2});
            }
        })
    });
}

function add() {
    var html = ' <div class="col-sm-12" style="margin-bottom:10px;">\n' +
        '                                <input type="text" class="form-control" name="backup_files[]"\n' +
        '                                       placeholder="请输入备份文件路径" style="width:80%;">\n' +
        '                                    <a onclick="del(this)" href="javascript:void(0);"\n' +
        '                                                                  class="btn btn-danger">删除</a>\n' +
        '                                </div>';
    $('#backup').append(html);
}

function del(obj) {
    $(obj).parent().remove();
}