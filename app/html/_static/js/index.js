$(function () {
    ajax_com({'cmd': 'user/ctrl-login_info'}, function (data) {
        if (data.errno === 0) {
            var user_acc=sessionStorage.getItem('user_acc');
            //$(".text-xs").html(data.data.role_name + '<b class="caret"></b>');
            $(".font-bold").text(user_acc);
        }
    })

    ajax_com({'cmd': "user/ctrl-user_menu"}, function (result) {
        if (result.errno === 0) {
            var str = '';
            $.each(result.data, function (k, v) {
                str += '<li>';
                if (v.child.length == '0')
                    str += '<a class="J_menuItem" href="' + v.menu_url + '"><i class="fa fa-' + v.menu_icon + '"></i> <span class="nav-label">' + v.menu_name + '</span></a>';
                else {
                    str += '<a href="#"><i class="fa fa-' + v.menu_icon + '"></i><span class="nav-label">' + v.menu_name + '</span><span class="fa arrow"></span></a>';
                    str += '<ul class="nav nav-second-level collapse">';
                    $.each(v.child, function (key, child) {
                        str += '<li><a class="J_menuItem" href="' + child.menu_url + '" data-index="0">' + child.menu_name + '</a></li>';
                    })
                    str += '</ul>';
                }
                str += '</li>'
            })
            $("#side-menu").append(str);
            asyncLoad('./_static/js/contabs.min.js');
            asyncLoad('./_static/js/plugins/metisMenu/jquery.metisMenu.js');
            asyncLoad('./_static/js/hplus.min.js');
        }
    })
});

function asyncLoad(src) {
    var ga = document.createElement('script');
    ga.type = 'text/javascript';
    ga.async = false;
    ga.src = src;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(ga, s);
}

function login_out() {
    ajax_com({'cmd': 'adminApi/admin_acc-login_out'}, function (data) {
        window.location.href = "./login.php";
    })
}