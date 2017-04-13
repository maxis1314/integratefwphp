<?php 

load_vendor("V_MAILER");

class V_Mailer{
	var $mail;
	function __construct($type="memcached"){		
	    $phpmailer = new PHPMailer();
            $phpmailer->Encoding = "base64";
            $phpmailer->SMTPDebug = false;

            if(true) {
                $phpmailer->IsSMTP();
                $phpmailer->Host = 'mail.gmx.com';
                $phpmailer->Port = 25;
                $phpmailer->SMTPSecure = "";

                $phpmailer->SMTPAuth = true;
                $phpmailer->Username = "daniel.forever@gmx.com";
                $phpmailer->Password = "";
            } else {
                $phpmailer->IsMail();
            }
            $phpmailer->SetFrom("", "");	
            $this->mail = $phpmailer;
	}
	function send($title,$body,$to=array(),$cc=array(),$priority=1){
            $this->mail->Subject      = $title;
            $this->mail->Priority     = $priority;

            $this->mail->Body         = $body;
            $this->mail->AltBody      = str_replace('<br/>', "\n", $body);
	    foreach($to as $one){
            	$this->mail->AddAddress($one, "");
	    }
	    foreach($cc as $one){
            	$this->mail->AddBCC($one, "");
	    }
            $result = $$this->mail->Send();
            $this->mail->ClearAddresses();
	}

}
