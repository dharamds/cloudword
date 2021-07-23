var List = function () {
    var webroot,selectedTable,csrf_tel_library;

    
    var quiz_datatable = function () {
          selectedTable = $('#selected_quiz_table').DataTable({
            // Processing indicator
            "processing": true,
            // DataTables server-side processing mode
            "serverSide": true,
            // Initial no order.
            "iDisplayLength": 10,
            "bPaginate": true,
            "scrollX": true,
            "autoWidth": false,
            "order": [],
            "drawCallback": function(settings) {
                $('.load_meta_data').each(function(key, item) {
                    $.getJSON(webroot + 'questions/get_meta_tags/' + $(this).attr('id'), function(data) {
                           if(data) $("#"+data.typeid).html(data.value);
                    });
				});
				$('.load_meta_category').each(function(key, item) {
                    $.getJSON(webroot + 'questions/get_meta_categories/' + $(this).attr('id'), function(data) {
                           if(data) $("#"+data.typeid).html(data.value);
                    });
				});

                },
            // Load data from an Ajax source
            "ajax": {
                "url": webroot + "assessments/getLists",
                "type": "POST",
                "data": function (data) {

                    data.system_category = CommanJS.getMetaCallBack('system_category');
                    data.system_tag = CommanJS.getMetaCallBack('system_tag');
                    data.assessment_category = CommanJS.getMetaCallBack('assessment_category');
                    data.assessment_tag = CommanJS.getMetaCallBack('assessment_tag');
                    /*data.course_tag = CommanJS.getMetaCallBack('course_tag');
                    data.module_tag = CommanJS.getMetaCallBack('module_tag');
                    data.lesson_tag = CommanJS.getMetaCallBack('lesson_tag');*/
                    data.outcomes_category = CommanJS.getMetaCallBack('learning_outcomes');
                    data.tel_mastery_standards = CommanJS.getMetaCallBack('tel_mastery_standards');
                    data.csrf_tel_library = csrf_tel_library;
                    /*data.compentencies_category = CommanJS.getMetaCallBack('learning_competencies');
                    data.skills_category = CommanJS.getMetaCallBack('learning_skills');
                    data.quiz_type = CommanJS.getMetaCallBack('quiz_type');
                    data.blooms_level = CommanJS.getMetaCallBack('blooms_level');*/

                    /*data.system_category = $('#toggleFilterSection #selected_system_category option:selected').val();
                    data.system_tag = $('#toggleFilterSection #selected_system_tag option:selected').val();
                    data.assessment_category = $('#toggleFilterSection #selected_assessment_category option:selected').val();
                    data.assessment_tag = $('#toggleFilterSection #selected_assessment_tag option:selected').val();
                    data.course_tag = $('#toggleFilterSection #selected_course_tag option:selected').val();
                    data.module_tag = $('#toggleFilterSection #selected_module_tag option:selected').val();
                    data.lesson_tag = $('#toggleFilterSection #selected_lesson_tag option:selected').val();
                    data.outcomes_category = $('#toggleFilterSection #selected_learning_outcomes option:selected').val();
                    data.standard_category = $('#toggleFilterSection #selected_learning_standards option:selected').val();
                    data.compentencies_category = $('#toggleFilterSection #selected_learning_competencies option:selected').val();
                    data.skills_category = $('#toggleFilterSection #selected_learning_skills option:selected').val();
                    data.quiz_type = $('#toggleFilterSection #selected_quiz_type option:selected').val();
                    data.blooms_level = $('#toggleFilterSection #selected_blooms_level option:selected').val();*/
                    $(window).scrollTop(0);
                },
            },
            //Set column definition initialisation properties
            "columnDefs": [{
                    "targets": 0,
                    "data": null,
                    "orderable": false,
                    render: function (data, type, row, meta) {
                          if(type === 'display'){
                                data = '<label class="checkbox-tel"><input type="checkbox" class="selected_question_select" name="selected_question_select" value="' + row['id'] + '" class="mr-2"></label>';
                            }
                            return data;
                        },

                },
                {
                    "targets": 1,
                    "data": null,
                    "orderable": false,
                    "render": function (data, type, full, meta) {

                        if (type === 'display') {
                            data = "<a class='btn btn-primary btn-sm' href='" + webroot + "assessments/edit/" + btoa(full['id']) + "' title='Click to Edit'><i class='ti ti-pencil'></i></a>";
                            data += '<button type="button" data-toggle="tooltip" data-placement="top" title="Click to Remove" type="button" data-id="'+full['id']+'" data-status="'+((full['status_delete'] == 0) ? "1" : "0")+'" data-actiontext="delete" data-message="Are you sure to remove assessment?" class="btn btn-danger btn-sm get_make_quiz_action" ><i class="ti ti-trash"></i></button>';
                        }
                        return data;

                    }

                },
                {
                    "targets": 2,
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                            var actionTxt = 'status';
                            //console.log(full);
                            if (full['status'] == '1') {
                                data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="Are you sure to make assessment inactive?" class="btn btn-success btn-status btn-sm get_make_quiz_action" >Active</button>';
                            } else {
                                data = '<button data-toggle="tooltip" data-placement="top" title="Click to Change Status" type="button" data-id="'+full['id']+'" data-status="'+((full['status'] == 0) ? "1" : "0")+'" data-actiontext="status" data-message="Are you sure to make assessment active?" class="btn btn-danger btn-status btn-sm get_make_quiz_action">Inactive</button>';
                            }
                        }
                        return data;

                    }

                },
                {
                    "targets": [6],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                         data = '<span id="' + full['id'] +'_9_9_cat" data-type="course_category" class="load_meta_category"> Loading...</span>';
						  //data = 1;
						}
                        return data;
                    }
                },
				{
                    "targets": [7],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_9_9" data-type="course_tags" class="load_meta_data"> Loading...</span>';
                        }
                        return data;
                    }
                },
                {
                    "targets": [8],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_9_1_syscat" data-type="system_category" class="load_meta_category"> Loading...</span>';
						 //data = 2;
						}
                        return data;
                    }
                },
                {
                    "targets": [9],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_9_1_systag" data-type="system_tags" class="load_meta_data"> Loading...</span>';
                        }
                        return data;
                    }
                },
                {
                    "targets": [10],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_9_14_outcome" data-type="system_tags" class="load_meta_category"> Loading...</span>';
                        }
                        return data;
                    }
                },
                {
                    "targets": [11],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_9_15_standard" data-type="system_tags" class="load_meta_category"> Loading...</span>';
                        }
                        return data;
                    }
                }
            ],
            "columns": [
                {
                    "data":"id"
                },
                {
                    "data":"id"
                }, 
                {
                    "data":"id"
                },                 
                {
                    "data": "title"
                },
                {
                    "data": "long_title"
                },
                {
                    "data": "course_title"
                },
                {
                    "data": "assesment_category"
                },
                {
                    "data": "assessment_tags"
                },
                {
                    "data": "system_category"
                },
                {
                    "data": "system_tags"
                },
                /*{
                    "data": "courses_tags"
                },
                {
                    "data": "modules_tags"
                },
                {
                    "data": "lessons_tags"
                },*/
                {
                    "data": "outcomes_category"
                },
                /*{
                    "data": "quiz_type_category",
                },
                {
                    "data": "blooms_level_category",
                },*/
                {
                    "data": "standards_category"
                }/*,
                {
                    "data": "competencies_category"
                },
                {
                    "data": "skills_category"
                }*/
            ]
        });
    }

    var getRemoveQuestionFromSections = function(questions, quizID, sectionsID){

        $.ajax({
            url: webroot+"quiz/questions/selectedRemove",
            method: "POST",
            data:{'selected_record':questions,'quizID':quizID,'sectionID':sectionID,'csrf_tel_library':csrf_tel_library},
            dataType:'json',
            beforeSend: function(){
               // $(wholeEle).css("opacity", "0.5");
            },
            success: function(result){
                CommanJS.getDisplayMessgae(result.code,result.message);
                $("#selected_select_all").prop("checked",false);
                $(".selected_question_select").prop("checked",false);
                if(result.code == 200){
                    selectedTable.search('').draw();
                }
            }
        });
        return false;
    }

    var getMakeActionOnRequest = function(id,status,actionType,wholeEle){
        $.ajax({
            url      :   webroot+"assessments/get_make_action_request",
            method   :   "POST",
            data:{id:id,status:status,actionType:actionType,csrf_tel_library:csrf_tel_library},
            dataType : "json",
            beforeSend: function(){
                $(wholeEle).css("opacity", "0.5");
            },              
            success: function (result) { 
                CommanJS.getDisplayMessgae(result.code,result.message);
                if(result.code == 200){
                    selectedTable.search('').draw();
                }
            }   
        });
    }

    var getMakeActionConfirm = function(){
        $("#selected_quiz_table").on('click','.get_make_quiz_action', function(){
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


    var getRemoveQuizConfirmation = function(ids){
        $.confirm({
            title: 'Confirm!',
            content: "Are you sure to remove selected assessment?",
            buttons: {
                confirm: function () {
                    getRemoveQuiz(ids);
                },
                cancel: function () {
                }
            }
        });
    }

    var getRemoveQuiz = function (ids){
        $.ajax({
            url: webroot+"assessments/get_remove_quiz",
            method: "POST",
            data:{'selected_record':ids,'csrf_tel_library':csrf_tel_library},
            dataType:'json',
            beforeSend: function(){
               // $(wholeEle).css("opacity", "0.5");
            },
            success: function(result){
                $("#selected_select_all").prop("checked",false);
                $(".selected_question_select").prop("checked",false);
                CommanJS.getDisplayMessgae(result.code,result.message);
                if(result.code == 200){
                    selectedTable.search('').draw();
                }
            }
        });
    }

    var getExportRecordList = function(ids){
        
        var form = document.createElement("form");
        var element1 = document.createElement("input"); 
        var element2 = document.createElement("input"); 
        form.method = "POST";
        form.action = webroot+"assessments/get_export_quiz";
        element2.value=csrf_tel_library;
        element2.name="csrf_tel_library";
        form.appendChild(element2);

        element1.value=ids;
        element1.name="selected_record";
        form.appendChild(element1);

        document.body.appendChild(form);
        form.submit();
        $("#selected_select_all").prop("checked",false);  
        selectedTable.search('').draw();   
        return false;
    }

    var init = function (dataVariable) {
        webroot = dataVariable.ajax_call_root;
        csrf_tel_library = dataVariable.csrf_tel_library;
        quiz_datatable();
        CommanJS.get_metadata_options("System category", 1, 1, "selected_system_cat");
        CommanJS.get_metadata_options("System tag", 2, 1, "selected_system_tag");
        CommanJS.get_metadata_options("Assessment category", 1, 9, "selected_assessment_cat");
        CommanJS.get_metadata_options("Assessment tag", 2, 9, "selected_assessment_tag");
        CommanJS.get_metadata_options("Course tag", 2, 4, "selected_course_tag");
        CommanJS.get_metadata_options("Module tag", 2, 6, "selected_module_tag");
        CommanJS.get_metadata_options("Lesson tag", 2, 5, "selected_lesson_tag");
        CommanJS.get_metadata_options("Learning outcomes", 1, 14, "selected_outcomes_cat");
        CommanJS.get_metadata_options("Quiz type", 1, 21, "selected_quiz_type");
        CommanJS.get_metadata_options("Blooms level", 1, 18, "selected_blooms_level");

        CommanJS.get_metadata_options("TEL mastery standards", 1, 15, "selected_standard_cat");
        CommanJS.get_metadata_options("Learning competencies", 1, 16, "selected_competencies_cat");
        CommanJS.get_metadata_options("Learning skills", 1, 17, "selected_skills_cat");

         $('.filter-container').on('change','.meta_data_filter', function(){
            selectedTable.search('').draw();
        });
        /*$('#toggleFilterSection').on('change', '.metadata_selector', function() {
           selectedTable.search('').draw();
        });*/

        $("#toggleFilterSection").on('click','.selected_reset_filter', function(){
            /*$(".metadata_selector").val(0);
            selectedTable.search('').draw();   */
             $(".meta_data_filter").prop("checked",false);
            $(".selectFilterMode span").text('');
            selectedTable.search('').draw();        
        });

        $("#selected_select_all").click(function () {
            $(".selected_question_select").prop('checked', $(this).prop('checked'));
        });

        $("#selected_quiz_table_wrapper").on('change','.selected_question_select',function(){
            if (!$(this).prop("checked")){
                $("#selected_select_all").prop("checked",false);
            }
        });

        getMakeActionConfirm();

        $("#remove_multiple").on('click', function(){
            var quiz = [];
            $.each($("input[name='selected_question_select']:checked"), function(){ 
                quiz.push($(this).val());
            });
            if(quiz.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            getRemoveQuizConfirmation(quiz);           
        });

        $("#bulk_export").on('click', function(){
            var recordIDS = [];
            $.each($("input[name='selected_question_select']:checked"), function(){ 
                recordIDS.push($(this).val());
            });
            if(recordIDS.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            getExportRecordList(recordIDS);           
        });

        $("#bulk_export_all").on('click', function(){
            var recordIDS = [];
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure?",
                buttons: {
                    confirm: function () {
                       getExportRecordList(recordIDS); 
                    },
                    cancel: function () {
                    }
                }
            });
        });


        $("[data-panel-toggle]").each(function(){
            var $this =  $(this),
                toggleContainer = $this.attr("data-panel-toggle");
            $this.click(function(){
                $("#"+toggleContainer).slideToggle()
            })
        });
    }   

    return {
        init:init
    }

}();