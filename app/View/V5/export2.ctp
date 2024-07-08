<div class="users form">
<?php echo $this->Form->create('Export', array('type' => 'file', 'id' => 'ToolsExportForm')); ?>
	<fieldset>
		<legend><?php echo __('Export'); ?></legend>
	<?php
	    echo $this->Form->input('csv', array('type' => 'file', 'required' => true, 'div' => 'form-group'));
	?>
	</fieldset>
	<?php echo $this->Form->submit('Export', array('class' => array('btn btn-primary'), 'div' => array('class' => 'form-group text-right'))); ?>
<?php echo $this->Form->end(null); ?>
</div>