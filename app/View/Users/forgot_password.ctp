<div class="row">
<div class="col-xs-12 main">
<div class="forgot-password-form">
<div class="well clearfix">
<?php echo $this->Form->create('User', array('class' => 'form-float')); ?>
    <fieldset>
        <legend><?php echo __('Create temporary password'); ?></legend>
        <?php echo $this->Form->input('email', array('label' => 'Email address','div' => 'form-group','class' => 'form-control')); ?>
        <?php echo $this->Form->submit('Create temporary password', array('class' => 'btn btn-primary', 'div' => array('class' => 'form-group text-right'))); ?>
     </fieldset>
<?php echo $this->Form->end(null); ?>
</div> 
</div>
</div>
</div>