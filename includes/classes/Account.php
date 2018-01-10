<?php 

class Account {

	private $con;
	private $errorArray;


	public function __construct($con) {
		$this->con = $con;
		$this->errorArray = array();
	}


	public function login($un, $pw) {
		$pw = md5($pw);		//encrypt password
		$query = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$un' AND password = '$pw'");
		if(mysqli_num_rows($query) == 1) {
			return true;
		} else {
			array_push($this->errorArray, Constants::$loginFailed);
			return false;
		}
	}


	public function register($username, $firstName, $lastName, $email, $email2, $password, $password2) {
		$this->validateUsername($username);
		$this->validateFirstName($firstName);
		$this->validateLastName($lastName);
		$this->validateEmails($email, $email2);
		$this->validatePasswords($password, $password2);

		if(empty($this->errorArray)) {
			//insert into db
			return $this->insertUserDetails($username, $firstName, $lastName, $email, $password);
		} else {
			return false;
		}
	}


//==============================
//	Database Input Functions
//==============================

	private function insertUserDetails($un, $fn, $ln, $em, $pw) {
		$encryptedPw = md5($pw);	//simple encryption method
		$profilePic = 'assets/images/profile-pics/mchenzo.jpg';
		$date = date('Y-m-d');

		$result = mysqli_query($this->con, "INSERT INTO users VALUES ('', '$un', '$fn', '$ln', '$em', '$encryptedPw', '$date', '$profilePic')");
		return $result;
	}

//==============================
//	Error Message Handling
//==============================

	public function getError($error) {
		if(!in_array($error, $this->errorArray)) {
			$error = '';
		}
		return "<span class = 'errorMessage' >$error</span>";
	}

//==============================
//	Validation functions
//==============================

	private function validateUsername($username) {
		if(strlen($username) > 25 || strlen($username) < 5) {
			array_push($this->errorArray, Constants::$usernameCharacters);
			return;
		}

		$checkUsernameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username = '$username'");
		if(mysqli_num_rows($checkUsernameQuery) != 0) {
			array_push($this->errorArray, Constants::$usernameTaken);
			return;
		}
	}

//==========================================================================

	private function validateFirstName($firstname) {
		if(strlen($firstname) > 25 || strlen($firstname) < 2) {
			array_push($this->errorArray, Constants::$firstNameCharacters);
			return;
		}
	}

//==========================================================================

	private function validateLastName($lastname) {
		if(strlen($lastname) > 25 || strlen($lastname) < 2) {
			array_push($this->errorArray, Constants::$lastNameCharacters);
			return;
		}
	}

//==========================================================================

	private function validateEmails($email, $email2) {
		if($email != $email2) {
			array_push($this->errorArray, Constants::$emailsDoNotMatch);
			return;
		}

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {	//check if email is correct format w/ FILTER_VALIDATE_EMAIL
			array_push($this->errorArray, Constants::$emailInvalid);
			return;
		}

		$checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email = '$email'");
		if(mysqli_num_rows($checkEmailQuery) != 0) {
			array_push($this->errorArray, Constants::$emailTaken);
			return;
		}
	}

//==========================================================================

	private function validatePasswords($pass, $pass2) {
		if($pass != $pass2) {
			array_push($this->errorArray, Constants::$passwordsDoNotMatch);
			return;
		}

		if(preg_match('/[^A-Za-z0-9]/', $pass)) {
			array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
			return;
		}

		if(strlen($pass) > 30 || strlen($pass) < 5) {
			array_push($this->errorArray, Constants::$passwordCharacters);
			return;
		}
	}

}

?>