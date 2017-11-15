<?php
namespace internet\a3\model;

use internet\a3\Exception\ToolshedException;

/**
 * Class AjaxModel      used for Ajax queries
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 */
class AjaxModel extends Model
{
    /**
     * Calls parent constructor
     * AjaxModel constructor.
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Returns products by giving a product name
     * using % wildcard to return all rows that have the param in it
     *
     * @param $name string
     * @return array
     * @throws ToolshedException
     */
    public function getProducts($name)
    {
        if (!$result = $this->db->query("SELECT * FROM `".TABLE_PRODUCT."`
                                                WHERE `prod_name` LIKE '%$name%'
                                                ORDER by `prod_sku` ASC;")) {
            throw new ToolshedException("Failed to retrieve any products from the database: (" . $this->db->errno . ") " . $this->db->error);
        }
        return $result->fetch_all();
    }

    /**
     * Returns products by category given an array of category names
     * including out of stock products
     *
     * @param array $category
     * @return array
     * @throws ToolshedException
     */
    public function getProductsByCategory($category)
    {
        if (!$result = $this->db->query("SELECT `prod_sku`, `prod_name`, `prod_cost`, `prod_qty` FROM `".TABLE_PRODUCT."`
                                                WHERE `prod_category` IN ('".implode("','",$category)."')
                                                ORDER by `prod_sku` ASC;")) {
            throw new ToolshedException("Failed to retrieve any products from the database: (" . $this->db->errno . ") " . $this->db->error);
        }
        return $result->fetch_all();
    }

    /**
     * Returns products by category given an array of category names
     * excluding out of stock products
     *
     * @param array $category
     * @return mixed
     * @throws ToolshedException
     */
    public function getProductsInStock(array $category)
    {
        if (!$result = $this->db->query("SELECT `prod_sku`, `prod_name`, `prod_cost`, `prod_qty` FROM `".TABLE_PRODUCT."`
                                                WHERE `prod_category` IN ('".implode("','",$category)."')
                                                AND `prod_qty` > 0
                                                ORDER by `prod_sku` ASC;")) {
            throw new ToolshedException("Failed to retrieve any products from the database: (" . $this->db->errno . ") " . $this->db->error);
        }
        return $result->fetch_all();
    }

    /**
     * Checks the username and
     * Returns true if the username already exist in database
     *
     * @param string    $uName username
     * @return bool     true if user exists, false otherwise
     * @throws ToolshedException
     */
    public function CheckUserName($uName)
    {
        if (!$result = $this->db->query("SELECT * FROM ".TABLE_CUSTOMER."
                                                WHERE cus_userName = '".$uName."';")) {
            throw new ToolshedException("Failed to retrieve any products from the database: (" . $this->db->errno . ") " . $this->db->error);
        }
        if(mysqli_num_rows($result) == 0) {
            return false;
        } else{
            return true;
        }
    }
}