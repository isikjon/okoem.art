<?php

defined( 'ABSPATH' ) || exit;

function okoyom_smtp_config(): array {
	if ( ! defined( 'OKOYOM_SMTP_USER' ) || ! defined( 'OKOYOM_SMTP_PASS' ) ) {
		return array();
	}

	$user = (string) constant( 'OKOYOM_SMTP_USER' );
	$pass = (string) constant( 'OKOYOM_SMTP_PASS' );

	if ( '' === $user || '' === $pass ) {
		return array();
	}

	return array(
		'host'      => defined( 'OKOYOM_SMTP_HOST' ) ? (string) constant( 'OKOYOM_SMTP_HOST' ) : 'mail.nic.ru',
		'port'      => defined( 'OKOYOM_SMTP_PORT' ) ? (int) constant( 'OKOYOM_SMTP_PORT' ) : 465,
		'secure'    => defined( 'OKOYOM_SMTP_SECURE' ) ? (string) constant( 'OKOYOM_SMTP_SECURE' ) : 'ssl',
		'user'      => $user,
		'pass'      => $pass,
		'from'      => defined( 'OKOYOM_SMTP_FROM' ) ? (string) constant( 'OKOYOM_SMTP_FROM' ) : $user,
		'from_name' => defined( 'OKOYOM_SMTP_FROM_NAME' ) ? (string) constant( 'OKOYOM_SMTP_FROM_NAME' ) : get_bloginfo( 'name' ),
	);
}

function okoyom_smtp_ready(): bool {
	return array() !== okoyom_smtp_config();
}

add_action(
	'phpmailer_init',
	function ( $mailer ): void {
		$config = okoyom_smtp_config();
		if ( array() === $config ) {
			return;
		}

		$mailer->isSMTP();
		$mailer->Host       = $config['host'];
		$mailer->Port       = $config['port'];
		$mailer->SMTPAuth   = true;
		$mailer->Username   = $config['user'];
		$mailer->Password   = $config['pass'];
		$mailer->SMTPSecure = $config['secure'];
		$mailer->CharSet    = 'UTF-8';
		$mailer->Timeout    = 12;

		$mailer->setFrom( $config['from'], $config['from_name'], false );
	},
	99
);

add_filter(
	'wp_mail_from',
	function ( string $from ): string {
		$config = okoyom_smtp_config();

		return array() === $config ? $from : $config['from'];
	},
	99
);

add_filter(
	'wp_mail_from_name',
	function ( string $name ): string {
		$config = okoyom_smtp_config();

		return array() === $config ? $name : $config['from_name'];
	},
	99
);

function okoyom_mail_error( ?string $error = null ): string {
	static $last = '';

	if ( null !== $error ) {
		$last = $error;
	}

	return $last;
}

add_action(
	'wp_mail_failed',
	function ( $wp_error ): void {
		if ( is_wp_error( $wp_error ) ) {
			okoyom_mail_error( $wp_error->get_error_message() );
		}
	}
);

function okoyom_mail_error_is_transient( string $error ): bool {
	if ( '' === trim( $error ) ) {
		return true;
	}

	$error = function_exists( 'mb_strtolower' ) ? mb_strtolower( $error, 'UTF-8' ) : strtolower( $error );

	$markers = array(
		'could not connect',
		'failed to connect',
		'smtp connect() failed',
		'connection refused',
		'connection timed out',
		'timed out',
		'network is unreachable',
		'no route to host',
		'temporarily',
		'try again later',
		'too many connections',
		'ошибка подключения',
		'не удалось подключиться',
		'не удалось соединиться',
		'время ожидания',
		'истекло время',
		'сервер недоступен',
		'попробуйте позже',
		'476',
		'421',
		'450',
		'451',
		'452',
	);

	foreach ( $markers as $marker ) {
		if ( false !== strpos( $error, $marker ) ) {
			return true;
		}
	}

	return false;
}
