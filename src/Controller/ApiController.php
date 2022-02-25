<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Helper\ParameterTrait;

class ApiController extends AbstractController
{
    use ParameterTrait;
    /**
    * @Route("/api/send-mail", name="send-mail", methods={"POST"})
    */
    public function getSendMail(Request $request, ValidatorInterface $validator)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $inputArr = $request->toArray();
        
        $inputArr['name'] = isset($inputArr['data']['name']) ? $inputArr['data']['name'] : "";
        $inputArr['phone_number'] = isset($inputArr['data']['phone_number']) ? $inputArr['data']['phone_number'] : "";
        $inputArr['email'] = isset($inputArr['data']['email']) ? $inputArr['data']['email'] : "";
        $inputArr['text'] = isset($inputArr['data']['text']) ? $inputArr['data']['text'] : "";

        $constraints = new Assert\Collection([
            'name' => [new Assert\Length(['min' => 2]), new Assert\NotBlank],
            'phone_number' => [new Assert\Length(['min' => 10]), new Assert\NotBlank],
            'email' => [new Assert\Email, new Assert\NotBlank],
            'text' => [new Assert\Length(['min' => 1]), new Assert\NotBlank],
            'language_code' => [new Assert\Length(['min' => 2, 'max' => "2"]), new Assert\NotBlank],
            'token' => [new Assert\Optional],
            'data' => new Assert\Optional
        ]);

        $errorMessages = $this->validate_assert($constraints, $inputArr, $validator);
        if (count($errorMessages) > 0) {
            return $this->json(["message" => "PLEASE_FILL_ALL_FIELDS"], Response::HTTP_BAD_REQUEST);
        }


        try
        {

            //set SMTP SETTINGS

            ini_set('SMTP','smtp.hostinger.com');
            ini_set('smtp_port',465);

            $mailTemplateMasterRo = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> <title>Mesaj vizitator Naty-Style</title> <meta name="viewport" content="width=device-width,initial-scale=1.0"/> </head> <body style="margin: 0; padding: 0;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td style="padding: 10px 0 30px 0;"> <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"> <tr> <td align="center" style="padding: 40px 0 30px 0;"> <img src="http://cdn.naty-style.com/logo-clean-2.3b846433.png"width="65%" alt="Rosance"> </td></tr><tr> <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"> <table border="0" cellpadding="0" cellspacing="0"> <tr> <td style="color: #153643;font-size: 24px;"><b>Mesaj de la %name%<br/></b></td></tr><tr> <td style="padding: 20px 0 30px 0; color: #153643; font-size: 16px;"> %message% <br/> <hr/> Nume: <b>%name%</b> <br/> Număr de telefon: <b>%phone_number%</b> <br/> Email: <b>%email%</b> <Br/> </td></tr></table> </td></tr><tr> <td bgcolor="#11CDEF" style="padding: 30px 30px 30px 30px;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td style="color: #ffffff; font-size: 14px;" width="75%">&reg; Naty-Style, Tratăm animăluțul ca și cum ar fi al nostru</td><td align="right" width="25%"> <table border="0" cellpadding="0" cellspacing="0"> <tr> <td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;"><a href="https://www.instagram.com/naty_style_grooming" style="color: #ffffff;"><img src="http://cdn.naty-style.com/instagram.png" width="38" height="38" alt="instagram" /></a></td><td style="font-size: 0;line-height: 0;" width="20">&nbsp;</td><td style="font-size: 12px; font-weight: bold;"><a href="https://www.facebook.com/natystylegrooming/" style="color: #ffffff;"><img src="http://cdn.naty-style.com/facebook.png" width="38" height="38" alt="Facebook" /></a></td></tr></table> </td></tr></table> </td></tr></table> </td></tr></table> </body></html>';
            $mailTemplateMasterEn = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> <title>Naty-Style visitor message</title> <meta name="viewport" content="width=device-width,initial-scale=1.0"/> </head> <body style="margin: 0; padding: 0;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td style="padding: 10px 0 30px 0;"> <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"> <tr> <td align="center" style="padding: 40px 0 30px 0;"> <img src="http://cdn.naty-style.com/logo-clean-2.3b846433.png"width="65%" alt="Rosance"> </td></tr><tr> <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"> <table border="0" cellpadding="0" cellspacing="0"> <tr> <td style="color: #153643;font-size: 24px;"><b>Message from %name%<br/></b></td></tr><tr> <td style="padding: 20px 0 30px 0; color: #153643; font-size: 16px;"> %message% <br/> <hr/> Name: <b>%name%</b> <br/> Phone number: <b>%phone_number%</b> <br/> Email: <b>%email%</b> <Br/> </td></tr></table> </td></tr><tr> <td bgcolor="#11CDEF" style="padding: 30px 30px 30px 30px;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td style="color: #ffffff; font-size: 14px;" width="75%">&reg; Naty-Style, We treat your pet like it is ours</td><td align="right" width="25%"> <table border="0" cellpadding="0" cellspacing="0"> <tr> <td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;"><a href="https://www.instagram.com/naty_style_grooming" style="color: #ffffff;"><img src="http://cdn.naty-style.com/instagram.png" width="38" height="38" alt="Instagram" /></a></td><td style="font-size: 0;line-height: 0;" width="20">&nbsp;</td><td style="font-size: 12px; font-weight: bold;"><a href="https://www.facebook.com/natystylegrooming/" style="color: #ffffff;"><img src="http://cdn.naty-style.com/facebook.png" width="38" height="38" alt="Facebook" /></a></td></tr></table> </td></tr></table> </td></tr></table> </td></tr></table> </body></html>';
            $mailTemplateSlaveRo = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> <title>Vă mulțumim pentru interesul acordat</title> <meta name="viewport" content="width=device-width,initial-scale=1.0"/> </head> <body style="margin: 0; padding: 0;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td style="padding: 10px 0 30px 0;"> <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"> <tr> <td align="center" style="padding: 40px 0 30px 0;"> <img src="http://cdn.naty-style.com/logo-clean-2.3b846433.png"width="65%" alt="Rosance"> </td></tr><tr> <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"> <table border="0" cellpadding="0" cellspacing="0"> <tr> <td style="color: #153643;font-size: 24px;"><b>Bună ziua,<br/></b></td></tr><tr> <td style="padding: 20px 0 30px 0; color: #153643; font-size: 16px;"> Vă mulțumim pentru interesul acordat, am primit mesajul dumneavoastră și vă vom răspunde în cel mai scurt timp cu putință. <br/> Cu drag, echipa Naty-Style </td></tr></table> </td></tr><tr> <td bgcolor="#11CDEF" style="padding: 30px 30px 30px 30px;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td style="color: #ffffff; font-size: 14px;" width="75%">&reg; Naty-Style, Tratăm animăluțul ca și cum ar fi al nostru!<br/>Ați primit acest email că urmare a unei solicitări de contact pe <a href="https://naty-style.com">Naty-Style</a> <br/> Dacă nu ați făcut dumneavoastră solicitarea atunci ignorați acest email</td><td align="right" width="25%"> <table border="0" cellpadding="0" cellspacing="0"> <tr> <td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;"><a href="https://www.instagram.com/naty_style_grooming" style="color: #ffffff;"><img src="http://cdn.naty-style.com/instagram.png" width="38" height="38" alt="Instagram" /></a></td><td style="font-size: 0;line-height: 0;" width="20">&nbsp;</td><td style="font-size: 12px; font-weight: bold;"><a href="https://www.facebook.com/natystylegrooming/" style="color: #ffffff;"><img src="http://cdn.naty-style.com/facebook.png" width="38" height="38" alt="Facebook" /></a></td></tr></table> </td></tr></table> </td></tr></table> </td></tr></table> </body></html>';
            $mailTemplateSlaveEn = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> <title>Vă mulțumim pentru interesul acordat</title> <meta name="viewport" content="width=device-width,initial-scale=1.0"/> </head> <body style="margin: 0; padding: 0;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td style="padding: 10px 0 30px 0;"> <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"> <tr> <td align="center" style="padding: 40px 0 30px 0;"> <img src="http://cdn.naty-style.com/logo-clean-2.3b846433.png"width="65%" alt="Rosance"> </td></tr><tr> <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"> <table border="0" cellpadding="0" cellspacing="0"> <tr> <td style="color: #153643;font-size: 24px;"><b>Hello,<br/></b></td></tr><tr> <td style="padding: 20px 0 30px 0; color: #153643; font-size: 16px;"> Thank you for your interest, we have received your message and we will get back to you as soon as possible. <br/> With love, Naty-Style team</td></tr></table> </td></tr><tr> <td bgcolor="#11CDEF" style="padding: 30px 30px 30px 30px;"> <table border="0" cellpadding="0" cellspacing="0" width="100%"> <tr> <td style="color: #ffffff; font-size: 14px;" width="75%">&reg; Naty-Style, We treat your pet like it is ours!<br/>You have received this email due to a contact form registration on <a href="https://naty-style.com">Naty-Style</a> <br/> If you did not completed contact form please ignore this message.</td><td align="right" width="25%"> <table border="0" cellpadding="0" cellspacing="0"> <tr> <td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;"><a href="https://www.instagram.com/naty_style_grooming" style="color: #ffffff;"><img src="http://cdn.naty-style.com/instagram.png" width="38" height="38" alt="Instagram" /></a></td><td style="font-size: 0;line-height: 0;" width="20">&nbsp;</td><td style="font-size: 12px; font-weight: bold;"><a href="https://www.facebook.com/natystylegrooming/" style="color: #ffffff;"><img src="http://cdn.naty-style.com/facebook.png" width="38" height="38" alt="Facebook" /></a></td></tr></table> </td></tr></table> </td></tr></table> </td></tr></table> </body></html>';
    
            //Mail admin
            if($inputArr['language_code'] == "ro")
            {
                $mailTemplateMasterRo = str_replace("%message%",$inputArr['text'],$mailTemplateMasterRo);
                $mailTemplateMasterRo = str_replace("%name%",$inputArr['name'],$mailTemplateMasterRo);
                $mailTemplateMasterRo = str_replace("%phone_number%",$inputArr['phone_number'],$mailTemplateMasterRo);
                $mailTemplateMasterRo = str_replace("%email%",$inputArr['email'],$mailTemplateMasterRo);
            }else{
                $mailTemplateMasterEn = str_replace("%message%",$inputArr['text'],$mailTemplateMasterEn);
                $mailTemplateMasterEn = str_replace("%name%",$inputArr['name'],$mailTemplateMasterEn);
                $mailTemplateMasterEn = str_replace("%phone_number%",$inputArr['phone_number'],$mailTemplateMasterEn);
                $mailTemplateMasterEn = str_replace("%email%",$inputArr['email'],$mailTemplateMasterEn);
            }

            $headerAdmin = "From:".$_ENV['SERVER_EMAIL']." \r\n";
			$headerAdmin .= "MIME-Version: 1.0 \r\n";
			$headerAdmin .= "Content-type: text/html\r\n";;

            $returnAdmin = mail(
                $_ENV['MAIN_EMAIL'],
                $inputArr['language_code'] == "ro" ? "Mesaj vizitator Naty-Style" : "Naty-Style visitor message",
                $inputArr['language_code'] == "ro" ? $mailTemplateMasterRo : $mailTemplateMasterEn,
                $headerAdmin);

            //Mail user
            $headerUser = "From:".$_ENV['SERVER_EMAIL']." \r\n";
			$headerUser .= "MIME-Version: 1.0 \r\n";
			$headerUser .= "Content-type: text/html\r\n";;

            $returnUser = mail(
                $inputArr['email'],
                $inputArr['language_code'] == "ro" ? "Vă mulțumim pentru interesul acordat" : "Thank you for your interest",
                $inputArr['language_code'] == "ro" ? $mailTemplateSlaveRo : $mailTemplateSlaveEn,
                $headerUser);


            if($returnAdmin && $returnUser)
                return $this->json(["message" => "MAIL_SENT"], Response::HTTP_OK);
            elseif($returnAdmin && !$returnUser)
			    return $this->json(["message" => "MAIL_NOT_SENT_USER"], Response::HTTP_OK);
            elseif(!$returnAdmin && $returnUser)
			    return $this->json(["message" => "MAIL_NOT_SENT_ADMIN"], Response::HTTP_OK);
            else
                return $this->json(["message" => "MAIL_NOT_SENT"], Response::HTTP_OK);

        }catch(Exception $ex)
        {
            return $this->json(['message' => "INTERNAL_ERROR"], Response::HTTP_OK);
        }
    }
}

?>