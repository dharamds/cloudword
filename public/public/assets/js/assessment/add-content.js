var AddContent = function () {
    var webroot,recordID,csrf_tel_library;


    var submitQuizContentForm = function() {
        $.ajax({
            url      :   webroot+"assessments/saveContents",
            method   :   "POST",
            data     :  $('#createassessments').serialize() + '&recordID='+recordID + "&csrf_tel_library="+csrf_tel_library,
            dataType : "json",        
            success: function (result) { 
                CommanJS.getDisplayMessgae(result.code,result.message); 
                if(result.code == 200){
                    $("#quizPanelTab li").removeClass("active");
                    $("#tab-metadata").addClass('active');
                    getViewTemplate('metadata',result.id); 
                }         
            }
        });
    }


  var createFormValidator = function() {
        $("#createassessments").validate({
            ignore: [],
            rules: {
                title: {
                    required : true,
                    noSpace  : true,
                    remote:{
                        url: webroot +"assessments/getCheckExist",
                        type: 'POST',
                        data: {'recordID':recordID,'csrf_tel_library':csrf_tel_library}
                    }  
                },
                long_title: {
                    noSpace  : true
                },
                quizInstructions:{
                    required: function(textarea) {
                          CKEDITOR.instances[textarea.id].updateElement(); // update textarea
                          var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
                          return editorcontent.length === 0;
                      }
                 }/*,
                quizFeedback:{
                    required: function(textarea) {
                          CKEDITOR.instances[textarea.id].updateElement(); // update textarea
                          var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
                          return editorcontent.length === 0;
                      }
                 }*/
            },
            messages: {
                title: {
                    required: "Please enter title",
                    remote: "Assessment name is already exist"                              
                },
                long_title: {
                    required: "Please enter long title"               
                },
                quizInstructions : {
                    required:"Please enter some content"
                }/*,
                quizFeedback : {
                    required:"Please enter some content"
                }*/
            },   
            submitHandler: function (form) { 
                submitQuizContentForm();
                return false;
            },
            errorPlacement: function(error, $elem) {
                if ($elem.is('textarea')) {
                    $elem.insertAfter($elem.next('div'));
                }
                error.insertAfter($elem);
            }
        });
    }

    var getActiveCkEditor = function(){
        

        CKEDITOR.replace( 'quizInstructions', {
            toolbar : 'short',
         });
        CKEDITOR.replace( 'quizFeedback', {
            toolbar : 'short',
        });
    }

  var init = function (dataVariable) {
    webroot = dataVariable.ajax_call_root;
    recordID = dataVariable.recordID;
    csrf_tel_library = dataVariable.csrf_tel_library;  

    jQuery.validator.addMethod("noSpace", function(value, element) { 
      return value == '' || value.trim().length != 0;  
    }, "Space is not allow");

    
    getActiveCkEditor();
    createFormValidator();   

    $(".click_to_next").on('click', function(){
        $("#quizPanelTab li").removeClass("active");
        $("#tab-setting").addClass('active');
        getViewTemplate('setting');
    });

    $(".click_to_finish").on('click', function(){
        window.location.href = webroot+"assessments";
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