<div class="container">
    <div class="row"><div class="col-sm-6">
            <form id="create_lead_form" class="validate-form" action="" method="POST">
				<h4>Create Lead</h4>
				
				<div class="form-group">
                    <label>Sales Rep Name</label>
                    <input class="form-control" type="text" id="agent_name" name="agent_name" placeholder="Enter sales rep name" required>
                    <input type="hidden" name="agent_email" id="agent_email" value="">
                    <input type="hidden" name="agent_id" id="agent_id" value="">
                </div>
                
                <div class="form-group">
                    <label>Plan Name</label>
                    <input class="form-control" type="text" name="plan_name" placeholder="Enter plan name" required>
                </div>
                
                <div class="form-group">
                    <label>Plan Discounts</label>
                    <input class="form-control" type="text" name="plan_discounts" placeholder="Enter plan discounts" required>
                </div>
                
                <div class="form-group">
                    <label>Solar</label>
                    <p>
                    <input type="radio" name="solar" value="1" required> Yes
                    <input type="radio" name="solar" value="0" required> No
                    </p>
                </div>

                <div class="form-group" id="service_required_field">
                    <label>Fuel</label>
                    <select class="form-control" name="fuel" id="fuel" tabindex="-1" title="" required>
                        <option value="">Select an Option</option>
                        <option value="Dual">Electricity &amp; Gas</option>
                        <option value="Elec">Electricity Only</option>
                        <option value="Gas">Gas Only</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Customer Type</label>
                    <select class="form-control" id="customer_type" name="customer_type" tabindex="-1" title=""  required>
                        <option value="">Select an Option</option>
                        <option value="Residential">Residential</option>
                        <option value="Business">Business</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Title</label>
                    <select class="form-control" name="title" tabindex="-1" title=""  required>
                        <option value="">None</option>
                        <option value="Mr.">Mr.</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Dr.">Dr.</option>
                        <option value="Prof.">Prof.</option>
                        <option value="MS">MS</option>
                        <option value="MRS">MRS</option>
                        <option value="MR">MR</option>
                        <option value="MISS">MISS</option>
                        <option value="DR">DR</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>First Name</label>
                    <input class="form-control" type="text" name="firstname" placeholder="Enter your firstname" required>
                </div>

                <div class="form-group" data-validate="LastName is required">
                    <label>Last Name</label>
                    <input class="form-control" type="text" name="lastname" placeholder="Enter your lastname" required>
                </div>
                
                <div class="form-group">
                    <label>Mobile Phone</label>
                    <input class="form-control" type="text" name="mobile" placeholder="Enter your mobile phone" required>
                </div>
                
                <div class="form-group">
                    <label>Home Phone</label>
                    <input class="form-control" type="text" name="home_phone" placeholder="Enter your home phone">
                </div>
                
                <div class="form-group">
                    <label>Work No.</label>
                    <input class="form-control" type="text" name="work_number" placeholder="Enter your Work No.">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email" placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob">
                </div>
                
                <div class="form-group nmi_field" style="display: none;">
                    <label>NMI</label>
                    <input class="form-control" type="text" name="nmi" placeholder="Enter your NMI">
                </div>

                <div class="form-group mirn_field" style="display: none;">
                    <label>MIRN</label>
                    <input class="form-control" type="text" name="mirn" placeholder="Enter your MIRN">
                </div>

                <div class="form-group" id="abn_field" style="display: none;">
                    <label>ABN</label>
                    <input class="form-control" type="text" name="abn" placeholder="Enter your ABN">
                </div>

                <div class="form-group">
                    <label>Connection Type</label>
                    <select class="form-control" name="connection_type" tabindex="-1" title=""  required>
                        <option value="">Select an Option</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Move In">Move In</option>
                    </select>
                </div>

                <div class="form-group" id="connection_date_field" style="display: none;">
                    <label>Connection Date</label>
                    <input type="date" name="connection_date">
                </div>

                <div class="form-group">
                    <label>Preferred Contact Method</label>
                    <select class="form-control" name="prefered_contact_method" tabindex="-1" title=""  required>
                        <option value="">Select an Option</option>
                        <option value="Home">Home</option>
                        <option value="Work">Work</option>
                        <option value="Mobile">Mobile</option>
                        <option value="EMAIL">EMAIL</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Bill Delivery</label>
                    <select class="form-control" name="bill_delivery" tabindex="-1" title=""  required>
                        <option value="">Select an Option</option>
                        <option value="POST">POST</option>
                        <option value="EMAIL">EMAIL</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Communications Delivery</label>
                    <select class="form-control" name="communications_delivery" tabindex="-1" title=""  required>
                        <option value="">Select an Option</option>
                        <option value="POST">POST</option>
                        <option value="EMAIL">EMAIL</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Identification</label>
                    <p>
                    <input type="radio" name="has_identification" value="1" class="has_identification" required> Yes
                    <input type="radio" name="has_identification" value="0" class="has_identification" required> No
                    </p>
                </div>
                <div class="form-group identification_field" style="display: none;">
                    <label>Document Type</label>
                    <select class="form-control" id="identification_document_type" name="identification_document_type]">
                        <option value=''>Select</option>
                        <option value='DRV'>Driver's License</option>
                        <option value='MED'>Medicare Card</option>
                        <option value='PP'>Passport</option>
                    </select>
                </div>
                <div class="form-group identification_field" style="display: none;">
                    <label>Document ID Number*</label>
                    <input class="form-control" id="identification_document_id" type="text" value='' name="identification_document_id"/>
                </div>
                <div class="form-group identification_field" style="display: none;">
                    <label>Document Expiry*</label>
                    <input type="date" name="identification_document_expiry">
                </div>
                <div class="form-group identification_field" style="display: none;">
                    <label>Document State</label>
                    <select class="form-control" name="identification_document_state">
                        <option selected="selected" value="">- Select -</option>
                        <option value="QLD">QLD</option>
                        <option value="VIC">VIC</option>
                        <option value="NSW">NSW</option>
                        <option value="SA">SA</option>
                        <option value="ACT">ACT</option>
                    </select>
                </div>
                <div class="form-group identification_field" style="display: none;">
                    <label>Document State</label>
                    <select class="form-control" name="identification_document_country">
                        <option>- Select -</option>
                        <option value='Australia' selected="selected">Australia</option>
                        <option value='New Zealand'>New Zealand</option>
                        <option value='Other Country'>Other Country</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Life Support</label>
                    <p>
                    <input type="radio" name="life_support" value="1" class="life_support" required> Yes
                    <input type="radio" name="life_support" value="0" class="life_support" required> No
                    </p>
                </div>
                
                <div class="form-group life_support_field" style="display: none;">
                    <label>What is the machine type?</label>
                    <select class="form-control" id="life_support_machine_type" name="life_support_machine_type">
                        <option>- Select -</option>
                        <option value='PAP / CPAP short for Positive Airway Pressure (Including 24 hour usage)'>PAP / CPAP short for Positive Airway Pressure (Including 24 hour usage)</option>
                        <option value="Ventilator For Life support (including Polio) (formerly known as 'respirator' or 'iron lung')">Ventilator For Life support (including Polio) (formerly known as 'respirator' or 'iron lung')</option>
                        <option value='Oxygen concentrators (Including 24 hour usage)'>Oxygen concentrators (Including 24 hour usage)</option>
                        <option value='Medical heating and cooling'>Medical heating and cooling</option>
                        <option value='Nebuliser (Including Ventolin)'>Nebuliser (Including Ventolin)</option>
                        <option value='Home haemodialysis'>Home haemodialysis</option>
                        <option value='Peritoneal dialysis'>Peritoneal dialysis</option>
                        <option value='Kidney dialysis machine'>Kidney dialysis machine</option>
                        <option value='Left ventricular assist device'>Left ventricular assist device</option>
                        <option value='Phototherapy equipment (Crigler Najjar)'>Phototherapy equipment (Crigler Najjar)</option>
                        <option value='Total Parental Nutrition (TPN)'>Total Parental Nutrition (TPN)</option>
                        <option value='Enteral feeding pump'>Enteral feeding pump</option>
                        <option value='Long stay life support'>Long stay life support</option>
                        <option value='Other Apparatus Not listed'>Other Apparatus Not listed</option>
                        <option value='A. An oxygen concentrator'>A. An oxygen concentrator</option>
                        <option value="B. An intermittent peritoneal dialysis machine">B. An intermittent peritoneal dialysis machine</option>
                        <option value='C. A kidney dialysis machine'>C. A kidney dialysis machine</option>
                        <option value='D. A chronic positive airways pressure respirator'>D. A chronic positive airways pressure respirator</option>
                        <option value='E. Cigler najjar syndrome phototherapy equipment'>E. Cigler najjar syndrome phototherapy equipment</option>
                        <option value='F. A ventilator for life support'>F. A ventilator for life support</option>
                        <option value='G. Other'>G. Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Concessions</label>
                    <p>
                    <input type="radio" name="has_concessions" value="1" class="has_concessions" required> Yes
                    <input type="radio" name="has_concessions" value="0" class="has_concessions" required> No
                    </p>
                </div>
                
                <div class="form-group concession_field" style="display: none;">
                    <label>Name on Concession Card</label>
                    <input class="form-control" type="text" value="" name="concession_name" id="concession_name_on_card"/>
                </div>
                <div class="form-group concession_field" style="display: none;">
                    <label>Concession Number</label>
                    <input class="form-control" type="text" name="concession_number" placeholder="Enter your Concession Number">
                </div>
                <div class="form-group concession_field" style="display: none;">
                    <label>Concessions Card Start Date</label>
                    <input type="date" name="concessions_start_date">
                </div>
                <div class="form-group concession_field" style="display: none;">
                    <label>Concessions Card Expiry Date</label>
                    <input type="date" name="concessions_expiry_date">
                </div>
                
                <div class="form-group">
                    <label>Do you want to register for electronic marketing</label>
                    <p>
                    <input type="radio" name="electronic" value="1" required> Yes
                    <input type="radio" name="electronic" value="0" required> No
                    </p>
                </div>
                
                <div class="form-group">
                    <label>Supply Address</label>
                    <p>
                    <input type="radio" name="has_supply_address" value="1" class="has_supply_address" required> Yes
                    <input type="radio" name="has_supply_address" value="0" class="has_supply_address" required> No
                    </p>
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>Unit</label>
                    <input class="form-control" type="text" name="supply_unit" placeholder="Enter your supply unit">
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>Lot</label>
                    <input class="form-control" type="text" name="supply_lot" placeholder="Enter your supply lot">
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>Floor</label>
                    <input class="form-control" type="text" name="supply_floor" placeholder="Enter your supply floor">
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>Building Name</label>
                    <input class="form-control" type="text" name="supply_building_name" placeholder="Enter your supply building name">
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>Street Number</label>
                    <input class="form-control" type="text" name="supply_street_number" placeholder="Enter your supply street number">
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>Street Name</label>
                    <input class="form-control" type="text" name="supply_street_name" placeholder="Enter your supply street name">
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>Suburb</label>
                    <input class="form-control" type="text" name="supply_suburb" placeholder="Enter your supply suburb">
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>Postcode</label>
                    <input class="form-control" type="text" name="supply_postcode" placeholder="Enter your supply postcode">
                </div>
                <div class="form-group supply_address_field" style="display: none;">
                    <label>State</label>
                    <select name="supply_state" class="form-control">
                        <option selected="selected" value="">- Select -</option>
                        <option value="QLD">QLD</option>
                        <option value="VIC">VIC</option>
                        <option value="NSW">NSW</option>
                        <option value="SA">SA</option>
                        <option value="ACT">ACT</option>
                    </select>
                </div>
                
                <div class="form-group mirn_field" style="display: none;">
                    <label>Is MIRN Address Different to Supply Address?</label>
                    <p>
                    <input type="radio" name="is_mirn_address_different_supply_address" value="1" class="is_mirn_address_different_supply_address" required> Yes
                    <input type="radio" name="is_mirn_address_different_supply_address" value="0" class="is_mirn_address_different_supply_address" required> No
                    </p>
                </div>
                
                <div class="form-group mirn_address_field" style="display: none;">
                    <label>Unit</label>
                    <input class="form-control" type="text" name="mirn_unit" placeholder="Enter your MIRN unit">
                </div>
                <div class="form-group mirn_address_field" style="display: none;">
                    <label>Lot</label>
                    <input class="form-control" type="text" name="mirn_lot" placeholder="Enter your MIRN lot">
                </div>
                <div class="form-group mirn_address_field" style="display: none;">
                    <label>Floor</label>
                    <input class="form-control" type="text" name="mirn_floor" placeholder="Enter your MIRN floor">
                </div>
                <div class="form-group mirn_address_field" style="display: none;">
                    <label>Building Name</label>
                    <input class="form-control" type="text" name="mirn_building_name" placeholder="Enter your MIRN building name">
                </div>
                <div class="form-group mirn_address_field" style="display: none;">
                    <label>Street Number</label>
                    <input class="form-control" type="text" name="mirn_street_number" placeholder="Enter your MIRN street number">
                </div>
                <div class="form-group mirn_address_field" style="display: none;">
                    <label>Street Name</label>
                    <input class="form-control" type="text" name="mirn_street_name" placeholder="Enter your MIRN street name">
                </div>
                <div class="form-group mirn_address_field" style="display: none;">
                    <label>Suburb</label>
                    <input class="form-control" type="text" name="mirn_suburb" placeholder="Enter your MIRN suburb">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Postcode</label>
                    <input class="form-control" type="text" name="billing_postcode" placeholder="Enter your billing postcode">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>State</label>
                    <select name="billing_state" class="form-control">
                        <option selected="selected" value="">- Select -</option>
                        <option value="QLD">QLD</option>
                        <option value="VIC">VIC</option>
                        <option value="NSW">NSW</option>
                        <option value="SA">SA</option>
                        <option value="ACT">ACT</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Is Billing Address Different to Supply Address?</label>
                    <p>
                    <input type="radio" name="is_billing_address_different_supply_address" value="1" class="is_billing_address_different_supply_address" required> Yes
                    <input type="radio" name="is_billing_address_different_supply_address" value="0" class="is_billing_address_different_supply_address" required> No
                    </p>
                </div>
                
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Unit</label>
                    <input class="form-control" type="text" name="billing_unit" placeholder="Enter your billing unit">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Lot</label>
                    <input class="form-control" type="text" name="billing_lot" placeholder="Enter your billing lot">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Floor</label>
                    <input class="form-control" type="text" name="billing_floor" placeholder="Enter your billing floor">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Building Name</label>
                    <input class="form-control" type="text" name="billing_building_name" placeholder="Enter your billing building name">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Street Number</label>
                    <input class="form-control" type="text" name="billing_street_number" placeholder="Enter your billing street number">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Street Name</label>
                    <input class="form-control" type="text" name="billing_street_name" placeholder="Enter your billing street name">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Suburb</label>
                    <input class="form-control" type="text" name="billing_suburb" placeholder="Enter your billing suburb">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>Postcode</label>
                    <input class="form-control" type="text" name="billing_postcode" placeholder="Enter your billing postcode">
                </div>
                <div class="form-group billing_address_field" style="display: none;">
                    <label>State</label>
                    <select name="billing_state" class="form-control">
                        <option selected="selected" value="">- Select -</option>
                        <option value="QLD">QLD</option>
                        <option value="VIC">VIC</option>
                        <option value="NSW">NSW</option>
                        <option value="SA">SA</option>
                        <option value="ACT">ACT</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Add Secondary Contact?</label>
                    <p>
                    <input type="radio" name="add_secondary_contact" value="1" class="add_secondary_contact" required> Yes
                    <input type="radio" name="add_secondary_contact" value="0" class="add_secondary_contact" required> No
                    </p>
                </div>
                
                <div class="form-group secondary_contact_field" style="display: none;">
                    <label>Title</label>
                    <select class="form-control" name="secondary_title" tabindex="-1" title="" >
                        <option value="">None</option>
                        <option value="Mr.">Mr.</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Dr.">Dr.</option>
                        <option value="Prof.">Prof.</option>
                        <option value="MS">MS</option>
                        <option value="MRS">MRS</option>
                        <option value="MR">MR</option>
                        <option value="MISS">MISS</option>
                        <option value="DR">DR</option>
                    </select>
                </div>
                
                <div class="form-group secondary_contact_field" style="display: none;">
                    <label>First Name</label>
                    <input class="form-control" type="text" name="secondary_firstname" placeholder="Enter your firstname">
                </div>

                <div class="form-group secondary_contact_field" style="display: none;">
                    <label>Last Name</label>
                    <input class="form-control" type="text" name="secondary_lastname" placeholder="Enter your lastname">
                </div>
                
                <div class="form-group secondary_contact_field" style="display: none;">
                    <label>Date of Birth</label>
                    <input type="date" name="secondary_dob">
                </div>
                
                <div class="form-group" id="secret_question_field">
                    <label>Secret Question</label>
                    <select class="form-control" name="secret_question" tabindex="-1" title=""  required>
                        <option value="">Select an Option</option>
                        <option value="What is your mothers maiden name?">What is your mothers maiden name?</option>
                        <option value="What is the town you were born in?">What is the town you were born in?</option>
                        <option value="What is the name of your first pet??">What is the name of your first pet??</option>
                        <option value="What is the first street you lived on?">What is the first street you lived on?</option>
                        <option value="What is the name of the first school you went to?">What is the name of the first school you went to?</option>
                    </select>
                </div>

                <div class="form-group" id="answer_field">
                    <label>Answer</label>
                    <input class="form-control" type="text" name="secret_answer" placeholder="Enter your Answer" required>
                </div>
                <div class="form-group" id="payment_method_field">
                    <label>Payment Method</label>
                    <select class="form-control" name="payment_method" tabindex="-1" title=""  required>
                        <option value="">Select an Option</option>
                        <option value="Default">Default</option>
                        <option value="Direct Debit">Direct Debit</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <input type="submit" value="Submit" name="submit" class="btn btn-success" />
                </div>
            </form>
        </div></div>
</div>
<script>
    $(document).ready(function() {
        $('#fuel').change(function() {
            if ($(this).val() == 'Dual') {
                $('.nmi_field').show();
                $('.mirn_field').show();
            } else if ($(this).val() == 'Elec') {
                $('.nmi_field').show();
                $('.mirn_field').hide();
            } else if ($(this).val() == 'Gas') {
                $('.nmi_field').hide();
                $('.mirn_field').show();
            }
        });
        
        $('.is_mirn_address_different_supply_address').click(function() {
            if ($('input[name=is_mirn_address_different_supply_address]:checked').val() == 1) {
                $('.mirn_address_field').show();
            } else {
                $('.mirn_address_field').hide();
            }
        });
        
        $('#customer_type').change(function() {
            if ($(this).val() == 'Business') {
                $('#abn_field').show();
            } else {
                $('#abn_field').hide();
            }
        });
        
        $('.has_identification').click(function() {
            if ($('input[name=has_identification]:checked').val() == 1) {
                $('.identification_field').show();
            } else {
                $('.identification_field').hide();
            }
        });
        
        $('.life_support').click(function() {
            if ($('input[name=life_support]:checked').val() == 1) {
                $('.life_support_field').show();
            } else {
                $('.life_support_field').hide();
            }
        });
        
        $('.has_concessions').click(function() {
            if ($('input[name=has_concessions]:checked').val() == 1) {
                $('.concession_field').show();
            } else {
                $('.concession_field').hide();
            }
        });
        
        $('.has_supply_address').click(function() {
            if ($('input[name=has_supply_address]:checked').val() == 1) {
                $('.supply_address_field').show();
            } else {
                $('.supply_address_field').hide();
            }
        });
        
        $('.add_secondary_contact').click(function() {
            if ($('input[name=add_secondary_contact]:checked').val() == 1) {
                $('.secondary_contact_field').show();
            } else {
                $('.secondary_contact_field').hide();
            }
        });
        
        $('.is_billing_address_different_supply_address').click(function() {
            if ($('input[name=is_billing_address_different_supply_address]:checked').val() == 1) {
                $('.billing_address_field').show();
            } else {
                $('.billing_address_field').hide();
            }
        });
    
        $('#create_lead_form').submit(function() {
            var message = [];
            var e=false;

            if (e) {
                alert(message.join("\n"));
                return false;
            } else {
                return true;
            }
        });
        
        $('#agent_name').bind('keyup', function() {
		    agent_lookup();
	    });
        
        agent_lookup = function() {
		    if ($('#agent_name').val() == "") {
		    	return;
		    }
            $('#agent_name').autocomplete({
		    	source: function( request, response ) {
		    		$.ajax({
		    			url: "/tools/sales_rep",
		    			dataType: "jsonp",
		    			type: "GET",
		    			contentType: "application/json; charset=utf-8",
		    			data: {term:$('#agent_name').val()},
		    			success: function( data ) {
		    				response( $.map( data.items, function( item ) {
		    					return {
		    						label: item.name,
		    						value: item.name,
		    						email: item.email,
		    						id: item.id
		    					}
		    				}));
		    			}
		    		});
		    	},
		    	delay:5,
		    	minLength: 1,
		    	select: function( event, ui ) {
		    		$('#agent_id').val(ui.item.id);
		    		$('#agent_email').val(ui.item.email);
		    	},
		    	change: function (event, ui) {
		    		if (ui.item == null || ui.item == undefined) {
                		$('#agent_id').val('');
                		$('#agent_name').val('');
                		$('#agent_email').val('');
            		}
    	    	}
		    });
        }
    });

</script>
