<?php namespace App\Models;

/**
 * Class ProductsModel
 *
 * ProductsModel is used to make data operations with products.
 */

class ProductsModel extends BaseModel
{
  protected $table = 'Products';
  protected $primaryKey = 'Id';
  protected $returnType = 'array';
  protected $allowedFields = ['Id', 'CodeIdentifier', 'Name', 'Price'];
}