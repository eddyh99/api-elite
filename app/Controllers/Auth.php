<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_crypto_wallet;
use App\Services\WalletCryptoService;

class Auth extends BaseController
{
	use ResponseTrait;

	public function __construct()
	{
		$this->member  = model('App\Models\V1\Mdl_member');
		$this->walletCryptoModel = new Mdl_crypto_wallet();
		$this->walletService = new WalletCryptoService();
	}

	public function postRegister()
	{

		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'phone_number' => [
				'rules'  => 'required|numeric',
				'errors' => [
					'numeric'      => 'Phone number must be numeric'
				]
			],
			'password' => [
				'rules'  => 'required|min_length[8]',
				'errors' =>  [
					'required'      => 'Password is required',
					'min_length'    => 'Min length password is 8 character'
				]
			],
			'role' => [
				'rules'  => 'required|in_list[member,admin,manager,superadmin,referral]',
				'errors' => [
					'required' => 'User role is required.',
					'in_list'  => 'Invalid user role.'
				]
			],
			'timezone' => [
				'rules'  => 'required',
				'errors' =>  [
					'required'      => 'User timezone is required',
				]
			],
			'ip_address' => [
				'rules'  => 'required|valid_ip',
				'errors' => [
					'required'  => 'User IP is required',
					'valid_ip'  => 'User IP must be a valid IP address',
				]
			]

		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data           = $this->request->getJSON();

		$mdata = array(
			"email"     => trim($data->email),
			"passwd"    => trim($data->password),
			"role"		=> trim($data->role),
			"status"	=> @$data->status ? @$data->status : ($data->role != 'member' ? 'active' : 'new'),
			"timezone"  => $data->timezone,
			"refcode"	=> $data->referral ?? null,
			'ip_addr'	=> $data->ip_address,
			'phone_number' => $data->phone_number
		);

		if (!empty($data->referral)) {
			$refmember = $this->member->getby_refcode($data->referral);
			if (!$refmember->exist) {
				return $this->respond(error_msg(400, "auth", '01', $refmember->message), 400);
			}
			$mdata["id_referral"] = $data->referral != 'm4573r' ? $refmember->id : null;
			$mdata["refcode"] = null;
		}

		$mdata['otp'] = rand(1000, 9999);
		$result = $this->member->add($mdata);

		if (!@$result->success) {
			if ($result->code == 1060 || $result->code == 1062) {
				$result->message = 'User already registered';
			}
			return $this->respond(error_msg(400, "auth", '01', $result->message), 400);
		}

		$userId = $result->id;
		$wallets = $this->walletService->generateAllWallets();
		helper('crypto'); // load helper untuk encode/decode private key

		$walletData = [];

		foreach ($wallets as $network => $wallet) {
			$walletData[] = [
				'member_id'  => $userId,
				'type'       => 'hedgefund',               // bisa disesuaikan
				'network'    => $network,                 // erc20, bep20, polygon, trc20, base, solana
				'address'    => $wallet['address'],
				'public_key' => $wallet['publicKey'] ?? null,
				'private_key' => encode_private_key($wallet['privateKey']),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			];
		}

		try {
			$this->walletCryptoModel->insertBatch($walletData);
		} catch (\Exception $e) {
			return $this->respond([
				'status' => 'error',
				'message' => $e->getMessage() ?: 'Unknown DB error',
				'trace' => $e->getTraceAsString()
			], 500);
		}

		$message = [
			"text" => $result->message,
			"otp"	  => $mdata['otp']
		];

		return $this->respond(error_msg(201, "auth", null, $message), 201);
	}

	public function postCheck_wallet()
	{
		$data = $this->request->getJSON();
		$email = $data->email ?? null;
		$type = $data->type ?? null;

		$result = $this->walletCryptoModel->getWalletByEmail($email, $type);
		if (!$result) {
			return $this->respond(error_msg(404, "auth", "01", "Wallet not found"), 404);
		}
		return $this->respond(error_msg(200, "auth", null, $result), 200);
	}

	public function postCreate_wallet()
	{
		helper('crypto'); // load helper untuk encode/decode private key

		$data = $this->request->getJSON();
		$email = $data->email ?? null;
		$type = $data->type ?? null;

		$member = $this->member->getby_email($email);

		$userId = $member->message->id;

		$wallets = $this->walletService->generateAllWallets();
		$walletData = [];

		foreach ($wallets as $network => $wallet) {
			$walletData[] = [
				'member_id'  => $userId,
				'type'       => $type,               // bisa disesuaikan
				'network'    => $network,                 // erc20, bep20, polygon, trc20, base, solana
				'address'    => $wallet['address'],
				'public_key' => $wallet['publicKey'] ?? null,
				'private_key' => encode_private_key($wallet['privateKey']),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			];
		}

		try {
			$this->walletCryptoModel->insertBatch($walletData);
		} catch (\Exception $e) {
			return $this->respond([
				'status' => 'error',
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			], 500);
		}
		return $this->respond(error_msg(201, "auth", null, "Wallet created successfully"), 201);
	}

	public function postGet_crypto_wallet()
	{
		$data = $this->request->getJSON();
		$email = $data->email ?? null;
		$type = $data->type ?? null;
		$network = $data->network ?? null;

		$result = $this->walletCryptoModel->getWalletInfo($email, $type, $network);
		return $this->respond(error_msg(200, "auth", null, $result), 200);
	}

	public function postSignin()
	{
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'password' => [
				'rules'  => 'required',
				'errors' =>  [
					'required'      => 'Password is required',
					'min_length'    => 'Min length password is 8 character'
				]
			],
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data           = $this->request->getJSON();

		$member = $this->member->getby_email($data->email);
		if (@$member->code != 200) {
			return $this->respond(error_msg($member->code, "auth", "01", $member->message), $member->code);
		}

		if ($data->password == $member->message->passwd) {
			$response = $member->message;
			if($response->role == 'member') {
				unset($response->access);
			}

			return $this->respond(error_msg(200, "auth", "02", $response), 200);
		} else {
			$response = "Invalid username or password";
			return $this->respond(error_msg(400, "auth", "02", $response), 400);
		}
	}

	/*
	public function postAdmin_Signin()
	{
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'password' => [
				'rules'  => 'required|min_length[8]',
				'errors' =>  [
					'required'      => 'Password is required',
					'min_length'    => 'Min length password is 8 character'
				]
			],
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data           = $this->request->getJSON();

		$member = $this->member->getby_email($data->email);
		if (@$member->code != 200) {
			return $this->respond(error_msg($member->code, "auth", "01", $member->message), $member->code);
		}

		$allowedRoles = ['admin', 'manager', 'superadmin'];

		// Validasi role
		if (!in_array($member->message->role, $allowedRoles)) {
			return $this->respond(error_msg(403, "auth", "03", "Access denied. You are not authorized to sign in."), 403);
		}

		if ($data->password == $member->message->passwd) {
			$response = $member->message;

			return $this->respond(error_msg(200, "auth", "02", $response), 200);
		} else {
			$response = "Invalid username or password";
			return $this->respond(error_msg(400, "auth", "02", $response), 400);
		}
	}
	*/

	public function postResend_token()
	{
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			]
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$email = $this->request->getJSON()->email;
		$mdata = [
			'email' => filter_var($email, FILTER_VALIDATE_EMAIL),
			'otp'	=> rand(1000, 9999)
		];

		$result = $this->member->update_otp($mdata);
		if ($result->code !== 200) {
			return $this->respond(error_msg(400, "auth", '01', $result->message), 400);
		}

		$message = [
			'text' => $result->message,
			"otp"	  => $mdata['otp']
		];

		return $this->respond(error_msg($result->code, "auth", null, $message), $result->code);
	}

	public function postActivate_member()
	{
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'otp' => [
				'rules'  => 'required|numeric|exact_length[4]',
				'errors' => [
					'required'     => 'OTP is required',
					'numeric'      => 'OTP must be a number',
					'exact_length' => 'OTP must be exactly 4 digits'
				]
			]
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data           = $this->request->getJSON();
		$mdata = array(
			"email"     => trim($data->email),
			"otp"    => trim($data->otp),
		);

		$result = $this->member->activate($mdata);
		if ($result->code !== 200) {
			return $this->respond(error_msg($result->code, "auth", '01', $result->message), $result->code);
		}

		return $this->respond(error_msg(200, "auth", null, $result->message), 200);
	}

	public function postReset_password() {
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'password' => [
				'rules'  => 'required|min_length[8]',
				'errors' =>  [
					'required'      => 'Password is required',
					'min_length'    => 'Min length password is 8 character'
				]
			],
			'otp' => [
				'rules'  => 'required|numeric|exact_length[4]',
				'errors' => [
					'required'     => 'OTP is required',
					'numeric'      => 'OTP must be a number',
					'exact_length' => 'OTP must be exactly 4 digits'
				]
			]
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data       = $this->request->getJSON();

		$mdata = [
			'email' 	=> trim($data->email),
			'password'  => trim($data->password),
			'otp'		=> trim($data->otp)
		];

		$isgodmode = !empty($data->isgodmode) && $data->isgodmode == true;
		$result = $this->member->reset_password($mdata, $isgodmode);
		if ($result->code !== 200) {
			return $this->respond(error_msg($result->code, "auth", '01', $result->message), $result->code);
		}

		return $this->respond(error_msg(200, "auth", null, $result->message), 200);
	}

	public function postOtp_check() {
        $data = $this->request->getJSON();
        $mdata = [
            "email" => $data->email,
            "otp" => $data->otp
        ];

        $result = $this->member->otp_check($mdata);
        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->message), 200);
    }

    public function getAssets()
    {
        $url = BINANCEAPI . "/account";

        $response = binanceAPI($url, []);
        if (isset($response->code)) {
            return $this->respond(error_msg(400, "binance", $response->code, $response->msg), 400);
        }

		return $this->respond(error_msg(200, "binance", null, $response), 200);
	}

	public function postGet_user_number()
	{
		$data = $this->request->getJSON();
		$email = $data->email ?? null;

		$result = $this->member->get_number_by_email($email);
		if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "auth", "01", $result->message), $result->code);
		}

		$response = $result->message->phone_number;
		return $this->respond(error_msg(200, "auth", null, $response), 200);
	}

	/* Cek wallet balance BEP20 USDT di BSC */
	/*
	public function postCheck_balance()
	{
		$json   = $this->request->getJSON(true);
		$wallet = $json['wallet_address'] ?? null;

		if (!$wallet) {
			return $this->response->setJSON([
				'status'  => 'error',
				'message' => 'Wallet address is required'
			]);
		}

		$rpcUrl = 'https://bsc-dataseed.binance.org/';
		$web3   = new \Web3\Web3($rpcUrl);

		$usdtContract = '0x55d398326f99059fF775485246999027B3197955';

		$erc20Abi = '[{
        "constant":true,
        "inputs":[{"name":"_owner","type":"address"}],
        "name":"balanceOf",
        "outputs":[{"name":"balance","type":"uint256"}],
        "type":"function"}]';

		$contract = new \Web3\Contract($web3->provider, $erc20Abi);

		$resultData = null;
		$errorMsg   = null;

		$contract->at($usdtContract)->call('balanceOf', $wallet, function ($err, $result) use ($wallet, &$resultData, &$errorMsg) {
			if ($err !== null) {
				$errorMsg = $err->getMessage();
				return;
			}

			$balance = null;
			$rawResult = $result; // ini adalah array

			// Format 1: [0 => BigNumber]
			if (isset($result[0])) {
				$balance = $result[0]->toString();
			}
			// Format 2: ['balance' => BigNumber]
			elseif (isset($result['balance'])) {
				$balance = $result['balance']->toString();
			}

			if ($balance === null) {
				$errorMsg = 'Balance result empty or unrecognized format';
				return;
			}

			$humanReadable = bcdiv($balance, bcpow('10', '18'), 6);

			$resultData = [
				'status'         => 'success',
				'wallet_address' => $wallet,
				// 'balance'        => $humanReadable,
				'raw_result'    => $rawResult
			];
		});


		// setelah call, balikin hasil
		if ($errorMsg !== null) {
			return $this->respond([
				'status'  => 'error',
				'message' => $errorMsg
			]);
		}

		return $this->respond($resultData);
	}
	*/

	// Cek wallet balance BEP20 USDT/USDC di BSC dengan multiple RPC dan fallback
	public function postCheck_wallet_bep20()
	{
		// Ambil data JSON dari request
		$json   = $this->request->getJSON(true);
		$wallet = $json['wallet_address'] ?? null;
		$token  = strtolower($json['token'] ?? ''); // token pakai lowercase, contoh "usdt_bep20" dan "usdc_bep20", ini digunakan untuk pengecekan salah satu dari dua token

		if (!$wallet) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Wallet address is required'
			]);
		}

		// Validasi format wallet BSC (harus 0x + 40 karakter hex)
		if (substr($wallet, 0, 2) !== '0x' || strlen($wallet) !== 42 || !ctype_xdigit(substr($wallet, 2))) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Invalid BSC wallet address'
			]);
		}

		// Mapping token -> kontrak BEP20 di BSC
		$tokenContracts = [
			'usdt_bep20' => '0x55d398326f99059fF775485246999027B3197955',
			'usdc_bep20' => '0x8ac76a51cc950d9822d68b83fe1ad97b32cd580d'
		];

		if (!isset($tokenContracts[$token])) {
			return $this->respond([
				'status' => 'error',
				'message' => 'Token not supported on BSC'
			]);
		}

		// Daftar RPC BSC, kalau RPC pertama timeout/error, akan dicoba yang berikutnya
		$rpcUrls = [
			'https://bsc-dataseed1.ninicoin.io/',
			'https://bsc-dataseed1.defibit.io/',
			'https://bsc-dataseed.binance.org/'
		];

		// ABI sederhana ERC20 untuk fungsi balanceOf yang akan dikirim ke kontrak
		$erc20Abi = '[{
			"constant": true,
			"inputs": [{"name":"_owner","type":"address"}],
			"name": "balanceOf",
			"outputs": [{"name":"balance","type":"uint256"}],
			"type": "function"
			}]';

		$resultData = [
			'status' => 'error',
			'wallet_address' => $wallet,
			'token' => $token,
			'balance' => '0',
			// 'rawBalance' => '0'
		];

		$success = false; // menandai apakah call berhasil
		$lastError = '';  // menyimpan pesan error terakhir untuk debug

		// Looping setiap RPC untuk fallback jika RPC pertama gagal
		foreach ($rpcUrls as $rpcUrl) {
			try {
				// Buat provider untuk Web3
				$provider = new \Web3\Providers\HttpProvider(
					new \Web3\RequestManagers\HttpRequestManager($rpcUrl, 10) // timeout 10 detik
				);
				$web3 = new \Web3\Web3($provider);
				$contract = new \Web3\Contract($web3->provider, $erc20Abi);

				// Panggil fungsi balanceOf dari kontrak ERC20
				$contract->at($tokenContracts[$token])->call('balanceOf', $wallet, function ($err, $balance) use (&$resultData, &$success, &$lastError) {
					if ($err !== null) {
						$lastError = $err->getMessage(); // simpan error jika ada
						return;
					}

					$raw = null;
					// $convert = $balance; // simpan raw balance untuk debug
					// $resultData['rawBalance'] = $convert['balance']->toString(); // simpan raw balance untuk debug

					// Parsing hasil balance (BigNumber)
					if (is_array($balance)) {
						if (isset($balance[0]) && method_exists($balance[0], 'toString')) {
							$raw = $balance[0]->toString();
						} elseif (isset($balance['balance']) && method_exists($balance['balance'], 'toString')) {
							$raw = $balance['balance']->toString();
						}
					}

					if ($raw === null) {
						$raw = '0'; // jika balance tidak terbaca, default 0
					}

					// Konversi raw balance ke human-readable (BEP20 biasanya 18 decimals)
					$resultData['balance'] = bcdiv($raw, bcpow('10', '18'), 6);
					$resultData['status'] = 'success';
					$success = true;
				});

				if ($success) break; // Jika berhasil, hentikan loop RPC

			} catch (\Throwable $e) {
				$lastError = $e->getMessage();
				continue; // coba RPC berikutnya
			}
		}

		// Jika semua RPC gagal, kembalikan pesan error terakhir
		if (!$success) {
			return $this->respond([
				'status' => 'error',
				'message' => 'All RPC failed: ' . $lastError
			]);
		}

		return $this->respond($resultData);
	}

	//check wallet balance ERC20 USDTT/USDC di Ethereum
	public function postCheck_wallet_erc20()
	{
		$json   = $this->request->getJSON(true);
		$wallet = $json['wallet_address'] ?? null;
		$token  = strtolower($json['token'] ?? '');

		if (!$wallet) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Wallet address is required'
			]);
		}

		// Validasi format wallet (ETH sama dengan BSC)
		if (substr($wallet, 0, 2) !== '0x' || strlen($wallet) !== 42 || !ctype_xdigit(substr($wallet, 2))) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Invalid Ethereum wallet address'
			]);
		}

		// Mapping token ERC20
		$tokenContracts = [
			'usdt_erc20' => ['address' => '0xdAC17F958D2ee523a2206206994597C13D831ec7', 'decimals' => 6],
			'usdc_erc20' => ['address' => '0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48', 'decimals' => 6],
		];

		if (!isset($tokenContracts[$token])) {
			return $this->respond([
				'status' => 'error',
				'message' => 'Token not supported on Ethereum'
			]);
		}

		$rpcUrls = [
			'https://eth-mainnet.public.blastapi.io',
			'https://cloudflare-eth.com',
			'https://rpc.ankr.com/eth'
		];

		$erc20Abi = '[{
        "constant": true,
        "inputs": [{"name":"_owner","type":"address"}],
        "name": "balanceOf",
        "outputs": [{"name":"balance","type":"uint256"}],
        "type": "function"
    	}]';

		$resultData = [
			'status' => 'error',
			'wallet_address' => $wallet,
			'token' => $token,
			'balance' => '0'
		];

		$success = false;
		$lastError = '';

		foreach ($rpcUrls as $rpcUrl) {
			try {
				$provider = new \Web3\Providers\HttpProvider(
					new \Web3\RequestManagers\HttpRequestManager($rpcUrl, 10)
				);
				$web3 = new \Web3\Web3($provider);
				$contract = new \Web3\Contract($web3->provider, $erc20Abi);

				$contract->at($tokenContracts[$token]['address'])->call('balanceOf', $wallet, function ($err, $balance) use (&$resultData, &$success, &$lastError, $tokenContracts, $token) {
					if ($err !== null) {
						$lastError = $err->getMessage();
						return;
					}

					$raw = null;
					if (is_array($balance)) {
						if (isset($balance[0]) && method_exists($balance[0], 'toString')) {
							$raw = $balance[0]->toString();
						} elseif (isset($balance['balance']) && method_exists($balance['balance'], 'toString')) {
							$raw = $balance['balance']->toString();
						}
					}
					if ($raw === null) $raw = '0';

					$decimals = $tokenContracts[$token]['decimals'];
					$resultData['balance'] = bcdiv($raw, bcpow('10', $decimals), 6);
					$resultData['status'] = 'success';
					$success = true;
				});

				if ($success) break;
			} catch (\Throwable $e) {
				$lastError = $e->getMessage();
				continue;
			}
		}

		if (!$success) {
			return $this->respond([
				'status' => 'error',
				'message' => 'All RPC failed: ' . $lastError
			]);
		}

		return $this->respond($resultData);
	}

	//check wallet balance ERC20 USDT/USDC di Polygon
	public function postCheck_wallet_polygon()
	{
		$json   = $this->request->getJSON(true);
		$wallet = $json['wallet_address'] ?? null;
		$token  = strtolower($json['token'] ?? '');

		if (!$wallet) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Wallet address is required'
			]);
		}

		// Validasi format wallet (Polygon juga sama seperti ETH/BSC)
		if (substr($wallet, 0, 2) !== '0x' || strlen($wallet) !== 42 || !ctype_xdigit(substr($wallet, 2))) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Invalid Polygon wallet address'
			]);
		}

		// Mapping token ERC20 di Polygon
		$tokenContracts = [
			'usdt_polygon' => ['address' => '0xc2132D05D31c914a87C6611C10748AEb04B58e8F', 'decimals' => 6],
			'usdc_polygon' => ['address' => '0x3c499c542cEF5E3811e1192ce70d8cC03d5c3359', 'decimals' => 6],
		];

		if (!isset($tokenContracts[$token])) {
			return $this->respond([
				'status' => 'error',
				'message' => 'Token not supported on Polygon'
			]);
		}

		// Daftar RPC Polygon
		$rpcUrls = [
			'https://polygon-rpc.com/',
			'https://rpc.ankr.com/polygon',
			'https://polygon-bor-rpc.publicnode.com'
		];

		$erc20Abi = '[{
        "constant": true,
        "inputs": [{"name":"_owner","type":"address"}],
        "name": "balanceOf",
        "outputs": [{"name":"balance","type":"uint256"}],
        "type": "function"
    	}]';

		$resultData = [
			'status' => 'error',
			'wallet_address' => $wallet,
			'token' => $token,
			'balance' => '0'
		];

		$success = false;
		$lastError = '';

		foreach ($rpcUrls as $rpcUrl) {
			try {
				$provider = new \Web3\Providers\HttpProvider(
					new \Web3\RequestManagers\HttpRequestManager($rpcUrl, 10)
				);
				$web3 = new \Web3\Web3($provider);
				$contract = new \Web3\Contract($web3->provider, $erc20Abi);

				$contract->at($tokenContracts[$token]['address'])->call('balanceOf', $wallet, function ($err, $balance) use (&$resultData, &$success, &$lastError, $tokenContracts, $token) {
					if ($err !== null) {
						$lastError = $err->getMessage();
						return;
					}

					$raw = null;
					if (is_array($balance)) {
						if (isset($balance[0]) && method_exists($balance[0], 'toString')) {
							$raw = $balance[0]->toString();
						} elseif (isset($balance['balance']) && method_exists($balance['balance'], 'toString')) {
							$raw = $balance['balance']->toString();
						}
					}
					if ($raw === null) $raw = '0';

					$decimals = $tokenContracts[$token]['decimals'];
					$resultData['balance'] = bcdiv($raw, bcpow('10', $decimals), 6);
					$resultData['status'] = 'success';
					$success = true;
				});

				if ($success) break;
			} catch (\Throwable $e) {
				$lastError = $e->getMessage();
				continue;
			}
		}

		if (!$success) {
			return $this->respond([
				'status' => 'error',
				'message' => 'All RPC failed: ' . $lastError
			]);
		}

		return $this->respond($resultData);
	}

	//check wallet balance USDC di Base
	public function postCheck_wallet_base()
	{
		$json   = $this->request->getJSON(true);
		$wallet = $json['wallet_address'] ?? null;
		$token  = strtolower($json['token'] ?? '');

		if (!$wallet) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Wallet address is required'
			]);
		}

		// Validasi format wallet Ethereum-style
		if (substr($wallet, 0, 2) !== '0x' || strlen($wallet) !== 42 || !ctype_xdigit(substr($wallet, 2))) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Invalid Base wallet address'
			]);
		}

		// Hanya USDC yang didukung di Base
		$tokenContracts = [
			'usdc_base' => ['address' => '0x833589fCD6eDb6E08f4c7C32D4f71b54bdA02913', 'decimals' => 6],
		];

		if (!isset($tokenContracts[$token])) {
			return $this->respond([
				'status' => 'error',
				'message' => 'Token not supported on Base'
			]);
		}

		$rpcUrls = [
			'https://mainnet.base.org',
			'https://developer-access-mainnet.base.org',
			'https://base-rpc.publicnode.com'
		];

		$erc20Abi = '[{
        "constant": true,
        "inputs": [{"name":"_owner","type":"address"}],
        "name": "balanceOf",
        "outputs": [{"name":"balance","type":"uint256"}],
        "type": "function"
    	}]';

		$resultData = [
			'status' => 'error',
			'wallet_address' => $wallet,
			'token' => $token,
			'balance' => '0'
		];

		$success = false;
		$lastError = '';

		foreach ($rpcUrls as $rpcUrl) {
			try {
				$provider = new \Web3\Providers\HttpProvider(
					new \Web3\RequestManagers\HttpRequestManager($rpcUrl, 10)
				);
				$web3 = new \Web3\Web3($provider);
				$contract = new \Web3\Contract($web3->provider, $erc20Abi);

				$contract->at($tokenContracts[$token]['address'])->call('balanceOf', $wallet, function ($err, $balance) use (&$resultData, &$success, &$lastError, $tokenContracts, $token) {
					if ($err !== null) {
						$lastError = $err->getMessage();
						return;
					}

					$raw = null;
					if (is_array($balance)) {
						if (isset($balance[0]) && method_exists($balance[0], 'toString')) {
							$raw = $balance[0]->toString();
						} elseif (isset($balance['balance']) && method_exists($balance['balance'], 'toString')) {
							$raw = $balance['balance']->toString();
						}
					}
					if ($raw === null) $raw = '0';

					$decimals = $tokenContracts[$token]['decimals'];
					$resultData['balance'] = bcdiv($raw, bcpow('10', $decimals), 6);
					$resultData['status'] = 'success';
					$success = true;
				});

				if ($success) break;
			} catch (\Throwable $e) {
				$lastError = $e->getMessage();
				continue;
			}
		}

		if (!$success) {
			return $this->respond([
				'status' => 'error',
				'message' => 'All RPC failed: ' . $lastError
			]);
		}

		return $this->respond($resultData);
	}

	//check wallet balance USDC di Solana
	public function postCheck_wallet_solana()
	{
		$json   = $this->request->getJSON(true);
		$wallet = $json['wallet_address'] ?? null;
		$token  = strtolower($json['token'] ?? '');

		if (!$wallet) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Wallet address is required'
			]);
		}

		// Validasi address Solana (Base58, 32–44 karakter)
		if (!preg_match('/^[1-9A-HJ-NP-Za-km-z]{32,44}$/', $wallet)) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Invalid Solana wallet address'
			]);
		}

		// Mapping token SPL
		$tokenMints = [
			'usdc_solana' => [
				'mint' => 'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v',
				'decimals' => 6
			],
		];

		if (!isset($tokenMints[$token])) {
			return $this->respond([
				'status' => 'error',
				'message' => 'Token not supported on Solana'
			]);
		}

		// Daftar RPC Solana (fallback kalau satu gagal)
		$rpcUrls = [
			'https://api.mainnet-beta.solana.com',
			'https://solana-api.projectserum.com'
		];

		$resultData = [
			'status' => 'success',
			'wallet_address' => $wallet,
			'token' => $token,
			'balance' => '0'
		];

		$success = false;
		$lastError = '';

		foreach ($rpcUrls as $rpcUrl) {
			try {
				$payload = [
					"jsonrpc" => "2.0",
					"id" => 1,
					"method" => "getTokenAccountsByOwner",
					"params" => [
						$wallet,
						["mint" => $tokenMints[$token]['mint']],
						["encoding" => "jsonParsed"]
					]
				];

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $rpcUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
				curl_setopt($ch, CURLOPT_TIMEOUT, 20);
				$response = curl_exec($ch);

				if (curl_errno($ch)) {
					$lastError = curl_error($ch);
					curl_close($ch);
					continue;
				}
				curl_close($ch);

				if (!$response) {
					$lastError = 'No response from Solana RPC';
					continue;
				}

				$data = json_decode($response, true);

				if (isset($data['result']['value']) && !empty($data['result']['value'])) {
					$totalBalance = '0';
					foreach ($data['result']['value'] as $acc) {
						$tokenAccount = $acc['account']['data']['parsed']['info']['tokenAmount'] ?? null;
						if ($tokenAccount) {
							$raw = $tokenAccount['amount'] ?? '0';
							$totalBalance = bcadd($totalBalance, $raw, 0);
						}
					}

					$decimals = $tokenMints[$token]['decimals'];
					$humanBalance = bcdiv($totalBalance, bcpow('10', $decimals), $decimals);
					$resultData['balance'] = rtrim(rtrim($humanBalance, '0'), '.');
				}

				$success = true;
				break; // stop kalau sudah sukses
			} catch (\Throwable $e) {
				$lastError = $e->getMessage();
				continue;
			}
		}

		if (!$success) {
			return $this->respond([
				'status' => 'error',
				'message' => 'All RPC failed: ' . $lastError
			]);
		}

		return $this->respond($resultData);
	}

	public function postCheck_wallet_trc20()
	{
		$json   = $this->request->getJSON(true);
		$wallet = $json['wallet_address'] ?? null;
		$token  = strtolower($json['token'] ?? '');

		if (!$wallet) {
			return $this->respond([
				'status'  => 'error',
				'message' => 'Wallet address is required'
			]);
		}

		// Validasi Tron wallet Base58 (T + 33 chars)
		if (!preg_match('/^T[1-9A-HJ-NP-Za-km-z]{33}$/', $wallet)) {
			return $this->respond([
				'status' => 'error',
				'message' => 'Invalid Tron wallet address'
			]);
		}

		// USDT TRC20
		$tokenContracts = [
			'usdt_trc20' => [
				'contract' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',
				'decimals' => 6
			]
		];

		if (!isset($tokenContracts[$token])) {
			return $this->respond([
				'status' => 'error',
				'message' => 'Token not supported on Tron'
			]);
		}

		$resultData = [
			'status' => 'success',
			'wallet_address' => $wallet,
			'token' => $token,
			'balance' => '0'
		];

		try {
			$apiUrl = "https://api.trongrid.io/wallet/triggersmartcontract";

			// Convert wallet & contract to HEX
			$hexWallet = $this->tronBase58ToHex($wallet);
			$hexContract = $this->tronBase58ToHex($tokenContracts[$token]['contract']);

			// Payload TRC20 balanceOf(address)
			$payload = [
				"contract_address" => $hexContract,
				"function_selector" => "balanceOf(address)",
				"parameter" => str_pad($hexWallet, 64, "0", STR_PAD_LEFT),
				"owner_address" => $hexWallet
			];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			$response = curl_exec($ch);

			if (curl_errno($ch)) {
				$error = curl_error($ch);
				curl_close($ch);
				return $this->respond([
					'status' => 'error',
					'message' => 'cURL error: ' . $error
				]);
			}
			curl_close($ch);

			if (!$response) {
				return $this->respond([
					'status' => 'error',
					'message' => 'No response from TronGrid'
				]);
			}

			$data = json_decode($response, true);

			// Ambil balance
			if (isset($data['constant_result'][0])) {
				$raw = hexdec($data['constant_result'][0]);
				$decimals = $tokenContracts[$token]['decimals'];
				$humanBalance = bcdiv($raw, bcpow('10', $decimals), $decimals);
				$resultData['balance'] = rtrim(rtrim($humanBalance, '0'), '.');
			}
		} catch (\Throwable $e) {
			return $this->respond([
				'status' => 'error',
				'message' => 'Exception: ' . $e->getMessage()
			]);
		}

		return $this->respond($resultData);
	}

	/**
	 * Convert Tron Base58 wallet ke HEX (prefix 41, abaikan checksum)
	 */
	private function tronBase58ToHex(string $address): string
	{
		$alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
		$base = strlen($alphabet);

		$num = gmp_init(0, 10);

		for ($i = 0; $i < strlen($address); $i++) {
			$pos = strpos($alphabet, $address[$i]);
			if ($pos === false) {
				throw new \Exception("Invalid Base58 character: " . $address[$i]);
			}
			$num = gmp_add(gmp_mul($num, $base), $pos);
		}

		$hex = gmp_strval($num, 16);
		if (strlen($hex) % 2 !== 0) $hex = "0" . $hex;

		$bin = hex2bin($hex);
		if (strlen($bin) < 21) throw new \Exception("Decoded address has invalid length");

		// Ambil 21 bytes pertama (prefix + pubkey)
		$addressBytes = substr($bin, 0, 21);

		return strtoupper(bin2hex($addressBytes));
	}
}
