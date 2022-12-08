<?php

use TinkoffAuth\Config\Api;
use TinkoffAuth\Facades\Tinkoff;

add_action( 'rest_api_init', function () {
	register_rest_route( 'tinkoff_auth/v1', '/callback', [
		'methods'  => 'GET',
		'callback' => 'tinkoff_auth_callback',
	] );
} );

function tinkoff_auth_callback( WP_REST_Request $request ) {
	// Получение данных пользователя
	$tinkoff  = new Tinkoff();
	$mediator = $tinkoff->auth();
	if ( ! $mediator->getStatus() ) {
		return tinkoff_auth_helper_build_response( false, 'Ошибка авторизации' );
	}

	$credentials = $mediator->getPayload();

	// Основная информация о пользователе
	$userinfo = $credentials[ Api::SCOPES_USERINFO ];

	// Паспорт пользователя
	$passportShort = $credentials[ Api::SCOPES_PASSPORT_SHORT ];
	$passportFull  = $credentials[ Api::SCOPES_PASSPORT ];
	$passport      = array_merge( $passportShort, $passportFull );

	// Водительские права
	$driveLicenses = $credentials[ Api::SCOPES_DRIVER_LICENSES ];

	// ИНН и Снилс
	$inn   = $credentials[ Api::SCOPES_INN ];
	$snils = $credentials[ Api::SCOPES_SNILS ];

	//Информации об идентификации и самозанятости
	$isIdentified   = $credentials[ Api::SCOPES_IDENTIFICATION ];
	$isSelfEmployed = $credentials[ Api::SCOPES_SELF_EMPLOYED_STATUS ];

	// Адреса
	$addresses = $credentials[ Api::SCOPES_ADDRESSES ];

	// Дебетовые карты, подписка и собренд
	$debitCards   = $credentials[ Api::SCOPES_DEBIT_CARDS ];
	$subscription = $credentials[ Api::SCOPES_SUBSCRIPTION ];
	$cobrand      = $credentials[ Api::SCOPES_COBRAND_STATUS ];

	// Формирование почты, имени пользователя и пароля
	$email    = $userinfo['email'] ?? null;
	$username = str_replace( [ '+', ' ', '-' ], '', $userinfo['phone_number'] ) ?? null;
	$password = md5( time() . rand( 0, 100 ) . rand( 0, 200 ) );

	if ( ! $email || ! $username ) {
		return tinkoff_auth_helper_build_response( false, 'Предоставленных данных недостаточно' );
	}

	// Создание пользователя
	$user = get_user_by( 'login', $username );
	if ( $user !== false ) {
		$is_tinkoff_user = $user->get( 'is_tinkoff' );
		if ( ! $is_tinkoff_user ) {
			return tinkoff_auth_helper_build_response( false, 'Пользователь с такой почтой уже существует' );
		}

		$user_id = $user->get( 'id' );
	} else {
		$user_id = null;
		if ( function_exists( 'wc_create_new_customer' ) ) {
			$user_id = wc_create_new_customer( $email, $username, $password );
		}

		if ( is_null( $user_id ) ) {
			$user_id = wp_create_user( $username, $password, $email );
		}

		if ( ! $user_id || is_wp_error( $user_id ) ) {
			$message = $user_id->get_error_message()  ?? 'Ошибка при создании пользователя';
			return tinkoff_auth_helper_build_response( false, $message );
		}
	}

	// Профиль WP
	update_user_meta( $user_id, 'is_tinkoff', true );
	update_user_meta( $user_id, 'first_name', $userinfo['given_name'] ?? '' );
	update_user_meta( $user_id, 'last_name', $userinfo['family_name'] ?? '' );

	// Плагин iiko
	if ( get_option( 'tinkoff_auth_compatibility_iiko' ) ) {
		update_user_meta( $user_id, 'iiko_email', $userinfo['email'] ?? '' );
		update_user_meta( $user_id, 'iiko_name', $userinfo['given_name'] ?? '' );
		update_user_meta( $user_id, 'iiko_middleName', $userinfo['middle_name'] ?? '' );
		update_user_meta( $user_id, 'iiko_middleName', $userinfo['middle_name'] ?? '' );
		update_user_meta( $user_id, 'iiko_surName', $userinfo['family_name'] ?? '' );
		update_user_meta( $user_id, 'iiko_phone', $username );
	}

	// Билинг
	update_user_meta( $user_id, 'billing_first_name', $userinfo['given_name'] ?? '' );
	update_user_meta( $user_id, 'billing_phone', $username );

	// Дополнительные данные от Тинькофф
	update_user_meta( $user_id, 'tinkof_auth_passport', $passport );
	update_user_meta( $user_id, 'tinkof_auth_drive_licenses', $driveLicenses );
	update_user_meta( $user_id, 'tinkof_auth_inn', $inn );
	update_user_meta( $user_id, 'tinkof_auth_snils', $snils );
	update_user_meta( $user_id, 'tinkof_auth_is_identified', $isIdentified );
	update_user_meta( $user_id, 'tinkof_auth_is_self_employed', $isSelfEmployed );
	update_user_meta( $user_id, 'tinkof_auth_addresses', $addresses );
	update_user_meta( $user_id, 'tinkof_auth_debitCards', $debitCards );
	update_user_meta( $user_id, 'tinkof_auth_subscription', $subscription );
	update_user_meta( $user_id, 'tinkof_auth_cobrand', $cobrand );

	// Авторизация
	wp_set_auth_cookie( $user_id );

	return tinkoff_auth_helper_build_response( true );
}

/**
 * Формирование Redirect URL
 *
 * @param $status
 * @param $message
 *
 * @return string
 */
function tinkoff_auth_helper_format_redirect_url( $status = true, $message = '' ) {
	$account_location = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

	return $account_location . '?' . http_build_query( [ 'status' => $status, 'message' => $message ] );
}

/**
 * Форомирование ответа
 *
 * @param $status
 * @param $message
 *
 * @return WP_REST_Response
 */
function tinkoff_auth_helper_build_response( $status = true, $message = '' ) {
	$response = new WP_REST_Response();
	$response->set_status( 307 );

	$response->header( 'Location', tinkoff_auth_helper_format_redirect_url( $status, $message ) );

	return $response;
}