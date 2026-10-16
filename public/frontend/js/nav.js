window.AurumOnReady = window.AurumOnReady || [];

window.AurumOnReady.push(function initNav() {
  var page = document.body.getAttribute("data-page") || "";
  var links = document.querySelectorAll("[data-nav]");
  for (var i = 0; i < links.length; i++) {
    var key = links[i].getAttribute("data-nav");
    var active = key === page || (key === "products" && page === "product-details");
    if (active) links[i].classList.add("nav-link-active");
    else links[i].classList.remove("nav-link-active");
  }

  var year = document.getElementById("footer-year");
  if (year) year.textContent = String(new Date().getFullYear());
});
