var AddSetting = function () {
    var webroot,recordID,section_question;

    var getSubmitSettingForm = function(){
        
        $.ajax({
            url      :   webroot+"assessments/saveSetting",
            method   :   "POST",
            data     :  $('#createassessment').serialize()+'&recordID='+recordID,
            dataType : "json",        
            success: function (result) { 
                CommanJS.getDisplayMessgae(result.code,result.message); 
                if(result.code == 200){
                    $("#quizPanelTab li").removeClass("active");
                    $("#tab-metadata").addClass('active');
                    getViewTemplate('metadata',recordID); 
                }         
            }
        });
    }

    var settingFormValidator = function() {
        $("#createassessment").validate({
            ignore: [], 
            rules:
            {
                gradebook_record:{ 
                    required:true 
                },
                question_section:{ 
                    required:true 
                }
            },
            messages:
            {
              gradebook_record:
              {
                required:"Please select gradebook record"
              },
              question_section:
              {
                required:"Select create question section setting"
              }
            },
            submitHandler: function (form) {
             getSubmitSettingForm();
              //alert("OK")
                return false;
            },
            errorPlacement: function(error, $elem) {
                if ($elem.is('textarea')) {
                    $elem.insertAfter($elem.next('div'));
                }

                error.insertAfter($elem);
                
                if ($elem.attr("type") == "radio") {
                   $(".rg-error").html(error);
                }
            }
        });

    } 

    var get_load_question_section_part = function(value){
        $("#questions-section-part").html('<div class="col-sm-12"><div class="page-loading-box"><span class="page-loader-quart"></span> Loading...</div></div>');
        $("#questions-section-part").load(webroot+"assessments/small_load_view/" + value + "/" + recordID, function () {});
    }
   

  var init = function (dataVariable) {
    webroot = dataVariable.ajax_call_root;  
    recordID = dataVariable.recordID;
    section_question = dataVariable.section_question;
    

    jQuery.validator.addMethod("noSpace", function(value, element) { 
      return value == '' || value.trim().length != 0;  
    }, "Space is not allow");

    jQuery.validator.addMethod("titleRequired", function(value, element, param) {
      return value != '';
    },"Please enter title");

    jQuery.validator.addMethod("minvalue", function(value, element, param) {
      return value != 0 || value > 0;
    },"Please enter value greater than 0");



    jQuery.validator.addMethod("descriptionRequired", function(value, element, param) {
        CKEDITOR.instances[element.id].updateElement(); // update textarea
        var editorcontent = element.value.replace(/<[^>]*>/gi, ''); // strip tags
        return editorcontent.length != 0; 
    },"Please enter something");

    settingFormValidator();

    


    $(".click_to_back").on('click', function(){
        $("#quizPanelTab li").removeClass("active");
        $("#tab-content").addClass('active');
        getViewTemplate('content',recordID);
    });

     $("input.touchspin4").TouchSpin({
        verticalbuttons: true,
        step: 1,
        min : 0,
        max : 100
    });

    $(".question_section").on('click', function(){
        get_load_question_section_part(this.value);
    });
    if(section_question > 0){
       get_load_question_section_part(section_question); 
    }

  }   

  return {
    init:init
  }

}();