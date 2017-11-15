<?php
namespace internet\a3\controller;

use internet\a3\Exception\ToolshedException;
use internet\a3\model\Model;
use internet\a3\model\CustomerModel;

/**
 * Class CustomerController - Determines which view to display and what data to send to the view from the model
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 */
class CustomerController extends Controller
{
    /**
     * Action called when user goes to the homepage
     * Checks if user is already logged in or not
     * if they are logged in, they will be logged out for security reasons
     *
     * Customer Index action
     */
    public function indexAction()
    {
        session_start();
        try {
            $model = new Model(); //setting up the tables and inserting dummy data when user first visits website
        }catch (ToolshedException $ex) {
            $this->showView('Error', $ex->getErrorMessage());
        }

        //Toolshed is only available within NZ
        date_default_timezone_set("Pacific/Auckland");

        if (isset($_SESSION['loggedin'])) {
            $this->logout();
        } else {
            $this->showView('Login');
        }
    }

    /**
     * Action called when user clicks sign up button
     * display new view
     *
     * Customer Register action
     */
    public function registerAction()
    {
        session_start();
        $this->showView('Register');
    }

    /**
     * Action called when user creates a new account
     * Gets all the $_POST data and sets the private members of the CustomerModel
     * validating all data before saving the new customer
     * If any of the validation fails, will be redirected to an error page displaying the error
     *
     * Customer Create action
     */
    public function createAction()
    {
        session_start();
        try {
            //Storing names in title case
            $fName = ucfirst(strtolower($_POST['firstName']));
            $lName = ucfirst(strtolower($_POST['lastName']));

            $customer = new CustomerModel();
            $customer->setFirstName($fName);
            $customer->setLastName($lName);
            $customer->setUserName($_POST['userName']);
            $customer->setEmail($_POST['eMail']);
            $customer->setPassword($_POST['pass1'], $_POST['pass2']);
            $customer->save();

            if(!isset($_SESSION['id'])) {
                //creating a new session and automatically logging them in
                $this->setSession($customer->getId(),
                    $customer->getFirstName(),
                    $customer->getLastName(),
                    $customer->getUserName());
            }
            //getting data to display on the customers welcome page
            $currentCustomer = array($customer->getFirstName(), $customer->getLastName());
            $this->showView('Welcome', $currentCustomer);
        } catch (ToolshedException $ex) {
            $this->showView('Error', $ex->getErrorMessage());
        }
    }

    /**
     * Action called when customer goes to log in page
     * Redirects to welcome page if already logged in
     *
     * Customer Login action
     */
    public function loginAction()
    {
        session_start();
        try {
            $customer = new CustomerModel();
            $customer->load($_SESSION['id']);

            //checking if they are logged in, should not be able to see the welcome page if not logged in
            if ($this->isLogedIn()) {
                $this->showView('Welcome');
            } else {
                $this->showView('Login');
            }
        } catch (ToolshedException $ex) {
            $this->showView('Error', $ex->getErrorMessage());
        }
    }

    /**
     * Action called when customer is redirected to the welcome page
     * validates the user name and password entered
     * creates a new session if one does not exist
     *
     * Customer Welcome action
     */
    public function welcomeAction()
    {
        session_start();
        try {
            if ($this->isLogedIn()) {
                $this->showView('Welcome');
            } else {
                $name = $_POST['userName'];
                $pass = $_POST['password'];

                $customer = new CustomerModel();
                $customer->load($customer->validateUser($name, $pass));

                if (!isset($_SESSION['id']) || $_SESSION == null) {
                    //Set the current session
                    $this->setSession($customer->getId(),
                        $customer->getFirstName(),
                        $customer->getLastName(),
                        $customer->getUserName());
                }
                if ($this->isLogedIn()) {
                    $this->showView('Welcome');
                } else {
                    $this->showView('Login');
                }
            }
        } catch (ToolshedException $ex) {
            $this->showView('Error', $ex->getErrorMessage());
        }
    }

    /**
     * Navigates to search page if logged in
     */
    public function searchAction() {

        session_start();

        if ($this->isLogedIn()) {
            $this->showView('Search');
        } else {
            $this->showView('Login');
        }
    }

    /**
     * Navigates to browse page if logged in
     */
    public function browseAction() {

        session_start();

        if ($this->isLogedIn()) {
            $this->showView('Browse');
        } else {
            $this->showView('Login');
        }
    }
}