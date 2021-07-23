var SchoolCourseSection = function (){
	var webroot,siteID,csrf_tel_library;
	
	var getOtherViewTemplate = function(selectorView,recordID,siteID){
		$( "#"+selectorView).load( webroot +"containers/schools/other_load_view/"+selectorView+'/'+recordID+"/"+siteID, function() {});
	}

	var getSectionUpdateData = function(id){
		$.ajax({
			url      :   webroot+"containers/schoolsCourse/get_section_data",
			method   :   "POST",
			data     : {id:id,csrf_tel_library:csrf_tel_library},
			dataType : "json",
			beforeSend: function() {
		        $('.create_section_form').css('opacity','0.5');
		        $('.section_submit_button').html('Update');
		    },         
			success: function (result) {
				$('.create_section_form').css('opacity','1'); 
				if(result.code == 400){
					CommanJS.getDisplayMessgae(result.code,result.message); 
				}else{
					$("#section_id").val(result.data.id);
					$("#section").val(result.data.name);
					$("#course_section").val(result.data.course_id);
					$("#section_start_date").val(result.data.start_date);
					$("#section_end_date").val(result.data.end_date);
				}
				
			}
		});
	} 

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		csrf_tel_library = dataVariable.csrf_tel_library;
		//console.log(siteID);
		$('.remove_section').on("click", function(){
			var removeId = this.value;
			var rowTr = $(this).parent('td').parent('tr');
			$.confirm({
				title: 'Confirm!',
				content: 'Are you sure to remove?',
				buttons: {
					confirm: function () {
						$.ajax({
							url      :   webroot+"containers/schoolsCourse/removeSection",
							method   :   "POST",
							data     : {id:removeId,siteID:siteID,csrf_tel_library:csrf_tel_library	},
							dataType : "json",
							beforeSend: function(){
				                $(rowTr).css("opacity", "0.5");
				            },                 
							success: function (result) { 
								CommanJS.getDisplayMessgae(result.code,result.message); 
								getViewInnerTemplate('section_list',siteID);
								$("#section_id").val(''); 
							}
						});
					},
					cancel: function () {
					}
				}
			});
		});

		$('.get_make_update_section').on('click', function(){
			getSectionUpdateData($(this).attr('data-id'))
		});

	}		

	return {
		init:init,
		getOtherViewTemplate:getOtherViewTemplate
	}	

}();