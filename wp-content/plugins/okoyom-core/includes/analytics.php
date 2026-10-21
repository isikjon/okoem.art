<?php

defined( 'ABSPATH' ) || exit;

function okoyom_metrika_ids( string $key = 'metrika' ): array {
	$raw = array_map( 'trim', explode( ',', okoyom_option( $key ) ) );
	$ids = array_filter( array_map( 'intval', $raw ) );

	return array_values( array_unique( $ids ) );
}

function okoyom_metrika_params( int $id ): array {
	$params = array(
		'ssr'                 => true,
		'clickmap'            => true,
		'ecommerce'           => 'dataLayer',
		'accurateTrackBounce' => true,
		'trackLinks'          => true,
	);

	if ( in_array( $id, okoyom_metrika_ids( 'metrika_webvisor' ), true ) ) {
		$params['webvisor'] = true;
	}

	return $params;
}

add_action(
	'wp_head',
	function (): void {
		$ids = okoyom_metrika_ids();
		if ( array() === $ids ) {
			return;
		}

		$counters = array();
		foreach ( $ids as $id ) {
			$counters[] = array( $id, okoyom_metrika_params( $id ) );
		}
		?>
<script>
(function () {
	var counters = <?php echo wp_json_encode( $counters ); ?>;
	var started = false;

	function start() {
		if (started) return;
		started = true;
		window.dataLayer = window.dataLayer || [];

		counters.forEach(function (counter) {
			var src = 'https://mc.yandex.ru/metrika/tag.js?id=' + counter[0];

			(function (m, e, t, r, i, k, a) {
				m[i] = m[i] || function () { (m[i].a = m[i].a || []).push(arguments); };
				m[i].l = 1 * new Date();
				for (var j = 0; j < e.scripts.length; j++) { if (e.scripts[j].src === r) { return; } }
				k = e.createElement(t); a = e.getElementsByTagName(t)[0];
				k.async = 1; k.src = r; a.parentNode.insertBefore(k, a);
			})(window, document, 'script', src, 'ym');

			window.ym(counter[0], 'init', Object.assign({}, counter[1], {
				referrer: document.referrer,
				url: location.href
			}));
		});
	}

	var choice = '';
	try { choice = localStorage.getItem('okoyom_cookie_consent') || ''; } catch (e) {}

	if ('all' === choice) {
		start();
		return;
	}

	document.addEventListener('okoyom:consent', function (event) {
		if (event.detail && 'all' === event.detail.choice) start();
	});
})();
</script>
		<?php
	},
	1
);
