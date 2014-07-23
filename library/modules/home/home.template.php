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

function template_home_main()
{
	global $core, $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				', $template['total_users'], ' users &bull; ', $template['total_meals'], ' meals &bull; ', $template['total_orders'], ' orders
			</div>
			<h2>', $core['title_long'], '</h2>
		</div>
		<div class="pull-left half">
			<div class="page-header">
				<h3>Most popular meals</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Meal</th>
						<th class="span2">Portion</th>
						<th class="span2">Orders</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['popular_meals']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="3">There are not any popular meals!</td>
					</tr>';
	}

	foreach ($template['popular_meals'] as $meal)
	{
		echo '
					<tr>
						<td>', $meal['name'], '</td>
						<td class="align_center">', $template['portions'][$meal['portion']], '</td>
						<td class="align_center">', $meal['orders'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<div class="pull-right half">
			<div class="page-header">
				<h3>Recently ordered meals</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Meal</th>
						<th class="span2">Portion</th>
						<th class="span2">Price</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['recent_meals']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="3">There are not any recent meals!</td>
					</tr>';
	}

	foreach ($template['recent_meals'] as $meal)
	{
		echo '
					<tr>
						<td>', $meal['name'], '</a></td>
						<td class="align_center">', $template['portions'][$meal['portion']], '</td>
						<td class="align_center">', $meal['price'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<br class="clear" />';
}