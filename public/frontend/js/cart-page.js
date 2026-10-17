window.AurumOnReady = window.AurumOnReady || [];

window.AurumOnReady.push(function initCartPage() {
  var listEl = document.getElementById("cart-items");
  var emptyEl = document.getElementById("cart-empty");
  var countEl = document.getElementById("cart-item-count");
  var form = document.getElementById("cart-inquiry-form");
  if (!listEl || !form || !window.AurumCart) return;

  var submitBtn = document.getElementById("cart-submit");
  var formHint = document.getElementById("cart-form-hint");
  var formError = document.getElementById("cart-form-error");
  var fields = ["name", "email", "phone"];
  var pending = false;
  var submitted = false;
  var timer = null;
  var productBase = (window.AurumPaths && window.AurumPaths.productBase) || "/products";

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function optionsLabel(options) {
    var keys = Object.keys(options || {});
    if (!keys.length) return "";
    return keys
      .map(function (key) {
        return key + ": " + options[key];
      })
      .join(" · ");
  }

  function setFormDisabled(disabled) {
    form.classList.toggle("is-disabled", disabled);
    if (submitBtn) submitBtn.disabled = disabled || pending;
    if (formHint) formHint.hidden = !disabled;
  }

  function render() {
    var items = window.AurumCart.getItems();
    listEl.innerHTML = "";

    if (countEl) {
      var total = window.AurumCart.totalQuantity();
      countEl.textContent = total
        ? total + " " + (total === 1 ? "item" : "items")
        : "";
    }

    if (!items.length) {
      if (emptyEl) emptyEl.hidden = false;
      setFormDisabled(true);
      return;
    }

    if (emptyEl) emptyEl.hidden = true;
    setFormDisabled(false);

    items.forEach(function (item) {
      var row = document.createElement("article");
      row.className = "cart-line";
      row.setAttribute("data-cart-line", item.slug);
      row.setAttribute("data-cart-options", JSON.stringify(item.options || {}));

      var thumb = item.image
        ? '<img src="' + escapeHtml(item.image) + '" alt="" loading="lazy" />'
        : "";

      var meta = item.model ? "Model " + escapeHtml(item.model) : "";
      var optionsText = optionsLabel(item.options);
      var optionsHtml = optionsText
        ? '<p class="cart-line__options">' + escapeHtml(optionsText) + "</p>"
        : "";

      row.innerHTML =
        '<div class="cart-line__thumb">' +
        thumb +
        "</div>" +
        '<div class="cart-line__body">' +
        '<a class="cart-line__name" href="' +
        escapeHtml(productBase + "/" + item.slug) +
        '">' +
        escapeHtml(item.name) +
        "</a>" +
        (meta ? '<p class="cart-line__meta">' + meta + "</p>" : "") +
        optionsHtml +
        '<div class="cart-line__actions">' +
        '<div class="cart-qty" role="group" aria-label="Quantity">' +
        '<button type="button" class="cart-qty__btn" data-qty-action="decrease" aria-label="Decrease quantity">−</button>' +
        '<span class="cart-qty__value" aria-live="polite">' +
        item.quantity +
        "</span>" +
        '<button type="button" class="cart-qty__btn" data-qty-action="increase" aria-label="Increase quantity">+</button>' +
        "</div>" +
        '<button type="button" class="cart-line__remove" data-remove-line>Remove</button>' +
        "</div>" +
        "</div>";

      listEl.appendChild(row);
    });
  }

  listEl.addEventListener("click", function (e) {
    var line = e.target.closest(".cart-line");
    if (!line) return;

    var slug = line.getAttribute("data-cart-line");
    var options = {};
    try {
      options = JSON.parse(line.getAttribute("data-cart-options") || "{}");
    } catch (err) {
      options = {};
    }

    var qtyEl = line.querySelector(".cart-qty__value");
    var current = qtyEl ? parseInt(qtyEl.textContent, 10) || 1 : 1;

    if (e.target.closest("[data-remove-line]")) {
      window.AurumCart.removeItem(slug, options);
      render();
      return;
    }

    var actionBtn = e.target.closest("[data-qty-action]");
    if (!actionBtn) return;

    var action = actionBtn.getAttribute("data-qty-action");
    var next = action === "increase" ? current + 1 : current - 1;
    window.AurumCart.updateQuantity(slug, options, next);
    render();
  });

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

  function setSubmitSuccess(on) {
    var iconDefault = document.getElementById("cart-submit-icon-default");
    var iconSuccess = document.getElementById("cart-submit-icon-success");
    var labelDefault = document.getElementById("cart-submit-label-default");
    var labelSuccess = document.getElementById("cart-submit-label-success");
    if (iconDefault) iconDefault.hidden = on;
    if (labelDefault) labelDefault.hidden = on;
    if (iconSuccess) iconSuccess.hidden = !on;
    if (labelSuccess) labelSuccess.hidden = !on;
  }

  function restoreButton() {
    submitted = false;
    if (submitBtn) submitBtn.disabled = window.AurumCart.getItems().length === 0;
    setSubmitSuccess(false);
    var thanks = document.getElementById("cart-thanks");
    if (thanks) thanks.hidden = true;
  }

  function showSuccess(message) {
    submitted = true;
    window.AurumCart.clear();
    render();
    fields.forEach(function (field) {
      form.elements[field].value = "";
    });
    if (submitBtn) submitBtn.disabled = true;
    setSubmitSuccess(true);
    var thanks = document.getElementById("cart-thanks");
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

    var items = window.AurumCart.getItems();
    if (!items.length) {
      setFormError("Add at least one product before submitting.");
      return;
    }

    clearErrors();
    var clientErrors = false;
    fields.forEach(function (field) {
      if (!form.elements[field].value.trim()) {
        var err = document.getElementById("error-" + field);
        setError(field, err ? err.textContent.replace(/^\s+/, "") : "This field is required");
        clientErrors = true;
      }
    });
    if (clientErrors) return;

    pending = true;
    if (submitBtn) submitBtn.disabled = true;
    if (submitted) restoreButton();
    if (submitBtn) submitBtn.disabled = true;

    var tokenInput = form.querySelector('input[name="_token"]');
    var headers = {
      Accept: "application/json",
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest"
    };
    if (tokenInput) headers["X-CSRF-TOKEN"] = tokenInput.value;

    var payload = {
      name: form.elements.name.value.trim(),
      email: form.elements.email.value.trim(),
      phone: form.elements.phone.value.trim(),
      items: items.map(function (item) {
        return {
          slug: item.slug,
          name: item.name,
          model: item.model,
          image: item.image,
          quantity: item.quantity,
          options: item.options
        };
      })
    };

    fetch(form.action, {
      method: "POST",
      headers: headers,
      body: JSON.stringify(payload)
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
            var simple = field.split(".")[0];
            if (fields.indexOf(simple) !== -1) setError(simple, message);
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
          setFormError("Too many requests. Please wait a moment and try again.");
          return;
        }

        setFormError("Could not submit your inquiry. Please try again.");
      })
      .catch(function () {
        setFormError("Could not submit your inquiry. Please try again.");
      })
      .then(function () {
        pending = false;
        if (!submitted && submitBtn) {
          submitBtn.disabled = window.AurumCart.getItems().length === 0;
        }
      });
  });

  if (sessionStorage.getItem("aurum_cart_flash")) {
    sessionStorage.removeItem("aurum_cart_flash");
  }

  render();
});
