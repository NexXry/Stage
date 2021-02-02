<?php


namespace App\Notification;
use Twig\Environment;


class MailNotification
{
	private \Swift_Mailer  $mailer;
	/**
	 * @var Environment
	 */
	private Environment $renderer;

	public function __construct(\Swift_Mailer $mailer, Environment $renderer){

		$this->mailer = $mailer;
		$this->renderer = $renderer;
	}

	public function notify($Subject,$userMail,$dateRdv,$message,$corpsMsg){
		$message = (new \Swift_Message($Subject))
			->setFrom('LDTATOUAGES&PIERICNG@example.com')
			->setTo($userMail)
			->setBody(
				$this->renderer->render(
				// templates/emails/registration.html.twig
					'mail/mail.html.twig',
					['dateRdv' => $dateRdv,'message' => $message,"msg"=>$corpsMsg]
				),
				'text/html'
			)
		;
		$this->mailer->send($message);
	}

	public function notifyContact($Subject,$userMail,$message,$corpsMsg){
		$message = (new \Swift_Message($Subject))
			->setFrom('LDTATOUAGES&PIERICNG@example.com')
			->setTo($userMail)
			->setBody(
				$this->renderer->render(
				// templates/emails/registration.html.twig
					'mail/mail.html.twig',
					['dateRdv' => "",'message' => $message,"msg"=>$corpsMsg]
				),
				'text/html'
			)
		;
		$this->mailer->send($message);
	}

}