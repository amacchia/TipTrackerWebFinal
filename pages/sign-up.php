<?php
$fname = $lname = $email = $pword = $org = $errMsg = "";
$fnameErrMsg = $lnameErrMsg = $emailErrMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (empty($_POST["fname"])) {
		$errMsg .= "Please enter a first name <br>";
	}
	else{
		$fname = clean_input($_POST["fname"]);
		validateFName($fname);
	}

	if (empty($_POST["lname"])) {
		$errMsg .= "Please enter a last name <br>";
	}
	else{
		$lname = clean_input($_POST["lname"]);
		validateLName($lname);
	}

	if (empty($_POST["email"])) {
		$errMsg .= "Please enter an email <br>";
	}
	else{
		$email = clean_input($_POST["email"]);
		validateEmail($email);
	}

	if (empty($_POST["pword"])) {
		$errMsg .= "Please enter a password <br>";
	}

	$fname = clean_input($_POST["fname"]);
	$lname = clean_input($_POST["lname"]);
	$email = clean_input($_POST["email"]);
	$pword = clean_input($_POST["pword"]);
	$org = clean_input($_POST["org"]);

	if ($errMsg == "") {
		if ($org != "") {
			insert_user_with_org($fname, $lname, $email, $pword, $org);
		} else {
			insert_user_without_org($fname, $lname, $email, $pword);
		}
	}
}

function insert_user_with_org($first_name, $last_name, $email, $pword, $org)
{
	$hash = password_hash($pword, PASSWORD_DEFAULT);

	$orgID = get_org_id($org);

	$sql = "INSERT INTO 
		users (first_name, last_name, email, pword, user_organization) 
		VALUES
		('$first_name', '$last_name', '$email', '$hash', '$orgID')";

	$result = $GLOBALS['conn']->query($sql);
	if ($result) {
		header('Location: index.php?page=login');
	} else {
		echo "Error: " . $sql . "<br>" . $GLOBALS['conn']->error;
	}

	$GLOBALS['conn']->close();
}

function insert_user_without_org($first_name, $last_name, $email, $pword)
{
	$hash = password_hash($pword, PASSWORD_DEFAULT);

	$sql = "INSERT INTO 
		users (first_name, last_name, email, pword) 
		VALUES
		('$first_name', '$last_name', '$email', '$hash')";

	$result = $GLOBALS['conn']->query($sql);
	if ($result) {
		header('Location: index.php?page=login');
	} else {
		echo "Error: ". $GLOBALS['conn']->error;
	}

	$GLOBALS['conn']->close();
}

function clean_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function get_org_id($org) {
	$sql = "SELECT org_id FROM organizations WHERE org_name = '$org'";

	$orgResult = $GLOBALS["conn"]->query($sql);

	if ($orgResult) {
		$row = $orgResult->fetch_assoc();
		if (count($row) > 0) {
			return $row['org_id'];		
		} else {
			// Insert new org
			$insertSQL = "INSERT INTO organizations (org_name) VALUES ('$org')";
			if ($GLOBALS["conn"]->query($insertSQL)) {
				return get_org_id($org);	
			} else {
				echo 'Error inserting org <br>';
			}
		}	
	}else {
		echo 'Error getting org <br>';
	}
}

function validateFName($fname) {
	$regex = "/[a-zA-z]+$/";
	if (!preg_match($regex, $fname) || strlen($lname) > 50) {
		global $fnameErrMsg;
		$fnameErrMsg = "First name cannot contain special characters or be longer than 50 characters";
	}
}

function validateLName($lname){
	$regex = "/[a-zA-z]+$/";
	if (!preg_match($regex, $lname) || strlen($lname) > 50) {
		global $lnameErrMsg;
		$lnameErrMsg = "Last name cannot contain special characters or be longer than 50 characters";
	}
}
function validateEmail($email) {
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		global $emailErrMsg;
		$emailErrMsg = "Invalid email format.";
	}
}
?>

<div class="container">
<h1 id="sign-up-form-title">Sign Up</h1>
<div id="sign-up-form">
    <form method="POST" action="./index.php?page=sign-up">

	<h6 class="input-label">First Name</h6>
	<input type="text" id="first" name="fname" class="form-input">
	&nbsp; <span id="first-valid" hidden></span>
	<span id="first-invalid" hidden></span>
	<span id="first-err-msg" hidden></span>
	<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($fnameErrMsg != "") {
				echo '<span id="server-first-msg" class="server-err-msg">'.$fnameErrMsg.'</span>';
			}
		}
	?>
	<br>

	<h6 class="input-label">Last Name</h6>
	<input type="text" id="last" name="lname" class="form-input">
	&nbsp; <span id="last-valid" hidden></span>
	<span id="last-invalid" hidden></span>
	<span id="last-err-msg" hidden></span>
	<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($lnameErrMsg != "") {
				echo '<span id="server-last-msg" class="server-err-msg">'.$lnameErrMsg.'</span>';
			}
		}
	?>
	<br>

	<h6 class="input-label">Email</h6>
	<input type="email" id ="email" name="email" class="form-input">
	&nbsp; <span id="email-valid" hidden></span>
	<span id="email-invalid" hidden></span>
	<span id="email-err-msg" hidden></span>
	<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($emailErrMsg != "") {
				echo '<span id="server-email-msg" class="server-err-msg">'.$emailErrMsg.'</span>';
			}
		}
	?>
	<br>

	<h6 class="input-label">Password</h6>
	<input type="password" name="pword" class="form-input"><br>

	<h6 class="input-label">Organization (optional)</h6>
		<input type="text" name="org" class="form-input"><br><br>

	<input type="submit" value="Sign Up" id="sub-btn" disabled>
	<p>Already a user? <a href="./index.php?page=login">Login</a></p>
    </form>
</div>

<div class="errorMsg">
<?php
if ($errMsg != "") {
	echo 'Error: ' . $errMsg;
}
?>
</div>
</div>
<script src="./sign-up.js"></script>
