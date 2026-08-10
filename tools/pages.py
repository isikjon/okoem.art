#!/usr/bin/env python3
"""Сборка страниц, которых нет в макете заказчика.

ТЗ требует страницу «Спасибо» (п. 2.1) и выдачу поиска (п. 7), а в вёрстке
их нет. Собираем их из существующих блоков, чтобы не изобретать новый визуал:
шапка, подвал и типографика берутся один в один с готовых страниц.

    python3 tools/pages.py

Скрипт перезаписывает результат при каждом запуске — правки вносить
в исходные страницы или сюда, но не в сгенерированные файлы.
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
MACKET = ROOT / "macket"

THANKS_BLOCK = """
<section class="page404">
    <div class="container">
        <div class="page404Content">
            <h2>
                Спасибо
            </h2>
            <p>
                Заявка отправлена. Менеджер свяжется с вами в течение рабочего дня.
            </p>
            <a style="width: fit-content" href="catalog.html" class="material-link">
                Вернуться в каталог
            </a>
        </div>
    </div>
</section>
"""

SEARCH_TITLE = """
        <div class="titleCatalog">
            <h1 class="titleCatalog__title">
                Результаты поиска
            </h1>
            <p class="titleCatalog__text">
                Поиск по названию и артикулу
            </p>
        </div>
"""

def set_title(html: str, title: str) -> str:
    return re.sub(r"<title>.*?</title>", f"<title>{title}</title>", html, count=1, flags=re.DOTALL)

def build_thanks() -> str:
    html = (MACKET / "404.html").read_text(encoding="utf-8")

    start = html.find('<section class="page404">')
    end = html.find("</section>", start) + len("</section>")
    if start == -1 or end <= start:
        raise ValueError("в 404.html не найден блок .page404")

    html = html[:start] + THANKS_BLOCK.strip() + html[end:]

    html = html.replace(
        '<link rel="stylesheet" href="style.css">',
        '<meta name="robots" content="noindex, nofollow">\n    <link rel="stylesheet" href="style.css">',
        1,
    )

    return set_title(html, "Спасибо — ОКОЁМ")

def build_search() -> str:
    html = (MACKET / "catalog.html").read_text(encoding="utf-8")

    start = html.find('<div class="titleCatalog">')
    if start == -1:
        raise ValueError("в catalog.html не найден блок .titleCatalog")

    text_pos = html.find("titleCatalog__text", start)
    end = html.find("</div>", html.find("</p>", text_pos)) + len("</div>")
    if text_pos == -1 or end < start:
        raise ValueError("не найдена граница блока .titleCatalog")

    html = html[:start] + SEARCH_TITLE.strip() + html[end:]

    html = html.replace(
        '<link rel="stylesheet" href="style.css">',
        '<meta name="robots" content="noindex, nofollow">\n    <link rel="stylesheet" href="style.css">',
        1,
    )

    return set_title(html, "Результаты поиска — ОКОЁМ")

def main() -> int:
    if not MACKET.exists():
        print(f"Не найден {MACKET}", file=sys.stderr)
        return 1

    for name, builder in (("thanks.html", build_thanks), ("search.html", build_search)):
        html = builder()
        (MACKET / name).write_text(html, encoding="utf-8")
        print(f"  {name:15} собрана ({len(html)} б)")

    return 0

if __name__ == "__main__":
    raise SystemExit(main())
