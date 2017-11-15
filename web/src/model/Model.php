<?php
namespace internet\a3\model;

use internet\a3\Exception\ToolshedException;
use mysqli;
require_once 'db_creds.php';

/**
 * Class Model - Base Model that creates database if it doesn't exist
 * Creates and populates the 2 tables (Customer and Product)
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 */
class Model
{
    /*
     * @var mysqli $db Database
     */
    protected $db;
    /*
     * Count to keep track of how many tables have been created. only want to populate the tables once all tables have been created.
     * @var int $count
     */
    private $count;

    /**
     * Creates a new database connection
     * Populates with 2 tables
     * populates the 2 tables with dummy data on first load if the tables are being created for the first time
     *
     * Model constructor.
     * @throws ToolshedException    if we can't connect to the database or the database is not available
     */
    function __construct()
    {
        $this->db = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS
        );

        //throw an exception if cannot connect to database
        if (!$this->db) {
            throw new ToolshedException("Cannot connect to database: (" . $this->db->errno . ") " . $this->db->error);
        }

        //----------------------------------------------------------------------------
        // This is to make our life easier
        // Create database
        $this->db->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME . ";");

        if (!$this->db->select_db(DB_NAME)) {
            throw new ToolshedException("Mysql database not available!");
        }

        //storing the queries into a string for readability
        //when sending the string to the method to create the new table
        $sqlCustomer = "CREATE TABLE `".TABLE_CUSTOMER."` (
                                        `cus_id` INT NOT NULL AUTO_INCREMENT, 
										`cus_fName` VARCHAR(50) NOT NULL, 
										`cus_lName` VARCHAR(50) NOT NULL, 
										`cus_userName` VARCHAR(20) NOT NULL,
										`cus_email` VARCHAR(50) NOT NULL, 
										`cus_pass` VARCHAR(100) NOT NULL,
										Primary key (cus_id));";

        $sqlProduct = "CREATE TABLE `".TABLE_PRODUCT."` (
                                        `prod_sku` VARCHAR(20) NOT NULL, 
										`prod_name` VARCHAR(100) NOT NULL, 
										`prod_category` VARCHAR(50) NOT NULL,
										`prod_cost` DECIMAL(6,2) NOT NULL,
										`prod_qty` INT NOT NULL,
										PRIMARY KEY (`prod_sku`))";
        $this->count = 0;
        $this->createNewTable($sqlCustomer, 'customer');
        $this->createNewTable($sqlProduct, 'product');
    }

    /**
     * Method to create the table if it does not already exist
     *
     * @param string $sqlQuery      query to create the new table
     * @param string $tableName     table name to check the table does not already exist
     * @throws ToolshedException    if the table failed to create
     */
    public function createNewTable($sqlQuery, $tableName)
    {
        $result = $this->db->query("SHOW TABLES LIKE '$tableName';");
        if ($result->num_rows == 0) {
            // table doesn't exist
            // create it and populate with sample data

            if (!$result = $this->db->query($sqlQuery)) {
                // handle appropriately
                throw new ToolshedException("Failed creating table: " . $tableName);
            }
            $this->count++;
            if($this->count == 2) { //only want to insert rows after all tables have been created
                $this->insertDummyData();
            }
        }
    }

    /**
     * Inserts dummy data into the 2 tables upon first load
     * only runs when the tables have been created for the first time
     */
    public function insertDummyData()
    {
        $this->insertCustomer('Tim', 'Taylor', 'TheToolman', 'tim.taylor@gmail.com', "TheToolman");

        $this->insertProduct("ham13", "Brass Hammer", "Hammers", "39.95", 10);
        $this->insertProduct("ham15", "Claw Hammer", "Hammers", "39.95", 0);
        $this->insertProduct("screw03", "Square Screwdriver", "Screwdrivers", "11.95", 17);
        $this->insertProduct("ham14", "Sledge Hammer", "Hammers", "42.95", 10);
        $this->insertProduct("span02", "Adjustable Spanner", "Spanners and Wrenches", "39.95", 20);
        $this->insertProduct("screw01", "Hex Screwdriver", "Screwdrivers", "11.95", 15);
        $this->insertProduct("screw02", "Phillips Screwdriver", "Screwdrivers", "22.95", 12);
        $this->insertProduct("span01", "Double Ring Spanner", "Spanners and Wrenches", "25.95", 4);
        $this->insertProduct("screw05", "Slotted Screwdriver", "Screwdrivers", "14.95", 0);
        $this->insertProduct("span03", "Open ended Spanner", "Spanners and Wrenches", "35.95", 10);
        $this->insertProduct("plier05", "Small Pliers", "Pliers", "9.95", 6);
        $this->insertProduct("plier02", "Medium Pliers", "Pliers", "8.55", 0);
        $this->insertProduct("plier01", "Large Pliers", "Pliers", "12.05", 12);
        $this->insertProduct("heat01", "Heavy Duty Heat Gun", "Heat Guns", "49.95", 12);
    }

    /**
     * Method used to insert new customers
     *
     * @param string $fName Customers first name
     * @param string $lName Customers last name
     * @param string $uName Customers user name
     * @param string $email Customers email address
     * @param string $pass Customers plain text password
     */
    public function insertCustomer($fName, $lName, $uName, $email, $pass) {
        $customer = new CustomerModel();
        $customer->setFirstName($fName);
        $customer->setLastName($lName);
        $customer->setUserName($uName);
        $customer->setEmail($email);
        $customer->setPassword($pass, $pass);
        $customer->save();
    }

    /**
     * Inserts products into the product table
     *
     * @param string $sku Product sku (Stock Keeping Unit)
     * @param string $name Product name
     * @param string $category Product category
     * @param float $cost Product cost
     * @param int $qty Product quantity on hand
     */
    public function insertProduct($sku, $name, $category, $cost, $qty) {
        $product = new ProductModel();
        $product->setSku($sku);
        $product->setName($name);
        $product->setCategory($category);
        $product->setCost($cost);
        $product->setQty($qty);
        $product->save();
    }
}