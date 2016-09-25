var Application;
(function (Application) {
    var Directives;
    (function (Directives) {
        var Navbar = (function () {
            function Navbar() {
                return this.createDirective();
            }
            Navbar.prototype.createDirective = function () {
                return {
                    restrict: "E",
                    templateUrl: "./dist/navbar.html",
                    scope: {}
                };
            };
            return Navbar;
        }());
        Directives.Navbar = Navbar;
    })(Directives = Application.Directives || (Application.Directives = {}));
})(Application || (Application = {}));
/// <reference path="angular.d.ts" />
var Application;
(function (Application) {
    var Controllers;
    (function (Controllers) {
        var HomeController = (function () {
            function HomeController($timeout, $interval) {
            }
            return HomeController;
        }());
        Controllers.HomeController = HomeController;
    })(Controllers = Application.Controllers || (Application.Controllers = {}));
})(Application || (Application = {}));
var Forecaster = (function () {
    function Forecaster() {
        this.initFields();
    }
    Forecaster.prototype.initFields = function () {
        this.Pronostiqueurs_NomUtilisateur = "";
        this.Pronostiqueurs_Nom = "";
        this.Pronostiqueurs_Prenom = "";
        this.Pronostiqueurs_Photo = "";
        this.Pronostiqueurs_Administrateur = 0;
        this.Pronostiqueurs_MEL = "";
        this.Pronostiqueurs_MotDePasse = "";
        this.Pronostiqueurs_PremiereConnexion = 1;
        this.Pronostiqueurs_DateDeNaissance = null;
        this.Pronostiqueurs_DateDebutPresence = null;
        this.Pronostiqueurs_DateFinPresence = null;
        this.Pronostiqueurs_LieuDeResidence = "";
        this.Pronostiqueurs_Ambitions = "";
        this.Pronostiqueurs_Palmares = "";
        this.Pronostiqueurs_Carriere = "";
        this.Pronostiqueurs_Commentaire = "";
        this.Pronostiqueurs_EquipeFavorite = "";
        this.Pronostiqueurs_CodeCouleur = "";
        this.Themes_Theme = 1;
    };
    return Forecaster;
}());
/// <reference path="angular.d.ts" />
/// <reference path="models/forecaster.ts" />
var Application;
(function (Application) {
    var Controllers;
    (function (Controllers) {
        var ForecastersController = (function () {
            function ForecastersController(forecastersService) {
                this.birthdayCalendar = {
                    opened: false
                };
                this.beginDateCalendar = {
                    opened: false
                };
                this.endDateCalendar = {
                    opened: false
                };
                this.dateOptions = {
                    formatYear: "yyyy",
                    maxDate: new Date(2020, 5, 22),
                    minDate: new Date(1920, 1, 1),
                    startingDay: 1
                };
                this.service = forecastersService;
                this.hasBeenModified = false;
                this.isMovable = false;
                this.isDeletable = false;
                this.isInCreationMode = false;
            }
            ForecastersController.prototype.$onInit = function () {
                var _this = this;
                this.service.getForecasters().then(function (forecasters) {
                    _this.forecasters = forecasters;
                }, function (err) {
                    console.log("ForecastersController $onInit(): Error during reading");
                });
            };
            /* Load the forecaster to the edit form */
            ForecastersController.prototype.editForecaster = function (forecaster, index) {
                this.currentForecaster = angular.copy(forecaster);
                this.currentForecasterIndex = index;
                this.isMovable = true;
                this.isDeletable = true;
                /* Reformat the SQL date to Javascript date */
                if (this.currentForecaster.Pronostiqueurs_DateDeNaissance !== null)
                    if (this.currentForecaster.Pronostiqueurs_DateDeNaissance.toString() !== "0/0/0")
                        this.currentForecaster.Pronostiqueurs_DateDeNaissance = new Date(this.currentForecaster.Pronostiqueurs_DateDeNaissance.toString());
                /* Reformat the SQL date to Javascript date */
                if (this.currentForecaster.Pronostiqueurs_DateDebutPresence !== null)
                    if (this.currentForecaster.Pronostiqueurs_DateDebutPresence.toString() !== "0/0/0")
                        this.currentForecaster.Pronostiqueurs_DateDebutPresence = new Date(this.currentForecaster.Pronostiqueurs_DateDebutPresence.toString());
                /* Reformat the SQL date to Javascript date */
                if (this.currentForecaster.Pronostiqueurs_DateFinPresence !== null)
                    if (this.currentForecaster.Pronostiqueurs_DateFinPresence.toString() !== "0/0/0")
                        this.currentForecaster.Pronostiqueurs_DateFinPresence = new Date(this.currentForecaster.Pronostiqueurs_DateFinPresence.toString());
            };
            /* Open the UI date picker dialog */
            ForecastersController.prototype.openBirthdayCalendar = function () {
                if (this.currentForecaster.Pronostiqueurs_DateDeNaissance === null || this.currentForecaster.Pronostiqueurs_DateDeNaissance.toString() === "0/0/0")
                    this.currentForecaster.Pronostiqueurs_DateDeNaissance = new Date();
                this.birthdayCalendar.opened = true;
            };
            /* Open the UI date picker dialog */
            ForecastersController.prototype.openBeginDateCalendar = function () {
                if (this.currentForecaster.Pronostiqueurs_DateDebutPresence === null || this.currentForecaster.Pronostiqueurs_DateDebutPresence.toString() === "0/0/0")
                    this.currentForecaster.Pronostiqueurs_DateDebutPresence = new Date();
                this.beginDateCalendar.opened = true;
            };
            /* Open the UI date picker dialog */
            ForecastersController.prototype.openEndDateCalendar = function () {
                if (this.currentForecaster.Pronostiqueurs_DateFinPresence === null || this.currentForecaster.Pronostiqueurs_DateFinPresence.toString() === "0/0/0")
                    this.currentForecaster.Pronostiqueurs_DateFinPresence = new Date();
                this.endDateCalendar.opened = true;
            };
            /* Add a new forecaster or save the modifications made on an existing forecaster */
            ForecastersController.prototype.saveModifications = function () {
                if (this.isInCreationMode === true) {
                    this.hasBeenModified = false;
                    this.isInCreationMode = false;
                    this.forecasters.push(this.currentForecaster);
                    this.service.createForecaster(this.currentForecaster).then(function (data) {
                    }, function (err) {
                        console.log("Error during creation");
                    });
                }
                else {
                    this.forecasters[this.currentForecasterIndex] = angular.copy(this.currentForecaster);
                    this.hasBeenModified = false;
                    this.service.updateForecaster(this.currentForecaster).then(function (data) {
                    }, function (err) {
                        console.log("Error during update");
                    });
                }
            };
            /* Cancel the creation of a new forecaster or the modifications made on an existing forecaster */
            ForecastersController.prototype.cancelModifications = function () {
                if (this.isInCreationMode === true) {
                    // In creation mode, the data doesn"t come from the forecasters array
                    this.currentForecaster.initFields();
                }
                else {
                    this.currentForecaster = angular.copy(this.forecasters[this.currentForecasterIndex]);
                    if (this.currentForecaster.Pronostiqueurs_DateDeNaissance !== null)
                        this.currentForecaster.Pronostiqueurs_DateDeNaissance = new Date(this.currentForecaster.Pronostiqueurs_DateDeNaissance.toString());
                    if (this.currentForecaster.Pronostiqueurs_DateDebutPresence !== null)
                        this.currentForecaster.Pronostiqueurs_DateDebutPresence = new Date(this.currentForecaster.Pronostiqueurs_DateDebutPresence.toString());
                    if (this.currentForecaster.Pronostiqueurs_DateFinPresence !== null)
                        this.currentForecaster.Pronostiqueurs_DateFinPresence = new Date(this.currentForecaster.Pronostiqueurs_DateFinPresence.toString());
                }
                this.hasBeenModified = false;
                this.isInCreationMode = false;
            };
            /* Indicates that a modification has been made */
            ForecastersController.prototype.setModifiedOn = function () {
                if (this.isInCreationMode === false)
                    this.hasBeenModified = true;
            };
            /* Indicates that a modification has been made on birthday */
            /* It"s more complex because it"s necessary to think about the UIB datepicker widget*/
            ForecastersController.prototype.checkBirthdayIsModified = function () {
                if (this.isInCreationMode === false) {
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance !== "0/0/0" && this.currentForecaster.Pronostiqueurs_DateDeNaissance !== this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance)
                        this.hasBeenModified = true;
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance === "0/0/0" && this.currentForecaster.Pronostiqueurs_DateDeNaissance !== null)
                        this.hasBeenModified = true;
                }
            };
            /* Indicates that a modification has been made on begin date */
            /* It"s more complex because it"s necessary to think about the UIB datepicker widget*/
            ForecastersController.prototype.checkBeginDateIsModified = function () {
                if (this.isInCreationMode === false) {
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence !== "0/0/0" && this.currentForecaster.Pronostiqueurs_DateDebutPresence !== this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence)
                        this.hasBeenModified = true;
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence === "0/0/0" && this.currentForecaster.Pronostiqueurs_DateDebutPresence !== null)
                        this.hasBeenModified = true;
                }
            };
            /* Indicates that a modification has been made on end date */
            /* It"s more complex because it"s necessary to think about the UIB datepicker widget*/
            ForecastersController.prototype.checkEndDateIsModified = function () {
                if (this.isInCreationMode === false) {
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence !== "0/0/0" && this.currentForecaster.Pronostiqueurs_DateFinPresence !== this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence)
                        this.hasBeenModified = true;
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence === "0/0/0" && this.currentForecaster.Pronostiqueurs_DateFinPresence !== null)
                        this.hasBeenModified = true;
                }
            };
            /* Create a new forecaster */
            ForecastersController.prototype.createForecaster = function () {
                this.currentForecaster = new Forecaster();
                this.hasBeenModified = true;
                this.isMovable = false;
                this.isDeletable = false;
                this.isInCreationMode = true;
            };
            /* Move a forecaster to the previous forecasters list */
            ForecastersController.prototype.moveForecaster = function () {
                var _this = this;
                this.service.moveForecaster(this.currentForecaster).then(function (data) {
                    _this.forecasters.splice(_this.currentForecasterIndex, 1);
                    _this.currentForecasterIndex = null;
                    _this.currentForecaster = null;
                    _this.isMovable = false;
                    _this.isDeletable = false;
                }, function (err) {
                    console.log("Error during move");
                });
            };
            /* Move a forecaster to the previous forecasters list */
            ForecastersController.prototype.deleteForecaster = function () {
                var _this = this;
                this.service.deleteForecaster(this.currentForecaster).then(function (data) {
                    _this.forecasters.splice(_this.currentForecasterIndex, 1);
                    _this.currentForecasterIndex = null;
                    _this.currentForecaster = null;
                    _this.isMovable = false;
                    _this.isDeletable = false;
                }, function (err) {
                    console.log("Error during delete");
                });
            };
            return ForecastersController;
        }());
        Controllers.ForecastersController = ForecastersController;
    })(Controllers = Application.Controllers || (Application.Controllers = {}));
})(Application || (Application = {}));
var Season = (function () {
    function Season() {
        this.initFields();
    }
    Season.prototype.initFields = function () {
    };
    return Season;
}());
var Championship = (function () {
    function Championship() {
        this.initFields();
    }
    Championship.prototype.initFields = function () {
    };
    return Championship;
}());
var Week = (function () {
    function Week() {
        this.initFields();
    }
    Week.prototype.initFields = function () {
    };
    return Week;
}());
var Standing = (function () {
    function Standing() {
    }
    return Standing;
}());
var StandingWeek = (function () {
    function StandingWeek() {
    }
    return StandingWeek;
}());
var StandingGoal = (function () {
    function StandingGoal() {
    }
    return StandingGoal;
}());
/// <reference path="angular.d.ts" />
/// <reference path="models/season.ts" />
/// <reference path="models/championship.ts" />
/// <reference path="models/week.ts" />
/// <reference path="models/standing.ts" />
var Application;
(function (Application) {
    var Controllers;
    (function (Controllers) {
        var StandingsController = (function () {
            function StandingsController(standingsService) {
                this.service = standingsService;
            }
            StandingsController.prototype.$onInit = function () {
                var _this = this;
                // Get all seasons
                this.service.getSeasons().then(function (seasons) {
                    _this.seasons = seasons;
                }, function (err) {
                    console.log("StandingsController $onInit(): Error during reading seasons");
                });
            };
            /* Select a season */
            StandingsController.prototype.selectSeason = function (season) {
                var _this = this;
                if (this.currentSeason === season)
                    return;
                this.currentSeason = season;
                this.championships = [];
                this.weeks = [];
                this.standings = [];
                // Get all existing championships except the French Cup for the selected season
                this.service.getChampionships(this.currentSeason).then(function (championships) {
                    _this.championships = championships;
                }, function (err) {
                    console.log("StandingsController selectSeason(): Error during reading championships");
                });
            };
            /* Select a championship */
            StandingsController.prototype.selectChampionship = function (championship) {
                var _this = this;
                this.currentChampionship = championship;
                /* Select all weeks for that championship */
                this.service.getWeeks(this.currentSeason, this.currentChampionship).then(function (weeks) {
                    _this.weeks = weeks;
                    _this.standings = [];
                }, function (err) {
                    console.log("StandingsController selectChampionship(): Error during reading weeks");
                });
            };
            /* Select a week and a reference date */
            StandingsController.prototype.selectWeek = function (week, referenceDate) {
                var _this = this;
                this.currentWeek = week;
                this.currentReferenceDate = referenceDate;
                this.service.getStandings(this.currentSeason, this.currentWeek, this.currentReferenceDate).then(function (standings) {
                    _this.standings = standings;
                }, function (err) {
                    console.log("StandingsController $onInit(): Error during reading standings");
                });
                this.service.getStandingsWeek(this.currentSeason, this.currentWeek, this.currentReferenceDate).then(function (standingsWeek) {
                    _this.standingsWeek = standingsWeek;
                }, function (err) {
                    console.log("StandingsController $onInit(): Error during reading standings week");
                });
                this.service.getStandingsGoal(this.currentSeason, this.currentWeek, this.currentReferenceDate).then(function (standingsGoal) {
                    _this.standingsGoal = standingsGoal;
                }, function (err) {
                    console.log("StandingsController $onInit(): Error during reading standings goal");
                });
            };
            return StandingsController;
        }());
        Controllers.StandingsController = StandingsController;
    })(Controllers = Application.Controllers || (Application.Controllers = {}));
})(Application || (Application = {}));
var Trophy = (function () {
    function Trophy() {
    }
    return Trophy;
}());
/// <reference path="angular.d.ts" />
/// <reference path="models/season.ts" />
/// <reference path="models/championship.ts" />
/// <reference path="models/trophy.ts" />
var Application;
(function (Application) {
    var Controllers;
    (function (Controllers) {
        var TrophiesController = (function () {
            function TrophiesController(trophiesService) {
                this.service = trophiesService;
            }
            TrophiesController.prototype.$onInit = function () {
                var _this = this;
                // Get all seasons
                this.service.getSeasons().then(function (seasons) {
                    _this.seasons = seasons;
                }, function (err) {
                    console.log("TrophiesController $onInit(): Error during reading seasons");
                });
            };
            /* Select a season */
            TrophiesController.prototype.selectSeason = function (season) {
                var _this = this;
                if (this.currentSeason === season)
                    return;
                this.currentSeason = season;
                this.championships = [];
                this.trophies = [];
                // Get all existing championships except the French Cup for the selected season
                this.service.getChampionships(this.currentSeason).then(function (championships) {
                    _this.championships = championships;
                }, function (err) {
                    console.log("TrophiesController selectSeason(): Error during reading championships");
                });
            };
            /* Select a championship */
            TrophiesController.prototype.selectChampionship = function (championship) {
                var _this = this;
                this.currentChampionship = championship;
                /* Get all trophies */
                this.service.getTrophies(this.currentSeason, this.currentChampionship).then(function (trophies) {
                    _this.trophies = trophies;
                }, function (err) {
                    console.log("TrophiesController $onInit(): Error during reading trophies");
                });
            };
            return TrophiesController;
        }());
        Controllers.TrophiesController = TrophiesController;
    })(Controllers = Application.Controllers || (Application.Controllers = {}));
})(Application || (Application = {}));
/// <reference path="angular.d.ts" />
var Application;
(function (Application) {
    var Services;
    (function (Services) {
        var ForecastersService = (function () {
            function ForecastersService($http) {
                this.http = $http;
            }
            /* Get all forecasters */
            ForecastersService.prototype.getForecasters = function () {
                var url = "./dist/forecasters.php";
                return this.http({
                    method: "POST",
                    url: url
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "forecasters-service getForecasters: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Create a new forecaster */
            ForecastersService.prototype.createForecaster = function (forecaster) {
                var url = "./dist/create-forecaster.php";
                var data = JSON.stringify(forecaster);
                return this.http({
                    method: "POST",
                    url: url,
                    data: { data: data }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "forecasters-service createForecaster: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Update forecaster */
            ForecastersService.prototype.updateForecaster = function (forecaster) {
                var url = "./dist/update-forecaster.php";
                var data = JSON.stringify(forecaster);
                return this.http({
                    method: "POST",
                    url: url,
                    data: { data: data }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "forecasters-service updateForecaster: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Move forecaster to the old forecasters list */
            ForecastersService.prototype.moveForecaster = function (forecaster) {
                var url = "./dist/move-forecaster.php";
                var data = JSON.stringify(forecaster);
                return this.http({
                    method: "POST",
                    url: url,
                    data: { data: data }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "forecasters-service moveForecaster: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Delete forecaster */
            ForecastersService.prototype.deleteForecaster = function (forecaster) {
                var url = "./dist/delete-forecaster.php";
                var data = JSON.stringify(forecaster);
                return this.http({
                    method: "POST",
                    url: url,
                    data: { data: data }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "forecasters-service deleteForecaster: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            return ForecastersService;
        }());
        Services.ForecastersService = ForecastersService;
    })(Services = Application.Services || (Application.Services = {}));
})(Application || (Application = {}));
/// <reference path="angular.d.ts" />
var Application;
(function (Application) {
    var Services;
    (function (Services) {
        var StandingsService = (function () {
            function StandingsService($http) {
                this.http = $http;
            }
            /* Get all seasons */
            StandingsService.prototype.getSeasons = function () {
                var url = "./dist/seasons.php";
                return this.http({
                    method: "POST",
                    url: url
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "standings-service getSeasons: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get all championships */
            StandingsService.prototype.getChampionships = function (season) {
                var url = "./dist/championships.php";
                return this.http({
                    method: "POST",
                    url: url,
                    data: { saison: season }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "standings-service getChampionships: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get all weeks */
            StandingsService.prototype.getWeeks = function (season, championship) {
                var url = "./dist/weeks.php";
                return this.http({
                    method: "POST",
                    url: url,
                    data: { saison: season.Saison, championnat: championship.Championnat }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "standings-service getWeeks: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get general standings for a week */
            StandingsService.prototype.getStandings = function (season, week, referenceDate) {
                var url = "./dist/standings.php";
                return this.http({
                    method: "POST",
                    url: url,
                    data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "standings-service getStandings: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get week standings */
            StandingsService.prototype.getStandingsWeek = function (season, week, referenceDate) {
                var url = "./dist/standings-week.php";
                return this.http({
                    method: "POST",
                    url: url,
                    data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "standings-service getStandingsWeek: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get goal standings */
            StandingsService.prototype.getStandingsGoal = function (season, week, referenceDate) {
                var url = "./dist/standings-goal.php";
                return this.http({
                    method: "POST",
                    url: url,
                    data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "standings-service getStandingsGoal: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            return StandingsService;
        }());
        Services.StandingsService = StandingsService;
    })(Services = Application.Services || (Application.Services = {}));
})(Application || (Application = {}));
/// <reference path="angular.d.ts" />
var Application;
(function (Application) {
    var Services;
    (function (Services) {
        var TrophiesService = (function () {
            function TrophiesService($http) {
                this.http = $http;
            }
            /* Get all seasons */
            TrophiesService.prototype.getSeasons = function () {
                var url = "./dist/seasons.php";
                return this.http({
                    method: "POST",
                    url: url
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "trophies-service getSeasons: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get all championships */
            TrophiesService.prototype.getChampionships = function (season) {
                var url = "./dist/championships.php";
                return this.http({
                    method: "POST",
                    url: url,
                    data: { saison: season }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "trophies-service getChampionships: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get all trophies */
            TrophiesService.prototype.getTrophies = function (season, championship) {
                var url = "./dist/trophies.php";
                return this.http({
                    method: "POST",
                    url: url,
                    data: JSON.stringify({ saison: season.Saison, championnat: championship.Championnat })
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : "trophies-service getTrophies: Server error";
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            return TrophiesService;
        }());
        Services.TrophiesService = TrophiesService;
    })(Services = Application.Services || (Application.Services = {}));
})(Application || (Application = {}));
/// <reference path="angular.d.ts" />
/// <reference path="angular-route.d.ts" />
/// <reference path="navbar-directive.ts" />
/// <reference path="home-controller.ts" />
/// <reference path="forecasters-controller.ts" />
/// <reference path="standings-controller.ts" />
/// <reference path="trophies-controller.ts" />
/// <reference path="forecasters-service.ts" />
/// <reference path="standings-service.ts" />
/// <reference path="trophies-service.ts" />
var appModule = angular.module("poulpeApp", ["ngAnimate", "ui.router", "ui.bootstrap", "ui.layout"]);
appModule.service("forecastersService", ["$http", function ($http) { return new Application.Services.ForecastersService($http); }]);
appModule.service("standingsService", ["$http", function ($http) { return new Application.Services.StandingsService($http); }]);
appModule.service("trophiesService", ["$http", function ($http) { return new Application.Services.TrophiesService($http); }]);
appModule.controller("HomeController", ["$timeout", "$interval", function ($timeout, $interval) { return new Application.Controllers.HomeController($timeout, $interval); }]);
appModule.controller("ForecastersController", ["forecastersService", function (forecastersService) { return new Application.Controllers.ForecastersController(forecastersService); }]);
appModule.controller("StandingsController", ["standingsService", function (standingsService) {
        return new Application.Controllers.StandingsController(standingsService);
    }]);
appModule.controller("TrophiesController", ["trophiesService", function (trophiesService) {
        return new Application.Controllers.TrophiesController(trophiesService);
    }]);
appModule.directive("navbar", function () { return new Application.Directives.Navbar(); });
appModule.component("forecastersComponent", {
    bindings: {},
    controller: "ForecastersController as ctrl",
    templateUrl: "./dist/forecasters.html"
});
appModule.component("standingsComponent", {
    bindings: {},
    controller: "StandingsController as ctrl",
    templateUrl: "./dist/standings.html"
});
appModule.component("trophiesComponent", {
    bindings: {},
    controller: "TrophiesController as ctrl",
    templateUrl: "./dist/trophies.html"
});
appModule.config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/home");
        $stateProvider
            .state("home", {
            url: "/home",
            template: "",
            controller: "HomeController"
        })
            .state("forecasters", {
            url: "/forecasters",
            template: "<forecasters-component></forecasters-component>",
            controller: "ForecastersController"
        })
            .state("standings", {
            url: "/standings",
            template: "<standings-component></standings-component>",
            controller: "StandingsController"
        })
            .state("trophies", {
            url: "/trophies",
            template: "<trophies-component></trophies-component>",
            controller: "TrophiesController"
        });
    }]);

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5hdmJhci1kaXJlY3RpdmUudHMiLCJob21lLWNvbnRyb2xsZXIudHMiLCJtb2RlbHMvZm9yZWNhc3Rlci50cyIsImZvcmVjYXN0ZXJzLWNvbnRyb2xsZXIudHMiLCJtb2RlbHMvc2Vhc29uLnRzIiwibW9kZWxzL2NoYW1waW9uc2hpcC50cyIsIm1vZGVscy93ZWVrLnRzIiwibW9kZWxzL3N0YW5kaW5nLnRzIiwic3RhbmRpbmdzLWNvbnRyb2xsZXIudHMiLCJtb2RlbHMvdHJvcGh5LnRzIiwidHJvcGhpZXMtY29udHJvbGxlci50cyIsImZvcmVjYXN0ZXJzLXNlcnZpY2UudHMiLCJzdGFuZGluZ3Mtc2VydmljZS50cyIsInRyb3BoaWVzLXNlcnZpY2UudHMiLCJhcHAudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUEsSUFBVSxXQUFXLENBaUJwQjtBQWpCRCxXQUFVLFdBQVc7SUFBQyxJQUFBLFVBQVUsQ0FpQi9CO0lBakJxQixXQUFBLFVBQVUsRUFBQyxDQUFDO1FBRTlCO1lBRUk7Z0JBQ0ksTUFBTSxDQUFDLElBQUksQ0FBQyxlQUFlLEVBQUUsQ0FBQztZQUNsQyxDQUFDO1lBRU8sZ0NBQWUsR0FBdkI7Z0JBQ0ksTUFBTSxDQUFDO29CQUNILFFBQVEsRUFBRSxHQUFHO29CQUNiLFdBQVcsRUFBRSxvQkFBb0I7b0JBQ2pDLEtBQUssRUFBRSxFQUNOO2lCQUNKLENBQUM7WUFDTixDQUFDO1lBQ0wsYUFBQztRQUFELENBZEEsQUFjQyxJQUFBO1FBZFksaUJBQU0sU0FjbEIsQ0FBQTtJQUNMLENBQUMsRUFqQnFCLFVBQVUsR0FBVixzQkFBVSxLQUFWLHNCQUFVLFFBaUIvQjtBQUFELENBQUMsRUFqQlMsV0FBVyxLQUFYLFdBQVcsUUFpQnBCO0FDakJELHFDQUFxQztBQUdyQyxJQUFVLFdBQVcsQ0FXcEI7QUFYRCxXQUFVLFdBQVc7SUFBQyxJQUFBLFdBQVcsQ0FXaEM7SUFYcUIsV0FBQSxXQUFXLEVBQUMsQ0FBQztRQUMvQjtZQU1JLHdCQUFZLFFBQTRCLEVBQUUsU0FBOEI7WUFDeEUsQ0FBQztZQUNMLHFCQUFDO1FBQUQsQ0FSQSxBQVFDLElBQUE7UUFSWSwwQkFBYyxpQkFRMUIsQ0FBQTtJQUVMLENBQUMsRUFYcUIsV0FBVyxHQUFYLHVCQUFXLEtBQVgsdUJBQVcsUUFXaEM7QUFBRCxDQUFDLEVBWFMsV0FBVyxLQUFYLFdBQVcsUUFXcEI7QUNkRDtJQXNCSTtRQUNJLElBQUksQ0FBQyxVQUFVLEVBQUUsQ0FBQztJQUN0QixDQUFDO0lBRUQsK0JBQVUsR0FBVjtRQUNJLElBQUksQ0FBQyw2QkFBNkIsR0FBRyxFQUFFLENBQUM7UUFDeEMsSUFBSSxDQUFDLGtCQUFrQixHQUFHLEVBQUUsQ0FBQztRQUM3QixJQUFJLENBQUMscUJBQXFCLEdBQUcsRUFBRSxDQUFDO1FBQ2hDLElBQUksQ0FBQyxvQkFBb0IsR0FBRyxFQUFFLENBQUM7UUFDL0IsSUFBSSxDQUFDLDZCQUE2QixHQUFHLENBQUMsQ0FBQztRQUN2QyxJQUFJLENBQUMsa0JBQWtCLEdBQUcsRUFBRSxDQUFDO1FBQzdCLElBQUksQ0FBQyx5QkFBeUIsR0FBRyxFQUFFLENBQUM7UUFDcEMsSUFBSSxDQUFDLGdDQUFnQyxHQUFHLENBQUMsQ0FBQztRQUMxQyxJQUFJLENBQUMsOEJBQThCLEdBQUcsSUFBSSxDQUFDO1FBQzNDLElBQUksQ0FBQyxnQ0FBZ0MsR0FBRyxJQUFJLENBQUM7UUFDN0MsSUFBSSxDQUFDLDhCQUE4QixHQUFHLElBQUksQ0FBQztRQUMzQyxJQUFJLENBQUMsOEJBQThCLEdBQUcsRUFBRSxDQUFDO1FBQ3pDLElBQUksQ0FBQyx3QkFBd0IsR0FBRyxFQUFFLENBQUM7UUFDbkMsSUFBSSxDQUFDLHVCQUF1QixHQUFHLEVBQUUsQ0FBQztRQUNsQyxJQUFJLENBQUMsdUJBQXVCLEdBQUcsRUFBRSxDQUFDO1FBQ2xDLElBQUksQ0FBQywwQkFBMEIsR0FBRyxFQUFFLENBQUM7UUFDckMsSUFBSSxDQUFDLDZCQUE2QixHQUFHLEVBQUUsQ0FBQztRQUN4QyxJQUFJLENBQUMsMEJBQTBCLEdBQUcsRUFBRSxDQUFDO1FBQ3JDLElBQUksQ0FBQyxZQUFZLEdBQUcsQ0FBQyxDQUFDO0lBQzFCLENBQUM7SUFDTCxpQkFBQztBQUFELENBL0NBLEFBK0NDLElBQUE7QUMvQ0QscUNBQXFDO0FBQ3JDLDZDQUE2QztBQUU3QyxJQUFVLFdBQVcsQ0F3TnBCO0FBeE5ELFdBQVUsV0FBVztJQUFDLElBQUEsV0FBVyxDQXdOaEM7SUF4TnFCLFdBQUEsV0FBVyxFQUFDLENBQUM7UUFDL0I7WUE2QkksK0JBQVksa0JBQXVCO2dCQXBCM0IscUJBQWdCLEdBQUc7b0JBQ3ZCLE1BQU0sRUFBRSxLQUFLO2lCQUNoQixDQUFDO2dCQUVNLHNCQUFpQixHQUFHO29CQUN4QixNQUFNLEVBQUUsS0FBSztpQkFDaEIsQ0FBQztnQkFFTSxvQkFBZSxHQUFHO29CQUN0QixNQUFNLEVBQUUsS0FBSztpQkFDaEIsQ0FBQztnQkFFTSxnQkFBVyxHQUFHO29CQUNsQixVQUFVLEVBQUUsTUFBTTtvQkFDbEIsT0FBTyxFQUFFLElBQUksSUFBSSxDQUFDLElBQUksRUFBRSxDQUFDLEVBQUUsRUFBRSxDQUFDO29CQUM5QixPQUFPLEVBQUUsSUFBSSxJQUFJLENBQUMsSUFBSSxFQUFFLENBQUMsRUFBRSxDQUFDLENBQUM7b0JBQzdCLFdBQVcsRUFBRSxDQUFDO2lCQUNqQixDQUFDO2dCQUlFLElBQUksQ0FBQyxPQUFPLEdBQUcsa0JBQWtCLENBQUM7Z0JBQ2xDLElBQUksQ0FBQyxlQUFlLEdBQUcsS0FBSyxDQUFDO2dCQUM3QixJQUFJLENBQUMsU0FBUyxHQUFHLEtBQUssQ0FBQztnQkFDdkIsSUFBSSxDQUFDLFdBQVcsR0FBRyxLQUFLLENBQUM7Z0JBQ3pCLElBQUksQ0FBQyxnQkFBZ0IsR0FBRyxLQUFLLENBQUM7WUFDbEMsQ0FBQztZQUVELHVDQUFPLEdBQVA7Z0JBQUEsaUJBTUM7Z0JBTEcsSUFBSSxDQUFDLE9BQU8sQ0FBQyxjQUFjLEVBQUUsQ0FBQyxJQUFJLENBQUMsVUFBQyxXQUFXO29CQUMzQyxLQUFJLENBQUMsV0FBVyxHQUFHLFdBQVcsQ0FBQztnQkFDbkMsQ0FBQyxFQUFFLFVBQUMsR0FBRztvQkFDSCxPQUFPLENBQUMsR0FBRyxDQUFDLHVEQUF1RCxDQUFDLENBQUM7Z0JBQ3pFLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUVELDBDQUEwQztZQUMxQyw4Q0FBYyxHQUFkLFVBQWUsVUFBZSxFQUFFLEtBQWE7Z0JBQ3pDLElBQUksQ0FBQyxpQkFBaUIsR0FBRyxPQUFPLENBQUMsSUFBSSxDQUFDLFVBQVUsQ0FBQyxDQUFDO2dCQUNsRCxJQUFJLENBQUMsc0JBQXNCLEdBQUcsS0FBSyxDQUFDO2dCQUNwQyxJQUFJLENBQUMsU0FBUyxHQUFHLElBQUksQ0FBQztnQkFDdEIsSUFBSSxDQUFDLFdBQVcsR0FBRyxJQUFJLENBQUM7Z0JBRXhCLDhDQUE4QztnQkFDOUMsRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixLQUFLLElBQUksQ0FBQztvQkFDL0QsRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixDQUFDLFFBQVEsRUFBRSxLQUFLLE9BQU8sQ0FBQzt3QkFDN0UsSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixHQUFHLElBQUksSUFBSSxDQUFDLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyw4QkFBOEIsQ0FBQyxRQUFRLEVBQUUsQ0FBQyxDQUFDO2dCQUUzSSw4Q0FBOEM7Z0JBQzlDLEVBQUUsQ0FBQyxDQUFDLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxnQ0FBZ0MsS0FBSyxJQUFJLENBQUM7b0JBQ2pFLEVBQUUsQ0FBQyxDQUFDLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxnQ0FBZ0MsQ0FBQyxRQUFRLEVBQUUsS0FBSyxPQUFPLENBQUM7d0JBQy9FLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxnQ0FBZ0MsR0FBRyxJQUFJLElBQUksQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsZ0NBQWdDLENBQUMsUUFBUSxFQUFFLENBQUMsQ0FBQztnQkFFL0ksOENBQThDO2dCQUM5QyxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLEtBQUssSUFBSSxDQUFDO29CQUMvRCxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLENBQUMsUUFBUSxFQUFFLEtBQUssT0FBTyxDQUFDO3dCQUM3RSxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLEdBQUcsSUFBSSxJQUFJLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixDQUFDLFFBQVEsRUFBRSxDQUFDLENBQUM7WUFDL0ksQ0FBQztZQUVELG9DQUFvQztZQUNwQyxvREFBb0IsR0FBcEI7Z0JBQ0ksRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixLQUFLLElBQUksSUFBSSxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLENBQUMsUUFBUSxFQUFFLEtBQUssT0FBTyxDQUFDO29CQUMvSSxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLEdBQUcsSUFBSSxJQUFJLEVBQUUsQ0FBQztnQkFFdkUsSUFBSSxDQUFDLGdCQUFnQixDQUFDLE1BQU0sR0FBRyxJQUFJLENBQUM7WUFDeEMsQ0FBQztZQUVELG9DQUFvQztZQUNwQyxxREFBcUIsR0FBckI7Z0JBQ0ksRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLGdDQUFnQyxLQUFLLElBQUksSUFBSSxJQUFJLENBQUMsaUJBQWlCLENBQUMsZ0NBQWdDLENBQUMsUUFBUSxFQUFFLEtBQUssT0FBTyxDQUFDO29CQUNuSixJQUFJLENBQUMsaUJBQWlCLENBQUMsZ0NBQWdDLEdBQUcsSUFBSSxJQUFJLEVBQUUsQ0FBQztnQkFFekUsSUFBSSxDQUFDLGlCQUFpQixDQUFDLE1BQU0sR0FBRyxJQUFJLENBQUM7WUFDekMsQ0FBQztZQUVELG9DQUFvQztZQUNwQyxtREFBbUIsR0FBbkI7Z0JBQ0ksRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixLQUFLLElBQUksSUFBSSxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLENBQUMsUUFBUSxFQUFFLEtBQUssT0FBTyxDQUFDO29CQUMvSSxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLEdBQUcsSUFBSSxJQUFJLEVBQUUsQ0FBQztnQkFFdkUsSUFBSSxDQUFDLGVBQWUsQ0FBQyxNQUFNLEdBQUcsSUFBSSxDQUFDO1lBQ3ZDLENBQUM7WUFFRCxtRkFBbUY7WUFDbkYsaURBQWlCLEdBQWpCO2dCQUNJLEVBQUUsQ0FBQyxDQUFDLElBQUksQ0FBQyxnQkFBZ0IsS0FBSyxJQUFJLENBQUMsQ0FBQyxDQUFDO29CQUNqQyxJQUFJLENBQUMsZUFBZSxHQUFHLEtBQUssQ0FBQztvQkFDN0IsSUFBSSxDQUFDLGdCQUFnQixHQUFHLEtBQUssQ0FBQztvQkFDOUIsSUFBSSxDQUFDLFdBQVcsQ0FBQyxJQUFJLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLENBQUM7b0JBQzlDLElBQUksQ0FBQyxPQUFPLENBQUMsZ0JBQWdCLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLENBQUMsSUFBSSxDQUFDLFVBQUMsSUFBSTtvQkFDaEUsQ0FBQyxFQUFFLFVBQUMsR0FBRzt3QkFDSCxPQUFPLENBQUMsR0FBRyxDQUFDLHVCQUF1QixDQUFDLENBQUM7b0JBQ3pDLENBQUMsQ0FBQyxDQUFDO2dCQUNQLENBQUM7Z0JBQ0QsSUFBSSxDQUFDLENBQUM7b0JBQ0YsSUFBSSxDQUFDLFdBQVcsQ0FBQyxJQUFJLENBQUMsc0JBQXNCLENBQUMsR0FBRyxPQUFPLENBQUMsSUFBSSxDQUFDLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxDQUFDO29CQUNyRixJQUFJLENBQUMsZUFBZSxHQUFHLEtBQUssQ0FBQztvQkFDN0IsSUFBSSxDQUFDLE9BQU8sQ0FBQyxnQkFBZ0IsQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsQ0FBQyxJQUFJLENBQUMsVUFBQyxJQUFJO29CQUNoRSxDQUFDLEVBQUUsVUFBQyxHQUFHO3dCQUNILE9BQU8sQ0FBQyxHQUFHLENBQUMscUJBQXFCLENBQUMsQ0FBQztvQkFDdkMsQ0FBQyxDQUFDLENBQUM7Z0JBRVAsQ0FBQztZQUNMLENBQUM7WUFFRCxpR0FBaUc7WUFDakcsbURBQW1CLEdBQW5CO2dCQUNJLEVBQUUsQ0FBQyxDQUFDLElBQUksQ0FBQyxnQkFBZ0IsS0FBSyxJQUFJLENBQUMsQ0FBQyxDQUFDO29CQUNqQyxxRUFBcUU7b0JBQ3JFLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxVQUFVLEVBQUUsQ0FBQztnQkFDeEMsQ0FBQztnQkFDRCxJQUFJLENBQUMsQ0FBQztvQkFDRixJQUFJLENBQUMsaUJBQWlCLEdBQUcsT0FBTyxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUMsV0FBVyxDQUFDLElBQUksQ0FBQyxzQkFBc0IsQ0FBQyxDQUFDLENBQUM7b0JBQ3JGLEVBQUUsQ0FBQyxDQUFDLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyw4QkFBOEIsS0FBSyxJQUFJLENBQUM7d0JBQy9ELElBQUksQ0FBQyxpQkFBaUIsQ0FBQyw4QkFBOEIsR0FBRyxJQUFJLElBQUksQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLENBQUMsUUFBUSxFQUFFLENBQUMsQ0FBQztvQkFFdkksRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLGdDQUFnQyxLQUFLLElBQUksQ0FBQzt3QkFDakUsSUFBSSxDQUFDLGlCQUFpQixDQUFDLGdDQUFnQyxHQUFHLElBQUksSUFBSSxDQUFDLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxnQ0FBZ0MsQ0FBQyxRQUFRLEVBQUUsQ0FBQyxDQUFDO29CQUUzSSxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLEtBQUssSUFBSSxDQUFDO3dCQUMvRCxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLEdBQUcsSUFBSSxJQUFJLENBQUMsSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixDQUFDLFFBQVEsRUFBRSxDQUFDLENBQUM7Z0JBQzNJLENBQUM7Z0JBRUQsSUFBSSxDQUFDLGVBQWUsR0FBRyxLQUFLLENBQUM7Z0JBQzdCLElBQUksQ0FBQyxnQkFBZ0IsR0FBRyxLQUFLLENBQUM7WUFDbEMsQ0FBQztZQUVELGlEQUFpRDtZQUNqRCw2Q0FBYSxHQUFiO2dCQUNJLEVBQUUsQ0FBQyxDQUFDLElBQUksQ0FBQyxnQkFBZ0IsS0FBSyxLQUFLLENBQUM7b0JBQ2hDLElBQUksQ0FBQyxlQUFlLEdBQUcsSUFBSSxDQUFDO1lBQ3BDLENBQUM7WUFFRCw2REFBNkQ7WUFDN0Qsc0ZBQXNGO1lBQ3RGLHVEQUF1QixHQUF2QjtnQkFDSSxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsZ0JBQWdCLEtBQUssS0FBSyxDQUFDLENBQUMsQ0FBQztvQkFDbEMsRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLFdBQVcsQ0FBQyxJQUFJLENBQUMsc0JBQXNCLENBQUMsQ0FBQyw4QkFBOEIsS0FBSyxPQUFPLElBQUksSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixLQUFLLElBQUksQ0FBQyxXQUFXLENBQUMsSUFBSSxDQUFDLHNCQUFzQixDQUFDLENBQUMsOEJBQThCLENBQUM7d0JBQ25PLElBQUksQ0FBQyxlQUFlLEdBQUcsSUFBSSxDQUFDO29CQUVoQyxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsV0FBVyxDQUFDLElBQUksQ0FBQyxzQkFBc0IsQ0FBQyxDQUFDLDhCQUE4QixLQUFLLE9BQU8sSUFBSSxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLEtBQUssSUFBSSxDQUFDO3dCQUMzSixJQUFJLENBQUMsZUFBZSxHQUFHLElBQUksQ0FBQztnQkFDcEMsQ0FBQztZQUNMLENBQUM7WUFFRCwrREFBK0Q7WUFDL0Qsc0ZBQXNGO1lBQ3RGLHdEQUF3QixHQUF4QjtnQkFDSSxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsZ0JBQWdCLEtBQUssS0FBSyxDQUFDLENBQUMsQ0FBQztvQkFDbEMsRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLFdBQVcsQ0FBQyxJQUFJLENBQUMsc0JBQXNCLENBQUMsQ0FBQyxnQ0FBZ0MsS0FBSyxPQUFPLElBQUksSUFBSSxDQUFDLGlCQUFpQixDQUFDLGdDQUFnQyxLQUFLLElBQUksQ0FBQyxXQUFXLENBQUMsSUFBSSxDQUFDLHNCQUFzQixDQUFDLENBQUMsZ0NBQWdDLENBQUM7d0JBQ3pPLElBQUksQ0FBQyxlQUFlLEdBQUcsSUFBSSxDQUFDO29CQUVoQyxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsV0FBVyxDQUFDLElBQUksQ0FBQyxzQkFBc0IsQ0FBQyxDQUFDLGdDQUFnQyxLQUFLLE9BQU8sSUFBSSxJQUFJLENBQUMsaUJBQWlCLENBQUMsZ0NBQWdDLEtBQUssSUFBSSxDQUFDO3dCQUMvSixJQUFJLENBQUMsZUFBZSxHQUFHLElBQUksQ0FBQztnQkFDcEMsQ0FBQztZQUNMLENBQUM7WUFFRCw2REFBNkQ7WUFDN0Qsc0ZBQXNGO1lBQ3RGLHNEQUFzQixHQUF0QjtnQkFDSSxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsZ0JBQWdCLEtBQUssS0FBSyxDQUFDLENBQUMsQ0FBQztvQkFDbEMsRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLFdBQVcsQ0FBQyxJQUFJLENBQUMsc0JBQXNCLENBQUMsQ0FBQyw4QkFBOEIsS0FBSyxPQUFPLElBQUksSUFBSSxDQUFDLGlCQUFpQixDQUFDLDhCQUE4QixLQUFLLElBQUksQ0FBQyxXQUFXLENBQUMsSUFBSSxDQUFDLHNCQUFzQixDQUFDLENBQUMsOEJBQThCLENBQUM7d0JBQ25PLElBQUksQ0FBQyxlQUFlLEdBQUcsSUFBSSxDQUFDO29CQUVoQyxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsV0FBVyxDQUFDLElBQUksQ0FBQyxzQkFBc0IsQ0FBQyxDQUFDLDhCQUE4QixLQUFLLE9BQU8sSUFBSSxJQUFJLENBQUMsaUJBQWlCLENBQUMsOEJBQThCLEtBQUssSUFBSSxDQUFDO3dCQUMzSixJQUFJLENBQUMsZUFBZSxHQUFHLElBQUksQ0FBQztnQkFDcEMsQ0FBQztZQUNMLENBQUM7WUFHRCw2QkFBNkI7WUFDN0IsZ0RBQWdCLEdBQWhCO2dCQUNJLElBQUksQ0FBQyxpQkFBaUIsR0FBRyxJQUFJLFVBQVUsRUFBRSxDQUFDO2dCQUMxQyxJQUFJLENBQUMsZUFBZSxHQUFHLElBQUksQ0FBQztnQkFDNUIsSUFBSSxDQUFDLFNBQVMsR0FBRyxLQUFLLENBQUM7Z0JBQ3ZCLElBQUksQ0FBQyxXQUFXLEdBQUcsS0FBSyxDQUFDO2dCQUN6QixJQUFJLENBQUMsZ0JBQWdCLEdBQUcsSUFBSSxDQUFDO1lBRWpDLENBQUM7WUFFRCx3REFBd0Q7WUFDeEQsOENBQWMsR0FBZDtnQkFBQSxpQkFVQztnQkFURyxJQUFJLENBQUMsT0FBTyxDQUFDLGNBQWMsQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsQ0FBQyxJQUFJLENBQUMsVUFBQyxJQUFJO29CQUMxRCxLQUFJLENBQUMsV0FBVyxDQUFDLE1BQU0sQ0FBQyxLQUFJLENBQUMsc0JBQXNCLEVBQUUsQ0FBQyxDQUFDLENBQUM7b0JBQ3hELEtBQUksQ0FBQyxzQkFBc0IsR0FBRyxJQUFJLENBQUM7b0JBQ25DLEtBQUksQ0FBQyxpQkFBaUIsR0FBRyxJQUFJLENBQUM7b0JBQzlCLEtBQUksQ0FBQyxTQUFTLEdBQUcsS0FBSyxDQUFDO29CQUN2QixLQUFJLENBQUMsV0FBVyxHQUFHLEtBQUssQ0FBQztnQkFDN0IsQ0FBQyxFQUFFLFVBQUMsR0FBRztvQkFDSCxPQUFPLENBQUMsR0FBRyxDQUFDLG1CQUFtQixDQUFDLENBQUM7Z0JBQ3JDLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUVELHdEQUF3RDtZQUN4RCxnREFBZ0IsR0FBaEI7Z0JBQUEsaUJBVUM7Z0JBVEcsSUFBSSxDQUFDLE9BQU8sQ0FBQyxnQkFBZ0IsQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsQ0FBQyxJQUFJLENBQUMsVUFBQyxJQUFJO29CQUM1RCxLQUFJLENBQUMsV0FBVyxDQUFDLE1BQU0sQ0FBQyxLQUFJLENBQUMsc0JBQXNCLEVBQUUsQ0FBQyxDQUFDLENBQUM7b0JBQ3hELEtBQUksQ0FBQyxzQkFBc0IsR0FBRyxJQUFJLENBQUM7b0JBQ25DLEtBQUksQ0FBQyxpQkFBaUIsR0FBRyxJQUFJLENBQUM7b0JBQzlCLEtBQUksQ0FBQyxTQUFTLEdBQUcsS0FBSyxDQUFDO29CQUN2QixLQUFJLENBQUMsV0FBVyxHQUFHLEtBQUssQ0FBQztnQkFDN0IsQ0FBQyxFQUFFLFVBQUMsR0FBRztvQkFDSCxPQUFPLENBQUMsR0FBRyxDQUFDLHFCQUFxQixDQUFDLENBQUM7Z0JBQ3ZDLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUNMLDRCQUFDO1FBQUQsQ0F0TkEsQUFzTkMsSUFBQTtRQXROWSxpQ0FBcUIsd0JBc05qQyxDQUFBO0lBQ0wsQ0FBQyxFQXhOcUIsV0FBVyxHQUFYLHVCQUFXLEtBQVgsdUJBQVcsUUF3TmhDO0FBQUQsQ0FBQyxFQXhOUyxXQUFXLEtBQVgsV0FBVyxRQXdOcEI7QUMzTkQ7SUFHSTtRQUNJLElBQUksQ0FBQyxVQUFVLEVBQUUsQ0FBQztJQUN0QixDQUFDO0lBRUQsMkJBQVUsR0FBVjtJQUNBLENBQUM7SUFDTCxhQUFDO0FBQUQsQ0FUQSxBQVNDLElBQUE7QUNURDtJQUlJO1FBQ0ksSUFBSSxDQUFDLFVBQVUsRUFBRSxDQUFDO0lBQ3RCLENBQUM7SUFFRCxpQ0FBVSxHQUFWO0lBQ0EsQ0FBQztJQUNMLG1CQUFDO0FBQUQsQ0FWQSxBQVVDLElBQUE7QUNWRDtJQUtJO1FBQ0ksSUFBSSxDQUFDLFVBQVUsRUFBRSxDQUFDO0lBQ3RCLENBQUM7SUFFRCx5QkFBVSxHQUFWO0lBQ0EsQ0FBQztJQUNMLFdBQUM7QUFBRCxDQVhBLEFBV0MsSUFBQTtBQ1hEO0lBQUE7SUFjQSxDQUFDO0lBQUQsZUFBQztBQUFELENBZEEsQUFjQyxJQUFBO0FBR0Q7SUFBQTtJQWNBLENBQUM7SUFBRCxtQkFBQztBQUFELENBZEEsQUFjQyxJQUFBO0FBR0Q7SUFBQTtJQVdBLENBQUM7SUFBRCxtQkFBQztBQUFELENBWEEsQUFXQyxJQUFBO0FDN0NELHFDQUFxQztBQUNyQyx5Q0FBeUM7QUFDekMsK0NBQStDO0FBQy9DLHVDQUF1QztBQUN2QywyQ0FBMkM7QUFHM0MsSUFBVSxXQUFXLENBc0ZwQjtBQXRGRCxXQUFVLFdBQVc7SUFBQyxJQUFBLFdBQVcsQ0FzRmhDO0lBdEZxQixXQUFBLFdBQVcsRUFBQyxDQUFDO1FBQy9CO1lBZ0JJLDZCQUFZLGdCQUFxQjtnQkFDN0IsSUFBSSxDQUFDLE9BQU8sR0FBRyxnQkFBZ0IsQ0FBQztZQUNwQyxDQUFDO1lBRUQscUNBQU8sR0FBUDtnQkFBQSxpQkFPQztnQkFORyxrQkFBa0I7Z0JBQ2xCLElBQUksQ0FBQyxPQUFPLENBQUMsVUFBVSxFQUFFLENBQUMsSUFBSSxDQUFDLFVBQUMsT0FBTztvQkFDbkMsS0FBSSxDQUFDLE9BQU8sR0FBRyxPQUFPLENBQUM7Z0JBQzNCLENBQUMsRUFBRSxVQUFDLEdBQUc7b0JBQ0gsT0FBTyxDQUFDLEdBQUcsQ0FBQyw2REFBNkQsQ0FBQyxDQUFDO2dCQUMvRSxDQUFDLENBQUMsQ0FBQztZQUNQLENBQUM7WUFFRCxxQkFBcUI7WUFDckIsMENBQVksR0FBWixVQUFhLE1BQWM7Z0JBQTNCLGlCQWdCQztnQkFmRyxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsYUFBYSxLQUFLLE1BQU0sQ0FBQztvQkFDOUIsTUFBTSxDQUFDO2dCQUVYLElBQUksQ0FBQyxhQUFhLEdBQUcsTUFBTSxDQUFDO2dCQUU1QixJQUFJLENBQUMsYUFBYSxHQUFHLEVBQUUsQ0FBQztnQkFDeEIsSUFBSSxDQUFDLEtBQUssR0FBRyxFQUFFLENBQUM7Z0JBQ2hCLElBQUksQ0FBQyxTQUFTLEdBQUcsRUFBRSxDQUFDO2dCQUVwQiwrRUFBK0U7Z0JBQy9FLElBQUksQ0FBQyxPQUFPLENBQUMsZ0JBQWdCLENBQUMsSUFBSSxDQUFDLGFBQWEsQ0FBQyxDQUFDLElBQUksQ0FBQyxVQUFDLGFBQWE7b0JBQ2pFLEtBQUksQ0FBQyxhQUFhLEdBQUcsYUFBYSxDQUFDO2dCQUN2QyxDQUFDLEVBQUUsVUFBQyxHQUFHO29CQUNILE9BQU8sQ0FBQyxHQUFHLENBQUMsd0VBQXdFLENBQUMsQ0FBQztnQkFDMUYsQ0FBQyxDQUFDLENBQUM7WUFDUCxDQUFDO1lBRUQsMkJBQTJCO1lBQzNCLGdEQUFrQixHQUFsQixVQUFtQixZQUEwQjtnQkFBN0MsaUJBVUM7Z0JBVEcsSUFBSSxDQUFDLG1CQUFtQixHQUFHLFlBQVksQ0FBQztnQkFFeEMsNENBQTRDO2dCQUM1QyxJQUFJLENBQUMsT0FBTyxDQUFDLFFBQVEsQ0FBQyxJQUFJLENBQUMsYUFBYSxFQUFFLElBQUksQ0FBQyxtQkFBbUIsQ0FBQyxDQUFDLElBQUksQ0FBQyxVQUFDLEtBQUs7b0JBQzNFLEtBQUksQ0FBQyxLQUFLLEdBQUcsS0FBSyxDQUFDO29CQUNuQixLQUFJLENBQUMsU0FBUyxHQUFHLEVBQUUsQ0FBQztnQkFDeEIsQ0FBQyxFQUFFLFVBQUMsR0FBRztvQkFDSCxPQUFPLENBQUMsR0FBRyxDQUFDLHNFQUFzRSxDQUFDLENBQUM7Z0JBQ3hGLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUVELHdDQUF3QztZQUN4Qyx3Q0FBVSxHQUFWLFVBQVcsSUFBVSxFQUFFLGFBQXFCO2dCQUE1QyxpQkFxQkM7Z0JBcEJHLElBQUksQ0FBQyxXQUFXLEdBQUcsSUFBSSxDQUFDO2dCQUN4QixJQUFJLENBQUMsb0JBQW9CLEdBQUcsYUFBYSxDQUFDO2dCQUUxQyxJQUFJLENBQUMsT0FBTyxDQUFDLFlBQVksQ0FBQyxJQUFJLENBQUMsYUFBYSxFQUFFLElBQUksQ0FBQyxXQUFXLEVBQUUsSUFBSSxDQUFDLG9CQUFvQixDQUFDLENBQUMsSUFBSSxDQUFDLFVBQUMsU0FBUztvQkFDdEcsS0FBSSxDQUFDLFNBQVMsR0FBRyxTQUFTLENBQUM7Z0JBQy9CLENBQUMsRUFBRSxVQUFDLEdBQUc7b0JBQ0gsT0FBTyxDQUFDLEdBQUcsQ0FBQywrREFBK0QsQ0FBQyxDQUFDO2dCQUNqRixDQUFDLENBQUMsQ0FBQztnQkFFSCxJQUFJLENBQUMsT0FBTyxDQUFDLGdCQUFnQixDQUFDLElBQUksQ0FBQyxhQUFhLEVBQUUsSUFBSSxDQUFDLFdBQVcsRUFBRSxJQUFJLENBQUMsb0JBQW9CLENBQUMsQ0FBQyxJQUFJLENBQUMsVUFBQyxhQUFhO29CQUM5RyxLQUFJLENBQUMsYUFBYSxHQUFHLGFBQWEsQ0FBQztnQkFDdkMsQ0FBQyxFQUFFLFVBQUMsR0FBRztvQkFDSCxPQUFPLENBQUMsR0FBRyxDQUFDLG9FQUFvRSxDQUFDLENBQUM7Z0JBQ3RGLENBQUMsQ0FBQyxDQUFDO2dCQUVILElBQUksQ0FBQyxPQUFPLENBQUMsZ0JBQWdCLENBQUMsSUFBSSxDQUFDLGFBQWEsRUFBRSxJQUFJLENBQUMsV0FBVyxFQUFFLElBQUksQ0FBQyxvQkFBb0IsQ0FBQyxDQUFDLElBQUksQ0FBQyxVQUFDLGFBQWE7b0JBQzlHLEtBQUksQ0FBQyxhQUFhLEdBQUcsYUFBYSxDQUFDO2dCQUN2QyxDQUFDLEVBQUUsVUFBQyxHQUFHO29CQUNILE9BQU8sQ0FBQyxHQUFHLENBQUMsb0VBQW9FLENBQUMsQ0FBQztnQkFDdEYsQ0FBQyxDQUFDLENBQUM7WUFDUCxDQUFDO1lBQ0wsMEJBQUM7UUFBRCxDQXBGQSxBQW9GQyxJQUFBO1FBcEZZLCtCQUFtQixzQkFvRi9CLENBQUE7SUFDTCxDQUFDLEVBdEZxQixXQUFXLEdBQVgsdUJBQVcsS0FBWCx1QkFBVyxRQXNGaEM7QUFBRCxDQUFDLEVBdEZTLFdBQVcsS0FBWCxXQUFXLFFBc0ZwQjtBQzdGRDtJQUFBO0lBTUEsQ0FBQztJQUFELGFBQUM7QUFBRCxDQU5BLEFBTUMsSUFBQTtBQ05ELHFDQUFxQztBQUNyQyx5Q0FBeUM7QUFDekMsK0NBQStDO0FBQy9DLHlDQUF5QztBQUd6QyxJQUFVLFdBQVcsQ0FxRHBCO0FBckRELFdBQVUsV0FBVztJQUFDLElBQUEsV0FBVyxDQXFEaEM7SUFyRHFCLFdBQUEsV0FBVyxFQUFDLENBQUM7UUFDL0I7WUFVSSw0QkFBWSxlQUFvQjtnQkFDNUIsSUFBSSxDQUFDLE9BQU8sR0FBRyxlQUFlLENBQUM7WUFDbkMsQ0FBQztZQUVELG9DQUFPLEdBQVA7Z0JBQUEsaUJBT0M7Z0JBTkcsa0JBQWtCO2dCQUNsQixJQUFJLENBQUMsT0FBTyxDQUFDLFVBQVUsRUFBRSxDQUFDLElBQUksQ0FBQyxVQUFDLE9BQU87b0JBQ25DLEtBQUksQ0FBQyxPQUFPLEdBQUcsT0FBTyxDQUFDO2dCQUMzQixDQUFDLEVBQUUsVUFBQyxHQUFHO29CQUNILE9BQU8sQ0FBQyxHQUFHLENBQUMsNERBQTRELENBQUMsQ0FBQztnQkFDOUUsQ0FBQyxDQUFDLENBQUM7WUFDUCxDQUFDO1lBRUQscUJBQXFCO1lBQ3JCLHlDQUFZLEdBQVosVUFBYSxNQUFjO2dCQUEzQixpQkFjQztnQkFiRyxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsYUFBYSxLQUFLLE1BQU0sQ0FBQztvQkFDOUIsTUFBTSxDQUFDO2dCQUVYLElBQUksQ0FBQyxhQUFhLEdBQUcsTUFBTSxDQUFDO2dCQUM1QixJQUFJLENBQUMsYUFBYSxHQUFHLEVBQUUsQ0FBQztnQkFDeEIsSUFBSSxDQUFDLFFBQVEsR0FBRyxFQUFFLENBQUM7Z0JBRW5CLCtFQUErRTtnQkFDL0UsSUFBSSxDQUFDLE9BQU8sQ0FBQyxnQkFBZ0IsQ0FBQyxJQUFJLENBQUMsYUFBYSxDQUFDLENBQUMsSUFBSSxDQUFDLFVBQUMsYUFBYTtvQkFDakUsS0FBSSxDQUFDLGFBQWEsR0FBRyxhQUFhLENBQUM7Z0JBQ3ZDLENBQUMsRUFBRSxVQUFDLEdBQUc7b0JBQ0gsT0FBTyxDQUFDLEdBQUcsQ0FBQyx1RUFBdUUsQ0FBQyxDQUFDO2dCQUN6RixDQUFDLENBQUMsQ0FBQztZQUNQLENBQUM7WUFFRCwyQkFBMkI7WUFDM0IsK0NBQWtCLEdBQWxCLFVBQW1CLFlBQTBCO2dCQUE3QyxpQkFTQztnQkFSRyxJQUFJLENBQUMsbUJBQW1CLEdBQUcsWUFBWSxDQUFDO2dCQUV4QyxzQkFBc0I7Z0JBQ3RCLElBQUksQ0FBQyxPQUFPLENBQUMsV0FBVyxDQUFDLElBQUksQ0FBQyxhQUFhLEVBQUUsSUFBSSxDQUFDLG1CQUFtQixDQUFDLENBQUMsSUFBSSxDQUFDLFVBQUMsUUFBUTtvQkFDakYsS0FBSSxDQUFDLFFBQVEsR0FBRyxRQUFRLENBQUM7Z0JBQzdCLENBQUMsRUFBRSxVQUFDLEdBQUc7b0JBQ0gsT0FBTyxDQUFDLEdBQUcsQ0FBQyw2REFBNkQsQ0FBQyxDQUFDO2dCQUMvRSxDQUFDLENBQUMsQ0FBQztZQUNQLENBQUM7WUFDTCx5QkFBQztRQUFELENBbkRBLEFBbURDLElBQUE7UUFuRFksOEJBQWtCLHFCQW1EOUIsQ0FBQTtJQUNMLENBQUMsRUFyRHFCLFdBQVcsR0FBWCx1QkFBVyxLQUFYLHVCQUFXLFFBcURoQztBQUFELENBQUMsRUFyRFMsV0FBVyxLQUFYLFdBQVcsUUFxRHBCO0FDM0RELHFDQUFxQztBQUdyQyxJQUFVLFdBQVcsQ0E0R3BCO0FBNUdELFdBQVUsV0FBVztJQUFDLElBQUEsUUFBUSxDQTRHN0I7SUE1R3FCLFdBQUEsUUFBUSxFQUFDLENBQUM7UUFDNUI7WUFHSSw0QkFBWSxLQUFzQjtnQkFDOUIsSUFBSSxDQUFDLElBQUksR0FBRyxLQUFLLENBQUM7WUFDdEIsQ0FBQztZQUdELHlCQUF5QjtZQUN6QiwyQ0FBYyxHQUFkO2dCQUNJLElBQUksR0FBRyxHQUFHLHdCQUF3QixDQUFDO2dCQUVuQyxNQUFNLENBQUMsSUFBSSxDQUFDLElBQUksQ0FBQztvQkFDYixNQUFNLEVBQUUsTUFBTTtvQkFDZCxHQUFHLEVBQUUsR0FBRztpQkFDWCxDQUFDLENBQUMsSUFBSSxDQUFDLHlCQUF5QixRQUFRO29CQUNyQyxNQUFNLENBQUMsUUFBUSxDQUFDLElBQUksSUFBSSxFQUFFLENBQUM7Z0JBQy9CLENBQUMsRUFBRSx1QkFBdUIsS0FBSztvQkFDM0IsSUFBSSxNQUFNLEdBQUcsQ0FBQyxLQUFLLENBQUMsT0FBTyxDQUFDLEdBQUcsS0FBSyxDQUFDLE9BQU87d0JBQ3hDLEtBQUssQ0FBQyxNQUFNLEdBQU0sS0FBSyxDQUFDLE1BQU0sV0FBTSxLQUFLLENBQUMsVUFBWSxHQUFHLGtEQUFrRCxDQUFDO29CQUNoSCxPQUFPLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUMseUJBQXlCO29CQUNoRCxNQUFNLENBQUMsRUFBRSxDQUFDO2dCQUNkLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUVELDZCQUE2QjtZQUM3Qiw2Q0FBZ0IsR0FBaEIsVUFBaUIsVUFBZTtnQkFDNUIsSUFBSSxHQUFHLEdBQUcsOEJBQThCLENBQUM7Z0JBRXpDLElBQUksSUFBSSxHQUFHLElBQUksQ0FBQyxTQUFTLENBQUMsVUFBVSxDQUFDLENBQUM7Z0JBRXRDLE1BQU0sQ0FBQyxJQUFJLENBQUMsSUFBSSxDQUFDO29CQUNiLE1BQU0sRUFBRSxNQUFNO29CQUNkLEdBQUcsRUFBRSxHQUFHO29CQUNSLElBQUksRUFBRSxFQUFFLElBQUksRUFBRSxJQUFJLEVBQUU7aUJBQ3ZCLENBQUMsQ0FBQyxJQUFJLENBQUMseUJBQXlCLFFBQVE7b0JBQ3JDLE1BQU0sQ0FBQyxRQUFRLENBQUMsSUFBSSxJQUFJLEVBQUUsQ0FBQztnQkFDL0IsQ0FBQyxFQUFFLHVCQUF1QixLQUFLO29CQUMzQixJQUFJLE1BQU0sR0FBRyxDQUFDLEtBQUssQ0FBQyxPQUFPLENBQUMsR0FBRyxLQUFLLENBQUMsT0FBTzt3QkFDeEMsS0FBSyxDQUFDLE1BQU0sR0FBTSxLQUFLLENBQUMsTUFBTSxXQUFNLEtBQUssQ0FBQyxVQUFZLEdBQUcsb0RBQW9ELENBQUM7b0JBQ2xILE9BQU8sQ0FBQyxLQUFLLENBQUMsTUFBTSxDQUFDLENBQUMsQ0FBQyx5QkFBeUI7b0JBQ2hELE1BQU0sQ0FBQyxFQUFFLENBQUM7Z0JBQ2QsQ0FBQyxDQUFDLENBQUM7WUFDUCxDQUFDO1lBRUQsdUJBQXVCO1lBQ3ZCLDZDQUFnQixHQUFoQixVQUFpQixVQUFlO2dCQUM1QixJQUFJLEdBQUcsR0FBRyw4QkFBOEIsQ0FBQztnQkFFekMsSUFBSSxJQUFJLEdBQUcsSUFBSSxDQUFDLFNBQVMsQ0FBQyxVQUFVLENBQUMsQ0FBQztnQkFFdEMsTUFBTSxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUM7b0JBQ2IsTUFBTSxFQUFFLE1BQU07b0JBQ2QsR0FBRyxFQUFFLEdBQUc7b0JBQ1IsSUFBSSxFQUFFLEVBQUUsSUFBSSxFQUFFLElBQUksRUFBRTtpQkFDdkIsQ0FBQyxDQUFDLElBQUksQ0FBQyx5QkFBeUIsUUFBUTtvQkFDckMsTUFBTSxDQUFDLFFBQVEsQ0FBQyxJQUFJLElBQUksRUFBRSxDQUFDO2dCQUMvQixDQUFDLEVBQUUsdUJBQXVCLEtBQUs7b0JBQzNCLElBQUksTUFBTSxHQUFHLENBQUMsS0FBSyxDQUFDLE9BQU8sQ0FBQyxHQUFHLEtBQUssQ0FBQyxPQUFPO3dCQUN4QyxLQUFLLENBQUMsTUFBTSxHQUFNLEtBQUssQ0FBQyxNQUFNLFdBQU0sS0FBSyxDQUFDLFVBQVksR0FBRyxvREFBb0QsQ0FBQztvQkFDbEgsT0FBTyxDQUFDLEtBQUssQ0FBQyxNQUFNLENBQUMsQ0FBQyxDQUFDLHlCQUF5QjtvQkFDaEQsTUFBTSxDQUFDLEVBQUUsQ0FBQztnQkFDZCxDQUFDLENBQUMsQ0FBQztZQUNQLENBQUM7WUFFRCxpREFBaUQ7WUFDakQsMkNBQWMsR0FBZCxVQUFlLFVBQWU7Z0JBQzFCLElBQUksR0FBRyxHQUFHLDRCQUE0QixDQUFDO2dCQUV2QyxJQUFJLElBQUksR0FBRyxJQUFJLENBQUMsU0FBUyxDQUFDLFVBQVUsQ0FBQyxDQUFDO2dCQUV0QyxNQUFNLENBQUMsSUFBSSxDQUFDLElBQUksQ0FBQztvQkFDYixNQUFNLEVBQUUsTUFBTTtvQkFDZCxHQUFHLEVBQUUsR0FBRztvQkFDUixJQUFJLEVBQUUsRUFBRSxJQUFJLEVBQUUsSUFBSSxFQUFFO2lCQUN2QixDQUFDLENBQUMsSUFBSSxDQUFDLHlCQUF5QixRQUFRO29CQUNyQyxNQUFNLENBQUMsUUFBUSxDQUFDLElBQUksSUFBSSxFQUFFLENBQUM7Z0JBQy9CLENBQUMsRUFBRSx1QkFBdUIsS0FBSztvQkFDM0IsSUFBSSxNQUFNLEdBQUcsQ0FBQyxLQUFLLENBQUMsT0FBTyxDQUFDLEdBQUcsS0FBSyxDQUFDLE9BQU87d0JBQ3hDLEtBQUssQ0FBQyxNQUFNLEdBQU0sS0FBSyxDQUFDLE1BQU0sV0FBTSxLQUFLLENBQUMsVUFBWSxHQUFHLGtEQUFrRCxDQUFDO29CQUNoSCxPQUFPLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUMseUJBQXlCO29CQUNoRCxNQUFNLENBQUMsRUFBRSxDQUFDO2dCQUNkLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUVELHVCQUF1QjtZQUN2Qiw2Q0FBZ0IsR0FBaEIsVUFBaUIsVUFBZTtnQkFDNUIsSUFBSSxHQUFHLEdBQUcsOEJBQThCLENBQUM7Z0JBRXpDLElBQUksSUFBSSxHQUFHLElBQUksQ0FBQyxTQUFTLENBQUMsVUFBVSxDQUFDLENBQUM7Z0JBRXRDLE1BQU0sQ0FBQyxJQUFJLENBQUMsSUFBSSxDQUFDO29CQUNiLE1BQU0sRUFBRSxNQUFNO29CQUNkLEdBQUcsRUFBRSxHQUFHO29CQUNSLElBQUksRUFBRSxFQUFFLElBQUksRUFBRSxJQUFJLEVBQUU7aUJBQ3ZCLENBQUMsQ0FBQyxJQUFJLENBQUMseUJBQXlCLFFBQVE7b0JBQ3JDLE1BQU0sQ0FBQyxRQUFRLENBQUMsSUFBSSxJQUFJLEVBQUUsQ0FBQztnQkFDL0IsQ0FBQyxFQUFFLHVCQUF1QixLQUFLO29CQUMzQixJQUFJLE1BQU0sR0FBRyxDQUFDLEtBQUssQ0FBQyxPQUFPLENBQUMsR0FBRyxLQUFLLENBQUMsT0FBTzt3QkFDeEMsS0FBSyxDQUFDLE1BQU0sR0FBTSxLQUFLLENBQUMsTUFBTSxXQUFNLEtBQUssQ0FBQyxVQUFZLEdBQUcsb0RBQW9ELENBQUM7b0JBQ2xILE9BQU8sQ0FBQyxLQUFLLENBQUMsTUFBTSxDQUFDLENBQUMsQ0FBQyx5QkFBeUI7b0JBQ2hELE1BQU0sQ0FBQyxFQUFFLENBQUM7Z0JBQ2QsQ0FBQyxDQUFDLENBQUM7WUFDUCxDQUFDO1lBR0wseUJBQUM7UUFBRCxDQTFHQSxBQTBHQyxJQUFBO1FBMUdZLDJCQUFrQixxQkEwRzlCLENBQUE7SUFDTCxDQUFDLEVBNUdxQixRQUFRLEdBQVIsb0JBQVEsS0FBUixvQkFBUSxRQTRHN0I7QUFBRCxDQUFDLEVBNUdTLFdBQVcsS0FBWCxXQUFXLFFBNEdwQjtBQy9HRCxxQ0FBcUM7QUFHckMsSUFBVSxXQUFXLENBbUhwQjtBQW5IRCxXQUFVLFdBQVc7SUFBQyxJQUFBLFFBQVEsQ0FtSDdCO0lBbkhxQixXQUFBLFFBQVEsRUFBQyxDQUFDO1FBQzVCO1lBR0ksMEJBQVksS0FBc0I7Z0JBQzlCLElBQUksQ0FBQyxJQUFJLEdBQUcsS0FBSyxDQUFDO1lBQ3RCLENBQUM7WUFFRCxxQkFBcUI7WUFDckIscUNBQVUsR0FBVjtnQkFDSSxJQUFJLEdBQUcsR0FBRyxvQkFBb0IsQ0FBQztnQkFFL0IsTUFBTSxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUM7b0JBQ2IsTUFBTSxFQUFFLE1BQU07b0JBQ2QsR0FBRyxFQUFFLEdBQUc7aUJBQ1gsQ0FBQyxDQUFDLElBQUksQ0FBQyx5QkFBeUIsUUFBUTtvQkFDckMsTUFBTSxDQUFDLFFBQVEsQ0FBQyxJQUFJLElBQUksRUFBRSxDQUFDO2dCQUMvQixDQUFDLEVBQUUsdUJBQXVCLEtBQUs7b0JBQzNCLElBQUksTUFBTSxHQUFHLENBQUMsS0FBSyxDQUFDLE9BQU8sQ0FBQyxHQUFHLEtBQUssQ0FBQyxPQUFPO3dCQUN4QyxLQUFLLENBQUMsTUFBTSxHQUFNLEtBQUssQ0FBQyxNQUFNLFdBQU0sS0FBSyxDQUFDLFVBQVksR0FBRyw0Q0FBNEMsQ0FBQztvQkFDMUcsT0FBTyxDQUFDLEtBQUssQ0FBQyxNQUFNLENBQUMsQ0FBQyxDQUFDLHlCQUF5QjtvQkFDaEQsTUFBTSxDQUFDLEVBQUUsQ0FBQztnQkFDZCxDQUFDLENBQUMsQ0FBQztZQUNQLENBQUM7WUFFRCwyQkFBMkI7WUFDM0IsMkNBQWdCLEdBQWhCLFVBQWlCLE1BQWM7Z0JBQzNCLElBQUksR0FBRyxHQUFHLDBCQUEwQixDQUFDO2dCQUVyQyxNQUFNLENBQUMsSUFBSSxDQUFDLElBQUksQ0FBQztvQkFDYixNQUFNLEVBQUUsTUFBTTtvQkFDZCxHQUFHLEVBQUUsR0FBRztvQkFDUixJQUFJLEVBQUUsRUFBRSxNQUFNLEVBQUUsTUFBTSxFQUFFO2lCQUMzQixDQUFDLENBQUMsSUFBSSxDQUFDLHlCQUF5QixRQUFRO29CQUNyQyxNQUFNLENBQUMsUUFBUSxDQUFDLElBQUksSUFBSSxFQUFFLENBQUM7Z0JBQy9CLENBQUMsRUFBRSx1QkFBdUIsS0FBSztvQkFDM0IsSUFBSSxNQUFNLEdBQUcsQ0FBQyxLQUFLLENBQUMsT0FBTyxDQUFDLEdBQUcsS0FBSyxDQUFDLE9BQU87d0JBQ3hDLEtBQUssQ0FBQyxNQUFNLEdBQU0sS0FBSyxDQUFDLE1BQU0sV0FBTSxLQUFLLENBQUMsVUFBWSxHQUFHLGtEQUFrRCxDQUFDO29CQUNoSCxPQUFPLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUMseUJBQXlCO29CQUNoRCxNQUFNLENBQUMsRUFBRSxDQUFDO2dCQUNkLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUVELG1CQUFtQjtZQUNuQixtQ0FBUSxHQUFSLFVBQVMsTUFBYyxFQUFFLFlBQTBCO2dCQUMvQyxJQUFJLEdBQUcsR0FBRyxrQkFBa0IsQ0FBQztnQkFFN0IsTUFBTSxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUM7b0JBQ2IsTUFBTSxFQUFFLE1BQU07b0JBQ2QsR0FBRyxFQUFFLEdBQUc7b0JBQ1IsSUFBSSxFQUFFLEVBQUUsTUFBTSxFQUFFLE1BQU0sQ0FBQyxNQUFNLEVBQUUsV0FBVyxFQUFFLFlBQVksQ0FBQyxXQUFXLEVBQUU7aUJBQ3pFLENBQUMsQ0FBQyxJQUFJLENBQUMseUJBQXlCLFFBQVE7b0JBQ3JDLE1BQU0sQ0FBQyxRQUFRLENBQUMsSUFBSSxJQUFJLEVBQUUsQ0FBQztnQkFDL0IsQ0FBQyxFQUFFLHVCQUF1QixLQUFLO29CQUMzQixJQUFJLE1BQU0sR0FBRyxDQUFDLEtBQUssQ0FBQyxPQUFPLENBQUMsR0FBRyxLQUFLLENBQUMsT0FBTzt3QkFDeEMsS0FBSyxDQUFDLE1BQU0sR0FBTSxLQUFLLENBQUMsTUFBTSxXQUFNLEtBQUssQ0FBQyxVQUFZLEdBQUcsMENBQTBDLENBQUM7b0JBQ3hHLE9BQU8sQ0FBQyxLQUFLLENBQUMsTUFBTSxDQUFDLENBQUMsQ0FBQyx5QkFBeUI7b0JBQ2hELE1BQU0sQ0FBQyxFQUFFLENBQUM7Z0JBQ2QsQ0FBQyxDQUFDLENBQUM7WUFDUCxDQUFDO1lBRUQsc0NBQXNDO1lBQ3RDLHVDQUFZLEdBQVosVUFBYSxNQUFjLEVBQUUsSUFBVSxFQUFFLGFBQXFCO2dCQUMxRCxJQUFJLEdBQUcsR0FBRyxzQkFBc0IsQ0FBQztnQkFFakMsTUFBTSxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUM7b0JBQ2IsTUFBTSxFQUFFLE1BQU07b0JBQ2QsR0FBRyxFQUFFLEdBQUc7b0JBQ1IsSUFBSSxFQUFFLElBQUksQ0FBQyxTQUFTLENBQUMsRUFBRSxRQUFRLEVBQUUsTUFBTSxDQUFDLE1BQU0sRUFBRSxTQUFTLEVBQUUsSUFBSSxDQUFDLE9BQU8sRUFBRSxnQkFBZ0IsRUFBRSxhQUFhLEVBQUUsQ0FBQztpQkFDOUcsQ0FBQyxDQUFDLElBQUksQ0FBQyx5QkFBeUIsUUFBUTtvQkFDckMsTUFBTSxDQUFDLFFBQVEsQ0FBQyxJQUFJLElBQUksRUFBRSxDQUFDO2dCQUMvQixDQUFDLEVBQUUsdUJBQXVCLEtBQUs7b0JBQzNCLElBQUksTUFBTSxHQUFHLENBQUMsS0FBSyxDQUFDLE9BQU8sQ0FBQyxHQUFHLEtBQUssQ0FBQyxPQUFPO3dCQUN4QyxLQUFLLENBQUMsTUFBTSxHQUFNLEtBQUssQ0FBQyxNQUFNLFdBQU0sS0FBSyxDQUFDLFVBQVksR0FBRyw4Q0FBOEMsQ0FBQztvQkFDNUcsT0FBTyxDQUFDLEtBQUssQ0FBQyxNQUFNLENBQUMsQ0FBQyxDQUFDLHlCQUF5QjtvQkFDaEQsTUFBTSxDQUFDLEVBQUUsQ0FBQztnQkFDZCxDQUFDLENBQUMsQ0FBQztZQUNQLENBQUM7WUFFRCx3QkFBd0I7WUFDeEIsMkNBQWdCLEdBQWhCLFVBQWlCLE1BQWMsRUFBRSxJQUFVLEVBQUUsYUFBcUI7Z0JBQzlELElBQUksR0FBRyxHQUFHLDJCQUEyQixDQUFDO2dCQUV0QyxNQUFNLENBQUMsSUFBSSxDQUFDLElBQUksQ0FBQztvQkFDYixNQUFNLEVBQUUsTUFBTTtvQkFDZCxHQUFHLEVBQUUsR0FBRztvQkFDUixJQUFJLEVBQUUsSUFBSSxDQUFDLFNBQVMsQ0FBQyxFQUFFLFFBQVEsRUFBRSxNQUFNLENBQUMsTUFBTSxFQUFFLFNBQVMsRUFBRSxJQUFJLENBQUMsT0FBTyxFQUFFLGdCQUFnQixFQUFFLGFBQWEsRUFBRSxDQUFDO2lCQUM5RyxDQUFDLENBQUMsSUFBSSxDQUFDLHlCQUF5QixRQUFRO29CQUNyQyxNQUFNLENBQUMsUUFBUSxDQUFDLElBQUksSUFBSSxFQUFFLENBQUM7Z0JBQy9CLENBQUMsRUFBRSx1QkFBdUIsS0FBSztvQkFDM0IsSUFBSSxNQUFNLEdBQUcsQ0FBQyxLQUFLLENBQUMsT0FBTyxDQUFDLEdBQUcsS0FBSyxDQUFDLE9BQU87d0JBQ3hDLEtBQUssQ0FBQyxNQUFNLEdBQU0sS0FBSyxDQUFDLE1BQU0sV0FBTSxLQUFLLENBQUMsVUFBWSxHQUFHLGtEQUFrRCxDQUFDO29CQUNoSCxPQUFPLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUMseUJBQXlCO29CQUNoRCxNQUFNLENBQUMsRUFBRSxDQUFDO2dCQUNkLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUVELHdCQUF3QjtZQUN4QiwyQ0FBZ0IsR0FBaEIsVUFBaUIsTUFBYyxFQUFFLElBQVUsRUFBRSxhQUFxQjtnQkFDOUQsSUFBSSxHQUFHLEdBQUcsMkJBQTJCLENBQUM7Z0JBRXRDLE1BQU0sQ0FBQyxJQUFJLENBQUMsSUFBSSxDQUFDO29CQUNiLE1BQU0sRUFBRSxNQUFNO29CQUNkLEdBQUcsRUFBRSxHQUFHO29CQUNSLElBQUksRUFBRSxJQUFJLENBQUMsU0FBUyxDQUFDLEVBQUUsUUFBUSxFQUFFLE1BQU0sQ0FBQyxNQUFNLEVBQUUsU0FBUyxFQUFFLElBQUksQ0FBQyxPQUFPLEVBQUUsZ0JBQWdCLEVBQUUsYUFBYSxFQUFFLENBQUM7aUJBQzlHLENBQUMsQ0FBQyxJQUFJLENBQUMseUJBQXlCLFFBQVE7b0JBQ3JDLE1BQU0sQ0FBQyxRQUFRLENBQUMsSUFBSSxJQUFJLEVBQUUsQ0FBQztnQkFDL0IsQ0FBQyxFQUFFLHVCQUF1QixLQUFLO29CQUMzQixJQUFJLE1BQU0sR0FBRyxDQUFDLEtBQUssQ0FBQyxPQUFPLENBQUMsR0FBRyxLQUFLLENBQUMsT0FBTzt3QkFDeEMsS0FBSyxDQUFDLE1BQU0sR0FBTSxLQUFLLENBQUMsTUFBTSxXQUFNLEtBQUssQ0FBQyxVQUFZLEdBQUcsa0RBQWtELENBQUM7b0JBQ2hILE9BQU8sQ0FBQyxLQUFLLENBQUMsTUFBTSxDQUFDLENBQUMsQ0FBQyx5QkFBeUI7b0JBQ2hELE1BQU0sQ0FBQyxFQUFFLENBQUM7Z0JBQ2QsQ0FBQyxDQUFDLENBQUM7WUFDUCxDQUFDO1lBQ0wsdUJBQUM7UUFBRCxDQWpIQSxBQWlIQyxJQUFBO1FBakhZLHlCQUFnQixtQkFpSDVCLENBQUE7SUFDTCxDQUFDLEVBbkhxQixRQUFRLEdBQVIsb0JBQVEsS0FBUixvQkFBUSxRQW1IN0I7QUFBRCxDQUFDLEVBbkhTLFdBQVcsS0FBWCxXQUFXLFFBbUhwQjtBQ3RIRCxxQ0FBcUM7QUFHckMsSUFBVSxXQUFXLENBNkRwQjtBQTdERCxXQUFVLFdBQVc7SUFBQyxJQUFBLFFBQVEsQ0E2RDdCO0lBN0RxQixXQUFBLFFBQVEsRUFBQyxDQUFDO1FBQzVCO1lBR0kseUJBQVksS0FBc0I7Z0JBQzlCLElBQUksQ0FBQyxJQUFJLEdBQUcsS0FBSyxDQUFDO1lBQ3RCLENBQUM7WUFFRCxxQkFBcUI7WUFDckIsb0NBQVUsR0FBVjtnQkFDSSxJQUFJLEdBQUcsR0FBRyxvQkFBb0IsQ0FBQztnQkFFL0IsTUFBTSxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUM7b0JBQ2IsTUFBTSxFQUFFLE1BQU07b0JBQ2QsR0FBRyxFQUFFLEdBQUc7aUJBQ1gsQ0FBQyxDQUFDLElBQUksQ0FBQyx5QkFBeUIsUUFBUTtvQkFDckMsTUFBTSxDQUFDLFFBQVEsQ0FBQyxJQUFJLElBQUksRUFBRSxDQUFDO2dCQUMvQixDQUFDLEVBQUUsdUJBQXVCLEtBQUs7b0JBQzNCLElBQUksTUFBTSxHQUFHLENBQUMsS0FBSyxDQUFDLE9BQU8sQ0FBQyxHQUFHLEtBQUssQ0FBQyxPQUFPO3dCQUN4QyxLQUFLLENBQUMsTUFBTSxHQUFNLEtBQUssQ0FBQyxNQUFNLFdBQU0sS0FBSyxDQUFDLFVBQVksR0FBRywyQ0FBMkMsQ0FBQztvQkFDekcsT0FBTyxDQUFDLEtBQUssQ0FBQyxNQUFNLENBQUMsQ0FBQyxDQUFDLHlCQUF5QjtvQkFDaEQsTUFBTSxDQUFDLEVBQUUsQ0FBQztnQkFDZCxDQUFDLENBQUMsQ0FBQztZQUNQLENBQUM7WUFFRCwyQkFBMkI7WUFDM0IsMENBQWdCLEdBQWhCLFVBQWlCLE1BQWM7Z0JBQzNCLElBQUksR0FBRyxHQUFHLDBCQUEwQixDQUFDO2dCQUVyQyxNQUFNLENBQUMsSUFBSSxDQUFDLElBQUksQ0FBQztvQkFDYixNQUFNLEVBQUUsTUFBTTtvQkFDZCxHQUFHLEVBQUUsR0FBRztvQkFDUixJQUFJLEVBQUUsRUFBRSxNQUFNLEVBQUUsTUFBTSxFQUFFO2lCQUMzQixDQUFDLENBQUMsSUFBSSxDQUFDLHlCQUF5QixRQUFRO29CQUNyQyxNQUFNLENBQUMsUUFBUSxDQUFDLElBQUksSUFBSSxFQUFFLENBQUM7Z0JBQy9CLENBQUMsRUFBRSx1QkFBdUIsS0FBSztvQkFDM0IsSUFBSSxNQUFNLEdBQUcsQ0FBQyxLQUFLLENBQUMsT0FBTyxDQUFDLEdBQUcsS0FBSyxDQUFDLE9BQU87d0JBQ3hDLEtBQUssQ0FBQyxNQUFNLEdBQU0sS0FBSyxDQUFDLE1BQU0sV0FBTSxLQUFLLENBQUMsVUFBWSxHQUFHLGlEQUFpRCxDQUFDO29CQUMvRyxPQUFPLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUMseUJBQXlCO29CQUNoRCxNQUFNLENBQUMsRUFBRSxDQUFDO2dCQUNkLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUVELHNCQUFzQjtZQUN0QixxQ0FBVyxHQUFYLFVBQVksTUFBYyxFQUFFLFlBQTBCO2dCQUNsRCxJQUFJLEdBQUcsR0FBRyxxQkFBcUIsQ0FBQztnQkFFaEMsTUFBTSxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUM7b0JBQ2IsTUFBTSxFQUFFLE1BQU07b0JBQ2QsR0FBRyxFQUFFLEdBQUc7b0JBQ1IsSUFBSSxFQUFFLElBQUksQ0FBQyxTQUFTLENBQUMsRUFBRSxNQUFNLEVBQUUsTUFBTSxDQUFDLE1BQU0sRUFBRSxXQUFXLEVBQUUsWUFBWSxDQUFDLFdBQVcsRUFBRSxDQUFDO2lCQUN6RixDQUFDLENBQUMsSUFBSSxDQUFDLHlCQUF5QixRQUFRO29CQUNyQyxNQUFNLENBQUMsUUFBUSxDQUFDLElBQUksSUFBSSxFQUFFLENBQUM7Z0JBQy9CLENBQUMsRUFBRSx1QkFBdUIsS0FBSztvQkFDM0IsSUFBSSxNQUFNLEdBQUcsQ0FBQyxLQUFLLENBQUMsT0FBTyxDQUFDLEdBQUcsS0FBSyxDQUFDLE9BQU87d0JBQ3hDLEtBQUssQ0FBQyxNQUFNLEdBQU0sS0FBSyxDQUFDLE1BQU0sV0FBTSxLQUFLLENBQUMsVUFBWSxHQUFHLDRDQUE0QyxDQUFDO29CQUMxRyxPQUFPLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUMseUJBQXlCO29CQUNoRCxNQUFNLENBQUMsRUFBRSxDQUFDO2dCQUNkLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQztZQUNMLHNCQUFDO1FBQUQsQ0EzREEsQUEyREMsSUFBQTtRQTNEWSx3QkFBZSxrQkEyRDNCLENBQUE7SUFDTCxDQUFDLEVBN0RxQixRQUFRLEdBQVIsb0JBQVEsS0FBUixvQkFBUSxRQTZEN0I7QUFBRCxDQUFDLEVBN0RTLFdBQVcsS0FBWCxXQUFXLFFBNkRwQjtBQ2hFRCxxQ0FBcUM7QUFDckMsMkNBQTJDO0FBRTNDLDRDQUE0QztBQUU1QywyQ0FBMkM7QUFDM0Msa0RBQWtEO0FBQ2xELGdEQUFnRDtBQUNoRCwrQ0FBK0M7QUFFL0MsK0NBQStDO0FBQy9DLDZDQUE2QztBQUM3Qyw0Q0FBNEM7QUFFNUMsSUFBSSxTQUFTLEdBQUcsT0FBTyxDQUFDLE1BQU0sQ0FBQyxXQUFXLEVBQUUsQ0FBQyxXQUFXLEVBQUUsV0FBVyxFQUFFLGNBQWMsRUFBRSxXQUFXLENBQUMsQ0FBQyxDQUFDO0FBR3JHLFNBQVMsQ0FBQyxPQUFPLENBQUMsb0JBQW9CLEVBQUUsQ0FBQyxPQUFPLEVBQUUsVUFBQyxLQUFLLElBQUssT0FBQSxJQUFJLFdBQVcsQ0FBQyxRQUFRLENBQUMsa0JBQWtCLENBQUMsS0FBSyxDQUFDLEVBQWxELENBQWtELENBQUMsQ0FBQyxDQUFDO0FBQ2xILFNBQVMsQ0FBQyxPQUFPLENBQUMsa0JBQWtCLEVBQUUsQ0FBQyxPQUFPLEVBQUUsVUFBQyxLQUFLLElBQUssT0FBQSxJQUFJLFdBQVcsQ0FBQyxRQUFRLENBQUMsZ0JBQWdCLENBQUMsS0FBSyxDQUFDLEVBQWhELENBQWdELENBQUMsQ0FBQyxDQUFDO0FBQzlHLFNBQVMsQ0FBQyxPQUFPLENBQUMsaUJBQWlCLEVBQUUsQ0FBQyxPQUFPLEVBQUUsVUFBQyxLQUFLLElBQUssT0FBQSxJQUFJLFdBQVcsQ0FBQyxRQUFRLENBQUMsZUFBZSxDQUFDLEtBQUssQ0FBQyxFQUEvQyxDQUErQyxDQUFDLENBQUMsQ0FBQztBQUU1RyxTQUFTLENBQUMsVUFBVSxDQUFDLGdCQUFnQixFQUFFLENBQUMsVUFBVSxFQUFFLFdBQVcsRUFBRSxVQUFDLFFBQVEsRUFBRSxTQUFTLElBQUssT0FBQSxJQUFJLFdBQVcsQ0FBQyxXQUFXLENBQUMsY0FBYyxDQUFDLFFBQVEsRUFBRSxTQUFTLENBQUMsRUFBL0QsQ0FBK0QsQ0FBQyxDQUFDLENBQUM7QUFFNUosU0FBUyxDQUFDLFVBQVUsQ0FBQyx1QkFBdUIsRUFBRSxDQUFDLG9CQUFvQixFQUFFLFVBQUMsa0JBQWtCLElBQUssT0FBQSxJQUFJLFdBQVcsQ0FBQyxXQUFXLENBQUMscUJBQXFCLENBQUMsa0JBQWtCLENBQUMsRUFBckUsQ0FBcUUsQ0FBQyxDQUFDLENBQUM7QUFFckssU0FBUyxDQUFDLFVBQVUsQ0FBQyxxQkFBcUIsRUFBRSxDQUFDLGtCQUFrQixFQUFFLFVBQUMsZ0JBQWdCO1FBQzlFLE9BQUEsSUFBSSxXQUFXLENBQUMsV0FBVyxDQUFDLG1CQUFtQixDQUFDLGdCQUFnQixDQUFDO0lBQWpFLENBQWlFLENBQUMsQ0FBQyxDQUFDO0FBRXhFLFNBQVMsQ0FBQyxVQUFVLENBQUMsb0JBQW9CLEVBQUUsQ0FBQyxpQkFBaUIsRUFBRSxVQUFDLGVBQWU7UUFDM0UsT0FBQSxJQUFJLFdBQVcsQ0FBQyxXQUFXLENBQUMsa0JBQWtCLENBQUMsZUFBZSxDQUFDO0lBQS9ELENBQStELENBQUMsQ0FBQyxDQUFDO0FBRXRFLFNBQVMsQ0FBQyxTQUFTLENBQUMsUUFBUSxFQUFFLGNBQU0sT0FBQSxJQUFJLFdBQVcsQ0FBQyxVQUFVLENBQUMsTUFBTSxFQUFFLEVBQW5DLENBQW1DLENBQUMsQ0FBQztBQUV6RSxTQUFTLENBQUMsU0FBUyxDQUFDLHNCQUFzQixFQUFFO0lBQzFDLFFBQVEsRUFBRSxFQUNUO0lBQ0QsVUFBVSxFQUFFLCtCQUErQjtJQUMzQyxXQUFXLEVBQUUseUJBQXlCO0NBQ3ZDLENBQUMsQ0FBQztBQUVILFNBQVMsQ0FBQyxTQUFTLENBQUMsb0JBQW9CLEVBQUU7SUFDdEMsUUFBUSxFQUFFLEVBQ1Q7SUFDRCxVQUFVLEVBQUUsNkJBQTZCO0lBQ3pDLFdBQVcsRUFBRSx1QkFBdUI7Q0FDdkMsQ0FBQyxDQUFDO0FBRUgsU0FBUyxDQUFDLFNBQVMsQ0FBQyxtQkFBbUIsRUFBRTtJQUNyQyxRQUFRLEVBQUUsRUFDVDtJQUNELFVBQVUsRUFBRSw0QkFBNEI7SUFDeEMsV0FBVyxFQUFFLHNCQUFzQjtDQUN0QyxDQUFDLENBQUM7QUFFSCxTQUFTLENBQUMsTUFBTSxDQUFDLENBQUMsZ0JBQWdCLEVBQUUsb0JBQW9CLEVBQUUsVUFBUyxjQUFjLEVBQUUsa0JBQWtCO1FBQ2pHLGtCQUFrQixDQUFDLFNBQVMsQ0FBQyxPQUFPLENBQUMsQ0FBQztRQUV0QyxjQUFjO2FBQ1QsS0FBSyxDQUFDLE1BQU0sRUFBRTtZQUNYLEdBQUcsRUFBRSxPQUFPO1lBQ1osUUFBUSxFQUFFLEVBQUU7WUFDWixVQUFVLEVBQUUsZ0JBQWdCO1NBQy9CLENBQUM7YUFDRCxLQUFLLENBQUMsYUFBYSxFQUFFO1lBQ2xCLEdBQUcsRUFBRSxjQUFjO1lBQ25CLFFBQVEsRUFBRSxpREFBaUQ7WUFDM0QsVUFBVSxFQUFFLHVCQUF1QjtTQUN0QyxDQUFDO2FBQ0QsS0FBSyxDQUFDLFdBQVcsRUFBRTtZQUNoQixHQUFHLEVBQUUsWUFBWTtZQUNqQixRQUFRLEVBQUUsNkNBQTZDO1lBQ3ZELFVBQVUsRUFBRSxxQkFBcUI7U0FDcEMsQ0FBQzthQUNELEtBQUssQ0FBQyxVQUFVLEVBQUU7WUFDZixHQUFHLEVBQUUsV0FBVztZQUNoQixRQUFRLEVBQUUsMkNBQTJDO1lBQ3JELFVBQVUsRUFBRSxvQkFBb0I7U0FDbkMsQ0FBQyxDQUFDO0lBQ1gsQ0FBQyxDQUFDLENBQUMsQ0FBQyIsImZpbGUiOiJhcHAuanMiLCJzb3VyY2VzQ29udGVudCI6WyJuYW1lc3BhY2UgQXBwbGljYXRpb24uRGlyZWN0aXZlcyB7XHJcblxyXG4gICAgZXhwb3J0IGNsYXNzIE5hdmJhciB7XHJcblxyXG4gICAgICAgIGNvbnN0cnVjdG9yKCkge1xyXG4gICAgICAgICAgICByZXR1cm4gdGhpcy5jcmVhdGVEaXJlY3RpdmUoKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHByaXZhdGUgY3JlYXRlRGlyZWN0aXZlKCk6IGFueSB7XHJcbiAgICAgICAgICAgIHJldHVybiB7XHJcbiAgICAgICAgICAgICAgICByZXN0cmljdDogXCJFXCIsXHJcbiAgICAgICAgICAgICAgICB0ZW1wbGF0ZVVybDogXCIuL2Rpc3QvbmF2YmFyLmh0bWxcIixcclxuICAgICAgICAgICAgICAgIHNjb3BlOiB7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH07XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG59XHJcbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJhbmd1bGFyLmQudHNcIiAvPlxuXG5cbm5hbWVzcGFjZSBBcHBsaWNhdGlvbi5Db250cm9sbGVycyB7XG4gICAgZXhwb3J0IGNsYXNzIEhvbWVDb250cm9sbGVyIHtcbiAgICAgICAgcHJpdmF0ZSB0aW1lb3V0OiBhbnk7XHRcdFx0XHRcdFx0XHQvLyBTZXJ2aWNlIHRpbWVvdXQgdG8gY2FsbCBvbmNlIGEgZnVuY3Rpb25cbiAgICAgICAgcHJpdmF0ZSBpbnRlcnZhbDogYW55O1x0XHRcdFx0XHRcdC8vIFNlcnZpY2UgaW50ZXJ2YWwgdG8gY2FsbCBjeWNsaWNhbGx5IGEgZnVuY3Rpb25cblxuXG5cbiAgICAgICAgY29uc3RydWN0b3IoJHRpbWVvdXQ6IG5nLklUaW1lb3V0U2VydmljZSwgJGludGVydmFsOiBuZy5JSW50ZXJ2YWxTZXJ2aWNlKSB7XG4gICAgICAgIH1cbiAgICB9XG5cbn1cbiIsImNsYXNzIEZvcmVjYXN0ZXIge1xuICAgIFByb25vc3RpcXVldXI6IG51bWJlcjtcbiAgICBQcm9ub3N0aXF1ZXVyc19Ob21VdGlsaXNhdGV1cjogc3RyaW5nO1xuICAgIFByb25vc3RpcXVldXJzX05vbTogc3RyaW5nO1xuICAgIFByb25vc3RpcXVldXJzX1ByZW5vbTogc3RyaW5nO1xuICAgIFByb25vc3RpcXVldXJzX1Bob3RvOiBzdHJpbmc7XG4gICAgUHJvbm9zdGlxdWV1cnNfQWRtaW5pc3RyYXRldXI6IG51bWJlcjtcbiAgICBQcm9ub3N0aXF1ZXVyc19NRUw6IHN0cmluZztcbiAgICBQcm9ub3N0aXF1ZXVyc19Nb3REZVBhc3NlOiBzdHJpbmc7XG4gICAgUHJvbm9zdGlxdWV1cnNfUHJlbWllcmVDb25uZXhpb246IG51bWJlcjtcbiAgICBQcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2U6IERhdGU7XG4gICAgUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2U6IERhdGU7XG4gICAgUHJvbm9zdGlxdWV1cnNfRGF0ZUZpblByZXNlbmNlOiBEYXRlO1xuICAgIFByb25vc3RpcXVldXJzX0xpZXVEZVJlc2lkZW5jZTogc3RyaW5nO1xuICAgIFByb25vc3RpcXVldXJzX0FtYml0aW9uczogc3RyaW5nO1xuICAgIFByb25vc3RpcXVldXJzX1BhbG1hcmVzOiBzdHJpbmc7XG4gICAgUHJvbm9zdGlxdWV1cnNfQ2FycmllcmU6IHN0cmluZztcbiAgICBQcm9ub3N0aXF1ZXVyc19Db21tZW50YWlyZTogc3RyaW5nO1xuICAgIFByb25vc3RpcXVldXJzX0VxdWlwZUZhdm9yaXRlOiBzdHJpbmc7XG4gICAgUHJvbm9zdGlxdWV1cnNfQ29kZUNvdWxldXI6IHN0cmluZztcbiAgICBUaGVtZXNfVGhlbWU6IG51bWJlcjtcblxuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLmluaXRGaWVsZHMoKTtcbiAgICB9XG5cbiAgICBpbml0RmllbGRzKCkge1xuICAgICAgICB0aGlzLlByb25vc3RpcXVldXJzX05vbVV0aWxpc2F0ZXVyID0gXCJcIjtcbiAgICAgICAgdGhpcy5Qcm9ub3N0aXF1ZXVyc19Ob20gPSBcIlwiO1xuICAgICAgICB0aGlzLlByb25vc3RpcXVldXJzX1ByZW5vbSA9IFwiXCI7XG4gICAgICAgIHRoaXMuUHJvbm9zdGlxdWV1cnNfUGhvdG8gPSBcIlwiO1xuICAgICAgICB0aGlzLlByb25vc3RpcXVldXJzX0FkbWluaXN0cmF0ZXVyID0gMDtcbiAgICAgICAgdGhpcy5Qcm9ub3N0aXF1ZXVyc19NRUwgPSBcIlwiO1xuICAgICAgICB0aGlzLlByb25vc3RpcXVldXJzX01vdERlUGFzc2UgPSBcIlwiO1xuICAgICAgICB0aGlzLlByb25vc3RpcXVldXJzX1ByZW1pZXJlQ29ubmV4aW9uID0gMTtcbiAgICAgICAgdGhpcy5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UgPSBudWxsO1xuICAgICAgICB0aGlzLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlID0gbnVsbDtcbiAgICAgICAgdGhpcy5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgPSBudWxsO1xuICAgICAgICB0aGlzLlByb25vc3RpcXVldXJzX0xpZXVEZVJlc2lkZW5jZSA9IFwiXCI7XG4gICAgICAgIHRoaXMuUHJvbm9zdGlxdWV1cnNfQW1iaXRpb25zID0gXCJcIjtcbiAgICAgICAgdGhpcy5Qcm9ub3N0aXF1ZXVyc19QYWxtYXJlcyA9IFwiXCI7XG4gICAgICAgIHRoaXMuUHJvbm9zdGlxdWV1cnNfQ2FycmllcmUgPSBcIlwiO1xuICAgICAgICB0aGlzLlByb25vc3RpcXVldXJzX0NvbW1lbnRhaXJlID0gXCJcIjtcbiAgICAgICAgdGhpcy5Qcm9ub3N0aXF1ZXVyc19FcXVpcGVGYXZvcml0ZSA9IFwiXCI7XG4gICAgICAgIHRoaXMuUHJvbm9zdGlxdWV1cnNfQ29kZUNvdWxldXIgPSBcIlwiO1xuICAgICAgICB0aGlzLlRoZW1lc19UaGVtZSA9IDE7XG4gICAgfVxufVxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cImFuZ3VsYXIuZC50c1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibW9kZWxzL2ZvcmVjYXN0ZXIudHNcIiAvPlxuXG5uYW1lc3BhY2UgQXBwbGljYXRpb24uQ29udHJvbGxlcnMge1xuICAgIGV4cG9ydCBjbGFzcyBGb3JlY2FzdGVyc0NvbnRyb2xsZXIge1xuICAgICAgICBwcml2YXRlIGZvcmVjYXN0ZXJzOiBhbnlbXTtcbiAgICAgICAgcHJpdmF0ZSBzZXJ2aWNlOiBhbnk7XG4gICAgICAgIHByaXZhdGUgY3VycmVudEZvcmVjYXN0ZXI6IEZvcmVjYXN0ZXI7XG4gICAgICAgIHByaXZhdGUgaGFzQmVlbk1vZGlmaWVkOiBib29sZWFuO1xuICAgICAgICBwcml2YXRlIGlzTW92YWJsZTogYm9vbGVhbjtcbiAgICAgICAgcHJpdmF0ZSBpc0RlbGV0YWJsZTogYm9vbGVhbjtcbiAgICAgICAgcHJpdmF0ZSBjdXJyZW50Rm9yZWNhc3RlckluZGV4OiBudW1iZXI7XG4gICAgICAgIHByaXZhdGUgaXNJbkNyZWF0aW9uTW9kZTogYm9vbGVhbjtcbiAgICAgICAgcHJpdmF0ZSBiaXJ0aGRheUNhbGVuZGFyID0ge1xuICAgICAgICAgICAgb3BlbmVkOiBmYWxzZVxuICAgICAgICB9O1xuXG4gICAgICAgIHByaXZhdGUgYmVnaW5EYXRlQ2FsZW5kYXIgPSB7XG4gICAgICAgICAgICBvcGVuZWQ6IGZhbHNlXG4gICAgICAgIH07XG5cbiAgICAgICAgcHJpdmF0ZSBlbmREYXRlQ2FsZW5kYXIgPSB7XG4gICAgICAgICAgICBvcGVuZWQ6IGZhbHNlXG4gICAgICAgIH07XG5cbiAgICAgICAgcHJpdmF0ZSBkYXRlT3B0aW9ucyA9IHtcbiAgICAgICAgICAgIGZvcm1hdFllYXI6IFwieXl5eVwiLFxuICAgICAgICAgICAgbWF4RGF0ZTogbmV3IERhdGUoMjAyMCwgNSwgMjIpLFxuICAgICAgICAgICAgbWluRGF0ZTogbmV3IERhdGUoMTkyMCwgMSwgMSksXG4gICAgICAgICAgICBzdGFydGluZ0RheTogMVxuICAgICAgICB9O1xuXG5cbiAgICAgICAgY29uc3RydWN0b3IoZm9yZWNhc3RlcnNTZXJ2aWNlOiBhbnkpIHtcbiAgICAgICAgICAgIHRoaXMuc2VydmljZSA9IGZvcmVjYXN0ZXJzU2VydmljZTtcbiAgICAgICAgICAgIHRoaXMuaGFzQmVlbk1vZGlmaWVkID0gZmFsc2U7XG4gICAgICAgICAgICB0aGlzLmlzTW92YWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgdGhpcy5pc0RlbGV0YWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgdGhpcy5pc0luQ3JlYXRpb25Nb2RlID0gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICAkb25Jbml0KCkge1xuICAgICAgICAgICAgdGhpcy5zZXJ2aWNlLmdldEZvcmVjYXN0ZXJzKCkudGhlbigoZm9yZWNhc3RlcnMpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcmVjYXN0ZXJzID0gZm9yZWNhc3RlcnM7XG4gICAgICAgICAgICB9LCAoZXJyKSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coXCJGb3JlY2FzdGVyc0NvbnRyb2xsZXIgJG9uSW5pdCgpOiBFcnJvciBkdXJpbmcgcmVhZGluZ1wiKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgLyogTG9hZCB0aGUgZm9yZWNhc3RlciB0byB0aGUgZWRpdCBmb3JtICovXG4gICAgICAgIGVkaXRGb3JlY2FzdGVyKGZvcmVjYXN0ZXI6IGFueSwgaW5kZXg6IG51bWJlcik6IHZvaWQge1xuICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3RlciA9IGFuZ3VsYXIuY29weShmb3JlY2FzdGVyKTtcbiAgICAgICAgICAgIHRoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleCA9IGluZGV4O1xuICAgICAgICAgICAgdGhpcy5pc01vdmFibGUgPSB0cnVlO1xuICAgICAgICAgICAgdGhpcy5pc0RlbGV0YWJsZSA9IHRydWU7XG5cbiAgICAgICAgICAgIC8qIFJlZm9ybWF0IHRoZSBTUUwgZGF0ZSB0byBKYXZhc2NyaXB0IGRhdGUgKi9cbiAgICAgICAgICAgIGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSAhPT0gbnVsbClcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UudG9TdHJpbmcoKSAhPT0gXCIwLzAvMFwiKVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSA9IG5ldyBEYXRlKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlLnRvU3RyaW5nKCkpO1xuXG4gICAgICAgICAgICAvKiBSZWZvcm1hdCB0aGUgU1FMIGRhdGUgdG8gSmF2YXNjcmlwdCBkYXRlICovXG4gICAgICAgICAgICBpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZSAhPT0gbnVsbClcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZS50b1N0cmluZygpICE9PSBcIjAvMC8wXCIpXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UgPSBuZXcgRGF0ZSh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlLnRvU3RyaW5nKCkpO1xuXG4gICAgICAgICAgICAvKiBSZWZvcm1hdCB0aGUgU1FMIGRhdGUgdG8gSmF2YXNjcmlwdCBkYXRlICovXG4gICAgICAgICAgICBpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgIT09IG51bGwpXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZUZpblByZXNlbmNlLnRvU3RyaW5nKCkgIT09IFwiMC8wLzBcIilcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgPSBuZXcgRGF0ZSh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZS50b1N0cmluZygpKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIE9wZW4gdGhlIFVJIGRhdGUgcGlja2VyIGRpYWxvZyAqL1xuICAgICAgICBvcGVuQmlydGhkYXlDYWxlbmRhcigpOiB2b2lkIHtcbiAgICAgICAgICAgIGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSA9PT0gbnVsbCB8fCB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZS50b1N0cmluZygpID09PSBcIjAvMC8wXCIpXG4gICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UgPSBuZXcgRGF0ZSgpO1xuXG4gICAgICAgICAgICB0aGlzLmJpcnRoZGF5Q2FsZW5kYXIub3BlbmVkID0gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIE9wZW4gdGhlIFVJIGRhdGUgcGlja2VyIGRpYWxvZyAqL1xuICAgICAgICBvcGVuQmVnaW5EYXRlQ2FsZW5kYXIoKTogdm9pZCB7XG4gICAgICAgICAgICBpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZSA9PT0gbnVsbCB8fCB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlLnRvU3RyaW5nKCkgPT09IFwiMC8wLzBcIilcbiAgICAgICAgICAgICAgICB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlID0gbmV3IERhdGUoKTtcblxuICAgICAgICAgICAgdGhpcy5iZWdpbkRhdGVDYWxlbmRhci5vcGVuZWQgPSB0cnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgLyogT3BlbiB0aGUgVUkgZGF0ZSBwaWNrZXIgZGlhbG9nICovXG4gICAgICAgIG9wZW5FbmREYXRlQ2FsZW5kYXIoKTogdm9pZCB7XG4gICAgICAgICAgICBpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgPT09IG51bGwgfHwgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UudG9TdHJpbmcoKSA9PT0gXCIwLzAvMFwiKVxuICAgICAgICAgICAgICAgIHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZUZpblByZXNlbmNlID0gbmV3IERhdGUoKTtcblxuICAgICAgICAgICAgdGhpcy5lbmREYXRlQ2FsZW5kYXIub3BlbmVkID0gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIEFkZCBhIG5ldyBmb3JlY2FzdGVyIG9yIHNhdmUgdGhlIG1vZGlmaWNhdGlvbnMgbWFkZSBvbiBhbiBleGlzdGluZyBmb3JlY2FzdGVyICovXG4gICAgICAgIHNhdmVNb2RpZmljYXRpb25zKCk6IHZvaWQge1xuICAgICAgICAgICAgaWYgKHRoaXMuaXNJbkNyZWF0aW9uTW9kZSA9PT0gdHJ1ZSkge1xuICAgICAgICAgICAgICAgIHRoaXMuaGFzQmVlbk1vZGlmaWVkID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgdGhpcy5pc0luQ3JlYXRpb25Nb2RlID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgdGhpcy5mb3JlY2FzdGVycy5wdXNoKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIpO1xuICAgICAgICAgICAgICAgIHRoaXMuc2VydmljZS5jcmVhdGVGb3JlY2FzdGVyKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIpLnRoZW4oKGRhdGEpID0+IHtcbiAgICAgICAgICAgICAgICB9LCAoZXJyKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKFwiRXJyb3IgZHVyaW5nIGNyZWF0aW9uXCIpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhpcy5mb3JlY2FzdGVyc1t0aGlzLmN1cnJlbnRGb3JlY2FzdGVySW5kZXhdID0gYW5ndWxhci5jb3B5KHRoaXMuY3VycmVudEZvcmVjYXN0ZXIpO1xuICAgICAgICAgICAgICAgIHRoaXMuaGFzQmVlbk1vZGlmaWVkID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgdGhpcy5zZXJ2aWNlLnVwZGF0ZUZvcmVjYXN0ZXIodGhpcy5jdXJyZW50Rm9yZWNhc3RlcikudGhlbigoZGF0YSkgPT4ge1xuICAgICAgICAgICAgICAgIH0sIChlcnIpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coXCJFcnJvciBkdXJpbmcgdXBkYXRlXCIpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvKiBDYW5jZWwgdGhlIGNyZWF0aW9uIG9mIGEgbmV3IGZvcmVjYXN0ZXIgb3IgdGhlIG1vZGlmaWNhdGlvbnMgbWFkZSBvbiBhbiBleGlzdGluZyBmb3JlY2FzdGVyICovXG4gICAgICAgIGNhbmNlbE1vZGlmaWNhdGlvbnMoKTogdm9pZCB7XG4gICAgICAgICAgICBpZiAodGhpcy5pc0luQ3JlYXRpb25Nb2RlID09PSB0cnVlKSB7XG4gICAgICAgICAgICAgICAgLy8gSW4gY3JlYXRpb24gbW9kZSwgdGhlIGRhdGEgZG9lc25cInQgY29tZSBmcm9tIHRoZSBmb3JlY2FzdGVycyBhcnJheVxuICAgICAgICAgICAgICAgIHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuaW5pdEZpZWxkcygpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3RlciA9IGFuZ3VsYXIuY29weSh0aGlzLmZvcmVjYXN0ZXJzW3RoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleF0pO1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSAhPT0gbnVsbClcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UgPSBuZXcgRGF0ZSh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZS50b1N0cmluZygpKTtcblxuICAgICAgICAgICAgICAgIGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlICE9PSBudWxsKVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlID0gbmV3IERhdGUodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZS50b1N0cmluZygpKTtcblxuICAgICAgICAgICAgICAgIGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZSAhPT0gbnVsbClcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgPSBuZXcgRGF0ZSh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZS50b1N0cmluZygpKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5oYXNCZWVuTW9kaWZpZWQgPSBmYWxzZTtcbiAgICAgICAgICAgIHRoaXMuaXNJbkNyZWF0aW9uTW9kZSA9IGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgLyogSW5kaWNhdGVzIHRoYXQgYSBtb2RpZmljYXRpb24gaGFzIGJlZW4gbWFkZSAqL1xuICAgICAgICBzZXRNb2RpZmllZE9uKCk6IHZvaWQge1xuICAgICAgICAgICAgaWYgKHRoaXMuaXNJbkNyZWF0aW9uTW9kZSA9PT0gZmFsc2UpXG4gICAgICAgICAgICAgICAgdGhpcy5oYXNCZWVuTW9kaWZpZWQgPSB0cnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgLyogSW5kaWNhdGVzIHRoYXQgYSBtb2RpZmljYXRpb24gaGFzIGJlZW4gbWFkZSBvbiBiaXJ0aGRheSAqL1xuICAgICAgICAvKiBJdFwicyBtb3JlIGNvbXBsZXggYmVjYXVzZSBpdFwicyBuZWNlc3NhcnkgdG8gdGhpbmsgYWJvdXQgdGhlIFVJQiBkYXRlcGlja2VyIHdpZGdldCovXG4gICAgICAgIGNoZWNrQmlydGhkYXlJc01vZGlmaWVkKCk6IHZvaWQge1xuICAgICAgICAgICAgaWYgKHRoaXMuaXNJbkNyZWF0aW9uTW9kZSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5mb3JlY2FzdGVyc1t0aGlzLmN1cnJlbnRGb3JlY2FzdGVySW5kZXhdLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSAhPT0gXCIwLzAvMFwiICYmIHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlICE9PSB0aGlzLmZvcmVjYXN0ZXJzW3RoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleF0uUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlKVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmhhc0JlZW5Nb2RpZmllZCA9IHRydWU7XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5mb3JlY2FzdGVyc1t0aGlzLmN1cnJlbnRGb3JlY2FzdGVySW5kZXhdLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSA9PT0gXCIwLzAvMFwiICYmIHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlICE9PSBudWxsKVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmhhc0JlZW5Nb2RpZmllZCA9IHRydWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvKiBJbmRpY2F0ZXMgdGhhdCBhIG1vZGlmaWNhdGlvbiBoYXMgYmVlbiBtYWRlIG9uIGJlZ2luIGRhdGUgKi9cbiAgICAgICAgLyogSXRcInMgbW9yZSBjb21wbGV4IGJlY2F1c2UgaXRcInMgbmVjZXNzYXJ5IHRvIHRoaW5rIGFib3V0IHRoZSBVSUIgZGF0ZXBpY2tlciB3aWRnZXQqL1xuICAgICAgICBjaGVja0JlZ2luRGF0ZUlzTW9kaWZpZWQoKTogdm9pZCB7XG4gICAgICAgICAgICBpZiAodGhpcy5pc0luQ3JlYXRpb25Nb2RlID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLmZvcmVjYXN0ZXJzW3RoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleF0uUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UgIT09IFwiMC8wLzBcIiAmJiB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlICE9PSB0aGlzLmZvcmVjYXN0ZXJzW3RoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleF0uUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UpXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaGFzQmVlbk1vZGlmaWVkID0gdHJ1ZTtcblxuICAgICAgICAgICAgICAgIGlmICh0aGlzLmZvcmVjYXN0ZXJzW3RoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleF0uUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UgPT09IFwiMC8wLzBcIiAmJiB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlICE9PSBudWxsKVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmhhc0JlZW5Nb2RpZmllZCA9IHRydWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvKiBJbmRpY2F0ZXMgdGhhdCBhIG1vZGlmaWNhdGlvbiBoYXMgYmVlbiBtYWRlIG9uIGVuZCBkYXRlICovXG4gICAgICAgIC8qIEl0XCJzIG1vcmUgY29tcGxleCBiZWNhdXNlIGl0XCJzIG5lY2Vzc2FyeSB0byB0aGluayBhYm91dCB0aGUgVUlCIGRhdGVwaWNrZXIgd2lkZ2V0Ki9cbiAgICAgICAgY2hlY2tFbmREYXRlSXNNb2RpZmllZCgpOiB2b2lkIHtcbiAgICAgICAgICAgIGlmICh0aGlzLmlzSW5DcmVhdGlvbk1vZGUgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgIT09IFwiMC8wLzBcIiAmJiB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZSAhPT0gdGhpcy5mb3JlY2FzdGVyc1t0aGlzLmN1cnJlbnRGb3JlY2FzdGVySW5kZXhdLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZSlcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5oYXNCZWVuTW9kaWZpZWQgPSB0cnVlO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgPT09IFwiMC8wLzBcIiAmJiB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZSAhPT0gbnVsbClcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5oYXNCZWVuTW9kaWZpZWQgPSB0cnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cblxuICAgICAgICAvKiBDcmVhdGUgYSBuZXcgZm9yZWNhc3RlciAqL1xuICAgICAgICBjcmVhdGVGb3JlY2FzdGVyKCk6IHZvaWQge1xuICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3RlciA9IG5ldyBGb3JlY2FzdGVyKCk7XG4gICAgICAgICAgICB0aGlzLmhhc0JlZW5Nb2RpZmllZCA9IHRydWU7XG4gICAgICAgICAgICB0aGlzLmlzTW92YWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgdGhpcy5pc0RlbGV0YWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgdGhpcy5pc0luQ3JlYXRpb25Nb2RlID0gdHJ1ZTtcblxuICAgICAgICB9XG5cbiAgICAgICAgLyogTW92ZSBhIGZvcmVjYXN0ZXIgdG8gdGhlIHByZXZpb3VzIGZvcmVjYXN0ZXJzIGxpc3QgKi9cbiAgICAgICAgbW92ZUZvcmVjYXN0ZXIoKTogdm9pZCB7XG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UubW92ZUZvcmVjYXN0ZXIodGhpcy5jdXJyZW50Rm9yZWNhc3RlcikudGhlbigoZGF0YSkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuZm9yZWNhc3RlcnMuc3BsaWNlKHRoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleCwgMSk7XG4gICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4ID0gbnVsbDtcbiAgICAgICAgICAgICAgICB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyID0gbnVsbDtcbiAgICAgICAgICAgICAgICB0aGlzLmlzTW92YWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIHRoaXMuaXNEZWxldGFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgIH0sIChlcnIpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhcIkVycm9yIGR1cmluZyBtb3ZlXCIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgICAgICAvKiBNb3ZlIGEgZm9yZWNhc3RlciB0byB0aGUgcHJldmlvdXMgZm9yZWNhc3RlcnMgbGlzdCAqL1xuICAgICAgICBkZWxldGVGb3JlY2FzdGVyKCk6IHZvaWQge1xuICAgICAgICAgICAgdGhpcy5zZXJ2aWNlLmRlbGV0ZUZvcmVjYXN0ZXIodGhpcy5jdXJyZW50Rm9yZWNhc3RlcikudGhlbigoZGF0YSkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuZm9yZWNhc3RlcnMuc3BsaWNlKHRoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleCwgMSk7XG4gICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4ID0gbnVsbDtcbiAgICAgICAgICAgICAgICB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyID0gbnVsbDtcbiAgICAgICAgICAgICAgICB0aGlzLmlzTW92YWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIHRoaXMuaXNEZWxldGFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgIH0sIChlcnIpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhcIkVycm9yIGR1cmluZyBkZWxldGVcIik7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH1cbn1cbiIsImNsYXNzIFNlYXNvbiB7XG4gICAgU2Fpc29uOiBudW1iZXI7XG5cbiAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgdGhpcy5pbml0RmllbGRzKCk7XG4gICAgfVxuXG4gICAgaW5pdEZpZWxkcygpIHtcbiAgICB9XG59XG4iLCJjbGFzcyBDaGFtcGlvbnNoaXAge1xuICAgIENoYW1waW9ubmF0OiBudW1iZXI7XG4gICAgQ2hhbXBpb25uYXRzX05vbUNvdXJ0OiBzdHJpbmc7XG5cbiAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgdGhpcy5pbml0RmllbGRzKCk7XG4gICAgfVxuXG4gICAgaW5pdEZpZWxkcygpIHtcbiAgICB9XG59IiwiY2xhc3MgV2VlayB7XG4gICAgSm91cm5lZTogbnVtYmVyO1xuICAgIEpvdXJuZWVzX05vbUNvdXJ0OiBzdHJpbmc7XG4gICAgQ2xhc3NlbWVudHNfRGF0ZVJlZmVyZW5jZTogRGF0ZTtcblxuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLmluaXRGaWVsZHMoKTtcbiAgICB9XG5cbiAgICBpbml0RmllbGRzKCkge1xuICAgIH1cbn0iLCJjbGFzcyBTdGFuZGluZyB7XG4gICAgU2Fpc29uc19TYWlzb246IG51bWJlcjtcbiAgICBKb3VybmVlc19Kb3VybmVlOiBudW1iZXI7XG4gICAgQ2xhc3NlbWVudHNfRGF0ZVJlZmVyZW5jZTogc3RyaW5nO1xuXG4gICAgQ2xhc3NlbWVudHNfQ2xhc3NlbWVudEdlbmVyYWxNYXRjaDogbnVtYmVyO1xuICAgIENsYXNzZW1lbnRzX0NsYXNzZW1lbnRHZW5lcmFsQnV0ZXVyOiBudW1iZXI7XG5cbiAgICBDbGFzc2VtZW50c19Qb2ludHNHZW5lcmFsTWF0Y2g6IG51bWJlcjtcbiAgICBDbGFzc2VtZW50c19Qb2ludHNHZW5lcmFsQnV0ZXVyOiBudW1iZXI7XG5cbiAgICBQcm9ub3N0aXF1ZXVyOiBudW1iZXI7XG4gICAgUHJvbm9zdGlxdWV1cnNfTm9tVXRpbGlzYXRldXI6IHN0cmluZztcbiAgICBQcm9ub3N0aXF1ZXVyc19QaG90bzogc3RyaW5nO1xufVxuXG5cbmNsYXNzIFN0YW5kaW5nV2VlayB7XG4gICAgU2Fpc29uc19TYWlzb246IG51bWJlcjtcbiAgICBKb3VybmVlc19Kb3VybmVlOiBudW1iZXI7XG4gICAgQ2xhc3NlbWVudHNfRGF0ZVJlZmVyZW5jZTogc3RyaW5nO1xuXG4gICAgQ2xhc3NlbWVudHNfQ2xhc3NlbWVudEpvdXJuZWVNYXRjaDogbnVtYmVyO1xuICAgIENsYXNzZW1lbnRzX0NsYXNzZW1lbnRKb3VybmVlQnV0ZXVyOiBudW1iZXI7XG5cbiAgICBDbGFzc2VtZW50c19Qb2ludHNKb3VybmVlTWF0Y2g6IG51bWJlcjtcbiAgICBDbGFzc2VtZW50c19Qb2ludHNKb3VybmVlQnV0ZXVyOiBudW1iZXI7XG5cbiAgICBQcm9ub3N0aXF1ZXVyOiBudW1iZXI7XG4gICAgUHJvbm9zdGlxdWV1cnNfTm9tVXRpbGlzYXRldXI6IHN0cmluZztcbiAgICBQcm9ub3N0aXF1ZXVyc19QaG90bzogc3RyaW5nO1xufVxuXG5cbmNsYXNzIFN0YW5kaW5nR29hbCB7XG4gICAgU2Fpc29uc19TYWlzb246IG51bWJlcjtcbiAgICBKb3VybmVlc19Kb3VybmVlOiBudW1iZXI7XG4gICAgQ2xhc3NlbWVudHNfRGF0ZVJlZmVyZW5jZTogc3RyaW5nO1xuXG4gICAgQ2xhc3NlbWVudHNfQ2xhc3NlbWVudEdlbmVyYWxCdXRldXI6IG51bWJlcjtcbiAgICBDbGFzc2VtZW50c19Qb2ludHNHZW5lcmFsQnV0ZXVyOiBudW1iZXI7XG5cbiAgICBQcm9ub3N0aXF1ZXVyOiBudW1iZXI7XG4gICAgUHJvbm9zdGlxdWV1cnNfTm9tVXRpbGlzYXRldXI6IHN0cmluZztcbiAgICBQcm9ub3N0aXF1ZXVyc19QaG90bzogc3RyaW5nO1xufVxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cImFuZ3VsYXIuZC50c1wiIC8+XHJcbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJtb2RlbHMvc2Vhc29uLnRzXCIgLz5cclxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIm1vZGVscy9jaGFtcGlvbnNoaXAudHNcIiAvPlxyXG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibW9kZWxzL3dlZWsudHNcIiAvPlxyXG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibW9kZWxzL3N0YW5kaW5nLnRzXCIgLz5cclxuXHJcblxyXG5uYW1lc3BhY2UgQXBwbGljYXRpb24uQ29udHJvbGxlcnMge1xyXG4gICAgZXhwb3J0IGNsYXNzIFN0YW5kaW5nc0NvbnRyb2xsZXIge1xyXG4gICAgICAgIHByaXZhdGUgc2Vhc29uczogYW55W107XHJcbiAgICAgICAgcHJpdmF0ZSBjaGFtcGlvbnNoaXBzOiBhbnlbXTtcclxuICAgICAgICBwcml2YXRlIHdlZWtzOiBhbnlbXTtcclxuICAgICAgICBwcml2YXRlIHN0YW5kaW5nczogYW55W107XHJcbiAgICAgICAgcHJpdmF0ZSBzdGFuZGluZ3NXZWVrOiBhbnlbXTtcclxuICAgICAgICBwcml2YXRlIHN0YW5kaW5nc0dvYWw6IGFueVtdO1xyXG5cclxuXHJcbiAgICAgICAgcHJpdmF0ZSBjdXJyZW50U2Vhc29uOiBTZWFzb247XHJcbiAgICAgICAgcHJpdmF0ZSBjdXJyZW50Q2hhbXBpb25zaGlwOiBDaGFtcGlvbnNoaXA7XHJcbiAgICAgICAgcHJpdmF0ZSBjdXJyZW50V2VlazogV2VlaztcclxuICAgICAgICBwcml2YXRlIGN1cnJlbnRSZWZlcmVuY2VEYXRlOiBhbnk7XHJcblxyXG4gICAgICAgIHByaXZhdGUgc2VydmljZTogYW55O1xyXG5cclxuICAgICAgICBjb25zdHJ1Y3RvcihzdGFuZGluZ3NTZXJ2aWNlOiBhbnkpIHtcclxuICAgICAgICAgICAgdGhpcy5zZXJ2aWNlID0gc3RhbmRpbmdzU2VydmljZTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgICRvbkluaXQoKSB7XHJcbiAgICAgICAgICAgIC8vIEdldCBhbGwgc2Vhc29uc1xyXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0U2Vhc29ucygpLnRoZW4oKHNlYXNvbnMpID0+IHtcclxuICAgICAgICAgICAgICAgIHRoaXMuc2Vhc29ucyA9IHNlYXNvbnM7XHJcbiAgICAgICAgICAgIH0sIChlcnIpID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKFwiU3RhbmRpbmdzQ29udHJvbGxlciAkb25Jbml0KCk6IEVycm9yIGR1cmluZyByZWFkaW5nIHNlYXNvbnNcIik7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLyogU2VsZWN0IGEgc2Vhc29uICovXHJcbiAgICAgICAgc2VsZWN0U2Vhc29uKHNlYXNvbjogU2Vhc29uKTogdm9pZCB7XHJcbiAgICAgICAgICAgIGlmICh0aGlzLmN1cnJlbnRTZWFzb24gPT09IHNlYXNvbilcclxuICAgICAgICAgICAgICAgIHJldHVybjtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuY3VycmVudFNlYXNvbiA9IHNlYXNvbjtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuY2hhbXBpb25zaGlwcyA9IFtdO1xyXG4gICAgICAgICAgICB0aGlzLndlZWtzID0gW107XHJcbiAgICAgICAgICAgIHRoaXMuc3RhbmRpbmdzID0gW107XHJcblxyXG4gICAgICAgICAgICAvLyBHZXQgYWxsIGV4aXN0aW5nIGNoYW1waW9uc2hpcHMgZXhjZXB0IHRoZSBGcmVuY2ggQ3VwIGZvciB0aGUgc2VsZWN0ZWQgc2Vhc29uXHJcbiAgICAgICAgICAgIHRoaXMuc2VydmljZS5nZXRDaGFtcGlvbnNoaXBzKHRoaXMuY3VycmVudFNlYXNvbikudGhlbigoY2hhbXBpb25zaGlwcykgPT4ge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5jaGFtcGlvbnNoaXBzID0gY2hhbXBpb25zaGlwcztcclxuICAgICAgICAgICAgfSwgKGVycikgPT4ge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coXCJTdGFuZGluZ3NDb250cm9sbGVyIHNlbGVjdFNlYXNvbigpOiBFcnJvciBkdXJpbmcgcmVhZGluZyBjaGFtcGlvbnNoaXBzXCIpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8qIFNlbGVjdCBhIGNoYW1waW9uc2hpcCAqL1xyXG4gICAgICAgIHNlbGVjdENoYW1waW9uc2hpcChjaGFtcGlvbnNoaXA6IENoYW1waW9uc2hpcCk6IHZvaWQge1xyXG4gICAgICAgICAgICB0aGlzLmN1cnJlbnRDaGFtcGlvbnNoaXAgPSBjaGFtcGlvbnNoaXA7XHJcblxyXG4gICAgICAgICAgICAvKiBTZWxlY3QgYWxsIHdlZWtzIGZvciB0aGF0IGNoYW1waW9uc2hpcCAqL1xyXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0V2Vla3ModGhpcy5jdXJyZW50U2Vhc29uLCB0aGlzLmN1cnJlbnRDaGFtcGlvbnNoaXApLnRoZW4oKHdlZWtzKSA9PiB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLndlZWtzID0gd2Vla3M7XHJcbiAgICAgICAgICAgICAgICB0aGlzLnN0YW5kaW5ncyA9IFtdO1xyXG4gICAgICAgICAgICB9LCAoZXJyKSA9PiB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhcIlN0YW5kaW5nc0NvbnRyb2xsZXIgc2VsZWN0Q2hhbXBpb25zaGlwKCk6IEVycm9yIGR1cmluZyByZWFkaW5nIHdlZWtzXCIpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8qIFNlbGVjdCBhIHdlZWsgYW5kIGEgcmVmZXJlbmNlIGRhdGUgKi9cclxuICAgICAgICBzZWxlY3RXZWVrKHdlZWs6IFdlZWssIHJlZmVyZW5jZURhdGU6IHN0cmluZyk6IGFueSB7XHJcbiAgICAgICAgICAgIHRoaXMuY3VycmVudFdlZWsgPSB3ZWVrO1xyXG4gICAgICAgICAgICB0aGlzLmN1cnJlbnRSZWZlcmVuY2VEYXRlID0gcmVmZXJlbmNlRGF0ZTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuc2VydmljZS5nZXRTdGFuZGluZ3ModGhpcy5jdXJyZW50U2Vhc29uLCB0aGlzLmN1cnJlbnRXZWVrLCB0aGlzLmN1cnJlbnRSZWZlcmVuY2VEYXRlKS50aGVuKChzdGFuZGluZ3MpID0+IHtcclxuICAgICAgICAgICAgICAgIHRoaXMuc3RhbmRpbmdzID0gc3RhbmRpbmdzO1xyXG4gICAgICAgICAgICB9LCAoZXJyKSA9PiB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhcIlN0YW5kaW5nc0NvbnRyb2xsZXIgJG9uSW5pdCgpOiBFcnJvciBkdXJpbmcgcmVhZGluZyBzdGFuZGluZ3NcIik7XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5zZXJ2aWNlLmdldFN0YW5kaW5nc1dlZWsodGhpcy5jdXJyZW50U2Vhc29uLCB0aGlzLmN1cnJlbnRXZWVrLCB0aGlzLmN1cnJlbnRSZWZlcmVuY2VEYXRlKS50aGVuKChzdGFuZGluZ3NXZWVrKSA9PiB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLnN0YW5kaW5nc1dlZWsgPSBzdGFuZGluZ3NXZWVrO1xyXG4gICAgICAgICAgICB9LCAoZXJyKSA9PiB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhcIlN0YW5kaW5nc0NvbnRyb2xsZXIgJG9uSW5pdCgpOiBFcnJvciBkdXJpbmcgcmVhZGluZyBzdGFuZGluZ3Mgd2Vla1wiKTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0U3RhbmRpbmdzR29hbCh0aGlzLmN1cnJlbnRTZWFzb24sIHRoaXMuY3VycmVudFdlZWssIHRoaXMuY3VycmVudFJlZmVyZW5jZURhdGUpLnRoZW4oKHN0YW5kaW5nc0dvYWwpID0+IHtcclxuICAgICAgICAgICAgICAgIHRoaXMuc3RhbmRpbmdzR29hbCA9IHN0YW5kaW5nc0dvYWw7XHJcbiAgICAgICAgICAgIH0sIChlcnIpID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKFwiU3RhbmRpbmdzQ29udHJvbGxlciAkb25Jbml0KCk6IEVycm9yIGR1cmluZyByZWFkaW5nIHN0YW5kaW5ncyBnb2FsXCIpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcbn1cclxuIiwiY2xhc3MgVHJvcGh5IHtcbiAgICBQcm9ub3N0aXF1ZXVyOiBudW1iZXI7XG4gICAgUHJvbm9zdGlxdWV1cnNfTm9tVXRpbGlzYXRldXI6IHN0cmluZztcblxuICAgIFRyb3BoZWU6IG51bWJlcjtcblxufVxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cImFuZ3VsYXIuZC50c1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibW9kZWxzL3NlYXNvbi50c1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibW9kZWxzL2NoYW1waW9uc2hpcC50c1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibW9kZWxzL3Ryb3BoeS50c1wiIC8+XG5cblxubmFtZXNwYWNlIEFwcGxpY2F0aW9uLkNvbnRyb2xsZXJzIHtcbiAgICBleHBvcnQgY2xhc3MgVHJvcGhpZXNDb250cm9sbGVyIHtcbiAgICAgICAgcHJpdmF0ZSBzZWFzb25zOiBhbnlbXTtcbiAgICAgICAgcHJpdmF0ZSBjaGFtcGlvbnNoaXBzOiBhbnlbXTtcbiAgICAgICAgcHJpdmF0ZSB0cm9waGllczogYW55W107XG5cbiAgICAgICAgcHJpdmF0ZSBjdXJyZW50U2Vhc29uOiBTZWFzb247XG4gICAgICAgIHByaXZhdGUgY3VycmVudENoYW1waW9uc2hpcDogQ2hhbXBpb25zaGlwO1xuXG4gICAgICAgIHByaXZhdGUgc2VydmljZTogYW55O1xuXG4gICAgICAgIGNvbnN0cnVjdG9yKHRyb3BoaWVzU2VydmljZTogYW55KSB7XG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UgPSB0cm9waGllc1NlcnZpY2U7XG4gICAgICAgIH1cblxuICAgICAgICAkb25Jbml0KCkge1xuICAgICAgICAgICAgLy8gR2V0IGFsbCBzZWFzb25zXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0U2Vhc29ucygpLnRoZW4oKHNlYXNvbnMpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnNlYXNvbnMgPSBzZWFzb25zO1xuICAgICAgICAgICAgfSwgKGVycikgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKFwiVHJvcGhpZXNDb250cm9sbGVyICRvbkluaXQoKTogRXJyb3IgZHVyaW5nIHJlYWRpbmcgc2Vhc29uc1wiKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgLyogU2VsZWN0IGEgc2Vhc29uICovXG4gICAgICAgIHNlbGVjdFNlYXNvbihzZWFzb246IFNlYXNvbik6IHZvaWQge1xuICAgICAgICAgICAgaWYgKHRoaXMuY3VycmVudFNlYXNvbiA9PT0gc2Vhc29uKVxuICAgICAgICAgICAgICAgIHJldHVybjtcblxuICAgICAgICAgICAgdGhpcy5jdXJyZW50U2Vhc29uID0gc2Vhc29uO1xuICAgICAgICAgICAgdGhpcy5jaGFtcGlvbnNoaXBzID0gW107XG4gICAgICAgICAgICB0aGlzLnRyb3BoaWVzID0gW107XG5cbiAgICAgICAgICAgIC8vIEdldCBhbGwgZXhpc3RpbmcgY2hhbXBpb25zaGlwcyBleGNlcHQgdGhlIEZyZW5jaCBDdXAgZm9yIHRoZSBzZWxlY3RlZCBzZWFzb25cbiAgICAgICAgICAgIHRoaXMuc2VydmljZS5nZXRDaGFtcGlvbnNoaXBzKHRoaXMuY3VycmVudFNlYXNvbikudGhlbigoY2hhbXBpb25zaGlwcykgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuY2hhbXBpb25zaGlwcyA9IGNoYW1waW9uc2hpcHM7XG4gICAgICAgICAgICB9LCAoZXJyKSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coXCJUcm9waGllc0NvbnRyb2xsZXIgc2VsZWN0U2Vhc29uKCk6IEVycm9yIGR1cmluZyByZWFkaW5nIGNoYW1waW9uc2hpcHNcIik7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIFNlbGVjdCBhIGNoYW1waW9uc2hpcCAqL1xuICAgICAgICBzZWxlY3RDaGFtcGlvbnNoaXAoY2hhbXBpb25zaGlwOiBDaGFtcGlvbnNoaXApOiB2b2lkIHtcbiAgICAgICAgICAgIHRoaXMuY3VycmVudENoYW1waW9uc2hpcCA9IGNoYW1waW9uc2hpcDtcblxuICAgICAgICAgICAgLyogR2V0IGFsbCB0cm9waGllcyAqL1xuICAgICAgICAgICAgdGhpcy5zZXJ2aWNlLmdldFRyb3BoaWVzKHRoaXMuY3VycmVudFNlYXNvbiwgdGhpcy5jdXJyZW50Q2hhbXBpb25zaGlwKS50aGVuKCh0cm9waGllcykgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMudHJvcGhpZXMgPSB0cm9waGllcztcbiAgICAgICAgICAgIH0sIChlcnIpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhcIlRyb3BoaWVzQ29udHJvbGxlciAkb25Jbml0KCk6IEVycm9yIGR1cmluZyByZWFkaW5nIHRyb3BoaWVzXCIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG59XG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiYW5ndWxhci5kLnRzXCIgLz5cclxuXHJcblxyXG5uYW1lc3BhY2UgQXBwbGljYXRpb24uU2VydmljZXMge1xyXG4gICAgZXhwb3J0IGNsYXNzIEZvcmVjYXN0ZXJzU2VydmljZSB7XHJcbiAgICAgICAgcHJpdmF0ZSBodHRwOiBhbnk7XHJcblxyXG4gICAgICAgIGNvbnN0cnVjdG9yKCRodHRwOiBuZy5JSHR0cFNlcnZpY2UpIHtcclxuICAgICAgICAgICAgdGhpcy5odHRwID0gJGh0dHA7XHJcbiAgICAgICAgfVxyXG5cclxuXHJcbiAgICAgICAgLyogR2V0IGFsbCBmb3JlY2FzdGVycyAqL1xyXG4gICAgICAgIGdldEZvcmVjYXN0ZXJzKCk6IGFueSB7XHJcbiAgICAgICAgICAgIGxldCB1cmwgPSBcIi4vZGlzdC9mb3JlY2FzdGVycy5waHBcIjtcclxuXHJcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmh0dHAoe1xyXG4gICAgICAgICAgICAgICAgbWV0aG9kOiBcIlBPU1RcIixcclxuICAgICAgICAgICAgICAgIHVybDogdXJsXHJcbiAgICAgICAgICAgIH0pLnRoZW4oZnVuY3Rpb24gc3VjY2Vzc0NhbGxiYWNrKHJlc3BvbnNlKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcclxuICAgICAgICAgICAgfSwgZnVuY3Rpb24gZXJyb3JDYWxsYmFjayhlcnJvcikge1xyXG4gICAgICAgICAgICAgICAgbGV0IGVyck1zZyA9IChlcnJvci5tZXNzYWdlKSA/IGVycm9yLm1lc3NhZ2UgOlxyXG4gICAgICAgICAgICAgICAgICAgIGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6IFwiZm9yZWNhc3RlcnMtc2VydmljZSBnZXRGb3JlY2FzdGVyczogU2VydmVyIGVycm9yXCI7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcclxuICAgICAgICAgICAgICAgIHJldHVybiBbXTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvKiBDcmVhdGUgYSBuZXcgZm9yZWNhc3RlciAqL1xyXG4gICAgICAgIGNyZWF0ZUZvcmVjYXN0ZXIoZm9yZWNhc3RlcjogYW55KTogYW55IHtcclxuICAgICAgICAgICAgbGV0IHVybCA9IFwiLi9kaXN0L2NyZWF0ZS1mb3JlY2FzdGVyLnBocFwiO1xyXG5cclxuICAgICAgICAgICAgbGV0IGRhdGEgPSBKU09OLnN0cmluZ2lmeShmb3JlY2FzdGVyKTtcclxuXHJcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmh0dHAoe1xyXG4gICAgICAgICAgICAgICAgbWV0aG9kOiBcIlBPU1RcIixcclxuICAgICAgICAgICAgICAgIHVybDogdXJsLFxyXG4gICAgICAgICAgICAgICAgZGF0YTogeyBkYXRhOiBkYXRhIH1cclxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xyXG4gICAgICAgICAgICB9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcbiAgICAgICAgICAgICAgICAgICAgZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogXCJmb3JlY2FzdGVycy1zZXJ2aWNlIGNyZWF0ZUZvcmVjYXN0ZXI6IFNlcnZlciBlcnJvclwiO1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLyogVXBkYXRlIGZvcmVjYXN0ZXIgKi9cclxuICAgICAgICB1cGRhdGVGb3JlY2FzdGVyKGZvcmVjYXN0ZXI6IGFueSk6IGFueSB7XHJcbiAgICAgICAgICAgIGxldCB1cmwgPSBcIi4vZGlzdC91cGRhdGUtZm9yZWNhc3Rlci5waHBcIjtcclxuXHJcbiAgICAgICAgICAgIGxldCBkYXRhID0gSlNPTi5zdHJpbmdpZnkoZm9yZWNhc3Rlcik7XHJcblxyXG4gICAgICAgICAgICByZXR1cm4gdGhpcy5odHRwKHtcclxuICAgICAgICAgICAgICAgIG1ldGhvZDogXCJQT1NUXCIsXHJcbiAgICAgICAgICAgICAgICB1cmw6IHVybCxcclxuICAgICAgICAgICAgICAgIGRhdGE6IHsgZGF0YTogZGF0YSB9XHJcbiAgICAgICAgICAgIH0pLnRoZW4oZnVuY3Rpb24gc3VjY2Vzc0NhbGxiYWNrKHJlc3BvbnNlKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcclxuICAgICAgICAgICAgfSwgZnVuY3Rpb24gZXJyb3JDYWxsYmFjayhlcnJvcikge1xyXG4gICAgICAgICAgICAgICAgbGV0IGVyck1zZyA9IChlcnJvci5tZXNzYWdlKSA/IGVycm9yLm1lc3NhZ2UgOlxyXG4gICAgICAgICAgICAgICAgICAgIGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6IFwiZm9yZWNhc3RlcnMtc2VydmljZSB1cGRhdGVGb3JlY2FzdGVyOiBTZXJ2ZXIgZXJyb3JcIjtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZXJyTXNnKTsgLy8gbG9nIHRvIGNvbnNvbGUgaW5zdGVhZFxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIFtdO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8qIE1vdmUgZm9yZWNhc3RlciB0byB0aGUgb2xkIGZvcmVjYXN0ZXJzIGxpc3QgKi9cclxuICAgICAgICBtb3ZlRm9yZWNhc3Rlcihmb3JlY2FzdGVyOiBhbnkpOiBhbnkge1xyXG4gICAgICAgICAgICBsZXQgdXJsID0gXCIuL2Rpc3QvbW92ZS1mb3JlY2FzdGVyLnBocFwiO1xyXG5cclxuICAgICAgICAgICAgbGV0IGRhdGEgPSBKU09OLnN0cmluZ2lmeShmb3JlY2FzdGVyKTtcclxuXHJcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmh0dHAoe1xyXG4gICAgICAgICAgICAgICAgbWV0aG9kOiBcIlBPU1RcIixcclxuICAgICAgICAgICAgICAgIHVybDogdXJsLFxyXG4gICAgICAgICAgICAgICAgZGF0YTogeyBkYXRhOiBkYXRhIH1cclxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xyXG4gICAgICAgICAgICB9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcbiAgICAgICAgICAgICAgICAgICAgZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogXCJmb3JlY2FzdGVycy1zZXJ2aWNlIG1vdmVGb3JlY2FzdGVyOiBTZXJ2ZXIgZXJyb3JcIjtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZXJyTXNnKTsgLy8gbG9nIHRvIGNvbnNvbGUgaW5zdGVhZFxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIFtdO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8qIERlbGV0ZSBmb3JlY2FzdGVyICovXHJcbiAgICAgICAgZGVsZXRlRm9yZWNhc3Rlcihmb3JlY2FzdGVyOiBhbnkpOiBhbnkge1xyXG4gICAgICAgICAgICBsZXQgdXJsID0gXCIuL2Rpc3QvZGVsZXRlLWZvcmVjYXN0ZXIucGhwXCI7XHJcblxyXG4gICAgICAgICAgICBsZXQgZGF0YSA9IEpTT04uc3RyaW5naWZ5KGZvcmVjYXN0ZXIpO1xyXG5cclxuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaHR0cCh7XHJcbiAgICAgICAgICAgICAgICBtZXRob2Q6IFwiUE9TVFwiLFxyXG4gICAgICAgICAgICAgICAgdXJsOiB1cmwsXHJcbiAgICAgICAgICAgICAgICBkYXRhOiB7IGRhdGE6IGRhdGEgfVxyXG4gICAgICAgICAgICB9KS50aGVuKGZ1bmN0aW9uIHN1Y2Nlc3NDYWxsYmFjayhyZXNwb25zZSkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuICAgICAgICAgICAgICAgIGxldCBlcnJNc2cgPSAoZXJyb3IubWVzc2FnZSkgPyBlcnJvci5tZXNzYWdlIDpcclxuICAgICAgICAgICAgICAgICAgICBlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiBcImZvcmVjYXN0ZXJzLXNlcnZpY2UgZGVsZXRlRm9yZWNhc3RlcjogU2VydmVyIGVycm9yXCI7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcclxuICAgICAgICAgICAgICAgIHJldHVybiBbXTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfVxyXG5cclxuXHJcbiAgICB9XHJcbn1cclxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cImFuZ3VsYXIuZC50c1wiIC8+XHJcblxyXG5cclxubmFtZXNwYWNlIEFwcGxpY2F0aW9uLlNlcnZpY2VzIHtcclxuICAgIGV4cG9ydCBjbGFzcyBTdGFuZGluZ3NTZXJ2aWNlIHtcclxuICAgICAgICBwcml2YXRlIGh0dHA6IGFueTtcclxuXHJcbiAgICAgICAgY29uc3RydWN0b3IoJGh0dHA6IG5nLklIdHRwU2VydmljZSkge1xyXG4gICAgICAgICAgICB0aGlzLmh0dHAgPSAkaHR0cDtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8qIEdldCBhbGwgc2Vhc29ucyAqL1xyXG4gICAgICAgIGdldFNlYXNvbnMoKTogYW55IHtcclxuICAgICAgICAgICAgbGV0IHVybCA9IFwiLi9kaXN0L3NlYXNvbnMucGhwXCI7XHJcblxyXG4gICAgICAgICAgICByZXR1cm4gdGhpcy5odHRwKHtcclxuICAgICAgICAgICAgICAgIG1ldGhvZDogXCJQT1NUXCIsXHJcbiAgICAgICAgICAgICAgICB1cmw6IHVybFxyXG4gICAgICAgICAgICB9KS50aGVuKGZ1bmN0aW9uIHN1Y2Nlc3NDYWxsYmFjayhyZXNwb25zZSkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuICAgICAgICAgICAgICAgIGxldCBlcnJNc2cgPSAoZXJyb3IubWVzc2FnZSkgPyBlcnJvci5tZXNzYWdlIDpcclxuICAgICAgICAgICAgICAgICAgICBlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiBcInN0YW5kaW5ncy1zZXJ2aWNlIGdldFNlYXNvbnM6IFNlcnZlciBlcnJvclwiO1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLyogR2V0IGFsbCBjaGFtcGlvbnNoaXBzICovXHJcbiAgICAgICAgZ2V0Q2hhbXBpb25zaGlwcyhzZWFzb246IFNlYXNvbik6IGFueSB7XHJcbiAgICAgICAgICAgIGxldCB1cmwgPSBcIi4vZGlzdC9jaGFtcGlvbnNoaXBzLnBocFwiO1xyXG5cclxuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaHR0cCh7XHJcbiAgICAgICAgICAgICAgICBtZXRob2Q6IFwiUE9TVFwiLFxyXG4gICAgICAgICAgICAgICAgdXJsOiB1cmwsXHJcbiAgICAgICAgICAgICAgICBkYXRhOiB7IHNhaXNvbjogc2Vhc29uIH1cclxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xyXG4gICAgICAgICAgICB9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcbiAgICAgICAgICAgICAgICAgICAgZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogXCJzdGFuZGluZ3Mtc2VydmljZSBnZXRDaGFtcGlvbnNoaXBzOiBTZXJ2ZXIgZXJyb3JcIjtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZXJyTXNnKTsgLy8gbG9nIHRvIGNvbnNvbGUgaW5zdGVhZFxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIFtdO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8qIEdldCBhbGwgd2Vla3MgKi9cclxuICAgICAgICBnZXRXZWVrcyhzZWFzb246IFNlYXNvbiwgY2hhbXBpb25zaGlwOiBDaGFtcGlvbnNoaXApOiBhbnkge1xyXG4gICAgICAgICAgICBsZXQgdXJsID0gXCIuL2Rpc3Qvd2Vla3MucGhwXCI7XHJcblxyXG4gICAgICAgICAgICByZXR1cm4gdGhpcy5odHRwKHtcclxuICAgICAgICAgICAgICAgIG1ldGhvZDogXCJQT1NUXCIsXHJcbiAgICAgICAgICAgICAgICB1cmw6IHVybCxcclxuICAgICAgICAgICAgICAgIGRhdGE6IHsgc2Fpc29uOiBzZWFzb24uU2Fpc29uLCBjaGFtcGlvbm5hdDogY2hhbXBpb25zaGlwLkNoYW1waW9ubmF0IH1cclxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xyXG4gICAgICAgICAgICB9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcbiAgICAgICAgICAgICAgICAgICAgZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogXCJzdGFuZGluZ3Mtc2VydmljZSBnZXRXZWVrczogU2VydmVyIGVycm9yXCI7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcclxuICAgICAgICAgICAgICAgIHJldHVybiBbXTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvKiBHZXQgZ2VuZXJhbCBzdGFuZGluZ3MgZm9yIGEgd2VlayAqL1xyXG4gICAgICAgIGdldFN0YW5kaW5ncyhzZWFzb246IFNlYXNvbiwgd2VlazogV2VlaywgcmVmZXJlbmNlRGF0ZTogc3RyaW5nKTogYW55IHtcclxuICAgICAgICAgICAgbGV0IHVybCA9IFwiLi9kaXN0L3N0YW5kaW5ncy5waHBcIjtcclxuXHJcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmh0dHAoe1xyXG4gICAgICAgICAgICAgICAgbWV0aG9kOiBcIlBPU1RcIixcclxuICAgICAgICAgICAgICAgIHVybDogdXJsLFxyXG4gICAgICAgICAgICAgICAgZGF0YTogSlNPTi5zdHJpbmdpZnkoeyBcInNhaXNvblwiOiBzZWFzb24uU2Fpc29uLCBcImpvdXJuZWVcIjogd2Vlay5Kb3VybmVlLCBcImRhdGUtcmVmZXJlbmNlXCI6IHJlZmVyZW5jZURhdGUgfSlcclxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xyXG4gICAgICAgICAgICB9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcbiAgICAgICAgICAgICAgICAgICAgZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogXCJzdGFuZGluZ3Mtc2VydmljZSBnZXRTdGFuZGluZ3M6IFNlcnZlciBlcnJvclwiO1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLyogR2V0IHdlZWsgc3RhbmRpbmdzICovXHJcbiAgICAgICAgZ2V0U3RhbmRpbmdzV2VlayhzZWFzb246IFNlYXNvbiwgd2VlazogV2VlaywgcmVmZXJlbmNlRGF0ZTogc3RyaW5nKTogYW55IHtcclxuICAgICAgICAgICAgbGV0IHVybCA9IFwiLi9kaXN0L3N0YW5kaW5ncy13ZWVrLnBocFwiO1xyXG5cclxuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaHR0cCh7XHJcbiAgICAgICAgICAgICAgICBtZXRob2Q6IFwiUE9TVFwiLFxyXG4gICAgICAgICAgICAgICAgdXJsOiB1cmwsXHJcbiAgICAgICAgICAgICAgICBkYXRhOiBKU09OLnN0cmluZ2lmeSh7IFwic2Fpc29uXCI6IHNlYXNvbi5TYWlzb24sIFwiam91cm5lZVwiOiB3ZWVrLkpvdXJuZWUsIFwiZGF0ZS1yZWZlcmVuY2VcIjogcmVmZXJlbmNlRGF0ZSB9KVxyXG4gICAgICAgICAgICB9KS50aGVuKGZ1bmN0aW9uIHN1Y2Nlc3NDYWxsYmFjayhyZXNwb25zZSkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuICAgICAgICAgICAgICAgIGxldCBlcnJNc2cgPSAoZXJyb3IubWVzc2FnZSkgPyBlcnJvci5tZXNzYWdlIDpcclxuICAgICAgICAgICAgICAgICAgICBlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiBcInN0YW5kaW5ncy1zZXJ2aWNlIGdldFN0YW5kaW5nc1dlZWs6IFNlcnZlciBlcnJvclwiO1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLyogR2V0IGdvYWwgc3RhbmRpbmdzICovXHJcbiAgICAgICAgZ2V0U3RhbmRpbmdzR29hbChzZWFzb246IFNlYXNvbiwgd2VlazogV2VlaywgcmVmZXJlbmNlRGF0ZTogc3RyaW5nKTogYW55IHtcclxuICAgICAgICAgICAgbGV0IHVybCA9IFwiLi9kaXN0L3N0YW5kaW5ncy1nb2FsLnBocFwiO1xyXG5cclxuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaHR0cCh7XHJcbiAgICAgICAgICAgICAgICBtZXRob2Q6IFwiUE9TVFwiLFxyXG4gICAgICAgICAgICAgICAgdXJsOiB1cmwsXHJcbiAgICAgICAgICAgICAgICBkYXRhOiBKU09OLnN0cmluZ2lmeSh7IFwic2Fpc29uXCI6IHNlYXNvbi5TYWlzb24sIFwiam91cm5lZVwiOiB3ZWVrLkpvdXJuZWUsIFwiZGF0ZS1yZWZlcmVuY2VcIjogcmVmZXJlbmNlRGF0ZSB9KVxyXG4gICAgICAgICAgICB9KS50aGVuKGZ1bmN0aW9uIHN1Y2Nlc3NDYWxsYmFjayhyZXNwb25zZSkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuICAgICAgICAgICAgICAgIGxldCBlcnJNc2cgPSAoZXJyb3IubWVzc2FnZSkgPyBlcnJvci5tZXNzYWdlIDpcclxuICAgICAgICAgICAgICAgICAgICBlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiBcInN0YW5kaW5ncy1zZXJ2aWNlIGdldFN0YW5kaW5nc0dvYWw6IFNlcnZlciBlcnJvclwiO1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxufVxyXG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiYW5ndWxhci5kLnRzXCIgLz5cblxuXG5uYW1lc3BhY2UgQXBwbGljYXRpb24uU2VydmljZXMge1xuICAgIGV4cG9ydCBjbGFzcyBUcm9waGllc1NlcnZpY2Uge1xuICAgICAgICBwcml2YXRlIGh0dHA6IGFueTtcblxuICAgICAgICBjb25zdHJ1Y3RvcigkaHR0cDogbmcuSUh0dHBTZXJ2aWNlKSB7XG4gICAgICAgICAgICB0aGlzLmh0dHAgPSAkaHR0cDtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIEdldCBhbGwgc2Vhc29ucyAqL1xuICAgICAgICBnZXRTZWFzb25zKCk6IGFueSB7XG4gICAgICAgICAgICBsZXQgdXJsID0gXCIuL2Rpc3Qvc2Vhc29ucy5waHBcIjtcblxuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaHR0cCh7XG4gICAgICAgICAgICAgICAgbWV0aG9kOiBcIlBPU1RcIixcbiAgICAgICAgICAgICAgICB1cmw6IHVybFxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XG4gICAgICAgICAgICAgICAgICAgIGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6IFwidHJvcGhpZXMtc2VydmljZSBnZXRTZWFzb25zOiBTZXJ2ZXIgZXJyb3JcIjtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIEdldCBhbGwgY2hhbXBpb25zaGlwcyAqL1xuICAgICAgICBnZXRDaGFtcGlvbnNoaXBzKHNlYXNvbjogU2Vhc29uKTogYW55IHtcbiAgICAgICAgICAgIGxldCB1cmwgPSBcIi4vZGlzdC9jaGFtcGlvbnNoaXBzLnBocFwiO1xuXG4gICAgICAgICAgICByZXR1cm4gdGhpcy5odHRwKHtcbiAgICAgICAgICAgICAgICBtZXRob2Q6IFwiUE9TVFwiLFxuICAgICAgICAgICAgICAgIHVybDogdXJsLFxuICAgICAgICAgICAgICAgIGRhdGE6IHsgc2Fpc29uOiBzZWFzb24gfVxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XG4gICAgICAgICAgICAgICAgICAgIGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6IFwidHJvcGhpZXMtc2VydmljZSBnZXRDaGFtcGlvbnNoaXBzOiBTZXJ2ZXIgZXJyb3JcIjtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIEdldCBhbGwgdHJvcGhpZXMgKi9cbiAgICAgICAgZ2V0VHJvcGhpZXMoc2Vhc29uOiBTZWFzb24sIGNoYW1waW9uc2hpcDogQ2hhbXBpb25zaGlwKTogYW55IHtcbiAgICAgICAgICAgIGxldCB1cmwgPSBcIi4vZGlzdC90cm9waGllcy5waHBcIjtcblxuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaHR0cCh7XG4gICAgICAgICAgICAgICAgbWV0aG9kOiBcIlBPU1RcIixcbiAgICAgICAgICAgICAgICB1cmw6IHVybCxcbiAgICAgICAgICAgICAgICBkYXRhOiBKU09OLnN0cmluZ2lmeSh7IHNhaXNvbjogc2Vhc29uLlNhaXNvbiwgY2hhbXBpb25uYXQ6IGNoYW1waW9uc2hpcC5DaGFtcGlvbm5hdCB9KVxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XG4gICAgICAgICAgICAgICAgICAgIGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6IFwidHJvcGhpZXMtc2VydmljZSBnZXRUcm9waGllczogU2VydmVyIGVycm9yXCI7XG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXG4gICAgICAgICAgICAgICAgcmV0dXJuIFtdO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG59XG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiYW5ndWxhci5kLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJhbmd1bGFyLXJvdXRlLmQudHNcIiAvPlxuXG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibmF2YmFyLWRpcmVjdGl2ZS50c1wiIC8+XG5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJob21lLWNvbnRyb2xsZXIudHNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cImZvcmVjYXN0ZXJzLWNvbnRyb2xsZXIudHNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cInN0YW5kaW5ncy1jb250cm9sbGVyLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJ0cm9waGllcy1jb250cm9sbGVyLnRzXCIgLz5cblxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cImZvcmVjYXN0ZXJzLXNlcnZpY2UudHNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cInN0YW5kaW5ncy1zZXJ2aWNlLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJ0cm9waGllcy1zZXJ2aWNlLnRzXCIgLz5cblxubGV0IGFwcE1vZHVsZSA9IGFuZ3VsYXIubW9kdWxlKFwicG91bHBlQXBwXCIsIFtcIm5nQW5pbWF0ZVwiLCBcInVpLnJvdXRlclwiLCBcInVpLmJvb3RzdHJhcFwiLCBcInVpLmxheW91dFwiXSk7XG5cblxuYXBwTW9kdWxlLnNlcnZpY2UoXCJmb3JlY2FzdGVyc1NlcnZpY2VcIiwgW1wiJGh0dHBcIiwgKCRodHRwKSA9PiBuZXcgQXBwbGljYXRpb24uU2VydmljZXMuRm9yZWNhc3RlcnNTZXJ2aWNlKCRodHRwKV0pO1xuYXBwTW9kdWxlLnNlcnZpY2UoXCJzdGFuZGluZ3NTZXJ2aWNlXCIsIFtcIiRodHRwXCIsICgkaHR0cCkgPT4gbmV3IEFwcGxpY2F0aW9uLlNlcnZpY2VzLlN0YW5kaW5nc1NlcnZpY2UoJGh0dHApXSk7XG5hcHBNb2R1bGUuc2VydmljZShcInRyb3BoaWVzU2VydmljZVwiLCBbXCIkaHR0cFwiLCAoJGh0dHApID0+IG5ldyBBcHBsaWNhdGlvbi5TZXJ2aWNlcy5Ucm9waGllc1NlcnZpY2UoJGh0dHApXSk7XG5cbmFwcE1vZHVsZS5jb250cm9sbGVyKFwiSG9tZUNvbnRyb2xsZXJcIiwgW1wiJHRpbWVvdXRcIiwgXCIkaW50ZXJ2YWxcIiwgKCR0aW1lb3V0LCAkaW50ZXJ2YWwpID0+XHRuZXcgQXBwbGljYXRpb24uQ29udHJvbGxlcnMuSG9tZUNvbnRyb2xsZXIoJHRpbWVvdXQsICRpbnRlcnZhbCldKTtcblxuYXBwTW9kdWxlLmNvbnRyb2xsZXIoXCJGb3JlY2FzdGVyc0NvbnRyb2xsZXJcIiwgW1wiZm9yZWNhc3RlcnNTZXJ2aWNlXCIsIChmb3JlY2FzdGVyc1NlcnZpY2UpID0+XHRuZXcgQXBwbGljYXRpb24uQ29udHJvbGxlcnMuRm9yZWNhc3RlcnNDb250cm9sbGVyKGZvcmVjYXN0ZXJzU2VydmljZSldKTtcblxuYXBwTW9kdWxlLmNvbnRyb2xsZXIoXCJTdGFuZGluZ3NDb250cm9sbGVyXCIsIFtcInN0YW5kaW5nc1NlcnZpY2VcIiwgKHN0YW5kaW5nc1NlcnZpY2UpID0+XG4gICAgbmV3IEFwcGxpY2F0aW9uLkNvbnRyb2xsZXJzLlN0YW5kaW5nc0NvbnRyb2xsZXIoc3RhbmRpbmdzU2VydmljZSldKTtcblxuYXBwTW9kdWxlLmNvbnRyb2xsZXIoXCJUcm9waGllc0NvbnRyb2xsZXJcIiwgW1widHJvcGhpZXNTZXJ2aWNlXCIsICh0cm9waGllc1NlcnZpY2UpID0+XG4gICAgbmV3IEFwcGxpY2F0aW9uLkNvbnRyb2xsZXJzLlRyb3BoaWVzQ29udHJvbGxlcih0cm9waGllc1NlcnZpY2UpXSk7XG5cbmFwcE1vZHVsZS5kaXJlY3RpdmUoXCJuYXZiYXJcIiwgKCkgPT4gbmV3IEFwcGxpY2F0aW9uLkRpcmVjdGl2ZXMuTmF2YmFyKCkpO1xuXG5hcHBNb2R1bGUuY29tcG9uZW50KFwiZm9yZWNhc3RlcnNDb21wb25lbnRcIiwge1xuICBiaW5kaW5nczoge1xuICB9LFxuICBjb250cm9sbGVyOiBcIkZvcmVjYXN0ZXJzQ29udHJvbGxlciBhcyBjdHJsXCIsXG4gIHRlbXBsYXRlVXJsOiBcIi4vZGlzdC9mb3JlY2FzdGVycy5odG1sXCJcbn0pO1xuXG5hcHBNb2R1bGUuY29tcG9uZW50KFwic3RhbmRpbmdzQ29tcG9uZW50XCIsIHtcbiAgICBiaW5kaW5nczoge1xuICAgIH0sXG4gICAgY29udHJvbGxlcjogXCJTdGFuZGluZ3NDb250cm9sbGVyIGFzIGN0cmxcIixcbiAgICB0ZW1wbGF0ZVVybDogXCIuL2Rpc3Qvc3RhbmRpbmdzLmh0bWxcIlxufSk7XG5cbmFwcE1vZHVsZS5jb21wb25lbnQoXCJ0cm9waGllc0NvbXBvbmVudFwiLCB7XG4gICAgYmluZGluZ3M6IHtcbiAgICB9LFxuICAgIGNvbnRyb2xsZXI6IFwiVHJvcGhpZXNDb250cm9sbGVyIGFzIGN0cmxcIixcbiAgICB0ZW1wbGF0ZVVybDogXCIuL2Rpc3QvdHJvcGhpZXMuaHRtbFwiXG59KTtcblxuYXBwTW9kdWxlLmNvbmZpZyhbXCIkc3RhdGVQcm92aWRlclwiLCBcIiR1cmxSb3V0ZXJQcm92aWRlclwiLCBmdW5jdGlvbigkc3RhdGVQcm92aWRlciwgJHVybFJvdXRlclByb3ZpZGVyKSB7XG4gICAgJHVybFJvdXRlclByb3ZpZGVyLm90aGVyd2lzZShcIi9ob21lXCIpO1xuXG4gICAgJHN0YXRlUHJvdmlkZXJcbiAgICAgICAgLnN0YXRlKFwiaG9tZVwiLCB7XG4gICAgICAgICAgICB1cmw6IFwiL2hvbWVcIixcbiAgICAgICAgICAgIHRlbXBsYXRlOiBcIlwiLFxuICAgICAgICAgICAgY29udHJvbGxlcjogXCJIb21lQ29udHJvbGxlclwiXG4gICAgICAgIH0pXG4gICAgICAgIC5zdGF0ZShcImZvcmVjYXN0ZXJzXCIsIHtcbiAgICAgICAgICAgIHVybDogXCIvZm9yZWNhc3RlcnNcIixcbiAgICAgICAgICAgIHRlbXBsYXRlOiBcIjxmb3JlY2FzdGVycy1jb21wb25lbnQ+PC9mb3JlY2FzdGVycy1jb21wb25lbnQ+XCIsXG4gICAgICAgICAgICBjb250cm9sbGVyOiBcIkZvcmVjYXN0ZXJzQ29udHJvbGxlclwiXG4gICAgICAgIH0pXG4gICAgICAgIC5zdGF0ZShcInN0YW5kaW5nc1wiLCB7XG4gICAgICAgICAgICB1cmw6IFwiL3N0YW5kaW5nc1wiLFxuICAgICAgICAgICAgdGVtcGxhdGU6IFwiPHN0YW5kaW5ncy1jb21wb25lbnQ+PC9zdGFuZGluZ3MtY29tcG9uZW50PlwiLFxuICAgICAgICAgICAgY29udHJvbGxlcjogXCJTdGFuZGluZ3NDb250cm9sbGVyXCJcbiAgICAgICAgfSlcbiAgICAgICAgLnN0YXRlKFwidHJvcGhpZXNcIiwge1xuICAgICAgICAgICAgdXJsOiBcIi90cm9waGllc1wiLFxuICAgICAgICAgICAgdGVtcGxhdGU6IFwiPHRyb3BoaWVzLWNvbXBvbmVudD48L3Ryb3BoaWVzLWNvbXBvbmVudD5cIixcbiAgICAgICAgICAgIGNvbnRyb2xsZXI6IFwiVHJvcGhpZXNDb250cm9sbGVyXCJcbiAgICAgICAgfSk7XG59XSk7XG4iXSwic291cmNlUm9vdCI6Ii9zb3VyY2UvIn0=
