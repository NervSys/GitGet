<!DOCTYPE HTML>
<html>
<head>
    <?php require __DIR__ . '/header.php'; ?>
    <title>编辑项目</title>
    <link href="./_static/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <!--    <link href="./_static/css/plugins/iCheck/custom.css" rel="stylesheet">-->
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
                        <input type="hidden" name="proj_id" id="proj_id" value="0">
                        <input type="hidden" name="branch_id" id="branch_id" value="0">
                        <input type="hidden" name="page" id="page" value="1">
                        <input type="hidden" name="page_size" id="page_size" value="10">
                        <input type="hidden" name="c" value="project/proj_git-log_list">
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span style="color:red">*</span>分支列表：</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="branch_id" id="branch_list">
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-success" type="button" onclick="update_branch()">更新远程</button>
                            </div>
                            <div class="col-sm-1">
                                <button class="btn btn-success" type="button" onclick="checkout()">切换</button>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <table class="table table-striped table-bordered table-hover table-sort" id="editable">
                            <thead>
                            <tr>
                                <th style="width:8%;">序</th>
                                <th style="width:50%;">最新提交</th>
                                <th style="width:15%;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
<script type="text/javascript" src="./_static/js/plugins/My97DatePicker/WdatePicker.js"></script>
<script src="./_static/js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="./_static/js/plugins/dataTables/dataTables.bootstrap.js"></script>
<script src="./_static/js/content.min.js?v=<?php echo date('Ymd') ?>"></script>
<script type="text/javascript" src="./_static/js/project_git.js"></script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>