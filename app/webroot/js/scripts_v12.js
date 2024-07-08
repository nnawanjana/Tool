(function($){
	$(document).ready(function(e) {
		 window.place_lookup = function() {
			if ($("#postcode").val() == "") {   
				 return;
			}
			$("#postcode").autocomplete({
        		source: function( request, response ) {
            		$.ajax({
                		url: "/tools/postcode_to_suburb",
						dataType: "jsonp",
						type: "GET",
						contentType: "application/json; charset=utf-8",
						data: {term:$("#postcode").val()},
						success: function( data ) {
                    		response( $.map( data.items, function( item ) {
                        		return {
                            		label: (item.postcode + ", " + item.suburb),
									value: item.postcode,
									Suburb: item.suburb,
									State: item.state,
									Postcode: item.postcode,
								}
							}));
						}
					});
				},
				delay: 10,
				minLength: 2,
				select: function( event, ui ) {
					$('#postcode_suburb').val(ui.item.label);
					$('#postcode_suburb').show();
					$('#postcode_suburb').attr('readonly','readonly');
					$("#postcode").hide();
					$('#postcode').removeClass("error");
					$('#suburb').val(ui.item.Suburb);
					$('#state').val(ui.item.State);
				},
			});
		}
		window.suburb_options = function(d) {
			$.ajax({
                url: "/tools/suburb_options",
				dataType: "json",
				type: "GET",
				contentType: "application/json; charset=utf-8",
				data: {postcode:$("#postcode").val()},
				success: function( data ) {
					if (data.length > 0) {
						var suburb_options = '<option value="">Suburb</option>';
						var has_suburb = false;
						$.each(data, function(key, value) {
							var selected = '';
							if (value.selected == 1 || (d && d == value.suburb))  {
								selected = 'selected="selected"';
								has_suburb = true;	
								
							}
							
							suburb_options += '<option value="'+value.suburb+'" '+selected+'>'+value.suburb+'</option>';
						});
						$('#suburb').empty().append(suburb_options);
						// fix issue: remove error message when suburb is selected
						if ($('#suburb').find(":selected")){
							$('#suburb').parent().find('.webform_error').remove();
						}
						if (has_suburb === false) {
							$('#suburb').parent().find('.webform_error').remove();
							$('#suburb').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select suburb</div></div></div><div class="webform_error_bottom"></div></div>');
						}
						
					}
					else {
						$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Sorry we do not service that area</div></div></div><div class="webform_error_bottom"></div></div>');
					}
				}
			});
		}
		/*
		window.elec_meter_type_radios = function() {
			var state = $('#state').val();
			if (state == 'NSW') {
				$('#elec_meter_type_fields').show();
			    $('#singlerate_radio').show();
			    $('#singlerate_cl1_radio').show();
			    $('#singlerate_cl2_radio').show();
			    $('#singlerate_cl1_cl2_radio').show();
			    $('#singlerate_cs_radio').hide();
			    $('#singlerate_cl1_cs_radio').hide();
			    $('#timeofuse_radio').show();
			    $('#timeofuse_shoulder_field').show();
			    $('#timeofuse_cs_radio').hide();
			    $('#timeofuse_cl1_cs_radio').hide();
			    $('#timeofuse_tariff12_radio').hide();
			    $('#timeofuse_tariff13_radio').hide();
			    $('#flexible_pricing_radio').hide();
			}
			else if (state == 'VIC') {
				$('#elec_meter_type_fields').show();
			    $('#singlerate_radio').show();
			    $('#singlerate_cl1_radio').show();
			    $('#singlerate_cl2_radio').hide();
			    $('#singlerate_cl1_cl2_radio').hide();
			    $('#singlerate_cs_radio').show();
			    $('#singlerate_cl1_cs_radio').show();
			    $('#timeofuse_radio').show();
			    $('#timeofuse_shoulder_field').hide();
			    $('#timeofuse_cs_radio').show();
			    $('#timeofuse_cl1_cs_radio').show();
			    $('#timeofuse_tariff12_radio').hide();
			    $('#timeofuse_tariff13_radio').hide();
			    $('#flexible_pricing_radio').show();
			}
			else if (state == 'SA') {
				$('#elec_meter_type_fields').show();
			    $('#singlerate_radio').show();
			    $('#singlerate_cl1_radio').show();
			    $('#singlerate_cl2_radio').hide();
			    $('#singlerate_cl1_cl2_radio').hide();
			    $('#singlerate_cs_radio').hide();
			    $('#singlerate_cl1_cs_radio').hide();
			    $('#timeofuse_radio').hide();
			    $('#timeofuse_cs_radio').hide();
			    $('#timeofuse_cl1_cs_radio').hide();
			    $('#timeofuse_tariff12_radio').hide();
			    $('#timeofuse_tariff13_radio').hide();
			    $('#flexible_pricing_radio').hide();
			}
			else if (state == 'QLD') {
				$('#elec_meter_type_fields').show();
			    $('#singlerate_radio').show();
			    $('#singlerate_cl1_radio').show();
			    $('#singlerate_cl2_radio').show();
			    $('#singlerate_cl1_cl2_radio').show();
			    $('#singlerate_cs_radio').hide();
			    $('#singlerate_cl1_cs_radio').hide();
			    $('#timeofuse_radio').hide();
			    $('#timeofuse_cs_radio').hide();
			    $('#timeofuse_cl1_cs_radio').hide();
			    $('#timeofuse_tariff12_radio').show();
			    $('#timeofuse_tariff13_radio').show();
			    $('#flexible_pricing_radio').hide();
			}
			else {
			    $('#elec_meter_type_fields').hide();
			}
		}
		*/
		window.tariff_lookup = function(element,type) {
			$('#nmi_distributor').text('');
			$.ajax({
                url: "/tools/tariff_options2",
				dataType: "json",
				type: "GET",
				contentType: "application/json; charset=utf-8",
				data: {state:$('#state').val(), customer_type:$('#customer_type').val(), nmi:$('#nmi').val(), field:element},
				success: function( data ) {
					if (data.length > 0) {
						$('#nmi_distributor').text(data[0].distributor);
						var has_tariff = false;
						var tariff_options = '<option value="">Tariff</option>';
						$.each(data, function(key, value) {
							var inlcude = true;
							var tariff_option_value = value.tariff_code+'|'+value.pricing_group+'|'+value.child_tariff+'|'+value.tariff_type+'|'+value.solar_rebate_scheme;
							if (element == 'tariff1') {
								if (($('#tariff2').val() && $('#tariff2').val() == tariff_option_value) || ($('#tariff3').val() && $('#tariff3').val() == tariff_option_value) || ($('#tariff4').val() && $('#tariff4').val() == tariff_option_value)) {
									inlcude = false;
								}
							}
							if (element == 'tariff2') {
								if (($('#tariff1').val() && $('#tariff1').val() == tariff_option_value) || ($('#tariff3').val() && $('#tariff3').val() == tariff_option_value)) {
									inlcude = false;
								}
								if ($('#tariff1').val()) {
									var tariff1 = $('#tariff1').val().split('|');
									if (tariff1[3] == 'Solar' && value.tariff_type == 'Solar') {
										inlcude = false;
									}
								}
							}
							if (element == 'tariff3') {
								if (($('#tariff1').val() && $('#tariff1').val() == tariff_option_value) || ($('#tariff2').val() && $('#tariff2').val() == tariff_option_value)) {
									inlcude = false;
								}
								if ($('#tariff1').val()) {
									var tariff1 = $('#tariff1').val().split('|');
									if (tariff1[3] == 'Solar' && value.tariff_type == 'Solar') {
										inlcude = false;
									}
								}
								if ($('#tariff2').val()) {
									var tariff2 = $('#tariff2').val().split('|');
									if (tariff2[3] == 'Solar' && value.tariff_type == 'Solar') {
										inlcude = false;
									}
								}
							}
							if (element == 'tariff4') {
								if (($('#tariff1').val() && $('#tariff1').val() == tariff_option_value) || ($('#tariff2').val() && $('#tariff2').val() == tariff_option_value) || ($('#tariff3').val() && $('#tariff3').val() == tariff_option_value)) {
									inlcude = false;
								}
								if ($('#tariff1').val()) {
									var tariff1 = $('#tariff1').val().split('|');
									if (tariff1[3] == 'Solar' && value.tariff_type == 'Solar') {
										inlcude = false;
									}
								}
								if ($('#tariff2').val()) {
									var tariff2 = $('#tariff2').val().split('|');
									if (tariff2[3] == 'Solar' && value.tariff_type == 'Solar') {
										inlcude = false;
									}
								}
								if ($('#tariff3').val()) {
									var tariff3 = $('#tariff3').val().split('|');
									if (tariff3[3] == 'Solar' && value.tariff_type == 'Solar') {
										inlcude = false;
									}
								}
							}
							if (type == 'parent' && value.child_tariff == 1) {
								inlcude = false;
							}
							if (type == 'child' && value.child_tariff == 0) {
								inlcude = false;
							}
							if (inlcude) {
								var selected = '';
								if (value.selected == 1)  {
									selected = 'selected="selected"';
									has_tariff = tariff_option_value;
								}
								if (value.tariff_type == 'Solar') {
									tariff_options += '<option value="'+tariff_option_value+'" '+selected+'>'+value.tariff_code+' (Solar)</option>';
								}
								else {
									tariff_options += '<option value="'+tariff_option_value+'" '+selected+'>'+value.tariff_code+'</option>';
								}
							}
						});
						$('#'+element).empty().append(tariff_options);
						if (has_tariff) {
							var has_tariff_arr = has_tariff.split('|');
							$('#'+element).val(has_tariff).combobox("refresh");
							if (has_tariff_arr[2] == 0 && has_tariff_arr[3] == 'Solar') {
								$('#'+element).parent().find('.plus').hide();
							}
						}
					}
					else {
						$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">NMI does not match state. Please select correct state and suburb above</div></div></div><div class="webform_error_bottom"></div></div>');
						$('#'+element).empty().append('<option value="">Tariff</option>');
					}
					selec_tariff();
				}
			});
		}
		window.selec_tariff = function() {
			if ($('#nmi').val() == '' || $('#tariff_parent').val() == '') {
				return false;
			}
			var tariff_parent = $('#tariff_parent').val().split('|');
			var tariff1 = $('#tariff1').val().split('|');
			var tariff2 = $('#tariff2').val().split('|');
			var tariff3 = $('#tariff3').val().split('|');
			var tariff4 = $('#tariff4').val().split('|');
			var tariff_parent_arr = tariff_parent[1].split(' + ');
			var pricing_groups_arr = [];
			var solar_specific_plan = false
			if (tariff1[3] == 'Solar') {
				solar_specific_plan = true;
			}
			if (tariff2[3] == 'Solar') {
				solar_specific_plan = true;
			}
			if (tariff3[3] == 'Solar') {
				solar_specific_plan = true;
			}
			if (tariff4[3] == 'Solar') {
				solar_specific_plan = true;
			}
			if (solar_specific_plan) {
				$('#solar_fields').show();
			}
			else {
				$('#solar_fields').hide();
			}
			
		    var renant_owner = $('#renant_owner').val();
			var customer_type = $('#customer_type').val();
			var plan_type = $('#plan_type').val();
			$('.battery-storage-solution').hide();
			$('.battery-storage-solar-solution').hide();
			if (plan_type == 'Dual' || plan_type == 'Elec') {
                if (renant_owner == 'Renter') {
        			if (customer_type == 'RES') {
            			if ($("#solar_fields").is(':visible')) {
            			    $('.battery-storage-solution').show();
                        }
        			} else if (customer_type == 'SME') {
            			if ($("#solar_fields").is(':visible')) {
            			    $('.battery-storage-solution').show();
                        } else {
                            $('.battery-storage-solar-solution').show();
                        }
        			}
    			} else if (renant_owner == 'Owner') {
        			if (customer_type == 'RES') {
            			if ($("#solar_fields").is(':visible')) {
                			$('.battery-storage-solution').show();
                        } else {
            			    $('.battery-storage-solar-solution').show();
                        }
        			} else if (customer_type == 'SME') {
            			if ($("#solar_fields").is(':visible')) {
            			    $('.battery-storage-solution').show();
                        } else {
                            $('.battery-storage-solar-solution').show();
                        }
        			}
    			}
			}
			
			pricing_groups_arr.push(tariff_parent_arr[0]);
			if (tariff1[1] == 'CL1' || tariff2[1] == 'CL1' || tariff3[1] == 'CL1' || tariff4[1] == 'CL1') {
				 if ($.inArray('CL1', pricing_groups_arr) == -1) {
				 	pricing_groups_arr.push('CL1');
				 }
			}
			if (tariff1[1] == 'CL2' || tariff2[1] == 'CL2' || tariff3[1] == 'CL2' || tariff4[1] == 'CL1') {
				if ($.inArray('CL2', pricing_groups_arr) == -1) {
				 	pricing_groups_arr.push('CL2');
				}
			}
			$.each(tariff_parent_arr, function(index,item){
				if (index > 0) {
					if ($.inArray(item, pricing_groups_arr) == -1) {
						pricing_groups_arr.push(item);
					}
				}
			});
			if (tariff1[1] == 'Climate Saver' || tariff2[1] == 'Climate Saver' || tariff3[1] == 'Climate Saver' || tariff4[1] == 'Climate Saver') {
				if ($.inArray('Climate Saver', pricing_groups_arr) == -1) {
				 	pricing_groups_arr.push('Climate Saver');
				}
			}
			pricing_groups = pricing_groups_arr.join(' + ');
			if ($('#elec_recent_bill').val() == 'No') {
				$('#elec_meter_type2').val(pricing_groups);
			}
			else {
				$('#elec_meter_type_fields').show();
				if (pricing_groups == 'Single Rate') {
					$('#elec_meter_type_fields .radio').hide();
					$('#singlerate_radio').show();
					$('#singlerate_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Single Rate + CL1') {
					$('#elec_meter_type_fields .radio').hide();
					$('#singlerate_cl1_radio').show();
					$('#singlerate_cl1_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Single Rate + CL2') {
					$('#elec_meter_type_fields .radio').hide();
					$('#singlerate_cl2_radio').show();
					$('#singlerate_cl2_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Single Rate + CL1 + CL2') {
					$('#elec_meter_type_fields .radio').hide();
					$('#singlerate_cl1_cl2_radio').show();
					$('#singlerate_cl1_cl2_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Single Rate + Climate Saver') {
					$('#elec_meter_type_fields .radio').hide();
					$('#singlerate_cs_radio').show();
					$('#singlerate_cs_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Single Rate + CL1 + Climate Saver') {
					$('#elec_meter_type_fields .radio').hide();
				    $('#singlerate_cl1_cs_radio').show();
				    $('#singlerate_cl1_cs_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Time of Use' || pricing_groups == 'Transitional Time of Use') {
					$('#elec_meter_type_fields .radio').hide();
					$('#timeofuse_radio').show();
					$('#timeofuse_radio input:radio').trigger( 'click' );
					$('#timeofuse_shoulder_field').show();
					if (pricing_groups == 'Transitional Time of Use') {
    					$('#timeofuse_radio input:radio').val('Transitional Time of Use');
    					$('#timeofuse_radio span.elec_meter_type_label').text('Transitional Time of Use');
					} else {
    					$('#timeofuse_radio input:radio').val('Time of Use');
    					$('#timeofuse_radio span.elec_meter_type_label').text('Time of Use');
					}
				}
				else if (pricing_groups == 'Time of Use (PowerSmart)') {
					$('#elec_meter_type_fields .radio').hide();
					$('#timeofuse_PowerSmart_radio').show();
					$('#timeofuse_PowerSmart_radio input:radio').trigger( 'click' );
					$('#timeofuse_shoulder_field').show();
				}
				else if (pricing_groups == 'Time of Use (LoadSmart)') {
					$('#elec_meter_type_fields .radio').hide();
					$('#timeofuse_LoadSmart_radio').show();
					$('#timeofuse_LoadSmart_radio input:radio').trigger( 'click' );
					$('#timeofuse_shoulder_field').show();
				}								
				else if (pricing_groups == 'Time of Use + Climate Saver') {
					$('#elec_meter_type_fields .radio').hide();
					$('#timeofuse_cs_radio').show();
					$('#timeofuse_cs_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Time of Use + CL1 + Climate Saver') {
					$('#elec_meter_type_fields .radio').hide();
				    $('#timeofuse_cl1_cs_radio').show();
				    $('#timeofuse_cl1_cs_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Time of Use + CL1' || pricing_groups == 'Transitional Time of Use + CL1') {
					$('#elec_meter_type_fields .radio').hide();
					$('#timeofuse_cl1_radio').show();
					$('#timeofuse_cl1_radio input:radio').trigger( 'click' );
					if (pricing_groups == 'Transitional Time of Use + CL1') {
    					$('#timeofuse_cl1_radio input:radio').val('Transitional Time of Use + CL1');
    					$('#timeofuse_cl1_radio span.elec_meter_type_label').text('Transitional Time of Use');
					} else {
    					$('#timeofuse_cl1_radio input:radio').val('Time of Use + CL1');
    					$('#timeofuse_cl1_radio span.elec_meter_type_label').text('Time of Use');
					}
				}
				else if (pricing_groups == 'Time of Use + CL2' || pricing_groups == 'Transitional Time of Use + CL2') {
					$('#elec_meter_type_fields .radio').hide();
				    $('#timeofuse_cl2_radio').show();
					$('#timeofuse_cl2_radio input:radio').trigger( 'click' );
					if (pricing_groups == 'Transitional Time of Use + CL2') {
    					$('#timeofuse_cl2_radio input:radio').val('Transitional Time of Use + CL2');
    					$('#timeofuse_cl2_radio span.elec_meter_type_label').text('Transitional Time of Use');
					} else {
    					$('#timeofuse_cl2_radio input:radio').val('Time of Use + CL2');
    					$('#timeofuse_cl2_radio span.elec_meter_type_label').text('Time of Use');
					}
				}
				else if (pricing_groups == 'Time of Use Tariff12') {
					$('#elec_meter_type_fields .radio').hide();
					$('#timeofuse_tariff12_radio').show();
					$('#timeofuse_tariff12_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Time of Use Tariff13') {
					$('#elec_meter_type_fields .radio').hide();
					$('#timeofuse_tariff13_radio').show();
					$('#timeofuse_tariff13_radio input:radio').trigger( 'click' );
				}
				else if (pricing_groups == 'Flexible Pricing') {
					$('#elec_meter_type_fields .radio').hide();
					$('#flexible_pricing_radio').show();
					$('#flexible_pricing_radio input:radio').trigger( 'click' );
				}
			}
		}
		$.widget( "custom.combobox", {
			_create: function() {
				this.wrapper = $( "<span>" )
					.addClass( "custom-combobox" )
					.insertAfter( this.element );

				this.element.hide();
				this._createAutocomplete();
				this._createShowAllButton();
			},

			_createAutocomplete: function() {
				var selected = this.element.children( ":selected" ),
					value = selected.val() ? selected.text() : "";

				this.input = $( "<input>" )
					.appendTo( this.wrapper )
					.val( value )
					.attr( "title", "" )
					.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: $.proxy( this, "_source" )
					})
					.tooltip({
						tooltipClass: "ui-state-highlight"
					});

				this._on( this.input, {
					autocompleteselect: function( event, ui ) {
						ui.item.option.selected = true;
						this._trigger( "select", event, {
							item: ui.item.option
						});
						this.element.trigger("change");
					},

					autocompletechange: "_removeIfInvalid"
				});
			},

			_createShowAllButton: function() {
				var input = this.input,
					wasOpen = false;

				$( "<a>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Show All Items" )
					.tooltip()
					.appendTo( this.wrapper )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "custom-combobox-toggle ui-corner-right" )
					.mousedown(function() {
						wasOpen = input.autocomplete( "widget" ).is( ":visible" );
					})
					.click(function() {
						input.focus();

						// Close if already visible
						if ( wasOpen ) {
							return;
						}

						// Pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
					});
			},

			_source: function( request, response ) {
				var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
				response( this.element.children( "option" ).map(function() {
					var text = $( this ).text();
					if ( this.value && ( !request.term || matcher.test(text) ) )
						return {
							label: text,
							value: text,
							option: this
						};
				}) );
			},

			_removeIfInvalid: function( event, ui ) {

				// Selected an item, nothing to do
				if ( ui.item ) {
					return;
				}

				// Search for a match (case-insensitive)
				var value = this.input.val(),
					valueLowerCase = value.toLowerCase(),
					valid = false;
				this.element.children( "option" ).each(function() {
					if ( $( this ).text().toLowerCase() === valueLowerCase ) {
						this.selected = valid = true;
						return false;
					}
				});

				// Found a match, nothing to do
				if ( valid ) {
					return;
				}

				// Remove invalid value
				this.input
					.val( "" )
					.attr( "title", value + " didn't match any item" )
					.tooltip( "open" );
				this.element.val( "" );
				this._delay(function() {
					this.input.tooltip( "close" ).attr( "title", "" );
				}, 2500 );
				this.input.data( "ui-autocomplete" ).term = "";
			},

			_destroy: function() {
				this.wrapper.remove();
				this.element.show();
			},
			refresh: function() { 
				selected = this.element.children( ":selected" );
				this.input.val(selected.text());
			},
		});
		$("#tariff1").combobox();
		$("#tariff2").combobox();
		$("#tariff3").combobox();
		$("#tariff4").combobox();
		// fix issue: suburb displayed delay when enter postcode 
		$('#postcode').on('mouseleave', function() {
			if ($('#postcode').val()) {
				suburb_options();
			}
			var suburbName = $('#suburb').find(":selected").text();
			if (suburbName && $('#postcode').val()){
				suburb_options(suburbName);
			}
		});
		
		$('#postcode').blur(function(){
			var e = false;
			
			if ($('#postcode').val() == '') { // No value entered, no need validate
				e = e || false;
			}
			else {
				var postcode = $('#postcode').val().replace(/\s/g, '');
				if ((postcode.length != 4) || (/[^0-9-()+\s]/.test(postcode))) {
					$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your postcode</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				}		
				else if(!(/^[2-5]/.test(postcode))) {
					$('#postcode').addClass("error");
					$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Sorry we do not service that area</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;	
				}
				else {
					$('#postcode').removeClass("error");
					e = e || false;
				}
			}
			if ($('#postcode').val() && e === false) {
				suburb_options();
				
			}
			else {
				$('#suburb').empty().append('<option value="">Suburb</option>');
			}
		});
		if ($('#postcode').val()) {
			suburb_options();
			
		}
		$('#suburb').change(function() {
			// fix issue: remove error message when suburb is selected
			if ($('#suburb').find(":selected")){
				$('#suburb').parent().find('.webform_error').remove();
			}
    		if ($(this).val()) {
        		$.get("/tools/get_state_by_suburb", {suburb: $(this).val(), postcode: $('#postcode').val()}, function(response) {
        		    $('#state').val(response.state);
                });
    		}
		}).change();
		$('#campaign_id2').change(function() {
    		var campaign_id2 = $(this).val();
    		if (campaign_id2 == 100) {
        		$('#electrician_name_field').show();
    		} else {
        		$('#electrician_name_field').hide();
    		}
        });
		$(document).on('click', '.radio-simulate .choice', function() {
            var id = $(this).prop('id');
			if (id == 'Yes') $(this).parent().addClass('active');
			if (id == 'No') $(this).parent().removeClass('active');
			$(this).parent().siblings().val(id).trigger('change');
        });
        $('input[name="pay_on_time_discount"]').change(function() {
	        if ($(this).val() == 'No' && $('input[name="direct_debit_discount"]').val() == 'Yes') {
		        $('.radio-simulate.pay-on-time-discount-choices #Yes').trigger( 'click' );
		        //alert("We've enabled pay on time discounts for you, as you've enabled direct debit discounts");
	        }
	        if ($(this).val() == 'Yes') {
		        $('.radio-simulate.bonus-discount-choices #Yes').trigger( 'click' );
	        }
        });
        $('input[name="direct_debit_discount"]').change(function() {
	        if ($(this).val() == 'Yes') {
		        $('.radio-simulate.pay-on-time-discount-choices #Yes').trigger( 'click' );
		        //alert("We've enabled pay on time discounts for you, as you've enabled direct debit discounts");
	        }
        });
        $(document).on('click', '.plan-type', function() {
			//$(this).addClass('active').siblings().removeClass('active');
			$('.plan-type').removeClass('active');
			$(this).addClass('active');
            var id = $(this).prop('id');
            
            $('#elec_transfer').hide();
            $('#gas_transfer').hide();
                
            if (id == 'Elec') {
	            $('#elec-disclaimer').show();
	            if ($('#looking_for').val() == 'Compare Plans') {
    	            $('#elec_transfer').show();
	            }
            }
            else {
	            $('#elec-disclaimer').hide();
            }
            if (id == 'Gas') {
                if ($('#looking_for').val() == 'Compare Plans') {
                    $('#gas_transfer').show();
                }
            }
            if (id == 'Dual') {
	            $('#dual-disclaimer').show();
	            if ($('#looking_for').val() == 'Compare Plans') {
	                $('#elec_transfer').show();
                    $('#gas_transfer').show();
                }
            }
            else {
	            $('#dual-disclaimer').hide();
            }
			$('#plan_type').val(id);
        });
        $(document).on('click', '.is-broadband', function() {
            $(this).toggleClass('active');
            if ($(this).hasClass('active')) {
                $('#is_broadband').val(1);
            } else {
                $('#is_broadband').val(0);
            }
        });
        $(document).on('click', '.customer-type', function() {
			$('.customer-type').removeClass('active');
			$(this).addClass('active');
            var id_tmp = $(this).prop('id');
            var id = '';
            if (id_tmp == 'SOHO') {
                id = 'RES';
                $('#is_soho').val(1);
            } else {
                id = id_tmp;
                $('#is_soho').val(0);
            }
			$('#customer_type').val(id);
			if (id == 'SME') {
				$('#business_name_field').show();
				$('#business-section').show();
			}
			else {
				$('#business_name_field').hide();
				$('#business-section').hide();
			}
			if ($('#nmi').val()) {
				tariff_lookup('tariff1', 'both');
				$('#tariff1').val('').combobox("refresh");
				/*
				$('#tariff_parent').val('');
				*/
			}
			$.post("/tools/get_usage_level", {plan_type: 'Elec', customer_type: id, version: 5}, function(response) {
                $('#elec_usage_level_buttons').html(response.html);
                if ($('#elec_usage_level').val()) {
                    var elec_usage_level = $('#elec_usage_level').val();
                    $('.elec-usages #' + elec_usage_level).trigger( 'click' );
                }
            }, "json");
            $.post("/tools/get_usage_level", {plan_type: 'Gas', customer_type: id, version: 5}, function(response) {
                $('#gas_usage_level_buttons').html(response.html);
                if ($('#gas_usage_level').val()) {
    	            var gas_usage_level = $('#gas_usage_level').val();
                    $('.gas-usages #' + gas_usage_level).trigger( 'click' );
                }
            }, "json");
        });
        if ($('#customer_type').val()) {
	        var customer_type = $('#customer_type').val();
	        $.post("/tools/get_usage_level", {plan_type: 'Elec', customer_type: customer_type, version: 5}, function(response) {
                $('#elec_usage_level_buttons').html(response.html);
                if ($('#elec_usage_level').val()) {
                    var elec_usage_level = $('#elec_usage_level').val();
                    $('.elec-usages #' + elec_usage_level).trigger( 'click' );
                }
            }, "json");
            $.post("/tools/get_usage_level", {plan_type: 'Gas', customer_type: customer_type, version: 5}, function(response) {
                $('#gas_usage_level_buttons').html(response.html);
                if ($('#gas_usage_level').val()) {
    	            var gas_usage_level = $('#gas_usage_level').val();
                    $('.gas-usages #' + gas_usage_level).trigger( 'click' );
                }
            }, "json");
            if ($('#is_soho').val() == 1) {
                $('#SOHO').trigger( 'click' );
            } else {
                $('#' + customer_type).trigger( 'click' );
            }
        }
        $(document).on('click', '.looking-for', function() {
            $('.looking-for').removeClass('active');
			$(this).addClass('active');
            var id = $(this).prop('id');
            if (id == 'MoveIn') {
                $('#looking_for').val('Move Properties');
                $('#move_in_date_field').show();
            } else {
                $('#looking_for').val('Compare Plans');
                $('#move_in_date_field').hide();
                $('#move_in_date').val('');
                $('#elec_transfer').hide();
                $('#gas_transfer').hide();
                if ($('#plan_type').val() == 'Elec' || $('#plan_type').val() == 'Dual') {
                    $('#elec_transfer').show();
                }
                if ($('#plan_type').val() == 'Gas' || $('#plan_type').val() == 'Dual') {
                    $('#gas_transfer').show();
                }
            }
        });
        if ($('#looking_for').val()) {
	        var looking_for = $('#looking_for').val();
	        var id = 'Transfer';
	        if (looking_for == 'Move Properties') {
    	        id = 'MoveIn';
	        }
	        $('#' + id).trigger( 'click' );
        }
        $(document).on('click', '.plan-type,.elec-recent-bill-choices .choice,.gas-recent-bill-choices .choice', function() {
			$('.hidden-field').hide();
			var plan_type = $('#plan_type').val();
			switch(plan_type) {
				case 'Dual':
					$('#elec_recent_bill_field').show();
					$('#gas_recent_bill_field').show();
					$('#recent_bill_field').show();
					if ( $('#elec_recent_bill').val() == 'Yes' ) {
						$('.e-y').show();
						if ($('#elec_supplier').val() == '' && $('#elec_supplier2').val()) {
							var elec_supplier = $('#elec_supplier2').val();
							$('#elec_supplier').val(elec_supplier);
						}
					}
					else if ( $('#elec_recent_bill').val() == 'No') {
						$('.e-n').show();
						$('.radio-hidden').hide();
						if ($('#elec_supplier2').val() == '' && $('#elec_supplier').val()) {
							var elec_supplier = $('#elec_supplier').val();
							$('#elec_supplier2').val(elec_supplier);
						}
					}
					if ( $('#gas_recent_bill').val() == 'Yes' ) {
						$('.g-y').show();
						if ($('#gas_supplier').val() == '' && $('#gas_supplier2').val()) {
							var gas_supplier = $('#gas_supplier2').val();
							$('#gas_supplier').val(gas_supplier);
						}
					}
					else if ( $('#gas_recent_bill').val() == 'No') {
						$('.g-n').show();
						$('.radio-hidden').hide();
						if ($('#gas_supplier2').val() == '' && $('#gas_supplier').val()) {
							var gas_supplier = $('#gas_supplier').val();
							$('#gas_supplier2').val(gas_supplier);
						}
					}
					selec_tariff();
				break;
				case 'Elec':
					$('#elec_recent_bill_field').show();
					$('#gas_recent_bill_field').hide();
					$('#recent_bill_field').show();
					if ( $('#elec_recent_bill').val() == 'Yes' ) {
						$('.e-y').show();
						if ($('#elec_supplier').val() == '' && $('#elec_supplier2').val()) {
							var elec_supplier = $('#elec_supplier2').val();
							$('#elec_supplier').val(elec_supplier);
						}
						var nmi = $('#nmi').val();
						if (nmi.substring(0, 2) == '20') {
							//$('#summer_winter_fields').show();
							//$( "span.peak-des" ).text('Summer Peak');
						}
						else {
							//$('#summer_winter_fields').hide();
							//$('span.peak-des').text('Peak');
							$('#elec_winter_peak').val('');
							$('#elec_billing_start').val('');
						}
					}
					else if ( $('#elec_recent_bill').val() == 'No') {
						$('.e-n').show();
						$('.radio-hidden').hide();
						if ($('#elec_supplier2').val() == '' && $('#elec_supplier').val()) {
							var elec_supplier = $('#elec_supplier').val();
							$('#elec_supplier2').val(elec_supplier);
						}
						//$('#summer_winter_fields').hide();
						//$('span.peak-des').text('Peak');
						$('#elec_winter_peak').val('');
						$('#elec_billing_start').val('');
					}
					selec_tariff();
				break;
				case 'Gas':
					$('#elec_recent_bill_field').hide();
					$('#gas_recent_bill_field').show();
					$('#recent_bill_field').show();
					if ( $('#gas_recent_bill').val() == 'Yes' ) {
						$('.g-y').show();
						if ($('#gas_supplier').val() == '' && $('#gas_supplier2').val()) {
							var gas_supplier = $('#gas_supplier2').val();
							$('#gas_supplier').val(gas_supplier);
						}
					}
					else if ( $('#gas_recent_bill').val() == 'No') {
						$('.g-n').show();
						$('.radio-hidden').hide();
						if ($('#gas_supplier2').val() == '' && $('#gas_supplier').val()) {
							var gas_supplier = $('#gas_supplier').val();
							$('#gas_supplier2').val(gas_supplier);
						}
					}
				break;
				default:
				break;
			}
			if ($('#state').val() && $('#nmi').val() && $('#tariff_parent').val() && $('#elec_recent_bill').val() == 'Yes') {
				$('#elec_meter_type_fields').show()
			}
			else {
				$('#elec_meter_type_fields').hide();
				$('#elec_meter_type_fields').find('input:text').val('');
				$('input[name="elec_meter_type"]').prop("checked", false);
			}
		});
		if ($('#plan_type').val()) {
	        var plan_type = $('#plan_type').val();
	        $('#' + plan_type).trigger( 'click' );
        }
        $(document).on('click', '.elec-current-discount-choices .choice,.gas-current-discount-choices .choice', function() {
            $('.elec-current-discount-yes').hide();
            if ( $('#elec_current_discount_choice').val() == 'Yes' ) {
                $('.elec-current-discount-yes').show();
            }
            $('.gas-current-discount-yes').hide();
            if ( $('#gas_current_discount_choice').val() == 'Yes' ) {
                $('.gas-current-discount-yes').show();
            }
        });
        if ($('#is_broadband').val() == 1) {
	        $('#Broadband').trigger( 'click' );
        }
        $('#move_in_date').datepicker({ dateFormat: 'dd/mm/yy', minDate: 0, });
		if ($('#elec_recent_bill').val()) {
			var elec_recent_bill = $('#elec_recent_bill').val();
			$('.elec-recent-bill-choices #' + elec_recent_bill).trigger( 'click' );
		}
		if ($('#elec_current_discount_choice').val()) {
    		var elec_current_discount_choice = $('#elec_current_discount_choice').val();
            $('.elec-current-discount-choices #' + elec_current_discount_choice).trigger( 'click' );
		}
		$(document).on('click', '#move_in_date_not_sure', function() {
			if ($(this).is(':checked')) {
				$('#move_in_date').val('');
			}
		});
		$('#elec_billing_start').datepicker({ dateFormat: 'dd/mm/yy', maxDate: 0, });
        $('#singlerate_cs_billing_start').datepicker({ dateFormat: 'dd/mm/yy', maxDate: 0, });
        $('#singlerate_cl1_cs_billing_start').datepicker({ dateFormat: 'dd/mm/yy', maxDate: 0, });
        $('#timeofuse_cs_billing_start').datepicker({ dateFormat: 'dd/mm/yy', maxDate: 0, });
        $('#timeofuse_cl1_cs_billing_start').datepicker({ dateFormat: 'dd/mm/yy', maxDate: 0, });
		$(document).on('click', '.elec-usages .usage', function() {
			$(this).addClass('active').siblings().removeClass('active');
            var id = $(this).prop('id');
			$('input[name="elec_usage_level"]').prop('value',id);
        });
        if ($('#gas_recent_bill').val()) {
			var gas_recent_bill = $('#gas_recent_bill').val();
			$('.gas-recent-bill-choices #' + gas_recent_bill).trigger( 'click' );
		}
		if ($('#gas_current_discount_choice').val()) {
    		var gas_current_discount_choice = $('#gas_current_discount_choice').val();
            $('.gas-current-discount-choices #' + gas_current_discount_choice).trigger( 'click' );
		}
        $(document).on('click', '.gas-usages .usage', function() {
			$(this).addClass('active').siblings().removeClass('active');
            var id = $(this).prop('id');
			$('input[name="gas_usage_level"]').prop('value',id);
        });
        $('#gas_billing_start').datepicker({ dateFormat: 'dd/mm/yy', maxDate: 0, });
        $('.radio-hidden').hide();
        $(document).on('click', 'input[name="elec_meter_type"]', function() {
			$('.radio-hidden').hide();
			$(this).parent().siblings('.radio-hidden').show();
		});
		if ($('input[name="elec_meter_type"]:checked').val()) {
			$('input[name="elec_meter_type"]:checked').trigger( 'click' );
		}
		$('#nmi').blur(function() {
        	var nmi = $('#nmi').val().replace(/[()]|\s|-/g, '');
        	if (nmi.length != 11) {
        		$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Make sure the NMI is 11 digits</div></div></div><div class="webform_error_bottom"></div></div>');
        		return false;
        	}
        	else {
        		var total = 0;
        		for ( var i = 0; i < 10; i++ ) {
					var str = nmi[i].charCodeAt(0).toString();
					if (i%2 == 0) {
						for ( var j = 0; j < str.length; j++ ) {
							total += eval(str[j]);
						}
					}
					else {
						var str2 = (str*2).toString();
						for ( var k = 0; k < str2.length; k++ ) {
							total += eval(str2[k]);
						}
					}
				}
				total += eval(nmi[10]);
				if (Math.round(total / 10) * 10 != total) {
					$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid NMI</div></div></div><div class="webform_error_bottom"></div></div>');
        			return false;
				}
				else {
					if ($('#elec_recent_bill').val() == 'Yes' && nmi.substring(0, 2) == '20') {
						//$('#summer_winter_fields').show();
						//$( "span.peak-des" ).text('Summer Peak');
					}
					else {
						//$('#summer_winter_fields').hide();
						//$('span.peak-des').text('Peak');
						$('#elec_winter_peak').val('');
						$('#elec_billing_start').val('');
					}
					$('#nmi').val(nmi);
					tariff_lookup('tariff1', 'both');
					$('#tariff1').val('').combobox("refresh");
					$('#tariff_parent').val('');
					$('.delete-tariff2').trigger("click");
					$('.delete-tariff3').trigger("click");
					$('.delete-tariff4').trigger("click");
				}
        	}
		});
		if ($('#nmi').val()) {
			tariff_lookup('tariff1', 'both');
			$('#tariff1').val('').combobox("refresh");
			/*
			$('#tariff_parent').val('');
			*/
			var nmi = $('#nmi').val();
			if ($('#elec_recent_bill').val() == 'Yes' && nmi.substring(0, 2) == '20') {
				//$('#summer_winter_fields').show();
				//$('span.peak-des').text('Summer Peak');
			}
			else {
				//$('#summer_winter_fields').hide();
				//$('span.peak-des').text('Peak');
				$('#elec_winter_peak').val('');
				$('#elec_billing_start').val('');
			}
		}
		$('#tariff1').change(function() {
			$(this).parent().find('.webform_error').remove();
			$('.delete-tariff2').trigger("click");
			$('.delete-tariff3').trigger("click");
			$('.delete-tariff4').trigger("click");
			var tariff1_value = $('#tariff1').val();
			var tariff1_arr = tariff1_value.split('|');
			if (tariff1_arr[2] == 0) {
				$('#tariff_parent').val(tariff1_value);
				$('.add-tariff2').show();
				/*
				if (tariff1_arr[3] == 'Solar') {
					$('.add-tariff2').hide();
				}
				else {
					$('.add-tariff2').show();
				}
				*/
			}
			else {
				$('#tariff_parent').val('');
				$('.add-tariff2').show();
			}
			selec_tariff();
			$('#solar_rebate_scheme').val('');
			if (tariff1_arr[4]) {
				if (tariff1_arr[4].indexOf("/") >= 0) {
    				/*
					$("#solar_rebate_scheme_modal").modal({
						backdrop: 'static',
						keyboard: false,
					});
					*/
					$('#solar_rebate_scheme').val('SFiT');
				}
				var state = $('#state').val();
				if ((state == 'VIC' && tariff1_arr[4] == 'TFIT') || (state == 'NSW' && tariff1_arr[4] == 'PFiT60') || (state == 'NSW' && tariff1_arr[4] == 'PFiT20')) {
    				/*
					$("#solar_rebate_scheme_modal2").modal({
						backdrop: 'static',
						keyboard: false,
					});
					*/
				}
			}
		});
		$('.add-tariff2').click(function(){
			if ($('#tariff1').val()) {
				load_tariff2();
			}
			else {
				setTimeout(function(){
					load_tariff2();
				}, 1000);
			}
		});
		$('#tariff2').change(function(){
			$('.delete-tariff3').trigger("click");
			var tariff2_value = $('#tariff2').val();
			var tariff2_arr = tariff2_value.split('|');
			if (tariff2_arr[2] == 0) {
				$('#tariff_parent').val(tariff2_value);
			}
			selec_tariff();
			$('#solar_rebate_scheme').val('');
			if (tariff2_arr[4]) {
				if (tariff2_arr[4].indexOf("/") >= 0) {
    				/*
					$("#solar_rebate_scheme_modal").modal({
						backdrop: 'static',
						keyboard: false,
					});
					*/
					$('#solar_rebate_scheme').val('SFiT');
				}
				var state = $('#state').val();
				if ((state == 'VIC' && tariff2_arr[4] == 'TFiT') || (state == 'NSW' && tariff2_arr[4] == 'PFiT60') || (state == 'NSW' && tariff2_arr[4] == 'PFiT20')) {
    				/*
					$("#solar_rebate_scheme_modal2").modal({
						backdrop: 'static',
						keyboard: false,
					});
					*/
				}
			}
		});
		$('.delete-tariff2').click(function(){
    		var tariff2_value = $('#tariff2').val();
			var tariff2_arr = tariff2_value.split('|');
			if (tariff2_arr[2] == 0) {
    			$('#tariff_parent').val('');
			}
			$('#tariff2').val('');
			$('#tariff2_field').hide();
			$('#tariff2').empty().append('<option value="">Tariff</option>');
			selec_tariff();
		});
		window.load_tariff2 = function() {
			var state = $('#state').val();
			var tariff1_value = $('#tariff1').val();
			var tariff1_arr = tariff1_value.split('|');
			if (tariff1_arr[2] == 1) {
			    if (state == 'NSW' || state == 'QLD') {
			    	tariff_lookup('tariff2', 'both');
			    }
			    else {
			    	tariff_lookup('tariff2', 'parent'); // VIC, CA
			    }
			}
			else {
			    if (state == 'NSW' || state == 'QLD') {
			    	tariff_lookup('tariff2', 'child');
			    }
			    else {
			    	tariff_lookup('tariff2', 'child'); // VIC, CA
			    }
			}
			if (state == 'NSW' || state == 'QLD') {
			    $('.add-tariff3').show();
			}
			else {
			    $('.add-tariff3').hide();
			}
			if (!$('#tariff2').val()) {
			    $('#tariff2').val('').combobox("refresh");
			}
			$('#tariff2_field').show();
		}
		$('.add-tariff3').click(function(){
			if ($('#tariff2').val()) {
				load_tariff3();
			}
			else {
				setTimeout(function(){
					load_tariff3();
				}, 1000);
			}
		});
		$('#tariff3').change(function(){
    		$('.delete-tariff4').trigger("click");
			var tariff3_value = $(this).val();
			var tariff3_arr = tariff3_value.split('|');
			if (tariff3_arr[2] == 0) {
				$('#tariff_parent').val(tariff3_value);
			}
			selec_tariff();
			$('#solar_rebate_scheme').val('');
			if (tariff3_arr[4]) {
				if (tariff3_arr[4].indexOf("/") >= 0) {
    				/*
					$("#solar_rebate_scheme_modal").modal({
						backdrop: 'static',
						keyboard: false,
					});
					*/
					$('#solar_rebate_scheme').val('SFiT');
				}
				var state = $('#state').val();
				if ((state == 'VIC' && tariff3_arr[4] == 'TFIT') || (state == 'NSW' && tariff3_arr[4] == 'PFiT60') || (state == 'NSW' && tariff3_arr[4] == 'PFiT20')) {
    				/*
					$("#solar_rebate_scheme_modal2").modal({
						backdrop: 'static',
						keyboard: false,
					});
					*/
				}
			}
		});
		$('.delete-tariff3').click(function(){
    		var tariff3_value = $('#tariff3').val();
			var tariff3_arr = tariff3_value.split('|');
			if (tariff3_arr[2] == 0) {
                $('#tariff_parent').val('');
			}
			$('#tariff3').val('');
			$('#tariff3_field').hide();
			$('#tariff3').empty().append('<option value="">Tariff</option>');
			selec_tariff();
		});
		window.load_tariff3 = function() {
			var state = $('#state').val();
			var tariff2_value = $('#tariff2').val();
			var tariff2_arr = tariff2_value.split('|');
			if (tariff2_arr[2] == 1) {
			    if (state == 'NSW' || state == 'QLD') {
			    	var tariff1_value = $('#tariff1').val();
			    	var tariff1_arr = tariff1_value.split('|');
			    	if (tariff1_arr[2] == 1) {
			    		tariff_lookup('tariff3', 'parent');
			    	}
			    	else {
			    		tariff_lookup('tariff3', 'child');
			    	}
			    }
			}
			else {
			    if (state == 'NSW' || state == 'QLD') {
			    	tariff_lookup('tariff3', 'child');
			    }
			}
			if (state == 'NSW' || state == 'QLD') {
			    $('.add-tariff4').show();
			}
			else {
			    $('.add-tariff4').hide();
			}
			if (!$('#tariff3').val()) {
			    $('#tariff3').val('').combobox("refresh");
			}
			$('#tariff3_field').show();
		}
		$('.add-tariff4').click(function(){
			if ($('#tariff4').val()) {
				load_tariff4();
			}
			else {
				setTimeout(function(){
					load_tariff4();
				}, 1000);
			}
		});
		$('#tariff4').change(function(){
			var tariff4_value = $(this).val();
			var tariff4_arr = tariff4_value.split('|');
			if (tariff4_arr[2] == 0) {
				$('#tariff_parent').val(tariff4_value);
			}
			selec_tariff();
			$('#solar_rebate_scheme').val('');
			if (tariff4_arr[4]) {
				if (tariff4_arr[4].indexOf("/") >= 0) {
    				/*
					$("#solar_rebate_scheme_modal").modal({
						backdrop: 'static',
						keyboard: false,
					});
					*/
					$('#solar_rebate_scheme').val('SFiT');
				}
				var state = $('#state').val();
				if ((state == 'VIC' && tariff4_arr[4] == 'TFIT') || (state == 'NSW' && tariff4_arr[4] == 'PFiT60') || (state == 'NSW' && tariff4_arr[4] == 'PFiT20')) {
    				/*
					$("#solar_rebate_scheme_modal2").modal({
						backdrop: 'static',
						keyboard: false,
					});
					*/
				}
			}
		});
		$('.delete-tariff4').click(function(){
    		var tariff4_value = $('#tariff4').val();
			var tariff4_arr = tariff4_value.split('|');
			if (tariff4_arr[2] == 0) {
                $('#tariff_parent').val('');
			}
			$('#tariff4').val('');
			$('#tariff4_field').hide();
			$('#tariff4').empty().append('<option value="">Tariff</option>');
			selec_tariff();
		});
		window.load_tariff4 = function() {
			var state = $('#state').val();
			var tariff3_value = $('#tariff3').val();
			var tariff3_arr = tariff3_value.split('|');
			if (tariff3_arr[2] == 1) {
			    if (state == 'NSW' || state == 'QLD') {
			    	var tariff1_value = $('#tariff1').val();
			    	var tariff1_arr = tariff1_value.split('|');
			    	if (tariff1_arr[2] == 1) {
			    		tariff_lookup('tariff4', 'parent');
			    	}
			    	else {
			    		tariff_lookup('tariff4', 'child');
			    	}
			    }
			}
			else {
			    if (state == 'NSW' || state == 'QLD') {
    			    tariff_lookup('tariff4', 'child');
                }
			}
			if (!$('#tariff4').val()) {
			    $('#tariff4').val('').combobox("refresh");
			}
			$('#tariff4_field').show();
		}
		$(document).on('click', '#step1_solar_rebate_scheme_form .continue', function(event) {
			$('#solar_rebate_scheme').val('SFiT');
			$('#solar_rebate_scheme_modal').modal('hide');
			event.preventDefault();
		});
		$(document).on('click', '#step1_solar_rebate_scheme_form2 .continue', function(event) {
			$('#solar_rebate_scheme_modal2').modal('hide');
			event.preventDefault();
		});
		
		$('#renant_owner').change(function() {
			var renant_owner = $(this).val();
			var customer_type = $("#customer_type").val();
			var plan_type = $("#plan_type").val();
			$(".battery-storage-solution").hide();
			$(".battery-storage-solar-solution").hide();
			if (plan_type == 'Dual' || plan_type == 'Elec') {
                if (renant_owner == 'Renter') {
        			if (customer_type == 'RES') {
            			if ($("#solar_fields").is(':visible')) {
            			    $(".battery-storage-solution").show();
                        }
        			} else if (customer_type == 'SME') {
            			if ($("#solar_fields").is(':visible')) {
            			    $(".battery-storage-solution").show();
                        } else {
                            $(".battery-storage-solar-solution").show();
                        }
        			}
    			} else if (renant_owner == 'Owner') {
        			if (customer_type == 'RES') {
            			if ($("#solar_fields").is(':visible')) {
                			$(".battery-storage-solution").show();
                        } else {
            			    $(".battery-storage-solar-solution").show();
                        }
        			} else if (customer_type == 'SME') {
            			if ($("#solar_fields").is(':visible')) {
            			    $(".battery-storage-solution").show();
                        } else {
                            $(".battery-storage-solar-solution").show();
                        }
        			}
    			}
			}
		});
		
		$(document).on('click', '#other_number', function() {
        	if ($(this).prop('checked')) {
        		$('#phone').show();
        	}
        	else {
        		$('#phone').hide();
        	}
		});
		if ($('#other_number').is(':checked')) {
			$('#phone').show();
        }
        else {
        	$('#phone').hide();
        }
		//$("#mobile").mask("(99) 9999-9999");
        //$("#phone").mask("(99) 9999-9999");
        $(document).on('click', '#step1 .continue', function(event) {
    		$('#no_sale_section').hide();
    		$('#lead_action').val('');
    		
			$('.webform_error').remove();
			var e = false;
			
			/*
			var sales_rep_name = $('#sales_rep_name').val();
			if (!sales_rep_name) {
			    $("#sales_rep_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select sales rep name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			*/
			var first_name = $('#first_name').val();
			if (!first_name) {
			    $("#first_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your first name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			/*
			var surname = $('#surname').val();
			if (!surname) {
			    $("#surname").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your surname</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			*/
			
			var mobile = $('#mobile').val().replace(/[()]|\s|-/g, '');
			if (mobile) {
    			$('#mobile').val(mobile);
    			if (mobile.length != 10 || !(/^(04)\d{8}$/.test(mobile))) {
    				$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var home_phone = $('#home_phone').val().replace(/[()]|\s|-/g, '');
			if (home_phone) {
                $('#home_phone').val(home_phone);
    			if (home_phone.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(home_phone))) {
    				$("#home_phone").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your home phone</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var work_number = $('#work_number').val().replace(/[()]|\s|-/g, '');
			if (work_number) {
    			$('#work_number').val(work_number);
    			if (work_number.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(work_number))) {
    				$("#work_number").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your work number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			if (!mobile && !home_phone && !work_number) {
    			$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			 e = e || true;
			}
			
            var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            var email = $('#email').val();
            if (email.length > 0) {
                if (!reg.test(email)) {
                    $('#email').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your email</div></div></div><div class="webform_error_bottom"></div></div>');
                    e = e || true;
                }
            }
			var plan_type = $('#plan_type').val();
			if (!plan_type) {
			    $("#plan_type").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select which product to compare</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var customer_type = $('#customer_type').val();
			if (!customer_type) {
			    $("#customer_type").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your comparison type</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var elec_recent_bill = $('#elec_recent_bill').val();
			var gas_recent_bill = $('#gas_recent_bill').val();
			if (plan_type && (elec_recent_bill || gas_recent_bill)) {
			    if (elec_recent_bill == 'Yes') {
			        if (plan_type == 'Dual' || plan_type == 'Elec') {
			            if (!$('#elec_supplier').val()) {
			                $('#elec_supplier').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#elec_billing_days').val()) {
			                $('#elec_billing_days').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter number of days in your billing cycle</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            var elec_meter_type = $('input[name="elec_meter_type"]:checked').val();
			            if (typeof(elec_meter_type) != 'undefined') {
			                if (elec_meter_type == 'Single Rate') {
			                    if (!$('#singlerate_peak').val()) {
			                        $('#singlerate_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + CL1') {
			                	if (!$('#singlerate_cl1_peak').val()) {
			                        $('#singlerate_cl1_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_cl1').val()) {
			                        $('#singlerate_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + CL2') {
			                	if (!$('#singlerate_cl2_peak').val()) {
			                        $('#singlerate_cl2_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_cl2').val()) {
			                        $('#singlerate_cl2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 2 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + CL1 + CL2') {
			                	if (!$('#singlerate_cl1_cl2_peak').val()) {
			                        $('#singlerate_cl1_cl2_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#singlerate_2_cl1').val()) {
			                        $('#singlerate_2_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_2_cl2').val()) {
			                        $('#singlerate_2_cl2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 2 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + Climate Saver') {
			                	if (!$('#singlerate_cs_peak').val()) {
			                        $('#singlerate_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#singlerate_cs').val()) {
			                        $('#singlerate_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_cs_billing_start').val()) {
			                        $('#singlerate_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + CL1 + Climate Saver') {
			                	if (!$('#singlerate_cl1_cs_peak').val()) {
			                        $('#singlerate_cl1_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#singlerate_3_cl1').val()) {
			                        $('#singlerate_3_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_3_cs').val()) {
			                        $('#singlerate_3_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#singlerate_cl1_cs_billing_start').val()) {
			                        $('#singlerate_cl1_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use') {
			                	if (!$('#timeofuse_peak').val()) {
			                        $('#timeofuse_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_offpeak').val()) {
			                        $('#timeofuse_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use + Climate Saver') {
			                	if (!$('#timeofuse_cs_peak').val()) {
			                        $('#timeofuse_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_cs_offpeak').val()) {
			                        $('#timeofuse_cs_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_cs').val()) {
			                        $('#timeofuse_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_cs_billing_start').val()) {
			                        $('#timeofuse_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use + CL1 + Climate Saver') {
			                	if (!$('#timeofuse_cl1_cs_peak').val()) {
			                        $('#timeofuse_cl1_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_cl1_cs_offpeak').val()) {
			                        $('#timeofuse_cl1_cs_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_cl1').val()) {
			                        $('#timeofuse_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_2_cs').val()) {
			                        $('#timeofuse_2_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_cl1_cs_billing_start').val()) {
			                        $('#timeofuse_cl1_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use (Tariff 12)') {
			                	if (!$('#timeofuse_tariff12_peak').val()) {
			                        $('#timeofuse_tariff12_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_tariff12_offpeak').val()) {
			                        $('#timeofuse_tariff12_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use (Tariff 13)') {
				                if (!$('#timeofuse_tariff13_peak').val()) {
			                        $('#timeofuse_tariff13_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_tariff13_offpeak').val()) {
			                        $('#timeofuse_tariff13_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Flexible Pricing') {
				                if (!$('#flexible_peak').val()) {
			                        $('#flexible_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#flexible_offpeak').val()) {
			                        $('#flexible_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			            }
			            var nmi = $('#nmi').val().replace(/[()]|\s|-/g, '');
			            //if (nmi.length > 0) {
			            	if (nmi.length != 11) {
								$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Make sure the NMI is 11 digits</div></div></div><div class="webform_error_bottom"></div></div>');
								e = e || true;
							}
							else {
								var total = 0;
								for ( var i = 0; i < 10; i++ ) {
									var str = nmi[i].charCodeAt(0).toString();
									if (i%2 == 0) {
										for ( var j = 0; j < str.length; j++ ) {
											total += eval(str[j]);
										}
									}
									else {
										var str2 = (str*2).toString();
										for ( var k = 0; k < str2.length; k++ ) {
											total += eval(str2[k]);
										}
									}
								}
								total += eval(nmi[10]);
								if (Math.round(total / 10) * 10 != total) {
									$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid NMI</div></div></div><div class="webform_error_bottom"></div></div>');
									e = e || true;
								}
								else {
									$('#nmi').val(nmi);
								}
							}
							var tariff1 = $('#tariff1').val();
							var tariff2 = $('#tariff2').val();
							var tariff3 = $('#tariff3').val();
							if (!tariff1 && !tariff2 && !tariff3) {
								$("#tariff1").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select a parent tariff.</div></div></div><div class="webform_error_bottom"></div></div>');
								e = e || true;
							}
							var solar_specific_plan = false
							var tariff1_value = $('#tariff1').val();
							if (tariff1_value) {
								var tariff1_arr = tariff1_value.split('|');
								if (tariff1_arr[3] == 'Solar') {
									solar_specific_plan = true;
								}
							}
							var tariff2_value = $('#tariff2').val();
							if (tariff2_value) {
								var tariff2_arr = tariff2_value.split('|');
								if (tariff2_arr[3] == 'Solar') {
									solar_specific_plan = true;
								}
							}
							var tariff3_value = $('#tariff3').val();
							if (tariff3_value) {
								var tariff3_arr = tariff3_value.split('|');
								if (tariff3_arr[3] == 'Solar') {
									solar_specific_plan = true;
								}
							}
							var tariff4_value = $('#tariff4').val();
							if (tariff4_value) {
								var tariff4_arr = tariff4_value.split('|');
								if (tariff4_arr[3] == 'Solar') {
									solar_specific_plan = true;
								}
							}
							if (solar_specific_plan) {
								var solar_generated = $("#solar_generated").val();
								if (!solar_generated) {
									$("#solar_generated").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">How much solar was generated?</div></div></div><div class="webform_error_bottom"></div></div>');
									e = e || true;
								}
								
								var state = $('#state').val();
								var nmi_check = nmi.substring(0, 2);
								if (state == 'QLD' && (nmi_check == 'QB' || nmi_check == '31')) {
        				            alert("Please ensure you use the QLD Alinta Solar Calculator for this comparison as you are completing a solar, bill to bill comparison in Queensland");
								}
							}
			            //}
			        }
			        $('#elec_supplier2').val('');
			        $('.elec-usages').removeClass('active');
					$('#elec_usage_level').val('');
			    }
			    else if (elec_recent_bill == 'No') {
			        if (plan_type == 'Dual' || plan_type == 'Elec') {
			            if (!$('#elec_supplier2').val()) {
			                $('#elec_supplier2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            var elec_usage_level = $('#elec_usage_level').val();
						if ( !elec_usage_level ) {
			            	$('#elec_usage_level').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your electricity usage</div></div></div><div class="webform_error_bottom"></div></div>');
							e = e || true;
						}
						var nmi = $('#nmi').val().replace(/[()]|\s|-/g, '');
			            //if (nmi.length > 0) {
			            	if (nmi.length != 11) {
								$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Make sure the NMI is 11 digits</div></div></div><div class="webform_error_bottom"></div></div>');
								e = e || true;
							}
							else {
								var total = 0;
								for ( var i = 0; i < 10; i++ ) {
									var str = nmi[i].charCodeAt(0).toString();
									if (i%2 == 0) {
										for ( var j = 0; j < str.length; j++ ) {
											total += eval(str[j]);
										}
									}
									else {
										var str2 = (str*2).toString();
										for ( var k = 0; k < str2.length; k++ ) {
											total += eval(str2[k]);
										}
									}
								}
								total += eval(nmi[10]);
								if (Math.round(total / 10) * 10 != total) {
									$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid NMI</div></div></div><div class="webform_error_bottom"></div></div>');
									e = e || true;
								}
								else {
									$('#nmi').val(nmi);
								}
							}
							var tariff1 = $('#tariff1').val();
							var tariff2 = $('#tariff2').val();
							var tariff3 = $('#tariff3').val();
							if (!tariff1 && !tariff2 && !tariff3) {
								$("#tariff1").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select a parent tariff.</div></div></div><div class="webform_error_bottom"></div></div>');
								e = e || true;
							}
			            //}
			        }
			        $('#elec_supplier').val('');
			        $('#elec_billing_days').val('');
			        $('#elec_spend').val('');
			        $('#elec_meter_type_fields').find('input:text').val('');
			        $('input[name="elec_meter_type"]').prop("checked", false);
			    }
			    if (gas_recent_bill == 'Yes') {
			        if (plan_type == 'Dual' || plan_type == 'Gas') {    
			            if (!$('#gas_supplier').val()) {
			                $('#gas_supplier').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#gas_billing_days').val()) {
			                $('#gas_billing_days').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter number of days in your billing cycle.</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#gas_billing_start').val()) {
			                $('#gas_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select billing start date.</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#gas_peak').val()) {
			                $('#gas_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        $('#gas_supplier2').val('');
			        $('.gas-usages').removeClass('active');
					$('#gas_usage_level').val('');
			    }
			    else if (gas_recent_bill == 'No') {
			        if (plan_type == 'Dual' || plan_type == 'Gas') {
			            if ( !$('#gas_supplier2').val() ) {
			                $('#gas_supplier2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            var gas_usage_level = $('#gas_usage_level').val();
						if ( !gas_usage_level ) {
			            	$('#gas_usage_level').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your gas usage</div></div></div><div class="webform_error_bottom"></div></div>');
							e = e || true;
						}
			        }
			        $('#gas_supplier').val('');
			        $('#gas_billing_days').val('');
					$('#gas_billing_start').val('');
					$('#gas_spend').val('');
					$('#gas_peak').val('');
					$('#gas_off_peak').val('');
			    }
			}
			var postcode = $('#postcode').val().replace(/\s/g, '');
			var suburb  = $('#suburb').val();
			if ((postcode.length != 4) || (/[^0-9-()+\s]/.test(postcode))) {
				$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your postcode</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;
			}
			else if(!(/^[2-5]/.test(postcode))) {
				$('#postcode').addClass("error");
				$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Sorry we do not service that area</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;	
			}
			else if ( !suburb ) {
			    $('#suburb').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select suburb</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			else {
			    $('#postcode').val(postcode);
				$('#postcode').removeClass("error");
			}
			
			/*
            if ($("#solar_fields").is(':visible')) {
				var solar_generated = $("#solar_generated").val();
				if (!solar_generated) {
					$("#solar_generated").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">How much solar was generated?</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				}
			}
			*/
			
			var renant_owner = $('#renant_owner').val();
			if (!renant_owner) {
			    $("#renant_owner").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Are you the Tenant or the Owner?</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			if ($('.battery-storage-solution').is(':visible')) {
    			var battery_storage_solution = $('#battery_storage_solution').val();
    			if (!battery_storage_solution) {
    			    $("#battery_storage_solution").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Are you interested to know more about our battery storage solution?</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
            }
			if ($('.battery-storage-solar-solution').is(':visible')) {
    			var battery_storage_solar_solution = $('#battery_storage_solar_solution').val();
    			if (!battery_storage_solar_solution) {
    			    $("#battery_storage_solar_solution").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Are you interested to know more about our battery storage & solar solution?</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var elec_current_discount_choice = $('#elec_current_discount_choice').val();
			if ($('#elec_transfer').is(':visible') && elec_current_discount_choice == 'Yes') {
    			if ($('#elec_current_discount').val() == '') {
        			$("#elec_current_discount").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your current electricity discount</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
    			if ($('#elec_current_discount_type').val() == '') {
        			$("#elec_current_discount_type").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current electricity discount type</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
    			if ($('#elec_current_discount_applies').val() == '') {
        			$("#elec_current_discount_applies").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current electricity discount applies</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var gas_current_discount_choice = $('#gas_current_discount_choice').val();
			if ($('#gas_transfer').is(':visible') && gas_current_discount_choice == 'Yes') {
    			if ($('#gas_current_discount').val() == '') {
        			$("#gas_current_discount").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your current gas discount</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
    			if ($('#gas_current_discount_type').val() == '') {
        			$("#gas_current_discount_type").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current gas discount type</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
    			if ($('#gas_current_discount_applies').val() == '') {
        			$("#gas_current_discount_applies").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current gas discount applies</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			if (!$('#sid').val()) {
				/*
			    if ($('#customer_type').val() == 'SME') {
				    var business_name = $('#business_name').val();
					if ((business_name.length < 3) || (/\d/.test(business_name))) {
						$('#business_name').addClass("error");
						$('#business_name').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your business name</div></div></div><div class="webform_error_bottom"></div></div>');
						e = e || true;
					}
					else {
						$('#business_name').removeClass("error");
						e = e || false;
					}
			    }
			    var first_name = $('#first_name').val();
				if ((first_name.length < 3) || (/\d/.test(first_name))) {
					$('#first_name').addClass("error");
					$('#first_name').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your first name</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				}
				else {
					$('#first_name').removeClass("error");
					e = e || false;
				}
				var surname = $('#surname').val();
				if ((surname.length < 3) || (/\d/.test(surname))) {
					$('#surname').addClass("error");
					$('#surname').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your last name</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				}
				else {
					$('#surname').removeClass("error");
					e = e || false;
				}
				var mobile = $('#mobile').val().replace(/[()]|\s|-/g, '');
				if ((mobile.length != 8 && mobile.length != 10) || !(/^(?!.*(\d)\1{4})\d{8,10}$/.test(mobile))) {
					$('#mobile').addClass("error");
					$('#mobile').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid number</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				}
				else if (!(/^(02|03|04|07|08)\d{8}$/.test(mobile)) && !(/^(1300|1800)\d{6}$/.test(mobile))) {
					$('#mobile').addClass("error");
					$('#mobile').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Oops! It looks like you forgot to put in your area code. (e.g. 03 for VIC)</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				}
				else {
					$('#mobile').val(mobile);
					$('#mobile').removeClass("error");
					e = e || false;
				}
				var phone = $('#phone').val().replace(/[()]|\s|-/g, '');
				if (phone.length > 0) {
					if ((phone.length != 8 && phone.length != 10) || !(/^(?!.*(\d)\1{4})\d{8,10}$/.test(phone))) {
						$('#phone').addClass("error");
						$('#phone').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid number</div></div></div><div class="webform_error_bottom"></div></div>');
						e = e || true;
					}
					else if (!(/^(02|03|04|07|08)\d{8}$/.test(phone)) && !(/^(1300|1800)\d{6}$/.test(phone))) {
						$('#phone').addClass("error");
						$('#phone').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Oops! It looks like you forgot to put in your area code. (e.g. 03 for VIC)</div></div></div><div class="webform_error_bottom"></div></div>');
						e = e || true;
					}
					else {
						$('#phone').val(phone);
						$('#phone').removeClass("error");
						e = e || false;
					}
				}
				var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				var email = $('#email').val();
				if (!reg.test(email)) {
					$('#email').addClass("error");
					$('#email').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your email</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				}
				else {
					$('#email').removeClass("error");
					e = e || false;
				}
				*/
			}
			/*
			if (!$('#term1').is(':checked')) {
				$('#term1').addClass("error");
				$('#term1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please agree to the terms and conditions</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;
			}
			else {
				$('#term1').removeClass("error");
				e = e || false;
			}
			*/
			
			if (!e) {
				$('#step1_error_message').html('');
				$('#processing').show();
				$.post("/v12/compare_save/1",$('#step1_form').serialize(),function(response) {
					window.location = "/v12/compare/2";
				});
			}
			else {
				$('#step1_error_message').html('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please fill out all required fields above</div></div></div><div class="webform_error_bottom"></div></div>');
				$('html,body').animate({'scrollTop': $('.webform_error:first').offset().top - 100}, 'slow');
			}
			event.preventDefault();
		});
		$(document).on('click', '#customer_details .comparison', function(event) {
    		$('#action').val('create-lead');
    		$('#no_sale_section').hide();
    		$('#lead_action').val('');
    		$('#lead_section').hide();
    		
			$('.webform_error').remove();
			if ($('#sid').val()) {
        		window.location = "/v12/compare/1";
        		return false;
    		}
			var e = false;
			var contact_code = $('#contact_code').val();
			if (!contact_code) {
			    $("#contact_code").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select contact code</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var campaign_id2 = $('#campaign_id2').val();
			if (!campaign_id2) {
			    $("#campaign_id2").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select Campaign</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var electrician_name = $('#electrician_name').val();
			if ($('#electrician_name_field').is(':visible') && !electrician_name) {
			    $("#electrician_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter electrician name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var agent_name = $('#agent_name').val();
			if (!agent_name) {
			    $("#agent_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter user name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var first_name = $('#first_name').val();
			if (!first_name) {
			    $("#first_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your first name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			/*
			var surname = $('#surname').val();
			if (!surname) {
			    $("#surname").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your surname</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			*/
			
			var mobile = $('#mobile').val().replace(/[()]|\s|-/g, '');
			if (mobile) {
    			$('#mobile').val(mobile);
    			if (mobile.length != 10 || !(/^(04)\d{8}$/.test(mobile))) {
    				$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var home_phone = $('#home_phone').val().replace(/[()]|\s|-/g, '');
			if (home_phone) {
                $('#home_phone').val(home_phone);
    			if (home_phone.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(home_phone))) {
    				$("#home_phone").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your home phone</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var work_number = $('#work_number').val().replace(/[()]|\s|-/g, '');
			if (work_number) {
    			$('#work_number').val(work_number);
    			if (work_number.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(work_number))) {
    				$("#work_number").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your work number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			if (!mobile && !home_phone && !work_number) {
    			$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			 e = e || true;
			}
			
			if (!e) {
				$('#processing').show();
				$.post("/v12/customer_details_save",$('#customer_details_form').serialize(),function(response) {
    				$('#processing').hide();
    				$('#sid').val(response);
				    window.location = "/v12/compare/1";
                });
			}
			else {
				$('html,body').animate({'scrollTop': $('.webform_error:first').offset().top - 100}, 'slow');
			}
			event.preventDefault();
        });
        $(document).on('click', '#customer_details .create-lead', function(event) {
    		$('#action').val('create-lead');
    		$('#no_sale_section').hide();
			$('.webform_error').remove();
			if ($('#sid').val()) {
        		$('#lead_section').show();
        		return false;
    		}
			var e = false;
			var contact_code = $('#contact_code').val();
			if (!contact_code) {
			    $("#contact_code").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select contact code</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var campaign_id2 = $('#campaign_id2').val();
			if (!campaign_id2) {
			    $("#campaign_id2").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select Campaign?</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var agent_name = $('#agent_name').val();
			if (!agent_name) {
			    $("#agent_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter user name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var first_name = $('#first_name').val();
			if (!first_name) {
			    $("#first_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your first name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			/*
			var surname = $('#surname').val();
			if (!surname) {
			    $("#surname").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your surname</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			*/
			
			var mobile = $('#mobile').val().replace(/[()]|\s|-/g, '');
			if (mobile) {
    			$('#mobile').val(mobile);
    			if (mobile.length != 10 || !(/^(04)\d{8}$/.test(mobile))) {
    				$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var home_phone = $('#home_phone').val().replace(/[()]|\s|-/g, '');
			if (home_phone) {
                $('#home_phone').val(home_phone);
    			if (home_phone.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(home_phone))) {
    				$("#home_phone").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your home phone</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var work_number = $('#work_number').val().replace(/[()]|\s|-/g, '');
			if (work_number) {
    			$('#work_number').val(work_number);
    			if (work_number.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(work_number))) {
    				$("#work_number").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your work number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			if (!mobile && !home_phone && !work_number) {
    			$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			 e = e || true;
			}
			
			if (!e) {
				$('#processing').show();
				$.post("/v12/customer_details_save",$('#customer_details_form').serialize(),function(response) {
    				$('#processing').hide();
				    $('#sid').val(response);
                    $('#lead_section').show();
                });
			}
			else {
				$('html,body').animate({'scrollTop': $('.webform_error:first').offset().top - 100}, 'slow');
			}
			event.preventDefault();
        });
        $(document).on('click', '#customer_details .no-sale', function(event) {
            $('#no_sale_section').show();
            $('#lead_section').hide();
            $('#sid').val('');
            event.preventDefault();
        });
        $(document).on('click', '#step1 .no-sale', function(event) {
            $('#no_sale_section').show();
            event.preventDefault();
        });
        $(document).on('click', '#customer_details .no-sale-ok', function(event) {
            $('#action').val('no-sale-ok');
            $('.webform_error').remove();
			var e = false;
			var contact_code = $('#contact_code').val();
			if (!contact_code) {
			    $("#contact_code").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select contact code</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var campaign_id2 = $('#campaign_id2').val();
			if (!campaign_id2) {
			    $("#campaign_id2").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select Campaign?</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var agent_name = $('#agent_name').val();
			if (!agent_name) {
			    $("#agent_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter user name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var first_name = $('#first_name').val();
			if (!first_name) {
			    $("#first_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your first name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			/*
			var surname = $('#surname').val();
			if (!surname) {
			    $("#surname").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your surname</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			*/
			
			var mobile = $('#mobile').val().replace(/[()]|\s|-/g, '');
			if (mobile) {
    			$('#mobile').val(mobile);
    			if (mobile.length != 10 || !(/^(04)\d{8}$/.test(mobile))) {
    				$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var home_phone = $('#home_phone').val().replace(/[()]|\s|-/g, '');
			if (home_phone) {
                $('#home_phone').val(home_phone);
    			if (home_phone.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(home_phone))) {
    				$("#home_phone").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your home phone</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var work_number = $('#work_number').val().replace(/[()]|\s|-/g, '');
			if (work_number) {
    			$('#work_number').val(work_number);
    			if (work_number.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(work_number))) {
    				$("#work_number").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your work number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			if (!mobile && !home_phone && !work_number) {
    			$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			 e = e || true;
			}
			
			var lead_action = $('#lead_action').val();
			if (!lead_action) {
			    $("#lead_action").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select lead action</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			if (!e) {
				$('#processing').show();
                $.post("/v12/customer_details_save",$('#customer_details_form').serialize(),function(response) {
				    window.location.replace("/v12/");
                });
			}
			else {
				$('html,body').animate({'scrollTop': $('.webform_error:first').offset().top - 100}, 'slow');
			}
			event.preventDefault();
        });
        $(document).on('click', '#step1 .no-sale-ok', function(event) {
            $('#action').val('no-sale-ok');
            var e = false;
			var first_name = $('#first_name').val();
			if (!first_name) {
			    $("#first_name").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your first name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			/*
			var surname = $('#surname').val();
			if (!surname) {
			    $("#surname").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your surname</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			*/
			
			var mobile = $('#mobile').val().replace(/[()]|\s|-/g, '');
			if (mobile) {
    			$('#mobile').val(mobile);
    			if (mobile.length != 10 || !(/^(04)\d{8}$/.test(mobile))) {
    				$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var home_phone = $('#home_phone').val().replace(/[()]|\s|-/g, '');
			if (home_phone) {
                $('#home_phone').val(home_phone);
    			if (home_phone.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(home_phone))) {
    				$("#home_phone").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your home phone</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			
			var work_number = $('#work_number').val().replace(/[()]|\s|-/g, '');
			if (work_number) {
    			$('#work_number').val(work_number);
    			if (work_number.length != 10 || !(/^(02|03|07|08)\d{8}$/.test(work_number))) {
    				$("#work_number").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your work number</div></div></div><div class="webform_error_bottom"></div></div>');
    			    e = e || true;
    			}
			}
			if (!mobile && !home_phone && !work_number) {
    			$("#mobile").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile number</div></div></div><div class="webform_error_bottom"></div></div>');
    			 e = e || true;
			}
			
            var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            var email = $('#email').val();
            if (email.length > 0) {
                if (!reg.test(email)) {
                    $('#email').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your email</div></div></div><div class="webform_error_bottom"></div></div>');
                    e = e || true;
                }
            }
            /*
			var plan_type = $('#plan_type').val();
			if (!plan_type) {
			    $("#plan_type").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select which product to compare</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var customer_type = $('#customer_type').val();
			if (!customer_type) {
			    $("#customer_type").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your comparison type</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var elec_recent_bill = $('#elec_recent_bill').val();
			var gas_recent_bill = $('#gas_recent_bill').val();
			if (plan_type && (elec_recent_bill || gas_recent_bill)) {
			    if (elec_recent_bill == 'Yes') {
			        if (plan_type == 'Dual' || plan_type == 'Elec') {
			            if (!$('#elec_supplier').val()) {
			                $('#elec_supplier').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#elec_billing_days').val()) {
			                $('#elec_billing_days').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter number of days in your billing cycle</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            var elec_meter_type = $('input[name="elec_meter_type"]:checked').val();
			            if (typeof(elec_meter_type) != 'undefined') {
			                if (elec_meter_type == 'Single Rate') {
			                    if (!$('#singlerate_peak').val()) {
			                        $('#singlerate_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + CL1') {
			                	if (!$('#singlerate_cl1_peak').val()) {
			                        $('#singlerate_cl1_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_cl1').val()) {
			                        $('#singlerate_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + CL2') {
			                	if (!$('#singlerate_cl2_peak').val()) {
			                        $('#singlerate_cl2_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_cl2').val()) {
			                        $('#singlerate_cl2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 2 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + CL1 + CL2') {
			                	if (!$('#singlerate_cl1_cl2_peak').val()) {
			                        $('#singlerate_cl1_cl2_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#singlerate_2_cl1').val()) {
			                        $('#singlerate_2_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_2_cl2').val()) {
			                        $('#singlerate_2_cl2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 2 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + Climate Saver') {
			                	if (!$('#singlerate_cs_peak').val()) {
			                        $('#singlerate_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#singlerate_cs').val()) {
			                        $('#singlerate_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_cs_billing_start').val()) {
			                        $('#singlerate_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Single Rate + CL1 + Climate Saver') {
			                	if (!$('#singlerate_cl1_cs_peak').val()) {
			                        $('#singlerate_cl1_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#singlerate_3_cl1').val()) {
			                        $('#singlerate_3_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#singlerate_3_cs').val()) {
			                        $('#singlerate_3_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#singlerate_cl1_cs_billing_start').val()) {
			                        $('#singlerate_cl1_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use') {
			                	if (!$('#timeofuse_peak').val()) {
			                        $('#timeofuse_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_offpeak').val()) {
			                        $('#timeofuse_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use + Climate Saver') {
			                	if (!$('#timeofuse_cs_peak').val()) {
			                        $('#timeofuse_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_cs_offpeak').val()) {
			                        $('#timeofuse_cs_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_cs').val()) {
			                        $('#timeofuse_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_cs_billing_start').val()) {
			                        $('#timeofuse_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use + CL1 + Climate Saver') {
			                	if (!$('#timeofuse_cl1_cs_peak').val()) {
			                        $('#timeofuse_cl1_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_cl1_cs_offpeak').val()) {
			                        $('#timeofuse_cl1_cs_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_cl1').val()) {
			                        $('#timeofuse_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_2_cs').val()) {
			                        $('#timeofuse_2_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                    if (!$('#timeofuse_cl1_cs_billing_start').val()) {
			                        $('#timeofuse_cl1_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use (Tariff 12)') {
			                	if (!$('#timeofuse_tariff12_peak').val()) {
			                        $('#timeofuse_tariff12_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_tariff12_offpeak').val()) {
			                        $('#timeofuse_tariff12_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Time of Use (Tariff 13)') {
				                if (!$('#timeofuse_tariff13_peak').val()) {
			                        $('#timeofuse_tariff13_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#timeofuse_tariff13_offpeak').val()) {
			                        $('#timeofuse_tariff13_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			                else if (elec_meter_type == 'Flexible Pricing') {
				                if (!$('#flexible_peak').val()) {
			                        $('#flexible_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
				                if (!$('#flexible_offpeak').val()) {
			                        $('#flexible_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                        e = e || true;
			                    }
			                }
			            }
			            var nmi = $('#nmi').val().replace(/[()]|\s|-/g, '');
			            //if (nmi.length > 0) {
			            	if (nmi.length != 11) {
								$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Make sure the NMI is 11 digits</div></div></div><div class="webform_error_bottom"></div></div>');
								e = e || true;
							}
							else {
								var total = 0;
								for ( var i = 0; i < 10; i++ ) {
									var str = nmi[i].charCodeAt(0).toString();
									if (i%2 == 0) {
										for ( var j = 0; j < str.length; j++ ) {
											total += eval(str[j]);
										}
									}
									else {
										var str2 = (str*2).toString();
										for ( var k = 0; k < str2.length; k++ ) {
											total += eval(str2[k]);
										}
									}
								}
								total += eval(nmi[10]);
								if (Math.round(total / 10) * 10 != total) {
									$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid NMI</div></div></div><div class="webform_error_bottom"></div></div>');
									e = e || true;
								}
								else {
									$('#nmi').val(nmi);
								}
							}
							var tariff_parent = $('#tariff_parent').val();
							if (!tariff_parent) {
								$("#tariff1").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">This is a child solar tariff. Please also select a parent tariff.</div></div></div><div class="webform_error_bottom"></div></div>');
								e = e || true;
							}
							var solar_specific_plan = false
							var tariff1_value = $('#tariff1').val();
							if (tariff1_value) {
								var tariff1_arr = tariff1_value.split('|');
								if (tariff1_arr[3] == 'Solar') {
									solar_specific_plan = true;
								}
							}
							var tariff2_value = $('#tariff2').val();
							if (tariff2_value) {
								var tariff2_arr = tariff2_value.split('|');
								if (tariff2_arr[3] == 'Solar') {
									solar_specific_plan = true;
								}
							}
							var tariff3_value = $('#tariff3').val();
							if (tariff3_value) {
								var tariff3_arr = tariff3_value.split('|');
								if (tariff3_arr[3] == 'Solar') {
									solar_specific_plan = true;
								}
							}
							var tariff4_value = $('#tariff4').val();
							if (tariff4_value) {
								var tariff4_arr = tariff4_value.split('|');
								if (tariff4_arr[3] == 'Solar') {
									solar_specific_plan = true;
								}
							}
							if (solar_specific_plan) {
								var solar_generated = $("#solar_generated").val();
								if (!solar_generated) {
									$("#solar_generated").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">How much solar was generated?</div></div></div><div class="webform_error_bottom"></div></div>');
									e = e || true;
								}
							}
			            //}
			        }
			        $('#elec_supplier2').val('');
			        $('.elec-usages').removeClass('active');
					$('#elec_usage_level').val('');
			    }
			    else if (elec_recent_bill == 'No') {
			        if (plan_type == 'Dual' || plan_type == 'Elec') {
			            if (!$('#elec_supplier2').val()) {
			                $('#elec_supplier2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            var elec_usage_level = $('#elec_usage_level').val();
						if ( !elec_usage_level ) {
			            	$('#elec_usage_level').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your electricity usage</div></div></div><div class="webform_error_bottom"></div></div>');
							e = e || true;
						}
						var nmi = $('#nmi').val().replace(/[()]|\s|-/g, '');
			            //if (nmi.length > 0) {
			            	if (nmi.length != 11) {
								$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Make sure the NMI is 11 digits</div></div></div><div class="webform_error_bottom"></div></div>');
								e = e || true;
							}
							else {
								var total = 0;
								for ( var i = 0; i < 10; i++ ) {
									var str = nmi[i].charCodeAt(0).toString();
									if (i%2 == 0) {
										for ( var j = 0; j < str.length; j++ ) {
											total += eval(str[j]);
										}
									}
									else {
										var str2 = (str*2).toString();
										for ( var k = 0; k < str2.length; k++ ) {
											total += eval(str2[k]);
										}
									}
								}
								total += eval(nmi[10]);
								if (Math.round(total / 10) * 10 != total) {
									$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid NMI</div></div></div><div class="webform_error_bottom"></div></div>');
									e = e || true;
								}
								else {
									$('#nmi').val(nmi);
								}
							}
							var tariff_parent = $('#tariff_parent').val();
							if (!tariff_parent) {
								$("#tariff1").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">This is a child solar tariff. Please also select a parent tariff.</div></div></div><div class="webform_error_bottom"></div></div>');
								e = e || true;
							}
			            //}
			        }
			        $('#elec_supplier').val('');
			        $('#elec_billing_days').val('');
			        $('#elec_spend').val('');
			        $('#elec_meter_type_fields').find('input:text').val('');
			        $('input[name="elec_meter_type"]').prop("checked", false);
			    }
			    if (gas_recent_bill == 'Yes') {
			        if (plan_type == 'Dual' || plan_type == 'Gas') {    
			            if (!$('#gas_supplier').val()) {
			                $('#gas_supplier').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#gas_billing_days').val()) {
			                $('#gas_billing_days').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter number of days in your billing cycle.</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#gas_billing_start').val()) {
			                $('#gas_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select billing start date.</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#gas_peak').val()) {
			                $('#gas_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        $('#gas_supplier2').val('');
			        $('.gas-usages').removeClass('active');
					$('#gas_usage_level').val('');
			    }
			    else if (gas_recent_bill == 'No') {
			        if (plan_type == 'Dual' || plan_type == 'Gas') {
			            if ( !$('#gas_supplier2').val() ) {
			                $('#gas_supplier2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            var gas_usage_level = $('#gas_usage_level').val();
						if ( !gas_usage_level ) {
			            	$('#gas_usage_level').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your gas usage</div></div></div><div class="webform_error_bottom"></div></div>');
							e = e || true;
						}
			        }
			        $('#gas_supplier').val('');
			        $('#gas_billing_days').val('');
					$('#gas_billing_start').val('');
					$('#gas_spend').val('');
					$('#gas_peak').val('');
					$('#gas_off_peak').val('');
			    }
			}
			var postcode = $('#postcode').val().replace(/\s/g, '');
			var suburb  = $('#suburb').val();
			if ((postcode.length != 4) || (/[^0-9-()+\s]/.test(postcode))) {
				$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your postcode</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;
			}
			else if(!(/^[2-5]/.test(postcode))) {
				$('#postcode').addClass("error");
				$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Sorry we do not service that area</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;	
			}
			else if ( !suburb ) {
			    $('#suburb').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select suburb</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			else {
			    $('#postcode').val(postcode);
				$('#postcode').removeClass("error");
			}
			*/
			var lead_action = $('#lead_action').val();
			if (!lead_action) {
			    $("#lead_action").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select lead action</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			if (!e) {
				$('#processing').show();
                $.post("/v12/customer_details_update",$('#step1_form').serialize(),function(response) {
				    window.location.replace("/v12/");
                });
			}
			else {
				$('html,body').animate({'scrollTop': $('.webform_error:first').offset().top - 100}, 'slow');
			}
			event.preventDefault();
        });
        $(document).on('click', '#step3 .no-sale-button', function(event) {
            $('.webform_error').remove();
            var e = false;
            var sid = $('#sid').val();
            var lead_action = $('#lead_action').val();
			if (!lead_action) {
			    $("#lead_action").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select lead action</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			if (!e) {
				$('#processing').show();
				var url = "/v12/customer_details_update";
				if (sid) {
    				$.post(url,{current_step: '3', sid: sid, lead_action: lead_action},function(response) {
				        window.location.replace("/v12/");
                    });
				} else {
                    window.location.replace("/v12/");
                }
			}
			else {
				$('html,body').animate({'scrollTop': $('.webform_error:first').offset().top - 100}, 'slow');
			}
            event.preventDefault();
        });
        $(document).on('click', '#step3 .solar-interest-button', function(event) {
            $('.webform_error').remove();
            var e = false;
            var sid = $('#sid').val();
            $('#processing').show();
			var url = "/v12/customer_details_update";
			if (sid) {
				$.post(url,{current_step: '3', sid: sid, lead_action: '', solar_interest: 1},function(response) {
			        window.location.replace("/v12/");
                });
			} else {
                window.location.replace("/v12/");
            }
            event.preventDefault();
        });
        $(document).on('click', '#customer_details .outbound', function(event) {
            $('.webform_error').remove();
            var e = false;
            var contact_code = $('#contact_code').val();
			if (!contact_code) {
			    $("#contact_code").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select contact code</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			if (!e) {
				$('#processing').show();
                $.post('/v12/contact_code_save',{outbound: 1, inbound: 0, contact_code: $('#contact_code').val()},function(response) {
				    window.location = "/v12/compare/1";
                });
            }
            else {
				$('html,body').animate({'scrollTop': $('.webform_error:first').offset().top - 100}, 'slow');
			}
            event.preventDefault();
        });
        $(document).on('click', '#customer_details .inbound', function(event) {
            $('#processing').show();
            $.post('/v12/contact_code_save',{outbound: 0, inbound: 1, contact_code: ''},function(response) {
                $('#processing').hide();
				$('#customer_details_field').show();
            });
            event.preventDefault();
        });
        $(document).on('click', '.test-lead', function(event) {
            $('#sid').val(332861);
            lead_lookup();
            event.preventDefault();
        });
            
		$('.plan-type').add('.customer-type').add('.elec-usages').add('.gas-usages').add('#term1_field').add('#step1_error_message').mouseenter(function() {
        	$(this).parent().find('.webform_error').remove();
		});
		$(':input').focus(function() {
			$(this).parent().find('.webform_error').remove();
		});
		if ($('input[name="pay_on_time_discount"]').val()) {
			var conditional_discount = $('input[name="pay_on_time_discount"]').val();
			$('.radio-simulate.pay-on-time-discount-choices #' + conditional_discount).trigger( 'click' );
		}
		if ($('input[name="direct_debit_discount"]').val()) {
			var conditional_discount = $('input[name="direct_debit_discount"]').val();
			$('.radio-simulate.direct-debit-discount-choices #' + conditional_discount).trigger( 'click' );
		}
		if ($('input[name="dual_fuel_discount"]').val()) {
			var conditional_discount = $('input[name="dual_fuel_discount"]').val();
			$('.radio-simulate.dual-fuel-discount-choices #' + conditional_discount).trigger( 'click' );
		}
		if ($('input[name="bonus_discount"]').val()) {
			var conditional_discount = $('input[name="bonus_discount"]').val();
			$('.radio-simulate.bonus-discount-choices #' + conditional_discount).trigger( 'click' );
		}
		if ($('input[name="prepay_discount"]').val()) {
			var conditional_discount = $('input[name="prepay_discount"]').val();
			$('.radio-simulate.prepay-discount-choices #' + conditional_discount).trigger( 'click' );
		}
		if ($('input[name="rate_freeze"]').val()) {
			var rate_freeze = $('input[name="rate_freeze"]').val();
			$('.radio-simulate.rate-freeze-choices #' + rate_freeze).trigger( 'click' );
		}
		if ($('input[name="no_contract_plan"]').val()) {
			var no_contract_plan = $('input[name="no_contract_plan"]').val();
			$('.radio-simulate.no-contract-plan-choices #' + no_contract_plan).trigger( 'click' );
		}
		if ($('input[name="bill_smoothing"]').val()) {
			var bill_smoothing = $('input[name="bill_smoothing"]').val();
			$('.radio-simulate.bill-smoothing-choices #' + bill_smoothing).trigger( 'click' );
		}
		if ($('input[name="online_account_management"]').val()) {
			var online_account_management = $('input[name="online_account_management"]').val();
			$('.radio-simulate.online-account-management-choices #' + online_account_management).trigger( 'click' );
		}
		if ($('input[name="energy_monitoring_tools"]').val()) {
			var energy_monitoring_tools = $('input[name="energy_monitoring_tools"]').val();
			$('.radio-simulate.energy-monitoring-tools-choices #' + energy_monitoring_tools).trigger( 'click' );
		}
		if ($('input[name="membership_reward_programs"]').val()) {
			var membership_reward_programs = $('input[name="membership_reward_programs"]').val();
			$('.radio-simulate.membership-reward-programs-choices #' + membership_reward_programs).trigger( 'click' );
		}
		if ($('input[name="renewable_energy"]').val()) {
			var renewable_energy = $('input[name="renewable_energy"]').val();
			$('.radio-simulate.renewable-energy-choices #' + renewable_energy).trigger( 'click' );
		}
		$(document).on('click', '#step2 .continue', function(event) {
			$('#sort_by').val($(this).prop('id'));
			$('#processing').show();
			var sort_by = $(this).attr('id');
			$.post("/v12/compare_save/2",$('#step2_form').serialize(),function(response) {
				window.location = "/v12/compare/3";
            });
            event.preventDefault();
		});
		$(document).on('click', '.check-residential', function(event) {
			$('input[name="customer_type"]').val('RES');
			$('.check-business').removeClass('active');
			$(this).addClass('active');
			compare();
		});
		$(document).on('click', '.check-business', function(event) {
			$('.check-residential').removeClass('active');
			$(this).addClass('active');
			$('input[name="customer_type"]').val('SME');
			compare();
		});
		$('.plans').jcarousel();
		$('#plan-pagination ul, #plan-pagination-top ul')
		.on('jcarouselpagination:active', 'li', function() {
        	$(this).addClass('active');
        })
        .on('jcarouselpagination:inactive', 'li', function() {
        	$(this).removeClass('active');
        })
		.jcarouselPagination({
			'item': function(page, carouselItems) {
				return '<li><a href="#' + page + '">' + page + '</a></li>';
			}
		});
		$('.plan-pagination-wrap .prev').jcarouselControl({
			target: '-=1'
		});
	
		$('.plan-pagination-wrap .next').jcarouselControl({
			target: '+=1'
		});
		/*$('.plan-page').width($('.plans').width());
		$(window).resize(function(){
			$('.plan-page').width($('.plans').width());
		})*/
		
		$('.plan-info-tabs').tabs();
		$(document).on('click', '.plan-pagination-wrap a', function() {
            $('.plans .plan').each(function(index, element) {
                if ($(this).width()== 687) {
					$(this).find('.plan-info').hide();
					$(this).find('.plan-info').find('.plan-call-form').hide();
					$(this).css('width','229px').siblings().css('display','block');
					$(this).find('.view-details').html('click to view plan details &raquo;');
					$('.plans').jcarousel('reload', {
						animation: 'slow'
					});
				}
            });
        });
        $(document).on('click', '.view-details', function(event) {
			var plan = $(this).parents('.plan');
			if (plan.width() == 687) {
				plan.find('.plan-info').hide();
				plan.find('.plan-info').find('.plan-call-form').hide();
				plan.css('width','229px').siblings().css('display','block');
				$(this).html('click to view plan details &raquo;');
				$('.plans').jcarousel('reload', {
					animation: 'slow'
				});
			}
			else {
				plan.css('width','687px').siblings().css('display','none').end().find('.plan-info').show('fast');
				$(this).html('&laquo; back to plans');
				$('.plans').jcarousel('reload', {
					animation: 'slow'
				});
				$('html,body').animate({'scrollTop': $('.plans').offset().top}, 'slow');
			}
			event.preventDefault();
		});
		$(document).on('click', '.back-to-plans,.plan-info-close', function(event) {
			$(this).parents('.plan-info').hide('fast');
			$(this).parents('.plan-info').find('.plan-call-form').hide('fast');
			var plan = $(this).parents('.plan');
			plan.find('.view-details').html('click to view plan details &raquo;');
			plan.css('width','229px').siblings().css('display','block');
		});
		$(document).on('click', '.add-to-top-picks', function(event) {
		    var id = $(this).attr('rel');
		    if ($.cookie('top_picks') == null) {
		        $.cookie('top_picks', id, { path: '/' });
		        $('#top_picks_count').text('(1)');
		        $(this).find('img').attr('src', '/img/top-picks.png');
		        $('#plan_favor_info_' + id).html('This plan has been added to your top picks');
		        $('#plan_favor_info_' + id).fadeIn();
		        setTimeout(function() {
		            $('#plan_favor_info_' + id).fadeOut();
		        }, 2000);
		        $('#clear_my_top_picks').addClass('active');
		    }
		    else {
		        var top_picks_arr = $.cookie('top_picks').split(',');
		        if ($.inArray(id, top_picks_arr) == -1) {
		            top_picks_arr.push(id);
		            var top_picks = top_picks_arr.join(',');
		            $('#top_picks_count').text('('+top_picks_arr.length+')');
		            $(this).find('img').attr('src', '/img/top-picks.png');
		            $(this).find('.plan-favor-text').text('Remove from Top Picks');
		            $('#plan_favor_info_' + id).html('This plan has been added to your top picks');
		            $('#plan_favor_info_' + id).fadeIn();
		            setTimeout(function() {
		                $('#plan_favor_info_' + id).fadeOut();
		            }, 2000);
		        }
		        else {
		            var top_picks_arr_new = [];
		            $.each(top_picks_arr, function(index,item){
		                if (item && item != id) {
		                    top_picks_arr_new.push(item);
		                }
		            });
		            var top_picks = (top_picks_arr_new.length > 0) ? top_picks_arr_new.join(',') : null;
		            $('#top_picks_count').text('('+top_picks_arr_new.length+')');
		            $(this).find('img').attr('src', '/img/favor.png');
		            $(this).find('.plan-favor-text').text('Add to Top Picks');
		            $('#plan_favor_info_' + id).html('This plan has been removed from your top picks');
		            $('#plan_favor_info_' + id).fadeIn();
		            setTimeout(function() {
		                $('#plan_favor_info_' + id).fadeOut();
		            }, 2000);
		        }
		        if (top_picks) {
			        $('#clear_my_top_picks').addClass('active');
		        }
		        else {
			        $('#clear_my_top_picks').removeClass('active');
		        }
		        $.cookie('top_picks', top_picks, { path: '/' });
		    }
		});
		$(document).on('click', '#clear_my_top_picks', function(event) {
		 	if ($.cookie('top_picks') != null) {
		 		$.cookie('top_picks', null, { path: '/' });
		 		$('#top_picks_count').text('(0)');
		 		$('.add-to-top-picks img').attr('src', '/img/favor.png');
		 		$('#clear_my_top_picks').removeClass('active');
		 		window.location = "/v12/compare/3";
		    }
		});
		if ($.cookie('top_picks') != null) {
		    var top_picks_arr = $.cookie('top_picks').split(',');
		    $('#top_picks_count').text('('+top_picks_arr.length+')');
		    $('#clear_my_top_picks').addClass('active');
		}
		$(document).on('click', '.clear-filters', function(event) {
		    $('#retailer_all').prop('checked', true);
		    $('.retailer').prop("checked", false);
		    $('#discount_type_all').prop('checked', true);
		    $('.discount_type').prop("checked", false);
		    $('#contract_length_all').prop('checked', true);
		    $('.contract_length').prop("checked", false);
		    $('#payment_options_all').prop('checked', true);
		    $('.payment_options').prop("checked", false);
		    $('input[name="solar"]').prop("checked", false);
		    compare();
		});
		$(document).on('click', '.filter_plan_type', function(event) {
			var has_modal = false;
			if ($(this).val() == 'Elec' && $('#elec_recent_bill_original').val() == 'No') {
				$('#plan_type').val('Elec');
				var elec_recent_bill = $('#elec_recent_bill').val();
				$('.elec-recent-bill-choices #' + elec_recent_bill).trigger( 'click' );
				has_modal = true;
				$("#electricity_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			if ($(this).val() == 'Gas' && $('#gas_recent_bill_original').val() == 'No') {
				$('#plan_type').val('Gas');
				var gas_recent_bill = $('#gas_recent_bill').val();
				$('.gas-recent-bill-choices #' + gas_recent_bill).trigger( 'click' );
				has_modal = true
				$("#gas_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			if ($(this).val() == 'Dual') {
                if ($('#elec_recent_bill_original').val() == 'No') {
    				$('#plan_type').val('Elec');
    				var elec_recent_bill = $('#elec_recent_bill').val();
    				$('.elec-recent-bill-choices #' + elec_recent_bill).trigger( 'click' );
    				has_modal = true;
    				$("#electricity_modal").modal({
    					backdrop: 'static',
    					keyboard: false,
    				});
    			}
    			else if ($('#gas_recent_bill_original').val() == 'No') {
    				$('#plan_type').val('Gas');
    				var gas_recent_bill = $('#gas_recent_bill').val();
    				$('.gas-recent-bill-choices #' + gas_recent_bill).trigger( 'click' );
    				has_modal = true
    				$("#gas_modal").modal({
    					backdrop: 'static',
    					keyboard: false,
    				});
    			}
            }
			/*
			if ($(this).val() == 'Dual') {
				if ($('#elec_details').val() == 0) {
					$('#plan_type').val('Elec');
					var elec_recent_bill = $('#elec_recent_bill').val();
					$('.elec-recent-bill-choices #' + elec_recent_bill).trigger( 'click' );
					has_modal = true;
					$("#electricity_modal").modal({
						backdrop: 'static',
						keyboard: false,
					});
				}
				else if ($('#gas_details').val() == 0) {
					$('#plan_type').val('Gas');
					var gas_recent_bill = $('#gas_recent_bill').val();
					$('.gas-recent-bill-choices #' + gas_recent_bill).trigger( 'click' );
					has_modal = true
					$("#gas_modal").modal({
						backdrop: 'static',
						keyboard: false,
					});
				}
			}
			*/
			if (!has_modal) {
				compare();
			}
		});
		$(document).on('click', '.enter-elec-details', function(event) {
			$('#plan_type').val('Elec');
			var elec_recent_bill = $('#elec_recent_bill').val();
			$('.elec-recent-bill-choices #' + elec_recent_bill).trigger( 'click' );
			$("#electricity_modal").modal({
				backdrop: 'static',
				keyboard: false,
			});
			event.preventDefault();
		});
		$(document).on('click', '.enter-gas-details', function(event) {
			$('#plan_type').val('Gas');
			var gas_recent_bill = $('#gas_recent_bill').val();
			$('.gas-recent-bill-choices #' + gas_recent_bill).trigger( 'click' );
			$("#gas_modal").modal({
				backdrop: 'static',
				keyboard: false,
			});
			event.preventDefault();
		});
		$('#sort_by').change(function() {
			compare();
		});
		$(document).on('click', '#retailer_all', function(event) {
        	$('.retailer').prop('checked',this.checked);
			compare();
		});
		$('.retailer').change(function() {
        	var check = ($('.retailer').filter(":checked").length == $('.retailer').length);
			$('#retailer_all').prop("checked", check);
			compare();
		});
		$(document).on('click', '#discount_type_all', function(event) {
        	$('.discount_type').prop('checked', this.checked);
			compare();
		});
		$('.discount_type').change(function() {
			if ($(this).is(':checked') && $(this).val() == 'Direct Debit') {
				$('input:checkbox[value="Pay On Time"]').prop('checked', true);
			}
			if ($(this).val() == 'Pay On Time' && !$(this).is(':checked') && $('input:checkbox[value="Direct Debit"]').is(':checked')) {
				$('input:checkbox[value="Pay On Time"]').prop('checked', true);
			}
        	var check = ($('.discount_type').filter(":checked").length == $('.discount_type').length);
			$('#discount_type_all').prop("checked", check);
			compare();
		});
		$(document).on('click', '#contract_length_all', function(event) {
        	$('.contract_length').prop('checked', this.checked);
			compare();
		});
		$('.contract_length').change(function() {
        	var check = ($('.contract_length').filter(":checked").length == $('.contract_length').length);
			$('#contract_length_all').prop("checked", check);
			compare();
		});
		$(document).on('click', '#payment_options_all', function(event) {
        	$('.payment_options').prop('checked', this.checked);
			compare();
		});
		$('.payment_options').change(function() {
        	var check = ($('.payment_options').filter(":checked").length == $('.payment_options').length);
			$('#payment_options_all').prop("checked", check);
			compare();
		});
		$('.solar').change(function() {
        	compare();
		});
		window.compare = function() {
			$('#processing').show();
			$('#filter_results_form').submit();
		}
		$(document).tooltip({
    		items: '.step .elec-usages .usage .item, .step .gas-usages .usage .item',
			position: {
				my: "center bottom-100",
				at: "center bottom",
				using: function( position, feedback ) {
					$( this ).css( position );
					$( "<div>" )
					.addClass( "arrow" )
					.addClass( feedback.vertical )
					.addClass( feedback.horizontal )
					.appendTo( this );
				}
			},
			content: function () {
            	//return $(this).prop('title');
            	return $(this).attr('title');
          	}
		});
		$(document).on('click', '#step3_electricity_form .continue', function(event) {
			$('.webform_error').remove();
			var e = false;
			var elec_recent_bill = $('#elec_recent_bill').val();
			if (elec_recent_bill == 'Yes') {
			    if (!$('#elec_supplier').val()) {
			        $('#elec_supplier').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    if (!$('#elec_billing_days').val()) {
			        $('#elec_billing_days').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter number of days in your billing cycle</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    var elec_meter_type = $('input[name="elec_meter_type"]:checked').val();
			    if (typeof(elec_meter_type) != 'undefined') {
			        if (elec_meter_type == 'Single Rate') {
			            if (!$('#singlerate_peak').val()) {
			                $('#singlerate_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Single Rate + CL1') {
			        	if (!$('#singlerate_cl1_peak').val()) {
			                $('#singlerate_cl1_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_cl1').val()) {
			                $('#singlerate_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Single Rate + CL2') {
			        	if (!$('#singlerate_cl2_peak').val()) {
			                $('#singlerate_cl2_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_cl2').val()) {
			                $('#singlerate_cl2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 2 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Single Rate + CL1 + CL2') {
			        	if (!$('#singlerate_cl1_cl2_peak').val()) {
			                $('#singlerate_cl1_cl2_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_cl1').val()) {
			                $('#singlerate_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_cl2').val()) {
			                $('#singlerate_cl2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 2 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Single Rate + Climate Saver') {
			        	if (!$('#singlerate_cs_peak').val()) {
			                $('#singlerate_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_cs').val()) {
			                $('#singlerate_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_cs_billing_start').val()) {
			                $('#singlerate_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Single Rate + CL1 + Climate Saver') {
			        	if (!$('#singlerate_cl1_cs_peak').val()) {
			                $('#singlerate_cl1_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_3_cl1').val()) {
			                $('#singlerate_3_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_3_cs').val()) {
			                $('#singlerate_3_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#singlerate_cl1_cs_billing_start').val()) {
			                $('#singlerate_cl1_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Time of Use') {
			        	if (!$('#timeofuse_peak').val()) {
			                $('#timeofuse_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_offpeak').val()) {
			                $('#timeofuse_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Time of Use + Climate Saver') {
			        	if (!$('#timeofuse_cs_peak').val()) {
			                $('#timeofuse_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_cs_offpeak').val()) {
			                $('#timeofuse_cs_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_cs').val()) {
			                $('#timeofuse_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_cs_billing_start').val()) {
			                $('#timeofuse_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Time of Use + CL1 + Climate Saver') {
			        	if (!$('#timeofuse_cl1_cs_peak').val()) {
			                $('#timeofuse_cl1_cs_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_cl1_cs_offpeak').val()) {
			                $('#timeofuse_cl1_cs_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_cl1').val()) {
			                $('#timeofuse_cl1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your controlled load 1 usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_2_cs').val()) {
			                $('#timeofuse_2_cs').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your climate saver</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_cl1_cs_billing_start').val()) {
			                $('#timeofuse_cl1_cs_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your billing start date</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Time of Use (Tariff 12)') {
			        	if (!$('#timeofuse_tariff12_peak').val()) {
			                $('#timeofuse_tariff12_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_tariff12_offpeak').val()) {
			                $('#timeofuse_tariff12_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Time of Use (Tariff 13)') {
			            if (!$('#timeofuse_tariff13_peak').val()) {
			                $('#timeofuse_tariff13_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#timeofuse_tariff13_offpeak').val()) {
			                $('#timeofuse_tariff13_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			        else if (elec_meter_type == 'Flexible Pricing') {
			            if (!$('#flexible_peak').val()) {
			                $('#flexible_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			            if (!$('#flexible_offpeak').val()) {
			                $('#flexible_offpeak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your off peak usage</div></div></div><div class="webform_error_bottom"></div></div>');
			                e = e || true;
			            }
			        }
			    }
			    $('#elec_supplier2').val('');
			    $('.elec-usages').removeClass('active');
				$('#elec_usage_level').val('');
			}
			else if (elec_recent_bill == 'No') {
			    if ( !$('#elec_supplier2').val() ) {
			        $('#elec_supplier2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    var elec_usage_level = $('#elec_usage_level').val();
			    if (!elec_usage_level) {
			        $('#elec_usage_level').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your electricity usage</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    $('#elec_supplier').val('');
			    $('#elec_billing_days').val('');
			    $('#elec_spend').val('');
			    $('#elec_meter_type_fields').find('input:text').val('');
			    $('input[name="elec_meter_type"]').prop("checked", false);
			}
			var nmi = $('#nmi').val().replace(/[()]|\s|-/g, '');
			if (nmi.length > 0) {
			    if (nmi.length != 11) {
			    	$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Make sure the NMI is 11 digits</div></div></div><div class="webform_error_bottom"></div></div>');
			    	e = e || true;
			    }
			    else {
			    	var total = 0;
			    	for ( var i = 0; i < 10; i++ ) {
			    		var str = nmi[i].charCodeAt(0).toString();
			    		if (i%2 == 0) {
			    			for ( var j = 0; j < str.length; j++ ) {
			    				total += eval(str[j]);
			    			}
			    		}
			    		else {
			    			var str2 = (str*2).toString();
			    			for ( var k = 0; k < str2.length; k++ ) {
			    				total += eval(str2[k]);
			    			}
			    		}
			    	}
			    	total += eval(nmi[10]);
			    	if (Math.round(total / 10) * 10 != total) {
			    		$('#nmi').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid NMI</div></div></div><div class="webform_error_bottom"></div></div>');
			    		e = e || true;
			    	}
			    	else {
			    		$('#nmi').val(nmi);
			    		e = e || false;
			    	}
			    }
			    var tariff1 = $('#tariff1').val();
				var tariff2 = $('#tariff2').val();
				var tariff3 = $('#tariff3').val();
				if (!tariff1 && !tariff2 && !tariff3) {
			    	$("#tariff1").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select a parent tariff.</div></div></div><div class="webform_error_bottom"></div></div>');
			    	e = e || true;
			    }
			}
			if (!e) {
				if ($('input[name="plan_type"]:checked').val() == 'Gas') {
					$('input[name="plan_type"][value="Dual"]').prop('checked', true);
				}
				$("#electricity_modal").modal('hide');
				$('#processing').show();
				$.post("/v12/compare_save/3",$('#step3_electricity_form').serialize(),function(response) {
					compare();
				});
			}
            event.preventDefault();
		});
		$(document).on('click', '#step3_gas_form .continue', function(event) {
			$('.webform_error').remove();
			var e = false;
			var gas_recent_bill = $('#gas_recent_bill').val();
			if ( gas_recent_bill == 'Yes') {
			    if (!$('#gas_supplier').val()) {
			        $('#gas_supplier').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    if (!$('#gas_billing_days').val()) {
			        $('#gas_billing_days').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter number of days in your billing cycle.</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    if (!$('#gas_billing_start').val()) {
			        $('#gas_billing_start').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select billing start date.</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    if (!$('#gas_peak').val()) {
			        $('#gas_peak').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your usage</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    $('#gas_supplier2').val('');
			    $('.gas-usages').removeClass('active');
				$('#gas_usage_level').val('');
			}
			else if (gas_recent_bill == 'No') {
			    if (!$('#gas_supplier2').val()) {
			        $('#gas_supplier2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			        e = e || true;
			    }
			    var gas_usage_level = $('#gas_usage_level').val();
				if (!gas_usage_level) {
			        $('#gas_usage_level').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your gas usage</div></div></div><div class="webform_error_bottom"></div></div>');
				    e = e || true;
				}
				$('#gas_supplier').val('');
			    $('#gas_billing_days').val('');
				$('#gas_billing_start').val('');
				$('#gas_spend').val('');
				$('#gas_peak').val('');
				$('#gas_off_peak').val('');
			}
			if (!e) {
				if ($('input[name="plan_type"]:checked').val() == 'Elec') {
					$('input[name="plan_type"][value="Dual"]').prop('checked', true);
				}
				$("#gas_modal").modal('hide');
				$('#processing').show();
				$.post("/v12/compare_save/3",$('#step3_gas_form').serialize(),function(response) {
					compare();
				});
			}
            event.preventDefault();
		});
		$(document).on('click', '#step3_electricity_form .close-modal', function(event) {
			$("#electricity_modal").modal('hide');
			$('input[name="plan_type"][value="Elec"]').prop('checked', false);
			var plan_type = $('#plan_type_original').val();
			$('#plan_type').val(plan_type);
			$('input[name="plan_type"][value="'+plan_type+'"]').prop('checked', true);
			event.preventDefault();
		});
		$(document).on('click', '#step3_gas_form .close-modal', function(event) {
			$("#gas_modal").modal('hide');
			$('input[name="plan_type"][value="Gas"]').prop('checked', false);
			var plan_type = $('#plan_type_original').val();
			$('#plan_type').val(plan_type);
			$('input[name="plan_type"][value="'+plan_type+'"]').prop('checked', true);
			event.preventDefault();
		});
		$(document).on('click', 'form[name="rates_form2"] input[name="discount_guaranteed"], form[name="rates_form2"] input[name="include_gst"]', function(event) {
			var plan_id = $(this).val();
			$.post("/v12/get_rates/1",$('#rates_form2_' + plan_id).serialize(),function(response) {
				$('#table2_rate_' + plan_id).html(response.html);
			}, "json");
		});
		$(document).on('click', 'form[name="rates_form2"] input[name="discount_pay_on_time"]', function(event) {
			if (!$(this).is(':checked') && $('form[name="rates_form2"] input[name="discount_direct_debit"]').is(':checked')) {
				$(this).prop('checked', true);
			}
			else {
				var plan_id = $(this).val();
				$.post("/v12/get_rates/1",$('#rates_form2_' + plan_id).serialize(),function(response) {
					$('#table2_rate_' + plan_id).html(response.html);
				}, "json");
			}
		});
		$(document).on('click', 'form[name="rates_form2"] input[name="discount_direct_debit"]', function(event) {
			var plan_id = $(this).val();
			if ($(this).is(':checked')) {
				$('form[name="rates_form2"] input[name="discount_pay_on_time"]').prop('checked', true);
			}
			$.post("/v12/get_rates/1",$('#rates_form2_' + plan_id).serialize(),function(response) {
				$('#table2_rate_' + plan_id).html(response.html);
			}, "json");
		});
		$(document).on('click', '.btn-gst', function(event) {
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="include_gst"]').val('');
				$(this).removeClass('active');
				var title = 'GST is currently not applied. Click to include GST';
			}
			else {
				$(this).closest('form').find('input[name="include_gst"]').val(plan_id);
				$(this).addClass('active');
				var title = 'GST is currently applied. Click to exclude GST';
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
		});
		$(document).on('click', '.btn-discount', function(event) {
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="discount_pay_on_time"]').val('');
				$(this).closest('form').find('input[name="discount_guaranteed"]').val('');
				$(this).closest('form').find('input[name="discount_direct_debit"]').val('');
				$(this).removeClass('active');
				var title = 'Discounts are not applied. Please apply required discounts in the filter list';
			}
			else {
				if ($('.plans-search-filters input:checkbox[value="Pay On Time"]').is(':checked')) {
					$(this).closest('form').find('input[name="discount_pay_on_time"]').val(plan_id);
				}
				if ($('.plans-search-filters input:checkbox[value="Guaranteed"]').is(':checked')) {
					$(this).closest('form').find('input[name="discount_guaranteed"]').val(plan_id);
				}
				if ($('.plans-search-filters input:checkbox[value="Direct Debit"]').is(':checked')) {
					$(this).closest('form').find('input[name="discount_direct_debit"]').val(plan_id);
				}
				$(this).addClass('active');
				var title = 'Discounts are currently applied. Do not quote customer these rates. Click to exclude discounts';
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
		});
		$(document).on('click', '.btn-gst2', function(event) {
    		/*
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="include_gst"]').val('');
				$(this).removeClass('active');
				var title = 'GST is currently not applied. Click to include GST';
				$("#gst_disclaimer_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			else {
				$(this).closest('form').find('input[name="include_gst"]').val(plan_id);
				$(this).addClass('active');
				var title = 'GST is currently applied. Click to exclude GST';
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
			*/
			if ($('#include_gst_all').val() == 1) {
    			$('#include_gst_all').val(0);
			} else {
    			$('#include_gst_all').val(1);
			}
			compare();
		});
		$(document).on('click', '#gst_disclaimer_modal .close-modal', function(event) {
			$("#gst_disclaimer_modal").modal('hide');
			event.preventDefault();
		});
		$(document).on('click', '.btn-gtd', function(event) {
    		/*
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="discount_guaranteed"]').val('');
				$(this).removeClass('active');
				var title = 'Guaranteed discount is not applied. Click to apply Guaranteed discount';
			}
			else {
				$(this).closest('form').find('input[name="discount_guaranteed"]').val(plan_id);
				$(this).addClass('active');
				var title = 'Guaranteed discount is currently applied. Do not quote customer these rates. Click to exclude Guaranteed discount';
				$("#discounts_disclaimer_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
			*/
			if ($('#discount_guaranteed_all').val() == 1) {
    			$('#discount_guaranteed_all').val(0);
			} else {
    			$('#discount_guaranteed_all').val(1);
			}
			compare();
		});
		$(document).on('click', '.btn-pot', function(event) {
    		/*
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="discount_pay_on_time"]').val('');
				$(this).removeClass('active');
				var title = 'Pay On Time discount is not applied. Click to apply Pay On Time discount';
			}
			else {
				$(this).closest('form').find('input[name="discount_pay_on_time"]').val(plan_id);
				$(this).addClass('active');
				var title = 'Pay On Time discount is currently applied. Do not quote customer these rates. Click to exclude Pay On Time discount';
				$("#discounts_disclaimer_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
			*/
			if ($('#discount_pay_on_time_all').val() == 1) {
    			$('#discount_pay_on_time_all').val(0);
			} else {
    			$('#discount_pay_on_time_all').val(1);
			}
			compare();
		});
		$(document).on('click', '.btn-dd', function(event) {
    		/*
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="discount_direct_debit"]').val('');
				$(this).removeClass('active');
				var title = 'Direct Debit discount is not applied. Click to apply Direct Debit discount';
			}
			else {
				$(this).closest('form').find('input[name="discount_direct_debit"]').val(plan_id);
				$(this).addClass('active');
				var title = 'Direct Debit discount is currently applied. Do not quote customer these rates. Click to exclude Direct Debit discount';
				$("#discounts_disclaimer_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
			*/
			if ($('#discount_direct_debit_all').val() == 1) {
    			$('#discount_direct_debit_all').val(0);
			} else {
    			$('#discount_direct_debit_all').val(1);
			}
			compare();
		});
		$(document).on('click', '.btn-dud', function(event) {
    		/*
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="discount_dual_fuel"]').val('');
				$(this).removeClass('active');
				var title = 'Double up discount is not applied. Click to apply Double up discount';
			}
			else {
				$(this).closest('form').find('input[name="discount_dual_fuel"]').val(plan_id);
				$(this).addClass('active');
				var title = 'Double up discount is currently applied. Do not quote customer these rates. Click to exclude Double up discount';
				$("#discounts_disclaimer_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
			*/
			if ($('#discount_dual_fuel_all').val() == 1) {
    			$('#discount_dual_fuel_all').val(0);
			} else {
    			$('#discount_dual_fuel_all').val(1);
			}
			compare();
		});
		$(document).on('click', '.btn-bon', function(event) {
    		/*
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="discount_bonus_sumo"]').val('');
				$(this).removeClass('active');
				var title = 'Bonus Pay on Time discount is not applied. Click to apply Bonus Pay on Time discount';
			}
			else {
				$(this).closest('form').find('input[name="discount_bonus_sumo"]').val(plan_id);
				$(this).addClass('active');
				var title = 'Bonus Pay on Time is currently applied. Do not quote customer these rates. Click to exclude Bonus Pay on Time discount';
				$("#discounts_disclaimer_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
			*/
			if ($('#discount_bonus_sumo_all').val() == 1) {
    			$('#discount_bonus_sumo_all').val(0);
			} else {
    			$('#discount_bonus_sumo_all').val(1);
			}
			compare();
		});
		$(document).on('click', '.btn-pre', function(event) {
    		/*
			var plan_id = $(this).closest('form').find('input[name="plan_id"]').val();
			if ($(this).hasClass('active')) {
				$(this).closest('form').find('input[name="discount_prepay"]').val('');
				$(this).removeClass('active');
				var title = 'Prepay discount is not applied. Click to apply Prepay discount';
			}
			else {
				$(this).closest('form').find('input[name="discount_prepay"]').val(plan_id);
				$(this).addClass('active');
				var title = 'Prepay discount is currently applied. Do not quote customer these rates. Click to exclude Prepay discount';
				$("#discounts_disclaimer_modal").modal({
					backdrop: 'static',
					keyboard: false,
				});
			}
			$(this).parent().tooltip("option", "content", title);
			$.post("/v12/get_rates",$('#rates_form1_' + plan_id).serialize(),function(response) {
				$('#table1_rate_' + plan_id).html(response.html);
			}, "json");
			return false;
			*/
			if ($('#discount_prepay_all').val() == 1) {
    			$('#discount_prepay_all').val(0);
			} else {
    			$('#discount_prepay_all').val(1);
			}
			compare();
		});
		$(document).on('click', '#discounts_disclaimer_modal .close-modal', function(event) {
			$("#discounts_disclaimer_modal").modal('hide');
			event.preventDefault();
		});
		$(document).on('click', '.filter-scroll .form-item .form-label', function(event) {
			$(this).toggleClass('active');
            $(this).parent('.form-item').find('.form-element').slideToggle();
        });
        $(document).on('click', '#development_mode', function(event) {
	        if ($(this).is(':checked')) {
		        $('.development_mode').show();
	        }
	        else {
	        	$('.development_mode').hide();  
	        }
        });
        window.signup = function(plan_id, elec_rate_id, gas_rate_id, ranking) {
	        $('#processing').show();
			$.post("/v12/signup",{plan_id:plan_id, elec_rate_id:elec_rate_id, gas_rate_id:gas_rate_id, ranking:ranking},function(response) {
				if (response) {
    				$('#processing').hide();
					window.open("https://signup.electricitywizard.com.au/admin/customers/signup/" + response.id);
				}
			});
            return false;
        }
        
        $(document).on("click", '.pause-save', function(event) {
	        $('#processing').show();
			$.post("/v12/pause",{},function(response) {
				if (response) {
    				$('#processing').hide();
					window.location.replace("/v12/");
				}
			});
            return false;
        });
        $(document).on("keyup", '#street_type', function(event) {
            street_type_lookup(this);
        });

        $('#agent_name').bind('keyup', function() {
		    agent_lookup();
	    });
	    
	    $('#electrician_name').bind('keyup', function() {
		    electrician_name_lookup();
	    });

        street_type_lookup = function(el) {
            if ($(el).val() == "") {
            	return;
            }
            $(el).autocomplete({
            	source: function( request, response ) {
            		$.ajax({
            			url: "/tools/street_type",
            			dataType: "jsonp",
            			type: "GET",
            			contentType: "application/json; charset=utf-8",
            			data: {term:$(el).val()},
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
            	delay: 1,
            	minLength: 1,
            	select: function( event, ui ) {
            	},
            	change: function (event, ui) {
            		if (ui.item == null || ui.item == undefined) {
                		$(el).val('');
            		}
        		}
            });
        }
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
		    	},
		    	change: function (event, ui) {
		    		if (ui.item == null || ui.item == undefined) {
                		$('#agent_id').val('');
                		$('#agent_name').val('');
            		}
    	    	}
		    });
        }
        lead_lookup = function() {
            $.post('/tools/get_lead_fields', {app_key: '48347de54501ba15d16d84dbcbe348fd', lead_id: $('#sid').val()}, function(data) {
        	    if (!data.first_name) {
                    alert('Lead not found');
                    $('#sid').val('');
                    return false;
                }
        	    var import_data = true;
        	    var overwrite = true;
                if ((data.sale_completion_date && data.sale_completion_date != '') && (data.agent_id && $.inArray(data.agent_id, ["125", "191", "196"]) == -1)) {
                    alert('This is an existing sale and cannot be overwritten, will import this data for ' + data.first_name);
                    overwrite = false;
                }
        	        if (import_data) {
                    $('#campaign_id').val(data.campaign_id);
                    $('#campaign_name').val(data.campaign_name);
                    $('#first_campaign').val(data.first_campaign);
                    if (data.campaign_source) {
                        $('#campaign_source').val(data.campaign_source);
                    }
                    if (data.centre_name) {
                        $('#centre_name').val(data.centre_name);
                    }
					if (data.lead_origin) {
						$('#lead_origin').val(data.lead_origin);
					}
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
                    if (data.sales_rep_name) {
                        //$('#sales_rep_name').val(data.sales_rep_name);
                        //$("#sales_rep_name").prop("readonly", true);
                    }
                    if (data.agent_name) {
                        var agent_name = data.agent_name;
                        var agent_name_arr = data.agent_name.split(", ");
                        if (agent_name_arr.length == 2) {
                            agent_name = agent_name_arr[1]+" "+agent_name_arr[0];
                        }
                        if ($("input[name='agent_name']").length > 0) {
                            $('#agent_name').val(agent_name);
                            $("#agent_name").prop("disabled", true);
                        } else {
                            $('#agent_name').text(agent_name);
                        }
                        $('#referring_agent').val(agent_name);
                    } else {
                        if ($("input[name='agent_name']").length > 0) {
                            $('#agent_name').val('');
							$("#agent_name").prop("disabled", false);
                        } else {
                            $('#agent_name').text('');
                        }
                        $('#referring_agent').val('');
                    }
                    if (data.agent_id) {
                        $('#agent_id').val(data.agent_id);
                    } else {
                        $('#agent_id').val('');
                    }
                    if (!data.sale_completion_date && !data.agent_id && data.campaign_id == 84 && (data.status == '*TestStatus' || data.status == 'New')) {
                        $("#agent_name").prop("disabled", false);
                    } else {
                        $("#agent_name").prop("disabled", true);
                    }
                    if (data.postcode) {
                        $('#postcode').val(data.postcode);
                        suburb_options(data.suburb);
                    }
                    if (data.state) {
                        $('#state').val(data.state);
                    }
                    if (data.plan_type && $('#plan_type').val() == '') {
                        $('#' + data.plan_type).trigger( 'click' );
                    }
                    if (data.customer_type) {
                        $.post("/tools/get_usage_level", {plan_type: 'Elec', customer_type: data.customer_type, version: 5}, function(response) {
                            $('#elec_usage_level_buttons').html(response.html);
                            if ($('#elec_usage_level').val()) {
                                var elec_usage_level = $('#elec_usage_level').val();
                                $('.elec-usages #' + elec_usage_level).trigger( 'click' );
                            }
                        }, "json");
                        $.post("/tools/get_usage_level", {plan_type: 'Gas', customer_type: data.customer_type, version: 5}, function(response) {
                            $('#gas_usage_level_buttons').html(response.html);
                            if ($('#gas_usage_level').val()) {
                                var gas_usage_level = $('#gas_usage_level').val();
                                $('.gas-usages #' + gas_usage_level).trigger( 'click' );
                            }
                        }, "json");
                        $('#' + data.customer_type).trigger( 'click' );
                    }
                    if (data.looking_for) {
                        var id = 'Transfer';
                        if (data.looking_for == 'Move Properties') {
                            id = 'MoveIn';
	                    }
                        $('#' + id).trigger( 'click' );
                    }
                    if (data.renant_owner) {
                        if ($("#renant_owner").length > 0) {
                            $('#renant_owner').val(data.renant_owner);
                        }
                    }
                    //alert('Data has been imported for ' + data.first_name);
                    
        	    } else {
                	alert('OK, the data has NOT been imported');
        	    }
        	    if (!overwrite) {
                	$('#sid').val('');
        	    }
            });
        }
        electrician_name_lookup = function() {
		    if ($('#electrician_name').val() == "") {
		    	return;
		    }
            $('#electrician_name').autocomplete({
		    	source: function( request, response ) {
		    		$.ajax({
		    			url: "/tools/electrician_name",
		    			dataType: "jsonp",
		    			type: "GET",
		    			contentType: "application/json; charset=utf-8",
		    			data: {term:$('#electrician_name').val()},
		    			success: function( data ) {
		    				response( $.map( data.items, function( item ) {
		    					return {
		    						label: item.name,
		    						value: item.name,
		    						id: item.id
		    					}
		    				}));
		    			}
		    		});
		    	},
		    	delay:5,
		    	minLength: 1,
		    	select: function( event, ui ) {
		    		$('#electrician_name').val(ui.item.name);
		    	},
		    	change: function (event, ui) {
		    		if (ui.item == null || ui.item == undefined) {
                		$('#electrician_name').val('');
            		}
    	    	}
		    });
        }
        $('#sid').blur(function() {
            if ($('#sid').is(':visible')) {
                if ($(this).val()) {
                	var sid = $(this).val();
                	//if (sid.length == 7) {
						lead_lookup();
					//}
                } else {
                	$('#sid').addClass('valid').removeClass('error');
                }
            }
        });
        if ($('#sid').val()) {
            if ($('#sid').is(':visible')) {
				var sid = $('#sid').val();
				//if (sid.length == 7) {
					lead_lookup();
				//}
            } else {
                $.post('/tools/get_lead_fields', {app_key: '48347de54501ba15d16d84dbcbe348fd', lead_id: $('#sid').val()}, function(data) {
            	    if ((data.sale_completion_date && data.sale_completion_date != '') && (data.agent_id && $.inArray(data.agent_id, ["125", "191", "196"]) == -1)) {
                        alert('This is an existing sale and cannot be overwritten!');
                        $('#sid').val('');
                    }
                });
            }
        }
		
		var clipboard = new Clipboard('.clipboard');
		
		$('#ToolsExportForm #ExportPlanType').change(function() {
    		$('#ToolsExportForm .nmi').hide();
    		$('#ToolsExportForm #ExportNmi').removeAttr('required');
    		$('#ToolsExportForm .tariff-code').hide();
    		$('#ToolsExportForm #ExportTariffCode').removeAttr('required');
			if ($(this).val() == 'Dual' || $(this).val() == 'Elec') {
    			$('#ToolsExportForm .nmi').show();
    			$('#ToolsExportForm #ExportNmi').attr('required', '');
    			$('#ToolsExportForm .tariff-code').show();
    			$('#ToolsExportForm #ExportTariffCode').attr('required', '');
			}	
		}).change();
		
		$('#ToolsExportForm #ExportPostcode').on('mouseleave', function() {
    		var postcode = $(this).val();
			if (postcode.length == 4) {
                $.ajax({
                    url: "/tools/suburb_options",
    				dataType: "json",
    				type: "GET",
    				contentType: "application/json; charset=utf-8",
    				data: {postcode:postcode},
    				success: function( data ) {
    					if (data.length > 0) {
    						var suburb_options = '<option value="">Please select</option>';
    						var has_suburb = false;
    						$.each(data, function(key, value) {
    							var selected = '';
    							if (value.selected == 1)  {
    								selected = 'selected="selected"';
    								has_suburb = true;	
    								
    							}
    							suburb_options += '<option value="'+value.suburb+'" '+selected+'>'+value.suburb+'</option>';
    						});
    						$('#ExportSuburb').empty().append(suburb_options);
    					}
    				}
    			});
			}
		});
		
		$('#ToolsExportForm #ExportNmi').blur(function() {
        	var nmi = $(this).val().replace(/[()]|\s|-/g, '');
        	if (nmi.length != 11) {
        		alert('Make sure the NMI is 11 digits');
        		return false;
        	}
        	else {
        		var total = 0;
        		for ( var i = 0; i < 10; i++ ) {
					var str = nmi[i].charCodeAt(0).toString();
					if (i%2 == 0) {
						for ( var j = 0; j < str.length; j++ ) {
							total += eval(str[j]);
						}
					}
					else {
						var str2 = (str*2).toString();
						for ( var k = 0; k < str2.length; k++ ) {
							total += eval(str2[k]);
						}
					}
				}
				total += eval(nmi[10]);
				if (Math.round(total / 10) * 10 != total) {
					alert('Please enter valid NMI');
        			return false;
				}
				else {
                    $.ajax({
                        url: "/tools/tariff_options",
        				dataType: "json",
        				type: "GET",
        				contentType: "application/json; charset=utf-8",
        				data: {state:$('#ToolsExportForm #ExportState').val(), customer_type: $('#ToolsExportForm #ExportCustomerType').val(), nmi: nmi, field: 'tariff1'},
        				success: function( data ) {
        					if (data.length > 0) {
        						var has_tariff = false;
        						var tariff_options = '<option value="">Please select</option>';
        						$.each(data, function(key, value) {
        							var inlcude = true;
        							var tariff_option_value = value.tariff_code;
        							if (value.child_tariff == 1) {
        								inlcude = false;
        							}
        							if (inlcude) {
        								var selected = '';
        								if (value.selected == 1)  {
        									selected = 'selected="selected"';
        									has_tariff = tariff_option_value;
        								}
        								if (value.tariff_type == 'Solar') {
        									tariff_options += '<option value="'+tariff_option_value+'" '+selected+'>'+value.tariff_code+' (Solar)</option>';
        								}
        								else {
        									tariff_options += '<option value="'+tariff_option_value+'" '+selected+'>'+value.tariff_code+'</option>';
        								}
        							}
        						});
        						$('#ToolsExportForm #ExportTariffCode').empty().append(tariff_options);
        						if (has_tariff) {
        							var has_tariff_arr = has_tariff.split('|');
        							$('#'+element).val(has_tariff).combobox("refresh");
        							if (has_tariff_arr[2] == 0 && has_tariff_arr[3] == 'Solar') {
        								$('#'+element).parent().find('.plus').hide();
        							}
        						}
        					}
        					else {
        						alert('NMI does not match state. Please select correct state and suburb above');
        						$('#ToolsExportForm #ExportTariffCode').empty().append('<option value="">Please select</option>');
        					}
        				}
        			});
				}
        	}
		});
		
		$('#ToolsLeadForm1 #renant_owner').change(function() {
    		$('#ToolsLeadForm1 .batter-storage').hide();
    		$('#ToolsLeadForm1 #batter_storage').removeAttr('required');
    		$('#ToolsLeadForm1 .batter-storage-solar').hide();
    		$('#ToolsLeadForm1 #batter_storage_solar').removeAttr('required');
			if ($('#renant_owner').val() == 'Owner') {
                if ($('#ToolsLeadForm1 #solar').val() == 'Yes') {
        			$('#ToolsLeadForm1 .batter-storage').show();
        			$('#ToolsLeadForm1 #batter_storage').attr('required', '');
    			} else if ($('#ToolsLeadForm1 #solar').val() == 'No') {
        			$('#ToolsLeadForm1 .batter-storage-solar').show();
        			$('#ToolsLeadForm1 #batter_storage_solar').attr('required', '');
    			}
            }
		}).change();
		
		$('#ToolsLeadForm1 #solar').change(function() {
    		$('#ToolsLeadForm1 .batter-storage').hide();
    		$('#ToolsLeadForm1 #batter_storage').removeAttr('required');
    		$('#ToolsLeadForm1 .batter-storage-solar').hide();
    		$('#ToolsLeadForm1 #batter_storage_solar').removeAttr('required');
			if ($('#renant_owner').val() == 'Owner') {
                if ($('#ToolsLeadForm1 #solar').val() == 'Yes') {
        			$('#ToolsLeadForm1 .batter-storage').show();
        			$('#ToolsLeadForm1 #batter_storage').attr('required', '');
    			} else if ($('#ToolsLeadForm1 #solar').val() == 'No') {
        			$('#ToolsLeadForm1 .batter-storage-solar').show();
        			$('#ToolsLeadForm1 #batter_storage_solar').attr('required', '');
    			}
            }
		}).change();
		
	});
	/*
	document.addEventListener("contextmenu", function(e) {
        e.preventDefault();
    }, false);
    */
})(jQuery);