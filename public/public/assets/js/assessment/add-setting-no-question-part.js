var AddSettingNoQuestionPart = function () {
   var webroot,recordID;
   

  var init = function (dataVariable) {
    webroot = dataVariable.ajax_call_root;  
    recordID = dataVariable.recordID;

    $('#question_per_quiz').rules('add', {
        minvalue: true
    });

    $("input.touchspin4").TouchSpin({
        verticalbuttons: true,
        step: 1,
        min : 1,
        max : 100
    });

  }   

  return {
    init:init
  }

}();