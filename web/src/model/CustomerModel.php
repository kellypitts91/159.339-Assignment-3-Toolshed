<?php
namespace internet\a3\model;

use internet\a3\Exception\ToolshedException;


/**
 * Class CustomerModel
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 */
class CustomerModel extends Model
{
    /*
     * @var integer Customer ID
     */
    private $_id;
    /*
     * @var string Customer first name
     */
	private $_strFirstName;
	/*
	 * @var string Customer last name
	 */
	private $_strLastName;
	/*
	 * @var string Customer user name
	 */
    private $_strUserName;
    /*
     * @var string Customer email address
     */
	private $_strEmail;
	/*
	 * @var string Customer hashed password
	 */
	private $_strHashedPassword;

    /**
     * Calls parent constructor
     *
     * CustomerModel constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return int Customer ID
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return string Customer first name
     */
    public function getFirstName()
    {
        return $this->_strFirstName;
    }
	
	/**
     * @return string Customer last name
     */
    public function getLastName()
    {
        return $this->_strLastName;
    }

    /**
     * @return string Customer user name
     */
    public function getUserName()
    {
        return $this->_strUserName;
    }

	/**
     * @return string Customer email address
     */
    public function getEmail()
    {
        return $this->_strEmail;
    }

    /**
     * @param string $fName Customer first name
     *
     * @return $this CustomerModel
     */
    public function setFirstName(string $fName)
    {
        $this->_strFirstName = mysqli_real_escape_string($this->db, $fName);
        return $this;
    }
	
	/**
     * @param string $lName Customer last name
     *
     * @return $this CustomerModel
     */
    public function setLastName(string $lName)
    {
        $this->_strLastName = mysqli_real_escape_string($this->db, $lName);
        return $this;
    }

    /**
     * Sets the username if it does not contain any special characters
     *
     * @param string $userName Customer user name
     * @return $this CustomerModel
     * @throws ToolshedException
     */
    public function setUserName(string $userName)
    {
        //must be only alphanumeric characters
        if(preg_match("/^[a-zA-Z0-9]+$/", $userName)) {
            $this->_strUserName = mysqli_real_escape_string($this->db, $userName);
            return $this;
        } else {
            throw new ToolshedException("Username must contain only alphanumeric characters");
        }

    }
	
	/**
     * @param string $email Customer email address
     * @return $this CustomerModel
     */
    public function setEmail(string $email)
    {
        $this->_strEmail =  mysqli_real_escape_string($this->db, $email);
        return $this;
    }

    /**
     * Passwords need to be checked that they match before continuing
     * Passwords must be at least 7-15 characters long
     * Contain 1 uppercase letter and not contain any special characters
     *
     * @param string $p1 Customer first Password
     * @param string $p2 Customer second Password
     * @return $this CustomerModel
     * @throws ToolshedException
     */
    public function setPassword(string $p1, string $p2) {
        //checking only alphanumeric characters entered and must have at least 1 uppercase letter
        if(preg_match("/(?=.*[A-Z])[A-Za-z0-9]{7,15}/", $p1)) {
            $this->_strHashedPassword = $this->checkPasswordMatch($p1, $p2);
            return $this;
        } else {
            throw new ToolshedException("Error: the password must contain at least 1 uppercase letter, be between 7 and 15 characters long and not contain any special characters");
        }
    }

    /**
     * Checking passwords match and then returning the hashed password
     *
     * @param string $p1 Customer first Password
     * @param string $p2 Customer second Password
     * @return string   Returns the hashed password
     * @throws ToolshedException
     */
    public function checkPasswordMatch(string $p1, string $p2) {
        $p1 = mysqli_real_escape_string($this->db, $p1);
        $p2 = mysqli_real_escape_string($this->db, $p2);
        if($p1 == $p2) {
            return password_hash($p1, PASSWORD_DEFAULT);
        } else {
            throw new ToolshedException("Passwords do not match");
        }
    }

    /**
     * Loads Customer information from the database
     * Checking the returned set returns only 1 row to prevent sql-injection
     *
     * @param int $id Customer ID
     * @return $this CustomerModel
     * @throws ToolshedException
     */
    public function load($id)
    {
        if (!$result = $this->db->query("SELECT * FROM `".TABLE_CUSTOMER."` WHERE `cus_id` = '$id';")) {
            throw new ToolshedException("Select customer failed: (". $this->db->errno . ") " . $this->db->error);
        }
		if(mysqli_num_rows($result) != 1) {
            throw  new ToolshedException("Username not found");
		} else {
            $row = $result->fetch_assoc();

            $this->_id = $id;
            $this->_strFirstName = $row['cus_fName'];
            $this->_strLastName = $row['cus_lName'];
            $this->_strUserName = $row['cus_userName'];
            $this->_strEmail = $row['cus_email'];
            $this->_strHashedPassword = $row['cus_pass'];
        }
        return $this;
    }

    /**
     * Saves Customer information to the database
     * if customer already exist, throws a ToolshedException
     *
     * @return $this CustomerModel
     * @throws ToolshedException
     */
    public function save()
    {
        if($this->userExist() > 0 && $_POST != null) {
            throw new ToolshedException("The username you entered already exist");
        }
        $fName = $this->_strFirstName??"NULL";
		$lName = $this->_strLastName??"NULL";
		$uName = $this->_strUserName??"NULL";
		$email = $this->_strEmail??"NULL";
		$pass = $this->_strHashedPassword??"NULL";

        if (!isset($this->_id)) {
            // New Customer - Perform INSERT
            // preparing the sql statement as a safety measure
            $stmt = $this->db->prepare("INSERT INTO `".TABLE_CUSTOMER."` VALUES (?, ?, ?, ?, ?, ?);");
            $stmt->bind_param('dsssss', $this->_id, $fName, $lName, $uName, $email, $pass);

            $stmt->execute();
            $stmt->close();
//            if (!$result = $this->db->query("INSERT INTO `".TABLE_CUSTOMER."` VALUES (NULL, '$fName', '$lName', '$uName', '$email', '$pass');")) {
//                throw new ToolshedException("Insert customer failed: (". $this->db->errno . ") " . $this->db->error);
//            }

            $this->_id = $this->db->insert_id;
        }
        return $this;
    }

    /**
     * Checks if user already exist in the database
     * if 1 or more rows are returned, the customer already exist (should only ever be 0 or 1)
     *
     * @return int  Returns number of rows found with that username
     * @throws ToolshedException
     */
    public function userExist() {
        if(!$result = $this->db->query("SELECT * FROM `".TABLE_CUSTOMER."` WHERE `cus_userName` = '$this->_strUserName'")) {
            throw new ToolshedException("Check email failed: (". $this->db->errno . ") " . $this->db->error);
        }
        return $result->num_rows;
    }

    /**
     * Method checks if the username and password entered match a valid username and (hashed)password in the database
     *
     * @param string $uName user name entered by user
     * @param string $pass plain text password entered by the user
     * @return int
     * @throws ToolshedException
     */
    public function validateUser($uName, $pass) {

        if(!$res = $this->db->query("SELECT * FROM `".TABLE_CUSTOMER."` WHERE `cus_userName` = '$uName'")){
            throw new ToolshedException("Validate customer failed: (". $this->db->errno . ") " . $this->db->error);
        }

        if($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if(password_verify($pass, $row['cus_pass'])) {
                return $row['cus_id']; //email and password match
            }
            throw new ToolshedException("Password incorrect"); //password incorrect
        }
        throw new ToolshedException("Username incorrect"); //email incorrect
    }
}