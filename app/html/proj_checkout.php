<!DOCTYPE HTML>
<html>
<head>
    <?php require __DIR__ . '/header.php'; ?>
    <title>切换分支</title>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>切换分支</h5>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" id="form-member-add">
                        <input type="hidden" name="proj_id" value="0">
                        <input type="hidden" name="cmd" value="project/ctrl-checkout">
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">项目名称：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="proj_name" name="proj_name"
                                      readonly>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">当前分支：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"  name="active_branch"
                                       readonly>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">切换到：</label>
                            <div class="col-sm-10" id="branchlist">
                               <select></select>
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
<script type="text/javascript" src="./_static/js/proj_checkout.js"></script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>