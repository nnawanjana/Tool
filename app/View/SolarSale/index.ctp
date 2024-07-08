
		<form id="customer_details_form" onsubmit="return false;">
		        <input type="hidden" name="action" value="" id="action">
				<div style="display: none;" id="processing">
					<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
				</div>
				<div id="customer_details" class="step clearfix">
				    
                    <div id="customer_details_field" class="form-horizontal">
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Lead ID (if applicable)"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Lead ID</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="sid" value="<?php echo $sid;?>" id="sid2" placeholder="Lead ID">
                            <input type="hidden" name="campaign_id" value="" id="campaign_id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Referrer Name"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Referrer Name</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="referring_agent" value="" id="referring_agent" placeholder="Referrer Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Name"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Customer's Name</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="first_name" value="" id="first_name" placeholder="First Name" readonly="readonly">
                        </div>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="surname" value="" id="surname" placeholder="Surname" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Mobile Number"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Mobile Number</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="tel" name="mobile" value="" id="mobile" placeholder="Mobile Number" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Home Phone"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Home Phone</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="tel" name="home_phone" value="" id="home_phone" placeholder="Home Phone" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Work Number"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Work Number</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="tel" name="work_number" value="" id="work_number" placeholder="Work Number" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Postcode"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Postcode</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="number" name="postcode" value="" id="postcode" placeholder="Postcode" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Sales Rep Name"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Sales Rep Name</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="sales_rep_name" value="" id="sales_rep_name" placeholder="Sales Rep Name" readonly="readonly">
                        </div>
                    </div>
                    </div>
                    
                    <div class="col-sm-12 clearfix">
                    <div class="form-group" style="position:relative; clear:both;">
                        <a href="javascript:;" class="btn-orange pull-left solar_interest" style="font-size: 15px;">Solar Interest</a>
                        <a href="javascript:;" class="btn-orange pull-left solar_appointment" style="font-size: 15px; margin-left: 22%;">Appointment Confirmed</a>
                        <a href="javascript:;" class="btn-orange pull-right solar_sale_confirmed" style="font-size: 15px;">Sale Confirmed</a>
                        <div class="clearfix"></div>
                    </div>
                    </div>

                    </div>
                </div>
        </form>
