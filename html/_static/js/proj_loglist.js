var table;
var status = 'true';
var token = sessionStorage.getItem('token');
$(function () {
    table = show_list();

    $("#form-member-add").submit(function () {
        if ($("input[name='commit']:checked").length == 0) {
            layer.msg("请选择要回滚的版本", {icon: 2});
            return false;
        }
        if (status == 'true') {
            status = 'false';
            $("input[name='token']").val(token);
            ajax_com($("#form-member-add").serialize(), function (data) {
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

function show_list() {
    return $("#editable").dataTable({
        "serverSide": true,  //启用服务器端分页
        "pageLength": 10,
        "lengthChange": false,
        "searching": false,
        "orderMulti": false,  //启用多列排序
        "bSort": false,
        "oLanguage": {
            sInfo: "总共有 _MAX_ 条数据",
            sInfoFiltered: "总共有 _MAX_ 条数据",
            sInfoEmpty: "暂无数据",
        },
        "ajax": function (data, callback, settings) {
            //封装请求参数
            var page = (data.start / data.length) + 1;//当前页码
            $("input[name='page']").val(page);
            $("input[name='token']").val(token);
            var proj_id = getQueryString('proj_id');
            $('.proj_id').val(proj_id);
            ajax_com($("#search_form").serialize(), function (data) {
                if (data.errno === 0) {
                    var data = data.data;
                    $("#user_num").text(data.cnt_data);
                    var returnData = {};
                    returnData.draw = data.draw;//这里直接自行返回了draw计数器,应该由后台返回
                    returnData.recordsTotal = data.cnt_data;//返回数据全部记录
                    returnData.recordsFiltered = data.cnt_data;//返回数据全部记录
                    returnData.data = data.list;//返回的数据列表
                    callback(returnData);
                    $(".dataTables_paginate").append("<div style='float:right;margin: 0 10px'><input type='number' style='width: 30px;height: 22px;'><input type='button' value='确定' id='direct_page' style='background: #ffffff;line-height: 28px;border:1px solid #ccc;height: 26px;padding: 0 6px;margin-left: 5px;cursor: pointer;'></div>");
                    $("#direct_page").click(function () {
                        var jump_page = $(this).parent().find("input").val();
                        if (jump_page) $("#editable").dataTable().fnPageChange(jump_page - 1);
                    })
                } else layer.msg(data.msg, {icon: 2});
            })
        },
        "columns": [
            {data: 'radio_html'},
            {data: 'proj_id'},
            {data: 'user_acc'},
            {data: 'current_commit_id'},
            {data: 'current_commit_data'},
            {data: 'add_time'},
        ]
    }).api();
}
