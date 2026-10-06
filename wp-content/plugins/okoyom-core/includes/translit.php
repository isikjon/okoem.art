<?php

defined( 'ABSPATH' ) || exit;

function okoyom_translit_map(): array {
	return array(
		'а' => 'a',  'б' => 'b',  'в' => 'v',  'г' => 'g',  'д' => 'd',
		'е' => 'e',  'ё' => 'e',  'ж' => 'zh', 'з' => 'z',  'и' => 'i',
		'й' => 'y',  'к' => 'k',  'л' => 'l',  'м' => 'm',  'н' => 'n',
		'о' => 'o',  'п' => 'p',  'р' => 'r',  'с' => 's',  'т' => 't',
		'у' => 'u',  'ф' => 'f',  'х' => 'h',  'ц' => 'c',  'ч' => 'ch',
		'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',  'ы' => 'y',  'ь' => '',
		'э' => 'e',  'ю' => 'yu', 'я' => 'ya',
		'і' => 'i',  'ї' => 'yi', 'є' => 'ye', 'ґ' => 'g',  'ў' => 'u',
	);
}

function okoyom_translit_symbols(): array {
	return array(
		'№' => '', '«' => '', '»' => '', '„' => '', '“' => '', '”' => '',
		'’' => '', '‘' => '', '…' => '', '°' => '', '©' => '', '®' => '', '™' => '',
		'—' => '-', '–' => '-', '−' => '-', '×' => 'x', '€' => 'eur', '₽' => 'rub',
	);
}

function okoyom_translit_needed( string $text ): bool {
	return (bool) preg_match( '~[^\x00-\x7F]~', $text );
}

function okoyom_translit_text( string $text ): string {
	$map = okoyom_translit_symbols();
	foreach ( okoyom_translit_map() as $from => $to ) {
		$map[ $from ]                           = $to;
		$map[ mb_strtoupper( $from, 'UTF-8' ) ] = $to;
	}

	return strtr( $text, $map );
}

function okoyom_translit_slug( string $text ): string {
	$slug = preg_replace( '~[^a-z0-9]+~', '-', mb_strtolower( okoyom_translit_text( $text ), 'UTF-8' ) );

	return trim( (string) $slug, '-' );
}

function okoyom_translit_title( $title, $raw_title = '', $context = 'save' ) {
	$source = '' !== (string) $raw_title ? (string) $raw_title : (string) $title;
	if ( ! okoyom_translit_needed( $source ) ) {
		return $title;
	}

	return (string) preg_replace( '~[^\x00-\x7F]+~', '-', remove_accents( okoyom_translit_text( $source ) ) );
}
add_filter( 'sanitize_title', 'okoyom_translit_title', 9, 3 );

function okoyom_translit_file_name( $filename, $filename_raw = '' ) {
	$filename = (string) $filename;
	if ( ! okoyom_translit_needed( $filename ) ) {
		return $filename;
	}

	$ext  = '';
	$dot  = strrpos( $filename, '.' );
	$name = $filename;
	if ( false !== $dot ) {
		$ext  = strtolower( substr( $filename, $dot ) );
		$name = substr( $filename, 0, $dot );
	}

	$name = mb_strtolower( remove_accents( okoyom_translit_text( $name ) ), 'UTF-8' );
	$name = preg_replace( '~[^a-z0-9._-]+~', '-', $name );
	$name = trim( (string) preg_replace( '~-+~', '-', (string) $name ), '-._' );

	return '' === $name ? 'file' . $ext : $name . $ext;
}
add_filter( 'sanitize_file_name', 'okoyom_translit_file_name', 10, 2 );
