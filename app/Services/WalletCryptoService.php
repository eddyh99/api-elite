<?php

namespace App\Services;

use Elliptic\EC;
use kornrunner\Keccak;

class WalletCryptoService
{
    /**
     * Generate semua wallet untuk user baru
     */
    public function generateAllWallets()
    {
        // 1. Wallet EVM (digunakan untuk ERC20, BEP20, Polygon)
        $evmWalletErc = $this->generateWalletEvm();
        $evmWalletBep = $this->generateWalletEvm();
        $evmWalletPolygon = $this->generateWalletEvm();
        $evmWalletBase = $this->generateWalletEvm();

        // 2. Wallet TRC20 (Tron)
        $tronWallet = $this->generateWalletTrc20();

        // 3. Solana
        $solanaWallet = $this->generateWalletSolana();

        // Return semua wallet
        return [
            'erc20'   => $evmWalletErc,
            'bep20'   => $evmWalletBep,
            'polygon' => $evmWalletPolygon,
            'base'    => $evmWalletBase,
            'trc20'   => $tronWallet,
            'solana'  => $solanaWallet,
        ];
    }

    /**
     * Wallet EVM (Ethereum, Polygon, BSC)
     */
    private function generateWalletEvm()
    {
        $ec = new EC('secp256k1');
        $key = $ec->genKeyPair();

        $privateKey = $key->getPrivate()->toString(16);
        $publicKey  = $key->getPublic(false, 'hex');
        $publicKeyBin = hex2bin(substr($publicKey, 2));
        $hash = Keccak::hash($publicKeyBin, 256);
        $address = '0x' . substr($hash, -40);

        return [
            'privateKey' => "0x{$privateKey}",
            'publicKey'  => $publicKey,
            'address'    => $address
        ];
    }

    /**
     * Wallet TRC20 (Tron)
     */
    private function generateWalletTrc20()
    {
        $ec = new EC('secp256k1');
        $key = $ec->genKeyPair();

        $privateKey = $key->getPrivate()->toString(16);
        $publicKey  = $key->getPublic(false, 'hex');

        $pubBin = hex2bin(substr($publicKey, 2));
        $hash = Keccak::hash($pubBin, 256);
        $addressHex = '41' . substr($hash, -40);
        $addrBin = hex2bin($addressHex);
        $checksum = substr(hash('sha256', hash('sha256', $addrBin, true), true), 0, 4);

        $tronAddress = $this->base58EncodeTron($addressHex . bin2hex($checksum));

        return [
            'privateKey' => $privateKey,
            'publicKey'  => $publicKey,
            'address'    => $tronAddress
        ];
    }

    /**
     * Encode Base58 khusus Tron
     */
    private function base58EncodeTron($hex)
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $num = gmp_init($hex, 16);
        $encoded = '';
        while (gmp_cmp($num, 0) > 0) {
            list($num, $rem) = gmp_div_qr($num, 58);
            $encoded = $alphabet[gmp_intval($rem)] . $encoded;
        }
        foreach (str_split(hex2bin($hex)) as $char) {
            if ($char === "\0") $encoded = '1' . $encoded;
            else break;
        }
        return $encoded;
    }

    /**
     * Wallet Solana (Ed25519)
     */
    private function generateWalletSolana()
    {
        $keypair = sodium_crypto_sign_keypair();

        $privateKey = sodium_bin2hex(sodium_crypto_sign_secretkey($keypair));
        $publicKeyBin = sodium_crypto_sign_publickey($keypair);
        $publicKeyHex = sodium_bin2hex($publicKeyBin);
        $address      = $this->base58EncodeSolana($publicKeyBin);

        return [
            'privateKey' => $privateKey,
            'publicKey'  => $publicKeyHex,
            'address'    => $address  
        ];
    }


    private function base58EncodeSolana($data)
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $num = gmp_init(bin2hex($data), 16);
        $encoded = '';
        while (gmp_cmp($num, 0) > 0) {
            list($num, $rem) = gmp_div_qr($num, 58);
            $encoded = $alphabet[gmp_intval($rem)] . $encoded;
        }
        return $encoded;
    }
}
