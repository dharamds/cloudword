var SchoolGroupsCreate = function (){
	var webroot,siteID,recordID,groupData,csrf_tel_library;

	var getViewEnrollUsersSection = function(groupID,siteID,courseID,sectionID){
		$.ajax({
 			url      :   webroot+"containers/SchoolsGroups/get_group_user_enroll_section",
 			method   :   "POST",
 			data     :  {'groupID':groupID,'siteID':siteID,'courseID':courseID,'sectionID':sectionID,'csrf_tel_library':csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#enroll_users_section").html(result);	
 			}
 		});
	}

	var getViewEnrollUsersListing = function(groupID,siteID,courseID,sectionID){
		$.ajax({
 			url      :   webroot+"containers/SchoolsGroups/get_group_user_enroll_listing",
 			method   :   "POST",
 			data     :  {'groupID':groupID,'siteID':siteID,'courseID':courseID,'sectionID':sectionID,'csrf_tel_library':csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#enroll_users_listing").html(result);	
 			}
 		});
	}

	var submitGroupForm = function(){
		$.ajax({
 			url      :   webroot+"containers/SchoolsGroups/saveGroups",
 			method   :   "POST",
 			data     :  $('#setupschoolgroup').serialize() + "&siteID=" + siteID + "&csrf_tel_library="+csrf_tel_library,
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);	
 				if(result.saveType == 'save_add_new'){
 					$("#group_id").val('');
 					$("#submit_type").val('');
 					$("#selected_course").val('');
 					$("#group_name").val('');
 					$("#group_section_id").empty(); 
            		$("#group_section_id").append('<option value="">--Section--</option>');
 					$('input[name=group_avaibility]').attr('checked',false);
 					$('.save_add_new').html('Save & Add New');
 					$('.save_enroll').html('Save & Enroll Users');

 					$("#enroll_users_section").html('');
 				}

 				if(result.saveType == 'save_enroll'){
 					$("#group_id").val(result.group_id);
 					getViewEnrollUsersSection(result.group_id,siteID,$("#selected_course").val(),$("#group_section_id").val());
 				}			
 			}
 		});
	}

	var createGroupFormValidator = function() {
		$("#setupschoolgroup").validate({
			ignore: [],
			rules: {
				course_id: {
					required : true
				},
				group_section_id: {
					required : true
				},
				group_name: {
					required : true,
					noSpace : true,
					remote:{
						url: webroot +"containers/SchoolsGroups/getCheckGroupName",
						type: 'POST',

						data:
	                    {
	                    	course_id: function()
	                    	{
	                    		return $('#selected_course').val();
	                    	},
	                    	group_section_id: function()
	                    	{
	                    		return $('#group_section_id').val();
	                    	},
	                    	group_id: function()
	                    	{
	                    		return $('#group_id').val();
	                    	},
	                    	'siteID':siteID,
	                    	'csrf_tel_library' : csrf_tel_library
	                    }
					}
				},
				group_avaibility: {
					required : true
				}
			},
			messages: {
				course_id: {
					required: "Please select course"
				},
				group_section_id: {
					required: "Please select section"
				},
				group_name: {
					required : "Please enter name",
					remote   : "Group name is already exist or must be select course/section" 
				},
				group_avaibility: {
					required : "Please select radio"
				}
			},   
			submitHandler: function (form) { 
				submitGroupForm();
				return false;
			},
			errorPlacement: function(error, $elem) {
	            error.insertAfter($elem);
	            if ($elem.attr("type") == "radio") {
	            	error.insertAfter($elem.parent().parent().siblings('div'));
	            }
	            
        	}
		});
	}


	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		recordID = dataVariable.recordID;
		groupData = dataVariable.groupData;
		csrf_tel_library = dataVariable.csrf_tel_library;

		if(recordID > 0)
		{
			var selectedOption;
			$.ajax({
				type: "POST",
				url: webroot+'containers/schoolsCourse/get_course_sections_by_id',
				dataType : 'json',
				data: {"course_id": $("#selected_course").val(),'siteID':siteID,'csrf_tel_library':csrf_tel_library},
				beforeSend: function() {
		        	$('#selected_section').css('opacity','0.5');
				},
				success: function (response) {
					$('#selected_section').css('opacity','1');
					var options = '<option value="">--Section--</option>';
					if(response.code == 200){
						$.each(response.data, function (key, value) {
							selectedOption =  ((value.id == groupData.section_id ) ? "selected" : "");
							options = options + "<option value=" + value.id + "  "+selectedOption+">" + value.name + "</option>";
						});						
					}else if(response.code == 400){
						CommanJS.getDisplayMessgae("400","Sorry, No section available of selected course.");
					}
					$("#group_section_id").html(options);
					getViewEnrollUsersSection(recordID,siteID,$("#selected_course").val(),$("#group_section_id").val());
				}
			});

			getViewEnrollUsersListing(recordID,siteID,$("#selected_course").val(),groupData.section_id);
		}	

		jQuery.validator.addMethod("noSpace", function(value, element) {
			return value == '' || value.trim().length != 0;  
		}, "Space is not allow");

		$(".save_enroll").on('click', function(){
			$("html, body").animate({ scrollTop: $(".save_enroll").offset().top }, 1000);
			$("#submit_type").val('save_enroll');
			$("#setupschoolgroup").submit();
			return false;
		});

		$(".save_add_new").on('click', function(){
			$("#submit_type").val('save_add_new');
			$("#setupschoolgroup").submit();
			return false;
		});

		createGroupFormValidator();
		$('#groups').on('change', '#selected_course', function () {
			$.ajax({
				type: "POST",
				url: webroot+'containers/schoolsCourse/get_course_sections_by_id',
				dataType : 'json',
				data: {"course_id": $(this).val(),'siteID':siteID,'csrf_tel_library':csrf_tel_library},
				beforeSend: function() {
		        	$('#group_section_id').css('opacity','0.5');
				},
				success: function (response) {
					$('#group_section_id').css('opacity','1');
					var options = '<option value="">--Section--</option>';
					if(response.code == 200){
						$.each(response.data, function (key, value) {
							options = options + "<option value=" + value.id + ">" + value.name + "</option>";
						});						
					}else if(response.code == 400){
						CommanJS.getDisplayMessgae("400","Sorry, No section available of selected course.");
					}
					//console.log(options);
					$("#group_section_id").html(options);
				}
			});
		});

		$(".back_to_group_list").on('click', function(){
			getViewTemplate('groups',siteID); 
		});

	}		

	return {
		init:init,
		getViewEnrollUsersSection:getViewEnrollUsersSection,
		getViewEnrollUsersListing:getViewEnrollUsersListing
	}	

}();