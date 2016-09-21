/// <reference path="angular.d.ts" />
/// <reference path="models/season.ts" />
/// <reference path="models/championship.ts" />
/// <reference path="models/week.ts" />
/// <reference path="models/standing.ts" />


namespace Application.Controllers {
    export class StandingsController {
        private seasons: any[];
        private championships: any[];
        private weeks: any[];
        private standings: any[];
        private standingsWeek: any[];
        private standingsGoal: any[];


        private currentSeason: Season;
        private currentChampionship: Championship;
        private currentWeek: Week;
        private currentReferenceDate: any;

        private service: any;

        constructor(standingsService: any) {
            this.service = standingsService;
        }

        $onInit() {
            // Get all seasons
            this.service.getSeasons().then((seasons) => {
                this.seasons = seasons;
            }, (err) => {
                console.log("StandingsController $onInit(): Error during reading seasons");
            });
        }

        /* Select a season */
        selectSeason(season: Season): void {
            if (this.currentSeason === season)
                return;

            this.currentSeason = season;

            this.championships = [];
            this.weeks = [];
            this.standings = [];

            // Get all existing championships except the French Cup for the selected season
            this.service.getChampionships(this.currentSeason).then((championships) => {
                this.championships = championships;
            }, (err) => {
                console.log("StandingsController selectSeason(): Error during reading championships");
            });
        }

        /* Select a championship */
        selectChampionship(championship: Championship): void {
            this.currentChampionship = championship;

            /* Select all weeks for that championship */
            this.service.getWeeks(this.currentSeason, this.currentChampionship).then((weeks) => {
                this.weeks = weeks;
                this.standings = [];
            }, (err) => {
                console.log("StandingsController selectChampionship(): Error during reading weeks");
            });
        }

        /* Select a week and a reference date */
        selectWeek(week: Week, referenceDate: string): any {
            this.currentWeek = week;
            this.currentReferenceDate = referenceDate;

            this.service.getStandings(this.currentSeason, this.currentWeek, this.currentReferenceDate).then((standings) => {
                this.standings = standings;
            }, (err) => {
                console.log("StandingsController $onInit(): Error during reading standings");
            });

            this.service.getStandingsWeek(this.currentSeason, this.currentWeek, this.currentReferenceDate).then((standingsWeek) => {
                this.standingsWeek = standingsWeek;
            }, (err) => {
                console.log("StandingsController $onInit(): Error during reading standings week");
            });

            this.service.getStandingsGoal(this.currentSeason, this.currentWeek, this.currentReferenceDate).then((standingsGoal) => {
                this.standingsGoal = standingsGoal;
            }, (err) => {
                console.log("StandingsController $onInit(): Error during reading standings goal");
            });
        }
    }
}
