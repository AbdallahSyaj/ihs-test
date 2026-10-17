(function (global) {
  var STORAGE_KEY = "aurum_cart_v1";

  function readRaw() {
    try {
      var raw = global.localStorage.getItem(STORAGE_KEY);
      if (!raw) return [];
      var parsed = JSON.parse(raw);
      return Array.isArray(parsed) ? parsed : [];
    } catch (err) {
      return [];
    }
  }

  function writeRaw(items) {
    try {
      global.localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
      global.dispatchEvent(new CustomEvent("aurum:cart-changed", { detail: { count: totalQuantity(items) } }));
    } catch (err) {
      /* ignore quota / privacy mode */
    }
  }

  function normalizeItem(item) {
    if (!item || typeof item !== "object") return null;
    var slug = typeof item.slug === "string" ? item.slug.trim() : "";
    var name = typeof item.name === "string" ? item.name.trim() : "";
    if (!slug || !name) return null;

    var quantity = parseInt(item.quantity, 10);
    if (!quantity || quantity < 1) quantity = 1;
    if (quantity > 99) quantity = 99;

    var options = item.options && typeof item.options === "object" ? item.options : {};
    var normalizedOptions = {};
    Object.keys(options).forEach(function (key) {
      if (typeof options[key] === "string" && options[key]) {
        normalizedOptions[key] = options[key];
      }
    });

    return {
      slug: slug,
      name: name,
      model: typeof item.model === "string" ? item.model : "",
      image: typeof item.image === "string" ? item.image : "",
      quantity: quantity,
      options: normalizedOptions
    };
  }

  function optionsKey(options) {
    var keys = Object.keys(options || {}).sort();
    return keys
      .map(function (k) {
        return k + ":" + options[k];
      })
      .join("|");
  }

  function lineKey(item) {
    return item.slug + "::" + optionsKey(item.options);
  }

  function totalQuantity(items) {
    return items.reduce(function (sum, item) {
      return sum + (item.quantity || 0);
    }, 0);
  }

  function getItems() {
    return readRaw()
      .map(normalizeItem)
      .filter(function (item) {
        return item !== null;
      });
  }

  function setItems(items) {
    writeRaw(
      items
        .map(normalizeItem)
        .filter(function (item) {
          return item !== null;
        })
    );
  }

  function addItem(item) {
    var next = normalizeItem(item);
    if (!next) return getItems();

    var items = getItems();
    var key = lineKey(next);
    var found = false;

    items = items.map(function (existing) {
      if (lineKey(existing) !== key) return existing;
      found = true;
      return {
        slug: existing.slug,
        name: existing.name,
        model: existing.model,
        image: existing.image || next.image,
        quantity: Math.min(99, existing.quantity + next.quantity),
        options: existing.options
      };
    });

    if (!found) items.push(next);
    setItems(items);
    return items;
  }

  function updateQuantity(slug, options, quantity) {
    var key = slug + "::" + optionsKey(options);
    var qty = parseInt(quantity, 10);
    if (!qty || qty < 1) {
      return removeItem(slug, options);
    }
    if (qty > 99) qty = 99;

    var items = getItems()
      .map(function (item) {
        if (lineKey(item) !== key) return item;
        return Object.assign({}, item, { quantity: qty });
      });
    setItems(items);
    return items;
  }

  function removeItem(slug, options) {
    var key = slug + "::" + optionsKey(options);
    var items = getItems().filter(function (item) {
      return lineKey(item) !== key;
    });
    setItems(items);
    return items;
  }

  function clear() {
    setItems([]);
  }

  global.AurumCart = {
    getItems: getItems,
    addItem: addItem,
    updateQuantity: updateQuantity,
    removeItem: removeItem,
    clear: clear,
    totalQuantity: function () {
      return totalQuantity(getItems());
    }
  };
})(window);
