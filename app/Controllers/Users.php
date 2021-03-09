<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;

/**
 * Class Users
 *
 * Users is used for API actions related to users.
 */

class Users extends BaseController
{
  /**
   * Method used to add a user.
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
    $usersModel = model('App\Models\UsersModel');
    
    try {
      // Add the user
      $data = $usersModel->addUser($post, $this->codes);
  
      // Return success
      return $this->respond($data['body'], $data['code']);
      
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
}