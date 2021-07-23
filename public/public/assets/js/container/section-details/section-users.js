var SectionDetailsUsers = function (){
	var webroot, table, siteID,csrf_tel_library;
	
	var getSectionUsersForInstructor = function(pageURL) {
		var url;
		var keyword = $('.search_course_section_std_for_instructor').val();
		if(pageURL){
			url = pageURL;
		}else{
			url = webroot+"containers/schoolsUsers/getUsers";
		}
		$.ajax({
 			url      :   url,
 			method   :   "POST",
 			data:{'keyword':keyword,'siteID':siteID,'utype':'all_for_instructor','csrf_tel_library':csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#section_users_select_for_instructor").html(result);
 				//multiselectCourse();
 			}	
		});
	}

	var getSectionUsers = function(pageURL) {
		var url;
		var keyword = $('.search_course_section_std').val();
		if(pageURL){
			url = pageURL;
		}else{
			url = webroot+"containers/schoolsUsers/getUsers";
		}
		$.ajax({
 			url      :   url,
 			method   :   "POST",
 			data:{'keyword':keyword,'siteID':siteID,'csrf_tel_library':csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#section_users_select").html(result);
 				//multiselectCourse();
 			}	
		});
	}

	var getSectionEnrollUsers = function(pageURL) {
		var url;
		var keyword = $('.search_enroll_user').val();
		if(pageURL){
			url = pageURL;
		}else{
			url = webroot+"containers/schoolsUsers/getUsers";
		}
		$.ajax({
 			url      :   url,
 			method   :   "POST",
 			data:{'keyword':keyword,'utype':'enroll','siteID':siteID,'csrf_tel_library':csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#section_enroll_users_select").html(result); 				
 			}	
		});
	}

	var getSectionEnrollInstructor = function(pageURL) {
		var url;
		var keyword = $('.search_instructor_user').val();
		if(pageURL){
			url = pageURL;
		}else{
			url = webroot+"containers/schoolsUsers/getUsers";
		}
		$.ajax({
 			url      :   url,
 			method   :   "POST",
 			data:{'keyword':keyword,'utype':'instructor','siteID':siteID,'csrf_tel_library':csrf_tel_library},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#section_instructor_select").html(result);
 				
 			}	
		});
	}

	var getSwitchList = function(type) {
		console.log(type);
		switch(type){
			case 1:
				getSectionUsers();
				getSectionEnrollUsers();
			break;

			case 2:
				getSectionUsers();
				getSectionEnrollUsers();
			break;

			case 3:
				getSectionUsersForInstructor();
				getSectionEnrollInstructor();
			break;

			case 4:
				getSectionUsersForInstructor();
				getSectionEnrollInstructor();
			break;
		}
	}

	var multiselect = function(userID,type) {
		$.ajax({
 			url      :   webroot+"containers/schoolsUsers/get_manage_users",
 			method   :   "POST",
 			data:{'userID':userID,'type':type,'siteID':siteID,'csrf_tel_library':csrf_tel_library},
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);
 				if(result.code == 200){
 					getCountEnrollUsers();
 					getSwitchList(type); //type 1->enroll, 2->unroll,3->make instructor,4->remove instructor
 					table.search('').draw();
 				}
 			}	
		});
		return false;
	}

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
				"url": webroot+"containers/schoolsUsers/getlist/",
				"type": "POST",
				"data":function(data) {
					data.siteID = siteID;
					data.csrf_tel_library = csrf_tel_library;
				},
			},
			"columnDefs": [{ 
				"targets": 0,
				"orderable": false,
				"data": null,
				"render": function(data, type, full, meta){              
					if(type === 'display'){
						data = '<label class="checkbox-tel"><input type="checkbox" class="users_select" name="users_select" value="' + full['user_id'] + '" class="mr-2"></label>';
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
		})

	}


	var unenrollUsers = function(users) {

		$.ajax({
 			url      :   webroot+"containers/schoolsUsers/get_unenroll_users",
 			method   :   "POST",
 			data:{'users':users,'csrf_tel_library':csrf_tel_library},
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);
 				if(result.code == 200){
 					table.search('').draw();
 					getSectionEnrollUsers();
					getSectionEnrollInstructor();
					getCountEnrollUsers();
 				}
 			}	
		});
		
		return false;
	}

	var getCountEnrollUsers = function(){
		$.ajax({
 			url      :   webroot+"containers/schoolsUsers/get_count_enroll_users",
 			method   :   "POST",
 			data     : {siteID:siteID,'csrf_tel_library':csrf_tel_library}, 
 			dataType : "json", 				
 			success: function (result) {  				
 				$("#enroll_count").html(result.count);
 			}	
		});
		
		return false;
	}


	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		csrf_tel_library = dataVariable.csrf_tel_library;

		getSectionUsers();
		getSectionEnrollUsers();
		getSectionEnrollInstructor();
		sectionUsersDataTables();

		getSectionUsersForInstructor();



		$("#bulkunenroll").on('click', function(){
			var users = [];
            $.each($("input[name='users_select']:checked"), function(){ 
            	users.push($(this).val());
            });
            if(users.length == 0)
            {
            	CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            $.confirm({
        	title: 'Confirm!',
        	content: "Are you sure to enroll selected users?",
        	buttons: {
        		confirm: function () {
        			 unenrollUsers(users);
        		},
        		cancel: function () {
        		}
        	}
        });	
		});
		
		$('#section_users_select_for_instructor').on('click','.pagination li a', function(e){
			e.preventDefault();
			getSectionUsersForInstructor($(this).attr('href'));
			
		});

		$('.search_course_section_std_for_instructor').on('keyup', function(){
			if($(this).val().length >= 3 || $(this).val() == ''){
				getSectionUsersForInstructor();
			}
		});


		$('#section_users_select').on('click','.pagination li a', function(e){
			e.preventDefault();
			getSectionUsers($(this).attr('href'));
			
		});

		

		$('#section_enroll_users_select').on('click','.pagination li a', function(e){
			e.preventDefault();
			getSectionEnrollUsers($(this).attr('href'));
			
		});

		$('#section_instructor_select').on('click','.pagination li a', function(e){
			e.preventDefault();
			getSectionEnrollInstructor($(this).attr('href'));
			
		});

		$('.search_course_section_std').on('keyup', function(){
			if($(this).val().length >= 3 || $(this).val() == ''){
				getSectionUsers();
			}
		});

		$('.search_enroll_user').on('keyup', function(){
			if($(this).val().length >= 3 || $(this).val() == ''){
				getSectionEnrollUsers();
			}
		});

		$('.search_instructor_user').on('keyup', function(){
			if($(this).val().length >= 3 || $(this).val() == ''){
				getSectionEnrollInstructor();
			}
		});

		 $('input').on('ifChecked', function(event){
          		$(".users_select").prop('checked', $(this).prop('checked'));
        });
		 $('input').on('ifUnchecked', function(event){
          $(".users_select").prop("checked", false);
        });
		// $("#sellectAll").click(function () {
		// 	$(".users_select").prop('checked', $(this).prop('checked'));
		// });

		$("#memListTable_wrapper").on('change','.users_select',function(){
			if (!$(this).prop("checked")){
				$("#sellectAll").prop("checked",false);
			}
		});

		
	}		

	return {
		init:init,
		multiselect:multiselect
	}	

}();