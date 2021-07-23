var CommanJS = function() {

    var ajax_call_root, notification, csrf_tel_library;
    var azure_media_path='https://rescueworld.blob.core.windows.net/academy-uploads/';

    var getPopupWithExternalView = function(title, url, callbackRequest, widthRatio, uploadLocation) {
        $.confirm({
            title: title,
            content: 'url:' + url + '/' + callbackRequest + '/' + uploadLocation,
            buttons: {
                yes: {
                    isHidden: true, // hide the button
                    btnClass: 'btn btn-primary btn-md'
                },
                cancel: {
                    isHidden: false, // hide the button
                    btnClass: 'btn btn-default btn-md'
                }
            },
            onContentReady: function() {
                var self = this;
            },
            columnClass: (widthRatio ? widthRatio : "medium"),
        });
    }

    var getDisplayMessgae = function(code, message) {
        var icon, clasName;

        switch (code) {
            case 200:
                icon = 'fa fa-exclamation-circle';
                clasName = 'success success-noty col-md-3';
                break;

            default:
                icon = 'fa fa-exclamation-triangle';
                clasName = 'error error-noty col-md-3';
        }

        $.notify({
            title: '<b><i class="' + icon + '"></i> Notification</b><br>',
            message: message,
        }, {
            type: clasName,
            allow_dismiss: true,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1431,
            delay: 5000,
            timer: 1000,
            animate: {
                enter: 'animated bounceInDown',
                exit: 'animated bounceOutUp'
            }
        });
        return true;
    }

    var getNotificationMessage = function(message, classNames) {
        $.notify({

            icon: 'glyphicon glyphicon-info-sign',
            title: '<b><i class="ti ti-check"></i> Notification</b><br>',
            message: message,
        }, {

            type: classNames + " col-md-3",
            allow_dismiss: true,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1431,
            delay: 5000,
            timer: 1000,
            animate: {
                enter: 'animated bounceInDown',
                exit: 'animated bounceOutUp'
            }
        });
    }

    var getTagSection = function(referencetype, viewEle, inputEle, referenceID, reference_sub_type) {
        $.ajax({
            url: ajax_call_root + "containers/schoolsMetadatas/get_tag_section",
            method: "POST",
            data: { 'viewEle': viewEle, 'referencetype': referencetype, 'inputEle': inputEle, 'referenceID': referenceID, 'reference_sub_type': reference_sub_type, 'csrf_tel_library': csrf_tel_library },
            dataType: "html",
            success: function(result) {
                $("#" + viewEle).html(result);
            }
        });
    }

    var getCatSection = function(referencetype, viewEle, inputEle, referenceID, reference_sub_type) {

        $.ajax({
            url: ajax_call_root + "containers/schoolsMetadatas/get_cat_section",
            method: "POST",
            data: { 'viewEle': viewEle, 'referencetype': referencetype, 'inputEle': inputEle, 'referenceID': referenceID, 'reference_sub_type': reference_sub_type, 'csrf_tel_library': csrf_tel_library },
            dataType: "html",
            success: function(result) {

                //$("#" + viewEle).html(result);
                $("#" + viewEle).html(result);
            }
        });
    }

    var get_metadata_options = function(selector, type, sub_type, selector_div) {
        var selector = selector;
        $.ajax({
            url: ajax_call_root + "containers/schoolsMetadatas/get_cat_filter",
            method: "POST",
            data: { 'selector': selector, 'type': type, 'sub_type': sub_type, 'selector_div': selector_div, 'csrf_tel_library': csrf_tel_library },
            dataType: "html",
            success: function(result) {
                $("#" + selector_div).html(result);
            }
        });
    }



    var unserialize = function(data) {
        // Takes a string representation of variable and recreates it    
        //   
        // version: 810.114  
        // discuss at: http://phpjs.org/functions/unserialize  
        // +     original by: Arpad Ray (mailto:arpad@php.net)  
        // +     improved by: Pedro Tainha (http://www.pedrotainha.com)  
        // +     bugfixed by: dptr1988  
        // +      revised by: d3x  
        // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)  
        // %            note: We feel the main purpose of this function should be to ease the transport of data between php & js  
        // %            note: Aiming for PHP-compatibility, we have to translate objects to arrays   
        // *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');  
        // *       returns 1: ['Kevin', 'van', 'Zonneveld']  
        // *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');  
        // *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}  

        var error = function(type, msg, filename, line) { throw new window[type](msg, filename, line); };
        var read_until = function(data, offset, stopchr) {
            var buf = [];
            var chr = data.slice(offset, offset + 1);
            var i = 2;
            while (chr != stopchr) {
                if ((i + offset) > data.length) {
                    error('Error', 'Invalid');
                }
                buf.push(chr);
                chr = data.slice(offset + (i - 1), offset + i);
                i += 1;
            }
            return [buf.length, buf.join('')];
        };
        var read_chrs = function(data, offset, length) {
            buf = [];
            for (var i = 0; i < length; i++) {
                var chr = data.slice(offset + (i - 1), offset + i);
                buf.push(chr);
            }
            return [buf.length, buf.join('')];
        };
        var _unserialize = function(data, offset) {
            if (!offset) offset = 0;
            var buf = [];
            var dtype = (data.slice(offset, offset + 1)).toLowerCase();

            var dataoffset = offset + 2;
            var typeconvert = new Function('x', 'return x');
            var chrs = 0;
            var datalength = 0;

            switch (dtype) {
                case "i":
                    typeconvert = new Function('x', 'return parseInt(x)');
                    var readData = read_until(data, dataoffset, ';');
                    var chrs = readData[0];
                    var readdata = readData[1];
                    dataoffset += chrs + 1;
                    break;
                case "b":
                    typeconvert = new Function('x', 'return (parseInt(x) == 1)');
                    var readData = read_until(data, dataoffset, ';');
                    var chrs = readData[0];
                    var readdata = readData[1];
                    dataoffset += chrs + 1;
                    break;
                case "d":
                    typeconvert = new Function('x', 'return parseFloat(x)');
                    var readData = read_until(data, dataoffset, ';');
                    var chrs = readData[0];
                    var readdata = readData[1];
                    dataoffset += chrs + 1;
                    break;
                case "n":
                    readdata = null;
                    break;
                case "s":
                    var ccount = read_until(data, dataoffset, ':');
                    var chrs = ccount[0];
                    var stringlength = ccount[1];
                    dataoffset += chrs + 2;

                    var readData = read_chrs(data, dataoffset + 1, parseInt(stringlength));
                    var chrs = readData[0];
                    var readdata = readData[1];
                    dataoffset += chrs + 2;
                    if (chrs != parseInt(stringlength) && chrs != readdata.length) {
                        error('SyntaxError', 'String length mismatch');
                    }
                    break;
                case "a":
                    var readdata = {};

                    var keyandchrs = read_until(data, dataoffset, ':');
                    var chrs = keyandchrs[0];
                    var keys = keyandchrs[1];
                    dataoffset += chrs + 2;

                    for (var i = 0; i < parseInt(keys); i++) {
                        var kprops = _unserialize(data, dataoffset);
                        var kchrs = kprops[1];
                        var key = kprops[2];
                        dataoffset += kchrs;

                        var vprops = _unserialize(data, dataoffset);
                        var vchrs = vprops[1];
                        var value = vprops[2];
                        dataoffset += vchrs;

                        readdata[key] = value;
                    }

                    dataoffset += 1;
                    break;
                default:
                    error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
                    break;
            }
            return [dtype, dataoffset - offset, typeconvert(readdata)];
        };
        return _unserialize(data, 0)[2];
    }

    /*var send_notification_form_validation = function () {
    	$("#send_notification").validate({
    		ignore: [],
    		rules: {
    			msg: {
    				required: true
    			},
    		},
    		messages: {
    			msg: "Please enter message."
    		},
    		errorPlacement: function (error, $elem) {
    			if ($elem.is('textarea')) {
    				$elem.insertAfter($elem.next('div'));
    			}
    			error.insertAfter($elem);
    		},
    		submitHandler: function (form) {
    			$('#email_submit_button').hide();
    			$.ajax({
    				url: ajax_call_root + "users/send_notification",
    				method: "POST",
    				data: $('form#send_notification').serialize(),
    				dataType: "json",

    				complete: function (xhr, status) {
    					$('#notification_modal').modal('hide');
    					getDisplayMessgae(200, 'Email sent successfully.');
    					$('#email_submit_button').show();
    				},
    				success: function (data) {
    					//console.log(data);

    				}
    			});
    			return false;
    		}

    	});
    };*/
    /*
    var send_message_form_validation = function () {
    	$("#send_message").validate({
    		ignore: [],
    		rules: {
    			msg: {
    				required: true
    			},
    		},
    		messages: {
    			msg: "Please enter message."
    		},
    		errorPlacement: function (error, $elem) {
    			if ($elem.is('textarea')) {
    				$elem.insertAfter($elem.next('div'));
    			}
    			error.insertAfter($elem);
    		},
    		submitHandler: function (form) {
    			$('#message_submit_button').hide();
    			$.ajax({
    				url: ajax_call_root + "users/send_message",
    				method: "POST",
    				data: $('form#send_message').serialize(),
    				dataType: "json",

    				complete: function (xhr, status) {
    					$('#message_modal').modal('hide');
    					getDisplayMessgae(200, 'Message sent successfully.');
    					$('#message_submit_button').show();
    				},
    				success: function (data) {
    					//console.log(data);

    				}
    			});
    			return false;
    		}
    	});
    };
    */
    var choose_media_image_file = function(call_back, instanceName) {

        $("#gallery_images_popup").modal();
        $.ajax({
            url: ajax_call_root + "general_modules/Gallery_Images/gallery_images_popup",
            method: "POST",
            data: { 'call_back': call_back, 'instanceName': instanceName, 'csrf_tel_library': csrf_tel_library },
            success: function(data) {
                $('#popup_img_div').html(data);
            }
        });
    }

    var getMetaCallBack = function(elementID) {
        var system_cat_selected = [];
        $('.' + elementID + '_check_fil:checkbox:checked').each(function(i) {
            if (isNaN($(this).attr('data-input-name'))) {
                system_cat_selected.push($(this).val());
            } else {
                system_cat_selected.push($(this).attr('data-input-name'));
            }

        });
        return system_cat_selected;
    }

    var getImagebox = function(file_name, from, cls = '') {
       
        var extension = file_name.substr((file_name.lastIndexOf('.') + 1)).toLowerCase();
        data = '';
        if (cls == '') {
            cls = 'f45 text-center mt-3';
        }
        switch (extension) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                if (from == 'datatable') {
                    //data += '<div class=""><img class="img-responsive img-rounded" src="' + ajax_call_root + '/uploads/media_images/thumbnails/' + file_name + '"></div>';
                    data += '<img class="img-responsive img-rounded" src="' + azure_media_path + 'media_images/thumbnails/' + file_name + '">';
                } else {
                    //data += '<div class=""><img  class="img-responsive img-rounded" src="' + file_name + '"></div>';
                    data += '<img  class="img-responsive img-rounded" src="' + file_name + '">';
                }
                break;
            case 'pdf':
                data += "<div id='add_class_div' class='flaticon-pdf " + cls + "' ></div>";
                break;
            case 'txt':
            case 'text':
                data += "<div id='add_class_div' class='flaticon-txt " + cls + "' ></div>";
                break;
            case 'csv':
                data += "<div id='add_class_div' class='flaticon-csv " + cls + "' ></div>";
                break;
            case 'mp3':
            case 'mb':
                data += "<div id='add_class_div' class='flaticon-mp3 " + cls + "' ></div>";
                break;
            case 'doc':
            case 'docx':
                data += "<div id='add_class_div' class='flaticon-doc f45 " + cls + "' ></div>";
                break;
            case 'xls':
            case 'xlsx':
                data += "<div id='add_class_div' class='flaticon-xls f45 " + cls + "' ></div>";
                break;
            case 'mp4':
                data += "<div id='add_class_div' class='f45 media-thumb-img " + cls + "' ><img src='" + ajax_call_root + "/public/assets/img/mp4.png'></div>";
                break;
            default:
                data += "<div id='add_class_div'  class=''><img class='img-responsive img-rounded' src='" + ajax_call_root + "/public/assets/img/logo-bg.png'></div>";
        }
        $('#add_class_div').addClass(cls)
        return data;
    }

    var init = function(dataVariable1) {

        //console.log(dataVariable1);

        ajax_call_root = dataVariable1.ajax_call_root;
        notification = dataVariable1.notification;
        csrf_tel_library = dataVariable1.get_csrf_hash;

        if (notification.success) {
            getNotificationMessage(notification.success, 'success success-noty');
        }
        if (notification.error) {
            getNotificationMessage(notification.error, 'danger error-noty');
        }
        //$('[data-toggle="tooltip"]').tooltip();
        //send_notification_form_validation();
        //send_message_form_validation();

    }

    return {
        init: init,
        getPopupWithExternalView: getPopupWithExternalView,
        getTagSection: getTagSection,
        getCatSection: getCatSection,
        getDisplayMessgae: getDisplayMessgae,
        get_metadata_options: get_metadata_options,
        unserialize: unserialize,
        //send_notification_form_validation: send_notification_form_validation,
        //send_message_form_validation: send_message_form_validation,
        choose_media_image_file: choose_media_image_file,
        getMetaCallBack: getMetaCallBack,
        getImagebox: getImagebox

    }

}();