/// <reference path="angular.d.ts" />


namespace Application.Services {
    export class TrophiesService {
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
                    error.status ? `${error.status} - ${error.statusText}` : "trophies-service getSeasons: Server error";
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
                    error.status ? `${error.status} - ${error.statusText}` : "trophies-service getChampionships: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Get all trophies */
        getTrophies(season: Season, championship: Championship): any {
            let url = "./dist/trophies.php";

            return this.http({
                method: "POST",
                url: url,
                data: JSON.stringify({ saison: season.Saison, championnat: championship.Championnat })
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "trophies-service getTrophies: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }
    }
}
