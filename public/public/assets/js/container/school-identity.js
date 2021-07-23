var SchoolIdentity = function (){
	var webroot,siteID,siteIDEncoded;
	var getActiveCKEDITOR = function(input){

		CKEDITOR.replace( 'editor2', {
            toolbar : 'short',
         });
	    CKEDITOR.replace( 'editor1', {
            toolbar : 'short',
         });
	}

	var submitIdentityForm = function() {
		$.ajax({
 			url      :   webroot+"containers/schools/saveIdentity",
 			method   :   "POST",
 			data     :  $('#setupschool').serialize() + "&siteID=" + siteID,
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);	
 				if(result.code == 200){
 					$("#tabslist li").removeClass("active");
	 				$("#identity").removeClass("active");
	 				$("#contactInfo").addClass("active");
	 				$('.contactInfo').addClass('active');
	 				$('.institute_tab_management').attr('data-siteid',result.containerID);
	 				getViewTemplate('contactInfo',result.containerID);	
 				} 				
 			}
 		});
	}


	var createFormValidator = function() {
		jQuery.validator.addMethod("nameRegex", function(value, element) {
    	        return this.optional(element) || /^[a-z0-9-_\\s]+$/i.test(value);
    	    }, "Name must contain only letters & number");

		$("#setupschool").validate({
			ignore: [],
			rules: {
				name: {
					required : true,
					noSpace  : true
				},
				domain: {
					required : true,
					noSpace  : true,
					nameRegex : true,
					/*url : true, 
						normalizer: function( value ) {
							var url = value;
							if ( url && url.substr( 0, 7 ) !== "http://"
							&& url.substr( 0, 8 ) !== "https://"
							&& url.substr( 0, 6 ) !== "ftp://" ) {
								url = "http://" + url;
							}
							return url;
						}
					,*/
					remote:{
						url: webroot +"containers/schools/getCheckExist",
						type: 'POST',
						data: {'siteID':siteID,'csrf_tel_library':csrf_tel_library}
					}	
				},
				
			},
			messages: {
				name: {
					required: "Please enter name"
					          
				},
				slogan: {
					required: "Please enter slogan"               
				},
				domain : {
					required: "Please enter domain name",
					url: "Please enter valide domain name",
					remote   : "Site domain is already exist, please try with new"               
				},
				home_page_text : {
					required:"Please enter some content"
				},
				home_footer_text : {
					required:"Please enter some content"
				}
			},   
			submitHandler: function (form) { 
				submitIdentityForm();
				return false;
			},
			errorPlacement: function(error, $elem) {
	            if ($elem.is('textarea')) {
	                $elem.insertAfter($elem.next('div'));
	            }
	            error.insertAfter($elem);
	            if($elem.attr('id') == "domain"){
	            	//console.log(error.insertAfter($elem.parent('div')))
	            }

        	}
		});
	}

	
	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		siteIDEncoded  = dataVariable.siteIDEncoded;

		csrf_tel_library = dataVariable.csrf_tel_library;		

		jQuery.validator.addMethod("noSpace", function(value, element) {
			return value == '' || value.trim().length != 0;  
		}, "Space is not allow");

		jQuery.validator.addMethod("emailExt", function(value, element, param) {
			return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
		},"Please enter valide email address");
		

		createFormValidator();
		getActiveCKEDITOR();

		$(".cancel_request").on('click', function(){
			window.location.href = webroot+"institutions/edit/"+siteIDEncoded;
		});
		
	}		

	return {
		init:init
	}	

}();