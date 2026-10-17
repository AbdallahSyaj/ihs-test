window.AurumOnReady = window.AurumOnReady || [];

window.AurumOnReady.push(function initCartBadge() {
  var badge = document.getElementById("site-cart-badge");
  if (!badge || !window.AurumCart) return;

  function sync() {
    var count = window.AurumCart.totalQuantity();
    badge.textContent = count > 99 ? "99+" : String(count);
    badge.hidden = count < 1;
  }

  sync();
  window.addEventListener("aurum:cart-changed", sync);
});
