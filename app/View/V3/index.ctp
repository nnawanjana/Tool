
		<form id="customer_details_form" onsubmit="return false;">
		        <input type="hidden" name="action" value="" id="action">
				<div style="display: none;" id="processing">
					<center><?php echo $this->Html->image('ajax-loader.gif', array('alt' => ''));?></center>
				</div>
				<div id="customer_details" class="step clearfix">
                    <h2>Customer Details</h2>
                    <div class="form-horizontal">
                    <div class="form-section col-sm-12 clearfix" style="display:none;">
				    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Contact Code"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Contact Code</label>
                        <div class="col-sm-3">
                            <select class="form-control" id="contact_code" name="contact_code">
                                <option value="">Please select</option>
                                <option value="EW" selected="selected">ElectricityWizard</option>
                                <option value="RC">RConcepts</option>
                            </select>
                        </div>
                    </div>
                    </div>
				    <div class="col-sm-12">
                        <div class="form-group" style="position:relative; clear:both;">
                    	    <a href="javascript:;" class="btn-orange pull-left outbound">Outbound</a>
                            <a href="javascript:;" class="btn-orange pull-right inbound">Inbound</a>
                        </div>
                    </div>
                    </div>
                    <div id="customer_details_field" class="form-horizontal" style="display:none;">
                    <div class="form-section col-sm-12 clearfix">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Witcom Lead"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Witcom Lead</label>
                        <div class="col-sm-3">
                            <select name="wtcm_business" id="wtcm_business" class="form-control">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="User"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>User</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="agent_name" value="" id="agent_name" placeholder="User">
                            <input type="hidden" name="agent_id" value="" id="agent_id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Name"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Customer's Name</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="first_name" value="" id="first_name" placeholder="First Name">
                        </div>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="surname" value="" id="surname" placeholder="Surname">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Mobile Number"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Mobile Number</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="tel" name="mobile" value="" id="mobile" placeholder="Mobile Number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Home Phone"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Home Phone</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="tel" name="home_phone" value="" id="home_phone" placeholder="Home Phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Customer's Work Number"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Work Number</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="tel" name="work_number" value="" id="work_number" placeholder="Work Number">
                        </div>
                    </div>
                    </div>
                    
                    <div class="col-sm-12 clearfix">
                    <div class="form-group" style="position:relative; clear:both;">
                    	<a href="javascript:;" class="btn-grey pull-left no-sale">No Sale</a>
                        <a href="javascript:;" class="btn-orange pull-right comparison">Comparison</a>
                        <div class="clearfix"></div>
                        <a href="javascript:;" class="pull-right create-lead">or create Lead ID</a>
                    </div>
                    
                    </div>
                    
                    <div class="form-section col-sm-12 clearfix" id="no_sale_section" style="display:none;">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="No Sale"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>No Sale</label>
                        <div class="col-sm-3">
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
                            <a href="javascript:;" class="btn-orange pull-left no-sale-ok">OK</a>
                        </div>
                    </div> 
                    </div>
                    
                    <div class="form-section col-sm-12 clearfix" id="lead_section" style="display:none;">
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Lead ID"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Lead ID</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="sid" value="<?php echo $sid;?>" id="sid" placeholder="Lead ID">
                        </div>
                        <div class="col-sm-3">
                            <button class="btn pull-left clipboard" data-clipboard-target="#sid">Copy to clipboard</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4 col-sm-offset-1"><span class="info" title="Comparison"><?php echo $this->Html->image('img-question.png', array('alt' => ''));?></span>Comparison</label>
                        <div class="col-sm-3">
                            <a href="/v4/compare/1" class="btn-orange pull-left comparison-continue">Continue</a>
                            <a href="/v4/" class="pull-left">or start a new comparison</a>
                        </div>
                    </div> 
                    </div>
                    
                    </div>
                </div>
        </form>
