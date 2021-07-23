var SchoolGroupsUserEnrollSection = function (){
	var webroot,siteID,courseID,sectionID,groupID,csrf_tel_library;
	
	
	var getSiteUsers = function(pageURL,displayLocation) {
		var url;
		var keyword = $('#search_text_box').val();
		if(pageURL != 'NO_URL'){
			url = pageURL;
		}else{
			url = webroot+"containers/SchoolsGroups/getSiteUsers";
		}
		$.ajax({
 			url      :   url,
 			method   :   "POST",
 			data:{'keyword':keyword,'siteID':siteID,'groupID':groupID,'courseID':courseID,'sectionID':sectionID,'csrf_tel_library':csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#"+displayLocation).html(result);
 			}	
		});
	}


	var getSiteGroupUsers = function(pageURL,displayLocation,type) {
		var url;
		var keyword = $('#search_text_box').val();
		if(pageURL != 'NO_URL'){
			url = pageURL;
		}else{
			url = webroot+"containers/SchoolsGroups/getSiteUsers";
		}
		$.ajax({
 			url      :   url,
 			method   :   "POST",
 			data:{'keyword':keyword,'siteID':siteID,'utype':type,'groupID':groupID,'courseID':courseID,'sectionID':sectionID,'csrf_tel_library':csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#"+displayLocation).html(result);
 			}	
		});
	}


	var getMakeEnrollUserInGroup = function(userID,ele){
		var actionType;
		actionType = $(ele).closest('div').attr('data-for');
		$.ajax({
 			url      :   webroot+"containers/SchoolsGroups/get_enroll_users_in_group",
 			method   :   "POST",
 			data:{'userID':userID,'actionType':actionType,'siteID':siteID,'courseID':courseID,'sectionID':sectionID,'groupID':groupID,'csrf_tel_library':csrf_tel_library},
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);
 				if(result.code == 200){
 					//getSiteGroupUsers('NO_URL',displaySection,section);
 					getSiteGroupUsers('NO_URL','site_group_leaders','group_leader');
					getSiteGroupUsers('NO_URL','site_group_users','student');
					SchoolGroupsCreate.getViewEnrollUsersListing(groupID,siteID,courseID,sectionID);
 				}
 			}	
		});
		return false;
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
 					$("#setupschoolgroup")[0].reset();
 				}

 				if(result.saveType == 'save_enroll'){
 					$("#group_id").val(result.group_id);
 					//getViewEnrollUsersSection(siteID,1,2);
 				}			
 			}
 		});
	}

	
	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		sectionID = dataVariable.sectionID;
		courseID  = dataVariable.courseID;
		groupID   = dataVariable.groupID;
		csrf_tel_library = dataVariable.csrf_tel_library;

		getSiteUsers('NO_URL','site_enroll_users');
		getSiteUsers('NO_URL','site_enroll_users_secondary');


		getSiteGroupUsers('NO_URL','site_group_leaders','group_leader');
		getSiteGroupUsers('NO_URL','site_group_users','student');
		
		$('#selected_course').on('change', function () {
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
					$("#section_id").html(options);
				}
			});
		});

		$(".back_to_group_list").on('click', function(){
			getViewTemplate('groups',siteID); 
		});

		$('#site_enroll_users').on('click','.pagination li a', function(e){
			e.preventDefault();
			getSiteUsers($(this).attr('href'),'site_enroll_users');			
		});

		$('.search_site_users').on('keyup', function(){
			if($(this).val().length >= 3 || $(this).val() == ''){
				$("#search_text_box").val($(this).val());
				getSiteUsers('NO_URL','site_enroll_users');
			}
		});

		$('#site_enroll_users_secondary').on('click','.pagination li a', function(e){
			e.preventDefault();
			getSiteUsers($(this).attr('href'),'site_enroll_users_secondary');		
		});

		$('.search_site_users_secondery').on('keyup', function(){
			if($(this).val().length >= 3 || $(this).val() == ''){
				$("#search_text_box").val($(this).val());
				getSiteUsers('NO_URL','site_enroll_users_secondary');
			}
		});


		$('#site_group_leaders').on('click','.pagination li a', function(e){
			e.preventDefault();
			getSiteGroupUsers($(this).attr('href'),'site_group_leaders','group_leader');			
		});

		$('.search_groupleader_users').on('keyup', function(){
			if($(this).val().length >= 3 || $(this).val() == ''){
				$("#search_text_box").val($(this).val());
				getSiteGroupUsers('NO_URL','site_group_leaders','group_leader');
			}
		});

		$('#site_group_users').on('click','.pagination li a', function(e){
			e.preventDefault();
			getSiteGroupUsers($(this).attr('href'),'site_group_users','student');			
		});

		$('.search_group_users').on('keyup', function(){
			if($(this).val().length >= 3 || $(this).val() == ''){
				$("#search_text_box").val($(this).val());
				getSiteGroupUsers('NO_URL','site_group_users','student');
			}
		});
		

		
	}		

	return {
		init:init,
		getMakeEnrollUserInGroup:getMakeEnrollUserInGroup
	}	

}();