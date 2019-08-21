<!DOCTYPE html>
<html>
<head>
    <?php require "./header.php" ?>
    <title>项目历史版本列表</title>
    <link href="./_static/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <style type="text/css">

    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>项目历史版本列表</h5>
                    <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="left" title="刷新"
                            onclick="javascript:location.replace(location.href);"><i class="fa fa-refresh"></i> 刷新
                    </button>
                </div>
                <form action="" onsubmit="return false" id="search_form" style="text-align: center">
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="token" value="">
                    <input type="hidden" name="proj_id" class="proj_id">
                    <input type="hidden" name="cmd" value="project/show-pull_logs">
                </form>
                <form method="post" class="form-horizontal" id="form-member-add">
                <div class="ibox-content">
                    <table class="table table-striped table-bordered table-hover table-sort" id="editable">
                        <thead>
                        <tr>
                            <th style="width:8%;"></th>
                            <th style="width:8%;">项目ID</th>
                            <th style="width:8%;">用户账户</th>
                            <th style="width:8%;">版本id</th>
                            <th style="width:8%;">操作描述</th>
                            <th style="width:8%;">操作时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
                    <input type="hidden" name="token" value="">
                    <input type="hidden" name="proj_id" class="proj_id">
                    <input type="hidden" name="cmd" value="project/ctrl-reset">
                    <div class="hr-line-dashed"></div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit">确定</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require "./footer.php" ?>
<script type="text/javascript" src="./_static/js/plugins/My97DatePicker/WdatePicker.js"></script>
<script src="./_static/js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="./_static/js/plugins/dataTables/dataTables.bootstrap.js"></script>
<script src="./_static/js/content.min.js?v=<?php echo date('Ymd')?>"></script>
<script src="./_static/js/proj_loglist.js?v=<?php echo date('Ymd')?>"></script>
</html>
