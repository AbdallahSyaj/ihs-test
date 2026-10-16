window.AurumOnReady = window.AurumOnReady || [];

window.AurumOnReady.push(function initContact() {
  var form = document.getElementById("contact-form");
  if (!form) return;

  var fields = ["name", "email", "subject", "message"];
  var submitted = false;
  var timer = null;

  function setError(field, on) {
    var input = form.querySelector('[name="' + field + '"]');
    var err = document.getElementById("error-" + field);
    input.classList.toggle("is-invalid", on);
    err.hidden = !on;
  }

  form.addEventListener("input", function (e) {
    var name = e.target.getAttribute("name");
    if (fields.indexOf(name) !== -1) setError(name, false);
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var errors = {};
    fields.forEach(function (field) {
      var value = form.elements[field].value.trim();
      if (!value) errors[field] = true;
    });
    fields.forEach(function (field) {
      setError(field, !!errors[field]);
    });
    if (Object.keys(errors).length) return;

    submitted = true;
    fields.forEach(function (field) {
      form.elements[field].value = "";
    });
    updateSubmit();
    if (timer) clearTimeout(timer);
    timer = setTimeout(function () {
      submitted = false;
      updateSubmit();
    }, 5000);
  });

  function updateSubmit() {
    var btn = document.getElementById("contact-submit");
    var thanks = document.getElementById("contact-thanks");
    var I = window.AurumIcons;
    if (submitted) {
      btn.innerHTML = I.svg("check", "icon icon-4", 1.5) + "Message Sent";
      thanks.hidden = false;
    } else {
      btn.innerHTML = I.svg("send", "icon icon-4", 1.5) + "Send Message";
      thanks.hidden = true;
    }
  }
});
