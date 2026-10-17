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

  var header = document.querySelector(".site-header");
  var menuToggle = document.getElementById("site-menu-toggle");
  var backdrop = document.getElementById("site-nav-backdrop");
  var desktopNavQuery = window.matchMedia("(min-width: 1024px)");

  function isMobileNav() {
    return !desktopNavQuery.matches;
  }

  function setMenuOpen(open) {
    if (!header || !menuToggle) return;

    var shouldOpen = open && isMobileNav();
    header.classList.toggle("is-menu-open", shouldOpen);
    menuToggle.setAttribute("aria-expanded", shouldOpen ? "true" : "false");
    document.body.classList.toggle("site-menu-open", shouldOpen);

    if (backdrop) {
      backdrop.hidden = !shouldOpen;
    }
  }

  function closeMenu() {
    setMenuOpen(false);
  }

  function toggleMenu() {
    if (!header) return;
    setMenuOpen(!header.classList.contains("is-menu-open"));
  }

  if (menuToggle && header) {
    menuToggle.addEventListener("click", function (e) {
      e.stopPropagation();
      toggleMenu();
    });
  }

  if (backdrop) {
    backdrop.addEventListener("click", closeMenu);
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeMenu();
  });

  for (var j = 0; j < links.length; j++) {
    links[j].addEventListener("click", function () {
      if (isMobileNav()) closeMenu();
    });
  }

  if (typeof desktopNavQuery.addEventListener === "function") {
    desktopNavQuery.addEventListener("change", closeMenu);
  } else if (typeof desktopNavQuery.addListener === "function") {
    desktopNavQuery.addListener(closeMenu);
  }
});
