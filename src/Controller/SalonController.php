<?php

namespace App\Controller;

use App\Entity\Realisation;
use App\Notification\MailNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;


class SalonController extends AbstractController
{
    /**
     * @Route("/salon", name="salon")
     */
    public function index(Request $request,MailNotification $mail): Response
    {

    	$em= $this->getDoctrine()->getManager();
    	$Rea = $em->getRepository(Realisation::class)->findAll();
	    $form = $this->createFormBuilder()
		    ->add('nom', TextType::class)
		    ->add('mail',EmailType::class )
		    ->add('telephone', NumberType::class )
		    ->add('message', TextareaType::class)
		    ->getForm();
	    $checker='';


	    $form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()){
	    	if (!empty($request->get('g-recaptcha-response'))){
			    $validator = Validation::createValidator();
			    $telephone = $validator->validate("0".$form->get("telephone")->getData(), [
				    new Length(['min' => 10,"minMessage"=>'Numéro de téléphone invalide, 10 caractères minimum !']),
				    new Regex(["pattern"=>"/(0)[0-9]{9}/","message"=>"Numéro de téléphone invalide !"])
			    ]);

			    if (0 === count($telephone) ){
				    $mail->notifyContact("Message du formulaire de contact ! ",$form->get("mail")->getData(),"Un utilisateur vous contact a propos de :",$form->get("message")->getData
				    ());
				    $checker ="Mail envoyer !";
			    }else{
				    $checker="Erreur dans le formulaire !";
			    }
		    }else{
	    		$checker='Il faut remplir le captcha !';
		    }
	    }



        return $this->render('salon/index.html.twig', [
            'controller_name' => 'SalonController',
	        'form' => $form->createView(),
	        "checker"=>$checker,
	        'Realisation'=>$Rea
        ]);
    }
}
