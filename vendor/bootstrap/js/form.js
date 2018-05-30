/**
 * email validation function in JS
 */
function isEmail(email) {
  var re = /^\w+([-+.'][^\s]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
  return re.test(email);
}

/**
 * check username and lastname to not contain space/specialchars/numbers.. just latin extended chars are permited
 */
function isName(email) {
  var regex = /[A-Za-z žđšćčŽĐŠĆČ]/g;
  return regex.test(email);
}


/**
 * happens after client submit form
 */
$('#signup-form').submit(function() {

  var naziv = $(".sp-spol").val();
  var ime = $("input[name=firstname]").val();
  var priimek = $("input[name=lastname]").val();
  var email = $(".sp-email").val();

  if (ime != "" && ime != undefined && ime != null && ime != false) {
    if (isName(ime) === false) {
      var element = $("input[name=firstname]")[0];
      element.setCustomValidity('Prosimo vnesite ime v pravilni obliki.')
      $(".sp-submit").trigger("click");
      return false;
    }
  } else {}

  console.log(priimek);
  if (priimek != "" && priimek != undefined && priimek != null && priimek != false) {
    if (isName(priimek) === false) {
      var element = $("input[name=lastname]")[0];
      element.setCustomValidity('Prosimo vnesite priimek v pravilni obliki.')
      $(".sp-submit").trigger("click");
      return false;
    }
  }

  if (toSend === true)
  {

    $.ajax({
      url: 'server/submit.php',
      data: {
        action: 'submit',
        email: email,
        naziv: naziv,
        ime: ime,
        priimek: priimek,
      },
      async: false,
      type: 'post',
      success: function(output) {

        if (output === "blacklisted") {
          $("#blocked").html("Z vašega naslova smo prejeli preveč prijav. Prosimo poskusite znova čez nekaj časa.");

        } else {
          $("#blocked").html("");
          window.location.href = "https://www.forms.my/zahvala"; // you can set custom redirect URL after success submit
        }
      }
    });

    return false;

  } else {

    return false;

    /**
     * we allways return false, as we fo our post request in client side via ajax call
     */
  }
});
