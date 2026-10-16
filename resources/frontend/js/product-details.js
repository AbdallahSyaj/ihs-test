window.AurumOnReady = window.AurumOnReady || [];

window.AurumOnReady.push(function initProductDetails() {
  var root = document.getElementById("product-details-app");
  if (!root) return;

  var I = window.AurumIcons;
  var FALLBACK = window.AurumData.fallbackId;
  var params = new URLSearchParams(window.location.search);
  var requestedId = params.get("id");

  window.AurumData.load()
    .then(function (data) {
      var product =
        data.products.find(function (p) {
          return p.id === requestedId;
        }) ||
        data.products.find(function (p) {
          return p.id === FALLBACK;
        }) ||
        data.products[0];

      render(product);
    })
    .catch(function (err) {
      root.innerHTML = '<p class="include-error">Could not load product data.</p>';
      console.error(err);
    });

  function render(product) {
    var state = {
      image: 0,
      tab: "description",
      selected: {},
      open: null,
      showValidation: false
    };

    root.innerHTML =
      '<div class="pd animate-fade-in">' +
      '<div class="container-lux pd__inner">' +
      '<div class="pd__crumb">' +
      '<a href="products.html">' +
      I.svg("chevronLeft", "icon icon-3", 1.5) +
      "Products</a>" +
      '<span class="pd__crumb-sep">/</span>' +
      '<span class="pd__crumb-name"></span>' +
      "</div>" +
      '<div class="pd__layout">' +
      '<div class="pd__gallery">' +
      '<div class="pd__main-image" id="pd-main"></div>' +
      '<div class="pd__thumbs" id="pd-thumbs"></div>' +
      "</div>" +
      '<div class="pd__info">' +
      '<span class="pd__meta" id="pd-meta"></span>' +
      ' <h1 class="pd__title" id="pd-title"></h1>' +
      '<p class="pd__desc" id="pd-desc"></p>' +
      '<div class="pd__price-row">' +
      '<span class="pd__price" id="pd-price"></span>' +
      '<span class="pd__stock"><span class="pd__stock-dot"></span>In stock — ready to ship</span>' +
      "</div>" +
      '<div class="divider-gold"></div>' +
      '<div class="pd__variants" id="pd-variants"></div>' +
      '<div class="pd__actions">' +
      '<button type="button" class="btn-gold" id="pd-cart">' +
      I.svg("bag", "icon icon-4", 1.5) +
      "Add to Cart</button>" +
      '<a href="contact.html" class="btn-outline">Request Details</a>' +
      "</div>" +
      '<div class="pd__tabs">' +
      '<div class="pd__tablist" id="pd-tabs"></div>' +
      '<div class="pd__tab-panel" id="pd-panel"></div>' +
      "</div></div></div></div></div>";

    root.querySelector(".pd__crumb-name").textContent = product.name;
    document.getElementById("pd-meta").textContent =
      product.categoryLabel + " — Model " + product.model;
    document.getElementById("pd-title").textContent = product.name;
    document.getElementById("pd-desc").textContent = product.description;
    document.getElementById("pd-price").textContent = product.price;

    var tabs = [
      { id: "description", label: "Description", icon: "fileText" },
      { id: "specs", label: "Specifications", icon: "info" },
      { id: "accessories", label: "Accessories", icon: "package" },
      { id: "compatibility", label: "Compatibility", icon: "link2" }
    ];

    function renderGallery() {
      var main = document.getElementById("pd-main");
      var badge = product.badge
        ? '<span class="badge-gold">' + product.badge + "</span>"
        : "";
      main.innerHTML =
        '<img class="animate-scale-in" src="' +
        product.gallery[state.image] +
        '" alt="' +
        product.name +
        " view " +
        (state.image + 1) +
        '">' +
        badge;

      var thumbs = document.getElementById("pd-thumbs");
      thumbs.innerHTML = product.gallery
        .map(function (img, i) {
          return (
            '<button type="button" class="pd__thumb' +
            (state.image === i ? " is-active" : "") +
            '" data-index="' +
            i +
            '">' +
            '<img src="' +
            img +
            '" alt="Thumbnail ' +
            (i + 1) +
            '" loading="lazy">' +
            (state.image === i ? '<span class="pd__thumb-bar"></span>' : "") +
            "</button>"
          );
        })
        .join("");
    }

    function renderVariants() {
      var wrap = document.getElementById("pd-variants");
      if (!product.variants.length) {
        wrap.innerHTML = "";
        wrap.style.display = "none";
        return;
      }
      wrap.style.display = "";
      wrap.innerHTML = product.variants
        .map(function (variant) {
          var selected = state.selected[variant.name];
          var isOpen = state.open === variant.name;
          var showError = state.showValidation && !selected;
          var btnClass = "pd__select-btn";
          if (isOpen) btnClass += " is-open";
          else if (showError) btnClass += " is-invalid";
          else if (selected) btnClass += " has-value";
          var options = variant.options
            .map(function (option) {
              return (
                '<button type="button" class="pd__select-option" data-variant="' +
                variant.name +
                '" data-option="' +
                option +
                '"><span>' +
                option +
                "</span>" +
                (selected === option ? I.svg("check", "icon icon-4", 1.5) : "") +
                "</button>"
              );
            })
            .join("");
          return (
            '<div class="pd__variant">' +
            '<label class="pd__variant-label"><span>' +
            variant.name +
            "</span>" +
            (showError
              ? '<span class="pd__variant-error">Please select a ' +
                variant.name.toLowerCase() +
                "</span>"
              : "") +
            "</label>" +
            '<div class="pd__select">' +
            '<button type="button" class="' +
            btnClass +
            '" data-toggle="' +
            variant.name +
            '">' +
            '<span class="' +
            (selected ? "" : "pd__select-placeholder") +
            '">' +
            (selected || "Select " + variant.name) +
            "</span>" +
            I.svg("chevronDown", "icon icon-4", 1.5) +
            "</button>" +
            (isOpen ? '<div class="pd__select-menu animate-fade-in">' + options + "</div>" : "") +
            "</div></div>"
          );
        })
        .join("");
    }

    function renderTabs() {
      document.getElementById("pd-tabs").innerHTML = tabs
        .map(function (tab) {
          return (
            '<button type="button" class="pd__tab' +
            (state.tab === tab.id ? " is-active" : "") +
            '" data-tab="' +
            tab.id +
            '">' +
            I.svg(tab.icon, "icon icon-3_5", 1.5) +
            tab.label +
            "</button>"
          );
        })
        .join("");

      var panel = document.getElementById("pd-panel");
      if (state.tab === "description") {
        panel.innerHTML =
          '<p class="pd__tab-text animate-fade-in">' + product.longDescription + "</p>";
      } else if (state.tab === "specs") {
        panel.innerHTML =
          '<div class="animate-fade-in">' +
          product.specs
            .map(function (spec) {
              return (
                '<div class="pd__spec"><span class="pd__spec-label">' +
                spec.label +
                '</span><span class="pd__spec-value">' +
                spec.value +
                "</span></div>"
              );
            })
            .join("") +
          "</div>";
      } else if (state.tab === "accessories") {
        panel.innerHTML =
          '<ul class="animate-fade-in">' +
          product.accessories
            .map(function (acc) {
              return (
                '<li class="pd__acc"><div class="pd__acc-name">' +
                I.svg("package", "icon icon-3_5", 1.5) +
                "<span>" +
                acc.name +
                '</span></div><span class="pd__acc-qty">×' +
                acc.qty +
                "</span></li>"
              );
            })
            .join("") +
          "</ul>";
      } else {
        panel.innerHTML =
          '<p class="pd__tab-text animate-fade-in">' + product.compatibility + "</p>";
      }
    }

    renderGallery();
    renderVariants();
    renderTabs();

    document.getElementById("pd-thumbs").addEventListener("click", function (e) {
      var btn = e.target.closest("[data-index]");
      if (!btn) return;
      state.image = Number(btn.getAttribute("data-index"));
      renderGallery();
    });

    document.getElementById("pd-variants").addEventListener("click", function (e) {
      var toggle = e.target.closest("[data-toggle]");
      if (toggle) {
        var name = toggle.getAttribute("data-toggle");
        state.open = state.open === name ? null : name;
        renderVariants();
        return;
      }
      var option = e.target.closest("[data-option]");
      if (option) {
        state.selected[option.getAttribute("data-variant")] = option.getAttribute("data-option");
        state.open = null;
        renderVariants();
      }
    });

    document.getElementById("pd-tabs").addEventListener("click", function (e) {
      var btn = e.target.closest("[data-tab]");
      if (!btn) return;
      state.tab = btn.getAttribute("data-tab");
      renderTabs();
    });

    document.getElementById("pd-cart").addEventListener("click", function () {
      var unselected = product.variants.filter(function (v) {
        return !state.selected[v.name];
      });
      state.showValidation = unselected.length > 0;
      if (state.showValidation) renderVariants();
    });
  }
});
