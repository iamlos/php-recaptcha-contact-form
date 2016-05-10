//validate.js validation rules
//more at http://rickharrison.github.io/validate.js/
var validator = new FormValidator('contact_form', [{
  name: 'subject',
  display: "<?php $lang_translate['Subject'] ?>",
  rules: 'required'
}, {
  name: 'email',
  display: "<?php $lang_translate['Email'] ?>",
  rules: 'required|valid_email'
}, {
  name: 'message',
  display: "<?php $lang_translate['Message'] ?>",
  rules: 'required'
}],
function(errors, evt) {
  if (errors.length > 0) {
    var errorString = '';
    for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
      errorString += errors[i].message + '<br />';
    }
    if (evt && evt.preventDefault) {
      evt.preventDefault();
    } else if (event) {
      event.returnValue = false;
    }
    document.getElementById("response-message").innerHTML = '<div class="error">'+errorString+'</div>';
    console.dir(errors);
  }
});
