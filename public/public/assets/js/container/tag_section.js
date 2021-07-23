var TagSection = function () {

	var webroot, post_data, csrf_tel_library;

	var removeAssignedTags = function (id, element) {
		//console.log(webroot);
		$.ajax({
			url: webroot + "containers/schoolsMetadatas/removeAssignTag",
			method: "POST",
			data: {
				'id': id,
				'csrf_tel_library': csrf_tel_library
			},
			dataType: "json",
			success: function (result) {
				if (result.code == 200) {
					CommanJS.getDisplayMessgae(result.code, result.message);
					$(element).parents('.tagItem').remove();
				}
			}
		});
		return false;
	}

	var getAutocompleteTags = function () {
		$("#" + post_data.inputEle).autocomplete({
			source: function (request, response) {
				$.ajax({
					type: "POST",
					url: webroot + "containers/schoolsMetadatas/get_all_active_meta_tags",
					data: {
						"keyword": request,
						'sectionData': post_data,
						'csrf_tel_library': csrf_tel_library
					},
					dataType: 'json',
					success: response
				});
			},
			search: function () {
				$(this).addClass('loading');
			},
			minLength: 2,
			select: function (event, ui) {
				//console.log(ui.item.label);
				getSaveTags(ui.item.label, post_data);
				$('#' + post_data.inputEle).val(ui.item.label); // display the selected text
				//  $('#sistema_select_id').val(ui.item.value); // save selected id to hidden input
				return false;
			},
			response: function (event, ui) {
				$(this).removeClass('loading');
			}
		});
		$("#system_tags").autocomplete({
			appendTo: $("#system_tags").siblings(".autocompletepanel"),
		})
	}

	var getAssignedTags = function () {
		var appendText;
		$('#' + post_data.inputEle).val('');
		$.ajax({
			url: webroot + "containers/schoolsMetadatas/getAssignedTag",
			method: "POST",
			data: {
				'post_data': post_data,
				'csrf_tel_library': csrf_tel_library
			},
			dataType: "json",
			success: function (result) {
				$.each(result.data, function (key, val) {
					//console.log(val);
					$('.' + post_data.inputEle).append('<div class="tagItem">' +
						val.name + '<i class="i fa fa-close remove_tag" id="' + val.id + '" onclick="TagSection.removeAssignedTags(' + val.id + ',this)"></i>' +
						'</div>');
				});
			}
		});
	}

	var getSaveTags = function (tagName, post_data) {
		$.ajax({
			url: webroot + "containers/schoolsMetadatas/saveAssignTag",
			method: "POST",
			data: {
				'tag': tagName,
				'post_data': post_data,
				'csrf_tel_library': csrf_tel_library
			},
			dataType: "json",
			success: function (result) {
				$('#' + post_data.inputEle).val('');
				if (result.code == 200) {
					$('.' + post_data.inputEle).append('<div class="tagItem">' +
						result.name + '<i class="i fa fa-close remove_tag" id="' + result.id + '" onclick="TagSection.removeAssignedTags(' + result.id + ',this)"></i>' +
						'</div>');
				}
			}
		});
	}


	var init = function (dataVariable) {
		//console.log(dataVariable.ajax_call_root);
		webroot = dataVariable.ajax_call_root;
		post_data = dataVariable.post_data;
		csrf_tel_library = dataVariable.csrf_tel_library;
		getAutocompleteTags();
		getAssignedTags();

		$('#' + post_data.inputEle).keypress(function (event) {
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if (keycode == '13') {
				if (this.value.trim() == '') {
					CommanJS.getDisplayMessgae(400, "Please enter something");
					return false;
				}
				getSaveTags(this.value, post_data);
				return false;
			}
		});
	}

	return {
		init: init,
		removeAssignedTags: removeAssignedTags
	}

}();