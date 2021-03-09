<?php namespace App\Models;

/**
 * Class PurchasesModel
 *
 * PurchasesModel is used to make data operations with purchases.
 */

class PurchasesModel extends BaseModel
{
  protected $table = 'Purchases';
  protected $primaryKey = 'Id';
  protected $returnType = 'array';
  protected $allowedFields = ['Id', 'UserId', 'SellerProductId', 'Quantity', 'Price', 'Date'];
}