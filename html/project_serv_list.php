<!DOCTYPE html>
<html>
<head>
    <?php require "./header.php" ?>
    <title>项目服务器管理列表</title>
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
                    <h5>项目服务器管理列表</h5>
                    <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="left" title="刷新"
                            onclick="javascript:location.replace(location.href);"><i class="fa fa-refresh"></i> 刷新
                    </button>
                </div>
                <form action="" onsubmit="return false" id="search_form" style="text-align: center">
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="cmd" value="server/show-project_serv_list">
                    <input type="hidden" name="proj_id" class="proj_id">
                    <input type="hidden" name="token" value="">
                </form>
                <div class="ibox-content">
                    <table class="table table-striped table-bordered table-hover table-sort" id="editable">
                        <thead>
                        <tr>
                            <th style="width:8%;">ID</th>
                            <th style="width:8%;">项目域名</th>
                            <th style="width:8%;">服务器IP</th>
                            <th style="width:8%;">服务器端口</th>
                            <th style="width:8%;">服务器描述</th>
                            <th style="width:8%;">本地路径</th>
                            <th style="width:8%;">备份文件</th>
                            <th style="width:8%;">当前分支</th>
                            <th style="width:20%;">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<?php require "./footer.php" ?>
<script type="text/javascript" src="./_static/js/plugins/My97DatePicker/WdatePicker.js"></script>
<script src="./_static/js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="./_static/js/plugins/dataTables/dataTables.bootstrap.js"></script>
<script src="./_static/js/content.min.js?v=<?php echo date('Ymd')?>"></script>
<script src="./_static/js/project_serv_list.js?v=<?php echo date('YmdH')?>"></script>
</html>
