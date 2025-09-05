<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;
use Hashids\Hashids;


class Mdl_crypto_wallet extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'crypto_wallet';
    protected $primaryKey = 'id';

    protected $allowedFields = ['member_id', 'type', 'network', 'address', 'public_key', 'private_key', 'created_at', 'updated_at'];

    protected $returnType = 'array';
    protected $useTimestamps = true;

    public function getWalletByEmail($email, $type = 'hedgefund')
    {
        $sql = "
        SELECT w.*
        FROM crypto_wallet w
        JOIN member m ON m.id = w.member_id
        WHERE m.email = ? 
          AND w.type = ?
        ";

        $query = $this->db->query($sql, [$email, $type]);
        return $query->getResultArray();
    }

    public function getWalletInfo($email, $type, $network)
    {
        $sql = "
            SELECT w.address, w.type, w.network
            FROM crypto_wallet w
            JOIN member m ON m.id = w.member_id
            WHERE m.email = ? 
              AND w.type = ? 
              AND w.network = ? 
            LIMIT 1
            ";

        return $this->db->query($sql, [$email, $type, $network])->getRow();
    }
}
