var status = 'true';
$(function () {
    ajax_com({"c": 'server/show-system_setting'}, function (data) {
        if (data.errno === 0) {
            var data = data.data;
            $("#local_path").val(data.local_path);
            $("#user_name").val(data.user_name);
            $("#user_email").val(data.user_email);
            if (data.pri_key) {
                $("#pri_key").val('*************');
            }
            $("#pub_key").text(data.pub_key);
        } else {
            layer.msg(data.message, {icon: 2});
            setTimeout(function () {
                parent.location.reload();
            }, 1000)
        }
    })
})

function set_local_path() {
    var setting_value = $("#local_path").val();
    ajax_com({"c": 'server/ctrl-system_setting', 'key': 'local_path', 'value': setting_value}, function (data) {
        if (data.errno === 0) {
            layer.msg('设置成功!', {icon: 1, time: 1000});
            setTimeout(function () {
                location.reload();
            }, 1000)
        } else {
            layer.msg(data.message, {icon: 2});
        }
    })
}

function set_user_name() {
    var setting_value = $("#user_name").val();
    ajax_com({"c": 'server/ctrl-system_setting', 'key': 'user_name', 'value': setting_value}, function (data) {
        if (data.errno === 0) {
            layer.msg('设置成功!', {icon: 1, time: 1000});
            setTimeout(function () {
                location.reload();
            }, 1000)
        } else {
            layer.msg(data.message, {icon: 2});
        }
    })
}

function set_user_email() {
    var setting_value = $("#user_email").val();
    ajax_com({"c": 'server/ctrl-system_setting', 'key': 'user_email', 'value': setting_value}, function (data) {
        if (data.errno === 0) {
            layer.msg('设置成功!', {icon: 1, time: 1000});
            setTimeout(function () {
                location.reload();
            }, 1000)
        } else {
            layer.msg(data.message, {icon: 2});
        }
    })
}

function set_pri_key() {
    var setting_value = $("#pri_key").val();
    ajax_com({"c": 'server/ctrl-system_setting', 'key': 'pri_key', 'value': setting_value}, function (data) {
        if (data.errno === 0) {
            layer.msg('设置成功!', {icon: 1, time: 1000});
            setTimeout(function () {
                location.reload();
            }, 1000)
        } else {
            layer.msg(data.message, {icon: 2});
        }
    })
}

function set_pub_key() {
    var setting_value = $("#pub_key").val();
    ajax_com({"c": 'server/ctrl-system_setting', 'key': 'pub_key', 'value': setting_value}, function (data) {
        if (data.errno === 0) {
            layer.msg('设置成功!', {icon: 1, time: 1000});
            setTimeout(function () {
                location.reload();
            }, 1000)
        } else {
            layer.msg(data.message, {icon: 2});
        }
    })
}