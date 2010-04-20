<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cross-Site Request Forgery helper.
 *
 * @package    CSRF
 * @author     Brandon Summers
 * @copyright  (c) 2010 Brandon Summers
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class EvoPix_CSRF {

	/**
	 * Singleton instance.
	 * 
	 * @access  protected
	 * @var     CSRF       singleton instance of CSRF
	 */
	protected static $instance = NULL;

	/**
	 * Gets the singleton instance.
	 * 
	 * @access  public
	 * @return  CSRF
	 */
	public static function instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new CSRF;
		}

		return self::$instance;
	}

	/**
	 * Constructor. Enforces singleton behavior by only creating a new class 
	 * if an instance doesn't already exist.
	 * 
	 * @acess   protected
	 * @return  CSRF
	 */
	protected function __construct()
	{
		if (isset(self::$instance) and ! is_null(self::$instance))
		{
			return self::$instance;
		}
	}

	/**
	 * Gets a new token and appends the creation time to the end.
	 * 
	 * @access  public
	 * @return  string
	 */
	public static function token()
	{
		$token = arr::get(self::instance()->generate_token(), 'token');

		return $token.'-'.time();
	}

	/**
	 * Checks if a token is valid. Used in conjunction with the Validation 
	 * library.
	 * 
	 * @access  public
	 * @param   string   token to validate
	 * @return  boolean
	 */
	public static function valid($token)
	{
		return self::instance()->validate_token($token);
	}

	/**
	 * Generates a new token and saves it to the session.
	 * 
	 * @access  protected
	 * @return  array
	 */
	protected function generate_token()
	{
		$tokens = Session::instance()->get('tokens', array());
		
		// Remove expired tokens
		$tokens = $this->clean_tokens($tokens);
		
		// Only store 5 tokens at a time.
		if (count($tokens) >= 5)
		{
			$tokens = array_values(array_slice($tokens, 0, 5, TRUE));
		}
		
		$token = array
		(
			'ts' => time(),
			'token' => sha1(uniqid(rand(), TRUE)),
		);
		
		$tokens[] = $token;
		Session::instance()->set('tokens', $tokens);
		
		return $token;
	}

	/**
	 * Removes expired tokens from the session.
	 * 
	 * @access  protected
	 * @param   array      tokens currently in session
	 * @return  array
	 */
	protected function clean_tokens($tokens)
	{
		$time = time();
		
		foreach (array_keys($tokens) as $key)
		{
			if ($tokens[$key]['ts'] > $time + 86400)
			{
				unset($tokens[$key]);
			}
		}
		
		return $tokens;
	}

	/**
	 * Validates a token against tokens stored in session. If the difference 
	 * between the time generated and current time is 0 or greater than 30 
	 * seconds it fails. If a matching token isn't found it session it also 
	 * fails.
	 * 
	 * @access  protected
	 * @param   string     token to validate
	 * @return  boolean
	 */
	protected function validate_token($token)
	{
		$split_token = explode('-', $token);
		$token = $split_token[0];
		$time = abs($split_token[1]);
		
		$diff = (time() - $time);
		
		if ($diff AND ($diff <= 30))
		{
			$tokens = Session::instance()->get('tokens', array());
			
			if ( ! is_array($tokens))
				return FALSE;
			
			foreach (array_keys($tokens) as $key)
			{
				if ($tokens[$key]['token'] == $token)
				{
					return TRUE;
				}
			}
		}

		return FALSE;
	}

}