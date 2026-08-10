<?php
declare( strict_types=1 );

$targets = array_merge(
	glob( __DIR__ . '/../wp-content/plugins/okoyom-core/*.php' ) ?: array(),
	glob( __DIR__ . '/../wp-content/plugins/okoyom-core/includes/*.php' ) ?: array(),
	glob( __DIR__ . '/../wp-content/themes/okoyom/*.php' ) ?: array(),
	glob( __DIR__ . '/../wp-content/themes/okoyom/inc/*.php' ) ?: array()
);

foreach ( $targets as $file ) {
	$src    = file_get_contents( $file );
	$tokens = token_get_all( $src );
	$out    = '';

	foreach ( $tokens as $token ) {
		if ( is_array( $token ) ) {
			[ $id, $text ] = $token;
			if ( T_COMMENT === $id || T_DOC_COMMENT === $id ) {
				if ( str_contains( $text, 'Plugin Name:' ) || str_contains( $text, 'Theme Name:' ) ) {
					$out .= $text;
				}
				continue;
			}
			$out .= $text;
		} else {
			$out .= $token;
		}
	}

	$out = preg_replace( "/[ \t]+\n/", "\n", $out );
	$out = preg_replace( "/\n{3,}/", "\n\n", $out );

	file_put_contents( $file, $out );
	echo basename( $file ), "\n";
}
