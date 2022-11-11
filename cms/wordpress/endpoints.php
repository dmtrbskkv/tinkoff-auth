<?php

use TinkoffAuth\Facades\Tinkoff;

add_action( 'rest_api_init', function () {
	register_rest_route( 'tinkoff_auth/v1', '/callback', [
		'methods'  => 'GET',
		'callback' => 'tinkoff_auth_callback',
	] );
} );

function tinkoff_auth_callback( WP_REST_Request $request ) {
	$account_location = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

	$response = new WP_REST_Response();
	$response->set_status( 307 );

	$tinkoff  = new Tinkoff();
	$mediator = $tinkoff->auth();
	if ( ! $mediator->getStatus() ) {
		$response->header( 'Location', tinkoff_auth_helper_format_redirect_url( false, 'Ошибка авторизации' ) );

		return $response;
	}

	$credentials = $mediator->getPayload();
	$email       = $credentials['email'] ?? null;
	$username    = str_replace( [ '+', ' ', '-' ], '', $credentials['phone_number'] ) ?? null;
	$password    = md5( time() . rand( 0, 100 ) . rand( 0, 200 ) );

	if ( ! $email || ! $username ) {
		$response->header( 'Location', tinkoff_auth_helper_format_redirect_url( false, 'Предоставленных данных недостаточно' ) );

		return $response;
	}

	$user = get_user_by( 'login', $username );
	if ( $user !== false ) {
		$is_tinkoff_user = $user->get( 'is_tinkoff' );
		if ( ! $is_tinkoff_user ) {
			$response->header( 'Location', tinkoff_auth_helper_format_redirect_url( false, 'Пользователь с такой почтой уже существует' ) );

			return $response;
		}

		$user_id = $user->get( 'id' );
	} else {
		$user_id = wp_create_user( $username, $password, $email );
		if ( is_wp_error( $user_id ) ) {
			$response->header( 'Location', tinkoff_auth_helper_format_redirect_url( false, 'Ошибка при создании пользователя' ) );

			return $response;
		}
	}


	update_user_meta( $user_id, 'is_tinkoff', true );
	update_user_meta( $user_id, 'first_name', $credentials['given_name'] ?? '' );
	update_user_meta( $user_id, 'last_name', $credentials['family_name'] ?? '' );
	wp_set_auth_cookie( $user_id );

	$response->header( 'Location', tinkoff_auth_helper_format_redirect_url() );

	return $response;
}

function tinkoff_auth_helper_format_redirect_url( $status = true, $message = '' ) {
	$account_location = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

	return $account_location . '?' . http_build_query( [ 'status' => $status, 'message' => $message ] );
}