<?php

/* Example basic variables for test config with gmail server. */
$baseUrl = '';
$test_db_name = '';
$db_username = '';
$db_password = '';
$email = '';
$email_password = '';
$name = '';

return [
	'displayErrorDetails' => true,
	'addContentLengthHeader' => false,
	'app' => [
		'url' => 'http://localhost:8080',
		'hash' => [
			'password_hash_algorithm' => PASSWORD_BCRYPT,
			'cost' => 10,
			'standard_hash_algorithm' => 'sha256'
		],
		'logging' => true /* TODO */
	],
	'loggers' => [
		/* Loggers: setup, general, network, database, security */
		/* Could be expanded to include: business, statistics, suspicious-activity, ... */
		'setup' => [
			'logger_name' => 'setup_logger',
			'channel_name' => 'setup',
			'level' => 100,
			'path' => BASE_PATH . '/logs/tests.log'
		],
		'general' => [  /* The default logger */
			'logger_name' => 'gen_logger',
			'channel_name' => 'general',
			'level' => 100,
			'path' => BASE_PATH . '/logs/tests.log'
		],
		'network' => [
			'logger_name' => 'net_logger',
			'channel_name' => 'network',
			'level' => 100,
			'path' => BASE_PATH . '/logs/tests.log'
		],
		'database' => [
			'logger_name' => 'db_logger',
			'channel_name' => 'database',
			'level' => 100,
			'path' => BASE_PATH . '/logs/tests.log'
		],
		'security' => [
			'logger_name' => 'sec_logger',
			'channel_name' => 'security',
			'level' => 100,
			'path' => BASE_PATH . '/logs/tests.log'
		],
		'debug' => [
			'logger_name' => 'debug_logger',
			'channel_name' => 'debug',
			'level' => 100,
			'path' => BASE_PATH . '/logs/tests.log'
		]
	],
	'db' => [
		'driver' => 'mysql',
		'host' => '127.0.0.1',
		'database' => $test_db_name,
		'username' => $db_username,
		'password' => $db_password,
		'charset' => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix' => ''
	],
	'auth' => [
		'session' => 'user_id',
		'remember' => 'user_r'
	],
	'mail' => [
		'host' => 'smtp.gmail.com',
		'smtp_auth' => true,
		'smtp_secure' => 'tls',
		'port' => 587,
		'username' => $email,
		'password' => $email_password,
		'html' => true,
		'from_email' => $email,
		'from_name' => $name
	],
	'twig' => [
		'debug' => true
	],
	'csrf' => [
		'session' => 'csrf_token'
	]
];