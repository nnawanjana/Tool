<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Create New User'); ?></legend>
	<?php
		echo $this->Form->input('role', array('options' => unserialize(USER_TYPES), 'value' => USER_REGISTRANT, 'div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('name', array('required' => true, 'div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('email', array('div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('phone', array('required' => false, 'div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('website', array('required' => false, 'div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('timezone', array('options' => $this->Timezone->show(), 'value' => 'Australia/Melbourne', 'div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('password', array('autocomplete' => 'off', 'div' => 'form-group', 'class' => 'form-control'));
	?>
	</fieldset>
	<?php echo $this->Form->submit('Create User', array('class' => array('btn btn-primary'), 'div' => array('class' => 'form-group text-right'))); ?>
<?php echo $this->Form->end(null); ?>
</div>