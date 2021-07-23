var Manage_Schools = function () {
    var webroot,table,get_csrf_hash;
    //var system_cat_selected = [];
 

    var serverside_datatable = function () {
         table = $('#memListTable').DataTable({
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
            "drawCallback": function(settings) {
                $('.load_course_number_data').each(function(key, item) {
                    $.getJSON(webroot + "containers/Schools/get_number_current_courses/" + $(this).attr('id'), function(data) {
                        if (data) $("#" + data.typeid).html(data.value);
                    });
                });

                $('.load_section_number_data').each(function(key, item) {
                    $.getJSON(webroot + "containers/Schools/get_number_current_section/" + $(this).attr('id'), function(data) {
                        if (data) $("#" + data.typeid).html(data.value);
                    });
                });

                $('.load_student_number_data').each(function(key, item) {
                    $.getJSON(webroot + "containers/Schools/get_number_current_student/" + $(this).attr('id'), function(data) {
                        if (data) $("#" + data.typeid).html(data.value);
                    });
                });

                $('.load_enrollment_number_data').each(function(key, item) {
                    $.getJSON(webroot + "containers/Schools/get_number_current_enrollment/" + $(this).attr('id'), function(data) {
                        if (data) $("#" + data.typeid).html(data.value);
                    });
                });

            },    
            // Load data from an Ajax source
            "ajax": {
                "url": webroot + "containers/schools/getLists",
                "type": "POST",
                "data": function (data) {
                    //data.system_category = $('#selected_system_category option:selected').val();
                    data.system_category = CommanJS.getMetaCallBack('system_category');
                    data.institution_category = CommanJS.getMetaCallBack('institute_category');
                    data.system_tag = CommanJS.getMetaCallBack('system_tag');
                    data.institution_tag = CommanJS.getMetaCallBack('institute_tag');
                    data.csrf_tel_library = get_csrf_hash;
                },
            },
            "deferLoading": 57,
            //Set column definition initialisation properties
            "columnDefs": [
                        {
                        "targets": 0,
                        "data": null,
                        "orderable": false,
                         render: function (data, type, row, meta) {
                          if(type === 'display'){
                                data = '<label class="checkbox-tel"><input type="checkbox" class="site_select" name="site_select" value="' + row['id'] + '" class="mr-2"></label>';
                            }
                            return data;
                        }
                    },
             
                     {
                    "targets": 1,
                    "data": null,
                    "orderable": false,
                    "render": function (data, type, full, meta) {

                        if (type === 'display') {
                            data = "<a class='btn btn-primary btn-sm' href='" + webroot + "institutions/edit/" + btoa(full['id']) + "' title='Click to Edit'><i class='ti ti-pencil'></i></a>";
                            data += '<button type="button" data-toggle="tooltip" data-placement="top" title="Click to Remove" type="button" data-id="'+full['id']+'" data-status="'+((full['status_delete'] == 0) ? "1" : "0")+'" data-actiontext="delete" data-message="Are you sure to remove site ?" class="btn btn-danger btn-sm get_make_site_action" ><i class="ti ti-trash"></i></button>';
                        }
                        return data;

                    }

                },
                    {
                    "targets": 2,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            var actionTxt = 'status';
                            //console.log(full);
                            if (full['status'] == '1') {
                                data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="For site to be inactive?" class="btn btn-success btn-status btn-sm get_make_site_action" >Active</button>';
                            } else {
                                data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="For site to be active?" class="btn btn-danger btn-status btn-sm get_make_site_action">Inactive</button>';
                            }
                        }
                        return data;

                    }

                },
                 {
                    "targets": [5],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            data = '<span id="' + full['id'] + '_current_course"  class="load_course_number_data"> Loading...</span>';
                        }
                        return data;
                    }
                },
                {
                    "targets": [6],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            data = '<span id="' + full['id'] + '_current_section"  class="load_section_number_data"> Loading...</span>';
                        }
                        return data;
                    }
                },
                 {
                    "targets": [7],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            data = '<span id="' + full['id'] + '_current_student"  class="load_student_number_data"> Loading...</span>';
                        }
                        return data;
                    }
                },
                 {
                    "targets": [8],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            data = '<span id="' + full['id'] + '_current_enrollment"  class="load_enrollment_number_data"> Loading...</span>';
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
                    "data": "name"
                },
                {
                    "data": "primary_contact_name"
                },
                {
                    "data": "total_course"
                },
                {
                    "data": "total_section"
                },
                {
                    "data": "total_user"
                },
                {
                    "data": "total_enroll_user"
                }
            ]
        });
    }

    var getMakeActionOnRequest = function(id,status,actionType,wholeEle){
        $.ajax({
            url      :   webroot+"containers/schools/get_make_action_request",
            method   :   "POST",
            data:{id:id,status:status,actionType:actionType,csrf_tel_library:get_csrf_hash},
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
        $("#memListTable").on('click','.get_make_site_action', function(){
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
            content: "Are you sure to remove selected site?",
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
            url: webroot+"containers/schools/get_remove_site",
            method: "POST",
            data:{'selected_record':ids,csrf_tel_library:get_csrf_hash},
            dataType:'json',
            beforeSend: function(){
               // $(wholeEle).css("opacity", "0.5");
            },
            success: function(result){
                $("#select_all").prop("checked",false);
                $(".site_select").prop("checked",false);
                CommanJS.getDisplayMessgae(result.code,result.message);
                if(result.code == 200){
                    table.search('').draw();
                }
            }
        });
    }

    var getExportSiteList = function(ids){
        var form = document.createElement("form");
        var element1 = document.createElement("input"); 
        var element2 = document.createElement("input"); 
        
        form.method = "POST";
        form.action = webroot+"containers/schools/get_export_site";
        element1.value=ids;
        element1.name="selected_record";

        element2.value=get_csrf_hash;
        element2.name="csrf_tel_library";

        form.appendChild(element2);
        form.appendChild(element1);
        document.body.appendChild(form);
        form.submit();     
        return false;
    }

    var getDeactiveSiteConfirmation = function(ids){
        $.confirm({
            title: 'Confirm!',
            content: "Are you sure to deactive selected site?",
            buttons: {
                confirm: function () {
                    getDeactiveSite(ids);
                },
                cancel: function () {
                }
            }
        });
    }

    var getDeactiveSite = function (ids){
        $.ajax({
            url: webroot+"containers/schools/get_change_status",
            method: "POST",
            data:{'selected_record':ids,csrf_tel_library:get_csrf_hash},
            dataType:'json',
            beforeSend: function(){
               // $(wholeEle).css("opacity", "0.5");
            },
            success: function(result){
                $("#select_all").prop("checked",false);
                $(".site_select").prop("checked",false);
                CommanJS.getDisplayMessgae(result.code,result.message);
                if(result.code == 200){
                    table.search('').draw();
                }
            }
        });
    }

    
    

    var init = function (dataVariable) {
        webroot = dataVariable.ajax_call_root;
        get_csrf_hash = dataVariable.get_csrf_hash;
        serverside_datatable();
       /* $('.selected_condition').on('change', function () {
            table.search('').draw();
        });*/

        getMakeActionConfirm();

        $('.filter-container').on('change','.meta_data_filter', function(){
            table.search('').draw();
        });
        
        /* $(document).on('change','.metadata_selector', function(){
             table.search('').draw();
        });*/

        CommanJS.get_metadata_options("System category", 1, 1, "reflect_system_category");
        CommanJS.get_metadata_options("Institute category", 1, 3, "reflect_institute_category");

        CommanJS.get_metadata_options("System tag", 2, 1, "reflect_system_tag");
        CommanJS.get_metadata_options("Institute tag", 2, 3, "reflect_institute_tag");

        $(".reset_filter").on('click', function(){
            $(".meta_data_filter").prop("checked",false);
            $(".selectFilterMode span").text('');
            table.search('').draw();            
        });

        $("#reflect_system_category .metadata_selector").SumoSelect({
          placeholder: 'Search category name...',
          csvDispCount: 2,
          selectAll: true,
          search: true,
          okCancelInMulti: false,
          //locale: ['Save', '', 'Select All']
        }); 

        $("#memListTable_wrapper").on('click', '#select_all', function () {
            $(".site_select").prop('checked', $(this).prop('checked'));
        });

        $("#memListTable_wrapper").on('click','.site_select',function(){
            console.log($("#memListTable_wrapper .site_select").length);
            if (!$(this).is(":checked")){
                $("#select_all").prop("checked",false);
            }
            if($("#memListTable_wrapper .site_select").length === $("#memListTable_wrapper .site_select:checked").length){
                $("#memListTable_wrapper #select_all").prop("checked",true);
            }else{
                $("#memListTable_wrapper #select_all").prop("checked",false);
            }
            
        });

        $("#remove_multiple").on('click', function(){
            var site = [];
            $.each($("input[name='site_select']:checked"), function(){ 
                site.push($(this).val());
            });
            if(site.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            getRemoveSiteConfirmation(site);           
        });

        $("#deactive_multiple").on('click', function(){
            var site = [];
            $.each($("input[name='site_select']:checked"), function(){ 
                site.push($(this).val());
            });
            if(site.length == 0){
                /*prop("disabled", false); 
                 $('.inputDisabled').prop("disabled", false);*/
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            getDeactiveSiteConfirmation(site);           
        });


        $("#bulk_export").on('click', function(){
            var siteIDS = [];
            $.each($("input[name='site_select']:checked"), function(){ 
                siteIDS.push($(this).val());
            });
            if(siteIDS.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            getExportSiteList(siteIDS);           
        });

        $("#bulk_export_all").on('click', function(){
            var siteIDS = [];
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure?",
                buttons: {
                    confirm: function () {
                       getExportSiteList(siteIDS); 
                    },
                    cancel: function () {
                    }
                }
            });
        });
        


    }

    return {
        init: init

    }

}();