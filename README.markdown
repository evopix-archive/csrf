# What is CSRF?

A CSRF (cross-site request forgery) is a malicious attack which forces an end user to execute unwanted actions on a web application in which the user is currently authenticated.

Visit the [OWASP website](http://www.owasp.org/index.php/Cross-Site_Request_Forgery_%28CSRF%29) for more information.

# How does it work?

When a form is requested this module generates a random token and stores both the time it was generated and the token itself in the users session. Then, the current time is appended to the token and it is inserted into the form. When the form is submitted this module checks the token's validity by first calculating the time between the forms rendering and submission. If the difference is 0 or longer than 30 seconds it is assumed the form was submitted by a bot and validation fails. If the first test passes then this module checks if the token matches a token currently stored in the users session, if it does then validation is successful and the form is submitted.

# Won't this break tabbing and forward/back buttons?

To cope with these issues, this module stores up to 5 tokens in the session at a time. Each token will expire after 5 minutes.

# Example Usage

First add the modules files to your modules directory and add it to your bootstrap.

## Add token to your form

You have the option of using either the Form helper method or calling the CSRF helper directly.

Form helper:

	<?php echo Form::token() ?>

CSRF helper:

	<input type="hidden" name="token" value="<?php echo CSRF::token() ?>" />

## Add validation rule to your controller

	$post->rules
	(
		'token', array
		(
			'not_empty' => NULL,
			'csrf::valid' => NULL,
		),
	);