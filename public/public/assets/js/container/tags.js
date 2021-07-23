var SchoolMetadata = function (){
	
	var webroot,post_data;
	

	var getAssignedTags = function(type) {
		var appendText;	
		$('.badgeTag').html('');	
		$.ajax({
 			url      :   webroot+"containers/schoolsMetadatas/getAssignedTag",
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
			url      :   webroot+"containers/schoolsMetadatas/removeAssignTag",
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

	var getAutocompleteTags = function() {
		console.log(post_data.inputEle);
		$( "#"+post_data.inputEle ).autocomplete({
			source: function (request, response) {
				$.ajax({
					type: "POST",
					url: webroot+"containers/schoolsMetadatas/get_all_active_meta",
					data: request,
					success: response,
					dataType: 'json'
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				log( "Selected: " + ui.item.value + " aka " + ui.item.id );
			}
		});
	}

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		post_data = dataVariable.post_data;
		getAutocompleteTags();

		$('.institute_tag').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){	
		    	if(this.value == '') {
		    		getDisplayMessgae(400,"Please enter something");
		    		return false;
		    	}
		    	$.ajax({
		 			url      :   webroot+"containers/schoolsMetadatas/saveAssignTag",
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
	

		$(document).on('click','.fa-close', function(){
			removeAssignedTags($(this).attr('id'),this);			
		});
	}		

	return {
		init:init
	}	

}();