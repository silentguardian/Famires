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

function meal_main()
{
	global $core, $template;

	$template['portions'] = array(1 => 'Small', 'Medium', 'Large');

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function meal_list()
{
	global $core, $template;

	$request = db_query("
		SELECT id_meal, name, portion, price
		FROM meal
		ORDER BY name, portion");
	$template['meals'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['meals'][] = array(
			'id' => $row['id_meal'],
			'name' => $row['name'],
			'portion' => $row['portion'],
			'price' => $row['price'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Meal List';
	$core['current_template'] = 'meal_list';
}

function meal_edit()
{
	global $core, $template;

	$id_meal = !empty($_REQUEST['meal']) ? (int) $_REQUEST['meal'] : 0;
	$is_new = empty($id_meal);

	if ($is_new)
	{
		$template['meal'] = array(
			'is_new' => true,
			'id' => 0,
			'name' => '',
			'portion' => 0,
			'price' => 0,
		);
	}
	else
	{
		$request = db_query("
			SELECT id_meal, name, portion, price
			FROM meal
			WHERE id_meal = $id_meal
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['meal'] = array(
				'is_new' => false,
				'id' => $row['id_meal'],
				'name' => $row['name'],
				'portion' => $row['portion'],
				'price' => $row['price'],
			);
		}
		db_free_result($request);

		if (!isset($template['meal']))
			fatal_error('The meal requested does not exist!');
	}

	if (!empty($_POST['save']))
	{
		check_session('meal');

		$values = array();
		$fields = array(
			'name' => 'string',
			'portion' => 'int',
			'price' => 'float',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'string')
				$values[$field] = !empty($_POST[$field]) ? htmlspecialchars($_POST[$field], ENT_QUOTES) : '';
			elseif ($type === 'int')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
			elseif ($type === 'float')
				$values[$field] = !empty($_POST[$field]) ? (float) $_POST[$field] : 0;
		}

		if ($values['name'] === '')
			fatal_error('Meal name field cannot be empty!');
		elseif ($values['portion'] < 1 || $values['portion'] > 3)
			fatal_error('Meal portion is not valid!');
		elseif ($values['price'] < 1)
			fatal_error('Meal price is not valid!');

		if ($is_new)
		{
			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO meal
					(" . implode(', ', array_keys($insert)) . ")
				VALUES
					(" . implode(', ', $insert) . ")");
		}
		else
		{
			$update = array();
			foreach ($values as $field => $value)
				$update[] = $field . " = '" . $value . "'";

			db_query("
				UPDATE meal
				SET " . implode(', ', $update) . "
				WHERE id_meal = $id_meal
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url('meal'));

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Meal';
	$core['current_template'] = 'meal_edit';
}

function meal_delete()
{
	global $core, $template;

	$id_meal = !empty($_REQUEST['meal']) ? (int) $_REQUEST['meal'] : 0;

	$request = db_query("
		SELECT id_meal, name
		FROM meal
		WHERE id_meal = $id_meal
		LIMIT 1");
	while ($row = db_fetch_assoc($request))
	{
		$template['meal'] = array(
			'id' => $row['id_meal'],
			'name' => $row['name'],
		);
	}
	db_free_result($request);

	if (!isset($template['meal']))
		fatal_error('The meal requested does not exist!');

	if (!empty($_POST['delete']))
	{
		check_session('meal');

		db_query("
			DELETE FROM meal
			WHERE id_meal = $id_meal
			LIMIT 1");

		db_query("
			DELETE FROM item
			WHERE id_meal = $id_meal");

		redirect(build_url('meal'));
	}

	if (!empty($_POST['delete']) || !empty($_POST['cancel']))
		redirect(build_url('meal'));

	$template['page_title'] = 'Delete Meal';
	$core['current_template'] = 'meal_delete';
}