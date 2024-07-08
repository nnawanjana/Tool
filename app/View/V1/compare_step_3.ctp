
<div class="modal fade" id="confirmation_modal" tabindex="-1" role="dialog" aria-labelledby="confirmation_modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 id="confirmationModalLabel" class="modal-title">Thank you!</h4>
        </div>
        <div class="modal-body">
          <p>Thank you - we'll be giving you a call shortly. In the meantime, start comparing below</p>
        </div>
        <div class="modal-footer">
        	<a href="javascript:;" class="btn-orange" data-dismiss="modal">Start Comparing</a>
		</div>
      </div>
    </div>
</div>

<div class="modal fade" id="callmeback_modal" tabindex="-1" role="dialog" aria-labelledby="callmeback_modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-body clearfix">
			<form id="callmeback_form" class="step" onsubmit="return false;">
				<div style="display: none;" id="callmeback_modal_processing">
					<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
				</div>
				<input type="hidden" value="<?php echo $sid;?>" name="sid" id="sid">
				<h2>Register for a call back</h2>
				<p class="topline">Please fill your full name and phone number below and we'll return your enquiry ASAP.
				</p>
				<div class="form-horizontal">
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Hello, nice to meet you!"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Your Name</label>
                        <div class="col-sm-5">
                        	<div style="position:relative">
                            <input class="form-control" type="text" name="name" value="" id="call_name" placeholder="Your Name">
                        	</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="In case we need to reach you"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Your Mobile Number</label>
                        <div class="col-sm-5">
                        	<div style="position:relative">
                            <input class="form-control" type="tel" name="mobile" value="" id="call_mobile" placeholder="Mobile">
                            </div>
                            <div style="position:relative; margin-top: 5px;">
                            <input class="form-control hidden-field" type="tel" name="phone" value="" id="call_phone" placeholder="Landline">
                            </div>
                            <div class="checkbox" style="margin-top: 0px;">
                            	<label><input type="checkbox" name="other_number" id="call_other_number" value="1">Other Number</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="In case we need to reach you"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Your Email</label>
                        <div class="col-sm-5">
                        	<div style="position:relative">
                            <input class="form-control" type="email" name="email" value="" id="call_email" placeholder="Email">
                        	</div>
                        	<table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose Symantec SSL for secure e-commerce and confidential communications." align="right">
<tr>
<td width="135" align="right" valign="top"><script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=electricitywizard.com.au&amp;size=S&amp;use_flash=NO&amp;use_transparent=NO&amp;lang=en"></script></td>
</tr>
</table>
                        </div>
                    </div>
                    </div>
                                        
                    <div class="col-sm-12 clearfix">
                    <div class="form-group" style="position:relative; clear:both;">
                        <a href="javascript:;" class="btn-orange pull-right continue">Call Me Back</a>
                        <a class="btn-grey pull-left" href="javascript:;" data-dismiss="modal">Close</a>
                        <div id="callmeback_error_message"></div>
                    </div>
                    </div>
             </div>
			</form>
      </div>
    </div>
  </div>
</div>
<div id="step3" class="step clearfix">
                    <div class="plan_type_intro">
                    <h4>Did you Know?</h4>
                    <p>You might be able to save more money by comparing electricity and gas separately give it a go!</p>
                    </div>
					<div style="display: none;" id="processing">
						<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
					</div>
                	<div class="pagination-left"></div>
                    <div class="pagination-right"></div>
                	<div class="row">
                        <div class="col-sm-6 col-xs-12" style="padding-left:0;">
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
                        	?>
                            <h2>Your <?php echo $plan_type;?> Search Results</h2>
                            <p class="topline">You have told us that you live in <strong><?php echo $suburb;?>, <?php echo $postcode;?></strong> and you want to <strong><?php echo $step1['looking_for'];?></strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="plan-top">
                                <div class="plan-tool">
                                    <a class="plan-top-pocks" href="/<?php echo $this->params['controller'];?>/compare/3?view_top_picks=1#step3">View my top picks <span id="top_picks_count">(0)</span> <?php echo $this->Html->image('top-picks.png', array('alt' => ''));?></a>
                                    <a id="clear_my_top_picks" class="plan-clear" href="javascript:;">Clear</a>
                                    <a href="javascript:window.print();"><?php echo $this->Html->image('print.png', array('alt' => ''));?> Print</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $total = count($plans);?>
                    
                    <div class="row">
                    	<div class="col-sm-7 col-xs-12" style="padding-left:0;">
                        	<div class="note">Please note that the plans displayed on this page are sorted randomly. To get a comparison tailored for your needs, please contact our call centre</div>
                        </div>    
                        <div class="col-sm-5 col-xs-12">
                        	
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
                            <div class="plans-search">
                            	<?php if (!$view_top_picks):?>
                            	<div class="registerforcallback"><span class="call-back-icon"></span>Register for a call back</div>
                                <div class="plans-search-title">Filter Your Results</div>
                                <div class="plans-search-filters">
                                <form id="filter_results_form" action="#step3" method="post">
                                    <div class="filter-scroll">
                                    	<div class="form-item"><a href="/<?php echo $this->params['controller'];?>/compare/1">Start New Search</a></div>
                                        <div class="form-item"><a href="javascript:;" class="clear-filters">Clear All</a></div>
                                        <div class="form-item plan_type">
                                            <div class="form-label">
                                                <label>Comparison Type [3]</label>
                                            </div>
                                            <div class="form-element">
                                                <label><input type="radio" name="plan_type" class="filter_plan_type" value="Dual" <?php if ($filters['plan_type'] == 'Dual'):?>checked="checked"<?php endif;?>>Electricity & Gas Bundle</label>
                                                <label><input type="radio" name="plan_type" class="filter_plan_type" value="Elec" <?php if ($filters['plan_type'] == 'Elec'):?>checked="checked"<?php endif;?>>Electricity Only</label>
                                                <label><input type="radio" name="plan_type" class="filter_plan_type" value="Gas" <?php if ($filters['plan_type'] == 'Gas'):?>checked="checked"<?php endif;?>>Gas Only</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-item">
                                            <div class="form-label"><label>Supplier [<?php echo count($available_retailers);?>]</label></div>
                                            <div class="form-element">
                                                <label><input type="checkbox" name="retailer[]" id="retailer_all" value="all" <?php if (in_array('all', $filters['retailer'])):?>checked="checked"<?php endif;?>>All</label>
                                                <?php if (in_array('ActewAGL', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="ActewAGL" <?php if (in_array('ActewAGL', $filters['retailer'])):?>checked="checked"<?php endif;?>>ActewAGL</label>
                                                <?php endif;?>
                                                <?php if (in_array('AGL', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="AGL" <?php if (in_array('AGL', $filters['retailer'])):?>checked="checked"<?php endif;?>>AGL</label>
                                                <?php endif;?>
                                                <?php if (in_array('Dodo', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Dodo" <?php if (in_array('Dodo', $filters['retailer'])):?>checked="checked"<?php endif;?>>Dodo</label>
                                                <?php endif;?>
                                                <?php if (in_array('Lumo Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Lumo Energy" <?php if (in_array('Lumo Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Lumo</label>
                                                <?php endif;?>
                                                <?php if (in_array('Momentum', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Momentum" <?php if (in_array('Momentum', $filters['retailer'])):?>checked="checked"<?php endif;?>>Momentum</label>
                                                <?php endif;?>
                                                <?php if (in_array('Origin Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Origin Energy" <?php if (in_array('Origin Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Origin Energy</label>
                                                <?php endif;?>
                                                <?php if (in_array('Powerdirect', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Powerdirect" <?php if (in_array('Powerdirect', $filters['retailer'])):?>checked="checked"<?php endif;?>>Powerdirect</label>
                                                <?php endif;?>
                                                <?php if (in_array('Red Energy', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Red Energy" <?php if (in_array('Red Energy', $filters['retailer'])):?>checked="checked"<?php endif;?>>Red Energy</label>
                                                <?php endif;?>
                                                <?php if (in_array('Powershop', $available_retailers)):?>
                                                <label><input type="checkbox" name="retailer[]" class="retailer" value="Powershop" <?php if (in_array('Powershop', $filters['retailer'])):?>checked="checked"<?php endif;?>>Powershop</label>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                        
                                        <div class="form-item">
                                            <div class="form-label">
                                                <label>Discount Type [3]</label>
                                            </div>
                                            <div class="form-element">
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
                                        
                                        <div class="form-item">
                                            <div class="form-label"><label>Contract Length [<?php echo count($available_contract_length);?>]</label>
                                            </div>
                                            <div class="form-element">
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
                                            <div class="form-label"><label>Payment Options [<?php echo count($available_payment_options);?>]</label></div>
                                            <div class="form-element">
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
                                    <input type="hidden" name="show_filters" id="show_filters" value="<?php echo ($total > 0) ? 1 : 0;?>">
                                </form>
                                <div class="extra-title">Your Important Preferences</div>
                                <div class="extra"><span class="info" title="Secure your rates for the length of the contract"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-2.png">Rate freeze plans</div>
                                <div class="extra"><span class="info" title="No exit-fees, and the flexibility to change plans"><img src="/img/tooltips_mark.png"></span><img alt="" src="/img/img-step2-3.png">No fixed-term contracts</div>
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
                                <?php endif;?>
                            </div>
                        
                            <div class="plans">
                            	<div class="plans-inner">
                            	<?php if ($total > 0):?>
                            	<?php $i = 0;?>
                                <div class="plan-page row">
                                <?php foreach ($plans as $plan):?>
                                <?php $i++;?>
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
                                            <div class="plan-label">Discounts:</div>
                                            <h4><?php echo $plan['Plan']['discount_bonuses_description'];?></h4>
                                            <div class="call clearfix">Like this offer? Sign Up On<br /><span class="AVANSERnumber"><a href="tel:1300359779">1300 359 779</a></span><br /><span class="callmeback-link pull-right">... or register a call back</span></div>
                                            <div class="plan-label">Features:</div>
                                            <ul class="benefits">
                                            	<?php if ($plan['Plan']['benefit1_title']):?>
                                                <li><?php echo $this->Icon->add($plan['Plan']['benefit1_title'], false, true);?></li>
                                                <?php endif;?>
                                                <?php if ($plan['Plan']['benefit2_title']):?>
                                                <li><?php echo $this->Icon->add($plan['Plan']['benefit2_title'], false, true);?></li>
                                                <?php endif;?>
                                                <li><span class="clipboard"></span><span class="icon_text">Contract Length: <?php echo $plan['Plan']['contract_length'];?></span></li>
                                            </ul>
                                            <div class="extra-title view-details">View Plan Details &raquo;</div>
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
                                                <?php if ($plan['Plan']['discount_guaranteed_description'] || $plan['Plan']['discount_pay_on_time_description'] || $plan['Plan']['discount_direct_debit_description'] || $plan['Plan']['discount_credit_description']):?>
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
                                                    </ul>
                                                    <?php endif;?>
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
                                                            <h4>No fixed-term contracts</h4>
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
                                                    <p>ElectricityWizard doesn't display electricity or gas rates for our partners energy plans as we need to verify your meter type to ensure you are quoted accurate rates.</p>
                                                    <p>Our team is based in Australia and we'll answer your call in under 90 seconds.</p>
                                                    <p align="center"><img src="/img/rate.png"></p>
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
<style>
.plan_type_intro {
	position: absolute;
	left: -180px;
	width: 160px;
	border: 1px solid #09F;
	border-radius: 10px;
	padding: 15px;
}
</style>
<script type="text/javascript">
(function($){
	$(document).ready(function(e) {
		<?php if (!$conversion_tracked):?>
		
		$("#mynotification").topBar({
			slide: false
		});
		$.post("/<?php echo $this->params['controller'];?>/conversion_tracked",{conversion_tracked:1});
		
		<?php endif;?>
		
		var plan_type = $('.plan_type').offset().top, step3 = $('#step3').offset().top, top = plan_type - step3;
		$('.plan_type_intro').css('top',top);
	});
})(jQuery);
</script>