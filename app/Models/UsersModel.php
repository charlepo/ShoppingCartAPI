<?php namespace App\Models;

/**
 * Class UsersModel
 *
 * UsersModel is used to make data operations with users.
 */

class UsersModel extends BaseModel
{
  protected $table = 'Users';
  protected $primaryKey = 'Id';
  protected $returnType = 'array';
  protected $allowedFields = ['Id', 'Name', 'Email'];
  
  /**
   * Method used to add a user.
   *
   * @param $post
   * @param $codes
   * @return array
   * @throws \ReflectionException
   */
  public function addUser($post, $codes): array
  {
    // Start transaction
    $this->startTransaction();
  
    // Find user based on email field
    $user = $this
      ->where([
        'Email' => $post['Email'],
      ])
      ->find();
  
    // Check if there's any existing user
    if ($user) {
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
    
    // Return data
    return $this->success(
      array_merge($post, ['Id' => $this->getInsertID()]),
      $codes['created']
    );
  }
}