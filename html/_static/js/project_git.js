var status = 'true';
var proj_id = 0;
$(function () {
    var id = getQueryString('proj_id');
    if (id != '' && id != null) {
        proj_id = id;
    }
    $("input[name='proj_id']").val(proj_id);
    ajax_com({'cmd': 'project/proj_git-branch_list', 'proj_id': proj_id}, function (data) {
        if (data.errno === 0) {
            var data = data.data;
            var ht='';
            $(data).each(function (i, v) {
                var s = '';
                if (v.active) {
                    s = 'selected="selected"';
                }
                ht += '<option value="'+v.branch_id+'" '+s+'>'+v.branch_name+'</option>';
            })
            $('#branch_list').html(ht)
        } else {
            layer.msg(data.message, {icon: 2});
            setTimeout(function () {
                parent.location.reload();
            }, 1000)
        }
    })
})

function update_branch() {
    ajax_com({'cmd':'project/proj_git-update_branch','proj_id':proj_id},function (data) {
        if (data.errno === 0) {
            layer.msg('ok', {icon: 1});
            setTimeout(function () {
                parent.location.reload();
            }, 1000)
        }else {
            layer.msg(data.message, {icon: 2});
            setTimeout(function () {
                parent.location.reload();
            }, 1000)
        }
    })
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