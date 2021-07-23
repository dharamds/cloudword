var Edit = function () {
    var webroot,recordID,csrf_tel_library;

    var get_check_setting = function(tab, id){
        $.ajax({
            url      :   webroot+"assessments/get_check_setting",
            method   :   "POST",
            data     :  {'quizID':id,'csrf_tel_library':csrf_tel_library},
            dataType : "json",        
            success: function (result) {
               if(result == 0){
                    CommanJS.getDisplayMessgae("400","Sorry, You must complete setting section");
                    $("#quizPanelTab li").removeClass("active");
                    $("#tab-setting").addClass('active');
                    getViewTemplate('setting',recordID);  
               }else{
                    getViewTemplate(tab,recordID);  
               } 

                      
            }
        });
    }


    var init = function (dataVariable) {
        webroot = dataVariable.ajax_call_root;
        recordID = dataVariable.recordID;
        csrf_tel_library = dataVariable.csrf_tel_library;  

        $('.btnNext').click(function(){
            $('#quizPanelTab.nav-tabs > .active').next('li').find('a').trigger('click');
            $('body,html').animate({
                scrollTop: 0
            }, 500);
        });
        getViewTemplate('content',recordID);

        $(".change_tab").on('click', function(){
            $("#quizPanelTab li").removeClass("active");
            $("#"+this.id).addClass('active');
            var tab = $("#"+this.id).attr('data-tab-name');
            console.log(tab);
            if(tab == 'questions'){
                get_check_setting(tab,recordID);
            }else{
                getViewTemplate(tab,recordID); 
            }
            //getViewTemplate(tab,recordID); 
        });

    }   

    return {
        init:init
    }

}();