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

  public function getLastDepositPrivateKeyWallet($email, $walletType)
  {
    $sql = "
        SELECT w.private_key, last_deposit.payment_type, w.network
        FROM crypto_wallet w
        JOIN member m ON m.id = w.member_id
        JOIN (
            SELECT md.member_id, md.payment_type
            FROM member_deposit md
            JOIN member m2 ON m2.id = md.member_id
            WHERE m2.email = ?
              AND md.status = 'complete'
              AND md.payment_type IN (
                  'usdt_bep20','usdt_trc20','usdt_erc20','usdt_polygon',
                  'usdc_bep20','usdc_trc20','usdc_erc20','usdc_polygon',
                  'usdc_base','usdc_solana'
              )
            ORDER BY md.created_at DESC
            LIMIT 1
        ) last_deposit ON last_deposit.member_id = w.member_id
        WHERE w.type = ?
          AND (
              (last_deposit.payment_type LIKE '%_bep20' AND w.network = 'bep20') OR
              (last_deposit.payment_type LIKE '%_trc20' AND w.network = 'trc20') OR
              (last_deposit.payment_type LIKE '%_erc20' AND w.network = 'erc20') OR
              (last_deposit.payment_type LIKE '%_polygon' AND w.network = 'polygon') OR
              (last_deposit.payment_type LIKE '%_base' AND w.network = 'base') OR
              (last_deposit.payment_type LIKE '%_solana' AND w.network = 'solana')
          )
        LIMIT 1
    ";

    return $this->db->query($sql, [$email, $walletType])->getRow();
  }
}
