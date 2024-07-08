<div class="users form">
	<?php if ($current_user['User']['id'] != $this->data['User']['id']): ?>
		<div class="delete">
			<?php echo $this->Form->postLink(__('Delete this User'), array('action' => 'delete', $this->data['User']['id']), array('class' => 'btn-danger btn'), __('Are you sure you want to delete # %s?', $this->data['User']['id'])); ?>
		</div>
	<?php endif; ?>
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('role', array('options' => unserialize(USER_TYPES), 'value' => USER_REGISTRANT, 'div' => 'form-group', 'class' => 'form-control')); 
		echo $this->Form->input('name', array('div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('timezone', array('options' => $this->Timezone->show(), 'type' => 'select', 'div' => 'form-group', 'class' => 'form-control')); 
		echo $this->Form->input('email', array('div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('phone', array('div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('website', array('div' => 'form-group', 'class' => 'form-control'));
		echo $this->Form->input('password', array('value' => '', 'required' => false, 'div' => 'form-group', 'class' => 'form-control'));
	?>
	</fieldset>
	<?php echo $this->Form->submit('Save', array('class' => array('btn btn-primary'), 'div' => array('class' => 'form-group text-right'))); ?>
<?php echo $this->Form->end(null); ?>
</div>