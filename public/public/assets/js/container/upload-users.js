var uploadUsers = function (){
	var webroot,siteID,callBackRequest,csrf_tel_library;
	

	var getUserSection = function() {
		$.ajax({
 			url      : webroot+"containers/schoolsUsers/get_user_enroll_user",
 			method   : "POST",
 			data     : {siteID:siteID,csrf_tel_library:csrf_tel_library}, 
 			dataType : "html", 				
 			success: function (result) { 
 				$("#course_section_detail_user_part").html(result);
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

	var callBackRequestFunction = function(element){
		switch(element){
			case 'getUserSection':
			getUserSection();
			break;

			case 'getCountEnrollUsers':
			getCountEnrollUsers()
			break;

			case 'containerUsersDataTables':
			table.search('').draw();
			break;
		}
	}
	var uploadUsers = function(form) 
	{	

		var formData = new FormData(form);
		 $.ajax({
 			url      :   webroot+"containers/schoolsUsers/uploadUsers",
 			method   :   "POST",
 			data:formData,
 			cache          :   false,
 			contentType    :   false,
 			processData    :   false,
 			dataType : "json", 	
 			beforeSend : function(){
 				$("#ajax_file_uploader").addClass('disabled-btn-area');
 				$("#ajax_file_loader").show();
 				$("#ajax_upload_message").hide();
 			},			
 			success: function (result) {
 				$("#ajax_file_uploader").removeClass('disabled-btn-area');
 				$("#ajax_upload_message").show();
 				$("#ajax_file_loader").hide();
 				$("#bulkupload").val("");
 				if(result.code == 200){
 					var callBackRequestArray = callBackRequest.split("%7C");
					callBackRequestArray.forEach(function(element) {
						callBackRequestFunction(element);
					});	
 					$("#ajax_upload_message .alert").removeClass('alert-danger').addClass('alert-success');
 				}else{
 					$("#ajax_upload_message .alert").removeClass('alert-success').addClass('alert-danger');
 				}
 				$("#ajax_upload_message .alert span").html(result.message);
 			}
 		});
	}



	var bulkUploadUsers = function() 
	{
		$("#userbulkupload").validate({
			ignore: [],
			rules: {
				user_type : {
					required : true,
				},
				bulkupload: {
					required : true,
					extension: "csv|xlsx|xls"
				}
			},
			messages: {
				user_type : {
					required : "Please select user type"
				},
				bulkupload: {
					required : "Please select files",
					extension: "Please select valide file"				          
				}
			},   
			submitHandler: function (form) { 
				uploadUsers(form);
				return false;
			}
		});
	}

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		callBackRequest = dataVariable.callBackRequest;
		csrf_tel_library = dataVariable.csrf_tel_library;
		bulkUploadUsers();	
			
	}		

	return {
		init:init
	}	

}();