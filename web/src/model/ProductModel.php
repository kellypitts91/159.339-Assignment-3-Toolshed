<?php
namespace internet\a3\model;

use internet\a3\Exception\ToolshedException;

/**
 * Class ProductModel - Inserts data into the Product table
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 */
class ProductModel extends Model
{
    /*
     * @var integer Product SKU (Stock Keeping Unit)
     */
    private $_strSKU;
    /*
     * @var string Product name
     */
    private $_strName;
    /*
     * @var string Product category
     */
    private $_strCategory;
    /*
     * @var float Product cost
     */
    private $_floCost;
    /*
     *  @var int Product quantity on hand
     */
    private $_intQty;

    /**
     * Calls parent constructor
     * ProductModel constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param string $sku Product SKU
     * @return $this ProductModel
     */
    public function setSku(string $sku)
    {
        $this->_strSKU = mysqli_real_escape_string($this->db, $sku);
        return $this;
    }

    /**
     * @param string $name Product Category
     * @return $this ProductModel
     */
    public function setName(string $name)
    {
        $this->_strName = mysqli_real_escape_string($this->db, $name);
        return $this;
    }

    /**
     * @param string $category Product Category
     * @return $this ProductModel
     */
    public function setCategory(string $category)
    {
        $this->_strCategory = mysqli_real_escape_string($this->db, $category);
        return $this;
    }

    /**
     * @param float $cost Product cost
     * @return $this ProductModel
     */
    public function setCost(float $cost)
    {
        $this->_floCost = mysqli_real_escape_string($this->db, $cost);
        return $this;
    }

    /**
     * @param int $qty Product Quantity
     * @return $this ProductModel
     */
    public function setQty(int $qty)
    {
        $this->_intQty = mysqli_real_escape_string($this->db, $qty);
        return $this;
    }

    /**
     * Saves current Product information to the database
     * if product already exist, updates the current product
     *
     * @return $this ProductModel
     */
    public function save()
    {
        $sku = $this->_strSKU??"NULL";
        $name = $this->_strName??"NULL";
        $category = $this->_strCategory??"NULL";
        $cost = $this->_floCost??"NULL";
        $qty = $this->_intQty??"NULL";

        // New Product - Perform INSERT
        $stmt = $this->db->prepare("INSERT INTO `".TABLE_PRODUCT."` VALUES (?, ?, ?, ?, ?);");
        $stmt->bind_param('sssdi', $sku, $name, $category, $cost, $qty);

        $stmt->execute();
        $stmt->close();
//        if (!$result = $this->db->query("INSERT INTO `".TABLE_PRODUCT."` VALUES ('$sku', '$name', '$category', '$cost', '$qty');")) {
//            throw new ToolshedException("Insert ".TABLE_PRODUCT." failed: (". $this->db->errno . ") " . $this->db->error);
//        }

        return $this;
    }
}