
$(document).ready(function () {

    jQuery.validator.addMethod("noSpace", function (value, element, param) {
        return value.match(/^(?=.*\S).+$/);
    }, "No space please and don't leave it empty");


    CKEDITOR.replace('description', {
        toolbar: 'short',
    });

});



$('#user_form_validation').validate({// initialize the plugin
    rules: {
        name: {
            required: true,
            noSpace: true
        },

        body: {
            required: function (textarea) {
                CKEDITOR.instances[textarea.id].updateElement(); // update textarea
                var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
                return editorcontent.length === 0;
            }
        },

    },
    messages: {
        name: {
            required: "Please enter name"
        },
        body: {
            required: "Please enter certificate body"
        }
    }
});
