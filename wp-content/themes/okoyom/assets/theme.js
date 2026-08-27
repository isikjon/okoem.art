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

        
        var area = (w / 100) * (h / 100);
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

        var area = (w / 100) * (h / 100);
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
        if (!event.target.closest('.pinterest-item')) return;
        var bar = window.innerWidth - document.documentElement.clientWidth;
        if (bar > 0) document.body.style.paddingRight = bar + 'px';
    }, true);

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.gallery-popup__close, .gallery-popup__bg')) return;
        setTimeout(function () { document.body.style.paddingRight = ''; }, 30);
    }, true);

    document.addEventListener('click', function (event) {
        var tile = event.target.closest('.pinterest-item');
        if (!tile) return;
        var popup = document.getElementById('galleryPopup');
        if (!popup) return;

        var title = tile.querySelector('.pinterest-title');
        var subtitle = tile.querySelector('.pinterest-subtitle');
        var pTitle = popup.querySelector('.gallery-popup__title');
        var pSub = popup.querySelector('.gallery-popup__subtitle');
        var pLink = popup.querySelector('.gallery-popup__link');
        if (pTitle) pTitle.textContent = title ? title.textContent.trim() : '';
        if (pSub) pSub.textContent = subtitle ? subtitle.textContent.trim() : '';

        var url = tile.getAttribute('data-product-url');
        if (pLink) {
            if (url) {
                pLink.setAttribute('href', url);
                pLink.style.display = '';
                if (!pLink.textContent.trim()) pLink.textContent = 'Перейти к товару';
            } else {
                pLink.style.display = 'none';
            }
        }
    });

    document.addEventListener('click', function (event) {
        var popup = document.getElementById('galleryPopup');
        if (!popup || !popup.classList.contains('active')) return;
        if (!event.target.closest('#galleryPopup')) return;
        if (event.target.closest('.gallery-popup__image, .gallery-popup__info, .gallery-popup__close')) return;
        popup.classList.remove('active');
        var y = parseInt(document.body.dataset.scrollY || '0', 10) || 0;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        window.scrollTo(0, y);
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.openModal, .openModal2, [data-lead-type]')) return;
        try {
            sessionStorage.setItem('okoyom_return_to', location.pathname + location.search);
            sessionStorage.setItem('okoyom_return_scroll', String(window.scrollY));
        } catch (e) {}
    }, true);

    document.addEventListener('click', function (event) {
        var back = event.target.closest('[data-back]');
        if (!back) return;
        event.preventDefault();
        var to = null;
        try { to = sessionStorage.getItem('okoyom_return_to'); } catch (e) {}
        if (to) {
            try { sessionStorage.setItem('okoyom_restore_scroll', '1'); } catch (e) {}
            window.location.href = to;
        } else {
            window.location.href = '/catalog/';
        }
    });

    document.addEventListener('mouseover', function (event) {
        var dot = event.target.closest('[data-color-version]');
        if (!dot) return;
        var title = dot.getAttribute('data-color-title');
        var wrap = dot.closest('.flexColorsCards');
        var titleEl = wrap ? wrap.querySelector('p') : null;
        if (titleEl && title) titleEl.textContent = title;
    });

    document.addEventListener('click', function (event) {
        var dot = event.target.closest('[data-color-version]');
        if (!dot) return;

        var url = dot.getAttribute('data-color-url');
        if (url) {
            window.location.href = url;
            return;
        }

        document.querySelectorAll('[data-color-version]').forEach(function (d) {
            d.classList.toggle('block-flexColorsCards__active', d === dot);
        });

        var title = dot.getAttribute('data-color-title');
        var wrap = dot.closest('.flexColorsCards');
        var titleEl = wrap ? wrap.querySelector('p') : null;
        if (titleEl && title) titleEl.textContent = title;

        var image = dot.getAttribute('data-color-image');
        if (image) {
            var mainImg = document.querySelector('.swiper-slide img');
            if (mainImg) mainImg.src = image;
        }
    });

    

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
        if (catIsCatalogPage()) { catRunSearch(input.value, true); return; }
        goToSearch(input.value.trim());
    });

    function catIsCatalogPage() {
        return !!document.querySelector('.flexFiltersCatalog') && !!catGrid();
    }

    function catRunSearch(value, doScroll) {
        catSearch = (value || '').trim().toLowerCase();
        catApply(doScroll);
    }

    document.addEventListener('input', function (event) {
        if (!catIsCatalogPage()) return;
        if (!event.target.matches('input[type="search"], input[placeholder*="артикул"], input[placeholder="Поиск"]')) return;
        catRunSearch(event.target.value, false);
    });

    function goToSearch(query) {
        if (query) {
            window.location.href = '/search/?q=' + encodeURIComponent(query);
            return;
        }
        if (location.pathname.indexOf('/search') === 0) window.location.href = '/catalog/';
    }

    document.addEventListener('search', function (event) {
        if (!event.target.matches('input[type="search"], input[placeholder*="артикул"], input[placeholder="Поиск"]')) return;
        if (catIsCatalogPage()) { catRunSearch(event.target.value, true); return; }
        goToSearch(event.target.value.trim());
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
            event.preventDefault();
            if (catIsCatalogPage()) { catRunSearch(input.value, true); return; }
            goToSearch(input.value.trim());
        }
    });

    var CAT_GROUPS = ['collection', 'series', 'subject', 'color'];

    function catStateFromUrl() {
        var params = new URLSearchParams(location.search);
        var state = {};
        CAT_GROUPS.forEach(function (p) {
            var v = params.get(p);
            state[p] = v ? v.split(',').filter(Boolean) : [];
        });
        return state;
    }

    var catPending = catStateFromUrl();
    var catSearch = '';

    function catGrid() {
        return document.querySelector('.tab-content__item.active .flexTwoTypeInfoMain-2')
            || document.querySelector('.flexTwoTypeInfoMain-2');
    }

    function catFiltersRow() {
        return document.querySelector('.flexFiltersCatalog');
    }

    function catWord(n) {
        var t2 = n % 100, t1 = n % 10;
        if (t2 >= 11 && t2 <= 14) return 'работ';
        if (t1 === 1) return 'работа';
        if (t1 >= 2 && t1 <= 4) return 'работы';
        return 'работ';
    }

    function catRowStuck() {
        var row = catFiltersRow();
        if (!row) return false;
        var top = parseInt(getComputedStyle(row).top, 10) || 0;
        return row.getBoundingClientRect().top <= top + 1;
    }

    function catScrollToTop() {
        var row = catFiltersRow();
        if (!row) return;
        var stickyTop = parseInt(getComputedStyle(row).top, 10) || 0;
        var anchor = document.querySelector('.catFilterAnchor');
        var docTop = anchor
            ? anchor.getBoundingClientRect().top + window.scrollY
            : row.getBoundingClientRect().top + window.scrollY;
        window.scrollTo({ top: Math.max(0, docTop - stickyTop) });
    }

    function catUpdateReset() {
        var reset = document.querySelector('.catResetFilters');
        if (!reset) return;
        var any = CAT_GROUPS.some(function (k) { return catPending[k] && catPending[k].length; });
        reset.classList.toggle('is-visible', any);
    }

    function catApply(doScroll) {
        var grid = catGrid();
        if (!grid) return;
        var cards = grid.querySelectorAll('.blockCardCatalog__card');
        var shown = 0;
        cards.forEach(function (card) {
            var ok = CAT_GROUPS.every(function (g) {
                if (!catPending[g] || !catPending[g].length) return true;
                var vals = (card.getAttribute('data-' + g) || '').split(',').filter(Boolean);
                return catPending[g].some(function (v) { return vals.indexOf(v) !== -1; });
            });

            if (ok && catSearch) {
                var hay = (card.getAttribute('data-title') || '') + ' ' + (card.getAttribute('data-sku') || '').toLowerCase();
                ok = hay.indexOf(catSearch) !== -1;
            }
            card.style.display = ok ? '' : 'none';
            if (ok) shown++;
        });

        var countEl = document.querySelector('.textSpanQuantityCatalog');
        if (countEl) countEl.textContent = shown + ' ' + catWord(shown);

        var empty = grid.querySelector('.catEmpty');
        if (0 === shown) {
            if (!empty) {
                empty = document.createElement('p');
                empty.className = 'textTitleSection catEmpty';
                empty.textContent = 'Ничего не найдено. Попробуйте изменить фильтры.';
                grid.appendChild(empty);
            }
            empty.style.display = '';
        } else if (empty) {
            empty.style.display = 'none';
        }

        var params = new URLSearchParams(location.search);
        CAT_GROUPS.forEach(function (g) {
            if (catPending[g] && catPending[g].length) params.set(g, catPending[g].join(','));
            else params.delete(g);
        });
        var qs = params.toString();
        history.replaceState(null, '', location.pathname + (qs ? '?' + qs : ''));

        catUpdateReset();

        if (doScroll && catRowStuck()) catScrollToTop();
    }

    var FILTER_HOMOGLYPHS = { 'c': 'с', 'e': 'е', 'o': 'о', 'a': 'а', 'p': 'р', 'y': 'у', 'x': 'х', 'k': 'к', 'm': 'м', 'h': 'н', 't': 'т', 'b': 'в' };

    function normalizeLabel(text) {
        return text.trim().toLowerCase().replace(':', '').replace(/[a-z]/g, function (ch) {
            return FILTER_HOMOGLYPHS[ch] || ch;
        });
    }

    function catBuildInlineFilters() {
        var data = window.okoyomCatFilters;
        if (!data || !data.maps) return;
        var keyByLabel = { 'коллекция': 'collection', 'серия': 'series', 'сюжет': 'subject', 'цвет': 'color' };

        CAT_GROUPS.forEach(function (k) {
            if (!catPending[k]) catPending[k] = [];
        });

        var row = catFiltersRow();
        if (row && !document.querySelector('.catFilterAnchor')) {
            var anchor = document.createElement('div');
            anchor.className = 'catFilterAnchor';
            row.parentNode.insertBefore(anchor, row);
        }

        function refreshLabel(panel, key, valueEl) {
            if (!valueEl) return;
            var names = catPending[key].map(function (s) { return data.maps[key][s]; });
            valueEl.textContent = names.length ? names.join(', ') : 'Все';
        }

        document.querySelectorAll('.ui-filter').forEach(function (panel) {
            var labelEl = panel.querySelector('.ui-filter__label');
            if (!labelEl) return;
            var key = keyByLabel[normalizeLabel(labelEl.textContent)];
            if (!key || !data.maps[key]) return;

            var list = panel.querySelector('.ui-filter__list');
            var valueEl = panel.querySelector('.ui-filter__value');
            if (!list) return;

            var isColor = key === 'color' && panel.classList.contains('ui-filter-2');

            var html = '<button class="ui-filter__item ui-filter__item--all' + (catPending[key].length ? '' : ' is-active') + '" data-value=""><span>Все</span><span class="ui-filter__check"></span></button>';
            Object.keys(data.maps[key]).forEach(function (slug, index) {
                var on = catPending[key].indexOf(slug) !== -1;
                var name = data.maps[key][slug];
                if (isColor) {
                    var hex = data.swatches ? data.swatches[slug] : '';
                    var circle = hex
                        ? '<span class="circleFilter" style="background:' + hex + ';border:1px solid ' + hex + '"></span>'
                        : '<span class="circleFilter circleFilter-' + (index % 13 + 1) + '"></span>';
                    html += '<button class="ui-filter__item' + (on ? ' is-active' : '') + '" type="button" data-value="' + slug + '" title="' + name + '">' + circle + '<span class="ui-filter__check"></span></button>';
                } else {
                    html += '<button class="ui-filter__item' + (on ? ' is-active' : '') + '" type="button" data-value="' + slug + '"><span>' + name + '</span><span class="ui-filter__check"></span></button>';
                }
            });
            html += '<button class="ui-filter__collapse" type="button">Свернуть <i></i></button>';
            list.innerHTML = html;
            refreshLabel(panel, key, valueEl);

            var collapseBtn = list.querySelector('.ui-filter__collapse');
            if (collapseBtn) {
                collapseBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    panel.classList.remove('is-open');
                });
            }

            list.querySelectorAll('.ui-filter__item').forEach(function (item) {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var value = item.getAttribute('data-value');

                    if (value === '') {
                        catPending[key] = [];
                    } else {
                        var i = catPending[key].indexOf(value);
                        if (i === -1) catPending[key].push(value); else catPending[key].splice(i, 1);
                    }

                    list.querySelectorAll('.ui-filter__item').forEach(function (it) {
                        var v = it.getAttribute('data-value');
                        if (v === null) return;
                        it.classList.toggle('is-active', v === '' ? catPending[key].length === 0 : catPending[key].indexOf(v) !== -1);
                    });
                    refreshLabel(panel, key, valueEl);
                    catApply(true);
                });
            });
        });

        var colorPanel = document.querySelector('.ui-filter-2');
        if (colorPanel && !document.querySelector('.catResetFilters')) {
            var resetBtn = document.createElement('button');
            resetBtn.type = 'button';
            resetBtn.className = 'catResetFilters';
            resetBtn.innerHTML = '<svg viewBox="0 0 14 14" fill="none"><path d="M3.5 3.5l7 7M10.5 3.5l-7 7" stroke="#6B6B6B" stroke-width="1.5" stroke-linecap="round"/></svg> Сбросить фильтрацию';
            colorPanel.insertAdjacentElement('afterend', resetBtn);
            resetBtn.addEventListener('click', function () {
                CAT_GROUPS.forEach(function (k) { catPending[k] = []; });
                document.querySelectorAll('.ui-filter__item').forEach(function (it) {
                    var v = it.getAttribute('data-value');
                    if (v !== null) it.classList.toggle('is-active', v === '');
                });
                document.querySelectorAll('.ui-filter__value').forEach(function (v) { v.textContent = 'Все'; });
                catApply(true);
            });
        }

        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('.ui-filter__trigger');
            if (trigger) {
                var panel = trigger.closest('.ui-filter');
                var open = panel.classList.contains('is-open');
                document.querySelectorAll('.ui-filter').forEach(function (p) { p.classList.remove('is-open'); });
                if (!open) panel.classList.add('is-open');
                return;
            }
            if (!e.target.closest('.ui-filter__dropdown')) {
                document.querySelectorAll('.ui-filter').forEach(function (p) { p.classList.remove('is-open'); });
            }
        });

        catApply(false);
    }

    document.addEventListener('click', function (event) {
        var show = event.target.closest('.mfilter-show');
        if (show) {
            event.preventDefault();
            var modal = show.closest('.mfilter');
            if (modal) modal.classList.remove('active');
            document.body.style.overflow = '';
            return;
        }
    });

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-filter-value]');
        if (!btn) return;
        var group = btn.closest('[data-filter-group]');
        if (!group) return;
        event.preventDefault();

        var param = group.getAttribute('data-filter-group');
        var value = btn.getAttribute('data-filter-value');
        if (!catPending[param]) catPending[param] = [];

        if (value === '') {
            catPending[param] = [];
        } else {
            var idx = catPending[param].indexOf(value);
            if (idx === -1) catPending[param].push(value); else catPending[param].splice(idx, 1);
        }
        group.querySelectorAll('[data-filter-value]').forEach(function (b) {
            var v = b.getAttribute('data-filter-value');
            b.classList.toggle('active', v === '' ? catPending[param].length === 0 : catPending[param].indexOf(v) !== -1);
        });
        catApply(true);
    });

    document.addEventListener('click', function (event) {
        var reset = event.target.closest('.mfilter__reset, [class*="mfilter-reset"]');
        if (!reset) return;
        var t = reset.textContent.trim().toLowerCase();
        if (t.indexOf('сброс') === -1) return;
        event.preventDefault();
        CAT_GROUPS.forEach(function (g) { catPending[g] = []; });
        document.querySelectorAll('.ui-filter__item, [data-filter-value]').forEach(function (b) {
            b.classList.toggle('is-active', b.getAttribute('data-value') === '');
            b.classList.toggle('active', b.getAttribute('data-filter-value') === '');
        });
        document.querySelectorAll('.ui-filter__value').forEach(function (v) { v.textContent = 'Все'; });
        catApply(true);
    });

    var inspState = { collection: [], color: [], subject: [] };

    function inspApply() {
        var tiles = document.querySelectorAll('.pinterest-item');
        var shown = 0;
        tiles.forEach(function (tile) {
            var ok = ['collection', 'color', 'subject'].every(function (k) {
                if (!inspState[k].length) return true;
                var vals = (tile.getAttribute('data-' + k) || '').split(' ');
                return inspState[k].some(function (v) { return vals.indexOf(v) !== -1; });
            });
            tile.style.display = ok ? '' : 'none';
            if (ok) shown++;
        });
        var counter = document.querySelector('.textSpanQuantityCatalog');
        if (counter) {
            var w = shown % 10, w2 = shown % 100, word;
            if (w2 >= 11 && w2 <= 14) word = 'объектов';
            else if (w === 1) word = 'объект';
            else if (w >= 2 && w <= 4) word = 'объекта';
            else word = 'объектов';
            counter.textContent = shown + ' ' + word;
        }
    }

    function inspBuildPanels() {
        var maps = window.okoyomInspFilters;
        if (!maps) return;
        var keyByLabel = { 'коллекция': 'collection', 'цвет': 'color', 'сюжет': 'subject' };

        document.querySelectorAll('.ui-filter').forEach(function (panel) {
            var labelEl = panel.querySelector('.ui-filter__label');
            if (!labelEl) return;
            var key = keyByLabel[normalizeLabel(labelEl.textContent)];
            if (!key || !maps[key]) return;

            var list = panel.querySelector('.ui-filter__list');
            var valueEl = panel.querySelector('.ui-filter__value');
            if (!list) return;

            var isColor = key === 'color' && panel.classList.contains('ui-filter-2');
            var html = '<button class="ui-filter__item ui-filter__item--all is-active" data-value=""><span>Все</span><span class="ui-filter__check"></span></button>';
            Object.keys(maps[key]).forEach(function (slug, index) {
                var name = maps[key][slug];
                if (isColor) {
                    var hex = maps.swatches ? maps.swatches[slug] : '';
                    var circle = hex
                        ? '<span class="circleFilter" style="background:' + hex + ';border:1px solid ' + hex + '"></span>'
                        : '<span class="circleFilter circleFilter-' + (index % 13 + 1) + '"></span>';
                    html += '<button class="ui-filter__item" type="button" data-value="' + slug + '" title="' + name + '">' + circle + '<span class="ui-filter__check"></span></button>';
                } else {
                    html += '<button class="ui-filter__item" type="button" data-value="' + slug + '"><span>' + name + '</span><span class="ui-filter__check"></span></button>';
                }
            });
            list.innerHTML = html;

            list.querySelectorAll('.ui-filter__item').forEach(function (item) {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var value = item.getAttribute('data-value');
                    if (value === '') {
                        inspState[key] = [];
                    } else {
                        var i = inspState[key].indexOf(value);
                        if (i === -1) inspState[key].push(value); else inspState[key].splice(i, 1);
                    }
                    list.querySelectorAll('.ui-filter__item').forEach(function (it) {
                        var v = it.getAttribute('data-value');
                        it.classList.toggle('is-active', v === '' ? inspState[key].length === 0 : inspState[key].indexOf(v) !== -1);
                    });
                    if (valueEl) {
                        var names = inspState[key].map(function (s) { return maps[key][s]; });
                        valueEl.textContent = names.length ? names.join(', ') : 'Все';
                    }
                    inspApply();
                });
            });
        });

        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('.ui-filter__trigger');
            if (trigger) {
                var panel = trigger.closest('.ui-filter');
                var open = panel.classList.contains('is-open');
                document.querySelectorAll('.ui-filter').forEach(function (p) { p.classList.remove('is-open'); });
                if (!open) panel.classList.add('is-open');
                return;
            }
            if (!e.target.closest('.ui-filter__dropdown')) {
                document.querySelectorAll('.ui-filter').forEach(function (p) { p.classList.remove('is-open'); });
            }
        });
    }

    document.addEventListener('click', function (event) {
        var main = event.target.closest('.muralGalleryMain');
        if (!main || !main.swiper) return;
        if (event.target.closest('a, button')) return;
        var rect = main.getBoundingClientRect();
        if (event.clientX - rect.left < rect.width / 2) {
            main.swiper.slidePrev();
        } else {
            main.swiper.slideNext();
        }
    });

    function catSlowBanners() {
        document.querySelectorAll('.mural-hero__slider').forEach(function (el) {
            if (el.swiper && el.swiper.autoplay) {
                el.swiper.params.autoplay.delay = 3000;
                el.swiper.autoplay.stop();
                el.swiper.autoplay.start();
            }
        });
    }

    (function () {
        var modal = document.querySelector('.mfilter');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    })();

    (function () {
        var toggle = document.getElementById('menu__toggle');
        var btn = document.querySelector('.menu__btn');
        if (!toggle || !btn) return;

        var guard = 0;
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var now = Date.now();
            if (now - guard < 400) return;
            guard = now;
            toggle.checked = !toggle.checked;
            document.body.classList.toggle('menu-open', toggle.checked);
        });

        var box = document.querySelector('.menu__box');
        if (box) {
            box.addEventListener('click', function (e) {
                if (e.target.closest('a')) {
                    toggle.checked = false;
                    document.body.classList.remove('menu-open');
                }
            });
        }
    })();

    document.addEventListener('DOMContentLoaded', function () {
        captureAttribution();
        updateHeaderCounters();
        markFavorites();
        renderCart();
        renderFavorites();
        hideDeadMoreButtons();
        inspBuildPanels();
        catBuildInlineFilters();
        setTimeout(catSlowBanners, 300);

        try {
            if (sessionStorage.getItem('okoyom_restore_scroll') === '1') {
                var savedY = parseInt(sessionStorage.getItem('okoyom_return_scroll') || '0', 10);
                sessionStorage.removeItem('okoyom_restore_scroll');
                window.scrollTo(0, savedY);
            }
        } catch (e) {}

        
        var params = new URLSearchParams(location.search);
        var q = params.get('q');
        if (q && location.pathname.indexOf('/search') === 0) {
            document.querySelectorAll('input[type="search"], input[placeholder*="артикул"], input[placeholder="Поиск"]').forEach(function (el) {
                el.value = q;
            });
        }
    });
})();
