<?php

namespace internet\a3\controller;

use internet\a3\view\View;
/**
 * Base Class Controller
 * Includes all methods that are not associated with just 1 controller
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 */
class Controller
{
    /**
     * Determines if a user is logged in or not
     *
     * @return bool  Returns true if logged in otherwise returns false
     */
    public function isLogedIn() {
        if ($_SESSION != null) {
            return true;
        }
        return false;
    }

    /**
     * Generate a link URL for a named route
     *
     * @param string $route  Named route to generate the link URL for
     * @param array  $params Any parameters required for the route
     *
     * @return string  URL for the route
     */
    static function linkTo($route, $params=[])
    {
        // cheating here! What is a better way of doing this?
        $router = $GLOBALS['router'];
        return $router->generate($route, $params);
    }

    /**
     * This method gets called whenever a ToolshedException is thrown.
     * Do nothing other than keep the current session
     *
     * Customer Error Action
     */
    public function errorAction() {
        session_start();
    }

    /**
     * End session and log the customer out, returning them to the homepage
     */
    public function logout() {
        session_destroy();
        $this->showView('Login');
    }

    /**
     * Start a new session based on the information provided when the user logs in or registers
     *
     * @param int $id           The customers Id
     * @param string $fName     The customers first name
     * @param string $lName     The customers last name
     * @param string $userName  The customers username
     */
    public function setSession($id, $fName, $lName, $userName)
    {
        $_SESSION['id'] = $id;
        $_SESSION['first_name'] = $fName;
        $_SESSION['last_name'] = $lName;
        $_SESSION['user_name'] = $userName;
        $_SESSION['loggedin'] = true;
    }

    /**
     * Determines which view to display to the user based on the filename and the data needed to display on that page
     *
     * @param string $fileName  The name of the template file to be viewed
     * @param null $data        Optional param for data to be sent to the view
     */
    public function showView($fileName, $data = null)
    {
        $view = new View($fileName);
        if($data == null) {
            echo $view->addData('linkTo', function ($route, $params = []) {
                return $this->linkTo($route, $params);
            })->render();
        } else {
            echo $view->addData('data', $data)
                ->addData('linkTo', function ($route, $params = []) {
                    return $this->linkTo($route, $params);
                })->render();
        }
    }
}