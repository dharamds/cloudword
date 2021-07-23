var SchoolVisual = function (){
	var webroot,siteID,csrf_tel_library;

	var submitVisualForm = function(form) {
		// e.preventDefault();
		var formData = new FormData(form);
		formData.append('siteID', siteID);
		formData.append('csrf_tel_library',csrf_tel_library);
		 $.ajax({
 			url      :   webroot+"containers/schools/saveVisual",
 			method   :   "POST",
 			data:formData,
 			cache          :   false,
 			contentType    :   false,
 			processData    :   false,
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);	
 				$("#tabslist li").removeClass("active");
 				$("#visuals").removeClass("active"); 				
 				$("#courses").addClass("active");
 				$('.courses').addClass('active');
 				getViewTemplate('courses',siteID);	
 			}
 		});
	}


	var createVisualFormValidator = function() {
		$("#setupvisual").validate({
			ignore: [],
			rules: {
				logo: {
					/*required : true,*/
					extension: "jpeg|jpg|png"
				}
			},
			messages: {
				logo: {
					required: "Please select logo",
					extension: "Please select valide file"
					          
				}
			},   
			submitHandler: function (form) { 
				submitVisualForm(form);
				return false;
			}
		});
	}

	var getDisplayImage = function(input){
		if (input.files && input.files[0]) {
		 	var reader = new FileReader();
		 	
		 	// for extension
		 	var val = $(input).val();
		 	var ext = val.substring(val.lastIndexOf('.') + 1).toLowerCase(); 
		 	if($.inArray(ext, ['png','jpg','jpeg']) == -1) {
		 		$(input).val('');
			    CommanJS.getDisplayMessgae('400',"invalid file!, Only JPG,JPEG and PNG allow");	
		 		return false;
		 	}		 	
		 	
		 	// for size
		 	var a=(input.files[0].size);
			if(Math.round(a/(1024*1024)) > 1){ // make it in MB so divide by 1024*1024
		 		$(input).val('');
			    CommanJS.getDisplayMessgae('400',"Logo size less than 1 MB");	
			    return false;
			}
		 	reader.onload = function(e) {
		 		$('#ist_logo_id').attr('src', e.target.result);
		 	}
		 	reader.readAsDataURL(input.files[0]);
		 	
		 	
		 	/*var myImg = document.querySelector("#ist_logo_id");
		 	console.log(myImg);
		 	
	        var realWidth = myImg.naturalWidth;
	        var realHeight = myImg.naturalHeight;
	        alert("Original width=" + realWidth + ", " + "Original height=" + realHeight);*/
		}
	}

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		csrf_tel_library = dataVariable.csrf_tel_library;



		/*$.validator.addMethod('filesize', function (value, element, param) {
		    return this.optional(element) || (element.files[0].size <= param)
		}, 'File size must be less than {0}');	*/	

		$('.cpicker').colorpicker();

		$("#dis_img").change(function() {
			getDisplayImage(this);
		});
		
		$('#header_back_color').on('keyup change', function(){
			$("#previewHeader").css("background-color", this.value);
			$(this).siblings('span').children('i').css('background-color',this.value);
		});
		$('#header_text_color').on('keyup change', function(){
			$("#previewHeader").css("color", this.value);
			$(this).siblings('span').children('i').css('background-color',this.value);
		});

		
		$('#footer_back_color').on('keyup change', function(){
			$("#previewFooter").css("background-color", this.value);
			$(this).siblings('span').children('i').css('background-color',this.value);
		});
		$('#footer_text_color').on('keyup change', function(){
			$("#previewFooter").css("color", this.value);
			$(this).siblings('span').children('i').css('background-color',this.value);
		});


		$('#button_back_color').on('keyup change', function(){
			$(".previewButton button").css("background-color", this.value);
			$(this).siblings('span').children('i').css('background-color',this.value);
		});
		$('#button_text_color').on('keyup change', function(){
			$(".previewButton button").css("color", this.value);
			$(this).siblings('span').children('i').css('background-color',this.value);
		});

		$('#panel_back_color').on('keyup change', function(){
			$(".previewpanel button").css("background-color", this.value);
			$(this).siblings('span').children('i').css('background-color',this.value);
		});
		$('#panel_text_color').on('keyup change', function(){
			$(".previewpanel button").css("color", this.value);
			$(this).siblings('span').children('i').css('background-color',this.value);
		});

		createVisualFormValidator();


		$(".back_to_contact").on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#visuals").removeClass("active");
			$("#contactInfo").addClass("active");
			$('.contactInfo').addClass('active');
			getViewTemplate('contactInfo',siteID);	
		});

	}		

	return {
		init:init
	}	

}();