var AddQuizQuestion = function () {
    var webroot,recordID,question_section;

    var get_selected_section_part_question_yes = function(recordID,sectionID){
        $.ajax({
            url      :   webroot+"assessments/get_selected_section_part_yes",
            method   :   "POST",
            data     : {'quizID':recordID,'sectionID':sectionID},
            dataType : "html",        
            success: function (result) { 
                $("#selected_section_part").html(result);
            }
        });
        return false; 
    }

    var get_selected_section_part_question_no = function(recordID){
        $.ajax({
            url      :   webroot+"assessments/get_selected_section_part_no",
            method   :   "POST",
            data     : {'quizID':recordID},
            dataType : "html",        
            success: function (result) { 
                $("#selected_section_part").html(result);
            }
        });
        return false; 
    }

    var init = function (dataVariable) {
        webroot = dataVariable.ajax_call_root;
        recordID = dataVariable.recordID;
        question_section= dataVariable.question_section;
        if(question_section == 1){
            get_selected_section_part_question_yes(recordID,$('#selected_que_section_id option:selected').val());
            $("#selected_que_section_id").on('change', function(){
                get_selected_section_part_question_yes(recordID,this.value);
            });
        }else{
            get_selected_section_part_question_no(recordID);
        }
       
        $(".click_to_finish").on('click', function(){
            window.location.href = webroot+"assessments";
        });

        $(".click_to_back").on('click', function(){
            $("#quizPanelTab li").removeClass("active");
            $("#tab-metadata").addClass('active');
            getViewTemplate('metadata',recordID); 
        });

    /*$(".scroll-top").on("click", function(){
    $('body,html').animate({
    scrollTop: 0
    }, 500);
    }); */


    }   

    return {
        init:init
    }

}();