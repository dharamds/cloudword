var SchoolGroupsSetCreate = function (){
	var webroot,siteID,csrf_tel_library;
	

	var getOpenGroupSetPopUp = function(){
		// $("#setOfGrpupsModal").modal();
		//CommanJS.getPopupWithExternalView("New Group Set",webroot+"containers/SchoolsGroups/getGroupSetView/"+siteID,"","xlarge");
		$.ajax({
 			url      :   webroot+"containers/SchoolsGroups/getGroupSetView",
 			method   :   "POST",
 			data     :  $('#setupschoolgroup').serialize() + "&siteID=" + siteID + "&csrf_tel_library="+csrf_tel_library,
 			dataType : "html", 				
 			success: function (result) { 
 				$("#popup_model_body").html(result);	
 				$("#setOfGrpupsModal").modal();
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
	                    	'csrf_tel_library':csrf_tel_library
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
				getOpenGroupSetPopUp();
				return false;
			},
			errorPlacement: function(error, $elem) {
				error.insertAfter($elem);
	            if ($elem.attr("type") == "radio") {
	                error.insertAfter($elem.parent().siblings('label'));
	            }
	            if ($elem.attr("type") == "number") {
	                $elem.insertAfter($elem.next('div'));
	                error.insertAfter($elem.parent().parent().parent());
	            }
        	}
		});
	}


	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		csrf_tel_library = dataVariable.csrf_tel_library;
			

		//getSiteUsers();
		//getSiteGroupLeaders();
		jQuery.validator.addMethod("noSpace", function(value, element) {
			return value == '' || value.trim().length != 0;  
		}, "Space is not allow");

		$(".create_group_set_popup").on('click', function(){
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
		        	$('#selected_section').css('opacity','0.5');
				},
				success: function (response) {
					$('#selected_section').css('opacity','1');
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
		init:init
	}	

}();