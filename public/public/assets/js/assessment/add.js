var Add = function () {
  var webroot,csrf_tel_library;

  var init = function (dataVariable) {
    webroot = dataVariable.ajax_call_root;
    csrf_tel_library = dataVariable.csrf_tel_library;  

    $('.btnNext').click(function(){
        $('#quizPanelTab.nav-tabs > .active').next('li').find('a').trigger('click');
        $('body,html').animate({
            scrollTop: 0
        }, 500);
    });

    getViewTemplate('content');
  
  }   

  return {
    init:init
  }

}();