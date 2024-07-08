<div class="users form">
<p>It seems that we were unable to help you out today with a new energy plan, but there are plenty of other ways to reduce your energy costs. If you're happy, my advice would be to keep you on file and get our energy solutions team to touch base with you, and they can see what would work well for you.</p>
<?php echo $this->Form->create('Form1', array('id' => 'ToolsLeadForm1')); ?>
	<fieldset>
		<legend><?php echo __('Lead'); ?></legend>
	<?php
	    echo $this->Form->input('sid', array('label' => 'Lead ID', 'id' => 'sid', 'name' => 'sid', 'value' => $sid, 'type' => 'number', 'required' => false, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('campaign_id', array('id' => 'campaign_id', 'name' => 'campaign_id', 'type' => 'hidden'));
	    echo $this->Form->input('campaign_name', array('id' => 'campaign_name', 'name' => 'campaign_name', 'type' => 'hidden'));
	    echo $this->Form->input('first_campaign', array('id' => 'first_campaign', 'type' => 'hidden'));
	    echo $this->Form->input('agent_name', array('id' => 'agent_name', 'name' => 'agent_name', 'type' => 'text', 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('agent_id', array('id' => 'agent_id', 'name' => 'agent_id', 'type' => 'hidden'));
	    echo $this->Form->input('name', array('label' => 'Customer Full Name', 'id' => 'name', 'name' => 'name', 'type' => 'text', 'required' => true, 'div' => 'form-group', 'class' => 'form-control', 'autocomplete' => 'off'));
	    echo $this->Form->input('phone_number', array('id' => 'phone_number', 'name' => 'phone_number', 'type' => 'tel', 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('email', array('id' => 'email', 'name' => 'email', 'type' => 'email', 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('street', array('id' => 'street', 'name' => 'street', 'type' => 'text', 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('postcode', array('id' => 'postcode', 'name' => 'postcode','required' => true, 'div' => 'form-group', 'class' => 'form-control', 'autocomplete' => 'off'));
	    echo $this->Form->input('suburb', array('id' => 'suburb', 'name' => 'suburb', 'options' => array('' => 'Please select'), 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('state', array('id' => 'state', 'name' => 'state', 'options' => $states, 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('renant_owner', array('label' => 'Owner / Renter', 'id' => 'renant_owner', 'name' => 'renant_owner', 'options' => $owner_renter, 'required' => true, 'div' => 'form-group', 'class' => 'form-control'));
	    echo $this->Form->input('solar', array('id' => 'solar', 'name' => 'solar', 'options' => $solar, 'div' => 'form-group solar', 'class' => 'form-control'));
        echo $this->Form->input('batter_storage', array('label' => 'Battery Storage Solution', 'id' => 'batter_storage', 'name' => 'batter_storage', 'required' => true, 'options' => $batter_storage, 'div' => 'form-group batter-storage', 'class' => 'form-control'));
        echo $this->Form->input('batter_storage_solar', array('label' => 'Battery Storage + Solar Solution EOI', 'id' => 'batter_storage_solar', 'name' => 'batter_storage_solar', 'required' => true, 'options' => $batter_storage_solar, 'div' => 'form-group batter-storage-solar', 'class' => 'form-control'));
	?>
	</fieldset>
	<?php echo $this->Form->submit('Save', array('class' => array('btn btn-primary'), 'div' => array('class' => 'form-group text-right'))); ?>
<?php echo $this->Form->end(null); ?>
</div>