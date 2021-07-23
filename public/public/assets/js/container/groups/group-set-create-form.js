var GroupSetCreateForm = function (){
	var webroot,siteID,csrf_tel_library;

	var getSubmitGroupSetForm = function(){
		$.ajax({
 			url      :   webroot+"containers/SchoolsGroups/saveGroupSet",
 			method   :   "POST",
 			data     :  $('#setupofgroup').serialize(),
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);	
 				if(result.code == 200){
 					//$("#setOfGrpupsModal").modal('hide');
 					$("[data-dismiss=modal]").trigger({ type: "click" });
 					$('body').removeClass('modal-open'); 
 					getViewTemplate('groups',siteID); 
 				}	
 			}
 		});
	}

	var createGroupFormValidator = function() {
		$("#setupofgroup").validate({
			ignore: [],			
			submitHandler: function (form) { 
				getSubmitGroupSetForm();
				return false;
			}
		});
	}


	var getAutocompleteStudentEmail = function() {
		$( ".student_email_search").autocomplete({
			source: function (request, response) {
				$.ajax({
					type: "POST",
					url: webroot+"containers/SchoolsUsers/get_site_active_users",
					data: {"keyword":request,'siteID':siteID,'csrf_tel_library':csrf_tel_library},
					dataType: 'json',
					success: response
				});
			},
			search  : function(){$(this).addClass('loading');},
			minLength: 1,
			select: function( event, ui ) {
				var nameID = $(this).attr('data-name');
				$("#"+nameID).val(ui.item.name);

				var dataID = $(this).attr('data-record-id');
				$("#"+dataID).val(ui.item.id);

				$(this).val(ui.item.label); // display the selected text
		        return false;
			},
			change: function(event, ui ){


			},
			response: function( event, ui ) {			
				if(ui.content != null){
					if(ui.content.length == 1){
						$(this).val(ui.content[0].label);
						var nameID = $(this).attr('data-name');
						$("#"+nameID).val(ui.content[0].name);
						console.log($(this).index(this));
						//$(this).css("border-color","");
						//$(".submit_save_group_btn").attr('disabled', false);
					}
				}else{
					//$(this).css("border-color","red");
				//	$(".submit_save_group_btn").attr('disabled', true)
					//CommanJS.getDisplayMessgae("400","This email is not exist with this site");
				}
				$(this).removeClass('loading');
			}
		}); 
	}

	var getAutocompleteStudentName = function() {
		$( ".student_name_search").autocomplete({
			source: function (request, response) {
				$.ajax({
					type: "POST",
					url: webroot+"containers/SchoolsUsers/get_site_active_users_by_name",
					data: {"keyword":request,'siteID':siteID,'csrf_tel_library':csrf_tel_library},
					dataType: 'json',
					success: response
				});
			},
			search  : function(){$(this).addClass('loading');},
			minLength: 1,
			select: function( event, ui ) {
				var emailID = $(this).attr('data-email');
				$("#"+emailID).val(ui.item.email);

				var dataID = $(this).attr('data-record-id');
				$("#"+dataID).val(ui.item.id);

				$(this).val(ui.item.label); // display the selected text
		        return false;
			},
			change: function(event, ui ){


			},
			response: function( event, ui ) {			
				$(this).removeClass('loading');
			}
		}); 
	}

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID = dataVariable.siteID;
		csrf_tel_library = dataVariable.csrf_tel_library;

		getAutocompleteStudentEmail();
		getAutocompleteStudentName();

		jQuery.validator.addMethod("noSpace", function(value, element) {
			return value == '' || value.trim().length != 0;  
		}, "Space is not allow");

		jQuery.validator.addMethod("emailExt", function(value, element, param) {
			return value == '' ||  value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
		},"Please enter valide email address");

		$(".save_group_set").on('click', function(){
			$("#setupofgroup").submit();
			return false;
		});

		createGroupFormValidator();

		$(".remove_row").on('click', function(){
			var ele = this;
			 $.confirm({
                title: 'Confirm!',
                content: 'Are you sure to remove?',
                buttons: {
                    confirm: function () {
                        $(ele).parent().parent().parent().remove();
                    },
                    cancel: function () {
                    }
                }
            });
			
		});
		$(".student_email_search").on('keyup', function(){
			if($("#"+this.id).val() == '')
			{ 
				var stdID = $(this).attr('data-record-id');
				$("#"+stdID).val('');
			}
		});
		
		$(".student_name_search").on('keyup', function(){
			if($("#"+this.id).val() == '')
			{ 
				var stdID = $(this).attr('data-record-id');
				$("#"+stdID).val('');
			}
		});
	}		

	return {
		init:init
	}	

}();