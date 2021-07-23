var AddSettingYesQuestionPart = function () {
    var webroot,recordID,sectionCount;

   
    var get_load_question_section_part = function(value){
        $("#questions-section-part").html('<div class="col-sm-12"><div class="page-loading-box"><span class="page-loader-quart"></span> Loading...</div></div>');
        $("#questions-section-part").load(webroot+"assessments/small_load_view/" + value + "/" + recordID, function () {});
    }

   var get_remove_section = function(remove_ID,record_ID){
        $.ajax({
            url      :   webroot+"assessments/removeSection",
            method   :   "POST",
            data     :  {record_ID:record_ID},
            dataType : "json",        
            success: function (result) { 
                CommanJS.getDisplayMessgae(result.code,result.message); 
                if(result.code == 200){
                    get_load_question_section_part(1);
                }         
            }
        });
    }

  var init = function (dataVariable) {
    webroot = dataVariable.ajax_call_root;  
    recordID = dataVariable.recordID;
    sectionCount = dataVariable.sectionCount;

    
    var config = { height: 80, toolbar : 'short'};    
    $('.ckeditor_form').each(function(e){
        CKEDITOR.replace(this.id, config);
        CKEDITOR.instances[this.id].updateElement();
    });

    $('[id^=title_]').each(function(e) {
        $(this).rules('add', {
            titleRequired: true,
            noSpace:true
        });
    });
    $('[id^=student_question_display_]').each(function(e) {
        $(this).rules('add', {
            minvalue: true
        });
    });

    $('[id^=editor_]').each(function(e) {
        $(this).rules('add', {
            descriptionRequired: true,
            noSpace:true
        });
    }); 

    $("input.touchspin4").TouchSpin({
        verticalbuttons: true,
        step: 1,
        min : 0,
        max : 100
    });

    //if($(".question_section").val())


    $(".add_new_section").on('click', function(){
        var countAddionalSection = $("#count_section_start .panel-response").length+1;
        $("#additional_section").append('<div class="panel panel-response mb-4" id="remove_'+countAddionalSection+'">'+
                    '<div class="panel-body">'+
                        '<div class="form-group col-xs-12 p-0 pt-3">'+
                            '<label class="control-label col-xs-12 p-0">Title <span class="field-required">*</span></label>'+
                            '<div class="col-xs-6 p-0">'+
                                '<input type="text" name="section['+countAddionalSection+'][title]" value="" id="title_'+countAddionalSection+'" class="form-control">'+
                            '</div>'+
                            '<div class="col-xs-3 p-0 pt-3">'+
                                '<div class="pull-right mt--10">'+
                                    '<div class="form-group flex-row flex-col-7 p-0 m-0 ml-auto">'+
                                        '<label class="control-label flex-col-5 p-0 pt-2">Weight %</label>'+
                                        '<div class="flex-col-5 p-0 m-0">'+
                                           '<input type="text" name="section['+countAddionalSection+'][weight]" value="0" class="form-control touchspin4">'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-xs-3 p-0 mt--25 pl-5">'+
                                '<div class="mt--10 f12">'+
                                    'Total Number of section question to display to student'+
                                '</div>'+
                                '<div class="d-inline-block w100">'+
                                    '<input type="text" name="section['+countAddionalSection+'][student_question_display]" id="student_question_display_'+countAddionalSection+'" value="1" class="form-control touchspin4 student_question_display">'+
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

                        $('[id^=student_question_display_]').each(function(e) {
                            $(this).rules('add', {
                                minvalue: true
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

    $(".remove_section_exist").on('click', function(){
        var remove_id = $(this).attr('data-remove-id');
        var record_id = $(this).attr('data-record-id');
        $.confirm({
            title: 'Confirm!',
            content: "Are you sure to remove?",
            buttons: {
                confirm: function () {
                    get_remove_section(remove_id,record_id);
                   
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