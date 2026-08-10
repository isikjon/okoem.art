#!/usr/bin/env node

import { copyFile, mkdir, cp, writeFile, readFile } from 'node:fs/promises';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const vendor = join(root, 'wp-content/themes/okoyom/assets/vendor');

const files = [
  ['node_modules/aos/dist/aos.css', 'aos/aos.css'],
  ['node_modules/aos/dist/aos.js', 'aos/aos.js'],
  ['node_modules/swiper/swiper-bundle.min.css', 'swiper/swiper-bundle.min.css'],
  ['node_modules/swiper/swiper-bundle.min.js', 'swiper/swiper-bundle.min.js'],
];

for (const [from, to] of files) {
  const target = join(vendor, to);
  await mkdir(dirname(target), { recursive: true });
  await copyFile(join(root, from), target);
  console.log(`  ${to}`);
}

const fonts = [
  ['@fontsource-variable/outfit', 'outfit'],
  ['@fontsource-variable/playfair-display', 'playfair-display'],
];

const cssParts = [];
for (const [pkg, slug] of fonts) {
  const src = join(root, 'node_modules', pkg);
  await cp(join(src, 'files'), join(vendor, 'fonts', slug, 'files'), { recursive: true });
  let css = await readFile(join(src, 'index.css'), 'utf8');
  css = css.replaceAll('./files/', `./${slug}/files/`);
  css = css.replaceAll(' Variable\'', '\'');
  cssParts.push(css);
  console.log(`  fonts/${slug}`);
}
const italic = join(root, 'node_modules/@fontsource-variable/playfair-display/index-italic.css');
try {
  let css = await readFile(italic, 'utf8');
  css = css.replaceAll('./files/', './playfair-display/files/');
  css = css.replaceAll(" Variable'", "'");
  cssParts.push(css);
  console.log('  fonts/playfair-display (italic)');
} catch {  }

await writeFile(join(vendor, 'fonts', 'fonts.css'), cssParts.join('\n'));
console.log('Библиотеки и шрифты разложены в assets/vendor.');
