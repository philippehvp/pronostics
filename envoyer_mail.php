<?php

	// Fonction d'envoi d'un mail
	function envoyerMail($titre, $message) {
		// Adresses de destination
		$adresseDestinataire = 'alexandrepueyo@hotmail.com,salles.jl@free.fr,alexandredecorps@gmail.com,philippe_hvp@hotmail.com';
		//$adresseDestinataire = 'philippe_hvp@hotmail.com';

		// Fabrication de la fin de chaîne (différente selon les serveurs de mail)
		if(!preg_match('#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#', $adresseDestinataire))
			$retourLigne = "\r\n";
		else
			$retourLigne = "\n";


		$message_txt = $message;
		$message_html = '	<html>
								<head>
									<title>' . $titre . '</title>
									<style type="text/css"></style>
								</head>

								<body>
									<p>' . $message . '</p>
								</body>
							</html>';

		// Frontière entre les éléments du mail
		$frontiere = "-----=".md5(rand());


		// Sujet du mail
		$sujetCourrier = $titre;
		$sujetCourrier = '=?utf-8?B?' . base64_encode($sujetCourrier) . '?=';

		// En-tête du mail
		$enteteCourrier = 'From: "Surveillance du Poulpe d\'Or" <surveillance-direct@lepoulpedor.com>' . $retourLigne;
		$enteteCourrier .= 'Reply-to: "Surveillance du Poulpe d\'Or" <surveillance-direct@lepoulpedor.com>' . $retourLigne;
		$enteteCourrier .= 'MIME-Version: 1.0' . $retourLigne;
		$enteteCourrier .= 'X-Priority: 3' . $retourLigne;
		$enteteCourrier .= 'Content-Type: multipart/alternative;' . $retourLigne . ' boundary="' . $frontiere . '"' . $retourLigne;

		// Message du mail
		$messageCourrier = $retourLigne . '--' . $frontiere . $retourLigne;

		$messageCourrier .= 'Content-Type: text/plain; charset="UTF-8"' . $retourLigne;
		$messageCourrier .= 'Content-Transfer-Encoding: 8bit' . $retourLigne;
		$messageCourrier .= $retourLigne . $message_txt . $retourLigne;

		$messageCourrier .= $retourLigne . '--' . $frontiere . $retourLigne;

		$messageCourrier .= 'Content-Type: text/html; charset="UTF-8"' . $retourLigne;
		$messageCourrier .= 'Content-Transfer-Encoding: 8bit' . $retourLigne;
		$messageCourrier .= $retourLigne . $message_html . $retourLigne;

		$messageCourrier .= $retourLigne . '--' . $frontiere . '--' . $retourLigne;
		$messageCourrier .= $retourLigne . '--' . $frontiere . '--' . $retourLigne;

		mail($adresseDestinataire, $sujetCourrier, $messageCourrier, $enteteCourrier);
	}


?>
