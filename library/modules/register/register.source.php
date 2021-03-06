<?php

/**
 * @package Famires
 *
 * @author Selman Eser
 * @copyright 2014 Selman Eser
 * @license BSD 2-clause
 *
 * @version 1.0
 */

if (!defined('CORE'))
	exit();

function register_main()
{
	global $core, $template;

	if (!empty($_POST['submit']))
	{
		check_session('register');

		$values = array();
		$fields = array(
			'username' => 'username',
			'password' => 'password',
			'verify_password' => 'password',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'password')
				$values[$field] = !empty($_POST[$field]) ? sha1($_POST[$field]) : '';
			elseif ($type === 'username')
				$values[$field] = !empty($_POST[$field]) && !preg_match('~[^A-Za-z0-9\._]~', $_POST[$field]) ? $_POST[$field] : '';
		}

		if ($values['username'] === '')
			fatal_error('You did not enter a valid username!');

		$request = db_query("
			SELECT id_user
			FROM user
			WHERE username = '$values[username]'
			LIMIT 1");
		list ($duplicate_id) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate_id))
			fatal_error('The username entered is already in use!');

		if ($values['password'] === '')
			fatal_error('You did not enter a valid password!');

		if ($values['password'] !== $values['verify_password'])
			fatal_error('The passwords entered do not match!');

		db_query("
			INSERT INTO user
				(username, password, registered)
			VALUES
				('$values[username]', '$values[password]', " . time() . ")");

		redirect(build_url('login'));
	}

	$template['page_title'] = 'Register';
	$core['current_template'] = 'register_main';
}