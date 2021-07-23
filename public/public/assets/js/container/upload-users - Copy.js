var uploadUsers = function (){
	var webroot;
	

	var getUserSection = function() {
		$.ajax({
 			url      :   webroot+"containers/schoolsusers/get_user_enroll_user",
 			method   :   "POST",
 			dataType : "html", 				
 			success: function (result) { 
 				$("#course_section_detail_user_part").html(result);
 			}	
		});
	}

	var getCountEnrollUsers = function(){
		$.ajax({
 			url      :   webroot+"containers/schoolsusers/get_count_enroll_users",
 			method   :   "POST",
 			dataType : "json", 				
 			success: function (result) {  				
 				$("#enroll_count").html(result.count);
 			}	
		});		
		return false;
	}

	var uploadUsers = function(form) 
	{
		
		var formData = new FormData(form);
		 $.ajax({
 			url      :   webroot+"containers/schoolsusers/uploadUsers",
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
 					getUserSection();
 					getCountEnrollUsers();
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
		bulkUploadUsers();	
			
	}		

	return {
		init:init
	}	

}();