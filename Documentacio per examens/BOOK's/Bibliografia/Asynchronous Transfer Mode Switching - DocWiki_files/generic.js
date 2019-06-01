function newPopup(url) {
	popupWindow = window.open(
		url,'popUpWindow','height=700,width=780,right=20,top=20,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no,status=no')
}

var extraHeaders = [];
var ajaxObject = null;
function makeAjaxCallWithDataType(REQUEST_PARAMETER_OBJ, _dataType, successFunction, failureFunction, completeFunction){
	
	 ajaxObject = $.ajax({
		async: true,
		type: 'GET',
		url: '../proxy.php',
		dataType: _dataType,
		data: REQUEST_PARAMETER_OBJ,
		beforeSend: function(xhr){
			xhr.setRequestHeader("outputFormat", "JSON");
			xhr.setRequestHeader("fromPartyId", "portkey");
			if(extraHeaders) {
				for(var i = 0; i < extraHeaders.length; i++) {
					xhr.setRequestHeader(extraHeaders.name, extraHeaders.value);
				}
			}
		},
		complete: function(xhr){
			
			if(completeFunction) {
				completeFunction(xhr);
			}
		},
		success: function(data){
			if(successFunction) {
				successFunction(data);
			}
		},
		error: function(xhr, textstatus){
			if(failureFunction) {
			failureFunction(xhr, textstatus);
			} 
		}
	
	});
}

function makeAjaxCall(REQUEST_PARAMETER_OBJ, successFunction, failureFunction){
	makeAjaxCallWithDataType(REQUEST_PARAMETER_OBJ,"json", successFunction, failureFunction);

}	

//function to add elipsis after a certain number of characters
function cutString(string, size){
	if(string){
		if(string.length >size){
			var newString = string.substring(0, size -4);
			return newString + " ...";
		}             
		return string ;
	}
}

//fc. that strips the tag of an element
function stripHTMLTag(html){
   var tmp = document.createElement("DIV");
   tmp.innerHTML = html;
   return tmp.textContent||tmp.innerText;
}

//function to keep the tooltio open for clickable link
jQuery(function($){
	$('#tooltip').click(function(e){
		$("#tooltipDiv").show();
		if(e){
			e.stopPropagation();			
		}else{
			 e.cancelBubble = true;
		}
	});
	
	$(document).click(function(){
		$("#tooltipDiv").hide();	   
	});
});

//function for highlighting search query
function highlight(value, term){
	return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<span class='highlight'>$1</span>");
}

//function to transform the date as required into string
function dateToTimeAgo(dateString){
	
	var oldDate = new Date(dateString);
	
	if (isNaN(oldDate)){
		oldDate = new Date(dateString.substring(0, dateString.indexOf("T")).replace(/-/g,'/'));
	}
	
	var timeAgoDateText = $.timeago(dateString);
		
	if (timeAgoDateText.indexOf("year") != -1) {
		
		var yearComponent = timeAgoDateText.substring(0, timeAgoDateText.indexOf(" "));
		oldDate.setFullYear(oldDate.getFullYear() + parseInt(yearComponent, 10));
		
		var monthsAgoText = $.timeago(oldDate);

		if (monthsAgoText.indexOf("0") == -1 && monthsAgoText.indexOf("day") == -1) {
			timeAgoDateText = timeAgoDateText.substring(0, timeAgoDateText.length - 4) + " and " + monthsAgoText;
		}
	}	
	return timeAgoDateText;	
}


var configDependent = [];

//generic function to get the values from the urlconfig.json file
function makeJsonGetCall(_url, datacache) {
	$.getJSON(_url, function(_data) {
		datacache['data'] = _data;
		
		for(var i =0; i < configDependent.length; i++) {
			configDependent[i]();
		}
		
	});
}

configDependent.push(function () {
	$('#widgetTitle').html(cutString(getConfigParam('wdgtTitle'), 115));
		
		if(getConfigParam('wdgtTitle').length > 56){
			$('#searchHeader').css("height","45px");
		}
		
		$('#widgetTitleRelatedDisc').html(getConfigParam('wdgtTitleRltdDisc'));
});

var urlConfigData = {}; 

$(document).ready(function() {
   // put all your jQuery goodness in here.
	makeJsonGetCall('../urlconfig.json', urlConfigData);
 });

//general function to get the value of the added variable in the urlconfig.json
function getConfigParam(key) {
	if(urlConfigData && urlConfigData['data'] && urlConfigData['data'][key]) {
		return urlConfigData['data'][key];
	} else {
		return null;
	}
}

//generic function to be able to add/change in the url the community ID
function gup(name){
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( window.location.href );
	if( results == null )
		return "";
	else
		return results[1];
}