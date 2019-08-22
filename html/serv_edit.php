<!DOCTYPE HTML>
<html>
<head>
    <?php require __DIR__ . '/header.php'; ?>
    <title>编辑服务器</title>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>编辑服务器</h5>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" id="form-member-add">
                        <input type="hidden" name="srv_id" value="0">
                        <input type="hidden" name="cmd" value="server/ctrl-addOrEdit">
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span style="color:red;margin-right:5px;font-size:18px;">*</span>服务器IP：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="srv_ip" id="srv_ip">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">服务器端口：</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="srv_port" id="srv_port" >
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="srv_name" id="srv_name">
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
<script type="text/javascript" src="./_static/js/serv_edit.js"></script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>