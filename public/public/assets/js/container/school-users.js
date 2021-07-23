var SchoolUsers = function (){
	var webroot,siteID,table,csrf_tel_library;
	
	var submitUserForm = function() {
		$.ajax({
 			url      :   webroot+"containers/schoolsUsers/createUser",
 			method   :   "POST",
 			data     :  $('#setupusers').serialize()+ "&siteID=" + siteID + "$csrf_tel_library="+csrf_tel_library,
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);
 				table.search('').draw();
 				$("#setupusers")[0].reset();	
 			}
 		});
	}

	var getOtherViewTemplate = function(selectorView,recordID)
	{
		$( "#"+selectorView).load( webroot +"containers/schools/other_load_view/"+selectorView+'/'+recordID, function() {});
	}

	var getOpenUploadPopup = function(title,callBackRequest,widthRatio,uploadLocation)
	{
		
		CommanJS.getPopupWithExternalView(title,webroot+"containers/schoolsUsers/getUploadUserView/"+siteID,callBackRequest,widthRatio,uploadLocation);
	}


	var createFormValidator = function() {
		$.validator.addMethod(
			"cust_mobile",
			function(value, element) {
				var regEx = /^[+]?\d+$/;
				var re = new RegExp(regEx);
				return this.optional(element) || re.test(value);
			},
			"Please enter a valid number."
	);
		$("#setupusers").validate({
			ignore: [],
			rules: {
				first_name: {
					required : true,
					noSpace  : true
				},
				middle_name: {
					noSpace  : true
				},
				last_name: {
					required : true,
					noSpace  : true
				},
				email: {
					required : true,
					noSpace  : true,
					emailExt : true
				},
				phone : {
					required: false,
					//number: true,
					maxlength: 25,                            
					cust_mobile:true,
				},
				"roles[]": {
					required : true
				}
				
			},
			messages: {
				first_name: {
					required: "Please enter first name"
				},
				middle_name: {
					required: "Please enter middle name"               
				},
				last_name : {
					required:"Please enter last name"
				},
				email : {
					required:"Please enter email"
				},
				phone : {
					required:"Please enter phone"
				},
				"roles[]" : {
					required:"Please select role"
				}
			},   
			submitHandler: function (form) { 
				submitUserForm();
				return false;
			}
		});
	}

	/*var getUserByEmail = function(email){
		$.ajax({
 			url      :   webroot+"containers/schoolsusers/get_user_by_email",
 			method   :   "POST",
 			data     : {"email":email},
 			dataType : "json", 				
 			success: function (result) { 
 				
 			}
 		});
	}*/
	
	var showHideUserSection = function ( hideElmId, showElmId ){
		$(showElmId).show();
		$(hideElmId).hide();
		table.search('').draw();

		$('body,html').animate({
			scrollTop: 0
		}, 500);     
    }

	var containerUsersDataTables = function(){
		/*$("#userListingSection").show();
		$("#userAddFormSection").show();*/
		 table = table = $('#containerListTable').DataTable({
		 	'destroy': true,
			// Processing indicator
			"processing": true,
			// DataTables server-side processing mode
			"serverSide": true,
			// Initial no order.
			"iDisplayLength": 10,
			"bPaginate": true,
			"order"   : [],
			"drawCallback": function(settings) {
                $('.load_course_data').each(function(key, item) {
                    $.getJSON(webroot+ "containers/schoolsUsers/get_enroll_courses/" + $(this).attr('id'), function(data) {
                           if(data) $("#"+data.typeid).html(data.value);
                    });
                });
                $('.load_section_data').each(function(key, item) {
                    $.getJSON(webroot+ "containers/schoolsUsers/get_enroll_section/" + $(this).attr('id'), function(data) {
                           if(data) $("#"+data.typeid).html(data.value);
                    });
				});
			},
			// Load data from an Ajax source
			"ajax": {
				"url": webroot+"containers/schoolsUsers/get_container_user_list/",
				"type": "POST",
				"data":function(data) {
					data.siteID = siteID;
					data.roles_selected = $('#selected_roles option:selected').val();
                    data.course_selected = $('#selected_course option:selected').val();
                    data.section_selected = $('#selected_section option:selected').val();
                    data.csrf_tel_library = csrf_tel_library;
				},
			},
			"columnDefs": [{
				"targets": 0,
				"data": null,
				"orderable": false,
				"render": function(data, type, full, meta) {
					var data = '';
					if (type === 'display') {
						data =
							'<label class="checkbox-tel"><input type="checkbox" name="users_select" class="users_select" value="' + full['id'] + '" data-userdata="' + full['user_name'] + '~' + full['email'] + '~' + full['id'] + '"></lable>';
					}
					return data;
				}
			},
			
                {
                    "targets": [3],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            data = '<span id="' + full['id'] + '_course"  class="load_course_data"> Loading...</span>';
                        }
                        return data;
                    }
                }/*,
                {
                    "targets": [6],
                    "orderable": false,
                    "visible": false,
					"data": null,
					"render": function(data, type, full, meta){
						data = 'NA';              
						if(type === 'display'){
							data = '<a data-toggle="tooltip" target="_blank" title="Login As" href="' + webroot + 'users/login_user/'+
							btoa(full['id']) +
							'" class="btn btn-danger btn-xs"><i class="ti ti-arrows-horizontal"></i></a>';
						}
						return data;
					}
                }*/,
                {
                    "targets": [4],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            data = '<span id="' + full['id'] + '_section"  class="load_section_data"> Loading...</span>';
                        }
                        return data;
                       
                        //return "NA";
                    }
                }],
			"columns": [
				{ "data": "checkbox"},
				{ "data": "user_name", "autoWidth": true},
				{ "data": "email" , "autoWidth": true},
				{ "data": "course_name" , "autoWidth": true},
				{ "data": "course_section_name" , "autoWidth": true},
				{ "data": "date" , "autoWidth": true},
				/*{ "data": "parent" , "autoWidth": true}*/
			]
		});

	}

	var getRemoveUserAction = function(users,siteID){
		$.ajax({
 			url      :   webroot+"containers/schoolsUsers/remove_users",
 			method   :   "POST",
 			data:{'users':users,'siteID':siteID,'csrf_tel_library':csrf_tel_library},
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);
 				if(result.code == 200){
 					table.search('').draw();
 				}
 			}	
		});
		return false;
	}

	var removeUsers = function(users) {
		if(users.length == 0){
			CommanJS.getDisplayMessgae('400',"Please select at least one user");
			return false;	
		}
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure to remove?',
			buttons: {
				confirm: function () {
					getRemoveUserAction(users,siteID);
				},
				cancel: function () {
				}
			}
		});
		
		
	}

	var getCoursesDropdown = function(siteID){
		$.ajax({
			type: "POST",
			url: webroot+"users/get_db_cources",
			data: {
				"container_id": siteID,'csrf_tel_library':csrf_tel_library
			},
			success: function(response) {
				var options = '<option value="0">--Course--</option>';
				$.each(JSON.parse(response), function(key, value) {
					options = options + "<option value=" + value.id + ">" + value
					.name + "</option>";
				});
				$("#selected_course").html(options);
			}
		});
	}

	


	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		csrf_tel_library = dataVariable.csrf_tel_library;		

		jQuery.validator.addMethod("noSpace", function(value, element) { 
			return value == '' || value.trim().length != 0;  
		}, "Space is not allow");

		jQuery.validator.addMethod("emailExt", function(value, element, param) {
			return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
		},"Please enter valide email address");



		createFormValidator();
		containerUsersDataTables();

		$('.selected_condition').on('change', function() {
           table.search('').draw();
        });

		getCoursesDropdown(siteID);


		$('#selected_course').on('change', function() {
            $.ajax({
                type: "POST",
                url: webroot+"users/get_db_sections",
                data: {
                    "container_id": siteID,
                    "course_id": $(this).val(),
                    "csrf_tel_library":csrf_tel_library
                },
                success: function(response) {
                    var options = '<option value="0">--Section--</option>';
                    $.each(JSON.parse(response), function(key, value) {
                        options = options + "<option value=" + value.id + ">" + value
                            .name + "</option>";
                    });
                    $("#selected_section").html(options);
                }
            });
        });


		$("#remove_users").on('click', function(){
			var users = [];
            $.each($("input[name='users_select']:checked"), function(){ 
            	users.push($(this).val());
            });
            removeUsers(users);
		});

		$("#sellectAlluser").click(function () {
			$(".users_select").prop('checked', $(this).prop('checked'));
		});

		$("#containerListTable").on('change','.users_select',function(){
			if (!$(this).prop("checked")){
				$("#sellectAlluser").prop("checked",false);
			}
		});

		$(".back_to_metadata").on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#users").removeClass("active");
			$("#metadata").addClass("active");
			$('.metadata').addClass('active');
			getViewTemplate('metadata',siteID);	
		});
		/*
		swap comment
		$(".next_to_group").on('click', function(){
			$('.panel-ctrls').html('');
			$("#tabslist li").removeClass("active");
			$("#users").removeClass("active");
			$("#groups").addClass("active");
			$('.groups').addClass('active');
			$("#groups").load(webroot+"containers/schools/load_view/groups/" + siteID, function () {});
		});
	*/
		$(".next_to_group").on('click', function(){
			$('.panel-ctrls').html('');
			$("#tabslist li").removeClass("active");
			$("#users").removeClass("active");
			$("#identity").addClass("active");
			$('.identity').addClass('active');
			$("#identity").load(webroot+"containers/schools/load_view/identity/" + siteID, function () {});
		});

		 $(".reset_filter").on('click', function(){
            $("#selected_roles").val(0);
            $("#selected_course").val(0);
            $("#selected_section").val(0);
            table.search('').draw();            
        });

		$('#users .dataTables_filter input').attr('placeholder','Search...');
		//DOM Manipulation to move datatable elements integrate to panel
		$('#users .panel-ctrls').append($('#users .dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
		$('#users .panel-ctrls').append("<i class='separator'></i>");
		$('#users .panel-ctrls').append($('#users .dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
		$('#users .panel-footer').append($("#users .dataTable+.row"));
		$('#users .dataTables_paginate>ul.pagination').addClass("pull-right m-n");

	}		

	return {
		init:init,
		getOpenUploadPopup:getOpenUploadPopup,
		showHideUserSection:showHideUserSection
	}	

}();