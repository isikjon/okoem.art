#!/usr/bin/env python3
"""Нарезка статического макета в шаблоны темы okoyom.

Скрипт повторяемый: при получении новой версии вёрстки от заказчика
кладём её в macket/ и запускаем заново — сгенерированные файлы
перезапишутся, ручные правки в них делать нельзя.

    python3 tools/convert.py [--src macket] [--dry-run]

Что делает:
  * копирует img/, style.css, adapt.css, app.js в тему;
  * переписывает пути к картинкам на OKOYOM_ASSETS_URI;
  * режет каждую страницу на header / контент / footer;
  * header.php и footer.php генерируются из index.html, контент страниц —
    в template-parts/static/<имя>.php.
"""

from __future__ import annotations

import argparse
import hashlib
import re
import shutil
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
THEME = ROOT / "wp-content" / "themes" / "okoyom"
ASSETS = THEME / "assets"
STATIC = THEME / "template-parts" / "static"

TEXT_REGISTRY: dict[str, str] = {}

RE_TEXT_NODE = re.compile(r"(>)([^<>]+)(<)")
RE_HAS_LETTER = re.compile(r"[A-Za-zА-Яа-яЁё]")


def wrap_texts(html: str) -> str:
    def replace(match: re.Match) -> str:
        head, text, tail = match.group(1), match.group(2), match.group(3)
        stripped = text.strip()
        if not stripped or not RE_HAS_LETTER.search(stripped):
            return match.group(0)

        normalized = re.sub(r"\s+", " ", stripped)
        key = hashlib.md5(normalized.encode("utf-8")).hexdigest()[:12]
        TEXT_REGISTRY[key] = normalized

        lead = text[: len(text) - len(text.lstrip())]
        trail = text[len(text.rstrip()) :]
        php_default = normalized.replace("\\", "\\\\").replace("'", "\\'")

        return "%s%s<?php echo okoyom_t( '%s', '%s' ); ?>%s%s" % (
            head,
            lead,
            key,
            php_default,
            trail,
            tail,
        )

    return RE_TEXT_NODE.sub(replace, html)

BASE_PAGE = "index.html"

COPY_FILES = ("style.css", "adapt.css", "app.js")

GENERATED = (
    "<?php\n"
    "defined( 'ABSPATH' ) || exit;\n"
    "?>\n"
)

RE_ASSET = re.compile(r'((?:src|href|data-src|poster)=")/?img/')

RE_ASSET_CSS = re.compile(r"url\(\s*/?img/")

RE_LOGO = re.compile(r'<a class="logo">')

NAV_BLOCKS = {
    "listHeader": "primary",
    "linksBurger": "primary",
    "col-2__footer": "footer",
}
HEADER_NAV = ("listHeader", "linksBurger")
FOOTER_NAV = ("col-2__footer",)

LINK_RULES = {
    "likeHeader": "okoyom_favorites_url()",
    "bagHeader": "okoyom_cart_url()",
    "btnWhiteTextBtnV3": "okoyom_favorites_url()",
    "btnWhiteTextBtnV2": "okoyom_cart_url()",
}

SOCIAL_LINKS = {
    "flex-col-4__footer-1.svg": "https://www.instagram.com/okoem.art",
    "flex-col-4__footer-2.svg": "https://ru.pinterest.com/okoemart",
    "flex-col-4__footer-3.svg": "https://vk.com/okoem_art",
    "flex-col-4__footer-4.svg": "https://yandex.ru/rythm/businesses/@okoem.art",
    "blockSocialsMain-1.png": "https://www.instagram.com/okoem.art",
    "blockSocialsMain-2.png": "https://ru.pinterest.com/okoemart",
    "blockSocialsMain-3.png": "https://vk.com/okoem_art",
    "blockSocialsMain-4.png": "https://yandex.ru/rythm/businesses/@okoem.art",
}

ROUTE_URL = "https://yandex.ru/maps/?text=" + (
    "Санкт-Петербург, ул. Полевая Сабировская, 54А, ТК Интерио"
).replace(" ", "%20").replace(",", "%2C")

EDITABLE_TEXT = (
    ("+7 (495) 123-45-67", "<?php echo esc_html( okoyom_option( 'phone' ) ); ?>"),
    ("+7 (900) 123-45-67", "<?php echo esc_html( okoyom_option( 'phone' ) ); ?>"),
    ('href="tel:+7 (495) 123-45-67"', 'href="<?php echo esc_attr( okoyom_phone_href() ); ?>"'),
    ('href="tel:+7 (900) 123-45-67"', 'href="<?php echo esc_attr( okoyom_phone_href() ); ?>"'),
)

SUBS_RE = (
    ("125K", "subs_instagram"),
    ("45K", "subs_pinterest"),
    ("89K", "subs_vk"),
    ("32K", "subs_dzen"),
)

HTML_TO_WP = {
    "index.html": "/",
    "catalog.html": "/catalog/",
    "card.html": "/catalog/",
    "inspiration.html": "/inspiration/",
    "designers.html": "/designers/",
    "buyers.html": "/buyers/",
    "about.html": "/about/",
    "contact.html": "/contacts/",
    "polite.html": "/policy/",
    "like-1.html": "/favorites/",
    "like-2.html": "/favorites/",
    "bag-1.html": "/cart/",
    "bag-2.html": "/cart/",
    "thanks.html": "/thanks/",
    "search.html": "/search/",
    "404.html": "/",
}

def rewrite_page_links(html: str) -> str:
    for name, path in HTML_TO_WP.items():
        html = html.replace('href="%s"' % name, 'href="%s"' % path)
    return html

def rewrite_editable(html: str) -> str:

    for needle, replacement in EDITABLE_TEXT:
        if needle.startswith('href="tel:'):
            html = html.replace(needle, replacement)
    for needle, replacement in EDITABLE_TEXT:
        if not needle.startswith('href="tel:'):
            html = html.replace(needle, replacement)

    for number, option in SUBS_RE:
        html = re.sub(
            r"(>\s*)%s(\s*<)" % re.escape(number),
            r"\1<?php echo esc_html( okoyom_option( '%s' ) ); ?>\2" % option,
            html,
        )

    return html

def rewrite_social(html: str) -> str:
    """Мёртвые ссылки соцсетей → реальные адреса заказчика."""
    for image_name, url in SOCIAL_LINKS.items():

        pattern = re.compile(
            r'<a ([^>]*)href="#!"([^>]*>\s*(?:<div[^>]*|<img[^>]*)'
            + re.escape(image_name) + ")",
            re.DOTALL,
        )
        html = pattern.sub(
            '<a \\1href="%s" target="_blank" rel="noopener"\\2' % url,
            html,
        )

    html = re.sub(
        r'(Полевая Сабировская.{0,3500}?)<a ([^>]*)href="#!"([^>]*>\s*Построить маршрут)',
        lambda m: '%s<a %shref="%s" target="_blank" rel="noopener"%s' % (
            m.group(1),
            m.group(2),
            ROUTE_URL,
            m.group(3).replace(" openModal", ""),
        ),
        html,
        flags=re.DOTALL,
    )

    return html

def rewrite_assets(html: str, wrap: bool = True) -> str:

    html = rewrite_social(html)
    html = rewrite_editable(html)
    html = rewrite_page_links(html)

    html = rewrite_to_webp(html)
    html = RE_ASSET.sub(r"\1<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/", html)
    html = RE_ASSET_CSS.sub("url(<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/", html)

    if wrap:
        html = wrap_texts(html)

    return html

def rewrite_chrome(html: str, nav_blocks: tuple[str, ...]) -> str:
    """Разводка навигации и логотипа в шапке/подвале."""
    html = RE_LOGO.sub(
        '<a class="logo" href="<?php echo esc_url( home_url( \'/\' ) ); ?>">', html
    )

    for css_class in nav_blocks:
        location = NAV_BLOCKS[css_class]
        pattern = re.compile(
            r'<div class="%s">.*?</div>' % re.escape(css_class), re.DOTALL
        )
        replacement = (
            "<?php okoyom_nav( '%s', '%s' ); ?>" % (location, css_class)
        )
        html, count = pattern.subn(replacement, html, count=1)
        if not count:
            print(f"  ! блок .{css_class} не найден", file=sys.stderr)

    for marker, php_call in LINK_RULES.items():
        pattern = re.compile(
            r'<a href="#!"((?:(?!</a>)[^>])*class="[^"]*%s[^"]*")' % re.escape(marker)
        )
        html = pattern.sub(
            '<a href="<?php echo esc_url( %s ); ?>"\\1' % php_call, html
        )

    return html

RE_DIV_TOKEN = re.compile(r"<div\b|</div>", re.IGNORECASE)

def find_block(html: str, opening: str) -> tuple[int, int] | None:
    """Границы блока с учётом вложенности: (начало содержимого, конец блока)."""
    start = html.find(opening)
    if start == -1:
        return None

    inner_start = start + len(opening)
    depth = 1
    for token in RE_DIV_TOKEN.finditer(html, inner_start):
        depth += 1 if token.group(0).lower() != "</div>" else -1
        if depth == 0:
            return inner_start, token.start()

    return None

GALLERY_OPENING = '<div class="pinterest-gallery">'
GALLERY_CALL = "\n        <?php okoyom_inspiration_gallery(); ?>\n    "

RE_COUNTER = re.compile(
    r'(<span class="textSpanQuantityCatalog">)\s*\d+\s+объект\w*\s*(</span>)'
)
COUNTER_CALL = r"\1<?php echo esc_html( okoyom_inspiration_count() ); ?>\2"

def extract_gallery(html: str) -> tuple[str, str | None]:
    """Возвращает (разметку без плиток, вырезанные плитки)."""
    bounds = find_block(html, GALLERY_OPENING)
    if not bounds:
        return html, None

    inner_start, inner_end = bounds
    without = html[:inner_start] + GALLERY_CALL + html[inner_end:]

    return RE_COUNTER.sub(COUNTER_CALL, without), html[inner_start:inner_end]

FILTER_GROUPS = (
    ("КОЛЛЕКЦИЯ", "collection"),
    ("СЕРИЯ", "series"),
    ("СЮЖЕТ", "subject"),
    ("ЦВЕТ", "color"),
)

def replace_filter_groups(html: str) -> str:
    for label, param in FILTER_GROUPS:
        pattern = re.compile(
            r'<div class="mfilter-group">\s*<div class="mfilter-label">\s*'
            + label + r'\b'
        )
        m = pattern.search(html)
        if not m:
            continue
        start = m.start()
        depth = 0
        i = start
        end = None
        for tok in re.finditer(r"<div\b|</div>", html[start:]):
            depth += 1 if tok.group(0) == "<div" else -1
            if depth == 0:
                end = start + tok.end()
                break
        if end is None:
            continue
        call = "<?php okoyom_render_filter_group( '%s' ); ?>" % param
        html = html[:start] + call + html[end:]
    return html

CATALOG_GRID_OPENING = '<div class="flexTwoTypeInfoMain flexTwoTypeInfoMain-2">'
CATALOG_TABS = ("all", "murals", "companion")
RE_CATALOG_COUNTER = re.compile(
    r'(<span class="textSpanQuantityCatalog">)\s*\d+\s+работ\w*\s*(</span>)'
)

def extract_catalog(html: str) -> tuple[str, str | None]:
    """Заменяет сетки карточек вызовами okoyom_catalog_grid()."""
    first_grid = None
    out = []
    rest = html
    for tab in CATALOG_TABS:
        bounds = find_block(rest, CATALOG_GRID_OPENING)
        if not bounds:
            break
        inner_start, inner_end = bounds
        if first_grid is None:
            first_grid = rest[inner_start:inner_end]
        out.append(rest[:inner_start])
        out.append(f"\n        <?php okoyom_catalog_grid( '{tab}' ); ?>\n    ")
        rest = rest[inner_end:]
    out.append(rest)
    result = "".join(out)

    if first_grid is not None:
        result = RE_CATALOG_COUNTER.sub(
            r"\1<?php echo esc_html( okoyom_catalog_count() ); ?>\2", result
        )

    return result, first_grid

def split_page(html: str) -> tuple[str, str, str]:
    """Возвращает (head_и_header, контент, footer)."""
    header_end = html.find("</header>")
    footer_start = html.find("<footer")
    if header_end == -1 or footer_start == -1:
        raise ValueError("не найдены границы <header>/<footer>")
    header_end += len("</header>")
    return html[:header_end], html[header_end:footer_start], html[footer_start:]

RE_SRC_SCRIPT = re.compile(r"<script[^>]*\ssrc=[^>]*>\s*</script>\s*")

def extract_scripts(chunk: str) -> str:
    """Хвост страницы: модалки и инлайн-скрипты, без подключений по src."""
    start = chunk.rfind("</footer>")
    if start == -1:
        return ""
    tail = chunk[start + len("</footer>") :]

    end = tail.rfind("</body>")
    if end != -1:
        tail = tail[:end]

    return RE_SRC_SCRIPT.sub("", tail).strip()

def build_header(chunk: str) -> str:
    """<!DOCTYPE>…</header> → header.php с WP-хуками."""
    body_start = chunk.find("<header")
    header_markup = rewrite_chrome(rewrite_assets(chunk[body_start:]), HEADER_NAV)
    return (
        GENERATED
        + """<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
"""
        + header_markup
        + "\n"
    )

def build_footer(chunk: str) -> str:
    """<footer>…</html> → footer.php, скрипты отдаёт wp_footer()."""
    end = chunk.rfind("</footer>")
    if end == -1:
        raise ValueError("не найден закрывающий </footer>")
    footer_markup = rewrite_chrome(
        rewrite_assets(chunk[: end + len("</footer>")]), FOOTER_NAV
    )
    return (
        GENERATED
        + footer_markup
        + "\n<?php wp_footer(); ?>\n</body>\n</html>\n"
    )

def make_webp(img_dst: Path) -> None:
    """ТЗ требует WebP. Рядом с каждым png/jpg кладём webp, favicon не трогаем."""
    if not shutil.which("cwebp"):
        print("  ! cwebp не найден, конвертация в webp пропущена", file=sys.stderr)
        return

    made = 0
    for path in sorted(img_dst.iterdir()):
        if path.suffix.lower() not in (".png", ".jpg", ".jpeg") or path.stem == "icon":
            continue
        out = path.with_suffix(".webp")
        if out.exists() and out.stat().st_mtime >= path.stat().st_mtime:
            continue
        subprocess.run(
            ["cwebp", "-quiet", "-q", "82", str(path), "-o", str(out)],
            check=True,
        )
        made += 1
    print(f"  webp            → assets/img/ ({made} новых)")

def rewrite_to_webp(html: str) -> str:
    """Ссылки на png/jpg переводим на webp, если такой файл есть в assets."""
    def repl(m: re.Match) -> str:
        name, ext = m.group(1), m.group(2)
        if name == "icon":
            return m.group(0)
        return f"{name}.webp" if (ASSETS / "img" / f"{name}.webp").exists() else m.group(0)

    return re.sub(r"([\w\-.]+)\.(png|jpe?g)\b", repl, html)

def copy_assets(src: Path, dry: bool) -> None:
    img_src, img_dst = src / "img", ASSETS / "img"
    if not dry:
        ASSETS.mkdir(parents=True, exist_ok=True)
        if img_dst.exists():
            shutil.rmtree(img_dst)
        shutil.copytree(img_src, img_dst)
        make_webp(img_dst)
    print(f"  img/            → assets/img/ ({len(list(img_src.iterdir()))} файлов)")

    for name in COPY_FILES:
        if not (src / name).exists():
            print(f"  ! {name} не найден в макете", file=sys.stderr)
            continue
        if not dry:
            shutil.copy2(src / name, ASSETS / name)
        print(f"  {name:15} → assets/{name}")

def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--src", default="macket", help="каталог со статической вёрсткой")
    ap.add_argument("--dry-run", action="store_true")
    args = ap.parse_args()

    src = (ROOT / args.src).resolve()
    if not (src / BASE_PAGE).exists():
        print(f"Не найден {src / BASE_PAGE}", file=sys.stderr)
        return 1

    print(f"Макет: {src}")
    print("Ассеты:")
    copy_assets(src, args.dry_run)

    base = (src / BASE_PAGE).read_text(encoding="utf-8")
    head, _, foot = split_page(base)
    if not args.dry_run:
        (THEME / "header.php").write_text(build_header(head), encoding="utf-8")
        (THEME / "footer.php").write_text(build_footer(foot), encoding="utf-8")
    print("Каркас:\n  header.php, footer.php")

    print("Страницы:")
    if not args.dry_run:
        STATIC.mkdir(parents=True, exist_ok=True)
    for page in sorted(src.glob("*.html")):
        html = page.read_text(encoding="utf-8")
        try:
            _, content, _ = split_page(html)
        except ValueError as exc:
            print(f"  ! {page.name}: {exc}", file=sys.stderr)
            continue
        out = STATIC / f"{page.stem}.php"
        _, _, tail = split_page(html)
        scripts = extract_scripts(tail)

        content, gallery = extract_gallery(content)

        catalog_cards = None
        if page.stem in ("catalog", "search"):
            content, catalog_cards = extract_catalog(content)
            content = replace_filter_groups(content)

        if not args.dry_run:
            out.write_text(
                GENERATED + rewrite_assets(content).strip() + "\n",
                encoding="utf-8",
            )

            gallery_file = STATIC / f"{page.stem}.gallery.php"
            if gallery:
                gallery_file.write_text(
                    GENERATED + rewrite_assets(gallery, wrap=False).strip() + "\n",
                    encoding="utf-8",
                )
            elif gallery_file.exists():
                gallery_file.unlink()

            cards_file = STATIC / f"{page.stem}.cards.php"
            if catalog_cards:
                cards_file.write_text(
                    GENERATED + rewrite_assets(catalog_cards, wrap=False).strip() + "\n",
                    encoding="utf-8",
                )
            elif cards_file.exists():
                cards_file.unlink()

            script_file = STATIC / f"{page.stem}.scripts.php"
            if scripts:
                script_file.write_text(
                    GENERATED + rewrite_assets(scripts, wrap=False).strip() + "\n",
                    encoding="utf-8",
                )
            elif script_file.exists():
                script_file.unlink()

        note = f", скриптов {scripts.count('<script')}" if scripts else ""
        if gallery:
            note += f", галерея вынесена ({gallery.count('pinterest-item')} плиток)"
        print(f"  {page.name:20} → template-parts/static/{out.name} ({len(content)} б{note})")

    if not args.dry_run:
        registry_lines = ["<?php", "defined( 'ABSPATH' ) || exit;", "", "return array("]
        for key in sorted(TEXT_REGISTRY):
            val = TEXT_REGISTRY[key].replace("\\", "\\\\").replace("'", "\\'")
            registry_lines.append("\t'%s' => '%s'," % (key, val))
        registry_lines.append(");")
        (THEME / "inc" / "texts-registry.php").write_text("\n".join(registry_lines) + "\n", encoding="utf-8")
        print(f"Реестр текстов: {len(TEXT_REGISTRY)} уникальных строк")

    return 0

if __name__ == "__main__":
    raise SystemExit(main())
