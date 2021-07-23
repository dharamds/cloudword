var AddQuizMetadata = function () {
  var webroot,recordID;

  var init = function (dataVariable) {
    webroot = dataVariable.ajax_call_root;
    recordID = dataVariable.recordID  

    $(".click_to_next").on('click', function(){
        $.ajax({
            url      :   webroot+"assessments/save_skill_mastery_points",
            method   :   "POST",
            data     :  $('#createquiz').serialize()+'&recordID='+recordID,
            dataType : "json",        
            success: function (result) { 
                CommanJS.getDisplayMessgae(result.code,result.message); 
                if(result.code == 200){
                    $("#quizPanelTab li").removeClass("active");
                    $("#tab-setting").addClass('active');
                    getViewTemplate('setting',recordID);
                }         
            }
        });
        return false;
        /**/
    });

    $(".click_to_back").on('click', function(){
        $("#quizPanelTab li").removeClass("active");
        $("#tab-content").addClass('active');
        getViewTemplate('content',recordID); 
    });

    /*Categories*/
    CommanJS.getCatSection(9,'assessment_categories','assessment_categories_input',recordID,9);
    CommanJS.getCatSection(1,'system_categories','system_categories_input',recordID,9);
    CommanJS.getCatSection(14,'learning_outcomes','learning_outcomes_input',recordID,9);
    CommanJS.getCatSection(15,'learning_standards','learning_standards_input',recordID,9);
   /* CommanJS.getCatSection(16,'competencies','competencies_input',recordID,9);
    CommanJS.getCatSection(17,'skills','skills_input',recordID,9);
    CommanJS.getCatSection(21,'quiz_type','quiz_type_input',recordID,9);
    CommanJS.getCatSection(19,'quiz_type_restrictions','quiz_type_restrictions_input',recordID,9);
    CommanJS.getCatSection(18,'blooms_level','blooms_level_input',recordID,9);*/
    /*end categories*/
    /*Tag*/
    CommanJS.getTagSection(9,'assessment_tag','assessment_tag_input',recordID,9);
    CommanJS.getTagSection(1,'system_tag','system_tag_input',recordID,9);
    /*CommanJS.getTagSection(4,'course_tag','course_tag_input',recordID,9);
    CommanJS.getTagSection(6,'module_tag','module_tag_input',recordID,9);
    CommanJS.getTagSection(5,'lesson_tag','lesson_tag_input',recordID,9);*/
    /*End Tag*/
    
    $("input.touchspin4").TouchSpin({
      verticalbuttons: true,
      step: 1,
      min : 0,
      max : 100
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