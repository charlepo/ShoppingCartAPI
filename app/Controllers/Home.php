<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;

/**
 * Class Home
 *
 * Home is used for basic API actions.
 */

class Home extends BaseController
{
  /**
   * Default method.
   */
  public function index()
  {
    return null;
  }

  /**
   * Method used to welcome user to the API.
   *
   * @return Response
   */
  public function welcome(): Response
  {
    // Send welcome message
    return $this->respond([
      'status' => 'success',
      'data' => 'Welcome to Shopping Cart API '.getenv('app.apiVersion').'.'
    ]);
  }
  
  /**
   * Method used to create the default tables for the database.
   *
   * @return Response
   */
  public function initialize(): Response
  {
    // Load model
    $databaseTasksModel = model('App\Models\DatabaseTasksModel');
  
    try {
      // Create initial resources for the default database
      $data = $databaseTasksModel->createInitialResources(getenv('database.default.database'), $this->codes);
      
      // Return success
      return $this->respond($data['body'], $data['code']);
      
    } catch (\Exception $e) {
      // Return error
      return $this->dbError();
    }
  }
}