<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function satoshiAdmin($url, $postData = NULL)
{
    $token = @sha1($_SESSION["logged_user"]->email . $_SESSION["logged_user"]->passwd);

    $ch     = curl_init($url);
    $headers    = array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    );

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    if (!is_null($postData)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }

    $result = (object) array(
        'result'        => json_decode(curl_exec($ch)),
        'status'        => curl_getinfo($ch)['http_code']
    );
    curl_close($ch);
    return $result;
}

function courseAdmin($url, $postData = NULL)
{
    $token = @sha1($_SESSION["logged_usercourse"]->email . $_SESSION["logged_usercourse"]->password);

    $ch     = curl_init($url);
    $headers    = array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    );

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    if (!is_null($postData)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }

    $result = (object) array(
        'result'        => json_decode(curl_exec($ch)),
        'status'        => curl_getinfo($ch)['http_code']
    );
    curl_close($ch);
    return $result;
}

function getExchange($amountEUR){
    // Your Open Exchange Rates API key
    //tdalgo@thedarkalgo.com : Banana69%
    $apiKey = 'e79bf324d5a1443b8e06c33c67c3b444';
    $url = "https://openexchangerates.org/api/latest.json?app_id=$apiKey";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    // Extract the exchange rate for USD to IDR
    $usdToEUR = $data['rates']['EUR'];
    $eurToUSD = 1 / $usdToEUR;
        
    $amountUSD = $amountEUR * $eurToUSD;
    return $amountUSD;
}

function get_coinpayments_nonce(): int
{
    // Detect sandbox or localhost
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $parts = explode('.', $host);
    $isSandbox = ($parts[0] === 'sandbox' || preg_match('/^localhost:(808[0-5])$/', $host));
    // Use separate file for each environment
    $fileName = $isSandbox ? 'coinpayments_nonce_sandbox.txt' : 'coinpayments_nonce_live.txt';
    $filePath = WRITEPATH . $fileName;

    // Create file if not exists
    if (!file_exists($filePath)) {
        // Initialize to 1 for sandbox or current time for live
        $initialValue = $isSandbox ? 1 : time();
        file_put_contents($filePath, $initialValue);
    }

    // Lock file to avoid race condition
    $fp = fopen($filePath, 'c+');
    if (flock($fp, LOCK_EX)) {
        $lastNonce = (int)trim(fread($fp, 100));

        // Choose strategy based on mode
        $newNonce = $isSandbox
            ? $lastNonce + 1
            : max($lastNonce + 1, time());

        // Save new nonce
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $newNonce);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return $newNonce;
    } else {
        fclose($fp);
        throw new \RuntimeException('Unable to acquire nonce file lock.');
    }
}



function sendmail_booking($subject, $mdata)
{
    $mail = new PHPMailer();

    try {
        $mail->isSMTP();
        $mail->Host         = HOST_MAIL;
        $mail->SMTPAuth     = true;
        $mail->Username     = USERNAME_MAIL;
        $mail->Password     = PASS_MAIL;
        $mail->SMTPAutoTLS  = true;
        $mail->SMTPSecure   = "ssl";
        $mail->Port         = 465;
        // $mail->SMTPDebug    = 2;
        $mail->SMTPOptions = array(
            'ssl'   => array(
                'verify_peer'           => false,
                'verify_peer_name'      => false,
                'allow_self_signed'     => false,
            )
        );

        $mail->setFrom(USERNAME_MAIL, NAMETITLE . ' Booking Consultation');
        $mail->addReplyTo(EMAIL_ONE);
        $mail->isHTML(true);

        $mail->ClearAllRecipients();

        $mail->Subject = $subject;
        $mail->AddAddress($mdata['email'][0]);
        $template = emailtemplate_client($mdata);

        $mail->msgHTML($template);

        if (!$mail->send()) {

            session()->setFlashdata('failed', 'Failed send email, please try again!');
            header("Location: " . BASE_URL . 'homepage/bookingconsultation');
            exit();
        } else {

            $mail->isSMTP();
            $mail->Host         = HOST_MAIL;
            $mail->SMTPAuth     = true;
            $mail->Username     = USERNAME_MAIL;
            $mail->Password     = PASS_MAIL;
            $mail->SMTPAutoTLS  = true;
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = 465;
            // $mail->SMTPDebug    = 2;
            $mail->SMTPOptions = array(
                'ssl'   => array(
                    'verify_peer'           => false,
                    'verify_peer_name'      => false,
                    'allow_self_signed'     => false,
                )
            );

            $mail->setFrom(USERNAME_MAIL, NAMETITLE . ' Booking Consultation');
            $mail->addReplyTo($mdata['email'][0]);
            $mail->isHTML(true);

            $mail->ClearAllRecipients();

            $mail->Subject = $subject;
            $mail->AddAddress(EMAIL_ONE);
            $mail->AddAddress(EMAIL_TWO);

            $template = emailtemplate_owner($mdata);

            $mail->msgHTML($template);

            if (!$mail->send()) {
                session()->setFlashdata('failed', 'Failed schedule booked, please try again!');
                header("Location: " . BASE_URL . 'homepage/bookingconsultation');
                exit();
            } else {
                session()->setFlashdata('success', 'Schedule booked successfully');
                header("Location: " . BASE_URL . 'homepage/contact_success');
                exit();
            }
        }
    } catch (Exception $e) {
        session()->setFlashdata('failed', 'Failed schedule booked, please try again!');
        header("Location: " . BASE_URL . 'homepage/bookingconsultation');
        exit();
    }
}


function sendmail_satoshi($email, $subject, $message, $title, $mailsender)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host         = HOST_MAIL;
        $mail->SMTPAuth     = true;
        $mail->Username     = USERNAME_MAIL;
        $mail->Password     = PASS_MAIL;
        $mail->SMTPAutoTLS  = true;
        $mail->SMTPSecure   = "tls";
        $mail->Port         = 587;
        $mail->SMTPOptions = array(
            'ssl'   => array(
                'verify_peer'           => false,
                'verify_peer_name'      => false,
                'allow_self_signed'     => false,
            )
        );

        $mail->SMTPDebug = 0;

        if ($mail->SMTPDebug > 0) {
            $mail->Debugoutput = function ($str, $level) {
                log_message('debug', "PHPMailer [$level]: $str");
            };
        }

        $mail->setFrom($mailsender, $title . ' Activation Email');
        $mail->isHTML(true);
        $mail->ClearAllRecipients();
        $mail->Subject = $subject;
        $mail->AddAddress($email);
        $mail->msgHTML($message);
        $mail->send();

        return true;
    } catch (Exception $e) {
        log_message('error', 'PHPMailer Error: ' . $e->getMessage());

        return false;
    }
}



function sendmail_contactform($subject, $mdata)
{
    $mail = new PHPMailer();

    try {
        $mail->isSMTP();
        $mail->Host         = HOST_MAIL;
        $mail->SMTPAuth     = true;
        $mail->Username     = USERNAME_MAIL;
        $mail->Password     = PASS_MAIL;
        $mail->SMTPAutoTLS  = true;
        $mail->SMTPSecure   = "tls";
        $mail->Port         = 587;
        $mail->SMTPOptions = array(
            'ssl'   => array(
                'verify_peer'           => false,
                'verify_peer_name'      => false,
                'allow_self_signed'     => false,
            )
        );

        $mail->setFrom(USERNAME_MAIL, NAMETITLE . ' Contact Form');
        $mail->addReplyTo($mdata['email']);
        $mail->isHTML(true);
        $mail->ClearAllRecipients();
        $mail->Subject = $subject;
        $mail->AddAddress(EMAIL_ONE);
        $mail->AddAddress(EMAIL_TWO);


        $template = emailtemplate_regular($mdata);
        $mail->msgHTML($template);

        if (!$mail->send()) {
            session()->setFlashdata('failed', 'Failed Send Message, Please Try Again!');
            header("Location: " . BASE_URL . 'homepage/contactform');
            exit();
        } else {
            session()->setFlashdata('success', 'Message successfully send');
            header("Location: " . BASE_URL . 'homepage/contact_success');
            exit();
        }
    } catch (Exception $e) {
        session()->setFlashdata('failed', 'Failed Send Message, Please Try Again!');
        header("Location: " . BASE_URL . 'homepage/contactform');
        exit();
    }
}

function sendmail_referral($subject, $mdata, $attachmentPath = null, $attachmentName = null)
{
    $mail = new PHPMailer();

    try {
        $mail->isSMTP();
        $mail->Host         = HOST_MAIL;
        $mail->SMTPAuth     = true;
        $mail->Username     = USERNAME_MAIL;
        $mail->Password     = PASS_MAIL;
        $mail->SMTPAutoTLS  = true;
        $mail->SMTPSecure   = "tls";
        $mail->Port         = 587;
        $mail->SMTPOptions = array(
            'ssl'   => array(
                'verify_peer'           => false,
                'verify_peer_name'      => false,
                'allow_self_signed'     => false,
            )
        );

        $mail->setFrom(USERNAME_MAIL, NAMETITLE . ' Request Referral');
        $mail->addReplyTo($mdata['email']);
        $mail->isHTML(true);
        $mail->ClearAllRecipients();
        $mail->Subject = $subject;
        $mail->AddAddress(EMAIL_ONE);
        $mail->AddAddress(EMAIL_TWO);

        // Attachments
        if (!empty($attachmentPath)) {
            $mail->addAttachment($attachmentPath, $attachmentName); // Add PDF from temporary path
        }

        $template = emailtemplate_referral($mdata);
        $mail->msgHTML($template);

        if (!$mail->send()) {
            session()->setFlashdata('failed', 'Failed Send Message, Please Try Again!');
            header("Location: " . BASE_URL . 'homepage/contactreferral');
            exit();
        } else {
            session()->setFlashdata('success', 'Message successfully send');
            header("Location: " . BASE_URL . 'homepage/contact_success');
            exit();
        }
    } catch (Exception $e) {
        session()->setFlashdata('failed', 'Failed Send Message, Please Try Again!');
        header("Location: " . BASE_URL . 'homepage/contactreferral');
        exit();
    }
}


function sendmail_accountdel($subject, $mdata)
{
    $mail = new PHPMailer();

    try {
        $mail->isSMTP();
        $mail->Host         = HOST_MAIL;
        $mail->SMTPAuth     = true;
        $mail->Username     = USERNAME_MAIL;
        $mail->Password     = PASS_MAIL;
        $mail->SMTPAutoTLS  = true;
        $mail->SMTPSecure   = "tls";
        $mail->Port         = 587;
        $mail->SMTPOptions = array(
            'ssl'   => array(
                'verify_peer'           => false,
                'verify_peer_name'      => false,
                'allow_self_signed'     => false,
            )
        );

        $mail->setFrom(USERNAME_MAIL, NAMETITLE . ' Account Deletion');
        $mail->addReplyTo($mdata['email']);
        $mail->isHTML(true);
        $mail->ClearAllRecipients();
        $mail->Subject = $subject;
        $mail->AddAddress(EMAIL_ONE);
        $mail->AddAddress(EMAIL_TWO);


        $template = emailtemplate_accountdel($mdata);
        $mail->msgHTML($template);

        if (!$mail->send()) {
            session()->setFlashdata('failed', 'Failed Send Message, Please Try Again!');
            header("Location: " . BASE_URL . 'homepage/account_deletion?step=' . base64_encode('second_step'));
            exit();
        } else {
            session()->setFlashdata('success', 'Message successfully send');
            header("Location: " . BASE_URL . 'homepage/account_deletion?step=' . base64_encode('third_step'));
            exit();
        }
    } catch (Exception $e) {
        session()->setFlashdata('failed', 'Failed Send Message, Please Try Again!');
        header("Location: " . BASE_URL . 'homepage/account_deletion?step=' . base64_encode('second_step'));
        exit();
    }
}
