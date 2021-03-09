<?php namespace App\Models;

/**
 * Class DatabaseTasksModel
 *
 * DatabaseTasksModel is used to make operations with databases using ORM (Object Relational Mapping)
 */

class DatabaseTasksModel extends BaseModel
{
  /**
   * Constructor.
   *
   * @param $database
   */
  public function createInitialResources($database, $codes): array
  {
    // Load forge
    $forge = \Config\Database::forge();
  
    // Create database
    $forge->createDatabase($database, true);
  
    // Products table
    $forge->addField([
      'Id' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'CodeIdentifier' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'unique' => true,
      ],
      'Name' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
      ],
      'Price' => [
        'type' => 'DECIMAL',
        'constraint' => '6,2',
        'unsigned' => true,
      ],
    ]);
    $forge->addPrimaryKey('Id');
    $forge->createTable('Products', true);
  
    // Sellers table
    $forge->addField([
      'Id' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'Name' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
      ],
      'Email' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'unique' => true,
      ],
    ]);
    $forge->addPrimaryKey('Id');
    $forge->createTable('Sellers', true);
  
    // SellerProducts table
    $forge->addField([
      'Id' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'SellerId' => [
        'type' => 'INT',
        'constraint' => 11,
      ],
      'ProductId' => [
        'type' => 'INT',
        'constraint' => 11,
      ],
    ]);
    $forge->addPrimaryKey('Id');
    $forge->addForeignKey('SellerId','Sellers','Id');
    $forge->addForeignKey('ProductId','Products','Id');
    $forge->createTable('SellerProducts', true);
    $this->db->query('ALTER TABLE SellerProducts ADD CONSTRAINT SellerId_ProductId_unique UNIQUE KEY(`SellerId`,`ProductId`);');
    
    // Users table
    $forge->addField([
      'Id' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'Name' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
      ],
      'Email' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'unique' => true,
      ],
    ]);
    $forge->addPrimaryKey('Id');
    $forge->createTable('Users', true);
  
    // CartProducts table
    $forge->addField([
      'Id' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'UserId' => [
        'type' => 'INT',
        'constraint' => 11,
      ],
      'SellerProductId' => [
        'type' => 'INT',
        'constraint' => 11,
      ],
      'Quantity' => [
        'type' => 'SMALLINT',
        'constraint' => 6,
      ],
      'Price' => [
        'type' => 'DECIMAL',
        'constraint' => '6,2',
        'unsigned' => true,
      ],
    ]);
    $forge->addPrimaryKey('Id');
    $forge->addForeignKey('UserId','Users','Id');
    $forge->addForeignKey('SellerProductId','SellerProducts','Id');
    $forge->createTable('CartProducts', true);
    $this->db->query('ALTER TABLE CartProducts ADD CONSTRAINT UserId_SellerProductId_unique UNIQUE KEY(`UserId`,`SellerProductId`);');
  
    // Purchases table
    $forge->addField([
      'Id' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'UserId' => [
        'type' => 'INT',
        'constraint' => 11,
      ],
      'SellerProductId' => [
        'type' => 'INT',
        'constraint' => 11,
      ],
      'Quantity' => [
        'type' => 'SMALLINT',
        'constraint' => 6,
      ],
      'Price' => [
        'type' => 'DECIMAL',
        'constraint' => '6,2',
        'unsigned' => true,
      ],
      'Date' => [
        'type' => 'DATE',
      ],
    ]);
    $forge->addPrimaryKey('Id');
    $forge->addForeignKey('UserId','Users','Id');
    $forge->addForeignKey('SellerProductId','SellerProducts','Id');
    $forge->createTable('Purchases', true);
  
    // Set vars
    $dbErrors = $this->db->error();
  
    // Check for db errors
    if ($dbErrors['code'] != 0) {
      // Return error
      return $this->dbError();
    }
    
    // Return success
    return $this->success(
      'The database schema has been initialized correctly.',
      $codes['created']
    );
  }
}