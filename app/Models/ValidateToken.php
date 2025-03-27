<?php
namespace App\Models;

use CodeIgniter\Model;
use Exception;

class ValidateToken extends Model
{

    protected $allowedFields = ['token'];
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    function checkAPIkey($token)
    {
        $sql="SELECT * FROM member WHERE sha1(CONCAT(email,passwd))=?";
        $data=$this->db->query($sql,array($token))->getRow();
        if (!$data) {
            throw new Exception("Account Not found");
        }
        return $data;
    }
}