(function () {
    'use strict';

    

    var CART_KEY = 'okoyom_cart';
    var FAV_KEY = 'okoyom_favorites';

    function read(key) {
        try {
            return JSON.parse(localStorage.getItem(key)) || [];
        } catch (e) {
            return [];
        }
    }

    function write(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
        updateHeaderCounters();
    }

    

    function updateHeaderCounters() {
        var favCount = read(FAV_KEY).length;
        var cartCount = read(CART_KEY).length;

        
        document.querySelectorAll('.flexBtnBurger a').forEach(function (link) {
            var text = link.textContent;
            if (text.indexOf('Избранное') !== -1) {
                link.textContent = favCount ? 'Избранное (' + favCount + ')' : 'Избранное';
            }
            if (text.indexOf('Корзина') !== -1) {
                link.textContent = cartCount ? 'Корзина (' + cartCount + ')' : 'Корзина';
            }
        });
    }

    

    function toggleFavorite(id) {
        var list = read(FAV_KEY);
        var index = list.indexOf(id);
        if (index === -1) {
            list.push(id);
        } else {
            list.splice(index, 1);
        }
        write(FAV_KEY, list);
        return index === -1;
    }

    function markFavorites() {
        var list = read(FAV_KEY);
        document.querySelectorAll('[data-favorite]').forEach(function (el) {
            var id = parseInt(el.getAttribute('data-favorite'), 10);
            el.classList.toggle('is-favorite', list.indexOf(id) !== -1);
        });
    }

    document.addEventListener('click', function (event) {
        var heart = event.target.closest('[data-favorite]');
        if (!heart) return;

        event.preventDefault();
        event.stopPropagation();
        toggleFavorite(parseInt(heart.getAttribute('data-favorite'), 10));
        markFavorites();
    });

    

    
    var product = window.okoyomProduct || null;
    if (product) {
        product.id = parseInt(product.id, 10);
        product.materials = (product.materials || []).map(function (m) {
            return {
                id: parseInt(m.id, 10),
                name: m.name,
                price: parseFloat(m.price),
                seam: m.seam,
                strip: parseInt(m.strip, 10) || 0
            };
        });
        product.limits = {
            wMin: parseInt(product.limits.wMin, 10),
            wMax: parseInt(product.limits.wMax, 10),
            hMin: parseInt(product.limits.hMin, 10),
            hMax: parseInt(product.limits.hMax, 10)
        };
    }
    var currentMaterial = product && product.materials.length ? product.materials[0] : null;

    function calc() {
        if (!product || !currentMaterial) return null;

        var wInput = document.querySelector('[data-calc="w"]');
        var hInput = document.querySelector('[data-calc="h"]');
        if (!wInput || !hInput) return null;

        var w = parseInt(wInput.value, 10);
        var h = parseInt(hInput.value, 10);
        var limits = product.limits;

        var wOk = !isNaN(w) && w >= limits.wMin && w <= limits.wMax;
        var hOk = !isNaN(h) && h >= limits.hMin && h <= limits.hMax;

        wInput.style.borderColor = wOk ? '' : '#c0392b';
        hInput.style.borderColor = hOk ? '' : '#c0392b';

        var note = document.querySelector('[data-calc-error]');
        if (!wOk || !hOk) {
            if (!note) {
                note = document.createElement('p');
                note.setAttribute('data-calc-error', '');
                note.style.cssText = 'color:#c0392b;font-size:13px;margin:8px 0 0';
                hInput.closest('.flexForm-right-flex-cardSectionContent').appendChild(note);
            }
            note.textContent = 'Ширина 1–10000 см, высота 1–6000 см, шаг 1 см.';
            return null;
        }
        if (note) note.remove();

        
        var area = ((w + 5) / 100) * ((h + 5) / 100);
        var total = Math.round(area * currentMaterial.price);

        var areaEl = document.querySelector('[data-calc="area"]');
        var priceEl = document.querySelector('[data-calc="price"]');
        if (areaEl) areaEl.textContent = area.toFixed(2) + ' м²';
        if (priceEl) priceEl.textContent = total.toLocaleString('ru-RU') + ' ₽';

        return { w: w, h: h, area: area, total: total };
    }

    

    function calcBg() {
        if (!product || !currentMaterial) return null;

        var wInput = document.querySelector('[data-calc-bg="w"]');
        var hInput = document.querySelector('[data-calc-bg="h"]');
        if (!wInput || !hInput) return null;

        
        var matInput = document.querySelector('[data-calc-bg="material-input"]');
        var matLabel = document.querySelector('[data-calc-bg="material"]');
        if (matInput) matInput.value = currentMaterial.name;
        if (matLabel) matLabel.textContent = currentMaterial.name;

        var w = parseInt(wInput.value, 10);
        var h = parseInt(hInput.value, 10);
        var limits = product.limits;
        var ok = !isNaN(w) && w >= limits.wMin && w <= limits.wMax
              && !isNaN(h) && h >= limits.hMin && h <= limits.hMax;

        wInput.style.borderColor = (!isNaN(w) && w >= limits.wMin && w <= limits.wMax) ? '' : '#c0392b';
        hInput.style.borderColor = (!isNaN(h) && h >= limits.hMin && h <= limits.hMax) ? '' : '#c0392b';
        if (!ok) return null;

        var area = ((w + 5) / 100) * ((h + 5) / 100);
        var total = Math.round(area * currentMaterial.price);

        var areaEl = document.querySelector('[data-calc-bg="area"]');
        var priceEl = document.querySelector('[data-calc-bg="price"]');
        if (areaEl) areaEl.textContent = area.toFixed(2) + ' м²';
        if (priceEl) priceEl.textContent = total.toLocaleString('ru-RU') + ' ₽';

        return { w: w, h: h, area: area, total: total, material: currentMaterial.name };
    }

    document.addEventListener('input', function (event) {
        if (event.target.matches('[data-calc="w"], [data-calc="h"]')) calc();
        if (event.target.matches('[data-calc-bg="w"], [data-calc-bg="h"]')) calcBg();
    });

    
    document.addEventListener('click', function (event) {
        var item = event.target.closest('[data-material]');
        if (!item || !product) return;

        var id = parseInt(item.getAttribute('data-material'), 10);
        currentMaterial = product.materials.find(function (m) { return m.id === id; }) || currentMaterial;

        document.querySelectorAll('[data-material]').forEach(function (el) {
            el.classList.toggle('is-active', el === item);
        });
        var value = document.querySelector('.material-select__value');
        if (value) value.textContent = currentMaterial.name;

        calc();
        calcBg();
    }, true);

    
    var leadContext = null;
    document.addEventListener('click', function (event) {
        var opener = event.target.closest('[data-lead-type]');
        if (opener) leadContext = opener.getAttribute('data-lead-type');
    }, true);

    

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-add-to-cart]');
        if (!button || !product) return;

        var result = calc();
        if (!result) {
            event.preventDefault();
            return;
        }

        var cart = read(CART_KEY);
        cart.push({
            productId: product.id,
            title: product.title,
            sku: product.sku,
            url: product.url,
            image: product.image,
            w: result.w,
            h: result.h,
            material: currentMaterial.name,
            materialId: currentMaterial.id,
            area: +result.area.toFixed(2),
            price: result.total
        });
        write(CART_KEY, cart);
        
    });

    

    function money(n) {
        return n.toLocaleString('ru-RU') + ' ₽';
    }

    function renderCart() {
        var itemsWrap = document.querySelector('[data-cart-items]');
        if (!itemsWrap) return;

        var cart = read(CART_KEY);
        var emptyBlock = document.querySelector('[data-cart-empty]');
        var fullBlock = document.querySelector('[data-cart-full]');

        if (emptyBlock) emptyBlock.style.display = cart.length ? 'none' : '';
        if (fullBlock) fullBlock.style.display = cart.length ? '' : 'none';
        if (!cart.length) return;

        var template = itemsWrap.querySelector('[data-cart-item-template]');
        if (!template) return;

        itemsWrap.querySelectorAll('[data-cart-item]').forEach(function (el) { el.remove(); });

        var total = 0;
        cart.forEach(function (item, index) {
            total += item.price;
            var node = template.cloneNode(true);
            node.removeAttribute('data-cart-item-template');
            node.setAttribute('data-cart-item', index);
            node.style.display = '';

            var img = node.querySelector('img');
            if (img && item.image) img.src = item.image;

            node.querySelectorAll('[data-cart-field]').forEach(function (field) {
                var kind = field.getAttribute('data-cart-field');
                if (kind === 'title') field.textContent = item.title;
                if (kind === 'size') field.textContent = 'Размер: ' + item.w + '×' + item.h + ' см';
                if (kind === 'area') field.textContent = 'Площадь: ' + item.area.toFixed(2) + ' м²';
                if (kind === 'material') field.textContent = 'Материал: ' + item.material;
                if (kind === 'price') field.textContent = money(item.price);
            });

            var remove = node.querySelector('[data-cart-remove]');
            if (remove) {
                remove.addEventListener('click', function (event) {
                    event.preventDefault();
                    var list = read(CART_KEY);
                    list.splice(index, 1);
                    write(CART_KEY, list);
                    renderCart();
                });
            }

            template.parentNode.insertBefore(node, template);
        });

        var count = document.querySelector('[data-cart-count]');
        if (count) count.textContent = 'Товары (' + cart.length + ')';
        document.querySelectorAll('[data-cart-total]').forEach(function (el) {
            el.textContent = money(total);
        });

        var clear = document.querySelector('[data-cart-clear]');
        if (clear && !clear.hasAttribute('data-bound')) {
            clear.setAttribute('data-bound', '1');
            clear.addEventListener('click', function (event) {
                event.preventDefault();
                write(CART_KEY, []);
                renderCart();
            });
        }
    }

    

    var ATTR_KEY = 'okoyom_attr';

    function captureAttribution() {
        var saved = null;
        try { saved = JSON.parse(localStorage.getItem(ATTR_KEY)); } catch (e) {  }

        var params = new URLSearchParams(location.search);
        var keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'yclid', 'vkclid'];
        var hasMarks = keys.some(function (k) { return params.get(k); });

        if (!saved || hasMarks) {
            var attr = saved || {};
            keys.forEach(function (k) {
                if (params.get(k)) attr[k] = params.get(k);
            });
            if (!attr.referer && document.referrer) attr.referer = document.referrer;
            if (!attr.landing_page_url) attr.landing_page_url = location.href;
            localStorage.setItem(ATTR_KEY, JSON.stringify(attr));
        }
    }

    function attribution() {
        var attr = {};
        try { attr = JSON.parse(localStorage.getItem(ATTR_KEY)) || {}; } catch (e) {  }
        attr.current_page_url = location.href;
        return attr;
    }

    

    function apiRoot() {
        return (window.okoyomData && window.okoyomData.restUrl) || '/wp-json/okoyom/v1/';
    }

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('.formAllProject');
        if (!form) return;

        event.preventDefault();

        
        var type = leadContext
            || (document.querySelector('[data-cart-items]') ? 'cart_request'
                : (product ? 'product_query' : 'contact'));

        var payload = {
            type: type,
            name: (form.querySelector('[name="name"]') || {}).value || '',
            phone: (form.querySelector('[name="tel"]') || {}).value || '',
            message: (form.querySelector('[name="text"]') || {}).value || '',
            cart: read(CART_KEY),
            attribution: attribution()
        };
        if (product) {
            payload.product_id = product.id;
            payload.sku = product.sku;
            payload.product_url = product.url;
        }

        
        if (type === 'companion_request') {
            var bg = calcBg();
            if (bg) {
                payload.message = (payload.message ? payload.message + '\n' : '')
                    + 'Фоновые обои: ' + bg.w + '×' + bg.h + ' см, '
                    + bg.area.toFixed(2) + ' м², ' + bg.material + ', '
                    + bg.total.toLocaleString('ru-RU') + ' ₽';
            }
        }

        var button = form.querySelector('button[type="submit"]');
        if (button) button.disabled = true;

        fetch(apiRoot() + 'lead', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data && data.ok) {
                window.location.href = '/thanks/';
                return;
            }
            if (button) button.disabled = false;
            alert((data && data.error) || 'Не удалось отправить. Попробуйте ещё раз.');
        }).catch(function () {
            if (button) button.disabled = false;
            alert('Не удалось отправить. Проверьте соединение.');
        });
    });

    

    function renderFavorites() {
        var grid = document.querySelector('[data-fav-grid]');
        if (!grid) return;

        var list = read(FAV_KEY);
        var shown = 0;
        grid.querySelectorAll('.blockCardCatalog__card').forEach(function (card) {
            var id = parseInt(card.getAttribute('data-product-id'), 10);
            var keep = list.indexOf(id) !== -1;
            card.style.display = keep ? '' : 'none';
            if (keep) shown++;
        });

        var emptyBlock = document.querySelector('[data-fav-empty]');
        var fullBlock = document.querySelector('[data-fav-full]');
        if (emptyBlock) emptyBlock.style.display = shown ? 'none' : '';
        if (fullBlock) fullBlock.style.display = shown ? '' : 'none';
    }

    
    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-favorite]') && document.querySelector('[data-fav-grid]')) {
            setTimeout(renderFavorites, 50);
        }
    });

    

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') return;
        var input = event.target;
        if (!input.matches('input[type="search"], input[placeholder*="артикул"], input[placeholder="Поиск"]')) return;

        event.preventDefault();
        var query = input.value.trim();
        if (query) window.location.href = '/search/?q=' + encodeURIComponent(query);
    });

    function hideDeadMoreButtons() {
        document.querySelectorAll('a, button').forEach(function (el) {
            var t = el.textContent.trim().toLowerCase();
            if (t === 'смотреть ещё' || t === 'смотреть еще') {
                el.style.display = 'none';
            }
        });
    }

    document.addEventListener('click', function (event) {
        var lens = event.target.closest('.filterModalOpen, [class*="searchIcon"]');
        if (!lens) return;
        var panel = lens.closest('.mfilter');
        var input = (panel || document).querySelector('input[type="search"], input[placeholder*="артикул"], input[placeholder="Поиск"]');
        if (input) {
            var query = input.value.trim();
            if (query) {
                event.preventDefault();
                window.location.href = '/search/?q=' + encodeURIComponent(query);
            }
        }
    });

    function currentFilters() {
        var params = new URLSearchParams(location.search);
        var state = {};
        ['collection', 'series', 'subject', 'color'].forEach(function (p) {
            var v = params.get(p);
            if (v) state[p] = v.split(',').filter(Boolean);
        });
        return state;
    }

    function applyFilters(state) {
        var params = new URLSearchParams();
        Object.keys(state).forEach(function (p) {
            if (state[p] && state[p].length) params.set(p, state[p].join(','));
        });
        var qs = params.toString();
        window.location.href = '/catalog/' + (qs ? '?' + qs : '');
    }

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-filter-value]');
        if (!btn) return;
        var group = btn.closest('[data-filter-group]');
        if (!group) return;
        event.preventDefault();

        var param = group.getAttribute('data-filter-group');
        var value = btn.getAttribute('data-filter-value');
        var state = currentFilters();

        if (value === '') {
            delete state[param];
        } else {
            var list = state[param] || [];
            var idx = list.indexOf(value);
            if (idx === -1) list.push(value); else list.splice(idx, 1);
            state[param] = list;
        }
        applyFilters(state);
    });

    document.addEventListener('click', function (event) {
        var reset = event.target.closest('.mfilter__reset, [class*="mfilter-reset"]');
        if (!reset) return;
        var t = reset.textContent.trim().toLowerCase();
        if (t.indexOf('сброс') === -1) return;
        event.preventDefault();
        window.location.href = '/catalog/';
    });

    document.addEventListener('DOMContentLoaded', function () {
        captureAttribution();
        updateHeaderCounters();
        markFavorites();
        renderCart();
        renderFavorites();
        hideDeadMoreButtons();

        
        var params = new URLSearchParams(location.search);
        var q = params.get('q');
        if (q && location.pathname.indexOf('/search') === 0) {
            document.querySelectorAll('input[type="search"], input[placeholder*="артикул"], input[placeholder="Поиск"]').forEach(function (el) {
                el.value = q;
            });
        }
    });
})();
