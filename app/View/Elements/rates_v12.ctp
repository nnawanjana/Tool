												
												<?php
    												echo '<p style="color:red;" align="center">CRITICAL VERBATIM</p>';
												    echo '<p style="" align="center">GST IS CURRENTLY APPLIED</p>';
													$total_discount_elec = 0;
													$total_discount_gas = 0;
													$has_discounts = false;
													if ($plan['Plan']['retailer'] == 'Powershop') {
														$total_discount_elec += $plan['Plan']['discount_guaranteed_elec'];
													}
													if ($discount_pay_on_time) {
														$total_discount_elec += $plan['Plan']['discount_pay_on_time_elec'];
														$total_discount_gas += $plan['Plan']['discount_pay_on_time_gas'];
														$has_discounts = true;
													}
													if ($discount_guaranteed) {
														if ($plan['Plan']['retailer'] != 'Powershop') {
															$total_discount_elec += $plan['Plan']['discount_guaranteed_elec'];
														}
														$total_discount_gas += $plan['Plan']['discount_guaranteed_gas'];
														$has_discounts = true;
													}
													if ($discount_direct_debit) {
														$total_discount_elec += $plan['Plan']['discount_direct_debit_elec'];
														$total_discount_gas += $plan['Plan']['discount_direct_debit_gas'];
														$has_discounts = true;
													}
													if ($discount_dual_fuel) {
														$total_discount_elec += $plan['Plan']['discount_dual_fuel_elec'];
														$total_discount_gas += $plan['Plan']['discount_dual_fuel_gas'];
														$has_discounts = true;
													}
													if ($discount_bonus_sumo) {
														$total_discount_elec += $plan['Plan']['discount_bonus_sumo'];
														$has_discounts = true;
													}
													if ($discount_prepay) {
														$total_discount_elec += $plan['Plan']['discount_prepay_elec'];
														$has_discounts = true;
													}
													if ($has_discounts) {
														echo '<p style="color:red;" align="center">I\'ll read you the rates inclusive of the discount. The rates displayed on your bill will not be inclusive of discount</p>';
													} else {
    													echo '<p style="" align="center">DISCOUNTS NOT APPLIED</p>';
													}
													?>
													<?php if ($rate_type == 'Elec' || $rate_type == 'Dual'):?>
													<?php if (empty($plan['Plan']['elec_rate'])):?>
													<p style="color:#ff9302;">No elec rate found</p>
													<?php else:?>
													<div class="development_mode"><strong>Elec:</strong> <?php echo $plan['Plan']['elec_rate']['distributor'];?></div>
													<div class="development_mode"><strong>Tariff:</strong> <?php if ($plan['Plan']['elec_rate']['tariff_class']) { echo $plan['Plan']['elec_rate']['tariff_class']; } else { echo $plan['Plan']['elec_rate']['tariff_type']; }?></div>
													<?php
    												$gst = 1;
												    if ($plan['Plan']['elec_rate']['gst_rates'] == 'No') {
    												    $gst = 1.1;
												    }
													$ratio = 1;
													switch ($plan['Plan']['elec_rate']['rate_tier_period']) {
														case '2':
															//$period = 'every 2 month';
															$period = '/qtr';
															$ratio = 1.5;
														break;
														case 'D':
															//$period = 'per day';
															$period = '/qtr';
															$ratio = 91.25;
														break;
														case 'M':
															//$period = 'per month';
															$period = '/qtr';
															$ratio = 3;
														break;
														case 'Q':
															$period = '/qtr';
														break;
														case 'Y':
															//$period = 'per year';
															$period = '/qtr';
															$ratio = 0.25;
														break;
													}
													$stp_ratio = 1;
                                                    switch ($plan['Plan']['elec_rate']['stp_period']) {
														case '2':
															$stp_period = 'every 2 month';
														break;
														case 'D':
															$stp_period = 'per day';
														break;
														case 'M':
															$stp_period = 'per month';
														break;
														case 'Q':
															$stp_period = 'per quarter';
														break;
														case 'Y':
															//$stp_period = 'per year';
															$stp_period = 'per day';
															$stp_ratio = 365;
														break;
													}
													$summer_winnter_rates = false;
													$tier_rates = array();
													if ($plan['Plan']['elec_rate']['peak_rate_1']) {
														if (strpos($plan['Plan']['elec_rate']['peak_rate_1'], '/') !== false) {
															$plan['Plan']['elec_rate']['peak_rate_1'] = explode('/', $plan['Plan']['elec_rate']['peak_rate_1']);
															$summer_winnter_rates = true;
														}
														$tier_rates[] = array('tier' => $plan['Plan']['elec_rate']['peak_tier_1'] * $ratio, 'rate' => $plan['Plan']['elec_rate']['peak_rate_1']);
													}
													if ($plan['Plan']['elec_rate']['peak_rate_2']) {
													    if (strpos($plan['Plan']['elec_rate']['peak_rate_2'], '/') !== false) {
															$plan['Plan']['elec_rate']['peak_rate_2'] = explode('/', $plan['Plan']['elec_rate']['peak_rate_2']);
														}
														$tier_rates[] = array('tier' => $plan['Plan']['elec_rate']['peak_tier_2'] * $ratio, 'rate' => $plan['Plan']['elec_rate']['peak_rate_2']);
													}
													if ($plan['Plan']['elec_rate']['peak_rate_3']) {
													    if (strpos($plan['Plan']['elec_rate']['peak_rate_3'], '/') !== false) {
															$plan['Plan']['elec_rate']['peak_rate_3'] = explode('/', $plan['Plan']['elec_rate']['peak_rate_3']);
														}
														$tier_rates[] = array('tier' => $plan['Plan']['elec_rate']['peak_tier_3'] * $ratio, 'rate' => $plan['Plan']['elec_rate']['peak_rate_3']);
													}
													if ($plan['Plan']['elec_rate']['peak_rate_4']) {
													    if (strpos($plan['Plan']['elec_rate']['peak_rate_4'], '/') !== false) {
															$plan['Plan']['elec_rate']['peak_rate_4'] = explode('/', $plan['Plan']['elec_rate']['peak_rate_4']);
														}
														$tier_rates[] = array('tier' => $plan['Plan']['elec_rate']['peak_tier_4'] * $ratio, 'rate' => $plan['Plan']['elec_rate']['peak_rate_4']);
													}
													if ($plan['Plan']['elec_rate']['peak_rate_5']) {
														if (strpos($plan['Plan']['elec_rate']['peak_rate_5'], '/') !== false) {
															$plan['Plan']['elec_rate']['peak_rate_5'] = explode('/', $plan['Plan']['elec_rate']['peak_rate_5']);
														}
														$tier_rates[] = array('tier' => 0, 'rate' => $plan['Plan']['elec_rate']['peak_rate_5']);
													}
													?>
													<?php if ($summer_winnter_rates):?>
													<strong>Summer Electricity Rates</strong>
                                                    <table>
                                                    <tr>
                                                        <td>Daily Supply Charge</td>
                                                        <?php
                                                        	if ($plan['Plan']['discount_applies']) {
																switch ($plan['Plan']['discount_applies']) {
																    case 'Usage':
																    	$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst, 3);
																    	break;
																    case 'Usage + STP + GST':
																    	$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst * (1 - $total_discount_elec / 100), 3);
																    	break;
																}
															}
															else {
																$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst, 3);
															}
                                                        ?>
                                                        <td><?php echo $stp_rate;?>c/day</td>
                                                    </tr>
                                                    <?php if (count($tier_rates) == 1):?>
                                                    <tr>
                                                        <td>Charge per kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 2):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 3):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 4):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[2]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[3]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 5):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[2]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[3]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[3]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[4]['rate'][0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    </table>
                                                    <strong>Winter Electricity Rates</strong>
                                                    <table>
                                                    <tr>
                                                        <td>Daily Supply Charge</td>
                                                        <?php
                                                        	if ($plan['Plan']['discount_applies']) {
																switch ($plan['Plan']['discount_applies']) {
																    case 'Usage':
																    	$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst, 3);
																    	break;
																    case 'Usage + STP + GST':
																    	$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst * (1 - $total_discount_elec / 100), 3);
																    	break;
																}
															}
															else {
																$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst, 3);
															}
                                                        ?>
                                                        <td><?php echo $stp_rate;?>c/day</td>
                                                    </tr>
                                                    <?php if (count($tier_rates) == 1):?>
                                                    <tr>
                                                        <td>Charge per kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 2):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 3):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 4):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[2]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[3]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 5):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[2]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[3]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[3]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[4]['rate'][1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    </table>
                                                    <?php else:?>
                                                    <?php if ($step1['elec_meter_type'] == 'Single Rate'):?>
													<strong>General Usage Electricity Rates</strong>
													<?php else:?>
													<strong>Peak Electricity Usage Rates</strong>
													<?php endif;?>
                                                    <table>
                                                    <tr>
                                                        <td>Daily Supply Charge</td>
                                                        <?php
                                                        	if ($plan['Plan']['discount_applies']) {
																switch ($plan['Plan']['discount_applies']) {
																    case 'Usage':
																    	$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst, 3);
																    	break;
																    case 'Usage + STP + GST':
																    	$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst * (1 - $total_discount_elec / 100), 3);
																    	break;
																}
															}
															else {
																$stp_rate = round($plan['Plan']['elec_rate']['stp'] / $stp_ratio * 100 * $gst, 3);
															}
                                                        ?>
                                                        <td><?php echo $stp_rate;?>c/day</td>
                                                    </tr>
                                                    <?php if (count($tier_rates) == 1):?>
                                                    <tr>
                                                        <td>Charge per kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 2):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 3):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 4):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[2]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[3]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($tier_rates) == 5):?>
                                                    <tr>
                                                        <td>First <?php echo round($tier_rates[0]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[1]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[2]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($tier_rates[3]['tier']);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[3]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($tier_rates[4]['rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    </table>
                                                    <?php endif;?>
                                                    <?php if ($plan['Plan']['elec_rate']['controlled_load_1_rate_1'] > 0 || $plan['Plan']['elec_rate']['controlled_load_2_rate'] > 0):?>
                                                    <strong>Controlled Load Electricity Rates</strong>
                                                    <table>
                                                    <?php if ($plan['Plan']['elec_rate']['controlled_load_1_rate_1']):?>
                                                    <?php if (!$plan['Plan']['elec_rate']['controlled_load_tier_1']):?>
                                                    <tr>
                                                        <td>Charge per kWh<?php echo $period;?></td>
                                                        <td><?php echo round($plan['Plan']['elec_rate']['controlled_load_1_rate_1'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php else:?>
                                                    <tr>
                                                        <td>First <?php echo round($plan['Plan']['elec_rate']['controlled_load_tier_1'] * $ratio);?> kWh<?php echo $period;?></td>
                                                        <td><?php echo round($plan['Plan']['elec_rate']['controlled_load_1_rate_1'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder kWh<?php echo $period;?></td>
                                                        <td><?php echo round($plan['Plan']['elec_rate']['controlled_load_1_rate_2'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php endif;?>
                                                    <?php if ($plan['Plan']['elec_rate']['controlled_load_2_rate']):?>
                                                    <tr>
                                                        <td>Charge per kWh<?php echo $period;?></td>
                                                        <td><?php echo round($plan['Plan']['elec_rate']['controlled_load_2_rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    </table>
                                                    <?php endif;?>
                                                    <?php if ($plan['Plan']['elec_rate']['shoulder_rate']):?>
                                                    <strong>Shoulder Electricity Rates</strong>
                                                    <table>
                                                    <tr>
                                                        <td>Charge per kWh<?php echo $period;?></td>
                                                        <td><?php echo round($plan['Plan']['elec_rate']['shoulder_rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    </table>
                                                    <?php endif;?>
                                                    <?php if ($plan['Plan']['elec_rate']['off_peak_rate']):?>
                                                    <strong>Off Peak Electricity Rates</strong>
                                                    <table>
                                                    <tr>
                                                        <td>Charge per kWh<?php echo $period;?></td>
                                                        <td><?php echo round($plan['Plan']['elec_rate']['off_peak_rate'] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    </table>
                                                    <?php endif;?>
                                                    <?php if ($plan['Plan']['elec_rate']['climate_saver_rate']):?>
                                                    <?php $climate_saver_rate_arr = explode('/', $plan['Plan']['elec_rate']['climate_saver_rate']);?>
                                                    <strong>Climate Saver Electricity Rates</strong>
                                                    <table>
                                                    <tr>
                                                        <td>Summer (Peak)</td>
                                                        <td><?php echo round($climate_saver_rate_arr[0] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Winter (Off Peak)</td>
                                                        <td><?php echo round($climate_saver_rate_arr[1] * 100 * $gst * (1 - $total_discount_elec / 100), 3);?>c/kwh</td>
                                                    </tr>
                                                    </table>
                                                    <?php endif;?>
                                                    <?php endif;?>
                                                    <?php if ($plan['Plan']['solar_rate']):?>
                                                    <strong>Solar</strong>
                                                    <table>
                                                    <?php if ($step1['looking_for'] != 'Move Properties' || !in_array($state, array('NSW', 'QLD'))):?>
                                                    <tr>
                                                        <td>Govt contribution per kWh</td>
                                                        <td><?php echo ($plan['Plan']['solar_rate']['government'] == '1 for 1') ? round($tier_rates[0]['rate'] * 100, 3) : $plan['Plan']['solar_rate']['government'];?>c/kwh</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <tr>
                                                        <td>Retailer contribution per kWh</td>
                                                        <td><?php echo ($plan['Plan']['solar_rate']['retailer'] == '1 for 1') ? round($tier_rates[0]['rate'] * 100, 3) : $plan['Plan']['solar_rate']['retailer'];?>c/kwh</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total contribution per kWh</td>
                                                        <td>
                                                        <?php
                                                        $govt_solar_rate = ($plan['Plan']['solar_rate']['government'] == '1 for 1') ? round($tier_rates[0]['rate'] * 100, 3) : $plan['Plan']['solar_rate']['government'];
                                                        if ($step1['looking_for'] == 'Move Properties' && in_array($state, array('NSW', 'QLD'))) {
                                                            $govt_solar_rate = 0;
                                                        }
                                                        $retailer_solar_rate = ($plan['Plan']['solar_rate']['retailer'] == '1 for 1') ? round($tier_rates[0]['rate'] * 100, 3) : $plan['Plan']['solar_rate']['retailer'];
                                                        echo ($govt_solar_rate + $retailer_solar_rate);
                                                        ?>c/kwh
                                                        </td>
                                                    </tr>
                                                    </table>
                                                    <strong>Solar Credit</strong>
                                                    <p>$<?php echo $step1['solar_generated'] * ($govt_solar_rate + $retailer_solar_rate) / 100;?></p>
                                                    <?php endif;?>
                                                    <?php endif;?>
                                                    <?php if ($rate_type == 'Gas' || $rate_type == 'Dual'):?>
                                                    <?php if (empty($plan['Plan']['gas_rate'])):?>
                                                    <p style="color:#ff9302;">No gas rate found</p>
                                                    <?php else:?>
                                                    <div class="development_mode"><strong>Gas:</strong> <?php echo $plan['Plan']['gas_rate']['distributor'];?></div>
                                                    <?php
                                                    $gst = 1;
												    if ($plan['Plan']['gas_rate']['gst_rates'] == 'No') {
    												    $gst = 1.1;
												    }
                                                    $ratio = 1;
													switch ($plan['Plan']['gas_rate']['rate_tier_period']) {
														case '2':
															if ($this->Session->read('User.state') == 'VIC') {
																$period = '/2 mth';
															}
															else {
																$period = '/qtr';
																$ratio = 1.5;
															}
														break;
														case 'D':
															if ($this->Session->read('User.state') == 'VIC') {
																$period = '/2 mth';
																$ratio = 60.83;
															}
															else {
																//$period = 'per day';
																$period = '/qtr';
																$ratio = 91.25;
															}
														break;
														case 'M':
															if ($this->Session->read('User.state') == 'VIC') {
																$period = '/2 mth';
																$ratio = 2;
															}
															else {
																//$period = 'per month';
																$period = '/qtr';
																$ratio = 3;
															}
														break;
														case 'Q':
															if ($this->Session->read('User.state') == 'VIC') {
																$period = '/2 mth';
																$ratio = 0.67;
															}
															else {
																$period = '/qtr';
															}
														break;
														case 'Y':
															if ($this->Session->read('User.state') == 'VIC') {
																$period = '/2 mth';
																$ratio = 0.17;
															}
															else {
																//$period = 'per year';
																$period = '/qtr';
																$ratio = 0.25;
															}
														break;
													}
													$stp_ratio = 1;
                                                    switch ($plan['Plan']['gas_rate']['stp_period']) {
														case '2':
															$stp_period = 'every 2 month';
														break;
														case 'D':
															$stp_period = 'per day';
														break;
														case 'M':
															$stp_period = 'per month';
														break;
														case 'Q':
															$stp_period = 'per quarter';
														break;
														case 'Y':
															//$stp_period = 'per year';
															$stp_period = 'per day';
															$stp_ratio = 365;
														break;
													}
													$peak_tier_rates = array();
													if ($plan['Plan']['gas_rate']['peak_rate_1']) {
														$peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['peak_tier_1'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['peak_rate_1'] / 100);
													}
													if ($plan['Plan']['gas_rate']['peak_rate_2']) {
														$peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['peak_tier_2'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['peak_rate_2'] / 100);
													}
													if ($plan['Plan']['gas_rate']['peak_rate_3']) {
														$peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['peak_tier_3'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['peak_rate_3'] / 100);
													}
													if ($plan['Plan']['gas_rate']['peak_rate_4']) {
														$peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['peak_tier_4'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['peak_rate_4'] / 100);
													}
													if ($plan['Plan']['gas_rate']['peak_rate_5']) {
														$peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['peak_tier_5'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['peak_rate_5'] / 100);
													}
													if ($plan['Plan']['gas_rate']['peak_rate_6']) {
														$peak_tier_rates[] = array('tier' => 0, 'rate' => $plan['Plan']['gas_rate']['peak_rate_6'] / 100);
													}
													$off_peak_tier_rates = array();
													if ($plan['Plan']['gas_rate']['off_peak_rate_1']) {
														$off_peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['off_peak_tier_1'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['off_peak_rate_1'] / 100);
													}
													if ($plan['Plan']['gas_rate']['off_peak_rate_2']) {
														$off_peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['off_peak_tier_2'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['off_peak_rate_2'] / 100);
													}
													if ($plan['Plan']['gas_rate']['off_peak_rate_3']) {
														$off_peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['off_peak_tier_3'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['off_peak_rate_3'] / 100);
													}
													if ($plan['Plan']['gas_rate']['off_peak_rate_4']) {
														$off_peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['off_peak_tier_4'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['off_peak_rate_4'] / 100);
													}
													if ($plan['Plan']['gas_rate']['off_peak_rate_5']) {
														$off_peak_tier_rates[] = array('tier' => $plan['Plan']['gas_rate']['off_peak_tier_5'] * $ratio, 'rate' => $plan['Plan']['gas_rate']['off_peak_rate_5'] / 100);
													}
													?>
                                                    <strong>Peak Gas Rates</strong>
                                                    <table>
                                                    <tr>
                                                        <td>Daily Supply Charge</td>
                                                        <?php
                                                            $discount_applies_gas = $plan['Plan']['discount_applies'];
                    										if ($plan['Plan']['discount_applies_gas']) {
                        										$discount_applies_gas = $plan['Plan']['discount_applies_gas'];
                    										}
                                                        	if ($discount_applies_gas) {
																switch ($discount_applies_gas) {
																    case 'Usage':
																    	$stp_rate = round($plan['Plan']['gas_rate']['stp'] / $stp_ratio * 100 * $gst, 3);
																    	break;
																    case 'Usage + STP + GST':
																    	$stp_rate = round($plan['Plan']['gas_rate']['stp'] / $stp_ratio * 100 * $gst * (1 - $total_discount_gas / 100), 3);
																    	break;
																}
															}
															else {
																$stp_rate = round($plan['Plan']['gas_rate']['stp'] / $stp_ratio * 100 * $gst, 3);
															}
                                                        ?>
                                                        <td><?php echo $stp_rate;?>c/day</td>
                                                    </tr>
                                                    <?php if (count($peak_tier_rates) == 1):?>
                                                    <tr>
                                                        <td>Charge per MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($peak_tier_rates) == 2):?>
                                                    <tr>
                                                        <td>First <?php echo round($peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($peak_tier_rates) == 3):?>
                                                    <tr>
                                                        <td>First <?php echo round($peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[1]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($peak_tier_rates) == 4):?>
                                                    <tr>
                                                        <td>First <?php echo round($peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[1]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[2]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[3]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($peak_tier_rates) == 5):?>
                                                    <tr>
                                                        <td>First <?php echo round($peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[1]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[2]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[3]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[3]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[4]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($peak_tier_rates) == 6):?>
                                                    <tr>
                                                        <td>First <?php echo round($peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[1]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[2]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[3]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[3]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($peak_tier_rates[4]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[4]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($peak_tier_rates[5]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    </table>
                                                    <?php if ($off_peak_tier_rates):?>
                                                    <strong>Off Peak Gas Rates</strong>
                                                    <table>
                                                    <?php if (count($off_peak_tier_rates) == 1):?>
                                                    <tr>
                                                        <td>Charge per MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($off_peak_tier_rates) == 2):?>
                                                    <tr>
                                                        <td>First <?php echo round($off_peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($off_peak_tier_rates) == 3):?>
                                                    <tr>
                                                        <td>First <?php echo round($off_peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($off_peak_tier_rates[1]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($off_peak_tier_rates) == 4):?>
                                                    <tr>
                                                        <td>First <?php echo round($off_peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($off_peak_tier_rates[1]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($off_peak_tier_rates[2]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[3]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    <?php if (count($off_peak_tier_rates) == 5):?>
                                                    <tr>
                                                        <td>First <?php echo round($off_peak_tier_rates[0]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[0]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($off_peak_tier_rates[1]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[1]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($off_peak_tier_rates[2]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[2]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Next <?php echo round($off_peak_tier_rates[3]['tier']);?> MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[3]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Remainder MJ<?php echo $period;?></td>
                                                        <td><?php echo round($off_peak_tier_rates[4]['rate'] * 100 * $gst * (1 - $total_discount_gas / 100), 3);?>c/mj</td>
                                                    </tr>
                                                    <?php endif;?>
                                                    </table>
                                                    <?php endif;?>
                                                    <?php endif;?>
                                                    <?php endif;?>