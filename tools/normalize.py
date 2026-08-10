#!/usr/bin/env python3
"""Приведение макета к требованиям ТЗ.

Скрипт идемпотентный: правит файлы в macket/ на месте, повторный запуск
ничего не меняет. Он же документирует, что именно мы поменяли в вёрстке
заказчика — список правок совпадает с docs/questions.md.

    python3 tools/normalize.py [--dry-run]

Что делает:
  * lang="en" → lang="ru";
  * убирает user-scalable=no (доступность, штраф Lighthouse);
  * выкидывает jQuery и jQuery UI — они подключались трижды и не используются;
  * проставляет width/height по реальным размерам файлов (главный источник CLS);
  * добавляет loading="lazy" и decoding="async" всем картинкам, кроме первых
    на странице — те грузятся сразу, иначе просядет LCP;
  * чинит текстовые поля, свёрстанные как <input type="email">.

Заполнение alt здесь сознательно не делается: у контентных картинок alt
приходит из медиабиблиотеки CMS на этапе натяжки, а генерировать его из
имён файлов — значит наплодить мусор. Декоративные иконки помечаются
aria-hidden, для них пустой alt корректен.
"""

from __future__ import annotations

import argparse
import re
import sys
import xml.etree.ElementTree as ET
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
MACKET = ROOT / "macket"

EAGER_IMAGES = 2

IMAGE_LABELS = {
    "logoHeader.svg": "Окоём",
    "logoFooter.svg": "Окоём",
    "telIconHeader.svg": "Позвонить",
    "telBurger.svg": "Позвонить",
    "messageIcon-2.svg": "WhatsApp",
    "likeHeader.svg": "Избранное",
    "bagHeader.svg": "Корзина",
    "burger.svg": "Открыть меню",
    "closeBurger.svg": "Закрыть меню",
    "filters.svg": "Фильтры",
    "flex-col-4__footer-1.svg": "Instagram",
    "flex-col-4__footer-2.svg": "Pinterest",
    "flex-col-4__footer-3.svg": "ВКонтакте",
    "flex-col-4__footer-4.svg": "Яндекс Дзен",
}

RE_IMG = re.compile(r"<img\b[^>]*>", re.IGNORECASE)
RE_SRC = re.compile(r'src="(?:/)?img/([^"]+)"')
RE_VIEWPORT = re.compile(r'(<meta name="viewport" content=")([^"]*)(")')
RE_JQUERY = re.compile(
    r'\s*<script src="https://ajax\.googleapis\.com/ajax/libs/jquery[^"]*"></script>'
)

def svg_size(path: Path) -> tuple[int, int] | None:
    """Размер SVG: сначала width/height, иначе viewBox."""
    try:
        root = ET.parse(path).getroot()
    except ET.ParseError:
        return None

    def to_px(value: str | None) -> float | None:
        if not value:
            return None
        match = re.match(r"^([\d.]+)", value.strip())
        return float(match.group(1)) if match else None

    width, height = to_px(root.get("width")), to_px(root.get("height"))
    if width and height:
        return round(width), round(height)

    view_box = root.get("viewBox")
    if view_box:
        parts = view_box.replace(",", " ").split()
        if len(parts) == 4:
            return round(float(parts[2])), round(float(parts[3]))

    return None

def image_size(name: str) -> tuple[int, int] | None:
    path = MACKET / "img" / name
    if not path.exists():
        return None

    if path.suffix.lower() == ".svg":
        return svg_size(path)

    from PIL import Image

    try:
        with Image.open(path) as image:
            return image.size
    except OSError:
        return None

def fix_img_tag(tag: str, index: int, sizes: dict[str, tuple[int, int]]) -> str:
    src = RE_SRC.search(tag)
    if not src:
        return tag

    name = src.group(1)
    attrs = tag[:-1].rstrip()
    if attrs.endswith("/"):
        attrs = attrs[:-1].rstrip()

    size = sizes.get(name)
    if size and "width=" not in attrs:
        attrs += f' width="{size[0]}" height="{size[1]}"'

    if index >= EAGER_IMAGES:
        if "loading=" not in attrs:
            attrs += ' loading="lazy"'
        if "decoding=" not in attrs:
            attrs += ' decoding="async"'
    elif "fetchpriority=" not in attrs:
        attrs += ' fetchpriority="high"'

    label = IMAGE_LABELS.get(name)
    if label:

        attrs = attrs.replace(' aria-hidden="true"', "")
        attrs = re.sub(r'alt="[^"]*"', f'alt="{label}"', attrs)
    elif 'alt=""' in attrs and name.lower().endswith(".svg") and "aria-hidden" not in attrs:

        attrs += ' aria-hidden="true"'

    return attrs + ">"

LINK_MAP = {
    "каталог": "catalog.html",
    "вдохновение": "inspiration.html",
    "дизайнерам": "designers.html",
    "покупателям": "buyers.html",
    "о студии": "about.html",
    "контакты": "contact.html",
    "главная": "index.html",
    "перейти на главную": "index.html",
    "избранное": "like-2.html",
    "избранное (2)": "like-2.html",
    "корзина": "bag-2.html",
    "смотреть каталог": "catalog.html",
    "перейти в каталог": "catalog.html",
    "смотреть": "catalog.html",
    "все работы": "catalog.html",
    "подобрать мурал": "catalog.html",
}

RE_PRODUCT_CARD = re.compile(r"₽\s*/\s*м²")

RE_DEAD_LINK = re.compile(r'(<a\b[^>]*href=")#!("[^>]*>)(.*?)(</a>)', re.DOTALL)
RE_LOGO_NO_HREF = re.compile(r'<a class="logo">')

def wire_links(html: str) -> tuple[str, int]:
    """Разводка мёртвых ссылок href="#!" по реальным страницам."""
    wired = 0

    def replace(match: re.Match) -> str:
        nonlocal wired
        head, tail, inner, close = match.groups()
        text = re.sub(r"<[^>]+>", " ", inner)
        text = re.sub(r"\s+", " ", text).strip().lower().rstrip("→ ").strip()

        target = LINK_MAP.get(text)
        if not target and RE_PRODUCT_CARD.search(text):
            target = "card.html"
        if not target:
            return match.group(0)

        wired += 1
        return f"{head}{target}{tail}{inner}{close}"

    html = RE_DEAD_LINK.sub(replace, html)

    html, logo_count = RE_LOGO_NO_HREF.subn('<a class="logo" href="index.html">', html)

    return html, wired + logo_count

BLANK_GIF = "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="

def fix_empty_src(html: str) -> str:
    return html.replace('<img src="" ', f'<img src="{BLANK_GIF}" ')

REAL_CONTENT = (
    ("@окоёмwallpaper", "@okoem.art"),
    ("окоём.studio", "okoemart"),
    ("окоём.wallpaper", "okoem_art"),

        (
        "наб. реки Мойки, 73 м. Адмиралтейская",
        "ТК «Интерио», ул. Полевая Сабировская, 54А 4 этаж, секция 454",
    ),
)

def fix_real_content(html: str) -> str:
    for needle, replacement in REAL_CONTENT:
        html = html.replace(needle, replacement)

    return html

def fix_inputs(html: str) -> str:
    """Поиск и артикул свёрстаны как type="email" — валидация их отбрасывает."""

    def replace(match: re.Match) -> str:
        tag = match.group(0)
        if 'type="email"' not in tag:
            return tag
        if not re.search(r'placeholder="[^"]*(Поиск|артикул)', tag):
            return tag
        return tag.replace('type="email"', 'type="search"')

    return re.sub(r"<input\b[^>]*>", replace, html)

CSS_MARKER = "/* okoyom:normalize */"

CSS_PRELUDE = f"""{CSS_MARKER}
/* У картинок проставлены width/height, чтобы браузер резервировал под них
   место и не было скачков раскладки при загрузке (ТЗ п. 13).

   Сами по себе атрибуты ломают эту вёрстку: одни блоки задают картинке
   только ширину (тогда буквально применялась бы высота из атрибута —
   photoBig растягивался с 835 до 3340 px), другие только высоту (тогда
   применялась бы ширина — логотип в шапке расползался с 92 до 122 px и
   сдвигал меню). Обнуляем оба измерения: атрибуты остаются подсказкой
   пропорции, а размер по-прежнему диктует CSS.

   Селектор из одного тега перебивается любым правилом с классом, поэтому
   блоки со своими размерами продолжают работать как раньше. */
img {{
    width: auto;
    height: auto;
}}

"""

def patch_css(path: Path, dry: bool) -> bool:
    """Добавляет правило в начало style.css. Повторный запуск ничего не делает."""
    css = path.read_text(encoding="utf-8")
    if CSS_MARKER in css:
        return False

    if not dry:
        path.write_text(CSS_PRELUDE + css, encoding="utf-8")

    return True

def normalize(path: Path, sizes: dict[str, tuple[int, int]]) -> tuple[str, list[str]]:
    html = path.read_text(encoding="utf-8")
    original = html
    changes: list[str] = []

    if '<html lang="en">' in html:
        html = html.replace('<html lang="en">', '<html lang="ru">')
        changes.append("lang")

    def strip_scale(match: re.Match) -> str:
        content = re.sub(r",?\s*user-scalable=no", "", match.group(2)).strip()
        return match.group(1) + content + match.group(3)

    html, viewport_count = RE_VIEWPORT.subn(strip_scale, html)
    if viewport_count and "user-scalable" not in html:
        changes.append("viewport")

    html, jquery_count = RE_JQUERY.subn("", html)
    if jquery_count:
        changes.append(f"jquery×{jquery_count}")

    counter = {"i": 0}

    def replace_img(match: re.Match) -> str:
        result = fix_img_tag(match.group(0), counter["i"], sizes)
        counter["i"] += 1
        return result

    html = RE_IMG.sub(replace_img, html)
    if counter["i"]:
        changes.append(f"img×{counter['i']}")

    html = fix_inputs(html)
    html = fix_empty_src(html)
    html = fix_real_content(html)

    html, wired = wire_links(html)
    if wired:
        changes.append(f"ссылок×{wired}")

    return html, (changes if html != original else [])

def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--dry-run", action="store_true")
    args = ap.parse_args()

    if not MACKET.exists():
        print(f"Не найден {MACKET}", file=sys.stderr)
        return 1

    sizes: dict[str, tuple[int, int]] = {}
    for image in sorted((MACKET / "img").iterdir()):
        size = image_size(image.name)
        if size:
            sizes[image.name] = size

    print(f"Размеры прочитаны у {len(sizes)} картинок из {len(list((MACKET / 'img').iterdir()))}")

    if patch_css(MACKET / "style.css", args.dry_run):
        print("  style.css        добавлено правило img { height: auto }")
    else:
        print("  style.css        правило уже на месте")

    touched = 0
    for page in sorted(MACKET.glob("*.html")):
        html, changes = normalize(page, sizes)
        if not changes:
            print(f"  {page.name:20} без изменений")
            continue
        if not args.dry_run:
            page.write_text(html, encoding="utf-8")
        touched += 1
        print(f"  {page.name:20} {', '.join(changes)}")

    print(f"Изменено страниц: {touched}")

    return 0

if __name__ == "__main__":
    raise SystemExit(main())
