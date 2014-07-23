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

function template_meal_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url(array('meal', 'edit')), '">Add Meal</a>
			</div>
			<h2>Meal List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Portion</th>
					<th>Price</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['meals']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="4">There are not any meals added yet!</td>
				</tr>';
	}

	foreach ($template['meals'] as $meal)
	{
		echo '
				<tr>
					<td>', $meal['name'], '</td>
					<td class="span2 align_center">', $template['portions'][$meal['portion']], '</td>
					<td class="span2 align_center">', $meal['price'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-primary" href="', build_url(array('meal', 'edit', $meal['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('meal', 'delete', $meal['id'])), '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_meal_edit()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('meal', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['meal']['is_new'] ? 'Edit' : 'Add'), ' Meal</legend>
				<div class="control-group">
					<label class="control-label" for="name">Name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="name" name="name" value="', $template['meal']['name'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="portion">Portion:</label>
					<div class="controls">
						<select id="portion" name="portion">
							<option value="0"', ($template['meal']['portion'] == 0 ? ' selected="selected"' : ''), '>Select portion</option>';

	foreach ($template['portions'] as $id => $label)
	{
		echo '
							<option value="', $id, '"', ($template['meal']['portion'] == $id ? ' selected="selected"' : ''), '>', $label, '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="price">Price:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="price" name="price" value="', $template['meal']['price'], '" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="meal" value="', $template['meal']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}

function template_meal_delete()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('meal', 'delete')), '" method="post">
			<fieldset>
				<legend>Delete Meal</legend>
				Are you sure you want to delete the meal &quot;', $template['meal']['name'], '&quot;?
				<div class="form-actions">
					<input type="submit" class="btn btn-danger" name="delete" value="Delete" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="meal" value="', $template['meal']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}