<?php

defined( 'ABSPATH' ) || exit;

function okoyom_filter_taxonomies_map(): array {
	return array(
		'collection' => array( 'oko_collection', 'Коллекция' ),
		'series'     => array( 'oko_series', 'Серия' ),
		'subject'    => array( 'oko_subject', 'Сюжет' ),
		'color'      => array( 'oko_color', 'Цвет' ),
	);
}

function okoyom_active_filters(): array {
	$active = array();
	foreach ( okoyom_filter_taxonomies_map() as $param => $info ) {
		if ( empty( $_GET[ $param ] ) ) {
			continue;
		}
		$raw    = is_array( $_GET[ $param ] ) ? $_GET[ $param ] : explode( ',', (string) $_GET[ $param ] );
		$slugs  = array_filter( array_map( 'sanitize_title', $raw ) );
		if ( $slugs ) {
			$active[ $param ] = array_values( array_unique( $slugs ) );
		}
	}
	return $active;
}

function okoyom_build_tax_query( array $filters, ?string $skip = null ): array {
	$tax_query = array( 'relation' => 'AND' );
	foreach ( $filters as $param => $slugs ) {
		if ( $param === $skip ) {
			continue;
		}
		$taxonomy = okoyom_filter_taxonomies_map()[ $param ][0] ?? null;
		if ( ! $taxonomy ) {
			continue;
		}
		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $slugs,
			'operator' => 'IN',
		);
	}
	return $tax_query;
}

function okoyom_filtered_products( string $scope = 'all', array $filters = array(), string $search = '' ): array {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'fields'         => 'ids',
	);

	$tax_query = okoyom_build_tax_query( $filters );

	if ( 'all' !== $scope ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $scope,
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$args['tax_query'] = $tax_query;
	}

	if ( '' !== $search ) {
		global $wpdb;
		$like = '%' . $wpdb->esc_like( $search ) . '%';
		$ids  = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT p.ID FROM {$wpdb->posts} p
				 LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_sku'
				 WHERE p.post_type='product' AND p.post_status='publish'
				   AND (p.post_title LIKE %s OR m.meta_value LIKE %s)",
				$like,
				$like
			)
		);
		if ( ! $ids ) {
			return array();
		}
		$args['post__in'] = array_map( 'intval', $ids );
	}

	return get_posts( $args );
}

function okoyom_facet_available( string $param, string $term_slug, string $scope, array $filters ): bool {
	$test         = $filters;
	$test[ $param ] = array_unique( array_merge( $filters[ $param ] ?? array(), array( $term_slug ) ) );
	return count( okoyom_filtered_products( $scope, $test ) ) > 0;
}

function okoyom_render_filter_group( string $param ): void {
	$map = okoyom_filter_taxonomies_map();
	if ( ! isset( $map[ $param ] ) ) {
		return;
	}
	list( $taxonomy, $label ) = $map[ $param ];

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'orderby'    => 'name',
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}

	$filters   = okoyom_active_filters();
	$scope     = okoyom_current_scope();
	$active    = $filters[ $param ] ?? array();
	$is_color  = 'color' === $param;
	$swatches  = $is_color && function_exists( 'okoyom_color_swatches' ) ? okoyom_color_swatches() : array();
	?>
	<div class="mfilter-group<?php echo $is_color ? ' mfilter-group--color' : ''; ?>" data-filter-group="<?php echo esc_attr( $param ); ?>">
		<div class="mfilter-label"><?php echo esc_html( mb_strtoupper( $label ) ); ?></div>
		<div class="mfilter-scroll-1">
			<div class="mfilter-scroll">
				<button type="button" class="<?php echo empty( $active ) ? 'active' : ''; ?>" data-filter-value="">
					Все
				</button>
				<?php $i = 0; ?>
				<?php foreach ( $terms as $term ) : ?>
					<?php $available = okoyom_facet_available( $param, $term->slug, $scope, $filters ); ?>
					<?php if ( $is_color ) : ?>
						<button type="button" title="<?php echo esc_attr( $term->name ); ?>"
							class="mfilter-color<?php echo in_array( $term->slug, $active, true ) ? ' active' : ''; ?>"
							data-filter-value="<?php echo esc_attr( $term->slug ); ?>"
							<?php echo $available ? '' : 'style="display:none"'; ?>>
							<?php if ( ! empty( $swatches[ $term->slug ] ) ) : ?>
								<span class="circleFilter" style="background:<?php echo esc_attr( $swatches[ $term->slug ] ); ?>;border:1px solid <?php echo esc_attr( $swatches[ $term->slug ] ); ?>"></span>
							<?php else : ?>
								<span class="circleFilter circleFilter-<?php echo esc_attr( (string) ( $i % 13 + 1 ) ); ?>"></span>
							<?php endif; ?>
						</button>
					<?php else : ?>
						<button type="button"
							class="<?php echo in_array( $term->slug, $active, true ) ? 'active' : ''; ?>"
							data-filter-value="<?php echo esc_attr( $term->slug ); ?>"
							<?php echo $available ? '' : 'style="display:none"'; ?>>
							<?php echo esc_html( $term->name ); ?>
						</button>
					<?php endif; ?>
					<?php ++$i; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

function okoyom_current_scope(): string {
	$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'all';
	return in_array( $tab, array( 'all', 'murals', 'companion' ), true ) ? $tab : 'all';
}

function okoyom_filters_localize( array $keys ): array {
	$maps    = array();
	$all     = okoyom_filter_taxonomies_map();
	$filters = okoyom_active_filters();
	$scope   = okoyom_current_scope();

	foreach ( $keys as $key ) {
		$taxonomy = $all[ $key ][0] ?? null;
		if ( ! $taxonomy ) {
			continue;
		}
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'orderby'    => 'name',
			)
		);
		$map = array();
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {

				if ( ! okoyom_facet_available( $key, $term->slug, $scope, $filters ) ) {
					continue;
				}
				$map[ $term->slug ] = $term->name;
			}
		}
		$maps[ $key ] = $map;
	}
	return $maps;
}

function okoyom_color_swatches(): array {
	if ( ! function_exists( 'okoyom_color_hex' ) ) {
		return array();
	}
	$terms = get_terms(
		array(
			'taxonomy'   => 'oko_color',
			'hide_empty' => true,
		)
	);
	$out = array();
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$hex = okoyom_color_hex( $term->term_id );
			if ( $hex ) {
				$out[ $term->slug ] = $hex;
			}
		}
	}
	return $out;
}

add_action(
	'wp_enqueue_scripts',
	function () {

		$is_tax_archive = is_tax( array( 'oko_collection', 'oko_series', 'oko_subject', 'oko_color' ) );

		if ( is_page( array( 'catalog', 'search' ) ) || is_post_type_archive( 'product' ) || $is_tax_archive ) {
			$archive     = function_exists( 'okoyom_archive_tax_term' ) ? okoyom_archive_tax_term() : null;
			$param_by_tax = array(
				'oko_collection' => 'collection',
				'oko_series'     => 'series',
				'oko_subject'    => 'subject',
				'oko_color'      => 'color',
			);

			wp_localize_script(
				'okoyom-theme',
				'okoyomCatFilters',
				array(
					'maps'     => okoyom_filters_localize( array( 'collection', 'series', 'subject', 'color' ) ),
					'active'   => okoyom_active_filters(),
					'swatches' => okoyom_color_swatches(),
					'archive'  => $archive ? array(
						'param' => $param_by_tax[ $archive['taxonomy'] ] ?? '',
						'slug'  => $archive['slug'],
					) : null,
					'pretty'   => array(
						'collection' => 'collection',
						'series'     => 'series',
						'subject'    => 'subject',
						'color'      => 'color',
					),
				)
			);
		}

		if ( ! is_page( 'inspiration' ) ) {
			return;
		}

		$maps = array();
		foreach ( array( 'collection' => 'oko_collection', 'color' => 'oko_color', 'subject' => 'oko_subject' ) as $key => $taxonomy ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => true,
				)
			);
			$map = array();
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$map[ $term->slug ] = $term->name;
				}
			}
			$maps[ $key ] = $map;
		}

		$maps['swatches'] = okoyom_color_swatches();
		wp_localize_script( 'okoyom-theme', 'okoyomInspFilters', $maps );
	},
	20
);
