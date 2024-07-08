<div class="users form">
<?php echo $this->Form->create('Export', array('id' => 'ToolsExportForm')); ?>
	<fieldset>
		<legend><?php echo __('Export'); ?></legend>
	<?php
	    echo $this->Form->input('state', array('options' => $states, 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('postcode', array('required' => true, 'div' => 'form-group', 'class' => 'form-control', 'autocomplete' => 'off'));
	    echo $this->Form->input('suburb', array('options' => array('' => 'Please select'), 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('plan_type', array('options' => $plan_types, 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('customer_type', array('options' => $customer_types, 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('nmi', array('label' => 'NMI', 'div' => 'form-group nmi', 'class' => 'form-control'));
        echo $this->Form->input('tariff_code', array('options' => array('' => 'Please select'), 'div' => 'form-group tariff-code', 'class' => 'form-control'));
		echo $this->Form->input('consumption_level', array('options' => $consumption_levels, 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	?>
	</fieldset>
	<?php echo $this->Form->submit('Export', array('class' => array('btn btn-primary'), 'div' => array('class' => 'form-group text-right'))); ?>
<?php echo $this->Form->end(null); ?>
</div>