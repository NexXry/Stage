<?php


function checkInfo($host, $nom, $mdp)
{
	$tab = [];
	$aMails = [];

	try {
		$dbh = new PDO('mysql:host=' . $host . ';dbname=Stage', $nom, $mdp);

		$sth = $dbh->prepare('SELECT date_reservation,mail from reservation,utilisateur WHERE reservation.un_utilisateur_id = utilisateur.id');
		if ($sth->execute(array())) {
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$tab[] = $row;
			}
		}
	} catch (PDOException $e) {
		print "Erreur !: " . $e->getMessage() . "<br/>";
		die();
	}

	for ($i = 0; $i < count($tab); $i++) {
		if (date('Y-m-d', strtotime($tab[$i]["date_reservation"])) == date('Y-m-d', strtotime("now +1 day"))&& date("w",strtotime("now +1 day"))!="0") {
				$aMails[]=[
					"date"=>$tab[$i]["date_reservation"],
					"utilisateur"=>$tab[$i]["mail"],
					"aMails"=>true
				];
		}
		else {
			$aMails[]=[
				"date"=>$tab[$i]["date_reservation"],
				"utilisateur"=>$tab[$i]["mail"],
				"aMails"=>false
			];
		}
		var_dump(date('Y-m-d', strtotime("now +1 day")));
	}


	return $aMails;
}

function mailAllTrue()
{
	$aMails =checkInfo('127.0.0.1:3306','NexXry',"Nicolas32v.");
	if (!empty($aMails)){
		for ($i = 0; $i < count($aMails); $i++) {
			if ($aMails[$i]["aMails"] == true){
                var_dump($aMails);
				mail( $aMails[$i]["utilisateur"]
					, "N'oublier pas votre rendez-vous !"
					,  "vous avez un rendez-vous le ".$aMails[$i]["date"]);

			}
		}
	}

}

mailAllTrue();










