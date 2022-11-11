<?php

function tinkoff_auth_auto_loader( $class_name ) {
	// Формируем корректное имя класса
	$class_name = str_replace( 'TinkoffAuth\\', '', $class_name );
	$class_name = str_replace( '\\', '/', $class_name ) . '.php';

	$file_path = str_replace( '//', '/', __DIR__ . '/../../src/' . $class_name );

	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}

spl_autoload_register( 'tinkoff_auth_auto_loader' );