<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>WEB开发工作管理系统登陆</title>

    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- bootstrap & fontawesome -->
    <link rel='stylesheet' type='text/css' href='/public/adminlte/css/bootstrap.min.css' />
    <link rel='stylesheet' type='text/css' href='/public/adminlte/font-awesome/4.1.0/css/font-awesome.min.css' />
    <!-- page specific plugin styles -->
    <!-- ace styles -->
    <link rel="stylesheet" href="/public/adminlte/css/ace.min.css" id="main-ace-style" />
    <!--[if lte IE 9]>
    <link rel='stylesheet' type='text/css' href='/public/adminlte/css/ace-part2.min.css' />
    <![endif]-->
    <link rel='stylesheet' type='text/css' href='/public/adminlte/css/ace-skins.min.css' />
    <link rel='stylesheet' type='text/css' href='/public/adminlte/css/ace-rtl.min.css' />
    <link rel='stylesheet' type='text/css' href='/public/adminlte/css/admin.base.css' />
    <link rel='stylesheet' type='text/css' href='/public/adminlte/css/default.css' />

    <!--[if lte IE 9]>
    <link rel='stylesheet' type='text/css' href='/public/adminlte/css/ace-ie.min.css' />
    <![endif]-->

</head>
<body class="login-layout light-login">
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="center">
                        <h1>
                            <i class="ace-icon fa fa-leaf green"></i>
                            <span class="red">WEB开发</span>
                            <span class="white" id="id-text2">工作管理系统</span>
                        </h1>
                        <h4 class="blue" id="id-company-text">&copy; 登陆</h4>
                    </div>

                    <div class="space-6"></div>

                    <div class="position-relative">
                        <div id="login-box" class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header blue lighter bigger">
                                        <i class="ace-icon fa fa-coffee green"></i>
                                        请输入你登录的账号和密码
                                    </h4>

                                    <div class="space-6"></div>
                                    <form action="/index/login" method="post">
                                        <fieldset>
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="text" value="" name="username" nullmsg="请输入用户名！" datatype="*" placeholder=" 账号" class="inputxt form-control">
                                                    <i class="ace-icon fa fa-user"></i>
                                                </span>
                                            </label>

                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="password" value="" name="password" nullmsg="请输入密码！" datatype="*" placeholder="密码" class="inputxt form-control">
                                                    <i class="ace-icon fa fa-lock"></i>
                                                </span>
                                            </label>

                                            <label class="block clearfix" id="ajaxTips">
                                                &nbsp;
                                            </label>

                                            <div class="clearfix">
                                                <!-- <label class="inline">
                                                    <input type="checkbox" class="ace" />
                                                    <span class="lbl">&nbsp;记住我</span>
                                                </label> -->
                                                <button type="submit" class="width-35 pull-right btn btn-sm btn-primary ajaxpost" name="login"> <i class="ace-icon fa fa-key"></i><span class="bigger-110">登录</span></button>
                                            </div>

                                            <div class="space-4"></div>
                                        </fieldset>
                                    </form>
                                </div><!-- /.widget-main -->

                            </div><!-- /.widget-body -->
                        </div><!-- /.login-box -->

                    </div><!-- /.position-relative -->

                    <div class="navbar-fixed-top align-right">
                        <br />
                        &nbsp;
                        <a id="btn-login-dark" href="#">暗色</a>
                        &nbsp;
                        <span class="blue">/</span>
                        &nbsp;
                        <a id="btn-login-blur" href="#">渐变</a>
                        &nbsp;
                        <span class="blue">/</span>
                        &nbsp;
                        <a id="btn-login-light" href="#">明亮</a>
                        &nbsp; &nbsp; &nbsp;
                    </div>

                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.main-content -->
</div><!-- /.main-container -->
<div id="Validform_msg"></div>
<!-- basic scripts -->

<!--[if !IE]> -->
<script src='/public/adminlte/js/jquery.2.1.1.min.js' type="text/javascript"></script>
<!-- <![endif]-->

<!--[if IE]>
<script src='/public/adminlte/js/jquery.1.11.1.min.js' type="text/javascript"></script>
<![endif]-->

<!--[if IE]>
<script type="text/javascript">
    window.jQuery || document.write("<script src='/public/adminlte/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='/public/adminlte/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>

<!-- inline scripts related to this page -->
<script src='/public/adminlte/js/admin/login.js' type="text/javascript"></script>
<script src='/public/adminlte/js/Validform_v5.3.2.js' type="text/javascript"></script>
<script src='/public/adminlte/js/jquery.Validform.extends.js' type="text/javascript"></script>
</body>
</html>