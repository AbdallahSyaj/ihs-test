window.AurumData = {
  load: function () {
    return fetch("data/products.json").then(function (res) {
      if (!res.ok) {
        throw new Error("Failed to load product data (HTTP " + res.status + ")");
      }
      return res.json();
    });
  },
  fallbackId: "aurum-pro-x1"
};
