$(function () {
    $("form").submit(function () {
        if ($("input[name='acc']").val() == '') {
            layer.msg('请输入账户！', {icon: 2});
            return false;
        }
        if ($("input[name='pwd']").val() == '') {
            layer.msg('请输入密码！', {icon: 2});
            return false;
        }
        com_ajax($("form").serialize(), function (data) {
            if (data.errno == 0) {
                layer.msg('登录成功', {icon: 1});
                setTimeout(function () {
                    console.log(data);
                    sessionStorage.setItem('token',data.data.token);
                    sessionStorage.setItem('user_acc',data.data.name);
                    window.location.href = './index.php';
                }, 1000);
            } else {
                layer.msg(data.message, {icon: 2});
            }
        })
        return false;
    });
})

function com_ajax(post_data, callback) {
    $.ajax({
        url: '/api.php',
        type: 'post',
        data: post_data,
        dataType: 'json',
        success: function (data) {
            callback(data);
        }
    })
}
