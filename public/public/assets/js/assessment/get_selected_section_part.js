var AddQuestionSectionPart = function () {
    var webroot,quizID,sectionID,selectedTable;

    var get_open_questions_popup = function(){
        $.ajax({
            url      :   webroot+"assessments/get_active_questions",
            method   :   "POST",
            data     : {'quizID':quizID,'sectionID':$('#selected_que_section_id option:selected').val()},
            dataType : "html",        
            success: function (result) { 
                $("#popup-questions").html(result);
                $("#sectionQuestionModal").modal({backdrop: 'static',keyboard: false});
            }
        });
        return false;
    }

    var reload_selected_tables = function (){
         selectedTable.search('').draw();
    }

    var selected_questions_datatable = function () {
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
            // Load data from an Ajax source
            "ajax": {
                "url": webroot + "assessments/questions/getSelectedLists/",
                "type": "POST",
                "data": function (data) {
                    data.sectionID = sectionID;
                    data.system_category = CommanJS.getMetaCallBack('system_category');
                    data.system_tag = CommanJS.getMetaCallBack('system_tag');
                    data.assessment_category = CommanJS.getMetaCallBack('assessment_category');
                    data.assessment_tag = CommanJS.getMetaCallBack('assessment_tag');
                    data.outcomes_category = CommanJS.getMetaCallBack('learning_outcomes');
                    data.tel_mastery_standards = CommanJS.getMetaCallBack('tel_mastery_standards');
                    data.blooms_level_category = CommanJS.getMetaCallBack('blooms_level');

                    /*data.system_category = $('#selected_toggle_filters #selected_system_category option:selected').val();
                    data.system_tag = $('#selected_toggle_filters #selected_system_tag option:selected').val();
                    data.assessment_category = $('#selected_toggle_filters #selected_assessment_category option:selected').val();
                    data.assessment_tag = $('#selected_toggle_filters #selected_assessment_tag option:selected').val();
                    data.course_tag = $('#selected_toggle_filters #selected_course_tag option:selected').val();
                    data.module_tag = $('#selected_toggle_filters #selected_module_tag option:selected').val();
                    data.lesson_tag = $('#selected_toggle_filters #selected_lesson_tag option:selected').val();
                    data.outcomes_category = $('#selected_toggle_filters #selected_learning_outcomes option:selected').val();
                    data.standard_category = $('#selected_toggle_filters #selected_learning_standards option:selected').val();
                    data.compentencies_category = $('#selected_toggle_filters #selected_learning_competencies option:selected').val();
                    data.skills_category = $('#selected_toggle_filters #selected_learning_skills option:selected').val();*/
                },
            },
            //Set column definition initialisation properties
            "columns": [
                {
                    "data":"sr_no",
                    "orderable": false,
                    "data": null,
                     render: function (data, type, row, meta) {
                          if(type === 'display'){
                                data = '<label class="checkbox-tel"><input type="checkbox" class="selected_question_select" name="selected_question_select" value="' + row['id'] + '" class="mr-2"></label>';
                            }
                            return data;
                        },
                    "autoWidth": true
                },                
                {
                    "data": "title",
                    "autoWidth": true
                },
                {
                    "data": "long_title",
                    "autoWidth": true
                },
                {
                    "data": "assesment_category",
                    "autoWidth": true
                },
                {
                    "data": "assessment_tags",
                    "autoWidth": true
                },
                {
                    "data": "system_category",
                    "autoWidth": true
                },
                {
                    "data": "system_tags",
                    "autoWidth": true
                },
                /*{
                    "data": "courses_tags",
                    "autoWidth": true
                },
                {
                    "data": "modules_tags",
                    "autoWidth": true
                },
                {
                    "data": "lessons_tags",
                    "autoWidth": true
                },*/
                {
                    "data": "outcomes_category",
                    "autoWidth": true
                },
                {
                    "data": "standards_category",
                    "autoWidth": true
                },
                {
                    "data": "blooms_level_category",
                    "autoWidth": true
                }/*,
                {
                    "data": "skills_category",
                    "autoWidth": true
                }*/
            ]
        });
    }

    var getRemoveQuestionFromSections = function(questions, quizID, sectionsID){

        $.ajax({
            url: webroot+"assessments/questions/selectedRemove",
            method: "POST",
            data:{'selected_record':questions,'quizID':quizID,'sectionID':sectionID},
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

    var init = function (dataVariable) {
        webroot = dataVariable.ajax_call_root;
        quizID = dataVariable.quizID;
        sectionID = dataVariable.sectionID;  
        //console.log(dataVariable);
        selected_questions_datatable();

        CommanJS.get_metadata_options("System category", 1, 1, "selected_system_cat");
        CommanJS.get_metadata_options("System tag", 2, 1, "selected_system_tag");
        CommanJS.get_metadata_options("Assessment category", 1, 9, "selected_assessment_cat");
        CommanJS.get_metadata_options("Assessment tag", 2, 9, "selected_assessment_tag");
       /* CommanJS.get_metadata_options("Course tag", 2, 4, "selected_course_tag");
        CommanJS.get_metadata_options("Module tag", 2, 6, "selected_module_tag");
        CommanJS.get_metadata_options("Lesson tag", 2, 5, "selected_lesson_tag");*/
        CommanJS.get_metadata_options("Learning outcomes", 1, 14, "selected_outcomes_cat");
        CommanJS.get_metadata_options("TEL mastery standards", 1, 15, "selected_standard_cat");
        CommanJS.get_metadata_options("Blooms level", 1, 18, "selected_bloom_level_cat");

       /* CommanJS.get_metadata_options("Learning competencies", 1, 16, "selected_competencies_cat");
        CommanJS.get_metadata_options("Learning skills", 1, 17, "selected_skills_cat");
*/
        $('#selected_toggle_filters').on('change','.meta_data_filter', function(){
            selectedTable.search('').draw();
        });
        /*$('#selected_toggle_filters').on('change', '.metadata_selector', function() {
           selectedTable.search('').draw();
        });*/

        $("#selected_toggle_filters").on('click','.selected_reset_filter', function(){
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

        $(".remove-questions-from-section").on('click', function(){
            var questions = [];
            $.each($("input[name='selected_question_select']:checked"), function(){ 
                questions.push($(this).val());
            });
            if(questions.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure to remove selected questions?",
                buttons: {
                    confirm: function () {
                        getRemoveQuestionFromSections(questions,quizID,sectionID);    
                    },
                    cancel: function () {
                    }
                }
            });
            
        });


        $('#selected_quiz_table_wrapper .dataTables_filter input').attr('placeholder','Search...');
        $('#addQuestionSectionOPtions .panel-ctrls').append($('#selected_quiz_table_wrapper .dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
        $('#addQuestionSectionOPtions .panel-ctrls').append("<i class='separator'></i>");
        $('#addQuestionSectionOPtions .panel-ctrls').append($('#selected_quiz_table_wrapper .dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
        $('#addQuestionSectionOPtions .panel-footer').append($("#selected_quiz_table_wrapper .dataTable+.row"));
        $('#selected_quiz_table_wrapper .dataTables_paginate>ul.pagination').addClass("pull-right m-n");
        $("#selected_quiz_table_wrapper .dataTables_scroll").addClass('mb-4');
        
    /*$(".scroll-top").on("click", function(){
    $('body,html').animate({
    scrollTop: 0
    }, 500);
    }); */


    }   

    return {
        init:init,
        get_open_questions_popup:get_open_questions_popup,
        reload_selected_tables:reload_selected_tables
    }

}();