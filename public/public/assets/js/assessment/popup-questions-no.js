var PopupQuestions = function () {
    var webroot,quizID,table;

    /*var get_open_questions_popup = function(sectionID){
        $.ajax({
            url      :   webroot+"quiz/get_active_questions",
            method   :   "POST",
            data     : {'quizID':recordID,'sectionID':sectionID},
            dataType : "html",        
            success: function (result) { 
                $("#popup-questions").html(result);
                $("#sectionQuestionModal").modal({backdrop: 'static',keyboard: false});
            }
        });
        return false;
    }*/

    var popup_datatabels = function () {
         table = $('#all-active-question-popup-list').DataTable({
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
                "url": webroot + "assessments/questions/getLists/",
                "type": "POST",
                "data": function (data) {
                    data.quizID = quizID;

                    data.system_category = CommanJS.getMetaCallBack('system_category');
                    data.system_tag = CommanJS.getMetaCallBack('system_tag');
                    data.assessment_category = CommanJS.getMetaCallBack('assessment_category');
                    data.assessment_tag = CommanJS.getMetaCallBack('assessment_tag');
                    data.outcomes_category = CommanJS.getMetaCallBack('learning_outcomes');
                    data.standard_category = CommanJS.getMetaCallBack('learning_standards');
                    data.blooms_level_category = CommanJS.getMetaCallBack('blooms_level');

                    /*data.system_category = $('#popup_search_filters #selected_system_category option:selected').val();
                    data.system_tag = $('#popup_search_filters #selected_system_tag option:selected').val();
                    data.assessment_category = $('#popup_search_filters #selected_assessment_category option:selected').val();
                    data.assessment_tag = $('#popup_search_filters #selected_assessment_tag option:selected').val();
                    data.course_tag = $('#popup_search_filters #selected_course_tag option:selected').val();
                    data.module_tag = $('#popup_search_filters #selected_module_tag option:selected').val();
                    data.lesson_tag = $('#popup_search_filters #selected_lesson_tag option:selected').val();
                    data.outcomes_category = $('#popup_search_filters #selected_learning_outcomes option:selected').val();
                    data.standard_category = $('#popup_search_filters #selected_learning_standards option:selected').val();
                    data.compentencies_category = $('#popup_search_filters #selected_learning_competencies option:selected').val();
                    data.skills_category = $('#popup_search_filters #selected_learning_skills option:selected').val();*/
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
                                data = '<label class="checkbox-tel"><input type="checkbox" class="question_select" name="question_select" value="' + row['id'] + '" class="mr-2"></label>';
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
                }
                /*{
                    "data": "competencies_category",
                    "autoWidth": true
                },
                {
                    "data": "skills_category",
                    "autoWidth": true
                }*/
            ]
        });
    }

    var getAddQuestionToSections = function(questions,quizID){
        $.ajax({
            url: webroot+"assessments/questions/selectedAssigned",
            method: "POST",
            data:{'selected_record':questions,'quizID':quizID,'sectionID':0},
            dataType:'json',
            beforeSend: function(){
               // $(wholeEle).css("opacity", "0.5");
            },
            success: function(result){
                CommanJS.getDisplayMessgae(result.code,result.message);
                $("#popup_select_all").prop("checked",false);
                $(".question_select").prop("checked",false);
                if(result.code == 200){
                    table.search('').draw();
                    AddQuestionSectionPart.reload_selected_tables();
                }
            }
        });
        return false;
    }




    var init = function (dataVariable) {

        webroot = dataVariable.ajax_call_root;
        quizID = dataVariable.quizID;
       
        CommanJS.get_metadata_options("System category", 1, 1, "pop_system_cat");
        CommanJS.get_metadata_options("System tag", 2, 1, "pop_system_tag");
        CommanJS.get_metadata_options("Assessment category", 1, 9, "pop_assessment_cat");
        CommanJS.get_metadata_options("Assessment tag", 2, 9, "pop_assessment_tag");
        CommanJS.get_metadata_options("Learning outcomes", 1, 14, "pop_outcomes_cat");
        CommanJS.get_metadata_options("Learning standards", 1, 15, "pop_standard_cat");
        CommanJS.get_metadata_options("Blooms level", 1, 18, "pop_bloom_level_cat");
       /* CommanJS.get_metadata_options("System category", 1, 1, "pop_system_cat");
        CommanJS.get_metadata_options("System tag", 2, 1, "pop_system_tag");
        CommanJS.get_metadata_options("Assessment category", 1, 9, "pop_assessment_cat");
        CommanJS.get_metadata_options("Assessment tag", 2, 9, "pop_assessment_tag");
        CommanJS.get_metadata_options("Course tag", 2, 4, "pop_course_tag");
        CommanJS.get_metadata_options("Module tag", 2, 6, "pop_module_tag");
        CommanJS.get_metadata_options("Lesson tag", 2, 5, "pop_lesson_tag");
        CommanJS.get_metadata_options("Learning outcomes", 1, 14, "pop_outcomes_cat");
        CommanJS.get_metadata_options("Learning standards", 1, 15, "pop_standard_cat");
        CommanJS.get_metadata_options("Learning competencies", 1, 16, "pop_competencies_cat");
        CommanJS.get_metadata_options("Learning skills", 1, 17, "pop_skills_cat");*/

        popup_datatabels();

         $('#popup-questions').on('change', '.meta_data_filter', function() {
           table.search('').draw();
        });

        /*$('#popup_search_filters').on('change', '.metadata_selector', function() {
           table.search('').draw();
        });*/

        $("#popup_search_filters").on('click','.popup_reset_filter', function(){
            $(".meta_data_filter").prop("checked",false);
            $(".selectFilterMode span").text('');
            table.search('').draw();           
        });


        $('#all-active-question-popup-list_wrapper .dataTables_filter input').attr('placeholder','Search...');
        $('#sectionOptions .panel-ctrls').append($('#all-active-question-popup-list_wrapper .dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
        $('#sectionOptions .panel-ctrls').append("<i class='separator'></i>");
        $('#sectionOptions .panel-ctrls').append($('#all-active-question-popup-list_wrapper .dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
        $('#sectionOptions .panel-footer').append($("#all-active-question-popup-list_wrapper .dataTable+.row"));
        $('#all-active-question-popup-list_wrapper .dataTables_paginate>ul.pagination').addClass("pull-right m-n");
        $("#all-active-question-popup-list_wrapper .dataTables_scroll").addClass('mb-4');

        $("#popup_select_all").click(function () {
            $(".question_select").prop('checked', $(this).prop('checked'));
        });

        $("#all-active-question-popup-list_wrapper").on('change','.question_select',function(){
            if (!$(this).prop("checked")){
                $("#popup_select_all").prop("checked",false);
            }
        });

        $(".add-questions-to-section").on('click', function(){
            var questions = [];
            $.each($("input[name='question_select']:checked"), function(){ 
                questions.push($(this).val());
            });
            if(questions.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            
            $.ajax({
                url: webroot+"assessments/get_count_questions_and_limit",
                method: "POST",
                data:{'quizID':quizID},
                dataType:'json',
                beforeSend: function(){
                   // $(wholeEle).css("opacity", "0.5");
                },
                success: function(result){
                   // CommanJS.getDisplayMessgae(result.code,result.message);
                    if(parseInt(result.total_assessment_questions)+parseInt(questions.length) < result.question_per_quiz){
                        CommanJS.getDisplayMessgae('400','Please select at least '+result.question_per_quiz+' questions');
                        return false;
                    }else{
                        getAddQuestionToSections(questions,quizID);   
                    }
                }
            });
            return false;

            /*if(parseInt(addedTotalQuestion)+parseInt(questions.length) < minimumSelectQuestion){
                CommanJS.getDisplayMessgae('400','Please select at least '+minimumSelectQuestion+' questions');
                return false;
            }*/

            //getAddQuestionToSections(questions,quizID);    
        });


        /*$(".scroll-top").on("click", function(){
        $('body,html').animate({
        scrollTop: 0
        }, 500);
        }); */


    }   

    return {
        init:init/*,
        get_open_questions_popup:get_open_questions_popup*/
    }

}();