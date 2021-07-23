$(window).load(function() 
{
    $("#demoskylo").trigger('click');

    $('.skylo .loader-box').contents().filter(function(){
        return this.nodeType === 3;
    }).remove();

});

function delete_row(id,url,table){
  var confirmbox = confirm("Do you want to delete this ?");
  if (confirmbox == true)
      window.location = url+"courses/polls/my_delete/"+id+'/'+table;
  else
      return false;
}

/*$("#change_pass").validate({
    rules: {
      password: "required",
      new_confirm: {
        equalTo: "#new"
      }
    },
});*/

// create new users page validation
$("#user_form_validation").validate({
    rules: {
       password : {
                  minlength : 8
              },
      confirm_password: {
        minlength : 8,
        equalTo: "#password"
      }
    },
});

// Add Category Form Validation
//$(".blog_settings").validate();

// Add Category Form Validation
$("#add_cat_validate").validate();


// create new group validation

$("#create_gp").validate();

// create new group validation

$("#social_config").validate();

 

$(function () {

	$(".panel-color-list>li>span").click(function(e) {
		$(".panel").attr('class','panel').addClass($(this).attr('data-style'));
	});
	
});


