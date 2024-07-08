<form id="step2_form" class="v4_form">
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
				<div style="display: none;" id="processing">
					<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
				</div>
				<div id="step2" class="step clearfix">
					<a href="/v4/" class="step_close">X</a>
					<div class="step-header">
                    <h2 class=" mb2">Product Options</h2>
                    <p>Please let us know what features you are looking for in an energy plan. Hover over the question mark boxes for more detailed information.</p>
					</div>
                    <div class="clearfix"></div>
                    
                    <div class="form-horizontal">
                    
                    <div class="col-sm-12 clearfix" style="padding-top:15px;">
                    <div class="form-group">
                    	<input type="hidden" name="sort_by" id="sort_by" value="lowest_price">
                    	<div class="sort_by_select">
                    	<span>Sort by</span>
						<select name="sort_by_select">
							<option value="my_preferences">My preferences</option>
							<option value="lowest_price">Lowest price</option>
						</select>	
                        <?php /*?><a href="javascript:;" id="my_preferences" class="btn-orange continue">My preferences</a>
                        <a href="javascript:;" id="lowest_price" class="btn-orange continue">Lowest price</a><?php */?>
                    	</div>
                    </div>
                    </div>
                    
                    <div class="form-section col-sm-12 clearfix border-none">
	                <div class="form-group has-bb" style="display:none;"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="These plans offer options for cleaner energy and offsetting carbon emissions."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>
                        <span class="title" style="color:red; font-weight: bold;">Reneweable energy plans<br>
                        <span class="des" style="color:red;">Environmentally-friendly energy plans</span></span></label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="renewable_energy" value="<?php echo (!empty($step2)) ? $step2['renewable_energy'] : 'Yes';?>">
                            <div class="radio-simulate style2 renewable-energy-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">	
                        <span class="info" title="Some providers have plans that will reward you for paying on time. If you intend to do this we will place these plans higher in your results list."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Include pay on time discounts</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="pay_on_time_discount" value="<?php echo (!empty($step2)) ? $step2['pay_on_time_discount'] : 'No';?>">
                            <div class="radio-simulate style2 pay-on-time-discount-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Yes</div>
                                <div class="choice" id="No">No</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="Some providers have plans that will reward you for paying by direct debit. If you intend to do this we will place these plans higher in your results list."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Include direct debit discounts</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="direct_debit_discount" value="<?php echo (!empty($step2)) ? $step2['direct_debit_discount'] : 'No';?>">
                            <div class="radio-simulate style2 direct-debit-discount-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Yes</div>
                                <div class="choice" id="No">No</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="If you bundle up your electricity and gas together, some plans offer an additional discount."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Include dual fuel discounts</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="dual_fuel_discount" value="<?php echo (!empty($step2)) ? $step2['dual_fuel_discount'] : 'No';?>">
                            <div class="radio-simulate style2 dual-fuel-discount-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Yes</div>
                                <div class="choice" id="No">No</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="Some plans offer an additional discount for the first 12 months of the plan."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Include bonus discounts</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="bonus_discount" value="<?php echo (!empty($step2)) ? $step2['bonus_discount'] : 'No';?>">
                            <div class="radio-simulate style2 bonus-discount-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Yes</div>
                                <div class="choice" id="No">No</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="By prepaying some of your energy bill, some plans offer an additional discount."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Include prepay discounts</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="prepay_discount" value="<?php echo (!empty($step2)) ? $step2['prepay_discount'] : 'No';?>">
                            <div class="radio-simulate style2 prepay-discount-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Yes</div>
                                <div class="choice" id="No">No</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb" style="display:none;"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="A selection of our plans can lock in rates for the length of the contract."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>
                        Rate freeze<br>Secure your rates for the length of the contract</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="rate_freeze" value="<?php echo (!empty($step2)) ? $step2['rate_freeze'] : 'No';?>">
                            <div class="radio-simulate style2 rate-freeze-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb" style="display:none;"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="Whilst fixed-term contracts can often offer higher discounts, no-fixed terms can give you flexibility to change plans in the short-term."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>No exit-fees<br>Have the flexibility to change plans without exit fees</label>
                        </div>
						<div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="no_contract_plan" value="<?php echo (!empty($step2)) ? $step2['no_contract_plan'] : 'No';?>">
                            <div class="radio-simulate style2 no-contract-plan-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb" style="display:none;"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="Some plans will enable you to pay your bills in instalments - making it easier to budget."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Bill smoothing<br>Based on average usage, pay your bills in smaller portions</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="bill_smoothing" value="<?php echo (!empty($step2)) ? $step2['bill_smoothing'] : 'No';?>">
                            <div class="radio-simulate style2 bill-smoothing-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb" style="display:none;"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="Certain providers now give customers the option to view their account details from the comfort of their computer or mobile device."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Manage accounts online<br>View your account details from your computer or mobile device</label>
                        </div>
						<div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="online_account_management" value="<?php echo (!empty($step2)) ? $step2['online_account_management'] : 'No';?>">
                            <div class="radio-simulate style2 online-account-management-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb" style="display:none;"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="This will give you the option of keeping an eye on your usage; knowing when you're using the most energy."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Monitor your energy<br>Keep an eye on your usage from your computer or mobile device</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="energy_monitoring_tools" value="<?php echo (!empty($step2)) ? $step2['energy_monitoring_tools'] : 'No';?>">
                            <div class="radio-simulate style2 energy-monitoring-tools-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div></div>
                    <div class="form-group has-bb" style="display:none;"><div class="row d-flex align-items-center">
                    	<div class="col-sm-7 col-xs-12">
						<label class="control-label v4_label v2">
                        <span class="info" title="Some providers can reward customers when paying your bills or choosing a certain plan."><?php echo $this->Html->image('v4/question.png', array('alt' => ''));?></span>Membership rewards<br>Be rewarded for choosing a certain plan</label>
						</div>	
                        <div class="col-sm-5 col-xs-12">
                        	<input type="hidden" name="membership_reward_programs" value="<?php echo (!empty($step2)) ? $step2['membership_reward_programs'] : 'No';?>">
                            <div class="radio-simulate style2 membership-reward-programs-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    </div>
					<div class="form-group d-flex align-items-center justify-content-between">	
					    <a href="/<?php echo $this->params['controller'];?>/compare/1" class="btn-blue">Back</a>	
                    	<a href="javascript:;" class="btn-blue continue">Comparison</a>	
                    </div>
				</div>
		</div>
	</div>
</form>