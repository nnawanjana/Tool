(function($){
	$(document).ready(function(e) {
		 window.place_lookup = function() {
			if ($("#postcode").val() == "") {   
				 return;
			}
			$("#postcode").autocomplete({
        		source: function( request, response ) {
            		jQuery.ajax({
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
				delay:10,
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
		window.suburb_options = function() {
			jQuery.ajax({
                url: "/tools/suburb_options",
				dataType: "json",
				type: "GET",
				contentType: "application/json; charset=utf-8",
				data: {postcode:$("#postcode").val()},
				success: function( data ) {
					if (data.length > 0) {
						var suburb_options = '<option value="">Suburb</option>';
						var state = '';
						var has_suburb = false;
						$.each(data, function(key, value) {
							var selected = '';
							if (value.selected == 1)  {
								selected = 'selected="selected"';
								has_suburb = true;
							}
							suburb_options += '<option value="'+value.suburb+'" '+selected+'>'+value.suburb+'</option>';
							state = value.state;
						});
						$('#suburb').empty().append(suburb_options);
						$('#state').val(state);
						if (has_suburb === false) {
							$('#suburb').parent().find('.webform_error').remove();
							$('#suburb').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select suburb</div></div></div><div class="webform_error_bottom"></div></div>');
						}
					} else {
						$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Sorry we do not service that area</div></div></div><div class="webform_error_bottom"></div></div>');
					}
				}
			});
		}
		$('#postcode').blur(function(){
			var e = false;
			if ($('#postcode').val() == '') { // No value entered, no need validate
				e = e || false;
			} else {
				var postcode = $('#postcode').val().replace(/\s/g, '');
				if ((postcode.length != 4) || (/[^0-9-()+\s]/.test(postcode))) {
					$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your postcode</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				}		
				else if(!(/^[2-5]/.test(postcode))) {
					$('#postcode').addClass("error");
					$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Sorry we do not service that area</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;	
				} else {
					$('#postcode').removeClass("error");
					e = e || false;
				}
			}
			if ($('#postcode').val() != '' && e === false) {
				suburb_options();
			} else {
				$('#suburb').empty().append('<option value="">Suburb</option>');
			}
		});
		if ($('#postcode').val()) {
			suburb_options();
		}
		$('.radio-simulate .choice').click(function() {
            var id = $(this).prop('id');
			if (id == 'Yes') $(this).parent().addClass('active');
			if (id == 'No') $(this).parent().removeClass('active');
			$(this).parent().siblings().val(id);
        });
    	$('.plan-type').click(function() {
			$(this).addClass('active').siblings().removeClass('active');
            var id = $(this).prop('id');
			$('#plan_type').val(id);
			$('.hidden-field').hide();
			switch(id){
				case 'Dual':
					$('.eg-n').show();
				break;
				case 'Elec':
					$('.e-n').show();
				break;
				case 'Gas':
					$('.g-n').show();
				break;
				default:
				break;
			}
        });
        if ($('#plan_type').val()) {
	        var plan_type = $('#plan_type').val();
	        $('#' + plan_type).trigger( 'click' );
        }
		$('.customer-type').click(function() {
			$(this).addClass('active').siblings().removeClass('active');
            var id = $(this).prop('id');
			$('#customer_type').val(id);
        });
        if ($('#customer_type').val()) {
	        var customer_type = $('#customer_type').val();
	        $('#' + customer_type).trigger( 'click' );
        }
		$('#step1 .continue').click(function(event) {
			$('.webform_error').remove();
			var plan_type = $('#plan_type').val();
			var e = false;
			if (!plan_type) {
			    $("#plan_type").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select which product to compare</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			} else {
				if ( plan_type == 'Dual' || plan_type == 'Elec') {
			    	if ( !$('#elec_supplier2').val() ) {
			    		$('#elec_supplier2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			    		e = e || true;
			    	}
			    }
			    if ( plan_type == 'Dual' || plan_type == 'Gas') {
			    	if ( !$('#gas_supplier2').val() ) {
			    		$('#gas_supplier2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your current provider</div></div></div><div class="webform_error_bottom"></div></div>');
			    		e = e || true;
			    	}
			    }
			}
			var customer_type = $('#customer_type').val();
			if (!customer_type) {
			    $("#customer_type").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select your comparison type</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var looking_for = $('#looking_for').val();
			if (!looking_for) {
			    $("#looking_for").after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">What are you looking to do?</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var postcode = $('#postcode').val().replace(/\s/g, '');
			var suburb  = $('#suburb').val();
			if ((postcode.length != 4) || (/[^0-9-()+\s]/.test(postcode))) {
				$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your postcode</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;
			} else if(!(/^[2-5]/.test(postcode))) {
				$('#postcode').addClass("error");
				$('#postcode').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Sorry we do not service that area</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;	
			} else if ( !suburb ) {
			    	$('#suburb').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please select suburb</div></div></div><div class="webform_error_bottom"></div></div>');
			    	e = e || true;
			} else {
			    $('#postcode').val(postcode);
				$('#postcode').removeClass("error");
				e = e || false;
			}
			if (!$('#term1').is(':checked')) {
				$('#term1').addClass("error");
				$('#term1').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please agree to the terms and conditions</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;
			} else {
				$('#term1').removeClass("error");
				e = e || false;
			}
			if (!$('#term2').is(':checked')) {
				$('#term2').addClass("error");
				$('#term2').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please agree to the terms and conditions</div></div></div><div class="webform_error_bottom"></div></div>');
				e = e || true;
			} else {
				$('#term2').removeClass("error");
				e = e || false;
			}
			if (!e) {
				$('#step1_error_message').html('');
				$('#processing').show();
				$.post("/v1/compare_save/1",$('#step1_form').serialize(),function(response) {
					window.location = "/compare/2";
				});
			} else {
				$('#step1_error_message').html('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please fill out all required fields above</div></div></div><div class="webform_error_bottom"></div></div>');
				$('html,body').animate({'scrollTop': $('.webform_error:first').offset().top - 100}, 'slow');
			}
			event.preventDefault();
		});
		$('.plan-type').add('.customer-type').add('#looking_for').add('#elec_supplier2').add('#gas_supplier2').add('#term1_field').add('#term2_field').add('#step1_error_message').mouseenter(function() {
        	$(this).parent().find('.webform_error').remove();
		});
		$(':input').focus(function() {
			$(this).parent().find('.webform_error').remove();
		});
		if ($('input[name="conditional_discount"]').val()) {
			var conditional_discount = $('input[name="conditional_discount"]').val();
			$('.radio-simulate.conditional-discount-choices #' + conditional_discount).trigger( 'click' );
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
		$('#step2 .continue').click(function(event) {
			$('#processing').show();
			var sort_by = $(this).attr('id');
			$.post("/v1/compare_save/2",$('#step2_form').serialize() + "&sort_by=" + sort_by,function(response) {
				window.location = "/compare/3";
            });
            event.preventDefault();
		});
		$('.check-residential').click(function() {
			$('input[name="customer_type"]').val('RES');
			$('.check-business').removeClass('active');
			$(this).addClass('active');
			compare();
		});
		$('.check-business').click(function() {
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
					$(this).find('.find-out-more').html('Find Out More');
					$('.plans').jcarousel('reload', {
						animation: 'slow'
					});
				}
            });
        });
		$('.find-out-more,.view-details').click(function(event) {
			var plan = $(this).parents('.plan');
			if (plan.width() == 687) {
				plan.find('.plan-info').hide();
				plan.find('.plan-info').find('.plan-call-form').hide();
				plan.css('width','229px').siblings().css('display','block');
				$(this).html('View Plan Details &raquo;');
				$('.plans').jcarousel('reload', {
					animation: 'slow'
				});
			} else {
				plan.css('width','687px').siblings().css('display','none').end().find('.plan-info').show('fast');
				$(this).html('&laquo; Back to Plans');
				$('.plans').jcarousel('reload', {
					animation: 'slow'
				});
			}
			event.preventDefault();
		});
		
		$('.back-to-plans,.plan-info-close').click(function() {
			$(this).parents('.plan-info').hide('fast');
			$(this).parents('.plan-info').find('.plan-call-form').hide('fast');
			var plan = $(this).parents('.plan');
			plan.find('.view-details').html('View Plan Details &raquo;');
			plan.css('width','229px').siblings().css('display','block');
		});
		
		$('.add-to-top-picks').click(function() {
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
		    } else {
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
		        } else {
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
		        } else {
			        $('#clear_my_top_picks').removeClass('active');
		        }
		        $.cookie('top_picks', top_picks, { path: '/' });
		    }
		});
		$('#clear_my_top_picks').click(function() {
		 	if ($.cookie('top_picks') != null) {
		 		$.cookie('top_picks', null, { path: '/' });
		 		$('#top_picks_count').text('(0)');
		 		$('.add-to-top-picks img').attr('src', '/img/favor.png');
		 		$('#clear_my_top_picks').removeClass('active');
		 		window.location = "/compare/3";
		    }
		});
		if ($.cookie('top_picks') != null) {
		    var top_picks_arr = $.cookie('top_picks').split(',');
		    $('#top_picks_count').text('('+top_picks_arr.length+')');
		    $('#clear_my_top_picks').addClass('active');
		}
		$('.clear-filters').click(function() {
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
		$('.filter_plan_type').click(function() {
			compare();
		});
		$('#retailer_all').click(function() {
        	$('.retailer').prop('checked',this.checked);
			compare();
		});
		$('.retailer').change(function() {
        	var check = ($('.retailer').filter(":checked").length == $('.retailer').length);
			$('#retailer_all').prop("checked", check);
			compare();
		});
		$('#discount_type_all').click(function() {
        	$('.discount_type').prop('checked', this.checked);
			compare();
		});
		$('.discount_type').change(function() {
        	var check = ($('.discount_type').filter(":checked").length == $('.discount_type').length);
			$('#discount_type_all').prop("checked", check);
			compare();
		});
		$('#contract_length_all').click(function() {
        	$('.contract_length').prop('checked', this.checked);
			compare();
		});
		$('.contract_length').change(function() {
        	var check = ($('.contract_length').filter(":checked").length == $('.contract_length').length);
			$('#contract_length_all').prop("checked", check);
			compare();
		});
		$('#payment_options_all').click(function() {
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
		$('.step span.info').tooltip({
			position: {
				my: "center bottom-40",
				at: "center bottom",
				using: function( position, feedback ) {
				$( this ).css( position );
				$( "<div>" )
				.addClass( "arrow" )
				.addClass( feedback.vertical )
				.addClass( feedback.horizontal )
				.appendTo( this );
				}
			}
		});
		$('.registerforcallback, .callmeback-link').click(function(){
			$("#callmeback_modal").modal({
				backdrop: 'static',
				keyboard: false,
			});
		});
		$('#callmeback_modal').on('shown.bs.modal', function (e) {
			$('#name').focus();
		});
		$('#call_name').add('#call_mobile').add('#call_phone').add('#call_email').focus(function() {
			$('.webform_error').remove();
		});
		$('#call_other_number').click(function() {
        	if ($(this).prop('checked')) {
        		$('#call_phone').show();
        	} else {
        		$('#call_phone').hide();
        	}
		});
		if ($('#call_other_number').is(':checked')) {
			$('#call_phone').show();
        } else {
        	$('#call_phone').hide();
        }

		$("#call_mobile").mask("(99) 9999-9999");
        $("#call_phone").mask("(99) 9999-9999");
        var exclude_phone = ['0411123456','0412123456'];
		$('#callmeback_form .continue').click(function(event) {
			$('.webform_error').remove();
			var e = false;
			var name = $('#call_name').val();
			if ((name.length < 3) || (/\d/.test(name))) {
			    $('#call_name').addClass("error");
			    $('#call_name').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your name</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			} else {
			    $('#call_name').removeClass("error");
			    e = e || false;
			}
			var mobile = $('#call_mobile').val().replace(/[()]|\s|-/g, '');
			if (mobile.length > 0) {
			    if ((mobile.length != 8 && mobile.length != 10) || !(/^(?!.*(\d)\1{4})\d{8,10}$/.test(mobile)) || $.inArray(mobile, exclude_phone) != -1) {
			    	$('#call_mobile').addClass("error");
			    	$('#call_mobile').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile</div></div></div><div class="webform_error_bottom"></div></div>');
			    	e = e || true;
			    } else if (!(/^(02|03|04|07|08)\d{8}$/.test(mobile)) && !(/^(1300|1800)\d{6}$/.test(mobile))) {
			    	$('#call_mobile').addClass("error");
			    	$('#call_mobile').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Oops! It looks like you forgot to put in your area code. (e.g. 03 for VIC)</div></div></div><div class="webform_error_bottom"></div></div>');
			    	e = e || true;
			    } else {
			    	$('#call_mobile').val(mobile);
			    	$('#call_mobile').removeClass("error");
			    	e = e || false;
			    }
			}
			var phone = $('#call_phone').val().replace(/[()]|\s|-/g, '');
			if (phone.length > 0) {
			    if ((phone.length != 8 && phone.length != 10) || !(/^(?!.*(\d)\1{4})\d{8,10}$/.test(phone)) || $.inArray(phone, exclude_phone) != -1) {
			    	$('#call_phone').addClass("error");
			    	$('#call_phone').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter valid number</div></div></div><div class="webform_error_bottom"></div></div>');
			    	e = e || true;
			    } else if (!(/^(02|03|04|07|08)\d{8}$/.test(phone)) && !(/^(1300|1800)\d{6}$/.test(phone))) {
			    	$('#call_phone').addClass("error");
			    	$('#call_phone').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Oops! It looks like you forgot to put in your area code. (e.g. 03 for VIC)</div></div></div><div class="webform_error_bottom"></div></div>');
			    	e = e || true;
			    } else {
			    	$('#call_phone').val(phone);
			    	$('#call_phone').removeClass("error");
			    	e = e || false;
			    }
			}
			if (mobile.length <= 0 && phone.length <= 0) {
			    $('#call_mobile').addClass("error");
			    $('#call_mobile').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your mobile</div></div></div><div class="webform_error_bottom"></div></div>');
			    e = e || true;
			}
			var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			var email = $('#call_email').val();
			if (email.length > 0) {
				if (!reg.test(email)) {
					$('#call_email').addClass("error");
					$('#call_email').after('<div class="webform_error"><div class="webform_error_left"><div class="webform_error_right"><div class="webform_error_center">Please enter your email</div></div></div><div class="webform_error_bottom"></div></div>');
					e = e || true;
				} else {
					$('#call_email').removeClass("error");
					e = e || false;
				}
			}
			if (!e) {
				$('#callmeback_modal_processing').show();
				$('#callmeback_error_message').html('');
				$.post("/v1/call_me_back",$('#callmeback_form').serialize(),function(response) {
					$('#callmeback_modal_processing').hide();
					$("#callmeback_modal").modal('hide');
					$("#confirmation_modal").modal('show');
					window['optimizely'] = window['optimizely'] || [];
					window.optimizely.push(["trackEvent", "v1_callmeback_formsubmission"]);
					//window.location = "/v1/compare/3";
				});
			}
			event.preventDefault();
		});
	});
})(jQuery);