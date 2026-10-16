window.AurumData = {
  load: function () {
    var url = (window.AurumPaths && window.AurumPaths.catalog) || "data/products.json";
    return fetch(url).then(function (res) {
      if (!res.ok) {
        throw new Error("Failed to load product data (HTTP " + res.status + ")");
      }
      return res.json();
    });
  },
  fallbackId: "aurum-pro-x1"
};
