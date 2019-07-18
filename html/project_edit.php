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
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" id="form-member-add">
                        <input type="hidden" name="proj_id" value="0">
                        <input type="hidden" name="token" value="">
                        <input type="hidden" name="cmd" value="project/ctrl-add">
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span style="color:red">*</span>项目名称：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="proj_name" name="proj_name"
                                       placeholder="请输入项目名称">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span style="color:red">*</span>项目简介：</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="proj_desc" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span style="color:red">*</span>Git 地址：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="proj_git_url" name="proj_git_url"
                                       placeholder="请输入Git 地址">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">选择服务器：</label>
                            <div class="col-sm-10" id="srvlist">

                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存内容</button>
                                <button class="btn btn-white" type="button" onclick="layer_close()">取消</button>
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
<script type="text/javascript" src="./_static/js/project_edit.js"></script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>