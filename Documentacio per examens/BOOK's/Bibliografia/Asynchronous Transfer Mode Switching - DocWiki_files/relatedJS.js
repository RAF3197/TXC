var restrictResultAt = 4;
var UNANSWERED_ICON = "../images/unanswered_question.png";
var ANSWERED_ICON = "../images/answered_question.png";
var defaultSearchParam;
var defaultSortField = "22";
var defaultSortOrder = "0";
var defaultStartIndex = "0";
var defaultAfterDate = "1969-12-31T17:00:00-07:00";
var defaultBeforeDate = "2222-12-31T17:00:00-07:00";
var imagesRequested = 0;
var imagesDownloaded = 0;

function updateSearch(_searchParam, _sortField, _sortOrder, _startIndex, _afterDate, _beforeDate){
        if(_searchParam) {
                defaultSearchParam = _searchParam;
        }
        if(_sortField) {
                defaultSortField = _sortField;
        }
        if(_sortOrder) {
                defaultSortOrder = _sortOrder;
        }
        if(_startIndex) {
                defaultStartIndex = _startIndex;
        }
        if(_afterDate) {
                defaultAfterDate = _afterDate;
        }
        if(_beforeDate) {
                defaultBeforeDate = _beforeDate;
        }
         showResults(restrictResultAt);

}

var failureDiscFunction = function(xhr, textstatus){
        $("#ajaxLoaderImg").css("display","none");
        $('#cscResults').children().remove();
        $('#cscResults').append("<span> Sorry you have encountered a temporary system error.</span>");
        imagesDownloaded = 0;
}

function showResults(value){
        $("#ajaxLoaderImg").css("display","block");

        var SAMPLE_DATA = {targetService:"extendedSearch",
                postData:"<search><PortkeySearchQuery><afterDate>" + defaultAfterDate + "</afterDate><beforeDate>" + defaultBeforeDate + "</beforeDate><queryString>" + defaultSearchParam + "</queryString><sortField>" + defaultSortField + "</sortField><sortOrder>" + defaultSortOrder + "</sortOrder></PortkeySearchQuery><contentTypes>disc</contentTypes><startIndex>" + defaultStartIndex + "</startIndex><numResults>" + value +"</numResults></search>"
        };
        restrictResultAt = value;
        makeAjaxCall(SAMPLE_DATA, successSearchResultFunction, failureDiscFunction);
}

var successSearchResultFunction = function(_data){
        $("#ajaxLoaderImg").css("display","block");
        var data = _data;
        if (!data || !data.searchResponse || !data.searchResponse.result || data.searchResponse.result.length == 0){
                $('#cscResults').children().remove();
                $('#ajaxLoaderImg').css('display','none');
                imagesDownloaded = 0;
//              $('#cscResults').append("<span> " + getValueByKey("sorryNotAvailable")  + "</span>");
$('#cscResults').append("<span> Sorry there are no discussions available now.</span>");

        }else{
                //$("#ajaxLoaderImg").css("display","none");
                $('#cscResults').children().remove();
                var csResults = data.searchResponse.result;
                var i = 0;
                imagesRequested = restrictResultAt;
                /****
                 * We are tracking the parent web site using "referrer" which hosts the widget.
                 * This can be used for the web site which i-frames the widget.
                 * This is only applicable to Search Widget so far.
                 */
                var referrer = document.referrer;
                if(!referrer){
                    referrer = document.location;
                }
                $.each(csResults,function(){
                        var cutSubject = cutString(stripHTMLTag(this.subject), 54);
                        cutSubject = cutSubject ? cutSubject : "";
                        var cutCommunityName = cutString(this.communityName, 38);
                        $('#cscResults').append("<div style='float:left;padding-top: 1px;'><a href=Javascript:newPopup('"+ this.url +"?utm_content="+ referrer +"&utm_source=docwiki&utm_medium=Related-Discussions-Widget&#comment-"+ this.ID +"');><img src='" + UNANSWERED_ICON + "' border='0' style=' margin: 2px 5px 0 -4px;' id='img" + this['threadID'] + "'/></a></div>");
                        $('#cscResults').append("<div class='content_results'><div class='results_header'><a href=Javascript:newPopup('"+ this.url +"?utm_content="+ referrer +"&utm_source=docwiki&utm_medium=Related-Discussions-Widget&#comment-"+ this.ID +"');>"+cutSubject+"</a></div><div class='communitylisting'> in "+ cutCommunityName +"</div></div>");
                        getthreads(this['threadID']);

                        if(i == (restrictResultAt - 1)){
                                return false;
                        }
                        i++;
                });
        }
}

var successGetRelatedDiscussionFunction = function(threaddata){
        var tdata = threaddata;
        var threadResults = tdata.forumThread.result;
        var imgurl = "";
        if(threadResults.showAsAnswered == false){
                imgurl = UNANSWERED_ICON;
        }else{
                imgurl = ANSWERED_ICON;
        }
        $("#img"+threadResults.ID).attr("src", imgurl);
        imagesDownloaded++;
                        if(imagesDownloaded == imagesRequested){
                                imagesDownloaded = 0;
                                $('#ajaxLoaderImg').css('display','none');
                                return false;
                        }
        //console.log(("showAsAnswered-")+threadResults.showAsAnswered + '-has thread ID-'+(threadResults.ID));
};

function getthreads(id){
        makeAjaxCall({targetService:"getForumThread", extraPath: id}, successGetRelatedDiscussionFunction, failureDiscFunction);
        $('#cscResultserr').empty();
        imagesDownloaded = 0;
}

configDependent.push(function () {
        $("#ajaxLoaderImg").css("display","block");
defaultSearchParam = $('title', top.document).text();
        $('#select').change();
        document.getElementsByTagName("select")[0].value = document.getElementsByTagName("option")[0].value;
        $('#cscResults').html('');
});

