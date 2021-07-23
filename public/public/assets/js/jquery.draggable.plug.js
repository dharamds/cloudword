$(function(){

	var i = 0, j = 0, k = 0, l = 0;
	
	$("[data-collapse-trigger]").on('click', function(){
	    $($(this).attr('data-collapse-trigger')).slideToggle();
        $(this).toggleClass('active');
	});

	$("#moduleContainer li, #lessonContainer li, #assessmentsContainer li").draggable({ 
        appendTo: "body",
        helper: 'clone',
        connectWith: ".connectedSortable",
        revert: "invalid",
        appendTo: "body",
    });
    //enableSortable();
	$('.dragAreaForModule')
	.droppable({
	    accept: "#moduleContainer li.ui-state-default",
	    drop : function(event, ui) {
	        $(this).find(".ui-state-highlight").remove();
	        var listLi = $("<li>",{
	            "id":"ui-list-modules-"+i,
	            "class":"ui-custom-collapse",
	            "data-container-id": ui.draggable.attr('data-list-id'),
	            "html" : "<h3><span class=\"move\">"+ui.draggable.text()+"</span><span class=\"collapse-trigger\"></span><a class=\"dismiss ui-button\" data-enable-id=\""+$(ui.draggable).attr('data-list-id')+"\"></a></h3><div class=\"panel-list-area panel-lessons-theme collapse-panel\"><ul class=\"inset-container lessonsInsetContainer\"></ul><div class=\"panel-draggable-area for-lessons dragAreaForLessons\">Drop Lessons Here</div></div>",
	        });
            // Saving data module in course
            var mid = ui.draggable.attr('data-list-id'); // module id
            var cid = $(".set_tab").attr('data-id'); // course id
            $.ajax({
               
                url: '<?php echo bs('courses/builder'); ?>',
                type: "POST",
                data: {'module_id':mid  , 'course_id': cid},                

            });
	        $(listLi).appendTo($("#listOfAllSection"));
	        lessonSection();
	        $(ui.draggable).addClass('ui-throw-out');
	        $(ui.draggable).draggable( "disable" );
	        i++;
	    },
	    over : function(event, ui){                      
	    }
	});
	$("#listOfAllSection")
    .sortable({
        placeholder: "ui-state-highlight",
        //items: "> li",
        tolerance:'intersect',
        cursor: 'move',
        handle: '.move',
        receive: function (event, ui) {
            ui.item.remove();
        },
        sort: function( event, ui ) {
            var getSortableItem = $(ui.item).outerHeight();
            $(ui.placeholder).css({"height":getSortableItem})
        }
    }).disableSelection()
    .on("click", ".dismiss", function(event) {
        event.preventDefault();
        var $thisId = $(this).parents('li').attr("id");
        $("#"+$thisId).remove();
        var allEnableId = $(this).parent().parent().children().find('a');
        allEnableId.each(function(){
            var allChildrenElement = $(this).attr('data-enable-id');
            $("li[data-list-id=\""+allChildrenElement+"\"]").removeClass('ui-throw-out');
            $("li[data-list-id=\""+allChildrenElement+"\"]").draggable( "enable" );
        })
        
        $("li[data-list-id=\""+$(this).attr('data-enable-id')+"\"]").removeClass('ui-throw-out');
        $("li[data-list-id=\""+$(this).attr('data-enable-id')+"\"]").draggable( "enable" );
        
    })
    .on("click",".collapse-trigger",function(){
        $(this).toggleClass('active');
        $(this).parent().siblings('.collapse-panel').slideToggle(function(){
            if($(this).is(':hidden')){
                $(this).removeClass('open');
                //enableSortable();
            }else{
                $(this).addClass('open');
                //disableSortable()
            }
        });
    });
    $(".collapse-trigger").on("click",function(){
        $(this).parent().siblings('.collapse-panel').slideToggle(function(){
            if($(this).is(':hidden')){
                $(this).removeClass('open');
                //enableSortable();
            }else{
                $(this).addClass('open');
                //disableSortable()
            }
        });
    });

	function lessonSection () {
	    $(".lessonsInsetContainer")
        .sortable({
            placeholder: "ui-state-highlight",
            cursor: 'move',
            sort: function( event, ui ) {
                var getSortableItem = $(ui.item).outerHeight();
                $(ui.placeholder).css({"height":getSortableItem})
            }
        })
	    .on("click", ".dismiss", function(event) {
	        event.preventDefault();
	        var $thisId = $(this).parents('li').attr("id");
	        $("#"+$thisId).remove();
            removeAllChildrenElement($(this));
	        $("li[data-list-id=\""+$(this).attr('data-enable-id')+"\"]").removeClass('ui-throw-out');
        	$("li[data-list-id=\""+$(this).attr('data-enable-id')+"\"]").draggable( "enable" );
	    });                    
	    $('.dragAreaForLessons')
	    .droppable({
	        accept: "#lessonContainer li",
	        drop : function(event, ui) {
	            $(this).find(".ui-state-highlight").remove();
	            var listLi = $("<li>",{
	                "id":"ui-list-lessons-"+j,
	                "class":"ui-custom-collapse",
	                "html" : "<h3><span class=\"move\">"+ui.draggable.text()+"</span><span class=\"collapse-trigger\"></span><a class=\"dismiss ui-button\" data-enable-id=\""+$(ui.draggable).attr('data-list-id')+"\" ></a></h3><div class=\"panel-list-area panel-assesments-theme collapse-panel\"><ul class=\"inset-container-quiz quizInsetContainer\"></ul><div class=\"panel-draggable-area for-quiz dragAreaForQuiz\">Drop Quiz Here</div></div>",
	            });
	            $(listLi).appendTo($(this).siblings('.lessonsInsetContainer'));
	            $(ui.draggable).addClass('ui-throw-out');
	        	$(ui.draggable).draggable( "disable" );
	            j++;
	            quizSection();
	        },
	        over : function(event, ui){                      
	        }
	    });
	}

	function quizSection () {
        $(".quizInsetContainer")
        .sortable({
            placeholder: "ui-state-highlight",
            sort: function( event, ui ) {
                var getSortableItem = $(ui.item).outerHeight();
                $(ui.placeholder).css({"height":getSortableItem})
            }
        })
        .on("click", ".dismiss", function(event) {
            event.preventDefault();
            var $thisId = $(this).parents('li').attr("id");
            $("#"+$thisId).remove();
            $("li[data-list-id=\""+$(this).attr('data-enable-id')+"\"]").removeClass('ui-throw-out');
        	$("li[data-list-id=\""+$(this).attr('data-enable-id')+"\"]").draggable( "enable" );
        });                    
        $('.dragAreaForQuiz')
        .droppable({
            accept: "#assessmentsContainer li",
            drop : function(event, ui) {
                $(this).find(".ui-state-highlight").remove();
                var listLi = $("<li>",{
                    "id":"ui-list-quiz-"+k,
                    "class":"ui-custom-collapse",
                    "html" : "<h3><span class=\"move\">"+ui.draggable.text()+"</span><a class=\"dismiss ui-button\"  data-enable-id=\""+$(ui.draggable).attr('data-list-id')+"\"></a></h3>",
                });
                $(listLi).appendTo($(this).siblings('.quizInsetContainer'));
                k++;
                $(ui.draggable).addClass('ui-throw-out');
                $(ui.draggable).draggable( "disable" );
            },
            over : function(event, ui){                      
            }
        });
    }


    function removeAllChildrenElement($this){
        var childeElement = $this.parent().siblings('.collapse-panel').children('ul').children('li');
        childeElement.each(function(){
            var chLiElement = $(this).find('a').attr('data-enable-id');
            $("li[data-list-id=\""+chLiElement+"\"]").removeClass('ui-throw-out');
            $("li[data-list-id=\""+chLiElement+"\"]").draggable( "enable" );   
        });
    }
    function disableSortable(){
        $("#listOfAllSection").sortable({
           disabled: true 
        }).enableSelection();
    }
    function enableSortable(){
        $("#listOfAllSection").sortable({
            disabled: false,
            placeholder: "ui-state-highlight",
            tolerance:'intersect',
            receive: function (event, ui) {
                ui.item.remove();
            },
            sort: function( event, ui ) {
                var getSortableItem = $(ui.item).outerHeight();
                $(ui.placeholder).css({"height":getSortableItem})
            }
        }).disableSelection();
    }

});
