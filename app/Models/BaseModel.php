<?php namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

/**
 * Class BaseModel
 *
 * BaseModel is used as a base class for all models.
 */

class BaseModel extends Model
{
  /**
   * Constructor.
   *
   * @param ConnectionInterface|null $db
   * @param ValidationInterface|null $validation
   */
  public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
  {
      parent::__construct($db, $validation);
  }
  
  /**
   * Method used to start a transaction.
   */
  public function startTransaction()
  {
    // Start transaction
    $this->db->transStart();
  }
  
  /**
   * Method used to commit a transaction.
   */
  public function endTransaction()
  {
    // End transaction
    $this->db->transComplete();
  }
  
  /**
   * Method used to respond success.
   * @param $data
   * @param $code
   * @return array
   */
  public function success($data, $code): array
  {
    return [
      'body' => [
        'status' => 'success',
        'data' => $data
      ],
      'code' => $code
    ];
  }
  
  /**
   * Method used to respond with a generic database error.
   * @param string $message
   * @param int $code
   * @return array
   */
  public function dbError($message = 'A database error occurred.', $code = 500): array
  {
    // Return error
    return [
      'body' => [
        'status' => 'error',
        'data' => $message
      ],
      'code' => $code
    ];
  }
  
  /**
   * Method used to delete an element.
   *
   * @param $namespace
   * @param $conditions
   * @param $codes
   * @param $deletedMessage
   * @param $notDeletedMessage
   * @return array
   */
  public function deleteElements($namespace, $conditions, $codes, $deletedMessage, $notDeletedMessage): array
  {
    // Load model
    $model = model($namespace);
    
    // Delete element(s)
    $model
      ->where($conditions)
      ->delete();
    
    // Set vars
    $dbErrors = $this->db->error();
    
    // Check for db errors
    if ($dbErrors['code'] != 0) {
      // Return error
      return $this->dbError();
    }
    
    // Set vars
    $data = $this->db->affectedRows() > 0 ? $deletedMessage : $notDeletedMessage;
    $code = $this->db->affectedRows() > 0 ? $codes['deleted'] : $codes['resource_not_found'];
    
    // Return success
    return $this->success(
      $data,
      $code
    );
  }
}