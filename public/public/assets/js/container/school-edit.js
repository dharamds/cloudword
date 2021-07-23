var SchoolEdit = function (){
	
	var webroot,record_id;
	
	var getDisplayImage = function(input){
		if (input.files && input.files[0]) {
		 	var reader = new FileReader();

		 	reader.onload = function(e) {
		 		$('#img').attr('src', e.target.result);
		 	}
		 	reader.readAsDataURL(input.files[0]);
		}
	}

	var createFormValidator = function() {
		$("#setupschool").validate({
			rules: {
				name: {
					required : true,
					noSpace  : true,
					remote:{
						url: webroot +"containers/schools/getCheckName",
						type: 'POST',
						date : record_id
					}
				},
				slogan: {
					required : true,
					noSpace  : true
				},
				domain: {
					url : true, 
						normalizer: function( value ) {
							var url = value;
							if ( url && url.substr( 0, 7 ) !== "http://"
							&& url.substr( 0, 8 ) !== "https://"
							&& url.substr( 0, 6 ) !== "ftp://" ) {
								url = "http://" + url;
							}
							return url;
						}
				},
				email: {
					required : true,
					noSpace  : true,
					emailExt : true
				},
				phone: {
					required : true,
					noSpace  : true,
					number : true
				},
				address: {
					required : true,
					noSpace  : true
				},
				logo: {
					required : true
				}
			},
			messages: {
				name: {
					required: "Please enter name",
					remote   : "Name is already exist, please try with new",              
				},
				slogan: {
					required: "Please enter slogan"               
				},
				domain : {
					required: "Please enter valide domain"            
				},
				email: {
					required: "Please enter email"               
				},
				phone: {
					required: "Please enter phone number",
					number  : "Please enter valid phone number"               
				},
				address: {
					required: "Please enter address"               
				},
				logo: {
					required: "Please select site logo"               
				}
			},   
			submitHandler: function (form) { 
				return true;
			}
		});
	}
	var init = function (dataVariable) {
		//console.log(dataVariable);
		webroot = dataVariable.ajax_call_root;
		record_id = dataVariable.record_id;	

		jQuery.validator.addMethod("noSpace", function(value, element) { 
			return value == '' || value.trim().length != 0;  
		}, "Space is not allow");

		jQuery.validator.addMethod("emailExt", function(value, element, param) {
			return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
		},"Please enter valide email address");

		$(".dis_img").change(function() {
			getDisplayImage(this);
		});

		createFormValidator();

		$('#summernote').summernote();

		$("#text_font").on('click', function(){
			$("#demo-font-text").css('font-family',this.value);
		});
	}		

	return {
		init:init
	}	

}();