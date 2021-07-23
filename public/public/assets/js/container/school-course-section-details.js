var SchoolCourseSectionDetails = function (){
	
	var webroot,siteID,csrf_tel_library;
	
	var getOtherViewTemplate = function(selectorView,recordID,siteID)
	{
		$( "#"+selectorView).load( webroot +"containers/schools/other_load_view/"+selectorView+'/'+recordID+'/'+siteID, function() {});
	}

	var getOpenUploadPopup = function(title,callbackRequest,widthRatio,uploadLocation)
	{	
		CommanJS.getPopupWithExternalView(title,webroot+"containers/schoolsUsers/getUploadUserView/"+siteID,callbackRequest,widthRatio,uploadLocation);
	}


	var getUserSection = function() {
		$.ajax({
 			url      :   webroot+"containers/schoolsUsers/get_user_enroll_user",
 			method   :   "POST",
 			data     :  {siteID:siteID,csrf_tel_library:csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#course_section_detail_user_part").html(result);
 				$('#course_section_detail_user_part [type="search"]').attr('placeholder','Search Here');
 			}	
		});
	}

	var getCountEnrollUsers = function(){
		$.ajax({
 			url      :   webroot+"containers/schoolsUsers/get_count_enroll_users",
 			method   :   "POST",
 			data     : {siteID:siteID,csrf_tel_library:csrf_tel_library}, 
 			dataType : "json", 				
 			success: function (result) {
 				$("#enroll_count").html(result.count);
 			}	
		});
		
		return false;
	}

	var getBackToList = function(){	
		$('body,html').animate({
			scrollTop: 0
		}, 500);
		getViewTemplate('courses',siteID);
	}

		

	var init = function (dataVariable) 
	{	
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		csrf_tel_library = dataVariable.csrf_tel_library;		
		getUserSection();
		getCountEnrollUsers();

		$('.course_next').on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#courses").removeClass("active"); 				
			$("#metadata").addClass("active");
			$('.metadata').addClass('active');
			getViewTemplate('metadata',siteID);
		});
	}		

	return {
		init:init,
		getOpenUploadPopup:getOpenUploadPopup,
		getBackToList:getBackToList
	}	

}();