var SchoolContact = function (){
	
	var webroot,states,siteID;
	

	var submitContactForm = function() {
		$.ajax({
 			url      :   webroot+"containers/schools/saveContact",
 			method   :   "POST",
 			data     :  $('#setupcontact').serialize()+ "&siteID=" + siteID,
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);	
 				if(result.code == 200){
	 				$("#tabslist li").removeClass("active");
	 				$("#contactInfo").removeClass("active");
	 				$("#visuals").addClass("active");
	 				$('.visuals').addClass('active');
	 				getViewTemplate('visuals',siteID);
	 			}		
 			}
 		});
	}


	var createContactFormValidator = function() {
		$.validator.addMethod(
			"cust_mobile",
			function(value, element) {
				var regEx = /^[+]?\d+$/;
				var re = new RegExp(regEx);
				return this.optional(element) || re.test(value);
			},
			"Please enter a valid number."
		);
		$("#setupcontact").validate({
			ignore: [],
			rules: {
				"institution[institution_address][street_1]": {
					required : true,
					noSpace  : true
				},
				"institution[institution_address][street_2]": {
					/*required : true,*/
					noSpace  : true
				},
				"institution[institution_address][state]": {
					required : true,
					noSpace  : true
				},
				"institution[institution_address][city]": {
					required : true,
					noSpace  : true
				},
				"institution[institution_address][zip]": {
					required : true,
					noSpace  : true,
					maxlength: 8
				}/*,
				"institution[primary][title]": {
					required : true
				}*/,
				"institution[primary][name]": {
					required : true,
					noSpace  : true
				},
				"institution[primary][email]": {
					required : true,
					noSpace  : true,
					emailExt : true
				},
				"institution[primary][pri_phone]": {
					required : true,
					noSpace  : true,
					cust_mobile:true,
					maxlength:25
					/*number  : true,*/
					/*phoneUS : true*/
				},
				"institution[primary][sec_phone]": {
					/*required : true,*/
					noSpace  : true,
				/*	number  : true,*/
					/*phoneUS : true*/
					cust_mobile:true,
					maxlength:25
				},
				"institution[instructional_support][name]": {
					noSpace  : true
				},
				"institution[instructional_support][email]": {
					noSpace  : true,
					emailExt : true
				},
				"institution[instructional_support][pri_phone]": {
					/*number  : true,*/
					/*phoneUS : true*/
					cust_mobile:true,
					maxlength:25
				},
				"institution[instructional_support][sec_phone]": {
					/*number  : true,*/
					/*phoneUS : true*/
					cust_mobile:true,
					maxlength:25
				},
				"institution[technical][name]": {
					noSpace  : true
				},
				"institution[technical][email]": {
					noSpace  : true,
					emailExt : true
				},
				"institution[technical][pri_phone]": {
					/*number  : true,*/
					/*phoneUS : true*/
					cust_mobile:true,
					maxlength:25
				},
				"institution[technical][sec_phone]": {
					/*number  : true,*/
					/*phoneUS : true*/
					cust_mobile:true,
					maxlength:25
				},
				"institution[billing][name]": {
					noSpace  : true
				},
				"institution[billing][email]": {
					noSpace  : true,
					emailExt : true
				},
				"institution[billing][pri_phone]": {
					/*number  : true,*/
					/*phoneUS : true*/
					cust_mobile:true,
					maxlength:25
				},
				"institution[billing][sec_phone]": {
					/*number  : true,*/
					/*phoneUS : true*/
					cust_mobile:true,
					maxlength:25
				},
				"institution[site_manager_1][name]": {
					noSpace  : true
				},
				"institution[site_manager_1][email]": {
					noSpace  : true,
					emailExt : true
				},
				"institution[site_manager_1][pri_phone]": {
					cust_mobile:true,
					maxlength:25
				},
				"institution[site_manager_1][sec_phone]": {
					cust_mobile:true,
					maxlength:25
				},
				"institution[site_manager_2][name]": {
					noSpace  : true
				},
				"institution[site_manager_2][email]": {
					noSpace  : true,
					emailExt : true
				},
				"institution[site_manager_2][pri_phone]": {
					cust_mobile:true,
					maxlength:25
				},
				"institution[site_manager_2][sec_phone]": {
					cust_mobile:true,
					maxlength:25
				}
			},
			messages: {
				"institution[institution_address][street_1]": {
					required: "Please enter street address 1"
					          
				},
				"institution[institution_address][street_2]": {
					required: "Please enter street address 2"					          
				},
				"institution[institution_address][state]": {
					required: "Please select state"					          
				},
				"institution[institution_address][city]": {
					required: "Please select city"					          
				},
				"institution[institution_address][zip]": {
					required: "Please enter zip"					          
				},
				"institution[primary][title]": {
					required: "Please select title"					          
				},
				"institution[primary][name]": {
					required: "Please enter name"					          
				},
				"institution[primary][email]": {
					required: "Please enter email"					          
				},
				"institution[primary][pri_phone]": {
					required: "Please enter primary phone",
					number :  "Invalid phone number"			          
				},
				"institution[primary][sec_phone]": {
					required: "Please enter secondary phone",
					number :  "Invalid phone number"				          
				},
				"institution[instructional_support][pri_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[instructional_support][sec_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[technical][pri_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[technical][sec_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[billing][pri_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[billing][sec_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[technical][sec_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[site_manager_1][pri_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[site_manager_1][sec_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[site_manager_2][pri_phone]": {
					number :  "Invalid phone number"	
				},
				"institution[site_manager_2][sec_phone]": {
					number :  "Invalid phone number"	
				}
			},   
			submitHandler: function (form) { 
				submitContactForm();
				return false;
			}
		});
	}

	var substringMatcher = function(strs) {
		return function findMatches(q, cb) {
		    var matches, substrRegex;

		    // an array that will be populated with substring matches
		    matches = [];

		    // regex used to determine if a string contains the substring `q`
		    substrRegex = new RegExp(q, 'i');

		    // iterate through the pool of strings and for any string that
		    // contains the substring `q`, add it to the `matches` array
		    $.each(strs, function(i, str) {
		        if (substrRegex.test(str)) {
		          	// the typeahead jQuery plugin expects suggestions to a
		          	// JavaScript object, refer to typeahead docs for more info
		         	matches.push({ value: str });
		      	}
		 	});

	      	cb(matches);
	  };
	}

	var autocompleteState = function() {
		$( ".states" ).autocomplete({
			source: states,
			minLength:0,
			select: function (event, ui) {
		        getCities(ui.item.value);
		    },
		    search  : function(){$(this).addClass('loading');},
			open    : function(){$(this).removeClass('loading');}
		}).focus(function(){
            if (this.value == ""){
	            $(this).autocomplete("search");
	        }
        });
	}
	
	

	var autocompleteCity = function(result) {
		$( ".city" ).autocomplete({
			source: result.data,
			minLength:0,
			search  : function(){$(this).addClass('loading');},
			open    : function(){$(this).removeClass('loading');}
		}).focus(function(){
            if (this.value == ""){
	            $(this).autocomplete("search");
	        }
        });		
	}

	var getCities = function(state) {
		
		$.ajax({
 			url      :  webroot+"containers/schools/get_city_per_state",
 			method   :   "POST",
 			data     : {state:state},
 			dataType : "json", 				
 			success: function (result) { 
 				autocompleteCity(result);
 			}
 		});
		
	}

	var getSelectSameAsPrimary = function(selectorID, element) {
		if($(element).is(':checked')){
			$("#"+selectorID).hide();
		}else{
			$("#"+selectorID).show();	
		}
	}

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		states  = dataVariable.states;
		siteID  = dataVariable.siteID;

		//$('.usphone').usPhoneFormat();
		jQuery.validator.addMethod("noSpace", function(value, element) { 
			return value == '' || value.trim().length != 0;  
		}, "Space is not allow");

		jQuery.validator.addMethod("emailExt", function(value, element, param) {
			return value == '' ||  value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
		},"Please enter valide email address");

		/*  jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length > 9 &&
                phone_number.match(/^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/);
        }, "Invalid phone number");*/

        jQuery.validator.addMethod("phoneUS", function (phone_number, element) {
        	console.log(phone_number);
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length > 9 &&
                    phone_number.match(/^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/);
        }, "Invalid phone number");

		

		createContactFormValidator();
		autocompleteState();

		$(".back_to_identity").on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#identity").addClass("active");
			$("#contactInfo").removeClass("active");
			$('.identity').addClass('active');
			getViewTemplate('identity',siteID);	
		});
	}		

	return {
		init:init,
		getSelectSameAsPrimary:getSelectSameAsPrimary
	}	

}();