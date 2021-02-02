<?php

namespace App\Controller;

use App\Entity\ForgotPass;
use App\Notification\MailNotification;
use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AuthentificationController extends AbstractController
{

    /**
     * @Route("/inscription", name="signin")
     */
    public function signin(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
    	$sigin = new Utilisateur();
        $form = $this->createForm(InscriptionType::class,$sigin);
	    $error=[];

        $form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()){
		    $sigin = $form->getData();
		    $encoded = $encoder->encodePassword($sigin, $form->get("password")->getData());
		    $sigin->setUsername();
		    $sigin->setRoleUser("ROLE_USER");
		    $validator = Validation::createValidator();
		    $telephone = $validator->validate("0".$sigin->getTelephone(), [
			    new Length(['min' => 10,"minMessage"=>'Numéro de téléphone invalide, 10 caractères minimum !']),
			    new Regex(["pattern"=>"/(0)[0-9]{9}/","message"=>"Numéro de téléphone invalide !"])
		    ]);
		    $violations = $validator->validate($sigin->getPassword(), [
			    new Length(['min' => 3,"minMessage"=>'Mot de passe trop court, 3 caractères minimum !']),
		    ]);
		    $sigin->setPassword($encoded);
		    if (0 === count($violations) and 0 === count($telephone) ){
			    $sigin->setTelephone('0'. $form->get("telephone")->getData());
			    if ($form->get("Majeur")->getData() == true ){
			    	$sigin->setMajeur(true);
			    }else{
				    $sigin->setMajeur(false);
			    }
			    $entityManager = $this->getDoctrine()->getManager();
			    $entityManager->persist($sigin);
			    $entityManager->flush();

			    return $this->redirectToRoute('app_login', array(''));
		    }
		    else{
			    if (0 != count($violations)){
				    foreach ($violations as $violation) {
					    $error[] = $violation->getMessage();
				    }
			    }
			    if (0 != count($telephone)){
				    foreach ($telephone as $violation) {
					    $error[]=  $violation->getMessage();
				    }
			    }
		    }
	    }

        return $this->render('authentification/inscription.html.twig', [
            'controller_name' => 'AuthentificationController',
            'form' => $form->createView(),
	        "error"=>$error
        ]);
    }


	/**
	 * @Route("/MotDePasseOublier", name="forgotPassword")
	 */
	public function forgotPassword(Request $request,MailNotification $mail): Response
	{
		$fg= new ForgotPass();
		$form = $this->createFormBuilder()
			->add('mail',EmailType::class )
			->getForm();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$fg->setMailUser($form->get("mail")->getData());
			$fg->setCode(strval(rand(50000,100000)));
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($fg);
			$mail->notifyContact("Mot de passe oublier ! ",$form->get("mail")->getData(),"Un utilisateur vous contact a propos de :",'Votre code de vérification : '
				.$fg->getCode());
			$entityManager->flush();
			return $this->redirectToRoute('ForgotNext',['email'=>$form->get("mail")->getData()]);
		}


		return $this->render('authentification/forgot.html.twig', [
			'controller_name' => 'AuthentificationController',
			'form' => $form->createView(),
		]);

	}

	/**
	 * @Route("/MotDePasseOublier/{email}", name="ForgotNext")
	 */
	public function forgotNext(Request $request,MailNotification $mail,UserPasswordEncoderInterface $encoder, $email): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Utilisateur = $em->getRepository(Utilisateur::class)->findByMail($email);
		$check='';
		$form = $this->createFormBuilder()
			->add('mdp',PasswordType::class )
			->add('code')
			->getForm();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$codeVerif = $em->getRepository(ForgotPass::class)->findByCode($form->get("code")->getData());
			if (!empty($codeVerif)){
				$Utilisateur->setPassword($encoder->encodePassword($Utilisateur, $form->get("mdp")->getData()));
				$em->persist($Utilisateur);
				$em->flush();

				$em->remove($codeVerif[0]);
				$em->flush();
				$check='mot de passe changer !';
			}else{
				$check='Code incorrect !';
			}
		}

		return $this->render('authentification/checkCode.html.twig', [
			'controller_name' => 'AuthentificationController',
			'form' => $form->createView(),
			"check"=>$check
		]);

	}


}
