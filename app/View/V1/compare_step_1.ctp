
<form id="step1_form">
	<input type="hidden" name="sid" value="<?php echo $sid;?>" id="sid">
				<div style="display: none;" id="processing">
					<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
				</div>
				<div id="step1" class="step clearfix">
                    <h2>About You</h2>
                    <?php if ($postcode && $suburb):?>
                    <p class="topline">You have told us that you live in <strong><?php echo $suburb;?>, <?php echo $postcode;?></strong>. We just need some details in order to find a great plan for you.</p>
                    <?php endif;?>
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
                            <input type="hidden" name="state" value="" id="state">
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
                    </div>
                    </div>
                    
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="What kind of property is this?"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Select your comparison type</label>
                        <div class="col-sm-5">
                            <input type="hidden" name="customer_type" value="<?php echo (!empty($step1)) ? $step1['customer_type'] : '';?>" id="customer_type">
                            <div id="RES" class="customer-type customer-type-r"></div>
                            <div id="SME" class="customer-type customer-type-b"></div>
                        </div>
                    </div>
                    </div>
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Is this for an existing property, or are you needing a connection for a new one?"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>What are you looking to do?</label>
                        <div class="col-sm-5">
                            <select class="form-control" name="looking_for" id="looking_for">
                            	<option value="">Please Select</option>
                            	<option value="Compare Plans" <?php echo (!empty($step1) && $step1['looking_for'] == 'Compare Plans') ? 'selected="selected"' : '';?>>Compare Plans</option>
								<option value="Move Properties" <?php echo (!empty($step1) && $step1['looking_for'] == 'Move Properties') ? 'selected="selected"' : '';?>>Move Properties</option>
                            </select>
                        </div>
                    </div>
                    </div>
                        
                    <div class="form-section col-sm-12 clearfix eg-n g-n e-n hidden-field"> 
                    <div class="form-group eg-n e-n hidden-field">
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
								<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['elec_supplier2'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group eg-n g-n hidden-field">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="If you're unsure, click Unsure/Other"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Who is your current gas supplier?</label>
                        <div class="col-sm-5">
                            <select class="form-control" name="gas_supplier2" id="gas_supplier2">
                            	<option value="">Please Select</option>
                            	<option value="ActewAGL" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'ActewAGL') ? 'selected="selected"' : '';?>>ActewAGL</option>
								<option value="AGL" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'AGL') ? 'selected="selected"' : '';?>>AGL</option>
								<option value="Alinta Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Alinta') ? 'selected="selected"' : '';?>>Alinta Energy</option>
								<option value="Australian Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Australian Power & Gas') ? 'selected="selected"' : '';?>>Australian Power & Gas</option>
								<option value="Dodo Power & Gas" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Dodo Power & Gas') ? 'selected="selected"' : '';?>>Dodo Power & Gas</option>
								<option value="Energy Australia (TRUenergy)" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Energy Australia (TRUenergy)') ? 'selected="selected"' : '';?>>Energy Australia (TRUenergy)</option>
								<option value="Lumo Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Lumo Energy') ? 'selected="selected"' : '';?>>Lumo Energy</option>
								<option value="Origin Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Origin Energy') ? 'selected="selected"' : '';?>>Origin Energy</option>
								<option value="Red Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Red Energy') ? 'selected="selected"' : '';?>>Red Energy</option>
								<option value="Simply Energy" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Simply Energy') ? 'selected="selected"' : '';?>>Simply Energy</option>
								<option value="Unsure/Other" <?php echo (!empty($step1) && $step1['gas_supplier2'] == 'Unsure/Other') ? 'selected="selected"' : '';?>>Unsure/Other</option>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="col-sm-12 clearfix">
                    <div class="form-group">
                        <div class="text-right" id="term1_field">
                            <div class="checkbox-simulate"><input type="checkbox" id="term1" value="1" name="term1" <?php if (!empty($step1) && $step1['term1'] == 1):?>checked="checked"<?php endif;?>><label for="term1" class="checkbox-simulate-bar"></label></div>
                            I understand that Electricity Wizard recommends plans from a range of providers on its <a onclick="window.open('http://electricitywizard.com.au/preferred-partners-pp','windowname1','width=600, height=600'); return false;" href="javascript:;">Preferred Partners List</a>.
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="text-right" id="term2_field">
                            <div class="checkbox-simulate"><input type="checkbox" id="term2" value="1" name="term2" <?php if (!empty($step1) && $step1['term2'] == 1):?>checked="checked"<?php endif;?>><label for="term2" class="checkbox-simulate-bar"></label></div>
                            I have read, understood and accept the <a onclick="window.open('http://electricitywizard.com.au/terms-and-conditions','windowname1','width=600, height=600'); return false;" href="javascript:;">Terms and Conditions</a> & <a onclick="window.open('http://electricitywizard.com.au/privacy-statement','windowname1','width=600, height=600'); return false;" href="javascript:;">Privacy Policy</a>.
                            
                        </div>
                    </div>
                    </div>
                    
                    <div class="col-sm-12 clearfix">
                    <div class="form-group" style="position:relative; clear:both;">
                    	<a href="http://electricitywizard.com.au" class="btn-grey pull-left">Back</a>
                        <a href="javascript:;" class="btn-orange pull-right continue">Continue</a>
                        <div id="step1_error_message"></div>
                    </div>
                    </div>
             </div>
        </div>
</form>

<?php if ($sid && !$conversion_tracked):?>
<div class="hidden">
	
<!-- Google Code for Web Form Lead Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 966780622;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "gUMkCPO9z1kQzs3_zAM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/966780622/?label=gUMkCPO9z1kQzs3_zAM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<SCRIPT language="JavaScript" type="text/javascript">
window.ysm_customData = new Object();
window.ysm_customData.conversion = "transId=,currency=,amount=";
var ysm_accountid  = "1V53AB3BB9R52PQKH70P1SJQUQK";
document.write("<SCR" + "IPT language='JavaScript' type='text/javascript' "
+ "SRC=//" + "srv3.wa.marketingsolutions.yahoo.com" + "/script/ScriptServlet" + "?aid=" + ysm_accountid
+ "></SCR" + "IPT>");
</SCRIPT>

<!-- Offer Conversion: cpl -->
<iframe src="http://tracking.tdgmedia.com.au/SL6Y" scrolling="no" frameborder="0" width="1" height="1"></iframe>
<!-- // End Offer Conversion -->

<!-- Empowered Communications Tracking Beacon -->
<!Transaction Beacon!>
<img src="http://campaigns.empoweredcomms.com.au/ml/imgs/com/3e76c6MTEzNDg6Y29tOnBybw/blank.gif" height="1" width="1" alt="">

<!-- Offer Conversion: Electricity Wizard -->

<iframe src="http://tracking.offerfactory.com.au/SL4C?adv_sub=<?php if (isset($sid)) { echo (int)$sid; }?>" scrolling="no" frameborder="0" width="1" height="1"></iframe>

<!-- // End Offer Conversion -->

<!-- Offer Conversion: Electricity Wizard - Free $50 Wine Voucher -->
<iframe src="http://tracking.offerfactory.com.au/SLCP?adv_sub=<?php if (isset($sid)) { echo (int)$sid; }?>" scrolling="no" frameborder="0" width="1" height="1"></iframe>
<!-- // End Offer Conversion -->

<script src="http://connect.zoomdirect.com.au/lead_third/14321/OPTIONAL_INFORMATION"></script>
<noscript><img src="http://connect.zoomdirect.com.au/track_lead/14321/OPTIONAL_INFORMATION"></noscript>

<!-- Offer Goal Conversion: LP CPL conversion -->
<iframe src="http://tracking.jackmedia.com.au/GL1jI" scrolling="no" frameborder="0" width="1" height="1"></iframe>
<!-- // End Offer Goal Conversion -->

<!-- Offer Conversion: Electricity Wizard CPL -->
<iframe src="http://tracking.jackmedia.com.au/SL1k4" scrolling="no" frameborder="0" width="1" height="1"></iframe>
<!-- // End Offer Conversion -->

</div>
<?php endif;?>
