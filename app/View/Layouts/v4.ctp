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
		echo $this->Html->css('styles_v4');
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
		echo $this->Html->script('scripts_v4');

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
<body class="v4">
<div class="v4_header"><div class="container"><div class="row"><div class="col-sm-12"><div class="inner">
	<div class="v4_logo"><a href="https://dealexpert.com.au/"><?php echo $this->Html->image('v4/v4_logo.png', array('alt' => ''));?></a></div>
	<div class="v4_call"><a href="tel:1300087011"><?php echo $this->Html->image('v4/v4_call.png', array('alt' => ''));?></a></div>
</div></div></div></div></div>         
<div id="main" class="v4_main clearfix">
	<div class="container">
		<div class="row">
        	<div class="col-xs-12">
        		<?php echo $this->Session->flash(); ?>
            	<?php echo $this->fetch('content'); ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>