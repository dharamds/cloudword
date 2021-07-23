var SchoolMetadata = function (){
	
	var webroot,states,containerID,csrf_tel_library;
	
	var submitMetaDataForm = function() {
		$.ajax({
 			url      :   webroot+"containers/schoolsMetadatas/saveMetadata",
 			method   :   "POST",
 			data     :  $('#setupmetadata').serialize(),
 			dataType : "json", 				
 			success: function (result) { 
 				/*getDisplayMessgae(result.code,result.message);	
 				$("#tabslist li").removeClass("active");
 				$("#contactInfo").removeClass("active");
 				$("#visuals").addClass("active");
 				$('.visuals').addClass('active');
 				getViewTemplate('visuals');	*/
 			}
 		});
	}


	

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		containerID = dataVariable.containerID;
		csrf_tel_library = dataVariable.csrf_tel_library;
		
		// for tag
		CommanJS.getTagSection(3,'institute_default_section','institution_default_tag',containerID,3);
		CommanJS.getTagSection(1,'institute_system_section','institution_system_tag',containerID,3);

		// for category
		CommanJS.getCatSection(3,'institute_cat_section','institution_default_cat',containerID,3);
		CommanJS.getCatSection(1,'system_cat_section','institution_system_cat',containerID,3);


		$(".back_to_course").on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#courses").addClass("active");
			$("#metadata").removeClass("active");
			$('.courses').addClass('active');
			getViewTemplate('courses',containerID);
		});

		$(".next_to_user").on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#users").addClass("active");
			$("#metadata").removeClass("active");
			$('.users').addClass('active');
			getViewTemplate('users',containerID);	
		});

		
		Utility.animateContent();
	}		

	return {
		init:init

	}	

}();