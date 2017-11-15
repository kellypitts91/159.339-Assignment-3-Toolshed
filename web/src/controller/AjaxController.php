<?php

namespace internet\a3\controller;

use internet\a3\model\AjaxModel;
/*
 * Ajax Controller  This controller handles all the ajax queries
 * Search - Search for product matching the input search field
 * Browse - Display products by category depending on the checkboxes ticked
 * Username - Searches if the username entered already exist
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 */

class AjaxController extends Controller
{
    /**
     * Ajax function that checks the input field for any matches in the database
     */
    public function getProductsBySearch()
    {
        $collection = new AjaxModel();
        $productList = $collection->getProducts($_POST['name']);
        foreach ($productList as $item) {
            //colon delimiter for each cell, bar delimiter for each row
            echo $item[0].":".$item[1].":".$item[2].":".$item[3].":".$item[4]."|";
        }
    }

    /**
     * Ajax function that checks the database for all products under category and if in stock or not
     */
    public function getProductsByBrowse()
    {
        $collection = new AjaxModel();
        $products = $this->checkCategoryType();
        if($_POST['stock'] == 'true') {
            $allProducts = $collection->getProductsInStock($products);
        } else {
            $allProducts = $collection->getProductsByCategory($products);
        }

        foreach ($allProducts as $item) {
            //colon delimiter for each cell, bar delimiter for each row
            echo $item[0].":".$item[1].":".$item[2].":".$item[3]."|";
        }
    }

    /**
     * Checks what checkboxes are checked
     * @return array of category types to use for query
     */
    public function checkCategoryType(): array
    {
        $products = array();
        if ($_POST['hammer'] == 'true') {
            $products[] = "Hammers";
        }
        if ($_POST['heat'] == 'true') {
            $products[] = "Heat Guns";
        }
        if ($_POST['pliers'] == 'true') {
            $products[] = "Pliers";
        }
        if ($_POST['screw'] == 'true') {
            $products[] = "Screwdrivers";
        }
        if($_POST['span'] == 'true') {
            $products[] = "Spanners and Wrenches";
        }
        return $products;
    }

    /**
     * Ajax function that checks the username entered exist in database or not
     */
    public function checkUserNameExist()
    {
        $collection = new AjaxModel();

        if ($collection->CheckUserName($_POST['username'])) {
            echo "true";
        } else {
            echo "false";
        }
    }
}