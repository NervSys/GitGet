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
                        <input type="hidden" name="cmd" value="project/ctrl-add">
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span style="color:red">*</span>分支列表：</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="branch_id" id="branch_list">
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-success" type="button" onclick="update_branch()">更新远程</button>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>

<!--请在下方写此页面业务相关的脚本-->
<script src="./_static/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="./_static/js/project_git.js"></script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>