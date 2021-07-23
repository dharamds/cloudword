$(document).ready(function($) {

    var i = 1, j=1, k=1, uiItemtext = [], itemstatus;
    $(".dragAreaForModule").droppable({
        accept: "#moduleContainer li.ui-state-default",
        drop : function(event, ui){
            var listItem = $("<li>",{
                "id":"ui-list-modules-"+i,
                "class":"ui-custom-collapse",
                "data-container-id": $(ui.draggable).attr('data-list-id'),
                "html" : "<h3>"+$(ui.draggable).text()+"<span class=\"collapse-trigger\"></span><a class=\"dismiss ui-button\" data-enable-id=\""+$(ui.draggable).attr('data-list-id')+"\"></a></h3><div class=\"panel-list-area panel-lessons-theme collapse-panel\"><ul class=\"inset-container lessonsInsetContainer\"></ul><div class=\"panel-draggable-area for-lessons dragAreaForLessons\">Drop Lessons Here</div></div>",
            });
            $("#listOfAllSection").append(listItem);
            ui.draggable.draggable({disabled: true});
            ui.draggable.addClass("ui-throw-out");
            $("#listOfAllSection").sortable({
                appendTo: document.body,
                handle: "h3",
            }).disableSelection();

            $(".dragAreaForLessons").droppable({
                accept: "#lessonContainer li.ui-state-default",
                drop : function(event, ui){
                    var listLessonsItem = $("<li>",{
                        "id":"ui-list-lessons-"+j,
                        "class":"ui-custom-collapse",
                        "html" : "<h3>"+$(ui.draggable).text()+"<span class=\"collapse-trigger\"></span><a class=\"dismiss ui-button\" data-enable-id=\""+$(ui.draggable).attr('data-list-id')+"\" ></a></h3><div class=\"panel-list-area panel-assesments-theme collapse-panel\"><ul class=\"inset-container-quiz quizInsetContainer\"></ul><div class=\"panel-draggable-area for-quiz dragAreaForQuiz\">Drop Quiz Here</div></div>",
                    });
                    $(this).siblings('.lessonsInsetContainer').append(listLessonsItem);
                    ui.draggable.draggable({disabled: true});
                    ui.draggable.addClass("ui-throw-out");

                    $(".lessonsInsetContainer").sortable({
                        appendTo: document.body,
                        handle: "h3",
                    }).disableSelection();
                    
                    $(".dragAreaForQuiz").droppable({
                        accept: "#assessmentsContainer li.ui-state-default",
                        drop : function(event, ui){
                            var listQuizItem = $("<li>",{
                                "id":"ui-list-quiz-"+k,
                                "class":"ui-custom-collapse",
                                "html" : "<h3>"+$(ui.draggable).text()+"<a class=\"dismiss ui-button\"  data-enable-id=\""+$(ui.draggable).attr('data-list-id')+"\"></a></h3>",
                            });
                            $(this).siblings('.quizInsetContainer').append(listQuizItem);
                            ui.draggable.draggable({disabled: true});
                            ui.draggable.addClass("ui-throw-out");
                        },
                        
                    });
                },
            });
        },
    });
    $(".newConnectedSortable").each(function(){
        $(this).sortable({
            appendTo: document.body,
            handle: "h3",
        }).disableSelection();
    });
    $(".lessonsInsetContainer").each(function(){
        $(this).sortable({
            appendTo: document.body,
            handle: "h3",
        }).disableSelection();
    });  
    $(".quizInsetContainer").each(function(){
        $(this).sortable({
            appendTo: document.body,
            handle: "h3",
        }).disableSelection();
    });        
    
    
    $("#leftSortableSection .connectedSortable li").draggable({
        connectToSortable: "#listOfAllSection",
        helper: "clone",
        revert: "invalid",
        start: function( event, ui){
            
        },
        drag: function( event, ui ) {
            
        }
    });

    function collapseElement(){
        $(".collapse-trigger").on("click",function(){
            $(this).toggleClass('active');

            if( $(this).parent().siblings('.collapse-panel').is(':hidden')){
                $(this).addClass('open');
                $(this).parent().siblings('.collapse-panel').slideDown();
            }else{
                $(this).removeClass('open');
                
                $(this).parent().siblings('.collapse-panel').slideUp();
            }
        });
    }


});