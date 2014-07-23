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

function template_order_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url(array('order', 'edit')), '">Add Order</a>
			</div>
			<h2>Order List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Order</th>
					<th>Table</th>
					<th>Discount</th>
					<th>User</th>
					<th>Time</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['orders']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="6">There are not any orders added yet!</td>
				</tr>';
	}

	foreach ($template['orders'] as $order)
	{
		echo '
				<tr>
					<td>Order #', $order['id'], '</td>
					<td class="span2 align_center">Table #', $order['table'], '</td>
					<td class="span2 align_center">', $template['discounts'][$order['discount']], '</td>
					<td class="span2 align_center">', $order['user'], '</td>
					<td class="span3 align_center">', $order['time'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-success" href="', build_url(array('order', 'view', $order['id'])), '">View</a>
						<a class="btn btn-primary" href="', build_url(array('order', 'edit', $order['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('order', 'delete', $order['id'])), '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_order_view()
{
	global $template, $user;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn" href="', build_url('order'), '">Back</a>
				<a class="btn btn-info" href="', build_url(array('order', 'print', $template['order']['id'])), '" target="_blank">Print</a>
				<a class="btn btn-primary" href="', build_url(array('order', 'edit', $template['order']['id'])), '">Edit</a>
				<a class="btn btn-danger" href="', build_url(array('order', 'delete', $template['order']['id'])), '">Delete</a>
			</div>
			<h2>View Order</h2>
		</div>
		<dl class="dl-horizontal well">
			<dt>Order:</dt>
			<dd>Order #', $template['order']['id'], '</dd>
			<dt>Table:</dt>
			<dd>Table #', $template['order']['table'], '</dd>
			<dt>Discount:</dt>
			<dd>', $template['discounts'][$template['order']['discount']], '</dd>
			<dt>User:</dt>
			<dd>', $template['order']['user'], '</dd>
			<dt>Time:</dt>
			<dd>', $template['order']['time'], '</dd>
		</dl>
		<div class="page-header">
			<h3>Ordered Meals</h3>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Portion</th>
					<th>Quantity</th>
					<th>Price</th>
					<th>Total</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['items']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="6">There are not any meals added yet!</td>
				</tr>';
	}

	foreach ($template['items'] as $meal)
	{
		echo '
				<tr>
					<td>', $meal['name'], '</td>
					<td class="span2 align_center">', $template['portions'][$meal['portion']], '</td>
					<td class="span2 align_center">', $meal['quantity'], '</td>
					<td class="span2 align_center">', $meal['price'], '</td>
					<td class="span2 align_center">', $meal['total'], '</td>
					<td class="span2 align_center">
						<a class="btn btn-danger" href="', build_url(array('order', 'meal', $template['order']['id'], $meal['id'])), '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>
		<div class="page-header">
			<h3>Add Meal</h3>
		</div>
		<form class="form-horizontal" action="', build_url(array('order', 'meal')), '" method="post">
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="meal">Meal:</label>
					<div class="controls">
						<select id="meal" name="meal">
							<option value="0">Select meal</option>';

	foreach ($template['meals'] as $meal)
	{
		echo '
							<option value="', $meal['id'], '">', $meal['name'], ' (', $template['portions'][$meal['portion']], ')</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="quantity">Quantity:</label>
					<div class="controls">
						<input type="text" class="input-small" id="quantity" name="quantity" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="submit" value="Submit" />
				</div>
			</fieldset>
			<input type="hidden" name="order" value="', $template['order']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}

function template_order_print()
{
	global $template, $user;

	echo '
		<div class="page-header">
			<h3 class="pull-right">', $template['order']['time'], '</h3>
			<h2>Order Summary (#', $template['order']['id'], ')</h2>
		</div>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Portion</th>
					<th>Quantity</th>
					<th>Price</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['items']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="5">There are not any meals added yet!</td>
				</tr>';
	}

	foreach ($template['items'] as $meal)
	{
		echo '
				<tr>
					<td>', $meal['name'], '</td>
					<td class="align_center">', $template['portions'][$meal['portion']], '</td>
					<td class="align_center">', $meal['quantity'], '</td>
					<td class="align_center">', $meal['price'], '</td>
					<td class="align_center">', $meal['total'], '</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>
		<p class="align_right highlight">Grand Total: ', $template['total'], ($template['order']['discount'] ? '<br />Discounted Total: ' . $template['discounted'] : ''), '</p>
		<br class="clear" />';
}

function template_order_edit()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('order', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['order']['is_new'] ? 'Edit' : 'Add'), ' Order</legend>
				<div class="control-group">
					<label class="control-label" for="id_table">Table:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="id_table" name="id_table" value="', $template['order']['id_table'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="discount">Discount:</label>
					<div class="controls">
						<select id="discount" name="discount">';

	foreach ($template['discounts'] as $id => $label)
	{
		echo '
							<option value="', $id, '"', ($template['order']['discount'] == $id ? ' selected="selected"' : ''), '>', $label, '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="order" value="', $template['order']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}

function template_order_delete()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('order', 'delete')), '" method="post">
			<fieldset>
				<legend>Delete Order</legend>
				Are you sure you want to delete the order &quot;Order #', $template['order']['id'], '&quot; for &quot;Table #', $template['order']['table'], '&quot;?
				<div class="form-actions">
					<input type="submit" class="btn btn-danger" name="delete" value="Delete" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="order" value="', $template['order']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}