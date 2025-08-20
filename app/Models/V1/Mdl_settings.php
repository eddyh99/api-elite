<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database Setting
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_settings extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key', 'value'];

    protected $returnType = 'object';
    protected $useTimestamps = false;


    public function get($key)
    {
        try {
            $data =  $this->where($this->db->escapeIdentifiers('key'), $key)->first()->value ?? null;

            return (object) [
                'code'    => 200,
                'message' => $data
            ];
        } catch (\Throwable $th) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while getting data'
            ];
        }
    }

    public function store($data)
    {
        try {
            $builder = $this->db->table("settings");
            $key = key($data);
            $value = $data[$key];

            $exists = $builder->where('key', $key)->get()->getRow();

            if ($exists) {
                $builder->where('key', $key)->update(['value' => $value]);
            } else {
                $builder->insert(['key' => $key, 'value' => $value]);
            }

            return (object) [
                'success'  => true,
                'message' => ''
            ];
        } catch (\Exception $e) {
            return (object) [
                'success'  => false,
                'code'    => $e->getCode(),
                'message' => 'An error occurred.' . $e
            ];
        }
    }

    public function createBankAccount($data)
    {
        try {
            $this->db->transStart();

            $batchData = [];
            foreach ($data as $key => $value) {
                $batchData[] = [
                    'key'   => $key,
                    'value' => trim($value)
                ];
            }

            if (!empty($batchData)) {
                $this->insertBatch($batchData);

                // Cek error segera setelah query
                $dbError = $this->db->error();
                if (!empty($dbError['code'])) {
                    $this->db->transRollback();
                    return (object) [
                        'success' => false,
                        'code'    => $dbError['code'],
                        'message' => $dbError['message']
                    ];
                }
            }

            $this->db->transComplete();

            return (object) [
                'success' => true,
                'code'    => 201,
                'message' => 'Bank account created successfully'
            ];
        } catch (\Throwable $e) {
            return (object) [
                'success' => false,
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getUsBankAccount()
    {
        $sql = "
        SELECT `key`, `value`
        FROM settings
        WHERE `key` IN (
            'us_bank_account_name',
            'us_bank_account_type',
            'us_bank_routing_number',
            'us_bank_account_number',
            'us_bank_fee_setting'
        )
        ";

        $query = $this->db->query($sql);
        $rows = $query->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }

        return (object) $result;
    }

    public function getInternationalBankAccount()
    {
        $sql = "
        SELECT `key`, `value`
        FROM settings
        WHERE `key` IN (
            'inter_bank_account_name',
            'inter_bank_account_number',
            'inter_swift_code',
            'inter_fee_setting'
        )
        ";

        $query = $this->db->query($sql);
        $rows = $query->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }

        return (object) $result;
    }

    public function updateBankAccount($data)
    {
        try {
            if (empty($data)) {
                throw new \Exception('No data provided for update');
            }

            $updateData = [];
            foreach ($data as $key => $value) {
                $updateData[] = [
                    'key'   => $key,
                    'value' => trim($value)
                ];
            }

            $this->db->transStart();

            $this->db->table($this->table)->updateBatch($updateData, 'key');

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception(
                    'Failed to update bank account: ' . $this->db->error()['message']
                );
            }

            return (object) [
                'success' => true,
                'code'    => 200,
                'message' => 'Bank account updated successfully'
            ];
        } catch (\Exception $e) {
            return (object) [
                'success' => false,
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getUsBankFee()
    {
        $sql = "
        SELECT `key`, `value`
        FROM settings
        WHERE `key` IN (
            'us_bank_fee'
        )
     ";

        $query = $this->db->query($sql);
        $rows = $query->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }

        return (object) $result;
    }

    public function getInternationalBankFee()
    {
        $sql = "
        SELECT `key`, `value`
        FROM settings
        WHERE `key` IN (
            'international_bank_fee'
        )
     ";

        $query = $this->db->query($sql);
        $rows = $query->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }

        return (object) $result;
    }
}
