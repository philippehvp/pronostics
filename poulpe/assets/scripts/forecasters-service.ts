/// <reference path="angular.d.ts" />


module Application.Services {
    export class ForecastersService {
        private http: any;

        constructor($http: ng.IHttpService) {
            this.http = $http;
        }


        /* Get all forecasters */
        getForecasters(): any {
            let url = "./dist/forecasters.php";

            return this.http({
                method: "POST",
                url: url
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "forecasters-service getForecasters: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Create a new forecaster */
        createForecaster(forecaster: any): any {
            let url = "./dist/create-forecaster.php";

            let data = JSON.stringify(forecaster);

            return this.http({
                method: "POST",
                url: url,
                data: { data: data }
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "forecasters-service createForecaster: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Update forecaster */
        updateForecaster(forecaster: any): any {
            let url = "./dist/update-forecaster.php";

            let data = JSON.stringify(forecaster);

            return this.http({
                method: "POST",
                url: url,
                data: { data: data }
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "forecasters-service updateForecaster: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Move forecaster to the old forecasters list */
        moveForecaster(forecaster: any): any {
            let url = "./dist/move-forecaster.php";

            let data = JSON.stringify(forecaster);

            return this.http({
                method: "POST",
                url: url,
                data: { data: data }
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "forecasters-service moveForecaster: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }

        /* Delete forecaster */
        deleteForecaster(forecaster: any): any {
            let url = "./dist/delete-forecaster.php";

            let data = JSON.stringify(forecaster);

            return this.http({
                method: "POST",
                url: url,
                data: { data: data }
            }).then(function successCallback(response) {
                return response.data || {};
            }, function errorCallback(error) {
                let errMsg = (error.message) ? error.message :
                    error.status ? `${error.status} - ${error.statusText}` : "forecasters-service deleteForecaster: Server error";
                console.error(errMsg); // log to console instead
                return [];
            });
        }


    }
}
