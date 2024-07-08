<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title_for_layout; ?></title>

	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
		echo $this->Html->css('//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900|PT+Sans:400,700|PT+Sans+Narrow:400,700');
		echo $this->Html->css('styles_v5');
		if ($step == 'customer_details' || $step == 1 || $step == 2) {
			echo $this->Html->css('responsive');
		}
		echo $this->Html->script('//code.jquery.com/jquery-1.11.0.min.js');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('//code.jquery.com/ui/1.10.4/jquery-ui.js');
		echo $this->Html->script('jquery.jcarousel.min');
		echo $this->Html->script('jquery.jcarousel-control.min');
		echo $this->Html->script('jquery.jcarousel-pagination.min');
		echo $this->Html->script('jquery.cookie');
		echo $this->Html->script('jquery.maskedinput');
		echo $this->Html->script('clipboard.min');
		echo $this->Html->script('scripts_v5');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php if ($step == 3):?>
    <style>
		@media (max-width: 935px) {
			body {
				width: 935px !important;
			}
			.container {
				width: 890px !important;
			}
			.col-sm-6 {
				width: 50%;
				float: left;
			}
		}
	</style>
    <?php endif;?>
 
</head>
<body class="v5">
<header id="header" class="clearfix">
	<div class="container">
    	<div class="row">
        	<div class="col-sm-6">
            	<div id="logo">
                	<a href="https://dealexpert.com.au/"><?php echo $this->Html->image('logo.png', array('alt' => 'Home','class'=>'img-responsive'));?></a>
                </div>
            </div>    
            <div class="col-sm-6">
            	<div id="tagline">
                	<div id="tel"><span class=""><a href="tel:1300087011">1300 087 011</a></span></div>
                    <div id="open-hours">Mon-Thu: 9am-6pm | Fri: 9am-4pm</div>
                </div>
            </div>
        </div>
    </div>    
</header>
<nav id="nav" class="hidden-phone clearfix">
	<div class="container">
    	<div class="row">
        	<div class="col-xs-12">
        	<ul>
            <li><a href="https://dealexpert.com.au/">Home</a></li>  
            <li <?php if ($step == 1):?>class="active"<?php endif;?>><?php if ($step1):?><a href="/<?php echo $this->params['controller'];?>/compare/1"><?php endif;?>About You<?php if ($step1):?></a><?php endif;?></li>
            <li <?php if ($step == 2):?>class="active"<?php endif;?>><?php if ($step2):?><a href="/<?php echo $this->params['controller'];?>/compare/2"><?php endif;?>Product Options<?php if ($step2):?></a><?php endif;?></li> 
            <li <?php if ($step == 3):?>class="active"<?php endif;?>><?php if ($step1 && $step2):?><a href="/<?php echo $this->params['controller'];?>/compare/3"><?php endif;?>See Your Results<?php if ($step1 && $step2):?></a><?php endif;?></li>
            <li <?php if ($step == 4):?>class="active"<?php endif;?>><a href="/<?php echo $this->params['controller'];?>/form1">Energy Solutions</a></li>
            </ul>
            </div>
        </div>
    </div>    
</nav>
<nav class="navbar navbar-default visible-phone" role="navigation">
	<div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Menu</a>
        </div>
    	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      		<ul class="nav navbar-nav">
                <li><a href="https://dealexpert.com.au/">Home</a></li>  
                <li <?php if ($step == 1):?>class="active"<?php endif;?>><?php if ($step1):?><a href="/<?php echo $this->params['controller'];?>/compare/1"><?php else:?><a href="#"><?php endif;?>About You</a></li>
                <li <?php if ($step == 2):?>class="active"<?php endif;?>><?php if ($step2):?><a href="/<?php echo $this->params['controller'];?>/compare/2"><?php else:?><a href="#"><?php endif;?>Product Options</a></li> 
                <li <?php if ($step == 3):?>class="active"<?php endif;?>><?php if ($step1 && $step2):?><a href="/<?php echo $this->params['controller'];?>/compare/3"><?php else:?><a href="#"><?php endif;?>See Your Results</a></li>
			</ul>
		</div>
	</div>
</nav>            
<div id="main" class="clearfix">
	<div class="container">
		<div class="row">
        	<div class="col-xs-12">
        		<?php echo $this->Session->flash(); ?>
            	<?php echo $this->fetch('content'); ?>
            </div>
        </div>
    </div>
</div>
<footer id="footer">
	<div class="inner">
    <div class="container">
    <div class="row">
		<div class="copy col-xs-12">
			<p><a href="https://dealexpert.com.au/">Copyright &copy; <?php echo date('Y');?> Deal Expert</a></p>
			<p><?php if ($step == 3):?>Development Mode <input type="checkbox" name="development_mode" id="development_mode" value="1"><?php endif;?></p>
		</div>
	</div>
    
    </div>
    </div>
    <?php //echo $this->element('sql_dump'); ?>

</footer>

</body>
</html>