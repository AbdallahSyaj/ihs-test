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

  var sliderEl = document.getElementById("pd-slider");
  var sliderTrack = document.getElementById("pd-slider-track");
  var thumbsEl = document.getElementById("pd-thumbs");
  var navPrev = document.getElementById("pd-nav-prev");
  var navNext = document.getElementById("pd-nav-next");
  var variantsEl = document.getElementById("pd-variants");
  var tabsEl = document.getElementById("pd-tabs");
  var cartBtn = document.getElementById("pd-cart");
  var cartToast = document.getElementById("pd-cart-toast");
  var pdRoot = root.querySelector(".pd");
  var productSlug = pdRoot ? pdRoot.getAttribute("data-product-slug") || "" : "";
  var productName = pdRoot ? pdRoot.getAttribute("data-product-name") || "" : "";
  var productModel = pdRoot ? pdRoot.getAttribute("data-product-model") || "" : "";
  var productImage = pdRoot ? pdRoot.getAttribute("data-product-image") || "" : "";
  var cartToastTimer = null;

  var slideCount = sliderTrack ? sliderTrack.querySelectorAll(".pd__slide").length : 0;
  var drag = {
    active: false,
    pointerId: null,
    startX: 0,
    deltaX: 0
  };

  function isRtl() {
    return document.documentElement.getAttribute("dir") === "rtl";
  }

  function clampIndex(index) {
    if (slideCount <= 0) return 0;
    return Math.max(0, Math.min(slideCount - 1, index));
  }

  function setTrackTransform(percent, animate) {
    if (!sliderTrack) return;
    sliderTrack.classList.toggle("is-animating", animate !== false);
    sliderTrack.style.transform = "translate3d(" + percent + "%, 0, 0)";
  }

  function scrollActiveThumbIntoView() {
    if (!thumbsEl) return;
    var thumb = thumbsEl.querySelector('[data-index="' + state.image + '"]');
    if (!thumb) return;
    thumb.scrollIntoView({ behavior: "smooth", inline: "nearest", block: "nearest" });
  }

  function updateNavButtons() {
    if (!navPrev || !navNext || slideCount <= 1) return;
    navPrev.disabled = state.image <= 0;
    navNext.disabled = state.image >= slideCount - 1;
  }

  function updateGallery(animate) {
    if (slideCount > 0) {
      setTrackTransform(-state.image * 100, animate);
    }

    if (thumbsEl) {
      thumbsEl.querySelectorAll(".pd__thumb").forEach(function (btn) {
        var index = Number(btn.getAttribute("data-index"));
        var active = index === state.image;
        btn.classList.toggle("is-active", active);
        btn.setAttribute("aria-current", active ? "true" : "false");
        var bar = btn.querySelector(".pd__thumb-bar");
        if (bar) bar.hidden = !active;
      });
      scrollActiveThumbIntoView();
    }

    updateNavButtons();
  }

  function goTo(index, animate) {
    if (slideCount <= 0) return;
    state.image = clampIndex(index);
    updateGallery(animate);
  }

  function goRelative(step) {
    goTo(state.image + step, true);
  }

  function onPointerDown(e) {
    if (!sliderEl || slideCount <= 1 || e.button > 0) return;
    if (e.target.closest(".pd__nav")) return;

    drag.active = true;
    drag.pointerId = e.pointerId;
    drag.startX = e.clientX;
    drag.deltaX = 0;
    sliderEl.classList.add("is-dragging");
    sliderEl.setPointerCapture(e.pointerId);
    setTrackTransform(-state.image * 100, false);
  }

  function onPointerMove(e) {
    if (!drag.active || e.pointerId !== drag.pointerId || !sliderEl) return;

    drag.deltaX = e.clientX - drag.startX;
    var width = sliderEl.clientWidth || 1;
    var percentOffset = (drag.deltaX / width) * 100;
    var base = -state.image * 100;
    var atStart = state.image <= 0;
    var atEnd = state.image >= slideCount - 1;
    var rtl = isRtl();
    var movingTowardStart = rtl ? drag.deltaX > 0 : drag.deltaX < 0;
    var movingTowardEnd = rtl ? drag.deltaX < 0 : drag.deltaX > 0;

    if ((atStart && movingTowardStart) || (atEnd && movingTowardEnd)) {
      percentOffset *= 0.35;
    }

    setTrackTransform(base + percentOffset, false);
  }

  function onPointerUp(e) {
    if (!drag.active || e.pointerId !== drag.pointerId) return;

    drag.active = false;
    drag.pointerId = null;
    if (sliderEl) {
      sliderEl.classList.remove("is-dragging");
      try {
        sliderEl.releasePointerCapture(e.pointerId);
      } catch (err) {
        // ignore if capture was already released
      }
    }

    var width = sliderEl ? sliderEl.clientWidth : 0;
    var threshold = Math.max(40, width * 0.12);
    var delta = isRtl() ? -drag.deltaX : drag.deltaX;

    if (delta < -threshold) {
      goRelative(1);
    } else if (delta > threshold) {
      goRelative(-1);
    } else {
      updateGallery(true);
    }
  }

  if (sliderEl && slideCount > 1) {
    sliderEl.addEventListener("pointerdown", onPointerDown);
    sliderEl.addEventListener("pointermove", onPointerMove);
    sliderEl.addEventListener("pointerup", onPointerUp);
    sliderEl.addEventListener("pointercancel", onPointerUp);

    sliderEl.addEventListener("keydown", function (e) {
      if (e.key !== "ArrowLeft" && e.key !== "ArrowRight") return;
      e.preventDefault();
      var rtl = isRtl();
      if (e.key === "ArrowLeft") {
        goRelative(rtl ? 1 : -1);
      } else {
        goRelative(rtl ? -1 : 1);
      }
    });
  }

  if (navPrev) {
    navPrev.addEventListener("click", function (e) {
      e.stopPropagation();
      goRelative(-1);
    });
  }

  if (navNext) {
    navNext.addEventListener("click", function (e) {
      e.stopPropagation();
      goRelative(1);
    });
  }

  if (thumbsEl) {
    thumbsEl.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-index]");
      if (!btn) return;
      state.image = Number(btn.getAttribute("data-index"));
      updateGallery(true);
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

  function showCartToast() {
    if (!cartToast) return;
    cartToast.hidden = false;
    if (cartToastTimer) clearTimeout(cartToastTimer);
    cartToastTimer = setTimeout(function () {
      cartToast.hidden = true;
    }, 3200);
  }

  if (cartBtn) {
    cartBtn.addEventListener("click", function () {
      if (variantsEl) {
        var unselected = 0;
        variantsEl.querySelectorAll(".pd__variant").forEach(function (block) {
          var name = block.getAttribute("data-variant");
          if (name && !state.selected[name]) unselected += 1;
        });

        if (unselected > 0) {
          state.showValidation = true;
          updateVariants();
          return;
        }
      }

      if (!window.AurumCart || !productSlug || !productName) return;

      window.AurumCart.addItem({
        slug: productSlug,
        name: productName,
        model: productModel,
        image: productImage,
        quantity: 1,
        options: Object.assign({}, state.selected)
      });
      showCartToast();
    });
  }

  updateGallery(true);
  updateVariants();
  updateTabs();
});
