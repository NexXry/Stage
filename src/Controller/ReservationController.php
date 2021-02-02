<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\ReservationPiercing;
use App\Entity\ReservationTatoo;
use App\Form\PiercingType;
use App\Form\ReservationType;
use App\Form\TatouageType;
use App\Notification\MailNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="reservation")
     */
    public function index(): Response
    {
    	/*Current Week(1)*/
    	$dateStart = $this->getWeekdayStart("last monday");
	    $dateEnd = $this->getWeekdayEnd("last monday");
	    $Jours = [];
	    $jourST=intval($dateStart[2])+1;
	    $i=$jourST;
	    $jourED=intval($dateEnd[2]);
	    $CuurentDay = $this->getCuurentDay();
	    while($i <= $jourED-1){
	        $Jours[] = $jourST++;
	        $i++;
	    }
	    /*Next Week(2)*/
	    $dateNStart = $this->getWeekdayStart("next monday");
	    $dateNEnd = $this->getWeekdayEnd("next monday");
	    $JoursN = [];
	    $jourSTN=intval($dateNStart[2])+1;
	    $iN=$jourSTN;
	    $jourEDN=intval($dateNEnd[2]);
	    while($iN <= $jourEDN-1){
		    $JoursN[] = $jourSTN++;
		    $iN++;
	    }
	    /*Another Week(3)*/
	    $dateAStart = $this->getWeekdayStart("next monday +1 week");
	    $dateAEnd = $this->getWeekdayEnd("next monday +1 week");
	    $JoursA = [];
	    $jourSTA=intval($dateAStart[2])+1;
	    $iA=$jourSTA;
	    $jourEDA=intval($dateAEnd[2]);
	    while($iA <= $jourEDA-1){
		    $JoursA[] = [
		    	"Jours"=>$jourSTA++
		    ];
		    $iA++;
	    }

	    /*the reservation*/
	    $em = $this->getDoctrine()->getManager();
	    $reservations = $em->getRepository(Reservation::class)->findAll();
	    $userReservations = [];
	    $user = $this->get('security.token_storage')->getToken()->getUser();
	    $reservationd ='';

	    foreach ($reservations as $res){
	    	if ($res->getUnUtilisateur() == $user){
			    $userReservations [] = $res;
			    $reservationd = $em->getRepository(Reservation::class)->findIdUser($res->getUnUtilisateur());
			    $reservationd = $reservationd[0]['id'];
		    }
	    }

        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
	        "date"=>$Jours,
	        "Nextdate"=>$JoursN,
	        'Another'=>$JoursA,
	        "Current"=>$CuurentDay,
	        "reservations"=>$userReservations,
	        "resId"=>$reservationd

        ]);
    }

	/**
	 * @Route("/reservation/{jour}", name="days")
	 */
	public function reservation($jour): Response
	{
		$heures=[
			"10","11","12","14","15","16","17","18","19"
		];
		$CuurentHour = $this->getCuurentHour();
		$t = $this->getDateDay("last monday",$jour);
		$dateCheck = date("d", strtotime("now"));
		return $this->render('reservation/choices.html.twig', [
			'controller_name' => 'ReservationController',
			"t"=>$t,
			"heures"=>$heures,
			"HeureEnCours"=>$CuurentHour,
			"dateCheck"=>$dateCheck,
			"j"=>$jour
		]);
	}

	/**
	 * @Route("/reservation/validation/{date}/{heure}/{type}", name="validation")
	 */
	public function validation($date,$heure,$type, Request $request): Response
	{
		$Date= $date;
		$Heure=$heure;
		$dateForDb = new\DateTime($Date);
		$dateForDb->setTime(intval($Heure),00);
		$jour = explode('-',$date);
		$user = $this->get('security.token_storage')->getToken()->getUser();
		$er='';


		if ($type == "tatouage" || $type == "piercing") {
			if ($type == "tatouage") {
				$reservation = new ReservationTatoo($dateForDb, "attente", "", $user);
				$form = $this->createForm(TatouageType::class, $reservation);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$reservation = $form->getData();
					$reservation->setDateReservation($dateForDb);
					$reservation->setEtat("attente");
					$reservation->setMessage($form->get("message")->getData());
					$reservation->setUnUtilisateur($user);

					if ($dateForDb->format('H') >= 10 and $dateForDb->format('H') <= 19) {
						$entityManager = $this->getDoctrine()->getManager();
						$entityManager->persist($reservation);
						$entityManager->flush();
						return $this->redirectToRoute('reservation');
					} else {
						$er = 'Vous pouvez prendre rendez-vous uniquement entre 10h et 19h';
					}
				}
			}

			if ($type == "piercing") {
				$reservation = new ReservationPiercing($dateForDb, "attente", "", $user);
				$form = $this->createForm(PiercingType::class, $reservation);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$reservation = $form->getData();
					$reservation->setDateReservation($dateForDb);
					$reservation->setEtat("attente");
					$reservation->setMessage($form->get("message")->getData());
					$reservation->setUnUtilisateur($user);

					if ($dateForDb->format('H') >= 10 and $dateForDb->format('H') <= 19) {
						$entityManager = $this->getDoctrine()->getManager();
						$entityManager->persist($reservation);
						$entityManager->flush();
						return $this->redirectToRoute('reservation');
					} else {
						$er = 'Vous pouvez prendre rendez-vous uniquement entre 10h et 19h';
					}
				}
			}
		}else{
			return $this->redirectToRoute('reservation',[""]);
		}



		return $this->render('reservation/validation.html.twig', [
			'controller_name' => 'ReservationController',
			"date"=>$Date,
			"heure"=>$Heure,
			"jour"=>$jour[2],
			'form' => $form->createView(),
			'erreur'=>$er,
			"type"=>$type
		]);
	}

	/**
	 * @Route("/reservation/annuler/{id}", name="annuler")
	 * @param $id
	 * @param MailNotification $mail
	 * @return Response
	 */
	public function annuler($id, MailNotification $mail)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$reservation = $entityManager->getRepository(Reservation::class)->find($id);
		$mail->notify("Annulation de rendez-vous",$reservation->getUnUtilisateur()->getMail(),$reservation->getDateReservation(),$reservation->getMessage(), "Vous avez annulez votre rendez-vous du");

		$entityManager->remove($reservation);
		$entityManager->flush();

		return $this->redirectToRoute('reservation');
	}

	/**
	 * @return false|string[]
	 * Retourne début la semaine courante
	 */
	public function getWeekdayStart(String $string) {

		$monday = strtotime($string);

		$monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;

		$sunday = strtotime(date("Y-m-d",$monday)." +6 days");

		$this_week_sd = date("Y-m-d",$monday);

		$jour = explode('-',$this_week_sd);


		return  $jour;

	}

	/**
	 * @return false|string[]
	 * Retourne fin la semaine courante
	 */
	public function getWeekdayEnd($string) {

		$monday = strtotime($string);

		$monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;

		$sunday = strtotime(date("Y-m-d",$monday)." +6 days");

		$this_week_ed = date("Y-m-d",$sunday);

		$jour = explode('-',$this_week_ed);


		return  $jour;

	}

	/**
	 * @return false|string[]
	 * Retourne début la semaine courante
	 */
	public function getDateDay(String $string,String $day) {

		$monday = strtotime($string);

		$monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;

		$sunday = strtotime(date("Y-m-".$day,$monday)." +6 days");

		$this_week_sd = date("Y-m-".$day,$monday);

		return  $this_week_sd;

	}

	/**
	 * @return int
	 */
	public function getCuurentDay() {

		$monday = date( "Y-m-d", strtotime( "now" ));
		$jour = explode('-',$monday);

		return  intval($jour[2]);

	}

	/**
	 * @return int
	 */
	public function getCuurentHour() {

		$monday = date( "H", strtotime( "now" ));

		return  $monday;

	}




}
