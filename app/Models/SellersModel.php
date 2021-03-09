<?php namespace App\Models;

/**
 * Class SellersModel
 *
 * SellersModel is used to make data operations with sellers.
 */

class SellersModel extends BaseModel
{
  protected $table = 'Sellers';
  protected $primaryKey = 'Id';
  protected $returnType = 'array';
  protected $allowedFields = ['Id', 'Name', 'Email'];
  
  /**
   * Method used to add a seller.
   *
   * @param $post
   * @param $codes
   * @return array
   * @throws \ReflectionException
   */
  public function addSeller($post, $codes): array
  {
    // Start transaction
    $this->startTransaction();
  
    // Find seller based on email field
    $seller = $this
      ->where([
        'Email' => $post['Email'],
      ])
      ->find();
  
    // Check if there's any existing seller
    if ($seller) {
      // Return error
      return $this->dbError(
        'The provided email already exists in the database.',
        $codes['conflict']
      );
    }
  
    // Insert data
    $this->insert($post);
  
    // Set vars
    $dbErrors = $this->db->error();
  
    // Check for db errors
    if ($dbErrors['code'] != 0) {
      // Return error
      return $this->dbError();
    }
  
    // End transaction
    $this->endTransaction();
  
    // Return success
    return $this->success(
      array_merge($post, ['Id' => $this->getInsertID()]),
      $codes['created']
    );
  }
  
  /**
   * Method used to add a product to a seller.
   *
   * @param $post
   * @param $codes
   * @return array
   */
  public function addProduct($post, $codes): array
  {
    // Load models
    $sellerProductsModel = model('App\Models\SellerProductsModel');
    $productsModel = model('App\Models\ProductsModel');
    
    // Start transaction
    $sellerProductsModel->startTransaction();
    
    // Find the product by the code identifier
    $product = $productsModel
      ->where('CodeIdentifier', $post['CodeIdentifier'])
      ->find();
    
    // Check if the product exists
    if (!$product) {
      // Set vars
      $productToSave = $post;
      
      // Remove field
      unset($productToSave['SellerId']);
      
      // Save product
      $productsModel->insert($post);
      
      // Set vars
      $dbErrors = $productsModel->db->error();
      
      // Check for db errors
      if ($dbErrors['code'] != 0) {
        // Return error
        return $this->dbError();
      }
      
      // Get last id
      $product = ['Id' => $productsModel->getInsertID()];
    } else {
      $product = $product[0];
    }
    
    // Find seller
    $seller = $this->find($post['SellerId']);
    
    // Check if there are any sellers
    if (!$seller) {
      // Return error
      return $this->dbError(
        'SellerId not found.',
        $codes['resource_not_found']
      );
    }
    
    // Set vars
    $sellerProductIds = [
      'SellerId' => $post['SellerId'],
      'ProductId' => $product['Id'],
    ];
    
    // Find seller/product
    $sellerProduct = $sellerProductsModel
      ->where($sellerProductIds)
      ->find();
    
    // Check if there's any existing seller/product
    if ($sellerProduct) {
      // Return error
      return $this->dbError(
        'The provided product already exists for the provided seller in the database.',
        $codes['conflict']
      );
    }
    
    // Save the SellerProduct
    $sellerProductsModel->insert($sellerProductIds);
    
    // Set vars
    $dbErrors = $sellerProductsModel->db->error();
    
    // Check for db errors
    if ($dbErrors['code'] != 0) {
      // Return error
      return $this->dbError();
    }
    
    // End transaction
    $sellerProductsModel->endTransaction();
    
    // Return success
    return $this->success(
      array_merge($sellerProductIds, ['Id' => $sellerProductsModel->getInsertID()]),
      $codes['created']
    );
  }
}