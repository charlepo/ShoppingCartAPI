<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
  /**
   * Trait to handle the API responses.
   */
  use ResponseTrait;
  
  /**
   * An array of helpers to be loaded automatically upon
   * class instantiation. These helpers will be available
   * to all other controllers that extend BaseController.
   *
   * @var array
   */
  protected $helpers = [];
  
  /**
   * Constructor.
   *
   * @param RequestInterface  $request
   * @param ResponseInterface $response
   * @param LoggerInterface   $logger
   */
  public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
  {
    // Do Not Edit This Line
    parent::initController($request, $response, $logger);
    
    //--------------------------------------------------------------------
    // Preload any models, libraries, etc, here.
    //--------------------------------------------------------------------
    // E.g.: $this->session = \Config\Services::session();
  }
  
  /**
   * Method used to validate the JSON input.
   *
   * @param array $post
   * @return array
   */
  public function dataValidations($post = []): array
  {
    // Check if provided data is of format JSON
    if (!$post) {
      // Send error
      return [
        'hasError' => true,
        'error' => $this->fail([
          'status' => 'error',
          'data' => 'The body of the request must be a valid JSON format.',
        ])
      ];
    }
    
    // No errors
    return [
      'hasError' => false,
    ];
  }
  
  /**
   * Method used to output a database error.
   *
   * @param string $message
   * @param int $code
   * @return Response
   */
  public function dbError($message = 'A database error occurred.', $code = 500): Response
  {
    // Return error
    return $this->respond([
      'status' => 'error',
      'data' => $message
    ], $code);
  }
}