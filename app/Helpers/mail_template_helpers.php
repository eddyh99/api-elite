<?php


function emailtemplate_client($mdata)
{
    // Looping email
    $requestemail = '';
    foreach ($mdata['email'] as $dt) {
        $requestemail .= '<br>' . $dt;
    }

    // Get Date and Start time
    $datestart = explode('#', $mdata['datetime']);
    $datestart = explode(' ', $datestart[0]);

    // Get End time only
    $dateend = explode('#', $mdata['datetime']);
    $dateend = explode(' ', $dateend[1]);

    return "
    <!DOCTYPE html>
        <html lang='en'>

        <head>
            <meta name='color-scheme' content='light'>
            <meta name='supported-color-schemes' content='light'>
            <title>Send Email</title>
        </head>

        <body>
            <div style='max-width: 600px; margin: 0 auto; position: relative; padding: 1rem; background-color: #F7F7F7;'>
                <div style='text-align: center; padding: 3rem;'>
                    <h3 style='font-weight: 600; font-size: 30px; line-height: 45px; color: #000000; margin-bottom: 1rem;text-align: center;'>
                        Hi, " . $mdata['fname'] . "
                    </h3>
                    <img src='" . BASE_URL . "assets/img/logo.png' alt='" . NAMETITLE . "' height='80'>
                </div>

                <div style='text-align: left; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        Thank you for scheduling the meeting. <br>
                        This is to confirm that we have received your payment of " . FEEMEETING . " for the scheduled meeting. Please let us know if you have any questions or need further assistance.
                    </p>
                    <p style='font-weight: 400;font-size: 18px;color: #000000;'>
                        Your Summary
                    </p>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        First Name: <span style='font-weight: 800;'>" . $mdata['fname'] . "</span><br>
                        Last Name: <span style='font-weight: 800;'>" . $mdata['lname'] . "</span><br>
                        Whatsapp: <span style='font-weight: 800;'>" . $mdata['whatsapp'] . "</span><br>
                        Email: <span style='font-weight: 800;'>" . $requestemail . "</span><br>
                        Timezone: <span style='font-weight: 800;'>" . $mdata['timezone'] . "</span><br>
                        Date: <span style='font-weight: 800;'>" . $datestart[0] . "</span><br>
                        Time: <span style='font-weight: 800;'>" . $datestart[1] . ' Until ' . $dateend[1] . "</span><br>
                        Description: <span style='font-weight: 800;'>" . $mdata['description'] . "</span><br>
                    </p>
                </div>
                <div style='text-align: center; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        Best regards,<br>  
                        " . NAMETITLE . " team
                    </p>
                </div>
                <hr>
                <hr>
                <p style='text-align: center;font-weight: 400;font-size: 12px;color: #999999;'>
                    Copyright © " . date('Y') . "
                </p>
            </div>
        </body>
    </html>";
}


function emailtemplate_owner($mdata)
{
    // Looping email
    $requestemail = '';
    foreach ($mdata['email'] as $dt) {
        $requestemail .= '<br>' . $dt;
    }

    // Get Date and Start time
    $datestart = explode('#', $mdata['datetime']);
    $datestart = explode(' ', $datestart[0]);

    // Get End time only
    $dateend = explode('#', $mdata['datetime']);
    $dateend = explode(' ', $dateend[1]);

    return "
    <!DOCTYPE html>
        <html lang='en'>

        <head>
            <meta name='color-scheme' content='light'>
            <meta name='supported-color-schemes' content='light'>
            <title>Send Email</title>
        </head>

        <body>
            <div style='max-width: 600px; margin: 0 auto; position: relative; padding: 1rem; background-color: #F7F7F7;'>
                <div style='text-align: center; padding: 3rem;'>
                    <h3 style='font-weight: 600; font-size: 30px; line-height: 45px; color: #000000; margin-bottom: 1rem;text-align: center;'>
                        New Schedule Meeting
                    </h3>
                    <img src='" . BASE_URL . "assets/img/logo.png' alt='" . NAMETITLE . "' height='80'>
                </div>

                <div style='text-align: left; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        Thank you for scheduling the meeting. <br>
                        This is to confirm that we have received payment of " . FEEMEETING . " for the scheduled meeting.<br>
                        Check your Google Calendar for make sure your schedule is ready.
                    </p>
                    <p style='font-weight: 400;font-size: 18px;color: #000000;'>
                        Summary Client
                    </p>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        First Name: <span style='font-weight: 800;'>" . $mdata['fname'] . "</span><br>
                        Last Name: <span style='font-weight: 800;'>" . $mdata['lname'] . "</span><br>
                        Whatsapp: <span style='font-weight: 800;'>" . $mdata['whatsapp'] . "</span><br>
                        Email: <span style='font-weight: 800;'>" . $requestemail . "</span><br>
                        Timezone: <span style='font-weight: 800;'>" . $mdata['timezone'] . "</span><br>
                        Date: <span style='font-weight: 800;'>" . $datestart[0] . "</span><br>
                        Time: <span style='font-weight: 800;'>" . $datestart[1] . ' Until ' . $dateend[1] . "</span><br>
                        Description: <span style='font-weight: 800;'>" . $mdata['description'] . "</span><br>
                    </p>
                </div>
                <div style='text-align: center; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        Best regards,<br>  
                        " . NAMETITLE . " team
                    </p>
                </div>
                <hr>
                <hr>
                <p style='text-align: center;font-weight: 400;font-size: 12px;color: #999999;'>
                    Copyright © " . date('Y') . "
                </p>
            </div>
        </body>
    </html>";
}

function emailtemplate_regular($mdata)
{
    return "
    <!DOCTYPE html>
        <html lang='en'>

        <head>
            <meta name='color-scheme' content='light'>
            <meta name='supported-color-schemes' content='light'>
            <title>Send Email</title>
        </head>

        <body>
            <div style='max-width: 600px; margin: 0 auto; position: relative; padding: 1rem; background-color: #F7F7F7;'>
                <div style='text-align: center; padding: 3rem;'>
                    <h3 style='font-weight: 600; font-size: 30px; line-height: 45px; color: #000000; margin-bottom: 1rem;text-align: center;'>
                        New email from, " . $mdata['fname'] . "
                    </h3>
                    <img src='" . BASE_URL . "assets/img/logo.png' alt='" . NAMETITLE . "' height='80'>
                </div>

                <div style='text-align: left; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        First Name: <span style='font-weight: 800;'>" . $mdata['fname'] . "</span><br>
                        Last Name: <span style='font-weight: 800;'>" . $mdata['lname'] . "</span><br>
                        Whatsapp: <span style='font-weight: 800;'>" . $mdata['whatsapp'] . "</span><br>
                        Email: <span style='font-weight: 800;'>" . $mdata['email'] . "</span><br>
                        Description: <span style='font-weight: 800;'>" . $mdata['description'] . "</span><br>
                    </p>
                </div>
                <div style='text-align: center; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        Best regards,<br>  
                        " . NAMETITLE . " team
                    </p>
                </div>
                <hr>
                <hr>
                <p style='text-align: center;font-weight: 400;font-size: 12px;color: #999999;'>
                    Copyright © " . date('Y') . "
                </p>
            </div>
        </body>
    </html>";
}

function emailtemplate_referral($mdata)
{
    return "
    <!DOCTYPE html>
        <html lang='en'>

        <head>
            <meta name='color-scheme' content='light'>
            <meta name='supported-color-schemes' content='light'>
            <title>Send Email</title>
        </head>

        <body>
            <div style='max-width: 600px; margin: 0 auto; position: relative; padding: 1rem; background-color: #F7F7F7;'>
                <div style='text-align: center; padding: 3rem;'>
                    <h3 style='font-weight: 600; font-size: 30px; line-height: 45px; color: #000000; margin-bottom: 1rem;text-align: center;'>
                        New email from, " . $mdata['fname'] . "
                    </h3>
                    <img src='" . BASE_URL . "assets/img/logo.png' alt='" . NAMETITLE . "' height='80'>
                </div>

                <div style='text-align: left; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        I would like to kindly request my approval for Referral Member at " . NAMETITLE . " <br>
                        Please let me know if you need any further information or clarification in below.
                    </p>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        First Name: <span style='font-weight: 800;'>" . $mdata['fname'] . "</span><br>
                        Last Name: <span style='font-weight: 800;'>" . $mdata['lname'] . "</span><br>
                        Whatsapp: <span style='font-weight: 800;'>" . $mdata['whatsapp'] . "</span><br>
                        Email: <span style='font-weight: 800;'>" . $mdata['email'] . "</span><br>
                        Instagram: <span style='font-weight: 800;'>" . @$mdata['instagram'] . "</span><br>
                        Tiktok: <span style='font-weight: 800;'>" . @$mdata['tiktok'] . "</span><br>
                        Linkedin: <span style='font-weight: 800;'>" . @$mdata['linkedin'] . "</span><br>
                        Discord: <span style='font-weight: 800;'>" . @$mdata['discord'] . "</span><br>
                        Facebook Profile: <span style='font-weight: 800;'>" . @$mdata['fprofile'] . "</span><br>
                        Facebook Group: <span style='font-weight: 800;'>" . @$mdata['fgroup'] . "</span><br>
                        Facebook Page: <span style='font-weight: 800;'>" . @$mdata['fpage'] . "</span><br>
                    </p>
                </div>
                <div style='text-align: center; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        Best regards,<br>  
                        " . NAMETITLE . " team
                    </p>
                </div>
                <hr>
                <hr>
                <p style='text-align: center;font-weight: 400;font-size: 12px;color: #999999;'>
                    Copyright © " . date('Y') . "
                </p>
            </div>
        </body>
    </html>";
}


function emailtemplate_accountdel($mdata)
{
    return "
    <!DOCTYPE html>
        <html lang='en'>

        <head>
            <meta name='color-scheme' content='light'>
            <meta name='supported-color-schemes' content='light'>
            <title>Send Email</title>
        </head>

        <body>
            <div style='max-width: 600px; margin: 0 auto; position: relative; padding: 1rem; background-color: #F7F7F7;'>
                <div style='text-align: center; padding: 3rem;'>
                    <h3 style='font-weight: 600; font-size: 30px; line-height: 45px; color: #000000; margin-bottom: 1rem;text-align: center;'>
                        Request Account Deletion
                    </h3>
                    <img src='" . BASE_URL . "assets/img/logo.png' alt='" . NAMETITLE . "' height='80'>
                </div>

                <div style='text-align: left; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        Email: <span style='font-weight: 800;'>" . $mdata['email'] . "</span><br>
                        Reason: <span style='font-weight: 800;'>" . $mdata['reason'] . "</span><br>
                    </p>
                </div>
                <div style='text-align: center; padding-bottom: 1rem;'>
                    <p style='font-weight: 400;font-size: 14px;color: #000000;'>
                        Best regards,<br>  
                        " . NAMETITLE . " team
                    </p>
                </div>
                <hr>
                <hr>
                <p style='text-align: center;font-weight: 400;font-size: 12px;color: #999999;'>
                    Copyright © " . date('Y') . "
                </p>
            </div>
        </body>
    </html>";
}

function emailtemplate_activation_account($otp, $email, $type, $link = null)
{
    return "
     <!DOCTYPE html>
        <html lang='en'>
            <head>
                <meta name='color-scheme' content='light'>
                <meta name='supported-color-schemes' content='light'>
                <title>Activation Account PN Global</title>
            </head>
    
            <body>
                <div style='
                max-width: 420px;
                margin: 0 auto;
                position: relative;
                padding: 1rem;
                '>
                    <div style='
                    text-align: center;
                    padding: 3rem;
                    '>
                        <h3 style='
                        font-weight: 600;
                        font-size: 30px;
                        line-height: 45px;
                        color: #000000;
                        margin-bottom: 1rem;
                        text-align: center;
                        '>
                            Dear, New User
                        </h3>
                    </div>
    
                    <div style='
                    text-align: center;
                    padding-bottom: 1rem;
                    '>
                        <p style='
                        font-weight: 400;
                        font-size: 14px;
                        color: #000000;
                        '>
                            Thank you for register PN Global " . $type . ". To proceed with your request, enter OTP for Active Account Below
                        </p>
                        <h1 style='letter-spacing: 12px;'>" . $otp . "</h1>
                        <br><br>
                        <h5>
                            or you can click link below for activation account
                        </h5>
                        <a target='_blank' href='" . BASE_URL . ($link ?? "homepage/satoshi_active_account/")  . base64_encode($email) . "'>
                            Active Account
                        </a>
                        <p style='
                        font-weight: 400;
                        font-size: 14px;
                        color: #000000;
                        '>
                            Best regards,<br>  
                            PN Global Team
                        </p>
                    </div>
                    <hr>
                    <hr>
                    <p style='
                    text-align: center;
                    font-weight: 400;
                    font-size: 12px;
                    color: #999999;
                    '>
                        Copyright © " . date('Y') . "
                    </p>
                </div>
            </body>
        </html>";
}

function emailtemplate_resend_token($otp, $email)
{
    return "
    <!DOCTYPE html>
        <html lang='en'>
            <head>
                <meta name='color-scheme' content='light'>
                <meta name='supported-color-schemes' content='light'>
                <title>Activation Account Satoshi Signal</title>
            </head>

            <body>
                <div style='
                max-width: 420px;
                margin: 0 auto;
                position: relative;
                padding: 1rem;
                '>
                    <div style='
                    text-align: center;
                    padding: 3rem;
                    '>
                        <h3 style='
                        font-weight: 600;
                        font-size: 30px;
                        line-height: 45px;
                        color: #000000;
                        margin-bottom: 1rem;
                        text-align: center;
                        '>
                            Dear, User
                        </h3>
                    </div>

                    <div style='
                    text-align: center;
                    padding-bottom: 1rem;
                    '>
                        <p style='
                        font-weight: 400;
                        font-size: 14px;
                        color: #000000;
                        '>
                            Thank you for register PN Global. To proceed with your request, enter OTP for Active Account Below
                        </p>
                        <h1 style='letter-spacing: 12px;'>" . $otp . "</h1>
                        <br><br>
                        <h5>
                            or you can click link below for activation account
                        </h5>
                        <a target='_blank' href='" . BASE_URL . "homepage/satoshi_active_account/" . base64_encode($email) . "'>
                            Active Account
                        </a>
                        <p style='
                        font-weight: 400;
                        font-size: 14px;
                        color: #000000;
                        '>
                            Best regards,<br>  
                            PN Global Team
                        </p>
                    </div>
                    <hr>
                    <hr>
                    <p style='
                    text-align: center;
                    font-weight: 400;
                    font-size: 12px;
                    color: #999999;
                    '>
                        Copyright © " . date('Y') . "
                    </p>
                </div>
            </body>
        </html>";
}

function emailtemplate_forgot_password($token, $email)
{
    return "
    <!DOCTYPE html>
        <html lang='en'>

        <head>
			<meta name='color-scheme' content='light'>
			<meta name='supported-color-schemes' content='light'>
			<title>Activation Account Satoshi Signal</title>
		</head>

		<body>
			<div style='
			max-width: 420px;
			margin: 0 auto;
			position: relative;
			padding: 1rem;
			'>
				<div style='
				text-align: center;
				padding: 3rem;
				'>
					<h3 style='
					font-weight: 600;
					font-size: 20px;
					line-height: 45px;
					color: #000000;
					margin-bottom: 1rem;
					text-align: center;
					'>
						Dear, <br> " . $email . "
					</h3>
				</div>

				<div style='
				text-align: center;
				padding-bottom: 1rem;
				'>
					<p style='
					font-weight: 400;
					font-size: 14px;
					color: #000000;
					'>
						Thank you for using Satoshi Signal App. To proceed with your request, please copy token reset password below 
					</p>
					<h2 id='copyToken'>
						" . $token . "
					</h2>
					<p style='
					font-weight: 400;
					font-size: 14px;
					color: #000000;
					'>
						Best regards,<br>  
						Satoshi Signal Team

					</p>
				</div>
				<hr>
				<hr>
				<p style='
				text-align: center;
				font-weight: 400;
				font-size: 12px;
				color: #999999;
				'>
					Copyright © " . date('Y') . "
				</p>
			</div>
		</body>
		</html>";
}

function emailtemplate_new_password($email)
{
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta name='color-scheme' content='light'>
        <meta name='supported-color-schemes' content='light'>
        <title>Reset Password - Satoshi Signal</title>
    </head>
    <body>
        <div style='max-width:420px; margin:0 auto; padding:1rem;'>
            <div style='text-align:center; padding:3rem;'>
                <h3 style='font-weight:600; font-size:30px; color:#000;'>Reset Password</h3>
            </div>
            <div style='text-align:center;'>
                <p style='font-size:14px; color:#000;'>
                    Anda telah mengajukan permintaan untuk reset password.
                    Silakan klik tombol di bawah ini untuk melanjutkan proses reset password:
                </p>
                <a href='" . BASE_URL . "member/auth/forgot_password' style='display:inline-block; padding:10px 20px; margin-top:1rem; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:4px;'>
                    Reset Password
                </a>
                <p style='font-size:14px; color:#000; margin-top:1rem;'>
                    Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda: 
                    <br>" . BASE_URL . "member/auth/forgot_password
                </p>
            </div>
            <hr style='margin-top:2rem;'>
            <p style='text-align:center; font-size:12px; color:#999;'>
                Copyright © " . date('Y') . "
            </p>
        </div>
    </body>
    </html>";
}

// course
function emailtemplate_activation_course($otp, $email)
{
    return "
     <!DOCTYPE html>
        <html lang='en'>
            <head>
                <meta name='color-scheme' content='light'>
                <meta name='supported-color-schemes' content='light'>
                <title>Activation Account PN Global</title>
            </head>
    
            <body>
                <div style='
                max-width: 420px;
                margin: 0 auto;
                position: relative;
                padding: 1rem;
                '>
                    <div style='
                    text-align: center;
                    padding: 3rem;
                    '>
                        <h3 style='
                        font-weight: 600;
                        font-size: 30px;
                        line-height: 45px;
                        color: #000000;
                        margin-bottom: 1rem;
                        text-align: center;
                        '>
                            Dear, New User
                        </h3>
                    </div>
    
                    <div style='
                    text-align: center;
                    padding-bottom: 1rem;
                    '>
                        <p style='
                        font-weight: 400;
                        font-size: 14px;
                        color: #000000;
                        '>
                            Thank you for register PN Global Course. To proceed with your request, enter OTP for Active Account Below
                        </p>
                        <h1 style='letter-spacing: 12px;'>" . $otp . "</h1>
                        <br><br>
                        <h5>
                            or you can click link below for activation account
                        </h5>
                        <a target='_blank' href='" . BASE_URL . "course/auth/forgot_password/" . base64_encode($email) . "'>
                            Active Account
                        </a>
                        <p style='
                        font-weight: 400;
                        font-size: 14px;
                        color: #000000;
                        '>
                            Best regards,<br>  
                            PN Global Team
                        </p>
                    </div>
                    <hr>
                    <hr>
                    <p style='
                    text-align: center;
                    font-weight: 400;
                    font-size: 12px;
                    color: #999999;
                    '>
                        Copyright © " . date('Y') . "
                    </p>
                </div>
            </body>
        </html>";
}

function emailtemplate_payment_course($link)
{
    return "
     <!DOCTYPE html>
        <html lang='en'>
            <head>
                <meta name='color-scheme' content='light'>
                <meta name='supported-color-schemes' content='light'>
                <title>PN Global Course - Complete Your Payment</title>
            </head>
    
            <body>
                <div style='
                max-width: 420px;
                margin: 0 auto;
                position: relative;
                padding: 1rem;
                '>
                    <div style='
                    text-align: center;
                    padding: 3rem;
                    '>
                        <h3 style='
                        font-weight: 600;
                        font-size: 30px;
                        line-height: 45px;
                        color: #000000;
                        margin-bottom: 1rem;
                        text-align: center;
                        '>
                            Dear, New User
                        </h3>
                    </div>
    
                    <div style='
                    text-align: center;
                    padding-bottom: 1rem;
                    '>
                        <p style='
                        font-weight: 400;
                        font-size: 14px;
                        color: #000000;
                        '>
                        Thank you for registering for the PN Global Course. To activate your account, please complete the payment using the link below:
                    </p>
                    <a href='" . $link . "' target='_blank'>" . $link . "</a>
                    <br><br>
                        <p style='
                        font-weight: 400;
                        font-size: 14px;
                        color: #000000;
                        '>
                            Best regards,<br>  
                            PN Global Team
                        </p>
                    </div>
                    <hr>
                    <hr>
                    <p style='
                    text-align: center;
                    font-weight: 400;
                    font-size: 12px;
                    color: #999999;
                    '>
                        Copyright © " . date('Y') . "
                    </p>
                </div>
            </body>
        </html>";
}

function emailtemplate_payment_onetoone($paymentlink, $amount, $paymenttimeout, $invoiceID)
{
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta name='color-scheme' content='light'>
        <meta name='supported-color-schemes' content='light'>
        <title>PN Global Course - Payment Reminder</title>
    </head>
    <body>
        <div style='
            max-width: 420px;
            margin: 0 auto;
            position: relative;
            padding: 1rem;
            font-family: Arial, sans-serif;
        '>
            <div style='
                padding: 2rem 1rem;
                text-align: left;
            '>
                <p style='
                    font-size: 16px;
                    color: #000000;
                    margin-bottom: 1rem;
                '>
                    Dear,
                </p>

                <p style='
                    font-size: 14px;
                    color: #000000;
                    margin-bottom: 1rem;
                '>
                    I hope this message finds you well.
                </p>

                <p style='
                    font-size: 14px;
                    color: #000000;
                    margin-bottom: 1rem;
                '>
                    This is a friendly reminder that your payment of <strong>{$amount}</strong> for <strong>Elite PNGlobal</strong> is now due.
                </p>

                <p style='
                    font-size: 14px;
                    color: #000000;
                    margin-bottom: 1rem;
                '>
                    We kindly ask you to settle the outstanding amount at your earliest convenience. Below are the payment details:
                </p>

                <ul style='
                    font-size: 14px;
                    color: #000000;
                    margin-bottom: 1rem;
                    padding-left: 1.2rem;
                '>
                    <li>Invoice Number: <strong>{$invoiceID}</strong></li>
                    <li>Due Date: <strong>{$paymenttimeout}</strong></li>
                    <li>Amount Due: <strong>{$amount}</strong></li>
                </ul>

                <div style='text-align: center; margin: 20px 0;'>
                    <a href='{$paymentlink}' target='_blank' style='
                        display: inline-block;
                        padding: 12px 24px;
                        background-color: #b48b3d;
                        color: #ffffff;
                        text-decoration: none;
                        font-size: 16px;
                        border-radius: 5px;
                        font-weight: bold;
                    '>
                        Complete Payment
                    </a>
                </div>

                <p style='
                    font-size: 14px;
                    color: #000000;
                    margin-bottom: 1rem;
                '>
                    If you have already made the payment, please disregard this message. Otherwise, we would appreciate your prompt attention to this matter.
                </p>

                <p style='
                    font-size: 14px;
                    color: #000000;
                    margin-bottom: 2rem;
                '>
                    Should you have any questions or need further clarification, feel free to contact us.
                </p>

                <p style='
                    font-size: 14px;
                    color: #000000;
                '>
                    Thank you for your attention and cooperation.<br><br>
                    Best regards,<br>
                    <strong>PNGlobalInternational</strong>
                </p>
            </div>

            <hr>
            <p style='
                text-align: center;
                font-size: 12px;
                color: #999999;
                margin-top: 2rem;
            '>
                &copy; " . date('Y') . " PN Global. All rights reserved.
            </p>
        </div>
    </body>
    </html>";
}

function emailtemplate_paymentstatus_onetoone($invoiceID){
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta name='color-scheme' content='light'>
        <meta name='supported-color-schemes' content='light'>
        <title>PN Global Course - Payment Success</title>
    </head>
    <body>
        <div style='
            max-width: 420px;
            margin: 0 auto;
            position: relative;
            padding: 1rem;
            font-family: Arial, sans-serif;
        '>
            <div style='
                padding: 2rem 1rem;
                text-align: left;
            '>
                <p style='
                    font-size: 16px;
                    color: #000000;
                    margin-bottom: 1rem;
                '>
                    Dear Customer,
                </p>

                <p style='
                    font-size: 14px;
                    color: #000000;
                    margin-bottom: 1rem;
                '>
                    Thank you for your payment. Your transaction has been successfully processed.
                </p>

                <ul style='
                    font-size: 14px;
                    color: #000000;
                    margin-bottom: 1rem;
                    padding-left: 1.2rem;
                '>
                    <li>Invoice Number: <strong>{$invoiceID}</strong></li>
                </ul>

                <p style='
                    font-size: 14px;
                    color: #000000;
                '>
                    Best regards,<br>
                    <strong>PNGlobalInternational</strong>
                </p>
            </div>

            <hr>
            <p style='
                text-align: center;
                font-size: 12px;
                color: #999999;
                margin-top: 2rem;
            '>
                &copy; " . date('Y') . " PN Global. All rights reserved.
            </p>
        </div>
    </body>
    </html>
    ";
}
