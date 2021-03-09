<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;

/**
 * Class Sellers
 *
 * Sellers is used for API actions related to sellers.
 */

class Sellers extends BaseController
{
  /**
   * Method used to add a seller.
   *
   * @return Response
   */
  public function add(): Response
  {
    // Set vars
    $post = $this->request->getJSON(true);
  
    // Make common input validations
    $validationsResult = $this->dataValidations($post);
  
    // Check if the validations outputted any errors
    if ($validationsResult['hasError']) {
      return $validationsResult['error'];
    }
    
    // Load model
    $sellersModel = model('App\Models\SellersModel');
  
    try {
      // Add the user
      $data = $sellersModel->addSeller($post, $this->codes);
    
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to delete a seller.
   *
   * @param $id
   * @return Response
   */
  public function delete($id): Response
  {
    // Load model
    $baseModel = model('App\Models\BaseModel');
  
    try {
      // Delete seller
      $data = $baseModel->deleteElements(
        'App\Models\SellersModel',
        ['Id' => $id],
        $this->codes,
        'The seller was deleted successfully.',
        'No seller was deleted.'
      );
    
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to add a product to a seller.
   *
   * @return Response
   */
  public function addProduct(): Response
  {
    // Set vars
    $post = $this->request->getJSON(true);
  
    // Make common input validations
    $validationsResult = $this->dataValidations($post);
  
    // Check if the validations outputted any errors
    if ($validationsResult['hasError']) {
      return $validationsResult['error'];
    }
    
    // Load models
    $sellersModel = model('App\Models\SellersModel');
  
    try {
      // Add the user
      $data = $sellersModel->addProduct($post, $this->codes);
    
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to delete a product of a seller.
   *
   * @param $id
   * @return Response
   */
  public function deleteProduct($id): Response
  {
    // Load model
    $baseModel = model('App\Models\BaseModel');
  
    try {
      // Delete seller
      $data = $baseModel->deleteElements(
        'App\Models\SellerProductsModel',
        ['Id' => $id],
        $this->codes,
        'The product attached to the seller was deleted successfully.',
        'No product was deleted for the provided seller.'
      );
    
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
}