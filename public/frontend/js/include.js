(function () {
  window.AurumOnReady = window.AurumOnReady || [];

  function showError(el, url, detail) {
    el.innerHTML =
      '<div class="include-error">Could not load <code>' +
      url +
      "</code>. Serve <code>resources/frontend</code> over HTTP (Laragon). " +
      (detail || "") +
      "</div>";
  }

  async function inject(el) {
    const url = el.getAttribute("data-include");
    try {
      const res = await fetch(url);
      if (!res.ok) {
        showError(el, url, "HTTP " + res.status);
        return;
      }
      el.innerHTML = await res.text();
    } catch (err) {
      showError(el, url, err && err.message ? err.message : "Network error");
    }
  }

  async function boot() {
    const slots = Array.prototype.slice.call(document.querySelectorAll("[data-include]"));
    await Promise.all(slots.map(inject));
    window.AurumOnReady.forEach(function (fn) {
      try {
        fn();
      } catch (err) {
        console.error(err);
      }
    });
    document.dispatchEvent(new CustomEvent("aurum:ready"));
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
