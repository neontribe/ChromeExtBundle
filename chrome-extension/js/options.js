// Saves options to chrome.storage

$( document ).ready(function() {
    
    chrome.storage.sync.get({
      kimaiurl: ""
    }, function(items) {
      $('#kimaiurl').val(items.kimaiurl);
    });

    $("#save").on('click', function () {
      var kimaiurl = $('#kimaiurl').val();
    
        chrome.storage.sync.set({
          kimaiurl: kimaiurl
        }, function() {
          // Update status to let user know options were saved.
          $('#status').html('Options saved.');
          setTimeout(function() {
            $('#status').html('');
          }, 2750);
        });
      });
})
