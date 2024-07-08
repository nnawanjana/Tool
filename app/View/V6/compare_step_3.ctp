
<script type="text/javascript">
$(document).ready(function() {
<?php if (!empty($step1) && $step1['tariff2']):?>
$('.add-tariff2').trigger("click");
<?php endif;?>
<?php if (!empty($step1) && $step1['tariff3']):?>
$('.add-tariff3').trigger("click");
<?php endif;?>
<?php if (!empty($step1) && $step1['tariff4']):?>
$('.add-tariff4').trigger("click");
<?php endif;?>
});
</script>
<input type="hidden" id="state" value="<?php echo $state;?>">
<input type="hidden" id="plan_type" value="<?php echo $filters['plan_type'];?>">
<input type="hidden" id="plan_type_original" value="<?php echo $filters['plan_type'];?>">
<input type="hidden" id="elec_recent_bill_original" value="<?php echo $step1['elec_recent_bill'];?>">
<input type="hidden" id="gas_recent_bill_original" value="<?php echo $step1['gas_recent_bill'];?>">
<div class="modal fade" id="electricity_modal" tabindex="-1" role="dialog" aria-labelledby="electricity_modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body clearfix">
      	<form id="step3_electricity_form" class="step" onsubmit="return false;">
      		<p class="topline">To start saving and instantly see your results, please fill in your details below.</p>
      			<div class="form-horizontal">
      				<div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Do you have a recent electricity bill in front of you?</label>
                        <div class="col-sm-5">
                        	<input type="hidden" name="elec_recent_bill" value="<?php echo (!empty($step1) && $step1['elec_recent_bill'] == 'Yes' && ($step1['plan_type'] == 'Dual' || $step1['plan_type'] == 'Elec')) ? 'Yes' : 'No';?>" id="elec_recent_bill">
                            <div class="radio-simulate elec-recent-bill-choices">
                            	<div class="bar"></div>
                                <div id="Yes" class="choice">Yes</div>
                                <div id="No" class="choice">No</div>
                            </div>
                        </div>
                    </div>
      				</div>
                    <div class="form-section col-sm-12 clearfix e-y e-n"> 
                    	<h2>Electricity Details</h2>
                    	<div class="e-y hidden-field">
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Who is your current electricity supplier?</label>
                            <div class="col-sm-5">
                                <select class="form-control" name="elec_supplier" id="elec_supplier">
                                    <option value="">Please Select</option>
                                    <option value="ActewAGL">ActewAGL</option>
									<option value="AGL">AGL</option>
									<option value="Alinta Energy">Alinta Energy</option>
									<option value="Australian Power & Gas">Australian Power & Gas</option>
									<option value="Click Energy">Click Energy</option>
									<option value="Dodo Power & Gas">Dodo Power & Gas</option>
									<option value="Diamond Energy">Diamond Energy</option>
									<option value="Energy Australia (TRUenergy)">Energy Australia (TRUenergy)</option>
									<option value="Ergon Energy">Ergon Energy</option>
									<option value="Lumo Energy">Lumo Energy</option>
									<option value="Momentum">Momentum</option>
									<option value="Neighbourhood Energy">Neighbourhood Energy</option>
									<option value="Origin Energy">Origin Energy</option>
									<option value="Powerdirect">Powerdirect</option>
									<option value="Powershop">Powershop</option>
									<option value="QEnergy">QEnergy</option>
									<option value="Red Energy">Red Energy</option>
									<option value="Sanctuary Energy">Sanctuary Energy</option>
									<option value="Simply Energy">Simply Energy</option>
									<option value="Energy Australia">Energy Australia</option>
									<option value="Sumo Power">Sumo Power</option>
									<option value="ERM">ERM</option>
									<option value="Next Business Energy">Next Business Energy</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>How many days are in the billing period?</label>
                            <div class="col-sm-2 col-xs-9">
                                <input type="text" class="form-control" placeholder="" name="elec_billing_days" id="elec_billing_days" value="">
                            </div>
                            <label class="control-label col-sm-1 col-xs-3">days</label>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Spend</label>
                            <div class="col-sm-2 col-xs-9">
                            	<div class="has-prefix">
                            	<div class="prefix">$</div>
                                <input type="text" class="form-control" placeholder="" name="elec_spend" id="elec_spend" value="">
                            	</div>
                            </div>
                    	</div>
                    	</div>
                    	<div class="e-n hidden-field">
						<div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="If you're unsure, click Unsure/Other"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Who is your current electricity supplier?</label>
                        <div class="col-sm-5">
                            <select class="form-control" name="elec_supplier2" id="elec_supplier2">
                            	<option value="">Please Select</option>
                            	<option value="ActewAGL" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
								<option value="AGL" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
								<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Alinta Energy') ? 'selected="selected"' : '';?>>Alinta Energy</option>
								<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
								<option value="Click Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Click Energy') ? 'selected="selected"' : '';?>>Click Energy</option>
								<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
								<option value="Diamond Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Diamond Energy') ? 'selected="selected"' : '';?>>Diamond Energy</option>
								<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
								<option value="Ergon Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Ergon Energy') ? 'selected="selected"' : '';?>>Ergon Energy</option>
								<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
								<option value="Momentum" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Momentum') ? 'selected="selected"' : '';?>>Momentum</option>
								<option value="Neighbourhood Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Neighbourhood Energy') ? 'selected="selected"' : '';?>>Neighbourhood Energy</option>
								<option value="Origin Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Origin Energy') ? 'selected="selected"' : '';?>>Origin Energy</option>
								<option value="Powerdirect" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Powerdirect') ? 'selected="selected"' : '';?>>Powerdirect</option>
								<option value="Powershop" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Powershop') ? 'selected="selected"' : '';?>>Powershop</option>
								<option value="QEnergy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'QEnergy') ? 'selected="selected"' : '';?>>QEnergy</option>
								<option value="Red Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Red Energy') ? 'selected="selected"' : '';?>>Red Energy</option>
								<option value="Sanctuary Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Sanctuary Energy') ? 'selected="selected"' : '';?>>Sanctuary Energy</option>
								<option value="Simply Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Simply Energy') ? 'selected="selected"' : '';?>>Simply Energy</option>
								<option value="Energy Australia" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Energy Australia') ? 'selected="selected"' : '';?>>Energy Australia</option>
								<option value="Sumo Power" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Sumo Power') ? 'selected="selected"' : '';?>>Sumo Power</option>
								<option value="ERM" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'ERM') ? 'selected="selected"' : '';?>>ERM</option>
								<option value="Next Business Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Next Business Energy') ? 'selected="selected"' : '';?>>Next Business Energy</option>
								<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>What level best describes your typical electricity usage?</label>
                        <div class="col-sm-7">
                        	<div class="elec-usages">
                            	<input type="hidden" name="elec_usage_level" value="<?php echo (!empty($step1)) ? $step1['elec_usage_level'] : '';?>" id="elec_usage_level">
                            	<div id="Low" class="usage">
                                	<div class="item">
                                		<h4><div class="status"></div>LOW</h4>
                                    	<p>Your total bill is  usually less than $500 per quarter</p>
                                    </div>
								</div>
                                <div id="Medium" class="usage">
                                	<div class="item">
                                    	<h4><div class="status"></div>MEDIUM</h4>
                                    	<p>Your total bill is usually $501 to $750 per quarter</p>
                                    </div>
								</div>
								<div id="High" class="usage">
                                	<div class="item">
                            			<h4><div class="status"></div>HIGH</h4>
                            			<p>Your total bill is usually more than $750 per quarter</p>
                                    </div>
                            	</div>
                            </div>    
                        </div>
                    </div>
                    </div>
                    <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>NMI</label>
                            <div class="col-sm-2 col-xs-9">
                                <input type="text" class="form-control" placeholder="" name="nmi" id="nmi" value="<?php echo (!empty($step1)) ? $step1['nmi'] : '';?>" maxlength="11"> <!--62032659066-->
                            </div>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Tariff</label>
                            <input type="hidden" name="tariff_parent" id="tariff_parent" value="<?php echo (!empty($step1)) ? $step1['tariff_parent'] : '';?>">
                            <div class="col-sm-3 col-xs-9">
                                <select class="form-control" name="tariff1" id="tariff1">
                            		<option value="">Tariff</option>
								</select>
								<span class="plus add-tariff2">+</span>
                            </div>
                            <div class="col-sm-3 col-xs-9" id="tariff2_field" style="display:none;">
	                            <select class="form-control" name="tariff2" id="tariff2">
                            		<option value="">Tariff</option>
								</select>
								<span class="plus add-tariff3">+</span>&nbsp;&nbsp;<span class="delete-tariff2">-</span>
                            </div>
                            <div class="col-sm-3 col-xs-9" id="tariff3_field" style="display:none;">
	                            <select class="form-control" name="tariff3" id="tariff3">
                            		<option value="">Tariff</option>
								</select>
								<span class="plus add-tariff4">+</span>&nbsp;&nbsp;<span class="delete-tariff3">-</span>
                            </div>
                            <div class="col-sm-3 col-xs-9" id="tariff4_field" style="display:none;">
	                            <select class="form-control" name="tariff4" id="tariff4">
                            		<option value="">Tariff</option>
								</select>
								<span class="delete-tariff4">-</span>
                            </div>
                    	</div>
                        <div class="form-group" id="elec_meter_type_fields">
                            <label class="control-label col-sm-4 col-sm-offset-1">How are you charged for electricity?</label>
                            <div class="col-sm-7">
                                <div class="radio" id="singlerate_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Single Rate</strong><br><span class="des">One rate at all times</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="singlerate_peak" id="singlerate_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_peak'])) ? $step1['singlerate_peak'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cl1_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="singlerate_cl1_peak" id="singlerate_cl1_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1_peak'])) ? $step1['singlerate_cl1_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="singlerate_cl1" id="singlerate_cl1" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1'])) ? $step1['singlerate_cl1'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cl2_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 2</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="singlerate_cl2_peak" id="singlerate_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl2_peak'])) ? $step1['singlerate_cl2_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="singlerate_cl2" id="singlerate_cl2" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl2'])) ? $step1['singlerate_cl2'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cl1_cl2_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1 + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1 + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1 and Controlled Load 2</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="singlerate_cl1_cl2_peak" id="singlerate_cl1_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1_cl2_peak'])) ? $step1['singlerate_cl1_cl2_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="singlerate_2_cl1" id="singlerate_2_cl1" value="<?php echo (!empty($step1) && isset($step1['singlerate_2_cl1'])) ? $step1['singlerate_2_cl1'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="singlerate_2_cl2" id="singlerate_2_cl2" value="<?php echo (!empty($step1) && isset($step1['singlerate_2_cl2'])) ? $step1['singlerate_2_cl2'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="singlerate_cs_peak" id="singlerate_cs_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cs_peak'])) ? $step1['singlerate_cs_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="singlerate_cs" id="singlerate_cs" value="<?php echo (!empty($step1) && isset($step1['singlerate_cs'])) ? $step1['singlerate_cs'] : '';?>"></div>
										<div class="col-sm-4 col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="singlerate_cs_billing_start" id="singlerate_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['singlerate_cs_billing_start'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cl1_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1 + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1 + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1 and Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="singlerate_cl1_cs_peak" id="singlerate_cl1_cs_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1_cs_peak'])) ? $step1['singlerate_cl1_cs_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="singlerate_3_cl1" id="singlerate_3_cl1" value="<?php echo (!empty($step1) && isset($step1['singlerate_3_cl1'])) ? $step1['singlerate_3_cl1'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="singlerate_3_cs" id="singlerate_3_cs" value="<?php echo (!empty($step1) && isset($step1['singlerate_3_cs'])) ? $step1['singlerate_3_cs'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="singlerate_cl1_cs_billing_start" id="singlerate_cl1_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['singlerate_cl1_cs_billing_start'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="timeofuse_peak" id="timeofuse_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_peak'])) ? $step1['timeofuse_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_offpeak" id="timeofuse_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_offpeak'])) ? $step1['timeofuse_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12" id="timeofuse_shoulder_field"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_shoulder" id="timeofuse_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_shoulder'])) ? $step1['timeofuse_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_PowerSmart_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (PowerSmart)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_ps_peak" id="timeofuse_ps_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ps_peak'])) ? $step1['timeofuse_ps_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_ps_offpeak" id="timeofuse_ps_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ps_offpeak'])) ? $step1['timeofuse_ps_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12" id="timeofuse_shoulder_field"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_ps_shoulder" id="timeofuse_ps_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ps_shoulder'])) ? $step1['timeofuse_ps_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_LoadSmart_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (LoadSmart)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_ls_peak" id="timeofuse_ls_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ls_peak'])) ? $step1['timeofuse_ls_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_ls_offpeak" id="timeofuse_ls_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ls_offpeak'])) ? $step1['timeofuse_ls_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12" id="timeofuse_shoulder_field"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_ls_shoulder" id="timeofuse_ls_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ls_shoulder'])) ? $step1['timeofuse_ls_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak and Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="timeofuse_cs_peak" id="timeofuse_cs_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs_peak'])) ? $step1['timeofuse_cs_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cs_offpeak" id="timeofuse_cs_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs_offpeak'])) ? $step1['timeofuse_cs_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="timeofuse_cs" id="timeofuse_cs" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs'])) ? $step1['timeofuse_cs'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="timeofuse_cs_billing_start" id="timeofuse_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['timeofuse_cs_billing_start'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_cl1_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + CL1 + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + CL1 + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak, Controlled Load 1 and Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-3 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="timeofuse_cl1_cs_peak" id="timeofuse_cl1_cs_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_cs_peak'])) ? $step1['timeofuse_cl1_cs_peak'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cl1_cs_offpeak" id="timeofuse_cl1_cs_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_cs_offpeak'])) ? $step1['timeofuse_cl1_cs_offpeak'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="timeofuse_cl1" id="timeofuse_cl1" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1'])) ? $step1['timeofuse_cl1'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="timeofuse_2_cs" id="timeofuse_2_cs" value="<?php echo (!empty($step1) && isset($step1['timeofuse_2_cs'])) ? $step1['timeofuse_2_cs'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="timeofuse_cl1_cs_billing_start" id="timeofuse_cl1_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['timeofuse_cl1_cs_billing_start'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_cl1_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + CL1" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + CL1') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak, Controlled Load 1</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-3 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="timeofuse_cl1_peak" id="timeofuse_cl1_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_peak'])) ? $step1['timeofuse_cl1_peak'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cl1_offpeak" id="timeofuse_cl1_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_offpeak'])) ? $step1['timeofuse_cl1_offpeak'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="timeofuse_2_cl1" id="timeofuse_2_cl1" value="<?php echo (!empty($step1) && isset($step1['timeofuse_2_cl1'])) ? $step1['timeofuse_2_cl1'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12" id="timeofuse_cl1_shoulder_field"><span class="des">Shoulder</span><input type="text" class="form-control" name="timeofuse_cl1_shoulder" id="timeofuse_cl1_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_shoulder'])) ? $step1['timeofuse_cl1_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_cl2_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak, Controlled Load 2</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-3 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="timeofuse_cl2_peak" id="timeofuse_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl2_peak'])) ? $step1['timeofuse_cl2_peak'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cl2_offpeak" id="timeofuse_cl2_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl2_offpeak'])) ? $step1['timeofuse_cl2_offpeak'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="timeofuse_2_cl2" id="timeofuse_2_cl2" value="<?php echo (!empty($step1) && isset($step1['timeofuse_2_cl2'])) ? $step1['timeofuse_2_cl2'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12" id="timeofuse_cl2_shoulder_field"><span class="des">Shoulder</span><input type="text" class="form-control" name="timeofuse_cl2_shoulder" id="timeofuse_cl2_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl2_shoulder'])) ? $step1['timeofuse_cl2_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_tariff12_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (Tariff 12)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use (Tariff 12)') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use (Tariff 12)</strong><br><span class="des">Peak with Off Peak and Shoulder</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="timeofuse_tariff12_peak" id="timeofuse_tariff12_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_peak'])) ? $step1['timeofuse_tariff12_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_tariff12_offpeak" id="timeofuse_tariff12_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_offpeak'])) ? $step1['timeofuse_tariff12_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_tariff12_shoulder" id="timeofuse_tariff12_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_shoulder'])) ? $step1['timeofuse_tariff12_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_tariff13_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (Tariff 13)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use (Tariff 13)') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use (Tariff 13)</strong><br><span class="des">Peak with Off Peak and Shoulder</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="timeofuse_tariff13_peak" id="timeofuse_tariff13_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_peak'])) ? $step1['timeofuse_tariff13_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_tariff13_offpeak" id="timeofuse_tariff13_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_offpeak'])) ? $step1['timeofuse_tariff13_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_tariff13_shoulder" id="timeofuse_tariff13_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_shoulder'])) ? $step1['timeofuse_tariff13_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="flexible_pricing_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Flexible Pricing" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Flexible Pricing') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Flexible Pricing</strong><br><span class="des">Peak with Off Peak and Shoulder</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des">Peak</span><input type="text" class="form-control" name="flexible_peak" id="flexible_peak" value="<?php echo (!empty($step1) && isset($step1['flexible_peak'])) ? $step1['flexible_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="flexible_offpeak" id="flexible_offpeak" value="<?php echo (!empty($step1) && isset($step1['flexible_offpeak'])) ? $step1['flexible_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="flexible_shoulder" id="flexible_shoulder" value="<?php echo (!empty($step1) && isset($step1['flexible_shoulder'])) ? $step1['flexible_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                            </div>
                    	</div>
                    	<div class="form-group" id="summer_winter_fields" style="display:none;">
                    		<label class="control-label col-sm-4 col-sm-offset-1"></label>
                    		<div class="col-sm-7">
                        	    <div class="col-sm-6 col-xs-12"><span class="des">Winter Peak</span>
                        	        <input type="text" class="form-control" placeholder="" name="elec_winter_peak" id="elec_winter_peak" value="<?php echo (!empty($step1)) ? $step1['elec_winter_peak'] : '';?>">
                        	    </div>
                        	    <div class="col-sm-6 col-xs-12"><span class="des">When did the bill start?</span>
                        	        <input type="text" class="form-control" placeholder="" name="elec_billing_start" id="elec_billing_start" value="<?php echo (!empty($step1)) ? $step1['elec_billing_start'] : '';?>">
                        	    </div>
                    		</div>
						</div>
                    </div>
                    <div class="col-sm-12 clearfix">
                    <div class="form-group" style="position:relative; clear:both;">
                        <a href="javascript:;" class="btn-orange pull-right continue">Get My Estimated Cost</a>
                        <a href="javascript:;" class="btn-grey pull-left close-modal">Cancel</a>
                        <div id="step3_error_message"></div>
                    </div>
                    </div>
      			</div>
      	</form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="gas_modal" tabindex="-1" role="dialog" aria-labelledby="gas_modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body clearfix">
      		<form id="step3_gas_form" class="step" onsubmit="return false;">
      			<p class="topline">To start saving and instantly see your results, please fill in your details below.</p>
      			<div class="form-horizontal">
      				<div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Do you have a recent gas bill in front of you?</label>
                        <div class="col-sm-5">
                        	<input type="hidden" name="gas_recent_bill" value="<?php echo (!empty($step1) && $step1['gas_recent_bill'] == 'Yes' && ($step1['plan_type'] == 'Dual' || $step1['plan_type'] == 'Gas')) ? 'Yes' : 'No';?>" id="gas_recent_bill">
                            <div class="radio-simulate gas-recent-bill-choices">
                            	<div class="bar"></div>
                                <div id="Yes" class="choice">Yes</div>
                                <div id="No" class="choice">No</div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="form-section col-sm-12 clearfix g-y g-n"> 
                    	<h2>Gas Details</h2>
                    	<div class="g-y hidden-field">
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Who is your current gas supplier?</label>
                            <div class="col-sm-5">
                                <select class="form-control" name="gas_supplier" id="gas_supplier">
                                	<option value="">Please Select</option>
                                    <option value="ActewAGL">ActewAGL</option>
									<option value="AGL">AGL</option>
									<option value="Alinta Energy">Alinta Energy</option>
									<option value="Australian Power & Gas">Australian Power & Gas</option>
									<option value="Dodo Power & Gas">Dodo Power & Gas</option>
									<option value="Energy Australia (TRUenergy)">Energy Australia (TRUenergy)</option>
									<option value="Lumo Energy">Lumo Energy</option>
									<option value="Origin Energy">Origin Energy</option>
									<option value="Red Energy">Red Energy</option>
									<option value="Simply Energy">Simply Energy</option>
									<option value="Energy Australia">Energy Australia</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>How many days are in the billing period?</label>
                            <div class="col-sm-2 col-xs-9">
                                <input type="text" class="form-control" placeholder="" name="gas_billing_days" id="gas_billing_days" value="">
                            </div>
                            <label class="control-label col-sm-1 col-xs-3">days</label>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>When did the bill start?</label>
                            <div class="col-sm-2 col-xs-9">
                                <input type="text" class="form-control" placeholder="" name="gas_billing_start" id="gas_billing_start" value="">
                            </div>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Spend</label>
                            <div class="col-sm-2 col-xs-9">
                            	<div class="has-prefix">
                            	<div class="prefix">$</div>
                                <input type="text" class="form-control" placeholder="" name="gas_spend" id="gas_spend" value="">
                            	</div>
                            </div>
                    	</div>
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>What is the usage amount on your bill?</label>
                            <div class="col-sm-5">
                        	<div class="row">
                            	<div class="col-sm-6 col-xs-6"><div class="has-tail"><div class="des">Peak</div><div class="tail">MJ</div><input type="text" class="form-control" placeholder="" name="gas_peak" id="gas_peak" value=""></div></div>
                            	<div class="col-sm-6 col-xs-6"><div class="has-tail"><div class="des">Off-Peak (if shown)</div><div class="tail">MJ</div><input type="text" class="form-control" placeholder="" name="gas_off_peak" id="gas_off_peak" value=""></div></div>
                            </div>
                            </div>
                        </div>
                    	</div>
                    	<div class="g-n hidden-field">
                    	<div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="If you're unsure, click Unsure/Other"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Who is your current gas supplier?</label>
                        <div class="col-sm-5">
                            <select class="form-control" name="gas_supplier2" id="gas_supplier2">
                            	<option value="">Please Select</option>
                            	<option value="ActewAGL" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
								<option value="AGL" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
								<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Alinta Energy') ? 'selected="selected"' : '';?>>Alinta Energy</option>
								<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
								<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
								<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
								<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
								<option value="Origin Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Origin Energy') ? 'selected="selected"' : '';?>>Origin Energy</option>
								<option value="Red Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Red Energy') ? 'selected="selected"' : '';?>>Red Energy</option>
								<option value="Simply Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Simply Energy') ? 'selected="selected"' : '';?>>Simply Energy</option>
								<option value="Energy Australia" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Energy Australia') ? 'selected="selected"' : '';?>>Energy Australia</option>
								<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                            </select>
                        </div>
						</div>
						<div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>What level best describes your typical gas usage?</label>
                        <div class="col-sm-7">
                        	<div class="gas-usages">
                            	<input type="hidden" name="gas_usage_level" value="<?php echo (!empty($step1)) ? $step1['gas_usage_level'] : '';?>" id="gas_usage_level">
                            	<div id="Low" class="usage">
                                	<div class="item">
                                		<h4><div class="status"></div>LOW</h4>
                                    	<p>Your total bill is usually less than $140 every 2 months</p>
                                    </div>
								</div>
                                <div id="Medium" class="usage">
                                	<div class="item">
                                    	<h4><div class="status"></div>MEDIUM</h4>
                                    	<p>Your total bill is usually $141 to $200 every 2 months</p>
                                    </div>
								</div>
								<div id="High" class="usage">
                                	<div class="item">
                            			<h4><div class="status"></div>HIGH</h4>
                            			<p>Your total bill is usually more than $200 every 2 months</p>
                                    </div>
                            	</div>
                            </div>
                        </div>
						</div>
                    </div>
                    </div>
                    <div class="col-sm-12 clearfix">
                    <div class="form-group" style="position:relative; clear:both;">
                        <a href="javascript:;" class="btn-orange pull-right continue">Get My Estimated Cost</a>
                        <a href="javascript:;" class="btn-grey pull-left close-modal">Cancel</a>
                        <div id="step3_error_message"></div>
                    </div>
                    </div>
      			</div>
      		</form>
		</div>
    </div>
  </div>
</div>

<div class="modal fade" id="gst_disclaimer_modal" tabindex="-1" role="dialog" aria-labelledby="gst_disclaimer_modalLabel" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-body clearfix">
	        <h2>Disclaimer</h2>
			<p>I'll provide GST exclusive charges today. You will be required to pay GST on your bills.</p>
	        <div class="col-sm-12 clearfix">
	        <div class="form-group" style="position:relative; clear:both;">
	            <a href="javascript:;" class="btn-orange pull-right close-modal">I have read this</a>
	        </div>
	        </div>
	  	</div>
	  </div>
	</div>
</div>
<div class="modal fade" id="discounts_disclaimer_modal" tabindex="-1" role="dialog" aria-labelledby="discounts_disclaimer_modalLabel" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-body clearfix">
	        <h2>Disclaimer</h2>
			<p>Discounted rates are for comparison purposes only and won't be the rates which will appear on your energy bill.</p>
	        <div class="col-sm-12 clearfix">
	        <div class="form-group" style="position:relative; clear:both;">
	            <a href="javascript:;" class="btn-orange pull-right close-modal">I have read this</a>
	        </div>
	        </div>
	  	</div>
	  </div>
	</div>
</div>

					<div id="step3" class="step clearfix">
					<div style="display: none;" id="processing">
						<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
					</div>
                	<div class="pagination-left"></div>
                    <div class="pagination-right"></div>
                	<div class="row">
                        <div class="col-sm-6 col-xs-12">
                        	<?php
                        	$plan_type_arr = array(
                        		'Dual' => 'Electricity & Gas Bundle',
                        		'Elec' => 'Electricity',
                        		'Gas' => 'Gas',
                        	);
                        	if ($filters['plan_type']) {
	                        	$plan_type = $plan_type_arr[$filters['plan_type']];
                        	} else {
	                        	$plan_type = $plan_type_arr[$step1['plan_type']];
                        	}
                        	$nmi_distributor = array();
                        	if ($step1['nmi_distributor']) {
	                        	$nmi_distributor = explode('/', $step1['nmi_distributor']);
                        	}
                        	?>
                            <h2>Your <?php echo $plan_type;?> Search Results</h2>
                            <p class="topline">You have told us that you live in <strong><?php echo $suburb;?>, <?php echo $postcode;?></strong> and you want to <strong><?php echo $step1['looking_for'];?></strong></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <p>
                            	Comparison Type: <?php echo $step1['customer_type'];?><br />
                            	<?php if ($step1['nmi']):?>NMI: <?php echo $step1['nmi'];?> <?php if ($nmi_distributor):?>(<?php echo $nmi_distributor[0];?>)<?php endif;?><br /><?php endif;?>
                            	<?php
                            	$tariffs = array();
                            	if ($step1['tariff1']) {
                            		$tariff1 = explode('|', $step1['tariff1']);
									if ($tariff1[3] == 'Solar') {
										if (strpos($tariff1[4], '/') !== false) {
											$tariff1[0] .= ' (' . $step1['solar_rebate_scheme'] . ')';
										}
										else {
											$tariff1[0] .= ' (' . $tariff1[4] . ')';
										}
									}
									$tariffs[] = $tariff1[0];
                            	}
                            	if ($step1['tariff2']) {
                            		$tariff2 = explode('|', $step1['tariff2']);
	                            	if ($tariff2[3] == 'Solar') {
										if (strpos($tariff2[4], '/') !== false) {
											$tariff2[0] .= ' (' . $step1['solar_rebate_scheme'] . ')';
										}
										else {
											$tariff2[0] .= ' (' . $tariff2[4] . ')';
										}
									}
									$tariffs[] = $tariff2[0];
                            	}
                            	if ($step1['tariff3']) {
                            		$tariff3 = explode('|', $step1['tariff3']);
	                            	if ($tariff3[3] == 'Solar') {
										if (strpos($tariff3[4], '/') !== false) {
											$tariff3[0] .= ' (' . $step1['solar_rebate_scheme'] . ')';
										}
										else {
											$tariff3[0] .= ' (' . $tariff3[4] . ')';
										}
									}
									$tariffs[] = $tariff3[0];
                            	}
                            	if ($step1['tariff4']) {
                            		$tariff4 = explode('|', $step1['tariff4']);
	                            	if ($tariff4[3] == 'Solar') {
										if (strpos($tariff4[4], '/') !== false) {
											$tariff4[0] .= ' (' . $step1['solar_rebate_scheme'] . ')';
										}
										else {
											$tariff4[0] .= ' (' . $tariff4[4] . ')';
										}
									}
									$tariffs[] = $tariff4[0];
                            	}
                            	?>
                            	<?php if ($tariffs):?>Tariff: <?php echo implode(' & ', $tariffs);?><br /><?php endif;?>
                            	<?php if ($step1['elec_spend']):?>Customer's Elec bill price: $<?php echo $step1['elec_spend'];?><br /><?php endif;?>
                            	<?php if ($step1['gas_spend']):?>Customer's Gas bill price: $<?php echo $step1['gas_spend'];?><br /><?php endif;?>
                            </p>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="plan-top">
                                <div class="plan-tool">
                                    <a class="plan-top-pocks" href="/<?php echo $this->params['controller'];?>/compare/3?view_top_picks=1#step3">View my top picks <span id="top_picks_count">(0)</span> <?php echo $this->Html->image('top-picks.png', array('alt' => ''));?></a>
                                    <a id="clear_my_top_picks" class="plan-clear" href="javascript:;">Clear</a>
                                    <a href="/v6/">Start new comparison</a>
                                    <a class="pause-save" href="javascript:;">Pause & Save</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-xs-12 pull-right">
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <select class="form-control" id="lead_action" name="lead_action">
                                        <option value="">Please select</option>
                                        <option value="193">Did not call EW</option>
                                        <option value="201">Wants To Shop Around</option>
                                        <option value="188">Getting Better Deal Already</option>
                                        <option value="189">In contract</option>
                                        <option value="192">Do Not Call</option>
                                        <option value="191">Not Serviceable</option>
                                        <option value="192">Duplicate lead</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <a href="javascript:;" class="btn-orange pull-left no-sale-button">No Sale</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php $total = count($plans);?>
                    
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                        	
                    		<div class="plan-pagination-wrap">
                            	<div class="total"><?php echo $total;?> results</div>
                            	<?php if ($total > 0):?>
                                <div id="plan-pagination-top">
                                	<a href="#" class="prev"></a>
                                    <ul></ul>
                                    <a href="#" class="next"></a>
                                </div>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    	<form id="filter_results_form" action="/v6/compare/3" method="post">
                    	    <input type="hidden" value="3" name="current_step" id="current_step">
                    	    <input type="hidden" name="sid" value="<?php echo $sid;?>" id="sid">
                    	    <input type="hidden" name="discount_pay_on_time_all" id="discount_pay_on_time_all" value="<?php echo $filters['discount_pay_on_time_all'];?>">
                    	    <input type="hidden" name="discount_guaranteed_all" id="discount_guaranteed_all" value="<?php echo $filters['discount_guaranteed_all'];?>">
                    	    <input type="hidden" name="discount_direct_debit_all" id="discount_direct_debit_all" value="<?php echo $filters['discount_direct_debit_all'];?>">
                    	    <input type="hidden" name="discount_dual_fuel_all" id="discount_dual_fuel_all" value="<?php echo $filters['discount_dual_fuel_all'];?>">
                    	    <input type="hidden" name="discount_bonus_sumo_all" id="discount_bonus_sumo_all" value="<?php echo $filters['discount_bonus_sumo_all'];?>">
                    	    <input type="hidden" name="discount_prepay_all" id="discount_prepay_all" value="<?php echo $filters['discount_prepay_all'];?>">
                    	    <input type="hidden" name="include_gst_all" id="include_gst_all" value="<?php echo $filters['include_gst_all'];?>">
                        	<div class="form-group">
                    		<div class="form-item" style="width:240px;">
                                <div class="form-label col-sm-3" style="margin: 5px 10px 0 -15px;white-space: nowrap;">
                                    <label>Sort By</label>
                                </div>
                                <div class="form-element">
                                    <select name="sort_by" id="sort_by" class="form-control">
                                    <option value="my_preferences" <?php if ($filters['sort_by'] == 'my_preferences'):?>selected="selected"<?php endif;?>>My Preferences</option>
                                    <option value="lowest_price" <?php if ($filters['sort_by'] == 'lowest_price'):?>selected="selected"<?php endif;?>>Lowest Price</option>
                                    <?php if ($filters['plan_type'] == 'Elec' || $filters['plan_type'] == 'Dual'):?>
                                    <option value="elec_peak" <?php if ($filters['sort_by'] == 'elec_peak'):?>selected="selected"<?php endif;?>>Elec Peak Lowest First</option>
                                    <option value="elec_cl" <?php if ($filters['sort_by'] == 'elec_cl'):?>selected="selected"<?php endif;?>>Elec Controlled Load: Lowest First</option>
								    <option value="elec_offpeak" <?php if ($filters['sort_by'] == 'elec_offpeak'):?>selected="selected"<?php endif;?>>Elec Off Peak Lowest First</option>
								    <option value="elec_stp" <?php if ($filters['sort_by'] == 'elec_stp'):?>selected="selected"<?php endif;?>>Elec Supply Charge: Lowest First</option>
                                    <?php endif;?>
                                    <?php if ($filters['plan_type'] == 'Gas' || $filters['plan_type'] == 'Dual'):?>
                                    <option value="gas_peak" <?php if ($filters['sort_by'] == 'gas_peak'):?>selected="selected"<?php endif;?>>Gas Peak Lowest First</option>
                                    <option value="gas_offpeak" <?php if ($filters['sort_by'] == 'gas_offpeak'):?>selected="selected"<?php endif;?>>Gas Off Peak Lowest First</option>
                                    <option value="gas_stp" <?php if ($filters['sort_by'] == 'gas_stp'):?>selected="selected"<?php endif;?>>Gas Supply Charge: Lowest First</option>
                                    <?php endif;?>
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            </div>
                            <div class="plans-search">
                            	<?php if (!$view_top_picks):?>
                                <div class="plans-search-title">Filter Your Results</div>
                                <div class="plans-search-filters">
                                	<input type="hidden" name="customer_type" value="<?php echo $filters['customer_type'];?>">
                                    <div class="filter-scroll">
                                        <div class="form-item"><a href="javascript:;" class="clear-filters">Clear All</a></div>
                                        <div class="form-item">
                                            <div class="form-label active">
                                                <label>Energy Type [3]</label>
                                            </div>
                                            <div class="form-element" style="display:block;">
                                                <label><input type="radio" name="plan_type" class="filter_plan_type" value="Dual" <?php if ($filters['plan_type'] == 'Dual'):?>checked="checked"<?php endif;?>>Electricity & Gas Bundle</label>
                                                <label><input type="radio" name="plan_type" class="filter_plan_type" value="Elec" <?php if ($filters['plan_type'] == 'Elec'):?>checked="checked"<?php endif;?>>Electricity Only</label>
                                                <label><input type="radio" name="plan_type" class="filter_plan_type" value="Gas" <?php if ($filters['plan_type'] == 'Gas'):?>checked="checked"<?php endif;?>>Gas Only</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-item">
                                            <div class="form-label active">
                                                <label>Apply Discounts [3]</label>
                                            </div>
                                            <div class="form-element" style="display:block;">
                                                <label><input type="checkbox" name="discount_type[]" id="discount_type_all" value="all" <?php if (in_array('all', $filters['discount_type'])):?>checked="checked"<?php endif;?>>All</label>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Pay On Time" <?php if (in_array('Pay On Time', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Pay On Time</label>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Guaranteed" <?php if (in_array('Guaranteed', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Guaranteed</label>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Direct Debit" <?php if (in_array('Direct Debit', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Direct Debit</label>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Dual Fuel" <?php if (in_array('Dual Fuel', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Dual Fuel</label>
                                                <?php if (in_array('Bonus', $available_discount_type)):?>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Bonus" <?php if (in_array('Bonus', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Bonus</label>
                                                <?php endif;?>
                                                <?php if (in_array('Prepay', $available_discount_type)):?>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Prepay" <?php if (in_array('Prepay', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Prepay</label>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                        
                                        <div class="form-item">
                                            <div class="form-label <?php if (!empty($filters['contract_length'])) { echo 'active'; }?>"><label>Contract Length [<?php echo count($available_contract_length);?>]</label>
                                            </div>
                                            <div class="form-element" <?php if (!empty($filters['contract_length'])):?>style="display:block;"<?php endif;?>>
                                                <label><input type="checkbox" name="contract_length[]" id="contract_length_all" value="all" <?php if (in_array('all', $filters['contract_length'])):?>checked="checked"<?php endif;?>>All</label>
                                                <?php if (in_array('No Term', $available_contract_length)):?>
                                                <label><input type="checkbox" name="contract_length[]" class="contract_length" value="No Term" <?php if (in_array('No Term', $filters['contract_length'])):?>checked="checked"<?php endif;?>>No Term</label>
                                                <?php endif;?>
                                                <?php if (in_array('12 Months', $available_contract_length)):?>
                                                <label><input type="checkbox" name="contract_length[]" class="contract_length" value="12 Months" <?php if (in_array('12 Months', $filters['contract_length'])):?>checked="checked"<?php endif;?>>12 Months</label>
                                                <?php endif;?>
                                                <?php if (in_array('24 Months', $available_contract_length)):?>
                                                <label><input type="checkbox" name="contract_length[]" class="contract_length" value="24 Months" <?php if (in_array('24 Months', $filters['contract_length'])):?>checked="checked"<?php endif;?>>24 Months</label>
                                                <?php endif;?>
                                                <?php if (in_array('36 Months', $available_contract_length)):?>
                                                <label><input type="checkbox" name="contract_length[]" class="contract_length" value="36 Months" <?php if (in_array('36 Months', $filters['contract_length'])):?>checked="checked"<?php endif;?>>36 Months</label>
												<?php endif;?>
                                            </div>
                                        </div>
                                        
                                        <div class="form-item">
                                            <div class="form-label <?php if (!empty($filters['retailer'])) { echo 'active'; }?>"><label>Suppliers [<?php echo count($available_retailers);?>]</label></div>
                                            <div class="form-element" <?php if (!empty($filters['retailer'])):?>style="display:block;"<?php endif;?>>
                                                <label><input type="checkbox" name="retailer[]" id="retailer_all" value="all" <?php if (in_array('all', $filters['retailer'])):?>checked="checked"<?php endif;?>>All</label>
                                                <?php if (in_array('ActewAGL', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="ActewAGL" <?php if (in_array('ActewAGL', $filters['retailer'])):?>checked="checked"<?php endif;?>>ActewAGL</label>
                                                <?php endif;?>
                                                <?php if (in_array('AGL', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="AGL" <?php if (in_array('AGL', $filters['retailer'])):?>checked="checked"<?php endif;?>>AGL</label>
                                                <?php endif;?>
                                                <?php if (in_array('Alinta Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Alinta Energy" <?php if (in_array('Alinta Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Alinta Energy</label>
                                                <?php endif;?>
                                                <?php if (in_array('Energy Australia', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Energy Australia" <?php if (in_array('Energy Australia', $filters['retailer'])):?>checked="checked"<?php endif;?>>Energy Australia</label>
                                                <?php endif;?>
                                                <?php if (in_array('Elysian Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Energy Energy" <?php if (in_array('Elysian Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Elysian Energy</label>
                                                <?php endif;?>
                                                <?php if (in_array('ERM', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="ERM" <?php if (in_array('ERM', $filters['retailer'])):?>checked="checked"<?php endif;?>>ERM</label>
                                                <?php endif;?>
                                                <?php if (in_array('Lumo Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Lumo Energy" <?php if (in_array('Lumo Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Lumo</label>
                                                <?php endif;?>
                                                <?php if (in_array('Momentum', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Momentum" <?php if (in_array('Momentum', $filters['retailer'])):?>checked="checked"<?php endif;?>>Momentum</label>
                                                <?php endif;?>
                                                <?php if (in_array('Next Business Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Next Business Energy" <?php if (in_array('Next Business Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Next Business Energy</label>
                                                <?php endif;?>
                                                <?php if (in_array('Origin Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Origin Energy" <?php if (in_array('Origin Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Origin Energy</label>
                                                <?php endif;?>
                                                <?php if (in_array('Powerdirect', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Powerdirect" <?php if (in_array('Powerdirect', $filters['retailer'])):?>checked="checked"<?php endif;?>>Powerdirect</label>
                                                <?php endif;?>
                                                <?php if (in_array('Powerdirect and AGL', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Powerdirect and AGL" <?php if (in_array('Powerdirect and AGL', $filters['retailer'])):?>checked="checked"<?php endif;?>>Powerdirect and AGL</label>
                                                <?php endif;?>
                                                <?php if (in_array('Red Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Red Energy" <?php if (in_array('Red Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Red Energy</label>
                                                <?php endif;?>
                                                <?php if (in_array('Powershop', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Powershop" <?php if (in_array('Powershop', $filters['retailer'])):?>checked="checked"<?php endif;?>>Powershop</label>
                                                <?php endif;?>
                                                <?php if (in_array('Sumo Power', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Sumo Power" <?php if (in_array('Sumo Power', $filters['retailer'])):?>checked="checked"<?php endif;?>>Sumo Power</label>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                        
                                        <div class="form-item">
                                            <div class="form-label <?php if (!empty($filters['payment_options'])) { echo 'active'; }?>"><label>Payment Options [<?php echo count($available_payment_options);?>]</label></div>
                                            <div class="form-element" <?php if (!empty($filters['payment_options'])):?>style="display:block;"<?php endif;?>>
                                                <label><input type="checkbox" name="payment_options[]" id="payment_options_all" value="all" <?php if (in_array('all', $filters['payment_options'])):?>checked="checked"<?php endif;?>>All</label>
                                                <?php if (in_array('BPAY', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="bpay" <?php if (in_array('bpay', $filters['payment_options'])):?>checked="checked"<?php endif;?>>BPAY</label>
                                                <?php endif;?>
                                                <?php if (in_array('Credit Card', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="credit_card" <?php if (in_array('credit_card', $filters['payment_options'])):?>checked="checked"<?php endif;?>>Credit Card</label>
                                                <?php endif;?>
                                                <?php if (in_array('EasiPay', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="easipay" <?php if (in_array('easipay', $filters['payment_options'])):?>checked="checked"<?php endif;?>>EasiPay</label>
                                                <?php endif;?>
                                                <?php if (in_array('Online', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="online" <?php if (in_array('online', $filters['payment_options'])):?>checked="checked"<?php endif;?>>Online</label>
                                                <?php endif;?>
                                                <?php if (in_array('Centrepay', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="centrepay" <?php if (in_array('centrepay', $filters['payment_options'])):?>checked="checked"<?php endif;?>>Centrepay</label>
                                                <?php endif;?>
                                                <?php if (in_array('Cash', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="cash" <?php if (in_array('cash', $filters['payment_options'])):?>checked="checked"<?php endif;?>>Cash</label>
                                                <?php endif;?>
                                                <?php if (in_array('Cheque', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="cheque" <?php if (in_array('cheque', $filters['payment_options'])):?>checked="checked"<?php endif;?>>Cheque</label>
                                                <?php endif;?>
                                                <?php if (in_array('POST billpay', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="post_billpay" <?php if (in_array('post_billpay', $filters['payment_options'])):?>checked="checked"<?php endif;?>>POST billpay</label>
                                                <?php endif;?>
                                                <?php if (in_array('Pay By Phone', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="pay_by_phone" <?php if (in_array('pay_by_phone', $filters['payment_options'])):?>checked="checked"<?php endif;?>>Pay By Phone</label>
                                                <?php endif;?>
                                                <?php if (in_array('AMEX', $available_payment_options)):?>
                                                <label><input type="checkbox" name="payment_options[]" class="payment_options" value="amex" <?php if (in_array('amex', $filters['payment_options'])):?>checked="checked"<?php endif;?>>AMEX</label>
                                                <?php endif;?>
                                            </div>
                                        </div>
										<!--
                                        <div class="form-item">
                                            <div class="form-label"><label>Solar [1]</label></div>
                                            <div class="form-element">
                                                <label><input type="checkbox" name="solar" class="solar" value="1">Solar Comparison</label>
                                            </div>
                                        </div>
                                        -->
                                         <div class="form-item"><a href="javascript:;" class="clear-filters">Clear All</a></div>
                                    </div>
									<div class="extra-title">Your Important Preferences</div>
									<div class="extra"><span class="info" title="Secure your rates for the length of the contract"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-2.png">Rate freeze plans</div>
									<div class="extra"><span class="info" title="No exit-fees, and the flexibility to change plans"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-3.png">No contract term</div>
									<div class="extra"><span class="info" title="Based on average usage, pay your bills in smaller portions"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-4.png">Bill smoothing</div>
									<div class="extra"><span class="info" title="View your account details from your computer or mobile device"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-5.png">Online account management</div>
									<div class="extra"><span class="info" title="Keep an eye on your usage from your computer or mobile device"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-6.png">Energy monitoring tools</div>
									<div class="extra"><span class="info" title="Be rewarded for choosing a certain plan"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-7.png">Membership rewards</div>
									<div class="extra"><span class="info" title="Environmentally-friendly energy plans"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-8.png">Reneweable energy plans</div>
                                </div>
                                <?php else:?>
                                <div class="back-to-search-results">
                                <a href="/<?php echo $this->params['controller'];?>/compare/3#step3"><< Back to Search Results</a>    
                                </div>
                                	
                                <div class="plans-search-filters">  
                                	<div class="filter-scroll style2">
                                        <div class="form-item"><a href="javascript:;" class="clear-filters">Clear All</a></div>
                                		<div class="form-item">
                                            <div class="form-label active">
                                                <label>Apply Discounts [<?php echo count($available_discount_type);?>]</label>
                                            </div>
                                            <div class="form-element" <?php if (!empty($filters['discount_type'])):?>style="display:block;"<?php endif;?>>
                                                <label><input type="checkbox" name="discount_type[]" id="discount_type_all" value="all" <?php if (in_array('all', $filters['discount_type'])):?>checked="checked"<?php endif;?>>All</label>
                                                <?php if (in_array('Pay On Time', $available_discount_type)):?>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Pay On Time" <?php if (in_array('Pay On Time', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Pay On Time</label>
                                                <?php endif;?>
                                                <?php if (in_array('Guaranteed', $available_discount_type)):?>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Guaranteed" <?php if (in_array('Guaranteed', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Guaranteed</label>
                                                <?php endif;?>
                                                <?php if (in_array('Direct Debit', $available_discount_type)):?>
                                                <label><input type="checkbox" name="discount_type[]" class="discount_type" value="Direct Debit" <?php if (in_array('Direct Debit', $filters['discount_type'])):?>checked="checked"<?php endif;?>>Direct Debit</label>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="extra-title">Your Important Preferences</div>
									<div class="extra"><span class="info" title="Secure your rates for the length of the contract"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-2.png">Rate freeze plans</div>
									<div class="extra"><span class="info" title="No exit-fees, and the flexibility to change plans"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-3.png">No contract term</div>
									<div class="extra"><span class="info" title="Based on average usage, pay your bills in smaller portions"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-4.png">Bill smoothing</div>
									<div class="extra"><span class="info" title="View your account details from your computer or mobile device"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-5.png">Online account management</div>
									<div class="extra"><span class="info" title="Keep an eye on your usage from your computer or mobile device"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-6.png">Energy monitoring tools</div>
									<div class="extra"><span class="info" title="Be rewarded for choosing a certain plan"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-7.png">Membership rewards</div>
									<div class="extra"><span class="info" title="Environmentally-friendly energy plans"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-8.png">Reneweable energy plans</div>
                                </div>    
                                <?php endif;?>
                            </div>
                            </form>
                        
                            <div class="plans">
                            	<div class="plans-inner">
                            	<?php if ($total > 0):?>
                            	<?php $i = 0;?>
                                <div class="plan-page row">
                                <?php foreach ($plans as $plan):?>
                                <?php $i++;?>
                                <?php
                                $has_bonus_sumo_discount = false;
                                $has_prepay_discount = false;
                                switch ($filters['plan_type']) {
	                                case 'Elec':
	                                	$has_pay_on_time_discount = ($plan['Plan']['discount_pay_on_time_elec']) ? true : false;
	                                	$has_guaranteed_discount = ($plan['Plan']['discount_guaranteed_elec']) ? true : false;
	                                	$has_direct_debit_discount = ($plan['Plan']['discount_direct_debit_elec']) ? true : false;
	                                	$has_dual_fuel_discount = ($plan['Plan']['discount_dual_fuel_elec']) ? true : false;
	                                	$has_bonus_sumo_discount = ($plan['Plan']['discount_bonus_sumo']) ? true : false;
	                                	$has_prepay_discount = ($plan['Plan']['discount_prepay_elec']) ? true : false;
	                                	break;
	                                case 'Gas':
	                                	$has_pay_on_time_discount = ($plan['Plan']['discount_pay_on_time_gas']) ? true : false;
	                                	$has_guaranteed_discount = ($plan['Plan']['discount_guaranteed_gas']) ? true : false;
	                                	$has_direct_debit_discount = ($plan['Plan']['discount_direct_debit_gas']) ? true : false;
	                                	$has_dual_fuel_discount = ($plan['Plan']['discount_dual_fuel_gas']) ? true : false;
	                                	break;
	                                case 'Dual':
	                                	$has_pay_on_time_discount = ($plan['Plan']['discount_pay_on_time_elec'] || $plan['Plan']['discount_pay_on_time_gas']) ? true : false;
	                                	$has_guaranteed_discount = ($plan['Plan']['discount_guaranteed_elec'] || $plan['Plan']['discount_guaranteed_gas']) ? true : false;
	                                	$has_direct_debit_discount = ($plan['Plan']['discount_direct_debit_elec'] || $plan['Plan']['discount_direct_debit_gas']) ? true : false;
	                                	$has_dual_fuel_discount = ($plan['Plan']['discount_dual_fuel_elec'] || $plan['Plan']['discount_dual_fuel_gas']) ? true : false;
	                                	$has_bonus_sumo_discount = ($plan['Plan']['discount_bonus_sumo']) ? true : false;
								    	break;
                                }
                                ?>
                                    <div id="plan-<?php echo $plan['Plan']['id'];?>" class="plan">
                                        <div class="plan-visible">
                                            <div class="plan-favor">
                                                <a class="add-to-top-picks" rel="<?php echo $plan['Plan']['id'];?>" href="javascript:;"><?php if ($top_picks && in_array($plan['Plan']['id'], $top_picks)):?><?php echo $this->Html->image('top-picks.png', array('alt' => ''));?><span class="plan-favor-text">Remove from Top Picks</span><?php else:?><?php echo $this->Html->image('favor.png', array('alt' => ''));?><span class="plan-favor-text">Add to Top Picks</span><?php endif;?></a>
                                                <div id="plan_favor_info_<?php echo $plan['Plan']['id'];?>" class="plan-favor-info"></div>
                                            </div>
                                            <div class="plan-img">
                                                <?php echo $this->Html->image("compare_logo_{$plan['Plan']['retailer']}.png", array('alt' => $plan['Plan']['retailer']));?>
                                            </div>
                                            <h2><?php echo $plan['Plan']['product_name'];?></h2>
                                            <div class="plan-label"><strong>Verbatim Pitch</strong></div>
                                            <h4><?php echo $plan['Plan']['product_summary'];?></h4>
                                            <div class="plan-label">Estimated Cost</div>
                                            <div class="estimated-cost">
                                            <?php if ($step1['elec_recent_bill'] == 'Yes' || $step1['gas_recent_bill'] == 'Yes'):?>
                                            <p>The Estimated Cost Would Be:<br />
                                            <?php if ($filters['plan_type'] == 'Dual'):?>
                                             <span class="orange">
                                             <?php if ($step1['elec_recent_bill'] == 'Yes' && $step1['gas_recent_bill'] == 'Yes'):?>
                                             Elec: $<?php echo $plan['Plan']['total_inc_discount_elec'];?><br>
                                             Gas: $<?php echo $plan['Plan']['total_inc_discount_gas'];?><br>
                                             Total: $<?php echo ($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']);?>
                                             <?php else:?>
                                             <?php if ($step1['elec_recent_bill'] == 'Yes'):?>Elec: $<?php echo $plan['Plan']['total_inc_discount_elec'];?><?php endif;?> 
                                             <?php if ($step1['gas_recent_bill'] == 'Yes'):?>Gas: $<?php echo $plan['Plan']['total_inc_discount_gas'];?><?php endif;?></span><br />
                                             <?php endif;?>
                                            <?php else:?>
                                            <span class="orange">
                                            <?php if ($step1['elec_recent_bill'] == 'Yes' && $filters['plan_type'] == 'Elec'):?>Elec: $<?php echo $plan['Plan']['total_inc_discount_elec'];?><?php endif;?> 
                                            <?php if ($step1['gas_recent_bill'] == 'Yes' && $filters['plan_type'] == 'Gas'):?>Gas: $<?php echo $plan['Plan']['total_inc_discount_gas'];?><?php endif;?></span><br />
                                            <?php endif;?>
                                            </p>
                                            <p class="light">Indicative estimate from information your provided <span class="info" title="This estimated cost is including GST and including any discounts that are ticked in the filter list on the left."><img src="/img/tooltips_mark.png"></span></p>
                                            <?php endif;?>
                                            <?php if ($step1['elec_recent_bill'] == 'No'):?>
                                            <div class="estimated-cost-type"><strong>Electricity</strong></div>
                                            <a href="javascript:;" class="enter-elec-details">Enter my bill details</a> <span class="info" title="We just need your bill details to display a cost estimate"><img src="/img/tooltips_mark.png"></span><br />
                                            <br />
                                            <?php endif;?>
                                            <?php if ($step1['gas_recent_bill'] == 'No'):?>
                                            <div class="estimated-cost-type"><strong>Gas</strong></div>
                                            <a href="javascript:;" class="enter-gas-details">Enter my bill details</a> <span class="info" title="We just need your bill details to display a cost estimate"><img src="/img/tooltips_mark.png"></span><br />
                                            <br />
                                            <?php endif;?>
                                            <form name="rates_form1" id="rates_form1_<?php echo $plan['Plan']['id'];?>" onsubmit="return false;">
                                            <input type="hidden" name="plan_id" value="<?php echo $plan['Plan']['id'];?>">
                                            <input type="hidden" name="elec_rate_id" value="<?php echo (isset($plan['Plan']['elec_rate'])) ?$plan['Plan']['elec_rate']['id'] : 0;?>">
                                            <input type="hidden" name="gas_rate_id" value="<?php echo (isset($plan['Plan']['gas_rate'])) ? $plan['Plan']['gas_rate']['id'] : 0;?>">
                                            <input type="hidden" name="rate_type" value="<?php echo $filters['plan_type'];?>">
                                            
                                            <input type="hidden" name="discount_pay_on_time" value="">
                                            <input type="hidden" name="discount_guaranteed" value="">
                                            <input type="hidden" name="discount_direct_debit" value="">
                                            <input type="hidden" name="discount_dual_fuel" value="">
                                            <input type="hidden" name="discount_bonus_sumo" value="">
                                            <input type="hidden" name="discount_prepay" value="">
                                            <input type="hidden" name="include_gst" value="<?php echo $plan['Plan']['id'];?>">

                                            <div style="float:left;">
                                            <?php if ($has_bonus_sumo_discount && $plan['Plan']['retailer'] == 'Sumo Power'):?>
                                                <?php if ($filters['discount_bonus_sumo_all']):?>
                                                <span class="info" title="Bonus Pay on Time is currently applied. Do not quote customer these rates. Click to exclude Bonus Pay on Time discount"><a href="#" class="btn-bon active"></a></span>
                                                <?php else:?>
                                                <span class="info" title="Bonus Pay on Time discount is not applied. Click to apply Bonus Pay on Time discount"><a href="#" class="btn-bon"></a></span>
                                                <?php endif;?>
                                            <?php endif;?>
                                            <?php if ($has_prepay_discount && $plan['Plan']['retailer'] == 'Sumo Power'):?>
                                                <?php if ($filters['discount_prepay_all']):?>
                                                <span class="info" title="Prepay discount is currently applied. Do not quote customer these rates. Click to exclude Prepay discount"><a href="#" class="btn-pre active"></a></span>
                                                <?php else:?>
                                                <span class="info" title="Prepay discount is not applied. Click to apply Prepay discount"><a href="#" class="btn-pre"></a></span>
                                                <?php endif;?>
                                            <?php endif;?>
                                            <?php if ($has_guaranteed_discount && $plan['Plan']['retailer'] != 'Powershop'):?>
                                                <?php if ($filters['discount_guaranteed_all']):?>
                                                <span class="info" title="Guaranteed discount is currently applied. Do not quote customer these rates. Click to exclude Guaranteed discount"><a href="#" class="btn-gtd active"></a></span>
                                                <?php else:?>
                                                <span class="info" title="Guaranteed discount is not applied. Click to apply Guaranteed discount"><a href="#" class="btn-gtd"></a></span>
                                                <?php endif;?>
                                            <?php endif;?>
                                            <?php if ($has_pay_on_time_discount):?>
                                                <?php if ($filters['discount_pay_on_time_all']):?>
                                                <span class="info" title="Pay On Time discount is currently applied. Do not quote customer these rates. Click to exclude Pay On Time discount"><a href="#" class="btn-pot active"></a></span>
                                                <?php else:?>
                                                <span class="info" title="Pay On Time discount is not applied. Click to apply Pay On Time discount"><a href="#" class="btn-pot"></a></span>
                                                <?php endif;?>
                                            <?php endif;?>
                                            <?php if ($has_direct_debit_discount):?>
                                                <?php if ($filters['discount_direct_debit_all']):?>
                                                <span class="info" title="Direct Debit discount is currently applied. Do not quote customer these rates. Click to exclude Direct Debit discount"><a href="#" class="btn-dd active"></a></span>
                                                <?php else:?>
                                                <span class="info" title="Direct Debit discount is not applied. Click to apply Direct Debit discount"><a href="#" class="btn-dd"></a></span>
                                                <?php endif;?>
                                            <?php endif;?>
                                            <?php if ($has_dual_fuel_discount):?>
                                                <?php if ($filters['discount_dual_fuel_all']):?>
                                                <span class="info" title="Double up discount is currently applied. Do not quote customer these rates. Click to exclude Double up discount"><a href="#" class="btn-dud active"></a></span>
                                                <?php else:?>
                                                <span class="info" title="Double up discount is not applied. Click to apply Double up discount"><a href="#" class="btn-dud"></a></span>
                                                <?php endif;?>
                                            <?php endif;?>
                                            </div>
                                            <div class="clearfix"></div>
                                            </form>
                                            <div class="table-rate" id="table1_rate_<?php echo $plan['Plan']['id'];?>">
                                            <?php echo $this->element('rates_v6', array('step1' => $step1, 'plan' => $plan, 'discount_pay_on_time' => ($filters['discount_pay_on_time_all']) ? true : false, 'discount_guaranteed' => ($filters['discount_guaranteed_all']) ? true : false, 'discount_direct_debit' => ($filters['discount_direct_debit_all']) ? true : false, 'discount_dual_fuel' => ($filters['discount_dual_fuel_all']) ? true : false, 'discount_bonus_sumo' => ($filters['discount_bonus_sumo_all']) ? true : false, 'include_gst' => ($filters['include_gst_all']) ? true : false, 'rate_type' => $filters['plan_type'])); ?>
                                            </div>
                                            </div>
                                            <div class="plan-label">Plan Information:</div>
                                            <ul class="benefits">
                                                <li><span class="clipboard"></span><span class="icon_text">Contract Length: <?php echo $plan['Plan']['contract_length'];?></span></li>
                                                <li><span class="tick"></span><span class="icon_text">Exit Fee: <?php echo $plan['Plan']['exit_fee'];?></span></li>
                                            </ul>
                                            <div class="call">to <span class="signup" onclick="signup(<?php echo $plan['Plan']['id'];?>, <?php echo (isset($plan['Plan']['elec_rate'])) ? $plan['Plan']['elec_rate']['id'] : 0;?>, <?php echo (isset($plan['Plan']['gas_rate'])) ? $plan['Plan']['gas_rate']['id'] : 0;?>, <?php echo $plan['Plan']['ranking'];?>);">sign up</span><br />Call <?php echo $this->Html->image('tel2.png', array('alt' => ''));?> <span class="AVANSERnumber"><a href="tel:1300359779">1300 359 779</a></span></div>
                                            <div class="extra-title view-details">click to view plan details &raquo;</div>
                                            <div class="extra"><?php if ($plan['Plan']['rate_freeze'] == 'Yes'):?><span class="info" title="<?php echo $plan['Plan']['rate_freeze_details'];?>"><img src="/img/check_green.png"></span><?php endif;?></div>
                                            <div class="extra"><?php if ($plan['Plan']['no_contract_plan'] == 'Yes'):?><span class="info" title="<?php echo $plan['Plan']['no_contract_plan_details'];?>"><img src="/img/check_green.png"></span><?php endif;?></div>
                                            <div class="extra"><?php if ($plan['Plan']['bill_smoothing'] == 'Yes'):?><span class="info" title="<?php echo $plan['Plan']['bill_smoothing_details'];?>"><img src="/img/check_green.png"></span><?php endif;?></div>
                                            <div class="extra"><?php if ($plan['Plan']['online_account_management'] == 'Yes'):?><span class="info" title="<?php echo $plan['Plan']['online_account_management_details'];?>"><img src="/img/check_green.png"></span><?php endif;?></div>
                                            <div class="extra"><?php if ($plan['Plan']['energy_monitoring_tools'] == 'Yes'):?><span class="info" title="<?php echo $plan['Plan']['energy_monitoring_tools_details'];?>"><img src="/img/check_green.png"></span><?php endif;?></div>
                                            <div class="extra"><?php if ($plan['Plan']['membership_reward_programs'] == 'Yes'):?><span class="info" title="<?php echo $plan['Plan']['membership_reward_programs_details'];?>"><img src="/img/check_green.png"></span><?php endif;?></div>
                                            <div class="extra"><?php if ($plan['Plan']['renewable_energy'] == 'Yes'):?><span class="info" title="<?php echo $plan['Plan']['renewable_energy_details'];?>"><img src="/img/check_green.png"></span><?php endif;?></div>
                                        </div>
                                        <div class="plan-info">
                                            <div class="plan-info-tabs jqueryui-tabs">
                                                <ul>
                                                    <li><a href="#tabs-discount">Discounts & Features</a></li>
                                                    <li><a href="#tabs-fees">Fees</a></li>
                                                    <li><a href="#tabs-rates">Rates</a></li>
                                                </ul>
                                                <div id="tabs-discount">
                                                <p class="back-to-plans">&laquo; back to plans</p>
                                                	<h4>Discounts</h4>
                                                    <ul>
                                                    	<?php if ($plan['Plan']['discount_guaranteed_description']):?>
                                                        <li>
                                                            <h4>Discount: Guaranteed</h4>
                                                            <p><span class="percent"></span><span class="icon_text"><?php echo $plan['Plan']['discount_guaranteed_description'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['discount_pay_on_time_description']):?>
                                                        <li>
                                                            <h4>Discount: Pay on Time</h4>
                                                            <p><span class="time"></span><span class="icon_text"><?php echo $plan['Plan']['discount_pay_on_time_description'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['discount_direct_debit_description']):?>
                                                        <li>
                                                            <h4>Discount: Direct Debit</h4>
                                                            <p><span class="directdebit"></span><span class="icon_text"><?php echo $plan['Plan']['discount_direct_debit_description'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['discount_credit_description']):?>
                                                        <li>
                                                            <h4>Discount: Credit</h4>
                                                            <p><span class="directdebit"></span><span class="icon_text"><?php echo $plan['Plan']['discount_credit_description'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['discount_dual_fuel_description']):?>
                                                        <li>
                                                            <h4>Discount: Dual Fuel</h4>
                                                            <p><span class="percent"></span><span class="icon_text"><?php echo $plan['Plan']['discount_dual_fuel_description'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['special_offer']):?>
                                                        <li>
                                                            <h4>Special Offer</h4>
                                                            <p><span class="directdebit"></span><span class="icon_text"><?php echo $plan['Plan']['special_offer'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                    </ul>
                                                    <h4>Features</h4>
                                                    <ul>
                                                        <li>
                                                            <h4><?php echo $plan['Plan']['benefit1_title'];?></h4>
                                                            <p><?php echo $this->Icon->add($plan['Plan']['benefit1_title'], true, true);?><span class='icon_text'><?php echo $plan['Plan']['benefit1_description'];?></span></p>
                                                        </li>
                                                        <?php if ($plan['Plan']['benefit2_title']):?>
                                                        <li>
                                                            <h4><?php echo $plan['Plan']['benefit2_title'];?></h4>
                                                            <p><?php echo $this->Icon->add($plan['Plan']['benefit2_title'], true, true);?><span class='icon_text'><?php echo $plan['Plan']['benefit2_description'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['benefit3_title']):?>
                                                        <li>
                                                            <h4><?php echo $plan['Plan']['benefit3_title'];?></h4>
                                                            <p><?php echo $this->Icon->add($plan['Plan']['benefit3_title'], true, true);?><span class='icon_text'><?php echo $plan['Plan']['benefit3_description'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['benefit4_title']):?>
                                                        <li>
                                                            <h4><?php echo $plan['Plan']['benefit4_title'];?></h4>
                                                            <p><?php echo $this->Icon->add($plan['Plan']['benefit4_title'], true, true);?><span class='icon_text'><?php echo $plan['Plan']['benefit4_description'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['rate_freeze'] == 'Yes'):?>
                                                        <li>
                                                            <h4>Rate freeze plans</h4>
                                                            <p><span class="rate_freeze"></span><span class='icon_text'><?php echo $plan['Plan']['rate_freeze_details'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['no_contract_plan'] == 'Yes'):?>
                                                        <li>
                                                            <h4>No contract term</h4>
                                                            <p><span class="no_contract_plan"></span><span class='icon_text'><?php echo $plan['Plan']['no_contract_plan_details'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['bill_smoothing'] == 'Yes'):?>
                                                        <li>
                                                            <h4>Bill smoothing</h4>
                                                            <p><span class="bill_smoothing"></span><span class='icon_text'><?php echo $plan['Plan']['bill_smoothing_details'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['online_account_management'] == 'Yes'):?>
                                                        <li>
                                                            <h4>Online account management</h4>
                                                            <p><span class="online_account_management"></span><span class='icon_text'><?php echo $plan['Plan']['online_account_management_details'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['energy_monitoring_tools'] == 'Yes'):?>
                                                        <li>
                                                            <h4>Energy monitoring tools</h4>
                                                            <p><span class="energy_monitoring_tools"></span><span class='icon_text'><?php echo $plan['Plan']['energy_monitoring_tools_details'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['membership_reward_programs'] == 'Yes'):?>
                                                        <li>
                                                            <h4>Membership rewards</h4>
                                                            <p><span class="membership_reward_programs"></span><span class='icon_text'><?php echo $plan['Plan']['membership_reward_programs_details'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                        <?php if ($plan['Plan']['renewable_energy'] == 'Yes'):?>
                                                        <li>
                                                            <h4>Reneweable energy plans</h4>
                                                            <p><span class="renewable_energy"></span><span class='icon_text'><?php echo $plan['Plan']['renewable_energy_details'];?></span></p>
                                                        </li>
                                                        <?php endif;?>
                                                    </ul>
                                                    <?php if ($plan['Plan']['terms']):?>
                                                    <h4>Terms</h4>
                                                    <p class="terms"><?php echo $plan['Plan']['terms'];?></p>
                                                    <?php endif;?>
                                                </div>
                                                <div id="tabs-fees">
                                                	<p class="back-to-plans">&laquo; back to plans</p>
                                                    <table width="100%">
                                                        <tr>
                                                            <th width="50%" class="left">Fees &amp; Charges</th>
                                                            <th width="50%" class="right">Billing Information</th>
                                                        </tr>
                                                        <tr>
                                                            <td class="left">
                                                            <ul>
                                                            	<?php if ($plan['Plan']['exit_fee']):?>
                                                                <li>
                                                                    <h4>Exit Fee</h4>
                                                                    <p><?php echo $plan['Plan']['exit_fee'];?></p>
                                                                </li>
                                                                <?php endif;?>
                                                                <?php if ($plan['Plan']['late_payment_fee']):?>
                                                                <li>
                                                                    <h4>Late Payment Fee</h4>
                                                                    <p><?php echo $plan['Plan']['late_payment_fee'];?></p>
                                                                </li>
                                                                <?php endif;?>
                                                                <?php if ($plan['Plan']['dishonoured_payment_fee']):?>
                                                                <li>
                                                                    <h4>Dishonoured Payment Fee</h4>
                                                                    <p><?php echo $plan['Plan']['dishonoured_payment_fee'];?></p>
                                                                </li>
                                                                <?php endif;?>
                                                                <?php if ($plan['Plan']['card_payment_fee']):?>
                                                                <li>
                                                                    <h4>Card Payment Fee</h4>
                                                                    <p><?php echo $plan['Plan']['card_payment_fee'];?></p>
                                                                </li>
                                                                <?php endif;?>
                                                                <?php if ($plan['Plan']['other_fees']):?>
                                                                <li>
                                                                    <h4>Other Fees</h4>
                                                                    <p class="fees"><?php echo $plan['Plan']['other_fees'];?></p>
                                                                </li>
                                                                <?php endif;?>
                                                            </ul>
                                                            </td>
                                                            <td class="right">
                                                            <ul>
                                                                <li>
                                                                    <h4>Billing Period</h4>
                                                                    <p><?php echo $plan['Plan']['billing_period'];?></p>
                                                                </li>
																<?php
																$payment_options = array();
																foreach ($payment_options_arr as $key => $value) {
																    if ($plan['Plan'][$key] == 'Yes') {
																        $payment_options[] = $value;
																    }
																}
																?>
                                                                <li>
                                                                    <h4>Payment Option</h4>
                                                                    <p><?php echo ($payment_options) ? implode(', ', $payment_options) : '';?></p>
                                                                </li>
                                                            </ul>
                                                            </td>
                                                        </tr>
                                                    </table>            
                                                </div>
                                                <div id="tabs-rates">
                                                	<p class="back-to-plans">&laquo; back to plans</p>
                                                    <h4>Electricity & Gas Rate</h4>
                                                    <form name="rates_form2" id="rates_form2_<?php echo $plan['Plan']['id'];?>" onsubmit="return false;">
                                                    <input type="hidden" name="plan_id" value="<?php echo $plan['Plan']['id'];?>">
                                                    <input type="hidden" name="elec_rate_id" value="<?php echo (isset($plan['Plan']['elec_rate'])) ?$plan['Plan']['elec_rate']['id'] : 0;?>">
                                                    <input type="hidden" name="gas_rate_id" value="<?php echo (isset($plan['Plan']['gas_rate'])) ? $plan['Plan']['gas_rate']['id'] : 0;?>">
                                                    <input type="hidden" name="rate_type" value="<?php echo $filters['plan_type'];?>">
                                                    
                                                    <div class="form-group">
                                                    	<label>Discounted Rates include:</label><br />
                                                    	<?php if ($has_pay_on_time_discount):?>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" name="discount_pay_on_time" value="<?php echo $plan['Plan']['id'];?>" <?php if (in_array('Pay On Time', $filters['discount_type'])):?>checked="checked"<?php endif;?>> Pay on time
                                                            <?php if ($filters['plan_type'] == 'Dual'):?>
                                                            	<?php if ($plan['Plan']['discount_pay_on_time_elec'] > 0 || $plan['Plan']['discount_pay_on_time_gas'] > 0):?>(<?php if ($plan['Plan']['discount_pay_on_time_elec'] > 0):?>E: <?php echo $plan['Plan']['discount_pay_on_time_elec'];?>%<?php endif;?> <?php if ($plan['Plan']['discount_pay_on_time_gas'] > 0):?>G: <?php echo $plan['Plan']['discount_pay_on_time_gas'];?>%<?php endif;?>)<?php endif;?>
                                                            <?php elseif ($filters['plan_type'] == 'Elec'):?>
                                                            <?php if ($plan['Plan']['discount_pay_on_time_elec'] > 0):?>(<?php echo $plan['Plan']['discount_pay_on_time_elec'];?>%)<?php endif;?>
                                                            <?php elseif ($filters['plan_type'] == 'Gas'):?>
                                                            <?php if ($plan['Plan']['discount_pay_on_time_gas'] > 0):?>(<?php echo $plan['Plan']['discount_pay_on_time_gas'];?>%)<?php endif;?>
                                                            <?php endif;?>
                                                        </label>
                                                        <?php endif;?>
                                                    	<?php if ($has_guaranteed_discount && $plan['Plan']['retailer'] != 'Powershop'):?>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" name="discount_guaranteed" value="<?php echo $plan['Plan']['id'];?>" <?php if (in_array('Guaranteed', $filters['discount_type'])):?>checked="checked"<?php endif;?>> Guaranteed
                                                            <?php if ($filters['plan_type'] == 'Dual'):?>
                                                            	<?php if ($plan['Plan']['discount_guaranteed_elec'] > 0 || $plan['Plan']['discount_guaranteed_gas'] > 0):?>(<?php if ($plan['Plan']['discount_guaranteed_elec'] > 0 && $plan['Plan']['retailer'] != 'Powershop'):?>E: <?php echo $plan['Plan']['discount_guaranteed_elec'];?>%<?php endif;?> <?php if ($plan['Plan']['discount_guaranteed_gas'] > 0):?>G: <?php echo $plan['Plan']['discount_guaranteed_gas'];?>%<?php endif;?>)<?php endif;?>
                                                            <?php elseif ($filters['plan_type'] == 'Elec'):?>
                                                            <?php if ($plan['Plan']['discount_guaranteed_elec'] > 0 && $plan['Plan']['retailer'] != 'Powershop'):?>(<?php echo $plan['Plan']['discount_guaranteed_elec'];?>%)<?php endif;?>
                                                            <?php elseif ($filters['plan_type'] == 'Gas'):?>
                                                            <?php if ($plan['Plan']['discount_guaranteed_gas'] > 0):?>(<?php echo $plan['Plan']['discount_guaranteed_gas'];?>%)<?php endif;?>
                                                            <?php endif;?>
                                                        </label>
                                                        <?php endif;?>
                                                        <?php if ($has_direct_debit_discount):?>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" name="discount_direct_debit" value="<?php echo $plan['Plan']['id'];?>" <?php if (in_array('Direct Debit', $filters['discount_type'])):?>checked="checked"<?php endif;?>> Direct debit
                                                            <?php if ($filters['plan_type'] == 'Dual'):?>
                                                            	<?php if ($plan['Plan']['discount_direct_debit_elec'] > 0 || $plan['Plan']['discount_direct_debit_gas'] > 0):?>(<?php if ($plan['Plan']['discount_direct_debit_elec'] > 0):?>E: <?php echo $plan['Plan']['discount_direct_debit_elec'];?>%<?php endif;?> <?php if ($plan['Plan']['discount_direct_debit_gas'] > 0):?>G: <?php echo $plan['Plan']['discount_direct_debit_gas'];?>%<?php endif;?>)<?php endif;?>
                                                            <?php elseif ($filters['plan_type'] == 'Elec'):?>
                                                            <?php if ($plan['Plan']['discount_direct_debit_elec'] > 0):?>(<?php echo $plan['Plan']['discount_direct_debit_elec'];?>%)<?php endif;?>
                                                            <?php elseif ($filters['plan_type'] == 'Gas'):?>
                                                            <?php if ($plan['Plan']['discount_direct_debit_gas'] > 0):?>(<?php echo $plan['Plan']['discount_direct_debit_gas'];?>%)<?php endif;?>
                                                            <?php endif;?>
                                                        </label>
                                                        <?php endif;?>
                                                        <br />
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" name="include_gst" value="<?php echo $plan['Plan']['id'];?>" checked="checked"> Include GST
                                                        </label>
                                                        <br />
														<em>
														<?php if ($plan['Plan']['discount_applies'] == 'Usage'):?>
															<small>Discounts apply to usage rates only</small>
														<?php elseif ($plan['Plan']['discount_applies'] == 'Usage + STP + GST'):?>
															<small>Discounts apply to usage rates, supply charge & GST</small>
														<?php endif;?>
														</em>
                                                    </div>
                                                    </form>
                                                    <div class="table-rate" id="table2_rate_<?php echo $plan['Plan']['id'];?>">
                                                    <?php echo $this->element('rate_details_v6', array('plan' => $plan, 'discount_pay_on_time' => (in_array('Pay On Time', $filters['discount_type'])) ? true : false, 'discount_guaranteed' => (in_array('Guaranteed', $filters['discount_type'])) ? true : false, 'discount_direct_debit' => (in_array('Direct debit', $filters['discount_type'])) ? true : false, 'include_gst' => true, 'rate_type' => $filters['plan_type'])); ?>
                                                    </div>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                <?php if ($i % 3 == 0 && $i < $total):?>
                                </div>
                                <div class="plan-page row">
                                <?php endif;?>
                                
                                <?php endforeach;?>
								</div>
                                <?php endif;?>
                                </div>
                            </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                        	<a href="/<?php echo $this->params['controller'];?>/compare/2" class="btn-grey pull-left" style="margin-left:-15px;">Back</a>
                    		<div class="plan-pagination-wrap">
                            	<span class="total"><?php echo $total;?> results</span>
                            	<?php if ($total > 0):?>
                                <div id="plan-pagination">
                                	<a href="#" class="prev"></a>
                                	<ul></ul>
                                    <a href="#" class="next"></a>
                                </div>
                                <?php endif;?>
                             </div>
                        </div>
                    </div>        
				</div>
