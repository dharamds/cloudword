/*!
 * File:        jquery.dataTables.rowReordering.js
 * Version:     1.2.3 / Datatables 1.10 hack
 * Author:      Jovan Popovic
 *
 * Copyright 2013 Jovan Popovic, all rights reserved.
 *
 * This source file is free software, under either the GPL v2 license or a
 * BSD style license, as supplied with this software.
 */
/*
 * NOTES:
 * 
 * This source file is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * 
 * Modified by Jeremie Legrand (KOMORI-CHAMBON SAS):
 *  - fast and ugly modification for Datatables 1.10 compatibility (at least, move rows even if it's really slow...)
 *  - can prevent datatable to actually DO the reordering at the end (after the success ajax call), for example
 *      if we want to reload the whole table in ajax just after:
 *              oTable.rowReordering({
 *                      sURL: 'UpdateRowOrder.php',
 *                      avoidMovingRows: true
 *              });
 *  - can call a fnUpdateCallback() function when drop is finished
 *      (Integration of a free comment in: https://code.google.com/p/jquery-datatables-row-reordering/wiki/Index)
 *              Author: "Comment by ra...@webrun.ca, Mar 16, 2013"
 *  - can pass additional data in POST, like this:
 *              oTable.rowReordering({
 *                       sURL: 'UpdateRowOrder.php',
 *                              sData: {'var_name': 'big content here'}
 *                      });
 * - on Ajax error return code, give to fnError the response text instead of xhr.statusText, if any,
 * - FIX a crash when 'tr' in 'tbody' didn't have an ID (the function fnGetState() made
 *              a $("#" + id, oTable) to get the 'tr' instead of just get it from the context) 
 * -
 *
 * Parameters:
 * @iIndexColumn     int         Position of the indexing column
 * @sURL             String      Server side page tat will be notified that order is changed
 * @iGroupingLevel   int         Defines that grouping is used
 */
(function ($) {

   "use strict";
   $.fn.rowReordering = function (options) {

      function _fnStartProcessingMode(oTable) {
       
         ///<summary>
         ///Function that starts "Processing" mode i.e. shows "Processing..." dialog while some action is executing(Default function)
         ///</summary>
         if (oTable.fnSettings().oFeatures.bProcessing) {
            $(".dataTables_processing").css('visibility', 'visible');
         }

      }

      function _fnEndProcessingMode(oTable) 
      {

         ///<summary>
         ///Function that ends the "Processing" mode and returns the table in the normal state(Default function)
         ///</summary>
         if (oTable.fnSettings().oFeatures.bProcessing) {
            $(".dataTables_processing").css('visibility', 'hidden');
         }

      }
      ///Not used
      function fnGetStartPosition(oTable, sSelector) 
      {

         var iStart = 1000000;
         $(sSelector, oTable).each(function () {
            iPosition = parseInt(oTable.fnGetData(this, properties.iIndexColumn), 10);
            if (iPosition < iStart)
               iStart = iPosition;
         });
         return iStart;

      }

      function fnCancelSorting(oTable, tbody, properties, iLogLevel, sMessage) {

         tbody.sortable('cancel');
         if(iLogLevel<=properties.iLogLevel){
            if(sMessage!= undefined){
               properties.fnAlert(sMessage, "");
            }else{
               properties.fnAlert("Row cannot be moved", "");
            }
         }
         properties.fnEndProcessingMode(oTable);

      }

      // ### KCM ### Get $('tr') instead of 'tr id', to avoid re-mapping in jQuery object from it's id (which can be null)
      // #function fnGetState(oTable, sSelector, id) {
      function fnGetState(oTable, sSelector, tr) {

      //var tr = $("#" + id, oTable);
      // ### END ###

      // console.log('iCurrentPosition'+tr[0]);
      // console.log('properties.iIndexColumn'+properties.iIndexColumn);
      // console.log('oTable.fnGetData(tr[0], properties.iIndexColumn)'+JSON.stringify(oTable.fnGetData(tr[0], properties.iIndexColumn)));
         
      var iCurrentPosition = parseInt(oTable.fnGetData(tr[0], properties.iIndexColumn), 100);
    //     console.log('iCurrentPosition'+iCurrentPosition);
         var iNewPosition = -1; // fnGetStartPosition(sSelector);
         var sDirection;
         var trPrevious = tr.prev(sSelector);
         if (trPrevious.length > 0) {
            iNewPosition = parseInt(oTable.fnGetData(trPrevious[0], properties.iIndexColumn), 10);
            if (iNewPosition < iCurrentPosition) {
               iNewPosition = iNewPosition + 1;
            }
         } else {
            var trNext = tr.next(sSelector);
            if (trNext.length > 0) {
               iNewPosition = parseInt(oTable.fnGetData(trNext[0], properties.iIndexColumn), 10);
               if (iNewPosition > iCurrentPosition)//moved back
               iNewPosition = iNewPosition - 1;
            }
         }
         if (iNewPosition < iCurrentPosition)
            sDirection = "back";
         else
            sDirection = "forward";

         return { sDirection: sDirection, iCurrentPosition: iCurrentPosition, iNewPosition: iNewPosition };

      }

      function fnMoveRows(oTable, sSelector, iCurrentPosition, iNewPosition, sDirection, id, sGroup) {
         var iStart = iCurrentPosition;
         var iEnd = iNewPosition;
         if (sDirection == "back") {
            iStart = iNewPosition;
            iEnd = iCurrentPosition;
         }

         $(oTable.fnGetNodes()).each(function () {
            if (sGroup != "" && $(this).attr("data-group") != sGroup)
               return;
            var tr = this;
            var iRowPosition = parseInt(oTable.fnGetData(tr, properties.iIndexColumn), 10);
            if (iStart <= iRowPosition && iRowPosition <= iEnd) {
               if (tr.id == id) {
                  oTable.fnUpdate(iNewPosition,
                        oTable.fnGetPosition(tr), // get row position in current model
                        properties.iIndexColumn,
                        false); // false = defer redraw until all row updates are done
               } else {
                  if (sDirection == "back") {
                     oTable.fnUpdate(iRowPosition + 1,
                           oTable.fnGetPosition(tr), // get row position in current model
                           properties.iIndexColumn,
                           false); // false = defer redraw until all row updates are done
                  } else {
                     oTable.fnUpdate(iRowPosition - 1,
                           oTable.fnGetPosition(tr), // get row position in current model
                           properties.iIndexColumn,
                           false); // false = defer redraw until all row updates are done
                  }
               }
            }
         });

         var oSettings = oTable.fnSettings();

         //Standing Redraw Extension
         //Author:   Jonathan Hoguet
         //http://datatables.net/plug-ins/api#fnStandingRedraw
         if (oSettings.oFeatures.bServerSide === false) {
            var before = oSettings._iDisplayStart;
            oSettings.oApi._fnReDraw(oSettings);
            //iDisplayStart has been reset to zero - so lets change it back
            oSettings._iDisplayStart = before;
            oSettings.oApi._fnCalculateEnd(oSettings);
         }
         //draw the 'current' page
         oSettings.oApi._fnDraw(oSettings);
      }

      function _fnAlert(message, type) { alert(message); }

      var defaults = {
            iIndexColumn: 0,
            iStartPosition: 1,
            sURL: null,
            sRequestType: "POST",
            iGroupingLevel: 0,
            fnAlert: _fnAlert,
            fnSuccess: jQuery.noop,
            iLogLevel: 1,
            sDataGroupAttribute: "data-group",
            fnStartProcessingMode: _fnStartProcessingMode,
            fnEndProcessingMode: _fnEndProcessingMode,
            fnUpdateAjaxRequest: jQuery.noop,
            sectionData:null
      };

      var properties = $.extend(defaults, options);

      var iFrom, iTo;

      // Return a helper with preserved width of cells (see Issue 9)
      var tableFixHelper = function(e, tr) 
      {
         var $originals = tr.children();
         var $helper = tr.clone();
         $helper.children().each(function(index){
            // Set helper cell sizes to match the original sizes
            $(this).width($originals.eq(index).width());
         });
         return $helper;
      };

      // ### KCM ### Ugly and fast method to get dataTable object
      var tables;
      if(this instanceof jQuery){
         tables = this;
      } else {
         tables = this.context;
      }

      $.each(tables, function () {
         var oTable;

         if (typeof this.nodeType !== 'undefined'){
            oTable = $(this).dataTable();
         } else {
            oTable = $(this.nTable).dataTable();
         }
         
         var aaSortingFixed = (oTable.fnSettings().aaSortingFixed == null ? new Array() : oTable.fnSettings().aaSortingFixed);
         aaSortingFixed.push([properties.iIndexColumn, "asc"]);

         oTable.fnSettings().aaSortingFixed = aaSortingFixed;

         
         for (var i = 0; i < oTable.fnSettings().aoColumns.length; i++) {
            oTable.fnSettings().aoColumns[i].bSortable = false;
            /*for(var j=0; j<aaSortingFixed.length; j++)
         {
         if( i == aaSortingFixed[j][0] )
         settings.aoColumns[i].bSortable = false;
         }*/
         }
         oTable.fnDraw();

         $("tbody", oTable).disableSelection().sortable({
            cursor: "move",
            helper: tableFixHelper,
            update: function (event, ui) {

               ///console.log("ui"+ui);
               var $dataTable = oTable;
               var tbody = $(this);
               var sSelector = "tbody tr";
               var sGroup = "";
               //alert('test');
              // console.log('tbody');
              // console.log(tbody);
               
                var info = $('#selected_quiz_table').DataTable().page.info();
                var startIndex = info.start;
                var endIndexPage = info.end;
                var recordId=[];
               $('.selected_question_select').each(function(index ){
                  recordId.push($(this).attr('data-quiz-section-id'));
               });
            
               var sortingTableArray = {
                                        "recordId":recordId,
                                        "startIndex":startIndex,
                                        "endIndexPage":endIndexPage,
                                        "quizID":properties.sectionData.quizID,
                                        "sectionID":properties.sectionData.sectionID
                                    };

               if (properties.bGroupingUsed) {
                  sGroup = $(ui.item).attr(properties.sDataGroupAttribute);
                  if(sGroup==null || sGroup==undefined){
                     fnCancelSorting($dataTable, tbody, properties, 3, "Grouping row cannot be moved");
                     return;
                  }
                  sSelector = "tbody tr[" + properties.sDataGroupAttribute + " ='" + sGroup + "']";
               }

               // ### KCM ###
               //      pass 'tr' directly, instead of giving id then redo a $('#' + id) in the function...
               // #var oState = fnGetState($dataTable, sSelector, ui.item.context.id);
               var tr = $( ui.item.context );
               var oState = fnGetState($dataTable, sSelector, tr);
               /// ### END ###
               if(oState.iNewPosition == -1) {
                  fnCancelSorting($dataTable, tbody, properties,2);
                  return;
               }

               var sRequestData = {
                  id: ui.item.context.id,
                  fromPosition: oState.iCurrentPosition,
                  toPosition: oState.iNewPosition,
                  direction: oState.sDirection,
                  group: sGroup,
                  "sortingTableArray":sortingTableArray,
                  // ### KCM ### Can pass additional data in POST
                  data: properties.sData
                  // ### END ###
               };

               if (properties.sURL != null) {
                  properties.fnStartProcessingMode($dataTable);
                  var oAjaxRequest = {
                        url: properties.sURL,
                        type: properties.sRequestType,
                        data: sRequestData,
                        success: function (data) {
                            return false;
                           properties.fnSuccess(data);

                           // ###KCM### Can avoid moving rows if we want (for example if we reload all the table in ajax juste after)
                           if(! properties.avoidMovingRows)
                              fnMoveRows($dataTable, sSelector, oState.iCurrentPosition, oState.iNewPosition, oState.sDirection, ui.item.context.id, sGroup);
                           // ### END ###
                           properties.fnEndProcessingMode($dataTable);

                           // ###KCM### Can have a callback when drop is finished
                           // Source: 
                           //      https://code.google.com/p/jquery-datatables-row-reordering/wiki/Index,
                           //      --> Free comment of "Comment by ra...@webrun.ca, Mar 16, 2013"
                           if(properties.fnUpdateCallback && typeof(properties.fnUpdateCallback) === 'function'){
                              properties.fnUpdateCallback(sRequestData);
                           }
                           // ###END###
                        },
                        error: function (jqXHR) {
                           //### KCM ### Get response text instead of statusText if any
                           // #fnCancelSorting($dataTable, tbody, properties, 1, jqXHR.statusText);
                           var err = (jqXHR.responseText != "" ? jqXHR.responseText : jqXHR.statusText);
                           fnCancelSorting($dataTable, tbody, properties, 1, err);
                           // ### END ###
                        }
                  };
                  properties.fnUpdateAjaxRequest(oAjaxRequest, properties, $dataTable);
                  $.ajax(oAjaxRequest);
               } else {
                  fnMoveRows($dataTable, sSelector, oState.iCurrentPosition, oState.iNewPosition, oState.sDirection, ui.item.context.id, sGroup);

                  // ###KCM### Can have a callback when drop is finished
                  // Source: 
                  //      https://code.google.com/p/jquery-datatables-row-reordering/wiki/Index,
                  //      --> Free comment of "Comment by ra...@webrun.ca, Mar 16, 2013"
                  if(properties.fnUpdateCallback && typeof(properties.fnUpdateCallback) === 'function'){
                     properties.fnUpdateCallback(sRequestData);
                  }
               }

            }
         });
      });

      return this;
   };

   // Attach RowReordering to DataTables so it can be accessed as an 'extra'
   $.fn.dataTable.rowReordering = $.fn.rowReordering;
   $.fn.DataTable.rowReordering = $.fn.rowReordering;

   // DataTables 1.10 API method aliases
   if ( $.fn.dataTable.Api ) {
      var Api = $.fn.dataTable.Api;
      Api.register( 'rowReordering()', $.fn.rowReordering );
   }
})(jQuery);





var AddQuestionSectionPart = function () {
    var webroot,quizID,sectionID,selectedTable,csrf_tel_library;

    var get_open_questions_popup = function(){
        $.ajax({
            url      :   webroot+"quiz/get_active_questions",
            method   :   "POST",
            data     : {'quizID':quizID,'sectionID':$('#selected_que_section_id option:selected').val(),'csrf_tel_library':csrf_tel_library},
            dataType : "html",        
            success: function (result) { 
                $("#popup-questions").html(result);
                $("#sectionQuestionModal").modal({backdrop: 'static',keyboard: false});
            }
        });
        return false;
    }

    var reload_selected_tables = function (){
         selectedTable.search('').draw();
    }

    var selected_questions_datatable = function () {
         table1 = selectedTable = $('#selected_quiz_table').DataTable({
            // Processing indicator
            "processing": true,
            // DataTables server-side processing mode
            "serverSide": true,
            // Initial no order.
            "iDisplayLength": 10,
            "bPaginate": true,
            "scrollX": true,
            "autoWidth": false,
            // "order": [],
            "drawCallback": function(settings) {
                $('.load_meta_data').each(function(key, item) {
                    $.getJSON(webroot + "/questions/get_meta_tags/" + $(this).attr('id'), function(data) {
                           if(data) $("#"+data.typeid).html(data.value);
                    });
                });      
                
                $('.load_meta_category').each(function (key, item) {
                    $.getJSON(webroot + 'questions/get_meta_categories/' + $(this).attr('id'), function (data) {
                        if (data) $("#" + data.typeid).html(data.value);
                    });
                });
                },
            // Load data from an Ajax source
            "ajax": {
                "url": webroot + "quiz/questions/getSelectedLists/",
                "type": "POST",
                "data": function (data) {
                    data.sectionID = sectionID;
                    data.quizID = quizID;
                    
                    data.system_category = CommanJS.getMetaCallBack('system_category');
                    data.system_tag = CommanJS.getMetaCallBack('system_tag');
                    data.assessment_category = CommanJS.getMetaCallBack('assessment_category');
                    data.assessment_tag = CommanJS.getMetaCallBack('assessment_tag');
                    data.outcomes_category = CommanJS.getMetaCallBack('learning_outcomes');
                    data.tel_mastery_standards = CommanJS.getMetaCallBack('tel_mastery_standards');
                    data.blooms_level_category = CommanJS.getMetaCallBack('blooms_level');
                    data.csrf_tel_library = csrf_tel_library;

                   /* data.course_tag = $('#selected_toggle_filters #selected_course_tag option:selected').val();
                    data.module_tag = $('#selected_toggle_filters #selected_module_tag option:selected').val();
                    data.lesson_tag = $('#selected_toggle_filters #selected_lesson_tag option:selected').val();*/
                   /* data.outcomes_category = $('#selected_toggle_filters #selected_learning_outcomes option:selected').val();
                    data.standard_category = $('#selected_toggle_filters #selected_learning_standards option:selected').val();*/



                    /*data.compentencies_category = $('#selected_toggle_filters #selected_learning_competencies option:selected').val();
                    data.skills_category = $('#selected_toggle_filters #selected_learning_skills option:selected').val();*/
                },
            },
            'createdRow': function(row, data, dataIndex){
                    $(row).attr('id', 'row-' + dataIndex);

            },
            "columnDefs": [
      //           {
      //               "targets": [3],
      //               "data": null,
      //               "render": function (data, type, full, meta) {
      //                   if (type === 'display') {
      //                      data = '<span id="' + full['id'] +'_11_9_assessment_category" data-type="assessment_category" class="load_meta_category"> Loading...</span>';
						//  //data = 2;
						// }
      //                   return data;
      //               }
      //           },
      //           {
      //               "targets": [4],
      //               "data": null,
      //               "render": function (data, type, full, meta) {
      //                   if (type === 'display') {
      //                      data = '<span id="' + full['id'] +'_11_9" data-type="assessment_tags" class="load_meta_data"> Loading...</span>';
      //                   }
      //                   return data;
      //               }
      //           },
                {
                    "targets": [3],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_11_1_system_category" data-type="system_category" class="load_meta_category"> Loading...</span>';
						 //data = 2;
						}
                        return data;
                    }
                },
                {
                    "targets": [4],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_11_1" data-type="system_tags" class="load_meta_data"> Loading...</span>';
                        }
                        return data;
                    }
                },
                {
                    "targets": [5],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_11_14_learning_outcomes" data-type="learning_outcomes" class="load_meta_category"> Loading...</span>';
						 //data = 2;
						}
                        return data;
                    }
                },
                {
                    "targets": [6],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_11_15_tel_standards" data-type="tel_standards" class="load_meta_category"> Loading...</span>';
						 //data = 2;
						}
                        return data;
                    }
                },
                {
                    "targets": [7],
                    "data": null,
                    "render": function (data, type, full, meta) {
                        if (type === 'display') {
                           data = '<span id="' + full['id'] +'_11_1_blooms_level" data-type="blooms_level" class="load_meta_category"> Loading...</span>';
						 //data = 2;
						}
                        return data;
                    }
                },
            ],
            //Set column definition initialisation properties
            "columns": [
                {
                    "data":"sr_no",
                    "orderable": false,
                    "data": null,
                     render: function (data, type, row, meta) {
                          if(type === 'display'){
                                data = '<label class="checkbox-tel"><input type="checkbox" data-quiz-section-id="'+row['quiz_section_id']+'" class="selected_question_select" name="selected_question_select" value="' + row['id'] + '" class="mr-2"></label>';
                            }
                            return data;
                        },
                    "autoWidth": true
                },                
                {
                    "data": "title",
                    "autoWidth": true
                },
                {
                    "data": "long_title",
                    "autoWidth": true
                },
                // {
                //     "data": "assesment_category",
                //     "autoWidth": true
                // },
                // {
                //     "data": "assessment_tags",
                //     "autoWidth": true
                // },
                {
                    "data": "system_category",
                    "autoWidth": true
                },
                {
                    "data": "system_tags",
                    "autoWidth": true
                },
                /*{
                    "data": "courses_tags",
                    "autoWidth": true
                },
                {
                    "data": "modules_tags",
                    "autoWidth": true
                },
                {
                    "data": "lessons_tags",
                    "autoWidth": true
                },*/
                {
                    "data": "outcomes_category",
                    "autoWidth": true
                },
                {
                    "data": "standards_category",
                    "autoWidth": true
                },
                {
                    "data": "blooms_level_category",
                    "autoWidth": true
                },
                /*{
                    "data": "skills_category",
                    "autoWidth": true
                }*/
            ]
        });
        var sectionData = {"quizID":quizID,"sectionID":sectionID};
        table1.rowReordering({
            sURL:webroot +'quiz/getListToSort',
            sectionData:sectionData
        });
    }

    var getRemoveQuestionFromSections = function(questions, quizID, sectionsID){

        $.ajax({
            url: webroot+"quiz/questions/selectedRemove",
            method: "POST",
            data:{'selected_record':questions,'quizID':quizID,'sectionID':sectionID,'csrf_tel_library':csrf_tel_library},
            dataType:'json',
            beforeSend: function(){
               // $(wholeEle).css("opacity", "0.5");
            },
            success: function(result){
                CommanJS.getDisplayMessgae(result.code,result.message);
                $("#selected_select_all").prop("checked",false);
                $(".selected_question_select").prop("checked",false);
                if(result.code == 200){
                    selectedTable.search('').draw();
                }
            }
        });
        return false;
    }

    var init = function (dataVariable) {
        webroot = dataVariable.ajax_call_root;
        quizID = dataVariable.quizID;
        sectionID = dataVariable.sectionID;
        csrf_tel_library = dataVariable.csrf_tel_library;  
        //console.log(dataVariable);
        selected_questions_datatable();

        CommanJS.get_metadata_options("Systems category", 1, 1, "selected_system_cat");
        CommanJS.get_metadata_options("Systems tag", 2, 1, "selected_system_tag");
        CommanJS.get_metadata_options("Assessments category", 1, 9, "selected_assessment_cat");
        CommanJS.get_metadata_options("Assessments tag", 2, 9, "selected_assessment_tag");
        /*CommanJS.get_metadata_options("Course tag", 2, 4, "selected_course_tag");
        CommanJS.get_metadata_options("Module tag", 2, 6, "selected_module_tag");
        CommanJS.get_metadata_options("Lesson tag", 2, 5, "selected_lesson_tag");*/
        CommanJS.get_metadata_options("Learnings outcomes", 1, 14, "selected_outcomes_cat");
        CommanJS.get_metadata_options("LMS mastery standard", 1, 15, "selected_standard_cat");
        CommanJS.get_metadata_options("Bloom level", 1, 18, "selected_bloom_level_cat");


        /*CommanJS.get_metadata_options("Learning competencies", 1, 16, "selected_competencies_cat");
        CommanJS.get_metadata_options("Learning skills", 1, 17, "selected_skills_cat");*/

        /*$('#selected_toggle_filters').on('change', '.metadata_selector', function() {
           selectedTable.search('').draw();
        });*/

        $('#selected_section_part').on('change','.meta_data_filter', function(){
            selectedTable.search('').draw();
        });

        $("#selected_toggle_filters").on('click','.selected_reset_filter', function(){
            $(".meta_data_filter").prop("checked",false);
            $(".selectFilterMode span").text('');
            selectedTable.search('').draw(); 
           /* $("#selected_toggle_filters .metadata_selector").val(0);
           selectedTable.search('').draw(); */         
        });

        $("#selected_select_all").click(function () {
            $(".selected_question_select").prop('checked', $(this).prop('checked'));
        });

        $("#selected_quiz_table_wrapper").on('change','.selected_question_select',function(){
            if (!$(this).prop("checked")){
                $("#selected_select_all").prop("checked",false);
            }
        });

        $(".remove-questions-from-section").on('click', function(){
            var questions = [];
            $.each($("input[name='selected_question_select']:checked"), function(){ 
                questions.push($(this).val());
            });
            if(questions.length == 0){
                CommanJS.getDisplayMessgae('400','Please select at least one record');
                return false;
            }
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure to remove selected questions?",
                buttons: {
                    confirm: function () {
                        getRemoveQuestionFromSections(questions,quizID,sectionID);    
                    },
                    cancel: function () {
                    }
                }
            });
            
        });


        $('#selected_quiz_table_wrapper .dataTables_filter input').attr('placeholder','Search...');
        $('#addQuestionSectionOPtions .panel-ctrls').append($('#selected_quiz_table_wrapper .dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
        $('#addQuestionSectionOPtions .panel-ctrls').append("<i class='separator'></i>");
        $('#addQuestionSectionOPtions .panel-ctrls').append($('#selected_quiz_table_wrapper .dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");
        $('#addQuestionSectionOPtions .panel-footer').append($("#selected_quiz_table_wrapper .dataTable+.row"));
        $('#selected_quiz_table_wrapper .dataTables_paginate>ul.pagination').addClass("pull-right m-n");
        $("#selected_quiz_table_wrapper .dataTables_scroll").addClass('mb-4');
        
    /*$(".scroll-top").on("click", function(){
    $('body,html').animate({
    scrollTop: 0
    }, 500);
    }); */


    }   

    return {
        init:init,
        get_open_questions_popup:get_open_questions_popup,
        reload_selected_tables:reload_selected_tables
    }

}();