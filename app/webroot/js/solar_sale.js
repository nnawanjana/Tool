(function($){
	$(document).ready(function(e) {
    	
    	$(document).on('click', '.solar_interest', function(event) {
            $.post("/solar_sale",{action: 'solar_interest', sid: $('#sid2').val(), campaign_id: $('#campaign_id').val(), referring_agent: $('#referring_agent').val()},function(response) {
				window.location.replace("/solar_sale");
            });
        });
        
    	$(document).on('click', '.solar_appointment', function(event) {
        	$('#processing').show();
            $.post("/solar_sale",{action: 'solar_appointment', sid: $('#sid2').val(), campaign_id: $('#campaign_id').val(), referring_agent: $('#referring_agent').val()},function(response) {
				window.location.replace("/solar_sale");
            });
        });
        
        $(document).on('click', '.solar_sale_confirmed', function(event) {
            $.post("/solar_sale",{action: 'solar_sale_confirmed', sid: $('#sid2').val(), campaign_id: $('#campaign_id').val(), referring_agent: $('#referring_agent').val()},function(response) {
				window.location.replace("/solar_sale");
            });
        });
        
        lead_lookup2 = function() {
            $.post('/tools/get_lead_fields', {app_key: '48347de54501ba15d16d84dbcbe348fd', lead_id: $('#sid2').val()}, function(data) {
        	    if (!data.first_name) {
                    alert('Lead not found');
                    $('#sid').val('');
                    return false;
                }
        	    
            	$('#campaign_id').val(data.campaign_id);
            	$('#campaign_name').val(data.campaign_name);
            	$('#first_campaign').val(data.first_campaign);
            	if (data.first_name) {
                	if ($('#sid').val() == '1433795') {
                    	$('#first_name').val('Chelsea');
                	} else {
                    	$('#first_name').val(data.first_name);
                	}
                	$("#first_name").prop("readonly", true);
                	
                	if ($("#name").length > 0) {
                    	$('#name').val(data.first_name + ' ' + data.last_name);
                	}
            	}
                if (data.last_name) {
                    if ($('#sid').val() == '1433795') {
                    	$('#surname').val('');
                	} else {
                    	$('#surname').val(data.last_name);
                	}
                	$("#surname").prop("readonly", true);
            	}
                if (data.mobile && data.mobile != '0') {
                    $('#mobile').val(data.mobile);
                    //$("#mobile").prop("readonly", true);
                    
                    if ($("#phone_number").length > 0) {
                    	$('#phone_number').val(data.mobile);
                	}
                }
                if (data.home_phone && data.home_phone != '0') {
                    $('#home_phone').val(data.home_phone);
                    //$("#home_phone").prop("readonly", true);
                }
                if (data.work_number && data.work_number != '0') {
                    $('#work_number').val(data.work_number);
                    //$("#work_number").prop("readonly", true);
                }
                if (data.email) {
                    $('#email').val(data.email);
                    if (data.email != '(Hard Bounce)') {
                        //$("#email").prop("readonly", true);
                    } else {
                        //$("#email").prop("readonly", false);
                    }
                }
                if (data.postcode) {
                    $('#postcode').val(data.postcode);
                }
                if (data.sales_rep_name) {
                    $('#sales_rep_name').val(data.sales_rep_name);
                }
                if (data.referring_agent) {
                    $('#referring_agent').val(data.referring_agent);
                    $("#referring_agent").prop("readonly", true);
                }
                
                if (data.solar_interest && data.solar_interest == 'Yes') {
                    $(".solar_interest").hide();
                } else {
                    $(".solar_interest").show();
                }
                
                if (data.solar_appointment && data.solar_appointment == 'Yes') {
                    $(".solar_appointment").hide();
                } else {
                    $(".solar_appointment").show();
                }
                
                if (data.solar_sale_confirmed && data.solar_sale_confirmed == 'Yes') {
                    $(".solar_sale_confirmed").hide();
                } else {
                    $(".solar_sale_confirmed").show();
                }
                
                //alert('Data has been imported for ' + data.first_name);
            });
        }
        
        $('#sid2').blur(function() {
            if ($('#sid2').is(':visible')) {
                if ($(this).val()) {
                	lead_lookup2();
                } else {
                	$('#sid2').addClass('valid').removeClass('error');
                }
            }
        });
        if ($('#sid2').val()) {
            if ($('#sid2').is(':visible')) {
                lead_lookup2();
            } else {
                $.post('/tools/get_lead_fields', {app_key: '48347de54501ba15d16d84dbcbe348fd', lead_id: $('#sid2').val()}, function(data) {
            	    
                });
            }
        }
        
        sales_rep_lookup = function() {
            if ($('#referring_agent').val() == "") {
                return;
            }
            $('#referring_agent').autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "/tools/sales_rep",
                        dataType: "jsonp",
                        type: "GET",
                        contentType: "application/json; charset=utf-8",
                        data: {term:$('#referring_agent').val()},
                        success: function( data ) {
                            response( $.map( data.items, function( item ) {
                                return {
                                    label: item.name,
                                    value: item.name
                                }
                            }));
                        }
                    });
                },
                delay:5,
                minLength: 1,
                select: function( event, ui ) {
                    
                },
                change: function (event, ui) {
                    if (ui.item == null || ui.item == undefined) {
                        $('#referring_agent').val('');
                    }
                }
            });
        }
        
        $('#referring_agent').bind('keyup', function(){
            sales_rep_lookup();
        });
    });
})(jQuery);