if (self == top) {
  document.documentElement.style.display = "block";
} else {
  top.location = self.location;
}

var requiredFieldHint = 'This is a required field.';
var invaildEmailFormatHint = 'Invalid email format. Please try again with a valid email address.';
var fingerPrintingTimeout = "800";
var enableSocial = "false";
var contactUrl = "https://www.netacad.com/signin-help";
var recoveryUrl = 'https://identity.cisco.com/ui/tenants/$realmName/v1.0/recovery-ui/forgot-password';
            
var validEmailInputHint = 'Enter a valid email address, formatted as user@company.com.'
            
var aboutUrl = '/ui/tenants/$realmName/v1.0/about-ui' 
            
            
if (contactUrl) {
  document.getElementsByClassName("contactUs")[0].setAttribute("href", contactUrl);
  document.getElementById('createAccount').style.display = 'none';
}

window.onload = function () {
  generateLanguageItems();
  setLanguageBasedOnLocale();
  ifShowSocialIcon();
};

function handleNonDefaultOrgLogin() {
  document.loginForm.action = "@renderNonDefaultOrgLogin";
  document.loginForm.submit();
}

function getElementById(ele) {
  return document.getElementById(ele);
}

function createInput(name, value) {
  var newInput = document.createElement("input");
  newInput.type = "hidden";
  newInput.name = name;
  newInput.value = value;
  return newInput;
}

function submitForSamlSocialLogin(socialApp) {
  var socialAppInput = createInput("socialApp", socialApp);
  var form = document.getElementById('loginForm');
  form.appendChild(socialAppInput);
  form.submit();
}

function onSubmit(e) {
  var disabled = document.getElementById('btn').getAttribute('disabled');
  if(disabled) return

  document.getElementById('btn').setAttribute('disabled', true);
  var form = document.getElementById('loginForm');
  var fingerData = fingerPrint('loginNext', $('#email').val());
  fingerPrintService(fingerData).then(function(data){}).catch(function(error){})
  setTimeout(function(){
    form.submit();
  }, fingerPrintingTimeout * 1)
}

function setErrorField(errorStatus, errorMessage) {
  getElementById("emailAlert").setAttribute("class", errorStatus);
  getElementById("errorMessages").innerText = errorMessage;
}

function toVerifyEmail(e) {
  var errorMessages = {
    empty: requiredFieldHint,
    error: invaildEmailFormatHint
  };

  var value = e.value;
  var verifyResult = false;
  var button = getElementById('btn');

  if (value) {
    verifyResult = true;
  } else {
    setErrorField("text--warning help-block", errorMessages.empty);
    return false;
  }

  if (verifyResult) {
    setErrorField("help-block hide", "");
    var emailEle = getElementById('email');
    emailEle.setAttribute('value', value.trim());
    button.removeAttribute('disabled');
  } 
}

function onBlur(e) {
  var btn = getElementById('btn');
  btn.setAttribute('disabled', true);
  toVerifyEmail(e);
};

function closeMsg() {
  var alertMessage = getElementById("alert-message");
  if (alertMessage) {
    alertMessage.classList.add("hide");
  }
};

function setImageUrl() {
  var imgEle = getElementById('img_left');
  imgEle.setAttribute('src', imgEle.getAttribute('data-src'));
}

function fingerPrint(fingerprintType, email) {
  var fingerPrint = fingerprintUser();
  fingerPrint['fingerprintType'] = fingerprintType;
  fingerPrint['email'] = email;
  return fingerPrint
}

function setEmptyError(alertId) {
  $('#' + alertId).attr('class', 'text--warning help-block');
  $('#' + alertId).html('<span class="icon-exclamation-circle"></span><span>' + requiredFieldHint + '</span>');
  if (alertId === 'passwordAlert') {
    $("#kc-login").attr('disabled', true);
  } else {
    $("#kc-next").attr('disabled', true);
  }
}


function onFieldChange(e) {
  var id = e.getAttribute('id');
  var fieldVal = e.value.trim();
  var alertId = id + "Alert";

  if (!fieldVal) {
    setEmptyError(alertId);
    return false
  }

  (id === 'username') ? $('#kc-next').attr('disabled', false) : $('#kc-login').attr('disabled', false);

  getElementById(alertId).classList.add('hide');
}

function getRequest(url) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      var response = JSON.parse(this.response);
      var responseData = response.response;
      sessionStorage.setItem('response', JSON.stringify(responseData));
    }
  };
  xhttp.open("GET", url, true);
  xhttp.send();
};

function verifyfield(type, value) {
  return new Promise(function (resolve, reject) {
    var data = JSON.parse(sessionStorage.getItem('response')).find(function (item) {
      return item.name === type;
    });
    var regex = data.regex,
      minLength = data.minLength,
      maxLength = data.maxLength;

    var verifyResult = false;
    verifyResult = verifyValue(regex, value, minLength, maxLength);
    resolve(verifyResult);
  });
}

function verifyValue(regexString, value, minLength, maxLength) {
  return createNewRegExp(regexString).test(value.trim()) && value.trim().length >= minLength && value.trim().length <= maxLength;
}

function createNewRegExp(str) {
  if (!str) return false;
  var regexString = str.slice(1, str.length - 2);
  var pattern = new RegExp(regexString);
  return pattern;
}

function serializeArray(form) {
    // Setup our serialized data
    var serialized = [];
    // Loop through each field in the form
    var N = form.elements.length;
    for (var i = 0; i < N; i++) {
        var field = form.elements[i];
        // Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
        if (!field.name || field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') continue;
        // If a multi-select, get all selections
        if (field.type === 'select-multiple') {
            var M = field.options.length;
            for (var n = 0; n < M; n++) {
                if (!field.options[n].selected) continue;
                serialized.push({name: field.name, value: field.options[n].value});
            }
        }
        // Convert field data to a query string
        else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
            serialized.push({name: field.name, value: field.value});
        }
    }
    return serialized;
};