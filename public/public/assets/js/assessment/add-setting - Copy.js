var AddSetting = function () {
    var webroot,recordID,sectionCount;

    var getSubmitSettingForm = function(){
        
        $.ajax({
            url      :   webroot+"quiz/saveSetting",
            method   :   "POST",
            data     :  $('#createquiz').serialize()+'&recordID='+recordID,
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
        $("#createquiz").validate({
            ignore: [], 
            rules:
            {
                gradebookRecord:{ 
                    required:true 
                }
            },
            messages:
            {
              gradebookRecord:
              {
                required:"Please select gradebook record"
              }
            },
            submitHandler: function (form) {
                getSubmitSettingForm();
                return false;
            },
            errorPlacement: function(error, $elem) {
                if ($elem.is('textarea')) {
                    $elem.insertAfter($elem.next('div'));
                }
                error.insertAfter($elem);
            }
        });

        $('[id^=title_]').each(function(e) {
            $(this).rules('add', {
                titleRequired: true,
                noSpace:true
            });
        });
        $('[id^=editor_]').each(function(e) {
            $(this).rules('add', {
                descriptionRequired: true,
                noSpace:true
            });
        });

        $('[id^=extra_editor_]').each(function(e) {
            $(this).rules('add', {
                required:true,
                descriptionRequired: true,
                noSpace:true
            });
        });     
        
    } 

    var get_load_question_section_part = function(value){
        $("#questions-section-part").load(webroot+"assessments/small_load_view/" + value + "/" + recordID, function () {});
    }
   

  var init = function (dataVariable) {
    webroot = dataVariable.ajax_call_root;  
    recordID = dataVariable.recordID;
    sectionCount = dataVariable.sectionCount;

    jQuery.validator.addMethod("noSpace", function(value, element) { 
      return value == '' || value.trim().length != 0;  
    }, "Space is not allow");

    jQuery.validator.addMethod("titleRequired", function(value, element, param) {
      return value != '';
    },"Please enter title");

    jQuery.validator.addMethod("descriptionRequired", function(value, element, param) {
        CKEDITOR.instances[element.id].updateElement(); // update textarea
        var editorcontent = element.value.replace(/<[^>]*>/gi, ''); // strip tags
        return editorcontent.length != 0; 
    },"Please enter something");

    settingFormValidator();

    var config = { height: 80, toolbar : 'short'};    
    $('.ckeditor_form').each(function(e){
        CKEDITOR.replace(this.id, config);
        CKEDITOR.instances[this.id].updateElement();
    });


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


    //if($(".question_section").val())


    $(".add_new_section").on('click', function(){
        var countAddionalSection = $("#count_section_start .panel-response").length+1;
        $("#additional_section").append('<div class="panel panel-response mb-4" id="remove_'+countAddionalSection+'">'+
                    '<div class="panel-body">'+
                        '<div class="form-group col-xs-12 p-0 pt-3">'+
                            '<label class="control-label col-xs-12 p-0">Section 1 Title *</label>'+
                            '<div class="col-xs-6 p-0">'+
                                '<input type="text" name="section['+countAddionalSection+'][title]" value="" id="title_'+countAddionalSection+'" class="form-control">'+
                            '</div>'+
                            '<div class="col-xs-6 p-0">'+
                                '<div class="pull-right mt--10">'+
                                    '<div class="form-group flex-row flex-col-7 p-0 m-0 ml-auto">'+
                                        '<label class="control-label flex-col-5 p-0 pt-2">Weigh %</label>'+
                                        '<div class="flex-col-5 p-0 m-0">'+
                                           '<input type="text" name="section['+countAddionalSection+'][weight]" value="0" class="form-control touchspin4">'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-xs-12 p-0 pb-4">'+
                            '<textarea required="true" name="section['+countAddionalSection+'][description]" id="extra_editor_'+countAddionalSection+'" class="form-control"></textarea>'+
                        '</div>'+
                        '<div class="col-xs-12 p-0 text-right">'+
                            '<button type="button" class="btn btn-danger btn-md remove_section" data-remove="remove_'+countAddionalSection+'">'+
                                '<i class="flaticon-waste-bin"></i> Remove'+
                            '</button>'+
                        '</div>'+
                    '</div>'+
                '</div>').ready(function(){
                        $("input.touchspin4").TouchSpin({
                            verticalbuttons: true,
                            step: 1,
                            min : 0,
                            max : 100
                        });
                        
                        /*$('textarea.ckeditor').each(function () {
                            var $textarea = $(this).attr('id');
                            CKEDITOR.instances[$textarea].updateElement();            
                        });*/
                        var mcqConfig = { height: 80, toolbar : 'short'};           
                        CKEDITOR.replace('extra_editor_'+countAddionalSection, mcqConfig);
                        CKEDITOR.instances['extra_editor_'+countAddionalSection].updateElement();  

                        $('[id^=title_]').each(function(e) {
                            $(this).rules('add', {
                                titleRequired: true,
                                noSpace:true
                            });
                        });

                        $('[id^=extra_editor_]').each(function(e) {
                            $(this).rules('add', {
                                descriptionRequired: true,
                                noSpace:true
                            });
                        });
                });
    });

    
    $("#additional_section").on('click','.remove_section', function(){
        var removeID = $(this).attr('data-remove');   
        $.confirm({
            title: 'Confirm!',
            content: "Are you sure to remove?",
            buttons: {
                confirm: function () {
                    $("#"+removeID).remove();
                },
                cancel: function () {
                }
            }
        });
    });
    
    
  }   

  return {
    init:init
  }

}();