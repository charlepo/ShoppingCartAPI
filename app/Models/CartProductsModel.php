<?php namespace App\Models;

/**
 * Class CartsModel
 *
 * CartsModel is used to make data operations with carts.
 */

class CartProductsModel extends BaseModel
{
  protected $table = 'CartProducts';
  protected $primaryKey = 'Id';
  protected $returnType = 'array';
  protected $allowedFields = ['Id', 'UserId', 'SellerProductId', 'Quantity', 'Price'];
  
  /**
   * Method used to add a product to the cart.
   *
   * @param $post
   * @param $codes
   * @return array
   * @throws \ReflectionException
   */
  public function addProduct($post, $codes): array
  {
    // Load models
    $usersModel = model('App\Models\UsersModel');
    
    // Start transaction
    $this->startTransaction();
    
    // Get the product price
    $db = \Config\Database::connect();
    $builder = $db->table('SellerProducts');
    $builder->select('Products.Price');
    $builder->join('Products', 'Products.Id = SellerProducts.ProductId');
    $builder->where('SellerProducts.Id', $post['SellerProductId']);
    $query = $builder->get();
    $product = $query->getResultArray();
    
    // Check if the seller/product was found
    if (count($product) < 1) {
      // Return error
      return $this->dbError(
        'The provided SellerProductId was not found.',
        $codes['resource_not_found']
      );
    }
    
    // Check the user id exists
    $user = $usersModel->find($post['UserId']);
    
    // Check if there's any user
    if (!$user) {
      // Return error
      return $this->dbError(
        'UserId not found.',
        $codes['resource_not_found']
      );
    }
    
    // Find cart based on user id and seller product id
    $cartProducts = $this
      ->where([
        'UserId' => $post['UserId'],
        'SellerProductId' => $post['SellerProductId'],
      ])
      ->find();
    
    // Check if there's any existing cart
    if ($cartProducts) {
      // Return error
      return $this->dbError(
        'The provided product already exists in the cart.',
        $codes['conflict']
      );
    }
    
    // Insert data
    $this->insert(array_merge($post, ['Price' => $product[0]['Price']]));
    
    // Get db errors
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
      array_merge($post, [
        'Id' => $this->getInsertID(),
        'Price' => $product[0]['Price']
      ]),
      $codes['created']
    );
  }
  
  /**
   * Method used to commit all the cart products.
   *
   * @param $post
   * @param $codes
   * @return array
   */
  public function commit($post, $codes): array
  {
    // Load models
    $purchasesModel = model('App\Models\PurchasesModel');
    
    // Start transaction
    $this->startTransaction();
    
    // Get all the products of a users' cart
    $cartProducts = $this
      ->where([
        'UserId' => $post['UserId'],
      ])
      ->find();
    
    // Check if there are any cart/products related to this user
    if (!$cartProducts) {
      // Return error
      return $this->dbError(
        'The provided UserId has no cart associated.',
        $codes['conflict']
      );
    }
    
    // Iterate over every product of the cart
    for ($x = 0; $x < count($cartProducts); $x++) {
      // Remove & add some elements
      unset($cartProducts[$x]['Id']);
      $cartProducts[$x]['Date'] =  date("Y/m/d");
      
      // Commit the cart products as purchases
      $purchasesModel->insert($cartProducts[$x]);
      
      // Get db errors
      $dbErrors = $purchasesModel->db->error();
      
      // Check for db errors
      if ($dbErrors['code'] != 0) {
        // Return error
        return $this->dbError();
      }
      
      // Add the last inserted id
      $cartProducts[$x]['Id'] = $purchasesModel->getInsertID();
    }
    
    // Delete products from cart
    $this
      ->where([
        'UserId' => $post['UserId'],
      ])
      ->delete();
    
    // Get db errors
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
      $cartProducts,
      $codes['created']
    );
  }
  
  /**
   * Method used to get the total amount of the cart.
   *
   * @param $userId
   * @return array
   */
  public function amount($userId, $codes): array
  {
    // Get the cart products
    $cartProducts = $this
      ->where([
        'UserId' => $userId,
      ])
      ->find();
    
    // Check if there are any cart/products related to this user
    if (!$cartProducts) {
      // Return error
      return $this->dbError(
        'The provided UserId has no cart associated.',
        $codes['resource_not_found']
      );
    }
    
    // Set vars
    $amount = 0;
    
    // Iterate over every cart product
    for ($x = 0; $x < count($cartProducts); $x++) {
      // Add every price to the total
      $amount += $cartProducts[$x]['Price'];
    }
    
    // Return success
    return $this->success(
      ['Amount' => $amount],
      $codes['created']
    );
  }
  
  /**
   * Method used to increase the amount of cart products.
   *
   * @param $post
   * @param $codes
   * @return array
   */
  public function increase($post, $codes): array
  {
    // Start transaction
    $this->startTransaction();
    
    // Find cart product
    $cartProduct = $this->find($post['CartProductId']);
    
    // Check if there's any cart/product
    if (!$cartProduct) {
      // Return error
      return $this->dbError(
        'CartProductId not found.',
        $codes['resource_not_found']
      );
    }
    
    // Increase
    $cartProduct['Quantity'] += $post['Quantity'];
    
    // Update the quantity
    $this->update($post['CartProductId'], [
      'Quantity' => $cartProduct['Quantity']
    ]);
    
    // Get db errors
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
      $cartProduct,
      $codes['updated']
    );
  }
  
  /**
   * Method used to decrease the amount of cart products.
   *
   * @param $post
   * @param $codes
   * @return array
   */
  public function decrease($post, $codes): array
  {
    // Start transaction
    $this->startTransaction();
    
    // Find cart product
    $cartProduct = $this->find($post['CartProductId']);
    
    // Check if there's any cart/product
    if (!$cartProduct) {
      // Return error
      return $this->dbError(
        'CartProductId not found.',
        $codes['resource_not_found']
      );
    }
    
    // Check if the difference between the quantity to decrease and the current quantity is less than zero
    if ($cartProduct['Quantity'] - $post['Quantity'] < 0) {
      // Return error
      return $this->dbError(
        'The amount of products is less than the quantity to decrease.',
        $codes['invalid_data']
      );
    }
    
    // Set vars
    $deleteProduct = $cartProduct['Quantity'] - $post['Quantity'] == 0;
    
    // Check if the difference in quantity is equal to 0
    if ($deleteProduct) {
      // Delete element
      $this->delete($post['CartProductId']);
    } else {
      // Decrease
      $cartProduct['Quantity'] -= $post['Quantity'];
      
      // Update the quantity
      $this->update($post['CartProductId'], [
        'Quantity' => $cartProduct['Quantity']
      ]);
    }
    
    // Get db errors
    $dbErrors = $this->db->error();
    
    // Check for db errors
    if ($dbErrors['code'] != 0) {
      // Return error
      return $this->dbError();
    }
    
    // End transaction
    $this->endTransaction();
    
    // Return success
    return $this->respond(
      $deleteProduct ? 'The product has been removed from the cart successfully.' : $cartProduct,
      $codes['updated']
    );
  }
}