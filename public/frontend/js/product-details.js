window.AurumOnReady = window.AurumOnReady || [];

window.AurumOnReady.push(function initProductDetails() {
  var root = document.getElementById("product-details-app");
  if (!root) return;

  var state = {
    image: 0,
    tab: "description",
    selected: {},
    open: null,
    showValidation: false
  };

  var mainImg = document.getElementById("pd-main-img");
  var thumbsEl = document.getElementById("pd-thumbs");
  var variantsEl = document.getElementById("pd-variants");
  var tabsEl = document.getElementById("pd-tabs");
  var cartBtn = document.getElementById("pd-cart");
  var productNameEl = root.querySelector("[data-product-name]");
  var productName = productNameEl ? productNameEl.getAttribute("data-product-name") || "" : "";

  function updateGallery() {
    if (!thumbsEl) return;

    var thumb = thumbsEl.querySelector('[data-index="' + state.image + '"]');
    if (mainImg && thumb) {
      var thumbImg = thumb.querySelector("img");
      if (thumbImg) {
        mainImg.src = thumbImg.getAttribute("src") || "";
        mainImg.alt = productName + " view " + (state.image + 1);
      }
    }

    thumbsEl.querySelectorAll(".pd__thumb").forEach(function (btn) {
      var index = Number(btn.getAttribute("data-index"));
      var active = index === state.image;
      btn.classList.toggle("is-active", active);
      var bar = btn.querySelector(".pd__thumb-bar");
      if (bar) bar.hidden = !active;
    });
  }

  function updateVariants() {
    if (!variantsEl) return;

    variantsEl.querySelectorAll(".pd__variant").forEach(function (block) {
      var variantName = block.getAttribute("data-variant");
      if (!variantName) return;

      var selected = state.selected[variantName];
      var isOpen = state.open === variantName;
      var showError = state.showValidation && !selected;
      var toggleBtn = block.querySelector("[data-toggle]");
      var menu = block.querySelector("[data-variant-menu]");
      var valueEl = block.querySelector(".pd__select-value");
      var errEl = block.querySelector("[data-variant-error]");

      if (toggleBtn) {
        toggleBtn.classList.toggle("is-open", isOpen);
        toggleBtn.classList.remove("is-invalid", "has-value");
        if (isOpen) {
          // is-open only
        } else if (showError) {
          toggleBtn.classList.add("is-invalid");
        } else if (selected) {
          toggleBtn.classList.add("has-value");
        }
      }

      if (menu) menu.hidden = !isOpen;

      if (valueEl) {
        valueEl.textContent = selected || "Select " + variantName;
        valueEl.classList.toggle("pd__select-placeholder", !selected);
      }

      if (errEl) errEl.hidden = !showError;

      block.querySelectorAll("[data-option]").forEach(function (optBtn) {
        var option = optBtn.getAttribute("data-option");
        var check = optBtn.querySelector(".pd__option-check");
        if (check) check.hidden = selected !== option;
      });
    });
  }

  function updateTabs() {
    if (tabsEl) {
      tabsEl.querySelectorAll("[data-tab]").forEach(function (btn) {
        btn.classList.toggle("is-active", btn.getAttribute("data-tab") === state.tab);
      });
    }

    root.querySelectorAll("[data-tab-panel]").forEach(function (panel) {
      panel.hidden = panel.getAttribute("data-tab-panel") !== state.tab;
    });
  }

  if (thumbsEl) {
    thumbsEl.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-index]");
      if (!btn) return;
      state.image = Number(btn.getAttribute("data-index"));
      updateGallery();
    });
  }

  if (variantsEl) {
    variantsEl.addEventListener("click", function (e) {
      var toggle = e.target.closest("[data-toggle]");
      if (toggle) {
        var name = toggle.getAttribute("data-toggle");
        state.open = state.open === name ? null : name;
        updateVariants();
        return;
      }

      var option = e.target.closest("[data-option]");
      if (option) {
        state.selected[option.getAttribute("data-variant")] = option.getAttribute("data-option");
        state.open = null;
        updateVariants();
      }
    });
  }

  if (tabsEl) {
    tabsEl.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-tab]");
      if (!btn) return;
      state.tab = btn.getAttribute("data-tab");
      updateTabs();
    });
  }

  if (cartBtn) {
    cartBtn.addEventListener("click", function () {
      if (!variantsEl) return;

      var unselected = 0;
      variantsEl.querySelectorAll(".pd__variant").forEach(function (block) {
        var name = block.getAttribute("data-variant");
        if (name && !state.selected[name]) unselected += 1;
      });

      state.showValidation = unselected > 0;
      if (state.showValidation) updateVariants();
    });
  }

  updateGallery();
  updateVariants();
  updateTabs();
});
