<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Category;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Notification\MailNotification;
use App\Paypal\PayPalPayment;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Stripe;

class BoutiqueController extends AbstractController
{
    /**
     * @Route("/boutique", name="boutique")
     */
    public function index(): Response
    {
    	$em = $this->getDoctrine()->getManager();
    	$Products = $em->getRepository(Produit::class)->findAll();

	    $em = $this->getDoctrine()->getManager();
	    $categ = $em->getRepository(Category::class)->findAll();
        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
	        "products" => $Products,
	        "categ"=>$categ
        ]);
    }

	/**
	 * @Route("/boutique/categ/{id}", name="boutique_categ")
	 */
	public function categ($id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Products = $em->getRepository(Produit::class)->findAll();

		$prd=[];

		foreach($Products as $prod){
			if ($prod->getLaCategorie()->getId() == $id){
				$prd[] = $prod;
			}
		}

		$em = $this->getDoctrine()->getManager();
		$categ = $em->getRepository(Category::class)->findAll();
		return $this->render('boutique/index.html.twig', [
			'controller_name' => 'BoutiqueController',
			"products" => $prd,
			"categ"=>$categ
		]);
	}

	/**
	 * @Route("/boutique/detail/{id}", name="boutique_detail")
	 */
	public function detail($id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Products = $em->getRepository(Produit::class)->find($id);

		$OtherProducts = $em->getRepository(Produit::class)->findAll();

		$prd=[];

		foreach($OtherProducts as $prod){
			if ($prod->getLaCategorie()->getId() == $Products->getLaCategorie()->getId() and $prod->getId() != $Products->getId()){
				$prd[] = $prod;
			}
		}

		return $this->render('boutique/detailProduit.html.twig', [
			'controller_name' => 'BoutiqueController',
			"products" => $Products,
			"otherProducts"=>$prd
		]);
	}

	/**
	 * @Route("/create-checkout-session/{id}", name="checkout")
	 */
	public function chackout($id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Product = $em->getRepository(Produit::class)->find($id);
		\Stripe\Stripe::setApiKey('sk_test_51IEdD4HXAaZs9PMrBTQJsUykYi1oNv5HHNWVb37rzH1UbbZOLocRuSSY0JHBxphaMqDzOSn31ATJ3NlrjEKrsC5K00wZL8oFrs');
		$user = $this->get('security.token_storage')->getToken()->getUser();

		$session = \Stripe\Checkout\Session::create([
			'customer_email' => $user->getMail(),
			'submit_type' => 'pay',
			'billing_address_collection' => 'required',
			'shipping_address_collection' => [
				'allowed_countries' => ['FR'],
			],
			'payment_method_types' => ['card'],
			'line_items' => [[
				'price_data' => [
					'currency' => 'eur',
					'product_data' => [
						'name' => $Product->getNom(),
						'images' => ["https://nicolas-castex.fr/uploads/products/".$Product->getImage()],
					],
					'unit_amount' => round($Product->getPrix()*100),
				],
				'quantity' => 1,
			]],
			'mode' => 'payment',
			'success_url' => $this->generateUrl('success',["produit"=>$id,"user"=>$user],UrlGeneratorInterface::ABSOLUTE_URL),
			'cancel_url' => $this->generateUrl('cancel',["produit"=>$id,"user"=>$user],UrlGeneratorInterface::ABSOLUTE_URL),
		]);

		return $this->json([ 'id' => $session->id ]);
	}

	/**
	 * @Route("/achatReussi/{produit}/{user}", name="success")
	 */
	public function success_url($produit,$user,MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$users = $em->getRepository(Utilisateur::class)->find($user);
		$produits = $em->getRepository(Produit::class)->find($produit);
		$achat = new Achat();
		$achat->setIdCommande(sha1(strval(Rand(0,10000000000))));
		$achat->setDateAchat(new DateTime());
		$achat->setDescriptionAchat("vous avez acheté un ".$produits->getNom()." pour ".$produits->getPrix()." €.");
		$achat->setPaymentAmount($produits->getPrix());
		$achat->setRecu("choisir");
		$achat->setLeUtilisateur($users);
		$achat->setLeProduit($produits);
		$em = $this->getDoctrine()->getManager();
		$em->persist($achat);
		$em->flush();
		$produits->setQuantite($produits->getQuantite()-1);
		$em->persist($produits);
		$em->flush();
		$mail->notifyContact("Nouvel achat  ! ",$users->getMail(),"Vous avez acheté un produit, aller sur votre compte puis choisir le mode de récupération.","Détail du produit: "
			.$achat->getDescriptionAchat());
		return $this->redirectToRoute('profil_recup',['id'=>$achat->getId()]);
	}

	/**
	 * @Route("/achatAnnuler/{produit}/{user}", name="cancel")
	 */
	public function error_url($produit,$user,MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$produits = $em->getRepository(Produit::class)->find($produit);
		$users = $em->getRepository(Utilisateur::class)->find($user);

		$mail->notifyContact("la tentative d'achat a échoué ! ",$users->getMail(),"Vérifier que vous avez les fonds pour l'achat sinon contacté le responsable du site 
		!","Réessayer plus tard.");

		return $this->render('boutique/failed.html.twig', [
			'controller_name' => 'BoutiqueController',
			"products" => $produits,
		]);
	}





//	/**
//	 * @Route("/achat/{produit}/{user}", name="boutique_detail_achat",methods={"POST"})
//	 * @return Response
//	 */
//	public function detailA(Request $request,$produit,$user,MailNotification $mail): Response
//	{
//		$em = $this->getDoctrine()->getManager();
//		$users = $em->getRepository(Utilisateur::class)->find($user);
//		$produits = $em->getRepository(Produit::class)->find($produit);
//		$requestData = json_decode($request->getContent(), true);
//		$achat = new Achat();
//		$achat->setIdCommande($requestData['details']["id"]);
//		$achat->setDateAchat(new DateTime());
//		$achat->setDescriptionAchat("vous avez acheter ".$produits->getNom()." pour ".$produits->getPrix()." €.");
//		$achat->setPaymentAmount($produits->getPrix());
//		$achat->setRecu(false);
//		$achat->setLeUtilisateur($users);
//		$achat->setLeProduit($produits);
//		$em = $this->getDoctrine()->getManager();
//		$em->persist($achat);
//		$em->flush();
//		$produits->setQuantite($produits->getQuantite()-1);
//		$em->persist($produits);
//		$em->flush();
//		$mail->notifyContact("Nouvel achat  ! ",$users->getMail(),"Vous avez acheté un produit, vérifier que votre adresse de livraison est correcte sinon passer récupérer le produit au salon !","Détail du produit: "
//			.$achat->getDescriptionAchat());
//		if (1==1){
//			return $this->redirectToRoute('profil_achats',[]);
//		}
//
//
//	}


}
