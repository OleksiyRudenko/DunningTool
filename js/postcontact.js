/**
 * Created by Rudenko on 06/07/2016.
 */
// source: https://wiki.base22.com/pages/viewpage.action?pageId=72942000
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function postToGoogleForm(){
  var name = $('#inputName').val();
  var email = $('#inputEmail').val();
  var telnr = $('#inputTelNr').val();
  if (
    // (name !== "") &&
    (email !== "") &&
    // (telnr !== "") &&
    (validateEmail(email))
  ) {
    $.ajax({
      url: "https://docs.google.com/forms/d/1yB0ppw_TKTED8fRUzekz175zshRmomHKBzkeDibCZ2E/formResponse",
      data: {
        "entry.1582441788" : name,
        "entry.1186358115" : email,
        "entry.2042469105" : telnr
      },
      type: "POST",
      dataType: "xml",
      statusCode: {
        0: function (){
          $('#inputName').val("");
          $('#inputEmail').val("");
          $('#inputTelNr').val("");
          //Success message
        },
        200: function (){
          $('#inputName').val("");
          $('#inputEmail').val("");
          $('#inputTelNr').val("");
          //Success Message
        }
      }
    });
  }
  else {
    // Validation error message
    
  }
}
