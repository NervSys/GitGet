function ajax_com(post_data, callback) {
    var token = sessionStorage.getItem('token');
    if (!token) {
        layer.msg("登录超时，请重新登录");
        setTimeout(function () {
            parent.location.href = './login.php';
        }, 600);
    }
    if (typeof post_data == 'object') {
        post_data['token'] = token;
    } else {
    }
    $.ajax({
        url: '/api.php',
        type: 'post',
        data: post_data,
        dataType: 'json',
        success: function (data) {
            if (data.code === 2005) {
                layer.msg("登录超时，请重新登录");
                setTimeout(function () {
                    parent.location.href = './login.php';
                }, 600);
            } else
                callback(data);
        }
    })
}

//获取url中的参数
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); // 匹配目标参数
    var result = window.location.search.substr(1).match(reg); // 对querystring匹配目标参数
    if (result != null) {
        return decodeURIComponent(result[2]);
    } else {
        return '';
    }
}

/*弹出层*/

/*
	参数解释：
	title	标题
	url		请求的url
	id		需要操作的数据id
	w		弹出层宽度（缺省调默认值）
	h		弹出层高度（缺省调默认值）
*/
function layer_show(title, url, w, h) {
    if (title == null || title == '') {
        title = false;
    }
    ;
    if (url == null || url == '') {
        url = "404.html";
    }
    ;
    if (w == null || w == '') {
        w = 800;
    }
    ;
    if (h == null || h == '') {
        h = ($(window).height() - 50);
    }
    ;
    layer.open({
        type: 2,
        area: [w + 'px', h + 'px'],
        fix: false, //不固定
        maxmin: true,
        shade: 0.4,
        title: title,
        content: url
    });
}

/*关闭弹出框口*/
function layer_close() {
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}