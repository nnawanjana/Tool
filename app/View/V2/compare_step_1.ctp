
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
		<form id="step1_form" onsubmit="return false;">
			<input type="hidden" value="<?php echo $sid;?>" name="sid" id="sid">
<?php if (!$sid && !$tracking):?>
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['medium'])) echo $this->request->query['submitted']['medium'];?>" name="medium">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['source'])) echo $this->request->query['submitted']['source'];?>" name="source">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['url'])) echo $this->request->query['submitted']['url'];?>" name="url">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['term'])) echo $this->request->query['submitted']['term'];?>" name="term">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['content'])) echo $this->request->query['submitted']['content'];?>" name="content">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['kwid'])) echo $this->request->query['submitted']['kwid'];?>" name="kwid">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['keyword'])) echo $this->request->query['submitted']['keyword'];?>" name="keyword">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['adid'])) echo $this->request->query['submitted']['adid'];?>" name="adid">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['campaign'])) echo $this->request->query['submitted']['campaign'];?>" name="campaign">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['publisher'])) echo $this->request->query['submitted']['publisher'];?>" name="publisher">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['utm_campaign'])) echo $this->request->query['submitted']['utm_campaign'];?>" name="utm_campaign">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['mtype'])) echo $this->request->query['submitted']['mtype'];?>" name="mtype">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['group'])) echo $this->request->query['submitted']['group'];?>" name="group">
			<input type="hidden" value="<?php echo (isset($this->request->query['submitted']['how_they_found_us'])) ? $this->request->query['submitted']['how_they_found_us'] : 'Website Lead - Google Search';?>" name="how_they_found_us">
			<input type="hidden" value="<?php if (isset($this->request->query['submitted']['leadage'])) echo $this->request->query['submitted']['leadage'];?>" name="leadage">
<?php endif;?>
			<input type="hidden" value="<?php echo (!empty($step1)) ? $step1['solar_rebate_scheme'] : '';?>" name="solar_rebate_scheme" id="solar_rebate_scheme">
				<div style="display: none;" id="processing">
					<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
				</div>
				<div id="step1" class="step clearfix">
                    <h2>About You</h2>
                    <div class="start-new-comparison">
                    	<a href="/<?php echo $this->params['controller'];?>/compare/1?refresh=1">Start new comparison</a>
                    </div>
                    <div class="form-horizontal">
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group" id="postcode_field">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="The deals on our panel are dependant on the area you live in, so please select the postcode and suburb your property is in"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Postcode and suburb</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="number" min="0"  name="postcode" value="<?php echo $postcode;?>" id="postcode" placeholder="E.g. 3000">
                        </div>
                         <div class="col-sm-3">
                            <select class="form-control" name="suburb" id="suburb">
                            	<option value="">Suburb</option>
                            </select>
                            <input type="hidden" name="state" value="<?php echo $state;?>" id="state">
                        </div>
                        <div class="col-sm-12 text-center">
                        	<p id="act-disclaimer" style="display:none;">Disclaimer: Currently we only have one retailer on our panel in this area, meaning we cannot compare multiple options for you, but we can compare your current retailer to Origin Energy.</p>
                        </div>
                    </div>
                    </div>
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="We'll need to know what kind of comparison you're after"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>What are you looking to compare?</label>
                        <div class="col-sm-5">
                            <input type="hidden" name="plan_type" value="<?php echo (!empty($step1)) ? $step1['plan_type'] : '';?>" id="plan_type">
                            <div id="Dual" class="plan-type plan-type-eg"></div>
                            <div id="Elec" class="plan-type plan-type-e"></div>
                            <div id="Gas"  class="plan-type plan-type-g"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12 text-center">
                        	<p id="elec-disclaimer" style="display:none;">Disclaimer: would you be willing to pay by direct debit or an online portal to possibly get the best deal in the area?</p>
							<p id="dual-disclaimer" style="display:none;">Disclaimer: would you be willing to split your electricity and gas retailers if it works out to be a cheaper option?</p>
                        </div>
                    </div>
                    </div>
                    
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="What kind of property is this?"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Select your comparison type</label>
                        <div class="col-sm-5">
                            <input type="hidden" name="customer_type" value="<?php echo (!empty($step1)) ? $step1['customer_type'] : 'RES';?>" id="customer_type">
                            <input type="hidden" name="looking_for" value="<?php echo (!empty($step1)) ? $step1['looking_for'] : 'Compare Plans';?>" id="looking_for">
                            <div id="RES" class="customer-type customer-type-r"></div>
                            <div id="SME" class="customer-type customer-type-b"></div>
                            <div id="MoveIn" class="looking-for movein"></div>
                            <div id="Transfer" class="looking-for transfer"></div>
                        </div>
                    </div>
                    <div class="form-group" id="move_in_date_field" style="display:none;">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Is this for an existing property, or are you needing a connection for a new one?"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Move in date</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" placeholder="" name="move_in_date" id="move_in_date" value="<?php echo (!empty($step1)) ? $step1['move_in_date'] : '';?>">
                            <div class="checkbox">
								<label><input type="checkbox" name="move_in_date_not_sure" id="move_in_date_not_sure" value="1" <?php if (!empty($step1) && $step1['move_in_date_not_sure'] == 1):?>checked="checked"<?php endif;?>>Not Sure</label>
							</div>
                        </div>

                    </div>
                    </div>
                    <div class="form-section col-sm-12 clearfix" id="recent_bill_field" style="display:none;">
                    <div class="form-group" id="elec_recent_bill_field" style="display:none;">
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
                    <div class="form-group" id="gas_recent_bill_field" style="display:none;">
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
                    
                    <div class="form-section col-sm-12 clearfix e-y e-n hidden-field"> 
                    	<h2>Electricity Details</h2>
                    	<div class="e-y hidden-field">
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Who is your current electricity supplier?</label>
                            <div class="col-sm-5">
                                <select class="form-control" name="elec_supplier" id="elec_supplier">
                                    <option value="">Please Select</option>
                                    <option value="ActewAGL" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
									<option value="AGL" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
									<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Alinta Energy') ? 'selected="selected"' : '';?>>Alinta Energy</option>
									<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
									<option value="Click Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Click Energy') ? 'selected="selected"' : '';?>>Click Energy</option>
									<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
									<option value="Diamond Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Diamond Energy') ? 'selected="selected"' : '';?>>Diamond Energy</option>
									<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
									<option value="Ergon Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Ergon Energy') ? 'selected="selected"' : '';?>>Ergon Energy</option>
									<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
									<option value="Momentum" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Momentum') ? 'selected="selected"' : '';?>>Momentum</option>
									<option value="Neighbourhood Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Neighbourhood Energy') ? 'selected="selected"' : '';?>>Neighbourhood Energy</option>
									<option value="Origin Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Origin Energy') ? 'selected="selected"' : '';?>>Origin Energy</option>
									<option value="Powerdirect" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Powerdirect') ? 'selected="selected"' : '';?>>Powerdirect</option>
									<option value="Powershop" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Powershop') ? 'selected="selected"' : '';?>>Powershop</option>
									<option value="QEnergy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'QEnergy') ? 'selected="selected"' : '';?>>QEnergy</option>
									<option value="Red Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Red Energy') ? 'selected="selected"' : '';?>>Red Energy</option>
									<option value="Sanctuary Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Sanctuary Energy') ? 'selected="selected"' : '';?>>Sanctuary Energy</option>
									<option value="Simply Energy" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Simply Energy') ? 'selected="selected"' : '';?>>Simply Energy</option>
									<option value="Energy Australia" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Energy Australia') ? 'selected="selected"' : '';?>>Energy Australia</option>
									<option value="Sumo Power" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Sumo Power') ? 'selected="selected"' : '';?>>Sumo Power</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>How many days are in the billing period?</label>
                            <div class="col-sm-2 col-xs-9">
                                <input type="text" class="form-control" placeholder="" name="elec_billing_days" id="elec_billing_days" value="<?php echo (!empty($step1)) ? $step1['elec_billing_days'] : '';?>">
                            </div>
                            <label class="control-label col-sm-1 col-xs-3">days</label>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Spend</label>
                            <div class="col-sm-2 col-xs-9">
                            	<div class="has-prefix">
                            	<div class="prefix">$</div>
                                <input type="text" class="form-control" placeholder="" name="elec_spend" id="elec_spend" value="<?php echo (!empty($step1)) ? $step1['elec_spend'] : '';?>">
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
								<option value="Sumo Power" <?php echo (!empty($step1) && $step1['elec_supplier'] == 'Sumo Power') ? 'selected="selected"' : '';?>>Sumo Power</option>
								<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>What level best describes your typical electricity usage?</label>
                        <input type="hidden" name="elec_usage_level" value="<?php echo (!empty($step1)) ? $step1['elec_usage_level'] : '';?>" id="elec_usage_level">
                        <input type="hidden" name="elec_meter_type2" value="<?php echo (!empty($step1)) ? $step1['elec_meter_type2'] : '';?>" id="elec_meter_type2">
                        <div class="col-sm-7">
                        	<div id="elec_usage_level_buttons"></div>
                        </div>
                    </div>
                    </div>
                    <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>NMI</label>
                            <div class="col-sm-2 col-xs-9">
                                <input type="text" class="form-control" placeholder="" name="nmi" id="nmi" value="<?php echo (!empty($step1)) ? $step1['nmi'] : '';?>" maxlength="11"> <!--62032659066-->
                                <div id="nmi_distributor"></div>
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
                    	<div class="form-group" id="solar_fields" style="display:none;">
                    		<label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title=""><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Solar Generated</label>
							<div class="col-sm-2 col-xs-9">
                            	<input class="form-control" type="text" name="solar_generated" value="<?php echo (!empty($step1)) ? $step1['solar_generated'] : '';?>" id="solar_generated">
							</div>
                    	</div>
                        <div class="form-group" id="elec_meter_type_fields">
                            <label class="control-label col-sm-4 col-sm-offset-1">Please enter your usage consumption</label>
                            <div class="col-sm-7">
                                <div class="radio" id="singlerate_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Single Rate</strong><br><span class="des">One rate at all times</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_peak" id="singlerate_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_peak'])) ? $step1['singlerate_peak'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cl1_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cl1_peak" id="singlerate_cl1_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1_peak'])) ? $step1['singlerate_cl1_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="singlerate_cl1" id="singlerate_cl1" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1'])) ? $step1['singlerate_cl1'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cl2_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 2</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cl2_peak" id="singlerate_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl2_peak'])) ? $step1['singlerate_cl2_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="singlerate_cl2" id="singlerate_cl2" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl2'])) ? $step1['singlerate_cl2'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cl1_cl2_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1 + CL2" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1 + CL2') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1 and Controlled Load 2</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cl1_cl2_peak" id="singlerate_cl1_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cl1_cl2_peak'])) ? $step1['singlerate_cl1_cl2_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 1</span><input type="text" class="form-control" name="singlerate_2_cl1" id="singlerate_2_cl1" value="<?php echo (!empty($step1) && isset($step1['singlerate_2_cl1'])) ? $step1['singlerate_2_cl1'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Controlled Load 2</span><input type="text" class="form-control" name="singlerate_2_cl2" id="singlerate_2_cl2" value="<?php echo (!empty($step1) && isset($step1['singlerate_2_cl2'])) ? $step1['singlerate_2_cl2'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="singlerate_cs_peak" id="singlerate_cs_peak" value="<?php echo (!empty($step1) && isset($step1['singlerate_cs_peak'])) ? $step1['singlerate_cs_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="singlerate_cs" id="singlerate_cs" value="<?php echo (!empty($step1) && isset($step1['singlerate_cs'])) ? $step1['singlerate_cs'] : '';?>"></div>
										<div class="col-sm-4 col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="singlerate_cs_billing_start" id="singlerate_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['singlerate_cs_billing_start'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="singlerate_cl1_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Single Rate + CL1 + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Single Rate + CL1 + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Two Rate</strong><br><span class="des">Peak with Controlled Load 1 and Climate Saver</span>
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
                                    <input type="radio" name="elec_meter_type" value="Time of Use" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_peak" id="timeofuse_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_peak'])) ? $step1['timeofuse_peak'] : '';?>"></div>
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
                                        <div class="col-sm-3 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cs_peak" id="timeofuse_cs_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs_peak'])) ? $step1['timeofuse_cs_peak'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_cs_offpeak" id="timeofuse_cs_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs_offpeak'])) ? $step1['timeofuse_cs_offpeak'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Climate Saver</span><input type="text" class="form-control" name="timeofuse_cs" id="timeofuse_cs" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cs'])) ? $step1['timeofuse_cs'] : '';?>"></div>
                                        <div class="col-sm-3 col-xs-12"><span class="des">Billing Start</span><input type="text" class="form-control" placeholder="" name="timeofuse_cs_billing_start" id="timeofuse_cs_billing_start" value="<?php echo (!empty($step1)) ? $step1['timeofuse_cs_billing_start'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_cl1_cs_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use + CL1 + Climate Saver" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use + CL1 + Climate Saver') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use</strong><br><span class="des">Peak with Off Peak, Controlled Load 1 and Climate Saver</span>
                                	</label>
                                    <div class="row radio-hidden">
                                        <div class="col-sm-3 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cl1_cs_peak" id="timeofuse_cl1_cs_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_cs_peak'])) ? $step1['timeofuse_cl1_cs_peak'] : '';?>"></div>
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
                                        <div class="col-sm-3 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cl1_peak" id="timeofuse_cl1_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl1_peak'])) ? $step1['timeofuse_cl1_peak'] : '';?>"></div>
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
                                        <div class="col-sm-3 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_cl2_peak" id="timeofuse_cl2_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_cl2_peak'])) ? $step1['timeofuse_cl2_peak'] : '';?>"></div>
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
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_tariff12_peak" id="timeofuse_tariff12_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_peak'])) ? $step1['timeofuse_tariff12_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_tariff12_offpeak" id="timeofuse_tariff12_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_offpeak'])) ? $step1['timeofuse_tariff12_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_tariff12_shoulder" id="timeofuse_tariff12_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff12_shoulder'])) ? $step1['timeofuse_tariff12_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="timeofuse_tariff13_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Time of Use (Tariff 13)" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Time of Use (Tariff 13)') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Time of Use (Tariff 13)</strong><br><span class="des">Peak with Off Peak and Shoulder</span>
                                	</label>
                                    <div class="row radio-hidden margin-top">
                                        <div class="col-sm-4 col-xs-12"><span class="des peak-des">Peak</span><input type="text" class="form-control" name="timeofuse_tariff13_peak" id="timeofuse_tariff13_peak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_peak'])) ? $step1['timeofuse_tariff13_peak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Off-Peak</span><input type="text" class="form-control" name="timeofuse_tariff13_offpeak" id="timeofuse_tariff13_offpeak" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_offpeak'])) ? $step1['timeofuse_tariff13_offpeak'] : '';?>"></div>
                                        <div class="col-sm-4 col-xs-12"><span class="des">Shoulder (if applicable)</span><input type="text" class="form-control" name="timeofuse_tariff13_shoulder" id="timeofuse_tariff13_shoulder" value="<?php echo (!empty($step1) && isset($step1['timeofuse_tariff13_shoulder'])) ? $step1['timeofuse_tariff13_shoulder'] : '';?>"></div>
                                    </div>  
                                </div>
                                <div class="radio" id="flexible_pricing_radio">
                                	<label>
                                    <input type="radio" name="elec_meter_type" value="Flexible Pricing" <?php echo (!empty($step1) && $step1['elec_meter_type'] == 'Flexible Pricing') ? 'checked="checked"' : '';?>><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span><strong>Flexible Pricing</strong><br><span class="des">Peak with Off Peak and Shoulder</span>
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
                    <div class="form-section col-sm-12 clearfix g-y g-n hidden-field"> 
                    	<h2>Gas Details</h2>
                    	<div class="g-y hidden-field">
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Who is your current gas supplier?</label>
                            <div class="col-sm-5">
                                <select class="form-control" name="gas_supplier" id="gas_supplier">
                                	<option value="">Please Select</option>
                                    <option value="ActewAGL" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
									<option value="AGL" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
									<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Alinta Energy') ? 'selected="selected"' : '';?>>Alinta Energy</option>
									<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
									<option value="Click Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Click Energy') ? 'selected="selected"' : '';?>>Click Energy</option>
									<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
									<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
									<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
									<option value="Momentum" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Momentum') ? 'selected="selected"' : '';?>>Momentum</option>
									<option value="Origin Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Origin Energy') ? 'selected="selected"' : '';?>>Origin Energy</option>
									<option value="Red Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Red Energy') ? 'selected="selected"' : '';?>>Red Energy</option>
									<option value="Simply Energy" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Simply Energy') ? 'selected="selected"' : '';?>>Simply Energy</option>
									<option value="Energy Australia" <?php echo (!empty($step1) && $step1['gas_supplier'] == 'Energy Australia') ? 'selected="selected"' : '';?>>Energy Australia</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>How many days are in the billing period?</label>
                            <div class="col-sm-2 col-xs-9">
                                <input type="text" class="form-control" placeholder="" name="gas_billing_days" id="gas_billing_days" value="<?php echo (!empty($step1)) ? $step1['gas_billing_days'] : '';?>">
                            </div>
                            <label class="control-label col-sm-1 col-xs-3">days</label>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>When did the bill start?</label>
                            <div class="col-sm-2 col-xs-9">
                                <input type="text" class="form-control" placeholder="" name="gas_billing_start" id="gas_billing_start" value="<?php echo (!empty($step1)) ? $step1['gas_billing_start'] : '';?>">
                            </div>
                    	</div>
                    	<div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Spend</label>
                            <div class="col-sm-2 col-xs-9">
                            	<div class="has-prefix">
                            	<div class="prefix">$</div>
                                <input type="text" class="form-control" placeholder="" name="gas_spend" id="gas_spend" value="<?php echo (!empty($step1)) ? $step1['gas_spend'] : '';?>">
                            	</div>
                            </div>
                    	</div>
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-sm-offset-1"><span class="info"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>What is the usage amount on your bill?</label>
                            <div class="col-sm-5">
                        	<div class="row">
                            	<div class="col-sm-6 col-xs-6"><div class="has-tail"><div class="des">Peak</div><div class="tail">MJ</div><input type="text" class="form-control" placeholder="" name="gas_peak" id="gas_peak" value="<?php echo (!empty($step1)) ? $step1['gas_peak'] : '';?>"></div></div>
                            	<div class="col-sm-6 col-xs-6"><div class="has-tail"><div class="des">Off-Peak (if shown)</div><div class="tail">MJ</div><input type="text" class="form-control" placeholder="" name="gas_off_peak" id="gas_off_peak" value="<?php echo (!empty($step1)) ? $step1['gas_off_peak'] : '';?>"></div></div>
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
								<option value="Click Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Click Energy') ? 'selected="selected"' : '';?>>Click Energy</option>
								<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
								<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
								<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
								<option value="Momentum" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Momentum') ? 'selected="selected"' : '';?>>Momentum</option>
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
                        <input type="hidden" name="gas_usage_level" value="<?php echo (!empty($step1)) ? $step1['gas_usage_level'] : '';?>" id="gas_usage_level">
                            <div class="col-sm-7">
                        	    <div id="gas_usage_level_buttons"></div>
                            </div>
						</div>
                    </div>
                    </div>
                    <?php if (false):?>
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group" id="business_name_field" style="display:none;">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title=""><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Business Name</label>
                        <div class="col-sm-5">
                            <input class="form-control" type="text" name="business_name" value="<?php echo (!empty($step1)) ? $step1['business_name'] : '';?>" id="business_name" placeholder="Business Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title=""><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Your Name</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="first_name" value="<?php echo (!empty($step1)) ? $step1['first_name'] : '';?>" id="first_name" placeholder="First Name">
                        </div>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="surname" value="<?php echo (!empty($step1)) ? $step1['surname'] : '';?>" id="surname" placeholder="Surname">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title=""><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Your Mobile Number</label>
                        <div class="col-sm-5">
                            <input class="form-control" type="tel" name="mobile" value="<?php echo (!empty($step1)) ? $step1['mobile'] : '';?>" id="mobile" placeholder="Mobile">
                            <input class="form-control hidden-field" type="tel" name="phone" value="<?php echo (!empty($step1)) ? $step1['phone'] : '';?>" id="phone" placeholder="Landline">
                            <div class="checkbox">
                            	<label><input type="checkbox" name="other_number" id="other_number" value="1" <?php if (!empty($step1) && $step1['other_number'] == 1):?>checked="checked"<?php endif;?>>Other Number</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title=""><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Your Email Address</label>
                        <div class="col-sm-5">
                            <input class="form-control" type="text" name="email" value="<?php echo (!empty($step1)) ? $step1['email'] : '';?>" id="email" placeholder="Email Address">
                        </div>
                    </div>
                    </div>
                    <?php endif;?>
                    
                    <div class="col-sm-12 clearfix">
                    <div class="form-group">
                        <div class="text-right" id="term1_field">
                            <div class="checkbox-simulate"><input type="checkbox" id="term1" value="1" name="term1" <?php if (!empty($step1) && $step1['term1'] == 1):?>checked="checked"<?php endif;?>><label for="term1" class="checkbox-simulate-bar"></label></div>
                            I have read, understood and accept the <a href="https://electricitywizard.com.au/terms-and-conditions" target="_blank">Terms and Conditions</a> & <a href="https://electricitywizard.com.au/terms-and-conditions" target="_blank">Terms and Conditions</a> and that Electricity Wizard recommends plans from a range of providers on its <a href="https://electricitywizard.com.au/preferred-partners-pp" target="_blank">Preferred Partners List</a>.
                        </div>
                    </div>
                    </div>
                    
                    <div class="col-sm-12 clearfix">
                    <div class="form-group" style="position:relative; clear:both;">
                    	<a href="https://electricitywizard.com.au" class="btn-grey pull-left">Back</a>
                        <a href="javascript:;" class="btn-orange pull-right continue">Continue</a>
                        <div id="step1_error_message"></div>
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