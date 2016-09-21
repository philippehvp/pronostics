/// <reference path="angular.d.ts" />
/// <reference path="models/season.ts" />
/// <reference path="models/championship.ts" />
/// <reference path="models/trophy.ts" />


namespace Application.Controllers {
    export class TrophiesController {
        private seasons: any[];
        private championships: any[];
        private trophies: any[];

        private currentSeason: Season;
        private currentChampionship: Championship;

        private service: any;

        constructor(trophiesService: any) {
            this.service = trophiesService;
        }

        $onInit() {
            // Get all seasons
            this.service.getSeasons().then((seasons) => {
                this.seasons = seasons;
            }, (err) => {
                console.log("TrophiesController $onInit(): Error during reading seasons");
            });
        }

        /* Select a season */
        selectSeason(season: Season): void {
            if (this.currentSeason === season)
                return;

            this.currentSeason = season;
            this.championships = [];
            this.trophies = [];

            // Get all existing championships except the French Cup for the selected season
            this.service.getChampionships(this.currentSeason).then((championships) => {
                this.championships = championships;
            }, (err) => {
                console.log("TrophiesController selectSeason(): Error during reading championships");
            });
        }

        /* Select a championship */
        selectChampionship(championship: Championship): void {
            this.currentChampionship = championship;

            /* Get all trophies */
            this.service.getTrophies(this.currentSeason, this.currentChampionship).then((trophies) => {
                this.trophies = trophies;
            }, (err) => {
                console.log("TrophiesController $onInit(): Error during reading trophies");
            });
        }
    }
}
