var SchoolCourse = function (){
	var webroot,siteID,csrf_tel_library,langs;
	
	
	var submitSectionForm = function(form) {
		// e.preventDefault();
		
		$.ajax({
 			url      :   webroot+"containers/schoolsCourse/saveCourseSections",
 			method   :   "POST",
 			data     :  $('#setupcourse').serialize()+ "&siteID=" + siteID + "&csrf_tel_library="+csrf_tel_library,
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);
 				if(result.code == 200){
 					$("#setupcourse")[0].reset();
 					$("#section_id").val('');
 					$('.section_submit_button').html('Save');
 					getViewInnerTemplate('section_list',siteID);
 				}	
 				
 			}
 		});
	}


	var createSectionFormValidator = function() {
		$("#setupcourse").validate({
			ignore: [],
			rules: {
				section: {
					required : true,
					noSpace : true
				},
				course_section: {
					required : true
				},
				section_start_date: {
					required : true
				},
				section_end_date: {
					required : true
				}
			},
			messages: {
				section: {
					required: "Please enter name"					          
				},
				course_section: {
					required: "Please select course"					          
				},
				section_start_date: {
					required: "Please enter start date"					          
				},
				section_end_date: {
					required: "Please enter end date"					          
				}
			},   
			submitHandler: function (form) { 
				submitSectionForm(form);
				return false;
			},
			errorPlacement: function (error, element) {
				error.insertAfter(element.parent('.input-group'));
			}
		});
	}

	var assignedCourse = function(courseID,actionType,siteID) {
		
		$.ajax({
 			url      :   webroot+"containers/schoolsCourse/assignedCourse",
 			method   :   "POST",
 			data:{courseID:courseID,actionType:actionType,siteID:siteID,csrf_tel_library:csrf_tel_library},
 			dataType : "json", 				
 			success: function (result) { 
 				CommanJS.getDisplayMessgae(result.code,result.message);
 				selectedCourse();
 				getViewInnerTemplate('section_list',siteID);

 			}
 		});
	}

	var selectedCourse = function(){
		$.ajax({
 			url      : webroot+"containers/schoolsCourse/selectedCourse",
 			method   : "POST",
 			data     : {'siteID':siteID,'csrf_tel_library':csrf_tel_library},
 			dataType : "json", 				
 			success: function (result) { 
 				var option = [];
				option.push('<option value="">Please select</option>');
				$.each(result.data, function(key, value) {
					option.push('<option value="'+ value.id +'">'+ value.name +'</option>');
				});
				$('#course_section').html(option);
 			}
 		});
	}

	var getDatePicker = function() {

		var dateToday = new Date();
		var dateFormat = "mm/dd/yy",
		from = $( "#section_start_date" )
		.datepicker({
			changeYear: true,
			changeMonth: true,
			numberOfMonths: 1,
			minDate: dateToday
		})
		.on( "change", function() {
			to.datepicker( "option", "minDate", getDate( this ) );
		}),
		to = $( "#section_end_date" ).datepicker({
			changeYear: true,
			changeMonth: true,
			numberOfMonths: 1,
			minDate: dateToday
		})
		.on( "change", function() {
			from.datepicker( "option", "maxDate", getDate( this ) );
		});

		function getDate( element ) {
			var date;
			try {
				date = $.datepicker.parseDate( dateFormat, element.value );
			} catch( error ) {
				date = null;
			}
			return date;
		}
	}

	
	var multiselectCourse = function(){

		$('#multi-select').multiSelect({

			selectableHeader: "<div class='all-coures'>"+langs.All_courses+"</div><input type='text' class='form-control' style='margin-bottom: 10px;'  autocomplete='off' placeholder='Search course name'>",
			selectionHeader: "<div class='enrolled-courses'>"+langs.Enrolled_Courses+"</div><input type='text' class='form-control' style='margin-bottom: 10px;' autocomplete='off' placeholder='Search course name'>",
			afterInit: function(ms){
				var that = this,
				$selectableSearch = that.$selectableUl.prev(),
				$selectionSearch = that.$selectionUl.prev(),
				selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
				selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

				that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
				.on('keydown', function(e){
					if (e.which === 40){
						that.$selectableUl.focus();
						return false;
					}
				});

				that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
				.on('keydown', function(e){
					if (e.which == 40){
						that.$selectionUl.focus();
						return false;
					}
				});
			},
			afterSelect: function(values){
				this.qs1.cache();
				this.qs2.cache();
				assignedCourse(values[0],'add',siteID);
				//getViewInnerTemplate('section_list',siteID);

			},
			afterDeselect: function(values){
				this.qs1.cache();
				this.qs2.cache();
				assignedCourse(values[0],'remove',siteID);
				//getViewInnerTemplate('section_list',siteID);

			}    
		});
	}

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID;
		csrf_tel_library = dataVariable.csrf_tel_library;
		langs= dataVariable.langs;
		jQuery.validator.addMethod("noSpace", function(value, element) { 
			return value == '' || value.trim().length != 0;  
		}, "Space is not allow");

		createSectionFormValidator();
		multiselectCourse();
		getDatePicker();
		getViewInnerTemplate('section_list',siteID);
		
		$('.course_next').on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#courses").removeClass("active"); 				
			$("#metadata").addClass("active");
			$('.metadata').addClass('active');
			getViewTemplate('metadata',siteID);
		});

		$(".back_to_visual").on('click', function(){
			$("#tabslist li").removeClass("active");
			$("#courses").removeClass("active");
			$("#visuals").addClass("active");
			$('.visuals').addClass('active');
			getViewTemplate('visuals',siteID);
		});
		
	}		

	return {
		init:init
	}	

}();