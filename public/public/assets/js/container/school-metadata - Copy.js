var SchoolMetadata = function (){
	
	var webroot,states;
	

	var getViewTemplate = function(selectorView) {		
		$( "#"+selectorView).load( webroot +"containers/schools/load_view/"+selectorView, function() {});
	}

	var submitMetaDataForm = function() {
		$.ajax({
 			url      :   webroot+"containers/schoolsmetadatas/saveMetadata",
 			method   :   "POST",
 			data     :  $('#setupmetadata').serialize(),
 			dataType : "json", 				
 			success: function (result) { 
 				/*getDisplayMessgae(result.code,result.message);	
 				$("#tabslist li").removeClass("active");
 				$("#contactInfo").removeClass("active");
 				$("#visuals").addClass("active");
 				$('.visuals').addClass('active');
 				getViewTemplate('visuals');	*/
 			}
 		});
	}


	var getTagSection = function(selectionEle,type,inputEle,referenceID){
		$.ajax({
 			url      :   webroot+"containers/schoolsmetadatas/get_tag_section",
 			method   :   "POST",
 			data     :  {'selectionEle':selectionEle,'inputEle':inputEle,'type':type,'referenceID':referenceID},
 			dataType : "html", 				
 			success: function (result) { 
 				$("#"+selectionEle).html(result);
 			}
 		});	
	}
	

	var getAssignedTags = function(type) {
		var appendText;	
		$('.badgeTag').html('');	
		$.ajax({
 			url      :   webroot+"containers/schoolsmetadatas/getAssignedTag",
 			method   :   "POST",
 			data     :  {'type':type},
 			dataType : "json", 				
 			success: function (result) { 
 				$.each(result.data, function (key, val) {
			       $('.badgeTag').append('<div class="tagItem">'+
                    				val.name+'<i class="i fa fa-close" id="'+val.id+'"></i>'+
                                  '</div>');
			    });
 			}
 		});	
	}
	

	
	var removeAssignedTags = function(id,element) {
		$.ajax({
			url      :   webroot+"containers/schoolsmetadatas/removeAssignTag",
			method   :   "POST",
			data     :  {'id':id},
			dataType : "json", 				
			success: function (result) { 
				getDisplayMessgae(result.code,result.message);
				if(result.code == 200)
				{
					$(element).parents('.tagItem').remove();
				}			
			}
		});
		return false;
	}


	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		
		getTagSection('institute_section','institution','institution_tag',18);

		Utility.animateContent();

		$('.institute_tag').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){	
		    	if(this.value == '') {
		    		getDisplayMessgae(400,"Please enter something");
		    		return false;
		    	}
		    	$.ajax({
		 			url      :   webroot+"containers/schoolsmetadatas/saveAssignTag",
		 			method   :   "POST",
		 			data     :  {'institute_tag':this.value,'type':1},
		 			dataType : "json", 				
		 			success: function (result) { 
		 				$('.institute_tag').val('');
		 				if(result.code == 200){
		 					$('.badgeTag').append('<div class="tagItem">'+
		 						result.name+'<i class="i fa fa-close" id="'+result.id+'"></i>'+
		 						'</div>');
		 				}				
		 			}
		 		});
		        return false;
		    }
		});
		getAssignedTags(5);

		$(document).on('click','.fa-close', function(){
			removeAssignedTags($(this).attr('id'),this);			
		});
	}		

	return {
		init:init,
		submitMetaDataForm:submitMetaDataForm

	}	

}();