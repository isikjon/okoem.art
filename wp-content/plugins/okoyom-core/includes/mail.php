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
