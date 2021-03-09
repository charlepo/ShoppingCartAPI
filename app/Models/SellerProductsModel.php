<?php namespace App\Models;

/**
 * Class SellerProductsModel
 *
 * SellerProductsModel is used to make data operations with seller products.
 */

class SellerProductsModel extends BaseModel
{
  protected $table = 'SellerProducts';
  protected $primaryKey = 'Id';
  protected $returnType = 'array';
  protected $allowedFields = ['Id', 'SellerId', 'ProductId'];
}