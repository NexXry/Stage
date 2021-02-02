<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Notification\MailNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index(): Response
    {
	    return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

	/**
	 * @Route("/modifer/{id}", name="profil-modifier")
	 */
	public function mod($id,Request $request, UserPasswordEncoderInterface $encoder): Response
	{
		$em= $this->getDoctrine();
		$sigin = $em->getRepository(Utilisateur::class)->find($id);
		$form = $this->createForm(InscriptionType::class,$sigin);
		$error=[];
		$user = $this->get('security.token_storage')->getToken()->getUser();

		if ($user->getId() == $id){
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid()){
				$sigin = $form->getData();
				$encoded = $encoder->encodePassword($sigin, $form->get("password")->getData());
				$sigin->setUsername();
				$sigin->setRoleUser($sigin->getRoleUser()[0]);
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
		}else{
			return $this->redirectToRoute('profil', array(''));
		}
		return $this->render('profil/modifier.html.twig', [
			'controller_name' => 'ProfilController',
			'form' => $form->createView(),
			"error"=>$error
		]);
	}


	/**
	 * @Route("/profil/choixMethodeRecuperationProduit/{id}", name="profil_recup")
	 */
	public function recuProduit($id)
	{
		$em = $this->getDoctrine()->getManager();
		$achats = $em->getRepository(Achat::class)->find($id);
		$user = $this->get('security.token_storage')->getToken()->getUser();

		if ($achats->getLeUtilisateur() == $user){
			return $this->render('profil/typeRecup.html.twig', [
				'controller_name' => 'ProfilController',
				'achat' =>$achats
			]);
		}
	}


	/**
	 * @Route("/profil/MesAchats", name="profil_achats")
	 */
	public function achats(): Response
	{
		$em = $this->getDoctrine()->getManager();
		$achats = $em->getRepository(Achat::class)->findAll();
		$user = $this->get('security.token_storage')->getToken()->getUser();
		$meinAchats = array();

		foreach ($achats as $ach){
			if ($ach->getLeUtilisateur()->getId() == $user->getId()){
				$meinAchats[] = $ach;
			}
		}

		return $this->render('profil/achats.html.twig', [
			'controller_name' => 'ProfilController',
			'achats' =>$meinAchats
		]);
	}

	/**
	 * @Route("/profil/produitReçu/{id}", name="profil_reçu")
	 *
	 */
	public function donner($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$ach = $em->getRepository(Achat::class)->find($id);
		$user = $em->getRepository(Utilisateur::class)->find($ach->getLeUtilisateur());

		$ach->setRecu("recu");

		$em->persist($ach);
		$em->flush();
		$mail->notifyContact("Votre produit est en votre possession  ! ",$user->getMail(),"Merci de votre achat.","Détail de l'achat: ".$ach->getDescriptionAchat());




		return $this->redirectToRoute('profil_achats',[]);
	}

	/**
	 * @Route("/profil/auSalon/{id}", name="profil_toSalon")
	 *
	 */
	public function profil_toSalon($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$ach = $em->getRepository(Achat::class)->find($id);
		$user = $em->getRepository(Utilisateur::class)->find($ach->getLeUtilisateur());

		$ach->setRecu("salon");

		$em->persist($ach);
		$em->flush();
		$mail->notifyContact("Votre produit est au Salon  ! ",$user->getMail(),"Venez le récupérer !","Détail de l'achat: ".$ach->getDescriptionAchat());


		return $this->redirectToRoute('profil_achats',[]);
	}

	/**
	 * @Route("/profil/EtreLivrer/{id}", name="profil_livrer")
	 *
	 */
	public function profil_livrer($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$ach = $em->getRepository(Achat::class)->find($id);
		$user = $em->getRepository(Utilisateur::class)->find($ach->getLeUtilisateur());

		$ach->setRecu("livrer");

		$em->persist($ach);
		$em->flush();
		$mail->notifyContact("Votre produit vous sera livré ! ",$user->getMail(),"Durée de livraison 2 semaine maximum","Détail de l'achat: ".$ach->getDescriptionAchat());


		return $this->redirectToRoute('profil_achats',[]);
	}



}
