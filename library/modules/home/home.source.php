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

function home_main()
{
	global $core, $template;

	$template['portions'] = array(1 => 'Small', 'Medium', 'Large');

	$request = db_query("
		SELECT COUNT(id_user)
		FROM user
		LIMIT 1");
	list ($template['total_users']) = db_fetch_row($request);
	db_free_result($request);

	$request = db_query("
		SELECT SUM(quantity)
		FROM item
		LIMIT 1");
	list ($template['total_meals']) = db_fetch_row($request);
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(id_order)
		FROM order
		LIMIT 1");
	list ($template['total_orders']) = db_fetch_row($request);
	db_free_result($request);

	$request = db_query("
		SELECT m.name, m.portion, SUM(i.quantity) AS orders
		FROM item AS i
			LEFT JOIN meal AS m ON (m.id_meal = i.id_meal)
		GROUP BY i.id_meal
		ORDER BY orders DESC
		LIMIT 5");
	$template['popular_meals'] = array();
	while ($row = db_fetch_assoc($request))
	{
		if ($row['orders'] < 1)
			continue;

		$template['popular_meals'][] = array(
			'name' => $row['name'],
			'portion' => $row['portion'],
			'orders' => $row['orders'],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT m.name, m.portion, m.price
		FROM item AS i
			INNER JOIN meal AS m ON (m.id_meal = i.id_meal)
		GROUP BY i.id_meal
		ORDER BY i.id_item DESC
		LIMIT 5");
	$template['recent_meals'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['recent_meals'][] = array(
			'name' => $row['name'],
			'portion' => $row['portion'],
			'price' => $row['price'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Home';
	$core['current_template'] = 'home_main';
}