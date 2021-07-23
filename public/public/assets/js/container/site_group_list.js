var SiteGroupsList = function (){
	var webroot,siteID,csrf_tel_library;
	
	var getOtherViewTemplate = function(selectorView,recordID,siteID){
      //console.log(selectorView+'=='+recordID+'=='+siteID);
		$( "#"+selectorView).load( webroot +"containers/schools/other_load_view/"+selectorView+'/'+recordID+"/"+siteID, function() {});
	}

	var serverside_group_table = function () {
         table = $('#group_list_table').DataTable({
            // Processing indicator
            "processing": true,
            // DataTables server-side processing mode
            "serverSide": true,
            // Initial no order.
            "iDisplayLength": 10,
            "bPaginate": true,
            "order": [1,2,4,5],
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
            "columnDefs": [{
                    "targets": [7],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            var actionTxt = 'status';
                            //console.log(full);
                            if (full['status'] == '1') {
                                //data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="Are you sure to make group inactive?" class="btn btn-midnightblue-alt btn-sm get_make_group_action" >Active</button>';
                                data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="Are you sure to make group inactive?" class="btn btn-midnightblue-alt btn-sm" >Active</button>';
                            } else {
                                /*data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="Are you sure to make group active?" class="btn btn-midnightblue-alt btn-sm get_make_group_action">Inactive</button>';*/
                                data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="Are you sure to make group active?" class="btn btn-midnightblue-alt btn-sm">Inactive</button>';
                            }
                        }
                        return data;

                    }

                },
                {
                    "targets": [8],
                    "data": null,
                    "width": "4%",
                    "render": function (data, type, full, meta) {

                        if (type === 'display') {
                            /*data = "<button type='button' class='btn btn-midnightblue-alt btn-sm' title='Click to Edit' onclick=\"SchoolGroups.getOtherViewTemplate('groups','"+full['id']+"','"+siteID+"')\"><i class='ti ti-pencil'></i></button>";
                            data += '<button type="button" data-toggle="tooltip" data-placement="top" title="Click to Remove" type="button" data-id="'+full['id']+'" data-status="'+((full['delete_status'] == 0) ? "1" : "0")+'" data-actiontext="delete" data-message="Are you sure to remove group?" class="btn btn-midnightblue-alt btn-sm get_make_group_action" ><i class="ti ti-close"></i></button>';*/
                            data = "<button type='button' class='btn btn-midnightblue-alt btn-sm' title='Click to Edit' ><i class='ti ti-pencil'></i></button>";
                            data += '<button type="button" data-toggle="tooltip" data-placement="top" title="Click to Remove" type="button" data-id="'+full['id']+'" data-status="'+((full['delete_status'] == 0) ? "1" : "0")+'" data-actiontext="delete" data-message="Are you sure to remove group?" class="btn btn-midnightblue-alt btn-sm" ><i class="ti ti-close"></i></button>';
                        }
                        return data;

                    }

                }
            ],
            "columns": [
                {
                   
                    "orderable": false,
                    "data": null,
                     render: function (data, type, row, meta) {
                          if(type === 'display'){
                                data = '<input type="checkbox" class="group_select" name="group_select" value="' + row['id'] + '" class="mr-2">';
                            }
                            return data;
                        },
                    "autoWidth": true
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

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
        csrf_tel_library = dataVariable.csrf_tel_library;
        alert("Asdasdasdasdasd");
        console.log("sssssssssssssssss"+dataVariable);
		
        serverside_group_table();
		getMakeActionConfirm();
		

		$("#select_all_group").click(function () {
            $(".group_select").prop('checked', $(this).prop('checked'));
        });

        $("#group_list_table_wrapper").on('change','.group_select',function(){
            if (!$(this).prop("checked")){
                $("#select_all_group").prop("checked",false);
            }
            console.log("Sunil");
        });

        $("#remove_multiple").on('click', function(){
            var site = [];
            $.each($("input[name='group_select']:checked"), function(){ 
                site.push($(this).val());
            });
            if(site.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            getRemoveSiteConfirmation(site);           
        });

        $('#selected_course').on('change', function () {
			$.ajax({
				type: "POST",
				url: webroot+'containers/schoolsCourse/get_course_sections_by_id',
				dataType : 'json',
				data: {"course_id": $(this).val(),'siteID':siteID,'csrf_tel_library':csrf_tel_library},
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
				}
			});

		});
			
		$(document).on('change','.group_filter', function(){
		 	//console.log('Sunil')
		 	table.search('').draw();
        });

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

        $(".back_to_course").on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#courses").addClass("active");
			$("#metadata").removeClass("active");
			$('.courses').addClass('active');
			getViewTemplate('metadata',containerID);	
		});

		/*$(".next_to_user").on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#users").addClass("active");
			$("#metadata").removeClass("active");
			$('.users').addClass('active');
			getViewTemplate('users',containerID);	
		});*/
		
	}		

	return {
		init:init,
		getOtherViewTemplate:getOtherViewTemplate
	}	

}();