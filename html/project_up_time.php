<!DOCTYPE HTML>
<html>
<head>
    <?php require __DIR__ . '/header.php'; ?>
    <title>定时更新</title>
    <link href="./_static/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>定时更新</h5>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" id="form-member-add">
                        <input type="hidden" name="proj_id" id="proj_id" value="0">
                        <input type="hidden" name="branch_id" id="branch_id" value="0">
                        <input type="hidden" name="page" id="page" value="1">
                        <input type="hidden" name="page_size" id="page_size" value="10">
                        <input type="hidden" name="c" value="project/proj_git-up_time_list">
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <select class="form-control" name="branch_id" id="branch_list">
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" name="time" id="time"
                                       onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',startDate:'%y-%M-%d 00:00:00'})"
                                       class="form-control"
                                       autocomplete="off" placeholder="更新时间">
                            </div>
                            <div class="col-sm-5">
                                <input type="text" name="remaking" id="remaking" class="form-control" placeholder="请填写备注">
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-success" type="button" onclick="update()">确定</button>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <table class="table table-striped table-bordered table-hover table-sort" id="editable">
                            <thead>
                            <tr>
                                <th style="width:8%;">序</th>
                                <th style="width:20%;">分支</th>
                                <th style="width:30%;">时间</th>
                                <th style="width:40%;">备注</th>
                                <th style="width:12%;">操作</th>
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
<script src="./_static/js/content.min.js?v=<?php echo date('Ymd'); ?>"></script>
<script type="text/javascript" src="./_static/js/project_up_time.js"></script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>