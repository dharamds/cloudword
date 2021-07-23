var SchoolCreate = function (){
	var webroot,siteID;
	

	var init = function (dataVariable) {
		webroot = dataVariable.ajax_call_root;
		siteID  = dataVariable.siteID; 
		getViewTemplate('identity',siteID);
	}		

	return {
		init:init
	}	

}();