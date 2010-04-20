<?php defined('SYSPATH') or die('No direct script access.');

class EvoPix_Form extends Kohana_Form {

	/**
	 * Creates a csrf token form input.
	 *
	 * @access  public
	 * @return  string
	 */
	public static function token()
	{
		return Form::input('token', csrf::token(), array('type' => 'hidden'));
	}

}