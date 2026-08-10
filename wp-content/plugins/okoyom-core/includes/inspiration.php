<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_INSPIRATION_CPT = 'oko_inspiration';

const OKOYOM_META_SUBTITLE = '_okoyom_subtitle';

function okoyom_register_inspiration_cpt(): void {
	register_post_type(
		OKOYOM_INSPIRATION_CPT,
		array(
			'labels'        => array(
				'name'          => 'Вдохновение',
				'singular_name' => 'Работа',
				'add_new_item'  => 'Добавить работу',
				'edit_item'     => 'Редактирование работы',
				'menu_name'     => 'Вдохновение',
			),

			'public'        => false,
			'show_ui'       => true,
			'menu_icon'     => 'dashicons-format-gallery',
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'hierarchical'  => false,
			'show_in_rest'  => true,
		)
	);

	register_post_meta(
		OKOYOM_INSPIRATION_CPT,
		OKOYOM_META_SUBTITLE,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
		)
	);
}

add_action( 'init', 'okoyom_register_inspiration_cpt' );

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'okoyom-inspiration-subtitle',
			'Надзаголовок',
			function ( WP_Post $post ) {
				wp_nonce_field( 'okoyom_inspiration_save', 'okoyom_inspiration_nonce' );
				$subtitle = get_post_meta( $post->ID, OKOYOM_META_SUBTITLE, true );
				?>
				<input type="text" class="widefat" name="okoyom_subtitle"
					value="<?php echo esc_attr( $subtitle ); ?>"
					placeholder="Интерьер">
				<p class="description">Мелкая подпись над названием плитки.</p>
				<?php
			},
			OKOYOM_INSPIRATION_CPT,
			'side'
		);
	}
);

add_action(
	'save_post_' . OKOYOM_INSPIRATION_CPT,
	function ( int $post_id ) {
		if ( ! isset( $_POST['okoyom_inspiration_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['okoyom_inspiration_nonce'] ), 'okoyom_inspiration_save' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		update_post_meta(
			$post_id,
			OKOYOM_META_SUBTITLE,
			sanitize_text_field( wp_unslash( $_POST['okoyom_subtitle'] ?? '' ) )
		);
	}
);

add_filter(
	'manage_' . OKOYOM_INSPIRATION_CPT . '_posts_columns',
	function ( array $columns ): array {
		$order = array( 'cb' => $columns['cb'] ?? '' );

		$order['okoyom_thumb']    = 'Снимок';
		$order['title']           = 'Название';
		$order['okoyom_subtitle'] = 'Надзаголовок';
		$order['okoyom_order']    = 'Порядок';
		$order['date']            = $columns['date'] ?? 'Дата';

		return array_filter( $order );
	}
);

add_action(
	'manage_' . OKOYOM_INSPIRATION_CPT . '_posts_custom_column',
	function ( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'okoyom_thumb':
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, array( 80, 80 ), array( 'style' => 'object-fit:cover;width:80px;height:80px' ) );
				} else {
					echo '<span style="color:#d63638">нет снимка</span>';
				}
				break;

			case 'okoyom_subtitle':
				echo esc_html( get_post_meta( $post_id, OKOYOM_META_SUBTITLE, true ) );
				break;

			case 'okoyom_order':
				echo (int) get_post_field( 'menu_order', $post_id );
				break;
		}
	},
	10,
	2
);

add_action(
	'pre_get_posts',
	function ( WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( OKOYOM_INSPIRATION_CPT !== $query->get( 'post_type' ) || $query->get( 'orderby' ) ) {
			return;
		}

		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'ASC' );
	}
);

function okoyom_inspiration_items( int $limit = -1 ): array {
	return get_posts(
		array(
			'post_type'      => OKOYOM_INSPIRATION_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
		)
	);
}
