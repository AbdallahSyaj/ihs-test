window.AurumOnReady = window.AurumOnReady || [];

window.AurumOnReady.push(function initProductsPage() {
  var root = document.getElementById("products-app");
  if (!root) return;

  var chipsEl = document.getElementById("filter-chips");
  var gridEl = document.getElementById("products-grid");
  var countEl = document.getElementById("products-count");
  var emptyEl = document.getElementById("products-empty");
  var searchEl = document.getElementById("products-search");
  var clearBtn = document.getElementById("clear-filters");

  var state = { category: "all", query: "" };
  var products = [];
  var categories = [];

  function card(product, index) {
    var badge = product.badge
      ? '<span class="badge-gold">' + product.badge + "</span>"
      : "";
    return (
      '<a class="product-card" href="product-details.html?id=' +
      encodeURIComponent(product.id) +
      '" style="animation-delay:' +
      index * 80 +
      'ms">' +
      '<div class="product-card__image">' +
      '<img src="' +
      product.image +
      '" alt="' +
      product.name +
      '" loading="lazy">' +
      badge +
      "</div>" +
      '<div class="product-card__body">' +
      '<span class="product-card__cat">' +
      product.categoryLabel +
      "</span>" +
      ' <h3 class="product-card__name">' +
      product.name +
      "</h3>" +
      '<p class="product-card__model">Model ' +
      product.model +
      "</p>" +
      '<p class="product-card__desc">' +
      product.description +
      "</p>" +
      '<div class="product-card__footer">' +
      '<span class="product-card__price">' +
      product.price +
      "</span>" +
      '<span class="product-card__cta">View Details →</span>' +
      "</div></div></a>"
    );
  }

  function filtered() {
    var q = state.query.toLowerCase();
    return products.filter(function (p) {
      var cat = state.category === "all" || p.category === state.category;
      var search =
        q === "" ||
        p.name.toLowerCase().includes(q) ||
        p.model.toLowerCase().includes(q) ||
        p.description.toLowerCase().includes(q);
      return cat && search;
    });
  }

  function render() {
    var list = filtered();
    countEl.textContent = list.length + (list.length === 1 ? " item" : " items");
    chipsEl.querySelectorAll("[data-category]").forEach(function (btn) {
      btn.classList.toggle("is-active", btn.getAttribute("data-category") === state.category);
    });
    if (list.length) {
      emptyEl.hidden = true;
      gridEl.hidden = false;
      gridEl.innerHTML = list.map(card).join("");
    } else {
      gridEl.hidden = true;
      gridEl.innerHTML = "";
      emptyEl.hidden = false;
    }
  }

  window.AurumData.load()
    .then(function (data) {
      products = data.products;
      categories = [{ id: "all", label: "All Equipment" }].concat(data.categories);
      chipsEl.innerHTML = categories
        .map(function (cat) {
          return (
            '<button type="button" class="products-filters__chip" data-category="' +
            cat.id +
            '">' +
            cat.label +
            "</button>"
          );
        })
        .join("");

      chipsEl.addEventListener("click", function (e) {
        var btn = e.target.closest("[data-category]");
        if (!btn) return;
        state.category = btn.getAttribute("data-category");
        render();
      });

      searchEl.addEventListener("input", function () {
        state.query = searchEl.value;
        render();
      });

      clearBtn.addEventListener("click", function () {
        state.category = "all";
        state.query = "";
        searchEl.value = "";
        render();
      });

      render();
    })
    .catch(function (err) {
      countEl.textContent = "Unable to load catalog.";
      console.error(err);
    });
});
