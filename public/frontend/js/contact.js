window.AurumOnReady = window.AurumOnReady || [];

window.AurumOnReady.push(function initContact() {
  var form = document.getElementById("contact-form");
  if (!form) return;

  var fields = ["name", "email", "subject", "message"];
  var pending = false;
  var submitted = false;
  var timer = null;
  var submitBtn = document.getElementById("contact-submit");
  var submitIconDefault = document.getElementById("contact-submit-icon-default");
  var submitIconSuccess = document.getElementById("contact-submit-icon-success");
  var submitLabelDefault = document.getElementById("contact-submit-label-default");
  var submitLabelSuccess = document.getElementById("contact-submit-label-success");
  var formError = document.getElementById("contact-form-error");

  function setSubmitButtonSuccess(on) {
    if (submitIconDefault) submitIconDefault.hidden = on;
    if (submitLabelDefault) submitLabelDefault.hidden = on;
    if (submitIconSuccess) submitIconSuccess.hidden = !on;
    if (submitLabelSuccess) submitLabelSuccess.hidden = !on;
  }

  function setError(field, message) {
    var input = form.querySelector('[name="' + field + '"]');
    var err = document.getElementById("error-" + field);
    if (!input || !err) return;
    var on = !!message;
    input.classList.toggle("is-invalid", on);
    err.hidden = !on;
    if (on) err.textContent = message;
  }

  function setFormError(message) {
    if (!formError) return;
    formError.hidden = !message;
    formError.textContent = message || "";
  }

  function clearErrors() {
    fields.forEach(function (field) {
      setError(field, "");
    });
    setFormError("");
  }

  function restoreButton() {
    submitted = false;
    submitBtn.disabled = false;
    setSubmitButtonSuccess(false);
    var thanks = document.getElementById("contact-thanks");
    if (thanks) thanks.hidden = true;
  }

  function showSuccess(message) {
    submitted = true;
    fields.forEach(function (field) {
      form.elements[field].value = "";
    });
    var thanks = document.getElementById("contact-thanks");
    submitBtn.disabled = true;
    if (submitIconSuccess && submitLabelSuccess) {
      setSubmitButtonSuccess(true);
    }
    if (thanks) {
      thanks.hidden = false;
      if (message) thanks.textContent = message;
    }
    if (timer) clearTimeout(timer);
    timer = setTimeout(restoreButton, 5000);
  }

  form.addEventListener("input", function (e) {
    var name = e.target.getAttribute("name");
    if (fields.indexOf(name) !== -1) setError(name, "");
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (pending) return;

    clearErrors();
    var clientErrors = false;
    fields.forEach(function (field) {
      if (!form.elements[field].value.trim()) {
        var err = document.getElementById("error-" + field);
        setError(field, err ? err.textContent : "This field is required");
        clientErrors = true;
      }
    });
    if (clientErrors) return;

    pending = true;
    submitBtn.disabled = true;
    if (submitted) restoreButton();
    submitBtn.disabled = true;

    var tokenInput = form.querySelector('input[name="_token"]');
    var headers = {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest"
    };
    if (tokenInput) headers["X-CSRF-TOKEN"] = tokenInput.value;

    fetch(form.action, {
      method: "POST",
      headers: headers,
      body: new FormData(form)
    })
      .then(function (res) {
        return res.text().then(function (text) {
          var data = null;
          if (text) {
            try {
              data = JSON.parse(text);
            } catch (err) {
              data = null;
            }
          }
          return { ok: res.ok, status: res.status, data: data };
        });
      })
      .then(function (result) {
        if (result.ok && result.data && result.data.message) {
          showSuccess(result.data.message);
          return;
        }

        var errors = result.data && result.data.errors ? result.data.errors : null;
        if (result.status === 422 && errors) {
          var unmatched = [];
          Object.keys(errors).forEach(function (field) {
            var message = errors[field] && errors[field][0] ? errors[field][0] : "";
            if (fields.indexOf(field) !== -1) setError(field, message);
            else if (message) unmatched.push(message);
          });
          if (unmatched.length) setFormError(unmatched.join(" "));
          return;
        }

        if (result.status === 419) {
          setFormError("Your session expired. Refresh the page and try again.");
          return;
        }

        if (result.status === 429) {
          setFormError("Too many messages. Please wait a moment and try again.");
          return;
        }

        setFormError("Could not send your message. Please try again.");
      })
      .catch(function () {
        setFormError("Could not send your message. Please try again.");
      })
      .then(function () {
        pending = false;
        if (!submitted) submitBtn.disabled = false;
      });
  });
});
