<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;

/**
 * Class CartProducts
 *
 * CartProducts is used for API actions related to cart products.
 */

class CartProducts extends BaseController
{
  /**
   * Method used to add a product to the cart.
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
    
    // Load models
    $cartProductsModel = model('App\Models\CartProductsModel');
  
    try {
      // Add the user
      $data = $cartProductsModel->addProduct($post, $this->codes);
    
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to delete a product of a cart.
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
        'App\Models\CartProductsModel',
        ['Id' => $id],
        $this->codes,
        'The product attached to the cart was deleted successfully.',
        'No product was deleted for the provided CartProductId.'
      );
    
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to delete all products of a cart.
   *
   * @param $id
   * @return Response
   */
  public function deleteAll($id): Response
  {
    // Load model
    $baseModel = model('App\Models\BaseModel');
    
    try {
      // Delete seller
      $data = $baseModel->deleteElements(
        'App\Models\CartProductsModel',
        ['UserId' => $id],
        $this->codes,
        'The product(s) attached to the cart were deleted successfully.',
        'No product was deleted for the provided UserId and SellerProductId.'
      );
      
      // Return success
      return $this->respond($data['body'], $data['code']);
      
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to commit all the cart products.
   *
   * @return Response
   */
  public function commit(): Response
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
    $cartProductsModel = model('App\Models\CartProductsModel');
  
    try {
      // Add the user
      $data = $cartProductsModel->commit($post, $this->codes);
    
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to get the total amount of the cart.
   *
   * @param $userId
   * @return Response
   */
  public function amount($userId): Response
  {
    // Load models
    $cartProductsModel = model('App\Models\CartProductsModel');
  
    try {
      // Add the user
      $data = $cartProductsModel->amount($userId, $this->codes);
    
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to increase the amount of cart products.
   *
   * @return Response
   */
  public function increase(): Response
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
    $cartProductsModel = model('App\Models\CartProductsModel');
  
    try {
      // Add the user
      $data = $cartProductsModel->increase($post, $this->codes);
      
      // Return success
      return $this->respond($data['body'], $data['code']);
    
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
  
  /**
   * Method used to decrease the amount of cart products.
   *
   * @return Response
   */
  public function decrease(): Response
  {
    // Check if provided data is of format JSON
    if (!$post = $this->request->getJSON(true)) {
      // Send error
      return $this->fail([
        'status' => 'error',
        'data' => 'The body of the request must be a valid JSON format.',
      ]);
    }
    
    // Data validations
    // ...
    
    // Load models
    $cartProductsModel = model('App\Models\CartProductsModel');
    
    // Start transaction
    $cartProductsModel->startTransaction();
    
    // Find cart product
    $cartProduct = $cartProductsModel->find($post['CartProductId']);
    
    // Check if there's any cart/product
    if (!$cartProduct) {
      // Return error
      return $this->respond([
        'status' => 'error',
        'data' => 'CartProductId not found.'
      ], 404);
    }
    
    // Check if the difference between the quantity to decrease and the current quantity is less than zero
    if ($cartProduct['Quantity'] - $post['Quantity'] < 0) {
      // Return error
      return $this->respond([
        'status' => 'error',
        'data' => 'The amount of products is less than the quantity to decrease.'
      ], 400);
    }
    
    // Set vars
    $deleteProduct = $cartProduct['Quantity'] - $post['Quantity'] == 0;
    
    // Check if the difference in quantity is equal to 0
    if ($deleteProduct) {
      // Delete element
      $cartProductsModel->delete($post['CartProductId']);
    } else {
      // Decrease
      $cartProduct['Quantity'] -= $post['Quantity'];
  
      // Update the quantity
      $cartProductsModel->update($post['CartProductId'], [
        'Quantity' => $cartProduct['Quantity']
      ]);
    }
    
    // Get db errors
    $dbErrors = $cartProductsModel->db->error();
    
    // Check for db errors
    if ($dbErrors['code'] != 0) {
      // Return error
      return $this->respond([
        'status' => 'error',
        'data' => 'A database error occurred.'
      ], 500);
    }
    
    // End transaction
    $cartProductsModel->endTransaction();
    
    // Return success
    return $this->respond([
      'status' => 'success',
      'data' => $deleteProduct ? 'The product has been removed from the cart successfully.' : $cartProduct
    ]);
  }
}