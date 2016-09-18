class Forecaster {
	Pronostiqueur: number;
	Pronostiqueurs_NomUtilisateur: string;
	Pronostiqueurs_Nom: string;
	Pronostiqueurs_Prenom: string;
	Pronostiqueurs_Photo: string;
	Pronostiqueurs_Administrateur: number;
	Pronostiqueurs_MEL: string;
	Pronostiqueurs_MotDePasse: string;
	Pronostiqueurs_PremiereConnexion: number;
	Pronostiqueurs_DateDeNaissance: Date;
	Pronostiqueurs_DateDebutPresence: Date;
	Pronostiqueurs_DateFinPresence: Date;
	Pronostiqueurs_LieuDeResidence: string;
	Pronostiqueurs_Ambitions: string;
	Pronostiqueurs_Palmares: string;
	Pronostiqueurs_Carriere: string;
	Pronostiqueurs_Commentaire: string;
	Pronostiqueurs_EquipeFavorite: string;
	Pronostiqueurs_CodeCouleur: string;
	Themes_Theme: number;

	constructor() {
		this.initFields();
	}

	initFields() {
		this.Pronostiqueurs_NomUtilisateur = '';
		this.Pronostiqueurs_Nom = '';
		this.Pronostiqueurs_Prenom = '';
		this.Pronostiqueurs_Photo = '';
		this.Pronostiqueurs_Administrateur = 0;
		this.Pronostiqueurs_MEL = '';
		this.Pronostiqueurs_MotDePasse = '';
		this.Pronostiqueurs_PremiereConnexion = 1;
		this.Pronostiqueurs_DateDeNaissance = null;
		this.Pronostiqueurs_DateDebutPresence = null;
		this.Pronostiqueurs_DateFinPresence = null;
		this.Pronostiqueurs_LieuDeResidence = '';
		this.Pronostiqueurs_Ambitions = '';
		this.Pronostiqueurs_Palmares = '';
		this.Pronostiqueurs_Carriere = '';
		this.Pronostiqueurs_Commentaire = '';
		this.Pronostiqueurs_EquipeFavorite = '';
		this.Pronostiqueurs_CodeCouleur = '';
		this.Themes_Theme = 1;
	}
}
