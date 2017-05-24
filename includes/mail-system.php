<?php
/* Send a mail. 
   Return `false` if everything has been executed corectly or error message on failure.
 --------------------------------------------------------------------------------------*/ 
function send_mail( $to, $subject, $message ){
	// Get settings from DB
	$settings = get_option( 'mail_settings' );

	//------------------------------------//--------------------------------------//

	if( isset($settings) ){
		//Create a new PHPMailer instance
		$mail = new PHPMailer;

		//Encoding
		$mail->CharSet = 'UTF-8';

		//Tell PHPMailer to use SMTP
		$mail->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;

		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$mail->Host = $settings['host'];
		// use
		// $mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = $settings['port'];

		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = $settings['encryption'];

		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = $settings['username'];

		//Password to use for SMTP authentication
		$mail->Password = $settings['password'];

		//Set who the message is to be sent from
		$mail->setFrom($settings['from'], $settings['from_name']);

		//Set an alternative reply-to address
		// $mail->addReplyTo('replyto@example.com', 'First Last');

		//Set who the message is to be sent to
		$to = (array) explode( ',', $to );
		foreach ($to as $t) {
			$mail->addAddress( $t );
		}

		//Set the subject line
		$mail->Subject = $subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML( $message );

		//Replace the plain text body with one created manually
		// $mail->AltBody = 'This is a plain-text message body';

		//Attach an image file
		// $mail->addAttachment('images/phpmailer_mini.png');

		//send the message, check for errors
		if (!$mail->send()) {
		    return "Mailer Error: " . $mail->ErrorInfo;
		} else {
		    return false;
		}
	}
	else{
		return 'Invalid settings!';
	}
}



/*
-------------------------------------------------------------------------------
Actions
-------------------------------------------------------------------------------
*/

// Trimite un email unui admin atunci cind a fost inregistrata o cerere
add_action( 'cerere_inregistrata_cu_success', function( $data ){
	$settings = get_option( 'mail_templates' );

	$sent = send_mail( 
		$settings['cerere_inregistrata_email'], 
		$settings['cerere_inregistrata_subject'], 
		str_ireplace( 
			array( '{SITE_URL}', '{SITE_TITLE}' ), 
			array( get_site_url(), get_option('site_title') ), 
			$settings['cerere_inregistrata_message'] 
		)
	);

} );

// Trimite un email unui utilizator atunci cind cererea lui a fost acceptata
add_action( 'cerere_acceptata', function( $cerere_id, $message ){
	$settings = get_option( 'mail_templates' );

	$cerere = get_cerere_by_id( $cerere_id );

	$sent = send_mail( 
		$cerere['email'], 
		$settings['cerere_acceptata_subject'], 
		str_ireplace( 
			array( '{SITE_URL}', '{SITE_TITLE}', '{REVIEW_MESSAGE}' ), 
			array( get_site_url(), get_option('site_title'), $message ),
			$settings['cerere_acceptata_message'] 
		)
	);

}, 99, 2 );

// Trimite un email unui utilizator atunci cind cererea lui a fost respinsa
add_action( 'cerere_respinsa', function( $cerere_id, $message ){
	$settings = get_option( 'mail_templates' );

	$cerere = get_cerere_by_id( $cerere_id );

	$sent = send_mail( 
		$cerere['email'], 
		$settings['cerere_respinsa_subject'], 
		str_ireplace( 
			array( '{SITE_URL}', '{SITE_TITLE}', '{REVIEW_MESSAGE}' ), 
			array( get_site_url(), get_option('site_title'), $message ),
			$settings['cerere_respinsa_message'] 
		)
	);

}, 99, 2 );