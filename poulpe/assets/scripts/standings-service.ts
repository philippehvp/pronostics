/// <reference path="angular.d.ts" />


namespace Application.Services {
    export class StandingsService {
        private http: any;

        constructor($http: ng.IHttpService) {
            this.http = $http;
        }

        /* Get all seasons */
        getSeasons(): any {
            let url = "./dist/seasons.php";

            return this.http({
                method: "POST",
                url: url
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "standings-service getSeasons: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Get all championships */
        getChampionships(season: Season): any {
            let url = "./dist/championships.php";

            return this.http({
                method: "POST",
                url: url,
                data: { saison: season }
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "standings-service getChampionships: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Get all weeks */
        getWeeks(season: Season, championship: Championship): any {
            let url = "./dist/weeks.php";

            return this.http({
                method: "POST",
                url: url,
                data: { saison: season.Saison, championnat: championship.Championnat }
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "standings-service getWeeks: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Get general standings for a week */
        getStandings(season: Season, week: Week, referenceDate: string): any {
            let url = "./dist/standings.php";

            return this.http({
                method: "POST",
                url: url,
                data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "standings-service getStandings: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Get week standings */
        getStandingsWeek(season: Season, week: Week, referenceDate: string): any {
            let url = "./dist/standings-week.php";

            return this.http({
                method: "POST",
                url: url,
                data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "standings-service getStandingsWeek: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Get goal standings */
        getStandingsGoal(season: Season, week: Week, referenceDate: string): any {
            let url = "./dist/standings-goal.php";

            return this.http({
                method: "POST",
                url: url,
                data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "standings-service getStandingsGoal: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }
    }
}
