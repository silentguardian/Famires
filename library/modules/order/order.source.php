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

function order_main()
{
	global $core, $template;

	$template['discounts'] = array('None', 'Student', 'Retiree');
	$template['portions'] = array(1 => 'Small', 'Medium', 'Large');

	$actions = array('list', 'view', 'print', 'meal', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function order_list()
{
	global $core, $template;

	$request = db_query("
		SELECT
			o.id_order, o.id_table, o.id_user,
			o.discount, o.time, u.username
		FROM order AS o
			LEFT JOIN user AS u ON (u.id_user = o.id_user)
		ORDER BY o.id_order DESC");
	$template['orders'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['orders'][] = array(
			'id' => $row['id_order'],
			'table' => $row['id_table'],
			'user' => $row['username'],
			'discount' => $row['discount'],
			'time' => format_time($row['time'], 'long'),
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Order List';
	$core['current_template'] = 'order_list';
}

function order_view()
{
	global $core, $template;

	order_get();

	$template['page_title'] = 'View Order';
	$core['current_template'] = 'order_view';
}

function order_print()
{
	global $core, $template;

	order_get();

	$template['strip_layers'] = true;
	$template['page_title'] = 'Print Order';
	$core['current_template'] = 'order_print';
}

function order_get()
{
	global $template, $user;

	$id_order = !empty($_REQUEST['order']) ? (int) $_REQUEST['order'] : 0;

	$request = db_query("
		SELECT
			o.id_order, o.id_table, o.id_user,
			o.discount, o.time, u.username
		FROM order AS o
			LEFT JOIN user AS u ON (u.id_user = o.id_user)
		WHERE o.id_order = $id_order
		LIMIT 1");
	$template['order'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['order'] = array(
			'id' => $row['id_order'],
			'table' => $row['id_table'],
			'user' => $row['username'],
			'discount' => $row['discount'],
			'time' => format_time($row['time'], 'long'),
		);
	}
	db_free_result($request);

	if (empty($template['order']))
		fatal_error('The order requested does not exist!');

	$request = db_query("
		SELECT i.id_item, m.name, m.price, m.portion, i.quantity
		FROM item AS i
			LEFT JOIN meal AS m ON (m.id_meal = i.id_meal)
		WHERE i.id_order = $id_order");
	$template['items'] = array();
	$template['total'] = 0;
	while ($row = db_fetch_assoc($request))
	{
		$template['items'][] = array(
			'id' => $row['id_item'],
			'name' => $row['name'],
			'portion' => $row['portion'],
			'quantity' => $row['quantity'],
			'price' => $row['price'],
			'total' => sprintf('%.2f', $row['price'] * $row['quantity']),
		);

		$template['total'] += $row['price'] * $row['quantity'];
	}
	db_free_result($request);

	$template['total'] = sprintf('%.2f', $template['total']);
	$template['discounted'] = sprintf('%.2f', ($template['total'] - ($template['total'] / 10)));

	$request = db_query("
		SELECT id_meal, name, portion
		FROM meal
		ORDER BY name, portion");
	$template['meals'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['meals'][] = array(
			'id' => $row['id_meal'],
			'name' => $row['name'],
			'portion' => $row['portion'],
		);
	}
	db_free_result($request);
}

function order_meal()
{
	global $core, $template;

	$id_order = !empty($_REQUEST['order']) ? (int) $_REQUEST['order'] : 0;
	$id_item = !empty($_GET['meal']) ? (int) $_GET['meal'] : 0;
	$id_meal = !empty($_POST['meal']) ? (int) $_POST['meal'] : 0;
	$quantity = !empty($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

	foreach (array('order', 'item', 'meal') as $type)
	{
		$variable = 'id_' . $type;

		$request = db_query("
			SELECT $variable
			FROM $type
			WHERE $variable = ${$variable}
			LIMIT 1");
		list ($$type) = db_fetch_row($request);
		db_free_result($request);
	}

	if (empty($id_order))
		fatal_error('The order requested does not exist!');

	if (empty($id_item) && empty($id_meal))
		fatal_error('The meal requested does not exist!');

	if (!empty($id_meal) && $quantity > 0)
	{
		check_session('order');

		db_query("
			INSERT INTO item
				(id_order, id_meal, quantity)
			VALUES
				($id_order, $id_meal, $quantity)");
	}
	elseif (!empty($id_item))
	{
		db_query("
			DELETE FROM item
			WHERE id_item = $id_item
			LIMIT 1");
	}

	redirect(build_url(array('order', 'view', $id_order)));
}

function order_edit()
{
	global $core, $template, $user;

	$id_order = !empty($_REQUEST['order']) ? (int) $_REQUEST['order'] : 0;
	$is_new = empty($id_order);

	if ($is_new)
	{
		$template['order'] = array(
			'is_new' => true,
			'id' => 0,
			'id_table' => 0,
			'discount' => 0,
		);
	}
	else
	{
		$request = db_query("
			SELECT id_order, id_table, discount
			FROM order
			WHERE id_order = $id_order
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['order'] = array(
				'is_new' => false,
				'id' => $row['id_order'],
				'id_table' => $row['id_table'],
				'discount' => $row['discount'],
			);
		}
		db_free_result($request);

		if (!isset($template['order']))
			fatal_error('The order requested does not exist!');
	}

	if (!empty($_POST['save']))
	{
		check_session('order');

		$values = array();
		$fields = array(
			'id_table' => 'int',
			'discount' => 'int',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'int')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		if ($values['id_table'] < 1)
			fatal_error('Order table is not valid!');
		elseif ($values['discount'] < 0 || $values['discount'] > 2)
			fatal_error('Order discount is not valid!');

		if ($is_new)
		{
			$insert = array(
				'id_user' => $user['id'],
				'time' => time(),
			);

			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO order
					(" . implode(', ', array_keys($insert)) . ")
				VALUES
					(" . implode(', ', $insert) . ")");

			$id_order = db_insert_id();
		}
		else
		{
			$update = array();
			foreach ($values as $field => $value)
				$update[] = $field . " = '" . $value . "'";

			db_query("
				UPDATE order
				SET " . implode(', ', $update) . "
				WHERE id_order = $id_order
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url(array('order', 'view', $id_order)));

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Order';
	$core['current_template'] = 'order_edit';
}

function order_delete()
{
	global $core, $template;

	$id_order = !empty($_REQUEST['order']) ? (int) $_REQUEST['order'] : 0;

	$request = db_query("
		SELECT id_order, id_table
		FROM order
		WHERE id_order = $id_order
		LIMIT 1");
	while ($row = db_fetch_assoc($request))
	{
		$template['order'] = array(
			'id' => $row['id_order'],
			'table' => $row['id_table'],
		);
	}
	db_free_result($request);

	if (!isset($template['order']))
		fatal_error('The order requested does not exist!');

	if (!empty($_POST['delete']))
	{
		check_session('order');

		db_query("
			DELETE FROM order
			WHERE id_order = $id_order
			LIMIT 1");

		db_query("
			DELETE FROM item
			WHERE id_order = $id_order");

		redirect(build_url('order'));
	}

	if (!empty($_POST['delete']) || !empty($_POST['cancel']))
		redirect(build_url('order'));

	$template['page_title'] = 'Delete Order';
	$core['current_template'] = 'order_delete';
}