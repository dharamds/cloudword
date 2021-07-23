var CatSection = function (){
	
	var webroot,post_data, iclick = 0,csrf_tel_library;
	categoriesIDSStatus=[];
	
	var saveCategory = function(categoriesIDS,actionType){

		
		var hidden  = $('.li-hidden').length;
		var visible = $('.li-visible').length;
		$.ajax({
			url      :   webroot+"containers/schoolsMetadatas/saveAssignCategories",
			method   :   "POST",
			data     :  {'categoriesIDS':categoriesIDS,'post_data':post_data,'actionType':actionType,'csrf_tel_library':csrf_tel_library},
			dataType : "json", 				
			success: function (result) {
				if(categoriesIDSStatus.length) {
						CommanJS.getDisplayMessgae(result.code,result.message);			
					
				}
			}
		});
	}
	
	var __getCheckAllSelect = function(element,elementView){
		var numberOfChecked = $("."+element+':checkbox:checked').length;
		var totalCheckboxes = $("."+element+':checkbox').length;
		//$("#"+elementView+' .search_categories').val(numberOfChecked+' selected');
		if(numberOfChecked > 0){
			$("#"+elementView+' .search_categories').attr("placeholder", numberOfChecked+' selected');
		}else{
			$("#"+elementView+' .search_categories').attr("placeholder", 'Search category');
		}
		if(numberOfChecked == totalCheckboxes ){
			 $("#"+element).prop("checked",true);
		}
	}

	var __getCollectValue = function(element,actionType){
		var categoriesIDS = [];
		$('.'+post_data.inputEle+':checkbox:checked').each(function(i) {
			categoriesIDS.push($(this).val());
		});
		categoriesIDSStatus=[];

		$('.'+post_data.inputEle).each(function(i) {
			console.log($(this).parent().parent().hasClass("li-hidden"));
			if(!$(this).parent().parent().hasClass("li-hidden")) {
				categoriesIDSStatus.push($(this).val());
			}
		});
	    saveCategory(categoriesIDS,actionType);
	}

	var __getCheckOrUncheck = function(element){
		if(element){
			return 'save';
		}else{
			return 'remove';
		}
	}

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		post_data = dataVariable.post_data;
		csrf_tel_library = dataVariable.csrf_tel_library;
		//console.log(dataVariable);
		__getCheckAllSelect(post_data.inputEle,post_data.viewEle);
		$("#"+post_data.viewEle+' .search_categories').on('keyup', function () {
			var value = this.value.toLowerCase();
			$("#"+post_data.viewEle+' ul label').filter(function() {
				//$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				if($(this).text().toLowerCase().indexOf(value) > -1){
					$(this).parent('li').removeClass('li-hidden').addClass('li-visible');
				}else{
					$(this).parent('li').removeClass('li-visible').addClass('li-hidden');
				}
				var numv = $(this).parents("ul").children('li.li-visible').length;
				
				if(numv == 0){
					$("#"+post_data.viewEle+' .no-match').show();
					$("#"+post_data.viewEle+' .no-match span').text('"'+value+'"');
				}else{
					$("#"+post_data.viewEle+' .no-match').hide();
				}
		    });
		});
		
		$(document).on('click','#'+post_data.inputEle,function () {
			//$("#"+post_data.inputEle).click(function () {
			
			$("."+post_data.inputEle).prop('checked', $(this).prop('checked'));

			__getCheckAllSelect(post_data.inputEle,post_data.viewEle);
			__getCollectValue(post_data.inputEle,__getCheckOrUncheck($(this).prop("checked")));
        });

        $("."+post_data.inputEle).on('change',function(){
            if (!$(this).prop("checked")){
                $("#"+post_data.inputEle).prop("checked",false);
            }
            __getCheckAllSelect(post_data.inputEle,post_data.viewEle);
            __getCollectValue(post_data.inputEle,__getCheckOrUncheck($(this).prop("checked")));
        });
		
	}		

	return {
		init:init
	}	

}();