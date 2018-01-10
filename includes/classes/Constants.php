<?php 

class Constants {

	public static $passwordsDoNotMatch = 'Your passwords do not match';		//static means you don't need to create an instance of a class
	public static $passwordNotAlphanumeric = 'Your password can only contain numbers or letters';
	public static $passwordCharacters = 'Your password must be between 5 and 30 characters';
	public static $emailInvalid = 'Invalid email';
	public static $emailTaken = 'This email is already being used';
	public static $emailsDoNotMatch = 'Your emails do not match';
	public static $lastNameCharacters = 'Your last name must be between 2 and 25 characters';
	public static $firstNameCharacters = 'Your first name must be between 2 and 25 characters';
	public static $usernameCharacters = 'Your username must be between 5 and 25 characters';
	public static $usernameTaken = 'This username is already taken';

	public static $loginFailed = 'Incorrect username or password';

}

?>