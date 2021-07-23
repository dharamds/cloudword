/* FancyMe Tags Plugin
 * By Pamela Rodriguez
 * Free to use
 * Link backs are appreciated :)
 * www.thepamrdz.com
 */

(function($){  
  
$.fn.fancymetags = function(params){
    params = $.extend( {
	  theme: 'default', 
	}, params);  
	var main = $(this);
	main.addClass(params.theme);
	main.addClass("fancyme-tags");
	main.html("<div class='input-field'><input type='text' class='form-control' placeholder='Add tag and press Enter' /><input type='button' value='add'/></div><div id='shownlist'></div><div id='hiddenlist'></div>");
	
	function getTags(){//Gets array of existing tags
		var alltags = new Array();
		var i = 0;
		$(".fancyme-tags #shownlist span").each(function( index ) {
  			alltags[i] = $(this).html().substr(0,$(this).html().length - 20);
			i ++;
		});	
		return alltags;	
	};
	
	function setTags(){//Gets string of existing tags separated by commas
		var texttags = getTags();
		var finaltext = "";
		for(var i=0; i<texttags.length; i++){
			if(finaltext == ""){
				finaltext = texttags[i];	
			}
			else{
				finaltext = finaltext + "," + texttags[i];
			}
		}
		return finaltext;
	}
	
	function addTags(){
		var tags = $(".fancyme-tags input[type=text]").val().trim();
		var listnews = "";
		var newtags = "";
		var existenttags = getTags();
		//checks if a comma was left alone at the end and removes it
		if(tags.substr(tags.length - 1) == ","){ 
			tags = tags.substr(0, tags.length -1);	
		}
		//checks if a comma was left alone at the beginning and removes it
		if(tags.substr(0,1) == ","){
			tags = tags.substr(1, tags.length);	
		}
		//if more than one tag was added
		if(tags.indexOf(",") !== -1){
			var artags = tags.split(',');	
			for(var i=0; i<artags.length; i++){
				if((artags[i].trim() !== "")&&($.inArray(artags[i].trim(), existenttags) == -1)){
					listnews = listnews + '<span>'+artags[i].trim()+' <a href="#" class="fa fa-times"></a></span>';
					existenttags.push(artags[i].trim());	
				}
			}
		}
		//else if just one tag was added
		else{
			if($.inArray(tags, existenttags) == -1){
				listnews = '<span>'+tags+' <a href="#" class="fa fa-times"></a></span>';	
			}
		}
		//adds everything to the html and clears the value of the textfield
		$(".fancyme-tags #shownlist").append(listnews);
		$(".fancyme-tags input[type=text]").val("");
		$(".fancyme-tags #hiddenlist").html(setTags());
	};
	//event listeners for button and for the enter keypress
	$(".fancyme-tags input[type=button]").click(addTags);
	$(".fancyme-tags input[type=text]").keypress(function(e){
        if(e.which == 13){
            addTags();
        } 
    });
	//erase event binding for the Xs in each tag
	$(document).on('click','.fancyme-tags #shownlist span a', function(){
		event.preventDefault();
		var btn = $(this);
		var hiddens = $(".fancyme-tags #hiddenlist").text();
		var tag = btn.parent().html().toString();
		tag = tag.substr(0,tag.length-20);
		btn.parent().remove();
		$(".fancyme-tags #hiddenlist").html(setTags());
	});
};  
  
})(jQuery); 