/**
 * Honeypot and Submit Slider JavaScript
 *
 * (NOTE: This solution was originally implemented at
 * https://jsfiddle.net/Treebasher/2xugqso3/.)
 */

/* setupForm sets the action and the method of the specified form element. The point of
this function is to prevent bots who might have JavaScript disabled from being able to
force submission of the form. (NOTE: This will also make it impossible for users that
have JavaScript disabled from using the form, but that should be the vast minority of
users.) */
function setupForm(formElement, action, method) {
  $(formElement).attr('action', action);
  $(formElement).attr('method', method);
}

/* addHoneypotAndSubmitSlider adds a honeypot and a submit slider to the sepcified form
element and then sets up the necessary event handlers. */
function addHoneypotAndSubmitSlider(formElement, sliderText) {
  /* Create the HTML for the honeypot and the submit slider */
  var honeypotHTML = "<input name='honeypot' class='honeypot' type='text'>";
  var submitSliderHTML = "<div class='submitSliderWrapper'>\
    <span class='shimmer'>" + sliderText + "</span>\
    <input name='submitSlider' class='submitSlider' type='range' min=0 max=100 value=0>\
  </div>";

  /* Add the honeypot and the submit slider to the specified form */
  $(formElement).append(honeypotHTML);
  $(formElement).append(submitSliderHTML);

  /* Set up the necessary event handlers for submitting the form */
  $(formElement + ' .submitSlider').on('mouseup touchend change', function(event) {
    /* The 'change' event is the most accurate one for what we are trying to
    achieve (in fact, it is the only one that works in Firefox), but it breaks
    Internet Explorer */
    var isIE = document.body.style.msTouchAction !== undefined;
    if (isIE && event.type == "change") {
	  return false;
    }

    /* If the slider has been (mostly) to the right and the honeypot has not been
    touched and then submit the form */
    if ($(this).val() >= 75 && $(formElement + ' .honeypot').val() == "") {
	  $(formElement).submit();
    }

    /* Reset the slider back to the beginning */
    $(this).val(0);
  });
  
  /* Set up the necessary event handlers for handling submission of the form */
  $(formElement).submit(function(e) {
    /* Only submit the form if the slider is actually where it should be */
    if ($(formElement + ' .submitSlider').val() < 75 || $(formElement + ' .honeypot').val() != "") {
      e.preventDefault(e);
    }
  });
}

