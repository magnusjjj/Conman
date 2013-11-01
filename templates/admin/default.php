<!DOCTYPE html>
<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo Settings::$path;?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
        <script type="text/javascript" src="<?php echo Settings::$path;?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/admin/css/bootstrap.min.css"/>
        <style type="text/css">
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
            .sidebar-nav {
                padding: 9px 0;
            }
        </style>
        <link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/admin/css/bootstrap-responsive.min.css"/>
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="#">Conman Admin - <?php echo Settings::$Society;?>-edition</a>
                    <div class="nav-collapse collapse">
                        <p class="navbar-text pull-right">
                            Logged in as <a href="#" class="navbar-link"><?php $user = Auth::user(true); echo $user['username'];?></a> ( <a href="<?php echo Router::url('/index/logout');?>" class="navbar-link">Logga ut</a> )
                        </p>
                        <ul class="nav">
                            <li class="active"><a href="<?php echo Router::url('index');?>">Ordrar</a></li>
                            <li><a href="<?php echo Router::url('/entrance');?>">Entré</a></li>
                            <li><a href="<?php echo Router::url('members');?>">Medlemmar</a></li>
                            <li><a href="<?php echo Router::url('typemembers');?>">Medlemmar efter köp</a></li>
                            <li><a href="<?php echo Router::url('status');?>">Status</a></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <div class="container-fluid">
                <div class="row-fluid"><?php echo ErrorHelper::print_errors();?></div>
                <div class="row-fluid">          <div class="hero-unit">
                    <h1>Hello, world!</h1>
                    <p>This is a template for a simple marketing or informational website. It includes a large callout called the hero unit and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
                    <p><a class="btn btn-primary btn-large">Learn more &raquo;</a></p>
                </div></div>
                <?php

                    if(isset($con))
                        $con->render();
                ?>
        </div>
    </body>
</html>
