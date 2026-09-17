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
    var catSort = 'default';
    var catInitialPath = location.pathname;
    var catIsSearch = catInitialPath.indexOf('/search') === 0;

    function catCanonicalUrl(params) {
        var qs = params.toString();
        if (catIsSearch) {
            return catInitialPath + (qs ? '?' + qs : '');
        }
        return '/catalog/' + (qs ? '?' + qs : '');
    }

    function catReorder(grid) {
        var cards = Array.prototype.slice.call(grid.querySelectorAll('.blockCardCatalog__card'));
        cards.forEach(function (card, i) {
            if (null === card.getAttribute('data-order')) card.setAttribute('data-order', String(i));
        });
        cards.sort(function (a, b) {
            if ('new' === catSort) {
                return (parseInt(b.getAttribute('data-date'), 10) || 0) - (parseInt(a.getAttribute('data-date'), 10) || 0);
            }
            return (parseInt(a.getAttribute('data-order'), 10) || 0) - (parseInt(b.getAttribute('data-order'), 10) || 0);
        });
        cards.forEach(function (card) { grid.appendChild(card); });
    }

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

        catReorder(grid);

        var countEl = document.querySelector('.textSpanQuantityCatalog');
        if (countEl) countEl.textContent = shown + ' ' + catWord(shown);

        var showBtn = document.querySelector('.mfilter-show');
        if (showBtn) showBtn.textContent = 'ПОКАЗАТЬ (' + shown + ')';

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

        var params = new URLSearchParams(catIsSearch ? location.search : '');
        CAT_GROUPS.forEach(function (g) {
            if (catPending[g] && catPending[g].length) params.set(g, catPending[g].join(','));
            else params.delete(g);
        });
        history.replaceState(null, '', catCanonicalUrl(params));

        catUpdateReset();

        if (doScroll && catRowStuck()) catScrollToTop();
    }

    var FILTER_HOMOGLYPHS = { 'c': 'с', 'e': 'е', 'o': 'о', 'a': 'а', 'p': 'р', 'y': 'у', 'x': 'х', 'k': 'к', 'm': 'м', 'h': 'н', 't': 'т', 'b': 'в' };

    function normalizeLabel(text) {
        return text.trim().toLowerCase().replace(':', '').replace(/[a-z]/g, function (ch) {
            return FILTER_HOMOGLYPHS[ch] || ch;
        });
    }

    function catFitDropdown(panel) {
        var dd = panel && panel.querySelector('.ui-filter__dropdown');
        if (!dd) return;
        var top = dd.getBoundingClientRect().top;
        dd.style.maxHeight = Math.max(160, window.innerHeight - top - 20) + 'px';
    }

    function catFollowThumbs() {
        var mainEl = document.querySelector('.muralGalleryMain');
        var thumbsEl = document.querySelector('.muralGalleryThumbs');
        if (!mainEl || !thumbsEl) return;
        var settled = 0;
        function bind() {
            var main = mainEl.swiper, thumbs = thumbsEl.swiper;
            if (!main || !thumbs) return false;
            thumbs.on('transitionEnd touchEnd', function () { settled = -thumbs.translate; });
            main.on('activeIndexChange', function () { settled = -thumbs.translate; });
            main.on('slideChange', function () {
                var slide = thumbs.slides[main.activeIndex];
                if (!slide) return;
                var top = slide.swiperSlideOffset;
                var bottom = top + slide.swiperSlideSize;
                var size = thumbs.size;
                var target = null;
                if (bottom > settled + size) target = bottom - size;
                else if (top < settled) target = top;
                if (null === target) { thumbs.translateTo(-settled, 0); return; }
                target = Math.max(0, Math.min(target, -thumbs.maxTranslate()));
                settled = target;
                thumbs.translateTo(-target, 300);
            });
            return true;
        }
        if (!bind()) window.addEventListener('load', bind);
    }

    function catEvenSocials() {
        if (window.innerWidth > 768) return;
        var changed = false;
        document.querySelectorAll('.link-flexSocialsMain[data-aos-offset]').forEach(function (a) {
            a.setAttribute('data-aos-offset', '200');
            changed = true;
        });
        var hero = document.querySelector('.mural-hero, .cardSection');
        var first = hero && hero.nextElementSibling;
        if (first) {
            first.querySelectorAll('[data-aos]').forEach(function (el) {
                el.setAttribute('data-aos-offset', '40');
                changed = true;
            });
        }
        if (changed && window.AOS && typeof window.AOS.refreshHard === 'function') window.AOS.refreshHard();
    }

    function catWatchDropdowns() {
        if (typeof MutationObserver === 'undefined' || !document.querySelector('.ui-filter')) return;
        var obs = new MutationObserver(function (list) {
            list.forEach(function (m) {
                var el = m.target;
                if (el.classList.contains('ui-filter') && el.classList.contains('is-open')) catFitDropdown(el);
            });
        });
        obs.observe(document.body, { attributes: true, subtree: true, attributeFilter: ['class'] });
    }

    function catWatchStuck() {
        var row = catFiltersRow();
        if (!row || getComputedStyle(row).position !== 'sticky') return;
        var raf = 0;
        function sync() {
            raf = 0;
            row.classList.toggle('is-stuck', catRowStuck());
            catFitDropdown(document.querySelector('.ui-filter.is-open'));
        }
        function onScroll() {
            if (!raf) raf = requestAnimationFrame(sync);
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        sync();
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
            var sel = catPending[key] || [];
            if (!sel.length) {
                valueEl.textContent = 'Все';
            } else if (sel.length === 1) {
                valueEl.textContent = data.maps[key][sel[0]] || 'Все';
            } else {
                valueEl.textContent = 'Выбрано: ' + sel.length;
            }
            if (panel) panel.classList.toggle('is-multi', sel.length > 1);
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
            resetBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> Сбросить';
            colorPanel.insertAdjacentElement('afterend', resetBtn);
            resetBtn.addEventListener('click', function () {
                CAT_GROUPS.forEach(function (k) { catPending[k] = []; });
                document.querySelectorAll('.ui-filter__item').forEach(function (it) {
                    var v = it.getAttribute('data-value');
                    if (v !== null) it.classList.toggle('is-active', v === '');
                });
                document.querySelectorAll('[data-filter-value]').forEach(function (b) {
                    b.classList.toggle('active', b.getAttribute('data-filter-value') === '');
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
        var sortBtn = event.target.closest('[data-sort]');
        if (!sortBtn || !catGrid()) return;
        event.preventDefault();
        catSort = sortBtn.getAttribute('data-sort') === 'new' ? 'new' : 'default';
        document.querySelectorAll('[data-sort]').forEach(function (b) {
            b.classList.toggle('active', b.getAttribute('data-sort') === catSort);
        });
        catApply(false);
    });

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-filter-value]');
        if (!btn || btn.closest('.mfilter--insp')) return;
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
        if (!reset || reset.closest('.mfilter--insp')) return;
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
        var showBtn = document.querySelector('.mfilter--insp .mfilter-show');
        if (showBtn) showBtn.textContent = 'ПОКАЗАТЬ (' + shown + ')';
    }

    function inspSyncPanels(maps) {
        document.querySelectorAll('.ui-filter').forEach(function (panel) {
            var list = panel.querySelector('.ui-filter__list');
            if (!list) return;
            var items = list.querySelectorAll('.ui-filter__item[data-value]');
            if (!items.length) return;
            var key = null;
            ['collection', 'color', 'subject'].forEach(function (k) {
                if (key) return;
                var first = items[1] && items[1].getAttribute('data-value');
                if (first && maps[k] && maps[k][first]) key = k;
            });
            if (!key) return;
            items.forEach(function (it) {
                var v = it.getAttribute('data-value');
                it.classList.toggle('is-active', v === '' ? inspState[key].length === 0 : inspState[key].indexOf(v) !== -1);
            });
            var valueEl = panel.querySelector('.ui-filter__value');
            if (valueEl) {
                var names = inspState[key].map(function (slug) { return maps[key][slug]; });
                valueEl.textContent = names.length ? names.join(', ') : 'Все';
            }
        });
    }

    function inspBuildMobilePanel() {
        var maps = window.okoyomInspFilters;
        var btn = document.querySelector('.inspirationTop .filterModalOpen');
        if (!maps || !btn || document.querySelector('.mfilter--insp')) return;
        var labels = { collection: 'КОЛЛЕКЦИЯ', subject: 'СЮЖЕТ', color: 'ЦВЕТ' };
        var icon = btn.querySelector('img');
        var closeSrc = icon ? icon.src.replace(/filters\.svg.*$/, 'close.svg') : '';

        var modal = document.createElement('div');
        modal.className = 'mfilter mfilter--insp';
        var overlay = document.createElement('div');
        overlay.className = 'mfilter__overlay';
        var panel = document.createElement('div');
        panel.className = 'mfilter__panel';
        var head = document.createElement('div');
        head.className = 'mfilter__head';
        var title = document.createElement('div');
        title.className = 'mfilter__title';
        title.textContent = 'Фильтры';
        var close = document.createElement('button');
        close.className = 'mfilter__close';
        close.type = 'button';
        if (closeSrc) {
            var closeImg = document.createElement('img');
            closeImg.src = closeSrc;
            closeImg.alt = '';
            closeImg.width = 40;
            closeImg.height = 40;
            close.appendChild(closeImg);
        } else {
            close.textContent = '×';
        }
        head.appendChild(title);
        head.appendChild(close);
        var content = document.createElement('div');
        content.className = 'mfilter__content';

        ['collection', 'subject', 'color'].forEach(function (key) {
            if (!maps[key] || !Object.keys(maps[key]).length) return;
            var isColor = key === 'color';
            var group = document.createElement('div');
            group.className = 'mfilter-group' + (isColor ? ' mfilter-group--color' : '');
            group.setAttribute('data-filter-group', key);
            var label = document.createElement('div');
            label.className = 'mfilter-label';
            label.textContent = labels[key];
            var wrap1 = document.createElement('div');
            wrap1.className = 'mfilter-scroll-1';
            var scroll = document.createElement('div');
            scroll.className = 'mfilter-scroll';
            var all = document.createElement('button');
            all.type = 'button';
            all.className = 'active';
            all.setAttribute('data-filter-value', '');
            all.textContent = 'Все';
            scroll.appendChild(all);
            Object.keys(maps[key]).forEach(function (slug, i) {
                var b = document.createElement('button');
                b.type = 'button';
                b.setAttribute('data-filter-value', slug);
                if (isColor) {
                    b.className = 'mfilter-color';
                    b.title = maps[key][slug];
                    var circle = document.createElement('span');
                    var hex = maps.swatches && maps.swatches[slug];
                    circle.className = 'circleFilter' + (hex ? '' : ' circleFilter-' + (i % 13 + 1));
                    if (hex) { circle.style.background = hex; circle.style.border = '1px solid ' + hex; }
                    b.appendChild(circle);
                } else {
                    b.textContent = maps[key][slug];
                }
                scroll.appendChild(b);
            });
            wrap1.appendChild(scroll);
            group.appendChild(label);
            group.appendChild(wrap1);
            content.appendChild(group);
        });

        var bottom = document.createElement('div');
        bottom.className = 'mfilter-bottom';
        var reset = document.createElement('button');
        reset.type = 'button';
        reset.className = 'mfilter-reset';
        reset.textContent = 'СБРОСИТЬ';
        var show = document.createElement('button');
        show.type = 'button';
        show.className = 'mfilter-show';
        show.textContent = 'ПОКАЗАТЬ';
        bottom.appendChild(reset);
        bottom.appendChild(show);
        panel.appendChild(head);
        panel.appendChild(content);
        panel.appendChild(bottom);
        modal.appendChild(overlay);
        modal.appendChild(panel);
        document.body.appendChild(modal);

        function openPanel() { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
        function closePanel() { modal.classList.remove('active'); document.body.style.overflow = ''; }
        btn.classList.remove('openModal2');
        btn.onclick = null;
        btn.addEventListener('click', function (e) { e.preventDefault(); openPanel(); });
        overlay.addEventListener('click', closePanel);
        close.addEventListener('click', closePanel);

        function repaint() {
            ['collection', 'subject', 'color'].forEach(function (key) {
                modal.querySelectorAll('[data-filter-group="' + key + '"] [data-filter-value]').forEach(function (b) {
                    var v = b.getAttribute('data-filter-value');
                    b.classList.toggle('active', v === '' ? inspState[key].length === 0 : inspState[key].indexOf(v) !== -1);
                });
            });
            inspSyncPanels(maps);
            inspApply();
        }

        modal.addEventListener('click', function (e) {
            var b = e.target.closest('[data-filter-value]');
            if (!b) return;
            e.preventDefault();
            var key = b.closest('[data-filter-group]').getAttribute('data-filter-group');
            var v = b.getAttribute('data-filter-value');
            if (v === '') {
                inspState[key] = [];
            } else {
                var i = inspState[key].indexOf(v);
                if (i === -1) inspState[key].push(v); else inspState[key].splice(i, 1);
            }
            repaint();
        });
        reset.addEventListener('click', function (e) {
            e.preventDefault();
            ['collection', 'subject', 'color'].forEach(function (key) { inspState[key] = []; });
            repaint();
        });
        inspApply();
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
        var row = document.querySelector('.inspirationTop .flexFiltersCatalog-3');
        if (row) {
            var top = row.closest('.inspirationTop');
            var wrapper = row.closest('.container');
            if (top && wrapper && wrapper.parentElement === top) {
                top.insertBefore(row, wrapper.nextSibling);
                row.classList.add('flexFiltersCatalog--insp');
            }
        }
    })();

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
        catWatchStuck();
        catWatchDropdowns();
        catFollowThumbs();
        catEvenSocials();
        inspBuildMobilePanel();
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
