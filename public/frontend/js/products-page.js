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

  if (!chipsEl || !gridEl || !countEl || !emptyEl || !searchEl || !clearBtn) return;

  var state = { category: "all", query: "" };
  var cards = Array.prototype.slice.call(gridEl.querySelectorAll(".product-card"));

  function cardMatches(card) {
    var category = card.getAttribute("data-category") || "";
    var catOk = state.category === "all" || category === state.category;
    if (!catOk) return false;

    var q = state.query.toLowerCase();
    if (q === "") return true;

    var nameEl = card.querySelector(".product-card__name");
    var modelEl = card.querySelector(".product-card__model");
    var descEl = card.querySelector(".product-card__desc");
    var name = nameEl ? nameEl.textContent.toLowerCase() : "";
    var model = modelEl ? modelEl.textContent.toLowerCase() : "";
    var desc = descEl ? descEl.textContent.toLowerCase() : "";

    return name.indexOf(q) !== -1 || model.indexOf(q) !== -1 || desc.indexOf(q) !== -1;
  }

  function render() {
    var visible = 0;
    cards.forEach(function (card) {
      var show = cardMatches(card);
      card.hidden = !show;
      if (show) visible += 1;
    });

    countEl.textContent = visible + (visible === 1 ? " item" : " items");

    chipsEl.querySelectorAll("[data-category]").forEach(function (btn) {
      btn.classList.toggle("is-active", btn.getAttribute("data-category") === state.category);
    });

    if (visible) {
      emptyEl.hidden = true;
      gridEl.hidden = false;
    } else {
      gridEl.hidden = true;
      emptyEl.hidden = false;
    }
  }

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
});
