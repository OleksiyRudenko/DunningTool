/**
 * Created by Oleksiy Rudenko on 06/07/2016.
 */
/*
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
  $('#formSubscribe').submit(function() {
    postToGoogleForm();
    return false;
  });
});
*/

(function($, window, document) {
  // The $ is now locally scoped
  // Listen for the jQuery ready event on the document
  $(function() {
    // The DOM is ready!
    $('[data-toggle="tooltip"]').tooltip();
    /* $('#formSubscribe').submit(function() {
      postToGoogleForm();
      return false;
    }); */
    // email & tel protection
    /* $(function(){
      setTimeout(function(){
        var m = ['com','.','gmail','@','rudenko','.','oleksiy'].reverse().join('');
        $('#speakerEmail').prop('href','mailto'+':'+m).text(m);

        var thr = ['5758','440','50','+380'].reverse().join(' ');
        var txt = ['5758','440','(050)','+38'].reverse().join(' ');
        $('#speakerTel').prop('href','tel'+':'+thr).text(txt);
      },1000);
    }); */
    // bootstrap-datepicker
    $('#oldDate1').datepicker({
    // $('.datepicker').datepicker({
      format: 'yyyy-mm-dd'
    });
  });
  // The rest of the code goes here!
}(window.jQuery, window, document));