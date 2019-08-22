<!DOCTYPE HTML>
<html>
<head>
    <?php require __DIR__ . '/header.php'; ?>
    <title>编辑项目</title>
    <link href="./_static/css/plugins/iCheck/custom.css" rel="stylesheet">
    <style type="text/css">
        .radio {
            display: inline
        }
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>编辑项目</h5>
                    <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="left" title="刷新"
                            onclick="javascript:location.replace(location.href);"><i class="fa fa-refresh"></i> 刷新
                    </button>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" id="form-member-add">
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">开发者：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="user_name" placeholder="请输入开发者姓名">
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-primary" type="button" onclick="set_user_name()">保存</button>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">开发者邮箱：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="user_email" placeholder="请输入开发者邮箱">
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-primary" type="button" onclick="set_user_email()">保存</button>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">私钥：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pri_key" placeholder="请输入私钥">
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-primary" type="button" onclick="set_pri_key()">保存</button>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">公钥：</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="pub_key" placeholder="请输入公钥" rows="5"></textarea>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-primary" type="button" onclick="set_pub_key()">保存</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>

<!--请在下方写此页面业务相关的脚本-->
<script src="./_static/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="./_static/js/system_setting.js"></script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>