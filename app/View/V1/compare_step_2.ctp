<form id="step2_form">
				<div style="display: none;" id="processing">
					<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
				</div>
				<div id="step2" class="step clearfix">
                    <h2>Product Options</h2>
                    <p class="topline">Please let us know what features you are looking for in an energy plan. Hover over the question mark boxes for more detailed information.</p>
                    <div class="form-horizontal">
                    
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                    	<label class="control-label col-sm-5 col-xs-12 col-sm-offset-1">
						<span class="sign"><?php echo $this->Html->image('img-step2-1.png', array('alt' => ''));?></span>
                        <span class="info" title="Some providers have plans that will reward you for paying on time or by direct debit. If you intend to do this we will place these plans higher in your results list."><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>
                        <span class="title">Pay on time and/or direct debit discount plans</span></label>
                        <div class="col-sm-6 col-xs-12">
                        	<input type="hidden" name="conditional_discount" value="<?php echo (!empty($step2)) ? $step2['conditional_discount'] : 'No';?>">
                            <div class="radio-simulate style2 conditional-discount-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-5 col-xs-12 col-sm-offset-1">
						<span class="sign"><?php echo $this->Html->image('img-step2-2.png', array('alt' => ''));?></span>
                        <span class="info" title="A selection of our plans can lock in rates for the length of the contract."><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>
                        <span class="title">Rate freeze<br>
                        <span class="des">Secure your rates for the length of the contract</span></span>
                        </label>
                        <div class="col-sm-6 col-xs-12">
                        	<input type="hidden" name="rate_freeze" value="<?php echo (!empty($step2)) ? $step2['rate_freeze'] : 'No';?>">
                            <div class="radio-simulate style2 rate-freeze-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-5 col-xs-12 col-sm-offset-1">
						<span class="sign"><?php echo $this->Html->image('img-step2-3.png', array('alt' => ''));?></span>
                        <span class="info" title="Whilst fixed-term contracts can often offer higher discounts, no-fixed terms can give you flexibility to change plans in the short-term."><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>
                        <span class="title">No exit-fees<br>
                        <span class="des">Have the flexibility to change plans without exit fees</span></span></label>
                        <div class="col-sm-6 col-xs-12">
                        	<input type="hidden" name="no_contract_plan" value="<?php echo (!empty($step2)) ? $step2['no_contract_plan'] : 'No';?>">
                            <div class="radio-simulate style2 no-contract-plan-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-5 col-xs-12 col-sm-offset-1">
						<span class="sign"><?php echo $this->Html->image('img-step2-4.png', array('alt' => ''));?></span>
                        <span class="info" title="Some plans will enable you to pay your bills in instalments - making it easier to budget."><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>
                        <span class="title">Bill smoothing<br>
                        <span class="des">Based on average usage, pay your bills in smaller portions</span></span></label>
                        <div class="col-sm-6 col-xs-12">
                        	<input type="hidden" name="bill_smoothing" value="<?php echo (!empty($step2)) ? $step2['bill_smoothing'] : 'No';?>">
                            <div class="radio-simulate style2 bill-smoothing-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-5 col-xs-12 col-sm-offset-1">
						<span class="sign"><?php echo $this->Html->image('img-step2-5.png', array('alt' => ''));?></span>
                        <span class="info" title="Certain providers now give customers the option to view their account details from the comfort of their computer or mobile device."><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>
                        <span class="title">Manage accounts online<br>
                        <span class="des">View your account details from your computer or mobile device</span></span></label>
                        <div class="col-sm-6 col-xs-12">
                        	<input type="hidden" name="online_account_management" value="<?php echo (!empty($step2)) ? $step2['online_account_management'] : 'No';?>">
                            <div class="radio-simulate style2 online-account-management-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-5 col-xs-12 col-sm-offset-1">
						<span class="sign"><?php echo $this->Html->image('img-step2-6.png', array('alt' => ''));?></span>
                        <span class="info" title="This will give you the option of keeping an eye on your usage; knowing when you're using the most energy."><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>
                        <span class="title">Monitor your energy<br>
                        <span class="des">Keep an eye on your usage from your computer or mobile device</span></span></label>
                        <div class="col-sm-6 col-xs-12">
                        	<input type="hidden" name="energy_monitoring_tools" value="<?php echo (!empty($step2)) ? $step2['energy_monitoring_tools'] : 'No';?>">
                            <div class="radio-simulate style2 energy-monitoring-tools-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-5 col-xs-12 col-sm-offset-1">
						<span class="sign"><?php echo $this->Html->image('img-step2-7.png', array('alt' => ''));?></span>
                        <span class="info" title="Some providers can reward customers when paying your bills or choosing a certain plan."><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>
                        <span class="title">Membership rewards<br>
                        <span class="des">Be rewarded for choosing a certain plan</span></span></label>
                        <div class="col-sm-6 col-xs-12">
                        	<input type="hidden" name="membership_reward_programs" value="<?php echo (!empty($step2)) ? $step2['membership_reward_programs'] : 'No';?>">
                            <div class="radio-simulate style2 membership-reward-programs-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-5 col-xs-12 col-sm-offset-1">
						<span class="sign"><?php echo $this->Html->image('img-step2-8.png', array('alt' => ''));?></span>
                        <span class="info" title="These plans offer options for cleaner energy and offsetting carbon emissions."><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>
                        <span class="title">Reneweable energy plans<br>
                        <span class="des">Environmentally-friendly energy plans</span></span></label>
                        <div class="col-sm-6 col-xs-12">
                        	<input type="hidden" name="renewable_energy" value="<?php echo (!empty($step2)) ? $step2['renewable_energy'] : 'No';?>">
                            <div class="radio-simulate style2 renewable-energy-choices">
                            	<div class="bar"></div>
                                <div class="choice" id="Yes">Important</div>
                                <div class="choice" id="No">Not so important</div>
                            </div>
                        </div>
                    </div>
                    </div>
                    
                    <div class="col-sm-12 clearfix">
                    <div class="form-group">
                    	<a href="/<?php echo $this->params['controller'];?>/compare/1" class="btn-grey col-sm-3 pull-left">Back</a>
                        <a href="javascript:;" id="my_preferences" class="btn-orange continue pull-right">Continue</a>
                    </div>
                    <div class="form-group skip-this-step">
                    <a href="javascript:;" class="continue pull-right">Skip this step</a>
                    </div>
                    </div>
                    
                    </div>
				</div>
</form>
