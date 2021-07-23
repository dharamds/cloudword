var SchoolGroups = function (){
	var webroot,siteID,groupNotify,csrf_tel_library;
	
	var getOtherViewTemplate = function(selectorView,recordID,siteID){
      //console.log(selectorView+'=='+recordID+'=='+siteID);
		$( "#"+selectorView).load( webroot +"containers/schools/other_load_view/"+selectorView+'/'+recordID+"/"+siteID, function() {});
	}

    var submitNotifyForm = function() {
        $.ajax({
            url      :   webroot+"containers/notifications/send_notification",
            method   :   "POST",
            data     :  $('#group_notification').serialize() + "&groupIds=" + groupNotify + "&siteID=" + siteID +"&csrf_tel_library="+csrf_tel_library,
            dataType : "json",           
            beforeSend: function() {
                $("#modal-loading").show();
            },   
            success: function (result) { 
                $("#modal-loading").hide();
                CommanJS.getDisplayMessgae(result.code,result.message)
                $("#group_notification_modal").modal('hide');  
                table.search('').draw();
                $("#select_all_group").prop("checked",false);           
            }
        });
    }
    var groupNotificationFormValidator = function() {

        jQuery.validator.addMethod("noSpace", function(value, element) {
            return value == '' || value.trim().length != 0;  
        }, "Space is not allow");

        jQuery.validator.addMethod("emailExt", function(value, element, param) {
            return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
        },"Please enter valide email address");

        $("#group_notification").validate({
            ignore: [],
            rules: {
                subject: {
                    required : true,
                    noSpace  : true
                }/*,
                from_email: {
                    required : true,
                    noSpace  : true,
                    emailExt : true
                }*/,
                msg:{
                     required: function(textarea) {
                          CKEDITOR.instances[textarea.id].updateElement(); // update textarea
                          var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
                          return editorcontent.length === 0;
                      }
                 }
            },
            messages: {
                subject: {
                    required: "Please enter subject"
                }/*,
                from_email: {
                    required: "Please enter email"               
                }*/,
                msg : {
                    required:"Please enter some content"
                }
            },   
            submitHandler: function (form) { 
                submitNotifyForm();
                return false;
            },
            errorPlacement: function(error, $elem) {
                if ($elem.is('textarea')) {
                    $elem.insertAfter($elem.next('div'));
                }
                error.insertAfter($elem);
            }
        });
    }

	var serverside_group_table = function () {
         table = $('#group_list_table').DataTable({
            // Processing indicator
            "processing": true,
            // DataTables server-side processing mode
            "serverSide": true,
            // Initial no order.
            "iDisplayLength": 10,
            "autoWidth": false,
            "bPaginate": true,
            "scrollX": true,
            "order": [],
            // Load data from an Ajax source
            "ajax": {
                "url": webroot + "containers/SchoolsGroups/getLists",
                "type": "POST",
                "data": function (data) {
                	data.institution_id = siteID;
                    data.selected_course = $('#selected_course option:selected').val();
                    data.section_id = $('#section_id option:selected').val();
                    data.group_leader = $('#group_leader option:selected').val();
                    data.csrf_tel_library = csrf_tel_library;
                },
            },
            //Set column definition initialisation properties
            "columnDefs": [
                {
                    "targets": 0,
                    "orderable": false,
                    "data": null,
                     render: function (data, type, row, meta) {
                          if(type === 'display'){
                                data = '<label class="checkbox-tel"><input type="checkbox" class="group_select" name="group_select" value="' + row['id'] + '" class="mr-2"></label>';
                            }
                            return data;
                        },
                    "autoWidth": true
                }, 
                {
                    "targets": [1],
                    "data": null,
                    "orderable": false,
                    "render": function (data, type, full, meta) {

                        if (type === 'display') {
                            data = "<button type='button' class='btn btn-primary btn-sm' title='Click to Edit' onclick=\"SchoolGroups.getOtherViewTemplate('groups','"+full['id']+"','"+siteID+"')\"><i class='ti ti-pencil'></i></button>";
                            data += '<button type="button" data-toggle="tooltip" data-placement="top" title="Click to Remove" type="button" data-id="'+full['id']+'" data-status="'+((full['delete_status'] == 0) ? "1" : "0")+'" data-actiontext="delete" data-message="Are you sure to remove group?" class="btn btn-danger btn-sm get_make_group_action" ><i class="ti ti-trash"></i></button>';
                        }
                        return data;

                    }

                },
                {
                    "targets": [2],
                    "data": null,
                    "orderable": true,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            var actionTxt = 'status';
                            //console.log(full);
                            if (full['status'] == '1') {
                                data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="Are you sure to make group inactive?" class="btn btn-success btn-round btn-status btn-sm get_make_group_action" >Active</button>';
                            } else {
                                data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="Are you sure to make group active?" class="btn btn-danger btn-round btn-status btn-sm get_make_group_action">Inactive</button>';
                            }
                        }
                        return data;

                    }

                }
            ],
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data": "id"
                },
                {
                    "data": "id"
                },                   
                {
                    "data": "group_name",
                    "autoWidth": true
                },
                {
                    "data": "course_name",
                    "autoWidth": true
                },
                {
                    "data": "section_name",
                    "autoWidth": true
                },
                {
                    "data": "display_avaibility",
                    "autoWidth": true
                },
                {
                    "data": "total_users",
                    "autoWidth": true
                },
                {
                    "data": "group_leaders",
                    "autoWidth": true
                }
            ]
        });
      $('#groups .dataTables_filter input').attr('placeholder','Search...');
        //DOM Manipulation to move datatable elements integrate to panel
      $('#groups .panel-ctrls').append($('#groups .dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
      $('#groups .panel-ctrls').append("<i class='separator'></i>");
      $('#groups .panel-ctrls').append($('#groups .dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
      $('#groups .panel-footer').append($("#groups .dataTable+.row"));
      $('#groups .dataTables_paginate>ul.pagination').addClass("pull-right m-n");
    }

    var getMakeActionOnRequest = function(id,status,actionType,wholeEle){
        $.ajax({
            url      :   webroot+"containers/SchoolsGroups/get_make_action_request",
            method   :   "POST",
            data:{id:id,status:status,actionType:actionType,csrf_tel_library:csrf_tel_library},
            dataType : "json",
            beforeSend: function(){
                $(wholeEle).css("opacity", "0.5");
            },              
            success: function (result) { 
                CommanJS.getDisplayMessgae(result.code,result.message);
                if(result.code == 200){
                    table.search('').draw();
                }
            }   
        });
    }


    var getMakeActionConfirm = function(){
        $("#group_list_table").on('click','.get_make_group_action', function(){
            $this = this;
            var rowTr = $($this).parent('td').parent('tr');
            $.confirm({
                title: 'Confirm!',
                content: $($this).attr('data-message'),
                buttons: {
                    confirm: function () {
                    	getMakeActionOnRequest($($this).attr('data-id'),$($this).attr('data-status'),$($this).attr('data-actiontext'),rowTr);
                    },
                    cancel: function () {
                    }
                }
            });
        });
        return false;
    }


    var getRemoveSiteConfirmation = function(ids){
        $.confirm({
            title: 'Confirm!',
            content: "Are you sure to remove selected group?",
            buttons: {
                confirm: function () {
                    getRemoveSite(ids);
                },
                cancel: function () {
                }
            }
        });
    }

    var getRemoveSite = function (ids){
        $.ajax({
            url: webroot+"containers/SchoolsGroups/get_remove_site",
            method: "POST",
            data:{'selected_record':ids,'csrf_tel_library':csrf_tel_library},
            dataType:'json',
            beforeSend: function(){
               // $(wholeEle).css("opacity", "0.5");
            },
            success: function(result){
                $("#select_all_group").prop("checked",false);
                $(".group_select").prop("checked",false);
                CommanJS.getDisplayMessgae(result.code,result.message);
                if(result.code == 200){
                    table.search('').draw();
                }
            }
        });
    }

    var getSectionByCourse = function(siteID,element){
       // console.log(element);
        $.ajax({
            type: "POST",
            url: webroot+'containers/schoolsCourse/get_course_sections_by_id',
            dataType : 'json',
            data: {"course_id": $(element).val(),'siteID':siteID,'csrf_tel_library':csrf_tel_library},
            beforeSend: function() {
                $('#section_id').css('opacity','0.5');
            },
            success: function (response) {
                $('#section_id').css('opacity','1');
                var options = '<option value="">--Section--</option>';
                if(response.code == 200){
                    $.each(response.data, function (key, value) {
                        options = options + "<option value=" + value.id + ">" + value.name + "</option>";
                    });                     
                }
                $("#section_id").html(options);
            }
        });
    }

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
        csrf_tel_library = dataVariable.csrf_tel_library;

		serverside_group_table();
		getMakeActionConfirm();
        groupNotificationFormValidator();
		

		$("#select_all_group").click(function () {
            $(".group_select").prop('checked', $(this).prop('checked'));
        });

        $("#group_list_table_wrapper").on('change','.group_select',function(){
            if (!$(this).prop("checked")){
                $("#select_all_group").prop("checked",false);
            }
           // console.log("Sunil");
        });

        $(".close_modal").on('click', function(){
            table.search('').draw();
            $("#select_all_group").prop("checked",false); 
            $("#group_notification_modal").modal('hide');
        });
        
        $("#notify_to_users").on('click', function(){
            groupNotify = [];
            $.each($("input[name='group_select']:checked"), function(){ 
                groupNotify.push($(this).val());
            });
            if(groupNotify.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            $("#msg").val('');
            $('#group_notification')[0].reset();
            CKEDITOR.instances.msg.setData('');
            $("#group_notification_modal").modal({backdrop: 'static',keyboard: false});
        });

        $("#remove_multiple").on('click', function(){
            var group = [];
            $.each($("input[name='group_select']:checked"), function(){ 
                group.push($(this).val());
            });
            if(group.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            getRemoveSiteConfirmation(group);           
        });
        $('#groups').on('change', '#selected_course', function () {
        	getSectionByCourse(siteID,this);
            table.search('').draw();
		});

		$('#section_id').on('change', function () {
			$.ajax({
				type: "POST",
				url: webroot+'containers/SchoolsGroups/get_groups_leaders_by_id',
				dataType : 'json',
				data: {"course_id": $("#selected_course").val(),"section_id": $("#section_id").val(),'siteID':siteID,'csrf_tel_library':csrf_tel_library},
				beforeSend: function() {
		        	$('#group_leader').css('opacity','0.5');
				},
				success: function (response) {
					$('#group_leader').css('opacity','1');
					var options = '<option value="">--Group Leader--</option>';
					if(response.code == 200){
						$.each(response.data, function (key, value) {
							options = options + "<option value=" + value.id + ">" + value.user_name + "</option>";
						});						
					}
					$("#group_leader").html(options);
                    table.search('').draw();
				}
			});

		});
		$('#group_leader').on('change', function () {
            table.search('').draw();
        });	
		//$(document).on('change','.group_filter', function(){
		 	//console.log('Sunil')
		 	//table.search('').draw();
           // getSectionByCourse(siteID);

       // });

        $(".reset_group_filter").on('click', function(){
            $("#selected_course").val('');
            $("#section_id").val('');
            $("#group_leader").val('');
            
            $("#section_id").empty(); 
            $("#section_id").append('<option value="">--Section--</option>');

            $("#group_leader").empty(); 
            $("#group_leader").append('<option value="">--Group Leader--</option>');

            table.search('').draw();            
        });

        $(".back_to_user").on('click', function(){
			$("#tabslist li").removeClass("active");
            $("#users").addClass("active");
            $("#groups").removeClass("active");
            $('.users').addClass('active');
            getViewTemplate('users',siteID);   
		});

        $(".cancel_request").on('click', function(){
            CommanJS.getDisplayMessgae(200,"Institution successfully created"); 
            window.location.href = webroot+"institutions/add";
        });

        $(".save_change_finish").on('click', function(){
            CommanJS.getDisplayMessgae(200,"Institution successfully created"); 
        });

        var config = { height: 120, allowedContent :true, toolbar: 'short' };
        $('.editor').each(function(e) {
            CKEDITOR.replace(this.id, config);
        });
		
	}		

	return {
		init:init,
		getOtherViewTemplate:getOtherViewTemplate
	}	

}();