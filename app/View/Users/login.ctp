<div class="row">
<div class="col-xs-12 main">
<div class="login-form">
<div class="well clearfix">
<?php echo $this->Form->create('User', array('class' => 'form-float')); ?>
    <fieldset>
        <legend><?php echo __('Login'); ?></legend>
    	<?php
	        echo $this->Form->input('email', array(
				'autocomplete' => 'off',
				'div' => 'form-group',
				'class' => 'form-control'
			));
		?>

		<?php
	        echo $this->Form->input('password', array(
				'after' => '<div><small>'.$this->Html->link('Forgot your password?', array('controller' => 'users', 'action' => 'forgot_password')).'</small></div>',
				'autocomplete' => 'off',
				'div' => 'form-group',
				'class' => 'form-control'
			));
	    ?>

		<?php echo $this->Form->submit('Login', array('class' => array('btn btn-primary'), 'div' => array('class' => 'form-group text-right'))); ?>

    </fieldset>
<?php echo $this->Form->end(null); ?>
</div> 
</div>
</div>
</div>