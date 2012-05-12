<?php

namespace Zco\Bundle\UserBundle;

final class UserEvents
{
	const CHECK_PASSWORD = 'zco_user.check_password';
	
	const FORM_LOGIN = 'zco_user.form_login';
	
	const ENV_LOGIN = 'zco_user.env_login';
	
	const PRE_LOGIN = 'zco_user.pre_login';
	
	const POST_LOGIN = 'zco_user.post_login';
	
	const PRE_LOGOUT = 'zco_user.pre_logout';
	
	const POST_LOGOUT = 'zco_user.post_logout';
	
	const PRE_REGISTER = 'zco_user.pre_register';
	
	const POST_REGISTER = 'zco_user.post_register';
	
	const VALIDATE_USERNAME = 'zco_user.validate_username';
	
	const VALIDATE_EMAIL = 'zco_user.validate_email';
	
	const VALIDATE_PASSWORD = 'zco_user.validate_password';
}