var SchoolGroupsUserEnrollListing = function (){
	var webroot, table, siteID,courseID,sectionID,groupID,csrf_tel_library;
	
	var sectionUsersDataTables = function(){

		 table = $('#memListTable').DataTable({
			// Processing indicator
			"processing": true,
			// DataTables server-side processing mode
			"serverSide": true,
			// Initial no order.
			"iDisplayLength": 10,
			"bPaginate": true,
			"order"   : [1,2],
			// Load data from an Ajax source
			"ajax": {
				"url": webroot+"containers/schoolsUsers/getgroupuserslist/",
				"type": "POST",
				"data":function(data) {
					data.siteID = siteID;
					data.courseID = courseID;
					data.sectionID = sectionID;
					data.groupID = groupID;
					data.csrf_tel_library = csrf_tel_library;
				},
			},
			"columnDefs": [{ 
				"targets": 0,
				"orderable": false,
				"data": null,
				"render": function(data, type, full, meta){              
					if(type === 'display'){
						data = '<input type="checkbox" class="users_select" name="users_select" value="' + full['user_id'] + '" class="mr-2">';
					}
					return data;
				}

			}],
			"columns": [
				{ "data": "user_id", "autoWidth": true },
				{ "data": "user_name", "autoWidth": true  },
				{ "data": "email" , "autoWidth": true },
				{ "data": "role" , "autoWidth": true }
			]
		});

	}


	var unenrollUsers = function(recordIDS) {

		$.ajax({
 			url      :   webroot+"containers/schoolsUsers/get_unenroll_users_from_group",
 			method   :   "POST",
 			data:{'recordIDS':recordIDS,'groupID':groupID,'csrf_tel_library':csrf_tel_library},
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);
 				if(result.code == 200){
 					table.search('').draw();
 					SchoolGroupsCreate.getViewEnrollUsersSection(groupID,siteID,courseID,sectionID);
 				}
 			}	
		});
		
		return false;
	}

	/*var getCountEnrollUsers = function(){
		$.ajax({
 			url      :   webroot+"containers/schoolsusers/get_count_enroll_users",
 			method   :   "POST",
 			data     : {siteID:siteID}, 
 			dataType : "json", 				
 			success: function (result) {  				
 				$("#enroll_count").html(result.count);
 			}	
		});
		
		return false;
	}*/


	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		courseID = dataVariable.courseID;
		sectionID = dataVariable.sectionID;
		groupID = dataVariable.groupID;
		csrf_tel_library = dataVariable.csrf_tel_library

		sectionUsersDataTables();

		$("#bulkunenroll").on('click', function(){
			var recordIDS = [];
            $.each($("input[name='users_select']:checked"), function(){ 
            	recordIDS.push($(this).val());
            });
            if(recordIDS.length == 0)
            {
            	CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            $.confirm({
            	title: 'Confirm!',
            	content: "Are you sure to enroll selected users?",
            	buttons: {
            		confirm: function () {
            			 unenrollUsers(recordIDS);
            		},
            		cancel: function () {
            		}
            	}
            });	
           
		});

		$("#sellectAll").click(function () {
			$(".users_select").prop('checked', $(this).prop('checked'));
		});

		$("#memListTable_wrapper").on('change','.users_select',function(){
			if (!$(this).prop("checked")){
				$("#sellectAll").prop("checked",false);
			}
		});
		
	}		

	return {
		init:init
	}	

}();