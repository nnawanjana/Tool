
	<div class="modal fade" id="solar_rebate_scheme_modal" tabindex="-1" role="dialog" aria-labelledby="solar_rebate_scheme_modalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-body clearfix">
	      		<form id="step1_solar_rebate_scheme_form" class="step" onsubmit="return false;">
	      			<div class="form-horizontal">
	                    <div class="form-section col-sm-12 clearfix ">
                        <div class="form-group">
	                        <h2>IMPORTANT</h2>
                        	<p style="margin:30px;">This tariff is either SFiT (retailer feed in) or SFiT (1:1). If the customer is currently on 1:1 and they change to another retailer, they will lose the 1:1 feed-in rate and drop to retailer minimum. Customer solar feed in will drop in December 2016 please ensure you advise customer. Please do not predict future feed in.</p>
                        </div>
	                    </div>
	                    <div class="col-sm-12 clearfix">
	                    <div class="form-group" style="position:relative; clear:both;">
	                        <a href="javascript:;" class="btn-orange pull-right continue">Continue</a>
	                    </div>
	                    </div>
	      			</div>
	      		</form>
			</div>
	    </div>
	  </div>
	</div>
	<div class="modal fade" id="solar_rebate_scheme_modal2" tabindex="-1" role="dialog" aria-labelledby="solar_rebate_scheme_modal2Label" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-body clearfix">
	      		<form id="step1_solar_rebate_scheme_form2" class="step" onsubmit="return false;">
	      			<div class="form-horizontal">
	                    <div class="form-section col-sm-12 clearfix ">
                        <div class="form-group">
	                        <h2>REMINDER</h2>
                        	<p style="margin:30px;">Customer solar feed in will drop in December 2016 please ensure you advise customer. Please do not predict future feed in.</p>
                        </div>
	                    </div>
	                    <div class="col-sm-12 clearfix">
	                    <div class="form-group" style="position:relative; clear:both;">
	                        <a href="javascript:;" class="btn-orange pull-right continue">Continue</a>
	                    </div>
	                    </div>
	      			</div>
	      		</form>
			</div>
	    </div>
	  </div>
	</div>
<form id="step1_form" onsubmit="return false;" class="v4_form">
	<div class="row">
		<div class="col-md-3">
			<ul class="v4_nav">
            <?php /*?><li <?php if ($step == 1):?>class="active"<?php endif;?>><?php if ($step1):?><a href="/<?php echo $this->params['controller'];?>/compare/1"><?php endif;?><?php echo $this->Html->image('v4/step1.png', array('alt' => ''));?>About You<?php if ($step1):?></a><?php endif;?></li><?php */?>
			<li class="active"><a href="/<?php echo $this->params['controller'];?>/compare/1"><?php echo $this->Html->image('v4/step1.png', array('alt' => ''));?>About You</a></li>	
            <li <?php if ($step == 2):?>class="active"<?php endif;?>><?php if ($step2):?><a href="/<?php echo $this->params['controller'];?>/compare/2"><?php endif;?><?php echo $this->Html->image('v4/step2.png', array('alt' => ''));?>Product Options<?php if ($step2):?></a><?php endif;?></li> 
            <li <?php if ($step == 3):?>class="active"<?php endif;?>><?php if ($step1 && $step2):?><a href="/<?php echo $this->params['controller'];?>/compare/3"><?php endif;?><?php echo $this->Html->image('v4/step3.png', array('alt' => ''));?>See Your Results<?php if ($step1 && $step2):?></a><?php endif;?></li>
            <li <?php if ($step == 4):?>class="active"<?php endif;?>><a href="/<?php echo $this->params['controller'];?>/form1"><?php echo $this->Html->image('v4/step4.png', array('alt' => ''));?>Energy Solutions</a></li>
            </ul>
		</div>
		<div class="col-md-9">	
		    <input type="hidden" value="1" name="current_step" id="current_step">
			<input type="hidden" value="<?php echo (!empty($step1)) ? $step1['solar_rebate_scheme'] : '';?>" name="solar_rebate_scheme" id="solar_rebate_scheme">
			<input type="hidden" value="<?php echo ($outbound) ? 1 : 0;?>" name="outbound" id="outbound">
			<input type="hidden" value="<?php echo ($inbound) ? 1 : 0;?>" name="inbound" id="inbound">
				<div style="display: none;" id="processing">
					<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
				</div>
				<div id="step1" class="step clearfix">
					<a href="/v4/" class="step_close">X</a>
					<div class="step-header d-flex justify-content-between align-items-center">
                    <h2>About You</h2>
                    <div class="start-new-comparison">
                    	<a href="/v4/">new comparison</a>
                    </div>
					</div>
					
                    <div class="form-horizontal">

                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Lead ID (if applicable)"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Lead ID (if applicable)</label>
                        <div class="v4_field">
                            <input class="form-control" type="text" name="sid" value="<?php echo $sid;?>" id="sid" placeholder="Lead ID">
                            <input type="hidden" name="campaign_id" value="" id="campaign_id">
                            <input type="hidden" name="campaign_name" value="" id="campaign_name">
                            <input type="hidden" name="first_campaign" value="" id="first_campaign">
                            <input type="hidden" name="campaign_source" value="" id="campaign_source">
                            <input type="hidden" name="centre_name" value="" id="centre_name">
                            <input type="hidden" name="lead_origin" value="" id="lead_origin">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="User"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>User</label>
                        <div class="v4_field">
                            <input class="form-control" type="text" name="agent_name" value="<?php echo $agent_name;?>" id="agent_name" placeholder="User" disabled="disabled">
                            <input type="hidden" name="agent_id" value="<?php echo $agent_id;?>" id="agent_id">
                            <input type="hidden" name="referring_agent" value="" id="referring_agent">
                        </div>
                    </div>
					<div class="row"><div class="col-sm-6">	
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Customer's Name"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Customer's Name</label>
                        <div class="v4_field">
                            <input class="form-control" type="text" name="first_name" value="" id="first_name" placeholder="First Name">
                        </div>
					</div>
					</div><div class="col-sm-6">
					<div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Customer's Name"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Surname</label>
                        <div class="v4_field">
                            <input class="form-control" type="text" name="surname" value="" id="surname" placeholder="Surname">
                        </div>
                    </div>
					</div></div>	
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Customer's Mobile Number"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Mobile Number</label>
                        <div class="v4_field">
                            <input class="form-control" type="tel" name="mobile" value="" id="mobile" placeholder="Mobile Number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Customer's Home Phone"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Home Phone</label>
                        <div class="v4_field">
                            <input class="form-control" type="tel" name="home_phone" value="" id="home_phone" placeholder="Home Phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Customer's Work Number"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Work Number</label>
                        <div class="v4_field">
                            <input class="form-control" type="tel" name="work_number" value="" id="work_number" placeholder="Work Number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Customer's Email"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Email</label>
                        <div class="v4_field">
                            <input class="form-control" type="email" name="email" value="" id="email" placeholder="Email">
                        </div>
                    </div>
                    </div>

                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group" id="postcode_field">
                    	<label class="control-label v4_label v2"><span class="info" title="The deals on our panel are dependant on the area you live in, so please select the postcode and suburb your property is in"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Postcode and suburb</label>
						<div class="row"><div class="col-sm-6">
                        	<div class="v4_field">
                            	<input class="form-control" type="number" min="0"  name="postcode" value="<?php echo $postcode;?>" id="postcode" placeholder="E.g. 3000">
                    		</div>
							</div><div class="col-sm-6">
                         	<div class="v4_field">
								<select class="form-control" name="suburb" id="suburb">
									<option value="">Please Select</option>
								</select>
                            	<input type="hidden" name="state" value="<?php echo $state;?>" id="state">
                        	</div>
						</div></div>	
					</div>
                    </div>
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label v4_label v2"><span class="info" title="We'll need to know what kind of comparison you're after"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>What are you looking to compare?</label>
                        <div class="row"><div class="col-sm-12">
                            <input type="hidden" name="plan_type" value="<?php echo (!empty($step1)) ? $step1['plan_type'] : '';?>" id="plan_type">
                            <div id="Dual" class="plan-type plan-type-eg"></div>
                            <div id="Elec" class="plan-type plan-type-e"></div>
                            <div id="Gas"  class="plan-type plan-type-g"></div>
                        </div></div>
                        <div class="clearfix"></div>
                        <div class="row"><div class="col-sm-12 text-center">
                        	<p id="elec-disclaimer" style="display:none;">Disclaimer: would you be willing to pay by direct debit or an online portal to possibly get the best deal in the area?</p>
							<p id="dual-disclaimer" style="display:none;">Disclaimer: would you be willing to split your electricity and gas retailers if it works out to be a cheaper option?</p>
                        </div></div>
                    </div>
                    </div>
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label v4_label v2"><span class="info" title="What kind of property is this?"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Select your comparison type</label>
                        <div class="col-sm-12">
                            <input type="hidden" name="customer_type" value="<?php echo (!empty($step1)) ? $step1['customer_type'] : 'RES';?>" id="customer_type">
                            <input type="hidden" name="is_soho" value="<?php echo (!empty($step1) && $step1['is_soho']) ? 1 : 0;?>" id="is_soho">
                            <input type="hidden" name="looking_for" value="<?php echo (!empty($step1)) ? $step1['looking_for'] : 'Compare Plans';?>" id="looking_for">
                            <div id="RES" class="customer-type customer-type-r"></div>
                            <div id="SME" class="customer-type customer-type-b"></div>
                            <div id="SOHO" class="customer-type customer-type-s"></div>
						</div>
						<p>&nbsp;</p>
						<div class="col-sm-12">
                            <div id="MoveIn" class="looking-for movein"></div>
                            <div id="Transfer" class="looking-for transfer"></div>
                        </div>
                    </div>
                    <div class="form-group" id="move_in_date_field" style="display:none;">
                        <label class="control-label v4_label"><span class="info" title="Is this for an existing property, or are you needing a connection for a new one?"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Move in date</label>
                        <div class="v4_field">
                            <input type="text" class="form-control" placeholder="" name="move_in_date" id="move_in_date" value="<?php echo (!empty($step1)) ? $step1['move_in_date'] : '';?>">
                            <div class="checkbox">
								<label><input type="checkbox" name="move_in_date_not_sure" id="move_in_date_not_sure" value="1" <?php if (!empty($step1) && $step1['move_in_date_not_sure'] == 1):?>checked="checked"<?php endif;?>>Not Sure</label>
							</div>
                        </div>

                    </div>
                    </div>
						
					<div class="form-section col-sm-12 clearfix">
                        <div id="elec_transfer" style="display:none;">
    						<div class="form-group">
								<div class="row">
                        		<div class="col-sm-6">
                                <label class="control-label v4_label v2"><span class="info"><img src="/img/v4/question.png" alt=""></span>Do you know your current electricity discount?</label></div>
                                <div class="col-sm-6">
                                	<input type="hidden" name="elec_current_discount_choice" value="<?php echo ((!empty($step1) && $step1['elec_current_discount_choice'] == 'Yes' && ($step1['plan_type'] == 'Dual' || $step1['plan_type'] == 'Elec')) || empty($step1)) ? 'Yes' : 'No';?>" id="elec_current_discount_choice">
                                    <div class="radio-simulate elec-current-discount-choices">
                                    	<div class="bar"></div>
                                        <div id="Yes" class="choice">Yes</div>
                                        <div id="No" class="choice">No</div>
                                    </div>
                                </div>
								</div>	
                            </div>
                            <div class="form-group elec-current-discount-yes" style="display:none;">
                                <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Discount %</label>
                                <div class="v4_field">
                                    <input type="text" class="form-control" placeholder="%" name="elec_current_discount" id="elec_current_discount" value="<?php echo (!empty($step1)) ? $step1['elec_current_discount'] : '';?>">
                                </div>
                            </div>
                            <div class="form-group elec-current-discount-yes" style="display:none;">
                                <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Discount Type</label>
                                <div class="v4_field">
                                    <select class="form-control" name="elec_current_discount_type" id="elec_current_discount_type">
                                    	<option value="">Please Select</option>
                                    	<option value="Pay On Time" <?php echo (!empty($step1) && $step1['elec_current_discount_type'] == 'Pay On Time') ? 'selected="selected"' : '';?>>Pay On Time</option>
                                    	<option value="Guaranteed" <?php echo (!empty($step1) && $step1['elec_current_discount_type'] == 'Guaranteed') ? 'selected="selected"' : '';?>>Guaranteed</option>
                                    	<option value="Direct Debit" <?php echo (!empty($step1) && $step1['elec_current_discount_type'] == 'Direct Debit') ? 'selected="selected"' : '';?>>Direct Debit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group elec-current-discount-yes" style="display:none;">
                                <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Discount Applies</label>
                                <div class="v4_field">
                                    <select class="form-control" name="elec_current_discount_applies" id="elec_current_discount_applies">
                                    	<option value="">Please Select</option>
                                    	<option value="Usage" <?php echo (!empty($step1) && $step1['elec_current_discount_applies'] == 'Usage') ? 'selected="selected"' : '';?>>Usage</option>
                                    	<option value="Usage & Supply" <?php echo (!empty($step1) && $step1['elec_current_discount_applies'] == 'Usage & Supply') ? 'selected="selected"' : '';?>>Usage & Supply</option>
                                    	<option value="Supply" <?php echo (!empty($step1) && $step1['elec_current_discount_applies'] == 'Supply') ? 'selected="selected"' : '';?>>Supply</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="gas_transfer" style="display:none;">
    						<div class="form-group">
                                <label class="control-label v4_label v2"><span class="info"><img src="/img/v4/question.png" alt=""></span>Do you know your current gas discount?</label>
                                <div class="v4_field">
                                	<input type="hidden" name="gas_current_discount_choice" value="<?php echo ((!empty($step1) && $step1['gas_current_discount_choice'] == 'Yes' && ($step1['plan_type'] == 'Dual' || $step1['plan_type'] == 'Gas')) || empty($step1)) ? 'Yes' : 'No';?>" id="gas_current_discount_choice">
                                    <div class="radio-simulate gas-current-discount-choices">
                                    	<div class="bar"></div>
                                        <div id="Yes" class="choice">Yes</div>
                                        <div id="No" class="choice">No</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group gas-current-discount-yes" style="display:none;">
                                <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Discount %</label>
                                <div class="v4_field">
                                    <input type="text" class="form-control" placeholder="%" name="gas_current_discount" id="gas_current_discount" value="<?php echo (!empty($step1)) ? $step1['gas_current_discount'] : '';?>">
                                </div>
                            </div>
                            <div class="form-group gas-current-discount-yes" style="display:none;">
                                <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Discount Type</label>
                                <div class="v4_field">
                                    <select class="form-control" name="gas_current_discount_type" id="gas_current_discount_type">
                                    	<option value="">Please Select</option>
                                    	<option value="Pay On Time" <?php echo (!empty($step1) && $step1['gas_current_discount_type'] == 'Pay On Time') ? 'selected="selected"' : '';?>>Pay On Time</option>
                                    	<option value="Guaranteed" <?php echo (!empty($step1) && $step1['gas_current_discount_type'] == 'Guaranteed') ? 'selected="selected"' : '';?>>Guaranteed</option>
                                    	<option value="Direct Debit" <?php echo (!empty($step1) && $step1['gas_current_discount_type'] == 'Direct Debit') ? 'selected="selected"' : '';?>>Direct Debit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group gas-current-discount-yes" style="display:none;">
                                <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Discount Applies</label>
                                <div class="v4_field">
                                    <select class="form-control" name="gas_current_discount_applies" id="gas_current_discount_applies">
                                    	<option value="">Please Select</option>
                                    	<option value="Usage" <?php echo (!empty($step1) && $step1['gas_current_discount_applies'] == 'Usage') ? 'selected="selected"' : '';?>>Usage</option>
                                    	<option value="Usage & Supply" <?php echo (!empty($step1) && $step1['gas_current_discount_applies'] == 'Usage & Supply') ? 'selected="selected"' : '';?>>Usage & Supply</option>
                                    	<option value="Supply" <?php echo (!empty($step1) && $step1['gas_current_discount_applies'] == 'Supply') ? 'selected="selected"' : '';?>>Supply</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>	
						
						
						
                    <div class="form-section col-sm-12 clearfix" id="recent_bill_field" style="display:none;">
                    <div class="" id="elec_recent_bill_field" style="display:none;">
						<div class="row">
                        <div class="col-sm-6"><label class="control-label v4_label v2"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Do you have a recent electricity bill in front of you?</label></div>
                        <div class="col-sm-6">
                        	<input type="hidden" name="elec_recent_bill" value="<?php echo (!empty($step1) && $step1['elec_recent_bill'] == 'Yes' && ($step1['plan_type'] == 'Dual' || $step1['plan_type'] == 'Elec')) ? 'Yes' : 'No';?>" id="elec_recent_bill">
                            <div class="radio-simulate elec-recent-bill-choices">
                            	<div class="bar"></div>
                                <div id="Yes" class="choice">Yes</div>
                                <div id="No" class="choice">No</div>
                            </div>
                        </div>
						</div>	
                    </div>
                    <div class="form-group" id="gas_recent_bill_field" style="display:none;">
                        <div class="row">
                        <div class="col-sm-6"><label class="control-label v4_label v2"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Do you have a recent gas bill in front of you?</label></div>
                        <div class="col-sm-6">
                        	<input type="hidden" name="gas_recent_bill" value="<?php echo (!empty($step1) && $step1['gas_recent_bill'] == 'Yes' && ($step1['plan_type'] == 'Dual' || $step1['plan_type'] == 'Gas')) ? 'Yes' : 'No';?>" id="gas_recent_bill">
                            <div class="radio-simulate gas-recent-bill-choices">
                            	<div class="bar"></div>
                                <div id="Yes" class="choice">Yes</div>
                                <div id="No" class="choice">No</div>
                            </div>
                        </div>
						</div>	
                    </div>
                    </div>

                    

                    <div class="form-section col-sm-12 clearfix e-y e-n hidden-field">
                    	<h2>Electricity Details</h2>
                    	<div class="e-y hidden-field">
                        <div class="form-group">
                            <label class="control-label v4_label"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Who is your current electricity supplier?</label>
                            <div class="v4_field">
                                <select class="form-control" name="elec_supplier" id="elec_supplier">
                                    <option value="">Please Select</option>
                                    <option value="1st Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == '1st Energy') ? 'selected="selected"' : ''; ?>>1st Energy</option>
                                    <option value="ActewAGL" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
									<option value="AGL" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
									<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Alinta Energy') ? 'selected="selected"' : '';?>>Alinta Energy</option>
									<option value="Amaysim" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Amaysim') ? 'selected="selected"' : ''; ?>>Amaysim</option>
									<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
									<option value="Click Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Click Energy') ? 'selected="selected"' : '';?>>Click Energy</option>
									<option value="CovaU" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'CovaU') ? 'selected="selected"' : ''; ?>>CovaU</option>
									<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
									<option value="Diamond Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Diamond Energy') ? 'selected="selected"' : '';?>>Diamond Energy</option>
									<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
									<option value="Energy Australia" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Energy Australia') ? 'selected="selected"' : '';?>>Energy Australia</option>
									<option value="Ergon Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Ergon Energy') ? 'selected="selected"' : '';?>>Ergon Energy</option>
									<option value="ERM" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'ERM') ? 'selected="selected"' : '';?>>ERM</option>
									<option value="Globird" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Globird') ? 'selected="selected"' : ''; ?>>Globird</option>
									<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
									<option value="Mojo Power" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Mojo Power') ? 'selected="selected"' : ''; ?>>Mojo Power</option>
									<option value="Momentum" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Momentum') ? 'selected="selected"' : '';?>>Momentum</option>
									<option value="Neighbourhood Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Neighbourhood Energy') ? 'selected="selected"' : '';?>>Neighbourhood Energy</option>
									<option value="Origin Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Origin Energy') ? 'selected="selected"' : '';?>>Origin Energy</option>
									<option value="Powerdirect" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Powerdirect') ? 'selected="selected"' : '';?>>Powerdirect</option>
									<option value="Powershop" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Powershop') ? 'selected="selected"' : '';?>>Powershop</option>
									<option value="QEnergy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'QEnergy') ? 'selected="selected"' : '';?>>QEnergy</option>
									<option value="Red Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Red Energy') ? 'selected="selected"' : '';?>>Red Energy</option>
									<option value="Sanctuary Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Sanctuary Energy') ? 'selected="selected"' : '';?>>Sanctuary Energy</option>
									<option value="Simply Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Simply Energy') ? 'selected="selected"' : '';?>>Simply Energy</option>
									<option value="Sumo Power" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Sumo Power') ? 'selected="selected"' : '';?>>Sumo Power</option>
									<option value="Tango Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Tango Energy') ? 'selected="selected"' : ''; ?>>Tango Energy</option>
									<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label v4_label"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>How many days are in the billing period?</label>
                            <div class="v4_field">
                                <input type="text" class="form-control" placeholder="" name="elec_billing_days" id="elec_billing_days" value="<?php echo (!empty($step1)) ? $step1['elec_billing_days'] : '';?>">
                            </div>
                            <span class="form-control-feedback">days</span>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label v4_label v2"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Spend</label>
                            <div class="v4_field">
                            	<div class="has-prefix">
                            	<div class="prefix">$</div>
                                <input type="text" class="form-control" placeholder="" name="elec_spend" id="elec_spend" value="<?php echo (!empty($step1)) ? $step1['elec_spend'] : '';?>">
                            	</div>
                            </div>
                    	</div>
                    	</div>
                    	<div class="e-n hidden-field">
						<div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="If you're unsure, click Unsure/Other"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Who is your current electricity supplier?</label>
                        <div class="v4_field">
                            <select class="form-control" name="elec_supplier2" id="elec_supplier2">
                            	<option value="">Please Select</option>
                            	<option value="1st Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == '1st Energy') ? 'selected="selected"' : ''; ?>>1st Energy</option>
                            	<option value="ActewAGL" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
								<option value="AGL" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
								<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Alinta Energy') ? 'selected="selected"' : '';?>>Alinta Energy</option>
								<option value="Amaysim" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Amaysim') ? 'selected="selected"' : ''; ?>>Amaysim</option>
								<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
								<option value="Click Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Click Energy') ? 'selected="selected"' : '';?>>Click Energy</option>
								<option value="CovaU" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'CovaU') ? 'selected="selected"' : ''; ?>>CovaU</option>
								<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
								<option value="Diamond Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Diamond Energy') ? 'selected="selected"' : '';?>>Diamond Energy</option>
								<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
								<option value="Ergon Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Ergon Energy') ? 'selected="selected"' : '';?>>Ergon Energy</option>
								<option value="ERM" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'ERM') ? 'selected="selected"' : '';?>>ERM</option>
								<option value="Globird" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Globird') ? 'selected="selected"' : ''; ?>>Globird</option>
								<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
								<option value="Mojo Power" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Mojo Power') ? 'selected="selected"' : ''; ?>>Mojo Power</option>
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
								<option value="Tango Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Tango Energy') ? 'selected="selected"' : ''; ?>>Tango Energy</option>
								<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label v4_label v2"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>How much is an average electricity bill for you household?</label>
                        <input type="hidden" name="elec_usage_level" value="<?php echo (!empty($step1)) ? $step1['elec_usage_level'] : '';?>" id="elec_usage_level">
                        <input type="hidden" name="elec_meter_type2" value="<?php echo (!empty($step1)) ? $step1['elec_meter_type2'] : '';?>" id="elec_meter_type2">
                        <div class="v4_field">
                        	<div id="elec_usage_level_buttons"></div>
                        </div>
                    </div>
                    </div>
                    <div class="form-group">
                            <label class="control-label v4_label"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>NMI</label>
                            <div class="v4_field">
                                <input type="text" class="form-control" placeholder="" name="nmi" id="nmi" value="<?php echo (!empty($step1)) ? $step1['nmi'] : '';?>" maxlength="11"> <!--62032659066-->
                                <div id="nmi_distributor"></div>
                            </div>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label v4_label v2"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Tariff</label>
                            <input type="hidden" name="tariff_parent" id="tariff_parent" value="<?php echo (!empty($step1)) ? $step1['tariff_parent'] : '';?>">
                            <div class="v4_field">
                                <select class="form-control" name="tariff1" id="tariff1">
                            		<option value="">Tariff</option>
								</select>
								<span class="plus add-tariff2">+</span>
                            </div>
                            <div class="v4_field" id="tariff2_field" style="display:none;">
	                            <select class="form-control" name="tariff2" id="tariff2">
                            		<option value="">Tariff</option>
								</select>
								<span class="plus add-tariff3">+</span>&nbsp;&nbsp;<span class="delete-tariff2">-</span>
                            </div>
                            <div class="v4_field" id="tariff3_field" style="display:none;">
	                            <select class="form-control" name="tariff3" id="tariff3">
                            		<option value="">Tariff</option>
								</select>
								<span class="plus add-tariff4">+</span>&nbsp;&nbsp;<span class="delete-tariff3">-</span>
                            </div>
                            <div class="v4_field" id="tariff4_field" style="display:none;">
	                            <select class="form-control" name="tariff4" id="tariff4">
                            		<option value="">Tariff</option>
								</select>
								<span class="delete-tariff4">-</span>
                            </div>
                    	</div>
                        <div id="solar_fields" style="display:none;">
                            <div class="form-group">
                                <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Solar Generated</label>
                                <div class="v4_field">
                                    <input class="form-control" type="text" name="solar_generated" value="<?php echo (!empty($step1)) ? $step1['solar_generated'] : '';?>" id="solar_generated">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label v4_label"><span class="info" title="Is your inverter capacity less than 10kW?"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Is your inverter capacity less than 10kW?</label>
                                <div class="v4_field">
                                    <select class="form-control" name="inverter_capacity" id="inverter_capacity">
                                        <option value="">Please Select</option>
                                        <option value="Yes" <?php echo (!empty($step1) && $step1['inverter_capacity'] == 'Yes') ? 'selected="selected"' : '';?>>Yes</option>
                                        <option value="No" <?php echo (!empty($step1) && $step1['inverter_capacity'] == 'No') ? 'selected="selected"' : '';?>>No</option>
                                        <option value="Unsure" <?php echo (!empty($step1) && $step1['inverter_capacity'] == 'Unsure') ? 'selected="selected"' : '';?>>Unsure</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="elec_meter_type_fields">
                            <label class="control-label v4_label">Please enter your usage</label>
                            <div class="v4_field">
                                <div class="radio" id="singlerate_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Single Rate</strong><br><span class="des">One rate at all times</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_peak" id="singlerate_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_peak'])) ? $step1['singlerate_peak'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="singlerate_cl1_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cl1_peak" id="singlerate_cl1_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1_peak'])) ? $step1['singlerate_cl1_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="singlerate_cl1" id="singlerate_cl1" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1'])) ? $step1['singlerate_cl1'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="singlerate_cl2_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 2</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cl2_peak" id="singlerate_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl2_peak'])) ? $step1['singlerate_cl2_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="singlerate_cl2" id="singlerate_cl2" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl2'])) ? $step1['singlerate_cl2'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="singlerate_cl1_cl2_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1 + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1 + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1 and Controlled Load 2</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cl1_cl2_peak" id="singlerate_cl1_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1_cl2_peak'])) ? $step1['singlerate_cl1_cl2_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="singlerate_2_cl1" id="singlerate_2_cl1" value="<?php echo (!empty($step1) && isset($step1['singlerate_2_cl1'])) ? $step1['singlerate_2_cl1'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="singlerate_2_cl2" id="singlerate_2_cl2" value="<?php echo (!empty($step1) && isset($step1['singlerate_2_cl2'])) ? $step1['singlerate_2_cl2'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="singlerate_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cs_peak" id="singlerate_cs_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cs_peak'])) ? $step1['singlerate_cs_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="singlerate_cs" id="singlerate_cs" value="<?php echo (!empty($step1) && isset($step1['singlerate_cs'])) ? $step1['singlerate_cs'] : '';?>"></div>
										<div class="col-sm-4 col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="singlerate_cs_billing_start" id="singlerate_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['singlerate_cs_billing_start'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="singlerate_cl1_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1 + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1 + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1 and Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cl1_cs_peak" id="singlerate_cl1_cs_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1_cs_peak'])) ? $step1['singlerate_cl1_cs_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="singlerate_3_cl1" id="singlerate_3_cl1" value="<?php echo (!empty($step1) && isset($step1['singlerate_3_cl1'])) ? $step1['singlerate_3_cl1'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="singlerate_3_cs" id="singlerate_3_cs" value="<?php echo (!empty($step1) && isset($step1['singlerate_3_cs'])) ? $step1['singlerate_3_cs'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="singlerate_cl1_cs_billing_start" id="singlerate_cl1_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['singlerate_cl1_cs_billing_start'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><span class="elec_meter_type_label" style="font-weight: bold;">Time of Use</span><br><span class="des" style="margin-left: 0 !important;">Peak with Off Peak</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_peak" id="timeofuse_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_peak'])) ? $step1['timeofuse_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_offpeak" id="timeofuse_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_offpeak'])) ? $step1['timeofuse_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12" id="timeofuse_shoulder_field"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_shoulder" id="timeofuse_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_shoulder'])) ? $step1['timeofuse_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_PowerSmart_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (PowerSmart)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_ps_peak" id="timeofuse_ps_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ps_peak'])) ? $step1['timeofuse_ps_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_ps_offpeak" id="timeofuse_ps_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ps_offpeak'])) ? $step1['timeofuse_ps_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12" id="timeofuse_shoulder_field"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_ps_shoulder" id="timeofuse_ps_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ps_shoulder'])) ? $step1['timeofuse_ps_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_LoadSmart_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (LoadSmart)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_ls_peak" id="timeofuse_ls_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ls_peak'])) ? $step1['timeofuse_ls_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_ls_offpeak" id="timeofuse_ls_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ls_offpeak'])) ? $step1['timeofuse_ls_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12" id="timeofuse_shoulder_field"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_ls_shoulder" id="timeofuse_ls_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_ls_shoulder'])) ? $step1['timeofuse_ls_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak and Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="v4_field col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cs_peak" id="timeofuse_cs_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs_peak'])) ? $step1['timeofuse_cs_peak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cs_offpeak" id="timeofuse_cs_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs_offpeak'])) ? $step1['timeofuse_cs_offpeak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="timeofuse_cs" id="timeofuse_cs" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs'])) ? $step1['timeofuse_cs'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="timeofuse_cs_billing_start" id="timeofuse_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['timeofuse_cs_billing_start'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_cl1_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + CL1 + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + CL1 + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak, Controlled Load 1 and Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="v4_field col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cl1_cs_peak" id="timeofuse_cl1_cs_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_cs_peak'])) ? $step1['timeofuse_cl1_cs_peak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cl1_cs_offpeak" id="timeofuse_cl1_cs_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_cs_offpeak'])) ? $step1['timeofuse_cl1_cs_offpeak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="timeofuse_cl1" id="timeofuse_cl1" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1'])) ? $step1['timeofuse_cl1'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="timeofuse_2_cs" id="timeofuse_2_cs" value="<?php echo (!empty($step1) && isset($step1['timeofuse_2_cs'])) ? $step1['timeofuse_2_cs'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="timeofuse_cl1_cs_billing_start" id="timeofuse_cl1_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['timeofuse_cl1_cs_billing_start'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_cl1_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + CL1" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + CL1') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><span class="elec_meter_type_label" style="font-weight: bold;">Time of Use</span><br><span class="des" style="margin-left: 0 !important;">Peak with Off Peak, Controlled Load 1</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="v4_field col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cl1_peak" id="timeofuse_cl1_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_peak'])) ? $step1['timeofuse_cl1_peak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cl1_offpeak" id="timeofuse_cl1_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_offpeak'])) ? $step1['timeofuse_cl1_offpeak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="timeofuse_2_cl1" id="timeofuse_2_cl1" value="<?php echo (!empty($step1) && isset($step1['timeofuse_2_cl1'])) ? $step1['timeofuse_2_cl1'] : '';?>"></div>
                                        <div class="v4_field col-xs-12" id="timeofuse_cl1_shoulder_field"><span class="des">Shoulder</span><input type="text" class="form-control" name="timeofuse_cl1_shoulder" id="timeofuse_cl1_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_shoulder'])) ? $step1['timeofuse_cl1_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_cl2_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><span class="elec_meter_type_label" style="font-weight: bold;">Time of Use</span><br><span class="des" style="margin-left: 0 !important;">Peak with Off Peak, Controlled Load 2</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="v4_field col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cl2_peak" id="timeofuse_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl2_peak'])) ? $step1['timeofuse_cl2_peak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cl2_offpeak" id="timeofuse_cl2_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl2_offpeak'])) ? $step1['timeofuse_cl2_offpeak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="timeofuse_2_cl2" id="timeofuse_2_cl2" value="<?php echo (!empty($step1) && isset($step1['timeofuse_2_cl2'])) ? $step1['timeofuse_2_cl2'] : '';?>"></div>
                                        <div class="v4_field col-xs-12" id="timeofuse_cl2_shoulder_field"><span class="des">Shoulder</span><input type="text" class="form-control" name="timeofuse_cl2_shoulder" id="timeofuse_cl2_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl2_shoulder'])) ? $step1['timeofuse_cl2_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_cl1_cl2_radio">
                                    <label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + CL1 + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + CL1 + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><span class="elec_meter_type_label" style="font-weight: bold;">Time of Use</span><br><span class="des" style="margin-left: 0 !important;">Peak with Off Peak, Controlled Load 1 and Controlled Load 2</span>
                                    </label>
                                    <div class="row radio-hidden">
                                        <div class="v4_field col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cl1_cl2_peak" id="timeofuse_cl1_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_cl2_peak'])) ? $step1['timeofuse_cl1_cl2_peak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cl1_cl2_offpeak" id="timeofuse_cl1_cl2_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_cl2_offpeak'])) ? $step1['timeofuse_cl1_cl2_offpeak'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="timeofuse_3_cl1" id="timeofuse_3_cl1" value="<?php echo (!empty($step1) && isset($step1['timeofuse_3_cl1'])) ? $step1['timeofuse_3_cl1'] : '';?>"></div>
                                        <div class="v4_field col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="timeofuse_3_cl2" id="timeofuse_3_cl2" value="<?php echo (!empty($step1) && isset($step1['timeofuse_3_cl2'])) ? $step1['timeofuse_3_cl2'] : '';?>"></div>
                                        <div class="v4_field col-xs-12" id="timeofuse_cl1_cl2_shoulder_field"><span class="des">Shoulder</span><input type="text" class="form-control" name="timeofuse_cl1_cl2_shoulder" id="timeofuse_cl1_cl2_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_cl2_shoulder'])) ? $step1['timeofuse_cl1_cl2_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_tariff12_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (Tariff 12)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use (Tariff 12)') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Time of Use (Tariff 12)</strong><br><span class="des">Peak with Off Peak and Shoulder</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_tariff12_peak" id="timeofuse_tariff12_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_peak'])) ? $step1['timeofuse_tariff12_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_tariff12_offpeak" id="timeofuse_tariff12_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_offpeak'])) ? $step1['timeofuse_tariff12_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_tariff12_shoulder" id="timeofuse_tariff12_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_shoulder'])) ? $step1['timeofuse_tariff12_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="timeofuse_tariff13_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (Tariff 13)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use (Tariff 13)') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Time of Use (Tariff 13)</strong><br><span class="des">Peak with Off Peak and Shoulder</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_tariff13_peak" id="timeofuse_tariff13_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_peak'])) ? $step1['timeofuse_tariff13_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_tariff13_offpeak" id="timeofuse_tariff13_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_offpeak'])) ? $step1['timeofuse_tariff13_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_tariff13_shoulder" id="timeofuse_tariff13_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_shoulder'])) ? $step1['timeofuse_tariff13_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                                <div class="radio" id="flexible_pricing_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Flexible Pricing" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Flexible Pricing') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span><strong>Flexible Pricing</strong><br><span class="des">Peak with Off Peak and Shoulder</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="flexible_peak" id="flexible_peak" value="<?php echo (!empty($step1) && isset($step1['flexible_peak'])) ? $step1['flexible_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="flexible_offpeak" id="flexible_offpeak" value="<?php echo (!empty($step1) && isset($step1['flexible_offpeak'])) ? $step1['flexible_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="flexible_shoulder" id="flexible_shoulder" value="<?php echo (!empty($step1) && isset($step1['flexible_shoulder'])) ? $step1['flexible_shoulder'] : '';?>"></div>
                                    </div>
                                </div>
                            </div>
                    	</div>
                    	<div class="form-group" id="summer_winter_fields" style="display:none;">
                    		<label class="control-label v4_label"></label>
                    		<div class="v4_field">
                        	    <div class="col-sm-6 col-xs-12"><span class="des">Winter Peak</span>
                        	        <input type="text" class="form-control" placeholder="" name="elec_winter_peak" id="elec_winter_peak" value="<?php echo (!empty($step1)) ? $step1['elec_winter_peak'] : '';?>">
                        	    </div>
                        	    <div class="col-sm-6 col-xs-12"><span class="des">When did the bill start?</span>
                        	        <input type="text" class="form-control" placeholder="" name="elec_billing_start" id="elec_billing_start" value="<?php echo (!empty($step1)) ? $step1['elec_billing_start'] : '';?>">
                        	    </div>
                    		</div>
						</div>
                    </div>
                    <div class="form-section col-sm-12 clearfix g-y g-n hidden-field">
                    	<h2>Gas Details</h2>
                    	<div class="g-y hidden-field">
                        <div class="form-group">
                            <label class="control-label v4_label"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Who is your current gas supplier?</label>
                            <div class="v4_field">
                                <select class="form-control" name="gas_supplier" id="gas_supplier">
                                	<option value="">Please Select</option>
                                    <option value="ActewAGL" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
									<option value="AGL" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
									<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Alinta Energy') ? 'selected="selected"' : '';?>>Alinta Energy</option>
									<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
									<option value="Click Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Click Energy') ? 'selected="selected"' : '';?>>Click Energy</option>
									<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
									<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
									<option value="Energy Australia" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Energy Australia') ? 'selected="selected"' : '';?>>Energy Australia</option>
									<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
									<option value="Momentum" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Momentum') ? 'selected="selected"' : '';?>>Momentum</option>
									<option value="Origin Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Origin Energy') ? 'selected="selected"' : '';?>>Origin Energy</option>
									<option value="Powershop" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Powershop') ? 'selected="selected"' : ''; ?>>Powershop</option>
									<option value="Red Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Red Energy') ? 'selected="selected"' : '';?>>Red Energy</option>
									<option value="Simply Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Simply Energy') ? 'selected="selected"' : '';?>>Simply Energy</option>
									<option value="Sumo Power" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Sumo Power') ? 'selected="selected"' : ''; ?>>Sumo Power</option>
									<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label v4_label"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>How many days are in the billing period?</label>
                            <div class="v4_field">
                                <input type="text" class="form-control" placeholder="" name="gas_billing_days" id="gas_billing_days" value="<?php echo (!empty($step1)) ? $step1['gas_billing_days'] : '';?>">
                            </div>
                            <label class="control-label col-sm-1 col-xs-3">days</label>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label v4_label"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>When did the bill start?</label>
                            <div class="v4_field">
                                <input type="text" class="form-control" placeholder="" name="gas_billing_start" id="gas_billing_start" value="<?php echo (!empty($step1)) ? $step1['gas_billing_start'] : '';?>">
                            </div>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label v4_label"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Spend</label>
                            <div class="v4_field">
                            	<div class="has-prefix">
                            	<div class="prefix">$</div>
                                <input type="text" class="form-control" placeholder="" name="gas_spend" id="gas_spend" value="<?php echo (!empty($step1)) ? $step1['gas_spend'] : '';?>">
                            	</div>
                            </div>
                    	</div>
                        <div class="form-group">
                            <label class="control-label v4_label v2"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>What is the usage amount on your bill?</label>
                            <div class="v4_field">
                        	<div class="row">
                            	<div class="col-sm-6 col-xs-6"><div class="has-tail"><div class="des">Peak</div><div class="tail">MJ</div><input type="text" class="form-control" placeholder="" name="gas_peak" id="gas_peak" value="<?php echo (!empty($step1)) ? $step1['gas_peak'] : '';?>"></div></div>
                            	<div class="col-sm-6 col-xs-6"><div class="has-tail"><div class="des">Off-Peak (if shown)</div><div class="tail">MJ</div><input type="text" class="form-control" placeholder="" name="gas_off_peak" id="gas_off_peak" value="<?php echo (!empty($step1)) ? $step1['gas_off_peak'] : '';?>"></div></div>
                            </div>
                            </div>
                        </div>
                    	</div>
                    	<div class="g-n hidden-field">
                    	<div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="If you're unsure, click Unsure/Other"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Who is your current gas supplier?</label>
                        <div class="v4_field">
                            <select class="form-control" name="gas_supplier2" id="gas_supplier2">
                            	<option value="">Please Select</option>
                            	<option value="ActewAGL" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
								<option value="AGL" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
								<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Alinta Energy') ? 'selected="selected"' : '';?>>Alinta Energy</option>
								<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
								<option value="Click Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Click Energy') ? 'selected="selected"' : '';?>>Click Energy</option>
								<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
								<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
								<option value="Energy Australia" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Energy Australia') ? 'selected="selected"' : '';?>>Energy Australia</option>
								<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
								<option value="Momentum" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Momentum') ? 'selected="selected"' : '';?>>Momentum</option>
								<option value="Origin Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Origin Energy') ? 'selected="selected"' : '';?>>Origin Energy</option>
								<option value="Powershop" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Powershop') ? 'selected="selected"' : ''; ?>>Powershop</option>
								<option value="Red Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Red Energy') ? 'selected="selected"' : '';?>>Red Energy</option>
								<option value="Simply Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Simply Energy') ? 'selected="selected"' : '';?>>Simply Energy</option>
								<option value="Sumo Power" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Sumo Power') ? 'selected="selected"' : ''; ?>>Sumo Power</option>
								<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                            </select>
                        </div>
						</div>
						<div class="form-group">
                        <label class="control-label v4_label v2"><span class="info"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>How much is an average gas bill for you household?</label>
                        <input type="hidden" name="gas_usage_level" value="<?php echo (!empty($step1)) ? $step1['gas_usage_level'] : '';?>" id="gas_usage_level">
                            <div class="col-sm-12">
                        	    <div id="gas_usage_level_buttons"></div>
                            </div>
						</div>
                    </div>
                    </div>
                    <div class="form-section col-sm-12 clearfix" id="business-section" style="display:none;">
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Company Industry"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Company Industry</label>
                        <div class="v4_field">
                            <select id="company_industry" name="company_industry" class="form-control">
                                <option value="Unknown">Unknown</option>
                                <option value="Accommodation">Accommodation</option>
                                <option value="Air and Space Transport">Air and Space Transport</option>
                                <option value="Apple and Pear Growing">Apple and Pear Growing</option>
                                <option value="Aquaculture">Aquaculture</option>
                                <option value="Arts">Arts</option>
                                <option value="Bakery Product Manufacturing">Bakery Product Manufacturing</option>
                                <option value="Basic Chemical Manufacturing">Basic Chemical Manufacturing</option>
                                <option value="Basic Non-Ferrous Metal Manufacturing">Basic Non-Ferrous Metal Manufacturing</option>
                                <option value="Beef Cattle Farming">Beef Cattle Farming</option>
                                <option value="Beverage and Malt Manufacturing">Beverage and Malt Manufacturing</option>
                                <option value="Builders Supplies Wholesaling">Builders Supplies Wholesaling</option>
                                <option value="Building Completion Services">Building Completion Services</option>
                                <option value="Building Construction">Building Construction</option>
                                <option value="Building Structure Services">Building Structure Services</option>
                                <option value="Cafes and Restaurants">Cafes and Restaurants</option>
                                <option value="Cement and Concrete Product Manufacturing">Cement and Concrete Product Manufacturing</option>
                                <option value="Ceramic Product Manufacturing">Ceramic Product Manufacturing</option>
                                <option value="Child Care Services">Child Care Services</option>
                                <option value="Clothing and Soft Good Retailing">Clothing and Soft Good Retailing</option>
                                <option value="Clothing Manufacturing">Clothing Manufacturing</option>
                                <option value="Clubs (Hospitality)">Clubs (Hospitality)</option>
                                <option value="Coal Mining">Coal Mining</option>
                                <option value="Community Care Services">Community Care Services</option>
                                <option value="Construction Material Mining">Construction Material Mining</option>
                                <option value="Crop and Plant Growing n.e.c.">Crop and Plant Growing n.e.c.</option>
                                <option value="Cut Flower and Flower Seed Growing">Cut Flower and Flower Seed Growing</option>
                                <option value="Dairy Cattle Farming">Dairy Cattle Farming</option>
                                <option value="Dairy Product Manufacturing">Dairy Product Manufacturing</option>
                                <option value="Deer Farming">Deer Farming</option>
                                <option value="Defence">Defence</option>
                                <option value="Department Stores">Department Stores</option>
                                <option value="Deposit Taking Financiers">Deposit Taking Financiers</option>
                                <option value="Electrical Equipment and Appliance Manufacturing">Electrical Equipment and Appliance Manufacturing</option>
                                <option value="Electricity Supply">Electricity Supply</option>
                                <option value="Electronic Equipment Manufacturing">Electronic Equipment Manufacturing</option>
                                <option value="Exploration">Exploration</option>
                                <option value="Fabricated Metal Product Manufacturing">Fabricated Metal Product Manufacturing</option>
                                <option value="Farm Produce Wholesaling">Farm Produce Wholesaling</option>
                                <option value="Film and Video Services">Film and Video Services</option>
                                <option value="Financial Asset Investors">Financial Asset Investors</option>
                                <option value="Finfish Trawling">Finfish Trawling</option>
                                <option value="Flour Mill and Cereal Food Manufacturing">Flour Mill and Cereal Food Manufacturing</option>
                                <option value="Food, Drink and Tobacco Wholesaling">Food, Drink and Tobacco Wholesaling</option>
                                <option value="Footwear Manufacturing">Footwear Manufacturing</option>
                                <option value="Foreign Government Representation">Foreign Government Representation</option>
                                <option value="Forestry">Forestry</option>
                                <option value="Fruit and Vegetable Processing">Fruit and Vegetable Processing</option>
                                <option value="Fruit Growing n.e.c.">Fruit Growing n.e.c.</option>
                                <option value="Furniture Manufacturing">Furniture Manufacturing</option>
                                <option value="Furniture, Houseware and Appliance Retail">Furniture, Houseware and Appliance Retail</option>
                                <option value="Gambling Services">Gambling Services</option>
                                <option value="Gas Supply">Gas Supply</option>
                                <option value="Glass and Glass Product Manufacturing">Glass and Glass Product Manufacturing</option>
                                <option value="Government Administration">Government Administration</option>
                                <option value="Grape Growing">Grape Growing</option>
                                <option value="Hospitals and Nursing Homes">Hospitals and Nursing Homes</option>
                                <option value="Household Equipment Repair Services">Household Equipment Repair Services</option>
                                <option value="Household Good Wholesaling">Household Good Wholesaling</option>
                                <option value="Industrial Machinery and Equipment Manufacturing">Industrial Machinery and Equipment Manufacturing</option>
                                <option value="Installation Trade Services">Installation Trade Services</option>
                                <option value="Interest Groups">Interest Groups</option>
                                <option value="Iron and Steel Manufacturing">Iron and Steel Manufacturing</option>
                                <option value="Justice">Justice</option>
                                <option value="Kiwi Fruit Growing">Kiwi Fruit Growing</option>
                                <option value="Knitting Mills">Knitting Mills</option>
                                <option value="Leather and Leather Product Manufacturing">Leather and Leather Product Manufacturing</option>
                                <option value="Legal and Accounting Services">Legal and Accounting Services</option>
                                <option value="Libraries">Libraries</option>
                                <option value="Life Insurance and Superannuation Funds">Life Insurance and Superannuation Funds</option>
                                <option value="Livestock Farming n.e.c.">Livestock Farming n.e.c.</option>
                                <option value="Log Sawmilling and Timber Dressing">Log Sawmilling and Timber Dressing</option>
                                <option value="Logging">Logging</option>
                                <option value="Machinery and Equipment Hiring and Leasing">Machinery and Equipment Hiring and Leasing</option>
                                <option value="Machinery and Equipment Wholesaling">Machinery and Equipment Wholesaling</option>
                                <option value="Marine Fishing n.e.c.">Marine Fishing n.e.c.</option>
                                <option value="Marketing and Business Management Service">Marketing and Business Management Service</option>
                                <option value="Meat and Meat Product Manufacturing">Meat and Meat Product Manufacturing</option>
                                <option value="Medical and Dental Services">Medical and Dental Services</option>
                                <option value="Metal Ore Mining">Metal Ore Mining</option>
                                <option value="Mineral, Metal and Chemical Wholesaling">Mineral, Metal and Chemical Wholesaling</option>
                                <option value="Motor Vehicle and Part Manufacturing">Motor Vehicle and Part Manufacturing</option>
                                <option value="Motor Vehicle Retailing">Motor Vehicle Retailing</option>
                                <option value="Motor Vehicle Services">Motor Vehicle Services</option>
                                <option value="Motor Vehicle Wholesaling">Motor Vehicle Wholesaling</option>
                                <option value="Museums">Museums</option>
                                <option value="Non-Building Construction">Non-Building Construction</option>
                                <option value="Non-Ferrous Basic Metal Product Manufacturing">Non-Ferrous Basic Metal Product Manufacturing</option>
                                <option value="Non-Financial Asset Investors">Non-Financial Asset Investors</option>
                                <option value="Non-Metallic Mineral Product Manufacturing">Non-Metallic Mineral Product Manufacturing</option>
                                <option value="Oil and Fat Manufacturing">Oil and Fat Manufacturing</option>
                                <option value="Oil and Gas Extraction">Oil and Gas Extraction</option>
                                <option value="Other Business Services">Other Business Services</option>
                                <option value="Other Chemical Product Manufacturing">Other Chemical Product Manufacturing</option>
                                <option value="Other Construction Services">Other Construction Services</option>
                                <option value="Other Education">Other Education</option>
                                <option value="Other Financiers">Other Financiers</option>
                                <option value="Other Food Manufacturing">Other Food Manufacturing</option>
                                <option value="Other Health Services">Other Health Services</option>
                                <option value="Other Insurance">Other Insurance</option>
                                <option value="Other Manufacturing">Other Manufacturing</option>
                                <option value="Other Mining">Other Mining</option>
                                <option value="Other Mining Services">Other Mining Services</option>
                                <option value="Other Personal and Household Good Retailing">Other Personal and Household Good Retailing</option>
                                <option value="Other Personal Services">Other Personal Services</option>
                                <option value="Other Recreation Services">Other Recreation Services</option>
                                <option value="Other Services to Transport">Other Services to Transport</option>
                                <option value="Other Transport">Other Transport</option>
                                <option value="Other Transport Equipment Manufacturing">Other Transport Equipment Manufacturing</option>
                                <option value="Other Wholesaling">Other Wholesaling</option>
                                <option value="Other Wood Product Manufacturing">Other Wood Product Manufacturing</option>
                                <option value="Paper and Paper Product Manufacturing">Paper and Paper Product Manufacturing</option>
                                <option value="Parks and Gardens">Parks and Gardens</option>
                                <option value="Personal and Household Goods Hiring">Personal and Household Goods Hiring</option>
                                <option value="Petroleum and Coal Product Manufacturing">Petroleum and Coal Product Manufacturing</option>
                                <option value="Petroleum Refining">Petroleum Refining</option>
                                <option value="Photo and Scientific Equipment Manufacturing">Photo and Scientific Equipment Manufacturing</option>
                                <option value="Plant Nurseries">Plant Nurseries</option>
                                <option value="Plastic Product Manufacturing">Plastic Product Manufacturing</option>
                                <option value="Post School Education">Post School Education</option>
                                <option value="Postal and Courier Services">Postal and Courier Services</option>
                                <option value="Prefabricated Building Manufacturing">Prefabricated Building Manufacturing</option>
                                <option value="Preschool Education">Preschool Education</option>
                                <option value="Printing and Services to Printing">Printing and Services to Printing</option>
                                <option value="Private Households Employing Staff">Private Households Employing Staff</option>
                                <option value="Property Operators and Developers">Property Operators and Developers</option>
                                <option value="Public Order and Safety Services">Public Order and Safety Services</option>
                                <option value="Publishing">Publishing</option>
                                <option value="Pubs, Taverns and Bars">Pubs, Taverns and Bars</option>
                                <option value="Radio and Television Services">Radio and Television Services</option>
                                <option value="Rail Transport">Rail Transport</option>
                                <option value="Real Estate Agents">Real Estate Agents</option>
                                <option value="Recorded Media Manufacturing and Publish">Recorded Media Manufacturing and Publish</option>
                                <option value="Recreational Good Retailing">Recreational Good Retailing</option>
                                <option value="Religious Organisations">Religious Organisations</option>
                                <option value="Residential">Residential</option>
                                <option value="Road Freight Transport">Road Freight Transport</option>
                                <option value="Road Passenger Transport">Road Passenger Transport</option>
                                <option value="Rubber Product Manufacturing">Rubber Product Manufacturing</option>
                                <option value="School Education">School Education</option>
                                <option value="Scientific Research">Scientific Research</option>
                                <option value="Services to Air Transport">Services to Air Transport</option>
                                <option value="Services to Finance and Investment">Services to Finance and Investment</option>
                                <option value="Services to Forestry">Services to Forestry</option>
                                <option value="Services to Insurance">Services to Insurance</option>
                                <option value="Services to Road Transport">Services to Road Transport</option>
                                <option value="Services to the Arts">Services to the Arts</option>
                                <option value="Services to Water Transport">Services to Water Transport</option>
                                <option value="Sheep Farming">Sheep Farming</option>
                                <option value="Sheep-Beef Cattle Farming">Sheep-Beef Cattle Farming</option>
                                <option value="Sheet Metal Product Manufacturing">Sheet Metal Product Manufacturing</option>
                                <option value="Site Preparation Services">Site Preparation Services</option>
                                <option value="Specialised Food Retailing">Specialised Food Retailing</option>
                                <option value="Sport">Sport</option>
                                <option value="Stone Fruit Growing">Stone Fruit Growing</option>
                                <option value="Storage">Storage</option>
                                <option value="Structural Metal Product Manufacturing">Structural Metal Product Manufacturing</option>
                                <option value="Supermarket and Grocery Stores">Supermarket and Grocery Stores</option>
                                <option value="Technical Services">Technical Services</option>
                                <option value="Telecommunication Services">Telecommunication Services</option>
                                <option value="Textile and Woven Fabric Manufacturing">Textile and Woven Fabric Manufacturing</option>
                                <option value="Textile Product Manufacturing">Textile Product Manufacturing</option>
                                <option value="Textile, Clothing and Footwear Wholesaling">Textile, Clothing and Footwear Wholesaling</option>
                                <option value="Tobacco Product Manufacturing">Tobacco Product Manufacturing</option>
                                <option value="Vegetable Growing">Vegetable Growing</option>
                                <option value="Veterinary Services">Veterinary Services</option>
                                <option value="Water Supply, Sewerage and Drainage Services">Water Supply, Sewerage and Drainage Services</option>
                                <option value="Water Transport">Water Transport</option>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="Are you the Tenant or the Owner?"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Are you the Tenant or the Owner?</label>
                        <div class="v4_field">
                            <select class="form-control" name="tenant_owner" id="tenant_owner">
                                <option value="">Select</option>
                                <option value="Renter" <?php if (!empty($step1) && $step1['tenant_owner'] == 'Renter'):?>selected="selected"<?php endif;?>>Tenant</option>
                                <option value="Owner" <?php if (!empty($step1) && $step1['tenant_owner'] == 'Owner'):?>selected="selected"<?php endif;?>>Owner</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group battery-storage-solution" style="display:none;">
                        <label class="control-label v4_label v2 v3"><span class="info" title="Are you interested to know more about our battery storage solution?"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Are you interested to know more about our battery storage solution?</label>
                        <div class="v4_field">
                            <select class="form-control" name="battery_storage_solution" id="battery_storage_solution">
                                <option value="">Select</option>
                                <option value="Yes" <?php if (!empty($step1) && $step1['battery_storage_solution'] == 'Yes'):?>selected="selected"<?php endif;?>>Yes</option>
                                <option value="No" <?php if (!empty($step1) && $step1['battery_storage_solution'] == 'No'):?>selected="selected"<?php endif;?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group battery-storage-solar-solution" style="display:none;">
                        <label class="control-label v4_label"><span class="info" title="Are you interested to know more about our battery storage & solar solution?"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Are you interested to know more about our battery storage & solar solution?</label>
                        <div class="v4_field">
                            <select class="form-control" name="battery_storage_solar_solution" id="battery_storage_solar_solution">
                                <option value="">Select</option>
                                <option value="Yes" <?php if (!empty($step1) && $step1['battery_storage_solar_solution'] == 'Yes'):?>selected="selected"<?php endif;?>>Yes</option>
                                <option value="No" <?php if (!empty($step1) && $step1['battery_storage_solar_solution'] == 'No'):?>selected="selected"<?php endif;?>>No</option>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="form-section col-sm-12 clearfix no-bill-script" style="display:none;">
                    <div class="form-group elec-and-gas" style="display:none;">
                        <div class="col-sm-12 text-center">
                        <p style="font-weight: bold; font-size: 16px; color: red;">In your situation, I will calculate the electricity comparison based on an average general usage tariff of <span class="elec-and-gas-elec-peak">[elec peak]</span> kWh over a <span class="elec-and-gas-elec-billing-days">[elec billing days]</span> day billing period & the gas comparison based on an average typical usage of <span class="elec-and-gas-gas-peak">[gas peak]</span> mj over a <span class="elec-and-gas-gas-billing-days">[gas billing days]</span> day billing period.</p>
                        <p>If customer asks about usage values: <br>Our usage profiles are estimates only and are calculated using benchmark data sets from the Australian Energy Market Operator (AEMO)</p>
                        </div>
                    </div>
                    <div class="form-group elec-only" style="display:none;">
                        <div class="col-sm-12 text-center">
                        <p style="font-weight: bold; font-size: 16px; color: red;">In your situation, I will calculate the electricity comparison based on an average general usage tariff of <span class="elec-peak-usage">[Dynamic peak usage]</span> kWh over a <span class="elec-billing-days">[dynamic billing days]</span> day billing period</p>
                        <p>If customer asks about usage values: <br>Our usage profiles are estimates only and are calculated using benchmark data sets from the Australian Energy Market Operator (AEMO)</p>
                        </div>
                    </div>
                    <div class="form-group gas-only" style="display:none;">
                        <div class="col-sm-12 text-center">
                        <p style="font-weight: bold; font-size: 16px; color: red;">In your situation, I will calculate the gas comparison based on an average typical usage of <span class="gas-peak-usage">[Dynamic peak usage]</span> mj over a <span class="gas-billing-days">[dynamic billing days]</span> day billing period</p>
                        <p>If customer asks about usage values: <br>Our usage profiles are estimates only and are calculated using benchmark data sets from the Australian Energy Market Operator (AEMO)</p>
                        </div>
                    </div>
                    </div>
                    <?php if (false):?>
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group" id="business_name_field" style="display:none;">
                        <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Business Name</label>
                        <div class="v4_field">
                            <input class="form-control" type="text" name="business_name" value="<?php echo (!empty($step1)) ? $step1['business_name'] : '';?>" id="business_name" placeholder="Business Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label v4_label v2"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Your Name</label>
						<div class="row"><div class="col-sm-6">
                        <div class="v4_field">
                            <input class="form-control" type="text" name="first_name" value="<?php echo (!empty($step1)) ? $step1['first_name'] : '';?>" id="first_name" placeholder="First Name">
                        </div>
						</div><div class="col-sm-6">
                        <div class="v4_field">
                            <input class="form-control" type="text" name="surname" value="<?php echo (!empty($step1)) ? $step1['surname'] : '';?>" id="surname" placeholder="Surname">
                        </div>
						</div></div>	
                    </div>
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Your Mobile Number</label>
                        <div class="v4_field">
                            <input class="form-control" type="tel" name="mobile" value="<?php echo (!empty($step1)) ? $step1['mobile'] : '';?>" id="mobile" placeholder="Mobile">
                            <input class="form-control hidden-field" type="tel" name="phone" value="<?php echo (!empty($step1)) ? $step1['phone'] : '';?>" id="phone" placeholder="Landline">
                            <div class="checkbox">
                            	<label><input type="checkbox" name="other_number" id="other_number" value="1" <?php if (!empty($step1) && $step1['other_number'] == 1):?>checked="checked"<?php endif;?>>Other Number</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title=""><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Your Email Address</label>
                        <div class="v4_field">
                            <input class="form-control" type="text" name="email" value="<?php echo (!empty($step1)) ? $step1['email'] : '';?>" id="email" placeholder="Email Address">
                        </div>
                    </div>
                    </div>
                    <?php endif;?>
					
					<div class="form-section"><div class="row">
                    <div class="col-sm-12 clearfix">
                    <div class="form-group">
                        <div class="text-left" id="term1_field">
                            <div class="checkbox-simulate"><input type="checkbox" id="term1" value="1" name="term1" checked="checked"><label for="term1" class="checkbox-simulate-bar"></label></div>
                            I have read, understood and accept the <a href="https://dealexpert.com.au/terms-and-conditions/" target="_blank">Terms and Conditions</a>.
                        </div>
                    </div>
                    </div></div>

                    <div class="row"><div class="col-sm-12 clearfix">
                    <div class="form-group" style="position:relative; clear:both;">
                    	<a href="javascript:;" class="btn-grey pull-left no-sale">No Sale</a>
                        <a href="javascript:;" class="btn-orange pull-right continue">Comparison</a>
                        <div id="step1_error_message"></div>
                    </div>
                    </div></div>
					</div>	

                    <div class="form-section col-sm-12 clearfix" id="no_sale_section" style="display:none;">
                    <div class="form-group">
                        <label class="control-label v4_label"><span class="info" title="No Sale"><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>No Sale</label>
                        <div class="v4_field">
                            <select class="form-control" id="lead_action" name="lead_action">
                                <option value="">Please select</option>
                                <option value="193">Did not call</option>
                                <option value="201">Wants To Shop Around</option>
                                <option value="188">Getting Better Deal Already</option>
                                <option value="189">In contract</option>
                                <option value="192">Do Not Call</option>
                                <option value="191">Not Serviceable</option>
                                <option value="192">Duplicate lead</option>
                            </select>
                        </div>
                        <div class="v4_field">
                            <a href="javascript:;" class="btn-orange pull-left no-sale-ok">OK</a>
                        </div>
                    </div>
                    </div>

			</div>
		</div>
		</div>
	</div>		
</form>
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
