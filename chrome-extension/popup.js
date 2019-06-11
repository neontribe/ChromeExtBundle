// This callback function is called when the content script has been 
// injected and returned its results
function onPageDetailsReceived(pageDetails)  { 
    chrome.storage.sync.get({
        kimaiurl: "",
        popupwidth: 800,
        popupheight: 1000
    }, function(items) {
        $("#content").width = items.popupwidth;
        $("#content").height = items.popupheight;

        // Is this github or trello?
        var location = new URL(pageDetails.url);
        var hostname = location.hostname;
        var pathname = location.pathname;
        var path = pathname.split("/");
        var project = false;
        var issue = false;

        if (hostname == "github.com") {
            project = path[1] + '-' + path[2];
            issue = path.join("-");
        }
        else if (hostname == "trello.com") {
            // get boardname and issue id
        }
        else {
            // It's not github or trello show kimai front page and exit early
            $("#content").attr("src", items.kimaiurl);
            console.log(items.kimaiurl);
            return;
        }

        var url = items.kimaiurl + "/en/chrome-ext/" + encodeURIComponent(project) + "/" + encodeURIComponent(issue);
        console.log(url);
        $("#content").attr("src", url);
    });
} 

$( document ).ready(function() {
    chrome.runtime.getBackgroundPage(function(eventPage) {
        // Call the getPageInfo function in the event page, passing in 
        // our onPageDetailsReceived function as the callback. This injects 
        // content.js into the current tab's HTML
        eventPage.getPageDetails(onPageDetailsReceived);
    });
});
