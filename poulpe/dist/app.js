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
                    restrict: 'E',
                    templateUrl: './dist/navbar.html',
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
            function HomeController($scope, $timeout, $interval) {
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
                    formatYear: 'yyyy',
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
                    console.log('ForecastersController $onInit(): Error during reading');
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
                    if (this.currentForecaster.Pronostiqueurs_DateDeNaissance.toString() !== '0/0/0')
                        this.currentForecaster.Pronostiqueurs_DateDeNaissance = new Date(this.currentForecaster.Pronostiqueurs_DateDeNaissance.toString());
                /* Reformat the SQL date to Javascript date */
                if (this.currentForecaster.Pronostiqueurs_DateDebutPresence !== null)
                    if (this.currentForecaster.Pronostiqueurs_DateDebutPresence.toString() !== '0/0/0')
                        this.currentForecaster.Pronostiqueurs_DateDebutPresence = new Date(this.currentForecaster.Pronostiqueurs_DateDebutPresence.toString());
                /* Reformat the SQL date to Javascript date */
                if (this.currentForecaster.Pronostiqueurs_DateFinPresence !== null)
                    if (this.currentForecaster.Pronostiqueurs_DateFinPresence.toString() !== '0/0/0')
                        this.currentForecaster.Pronostiqueurs_DateFinPresence = new Date(this.currentForecaster.Pronostiqueurs_DateFinPresence.toString());
            };
            /* Open the UI date picker dialog */
            ForecastersController.prototype.openBirthdayCalendar = function () {
                if (this.currentForecaster.Pronostiqueurs_DateDeNaissance === null || this.currentForecaster.Pronostiqueurs_DateDeNaissance.toString() === '0/0/0')
                    this.currentForecaster.Pronostiqueurs_DateDeNaissance = new Date();
                this.birthdayCalendar.opened = true;
            };
            /* Open the UI date picker dialog */
            ForecastersController.prototype.openBeginDateCalendar = function () {
                if (this.currentForecaster.Pronostiqueurs_DateDebutPresence === null || this.currentForecaster.Pronostiqueurs_DateDebutPresence.toString() === '0/0/0')
                    this.currentForecaster.Pronostiqueurs_DateDebutPresence = new Date();
                this.beginDateCalendar.opened = true;
            };
            /* Open the UI date picker dialog */
            ForecastersController.prototype.openEndDateCalendar = function () {
                if (this.currentForecaster.Pronostiqueurs_DateFinPresence === null || this.currentForecaster.Pronostiqueurs_DateFinPresence.toString() === '0/0/0')
                    this.currentForecaster.Pronostiqueurs_DateFinPresence = new Date();
                this.endDateCalendar.opened = true;
            };
            /* Add a new forecaster or save the modifications made on an existing forecaster */
            ForecastersController.prototype.saveModifications = function () {
                if (this.isInCreationMode == true) {
                    this.hasBeenModified = false;
                    this.isInCreationMode = false;
                    this.forecasters.push(this.currentForecaster);
                    this.service.createForecaster(this.currentForecaster).then(function (data) {
                    }, function (err) {
                        console.log('Error during creation');
                    });
                }
                else {
                    this.forecasters[this.currentForecasterIndex] = angular.copy(this.currentForecaster);
                    this.hasBeenModified = false;
                    this.service.updateForecaster(this.currentForecaster).then(function (data) {
                    }, function (err) {
                        console.log('Error during update');
                    });
                }
            };
            /* Cancel the creation of a new forecaster or the modifications made on an existing forecaster */
            ForecastersController.prototype.cancelModifications = function () {
                if (this.isInCreationMode === true) {
                    // In creation mode, the data doesn't come from the forecasters array
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
            /* It's more complex because it's necessary to think about the UIB datepicker widget*/
            ForecastersController.prototype.checkBirthdayIsModified = function () {
                if (this.isInCreationMode === false) {
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance !== '0/0/0' && this.currentForecaster.Pronostiqueurs_DateDeNaissance != this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance)
                        this.hasBeenModified = true;
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance === '0/0/0' && this.currentForecaster.Pronostiqueurs_DateDeNaissance !== null)
                        this.hasBeenModified = true;
                }
            };
            /* Indicates that a modification has been made on begin date */
            /* It's more complex because it's necessary to think about the UIB datepicker widget*/
            ForecastersController.prototype.checkBeginDateIsModified = function () {
                if (this.isInCreationMode === false) {
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence !== '0/0/0' && this.currentForecaster.Pronostiqueurs_DateDebutPresence != this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence)
                        this.hasBeenModified = true;
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence === '0/0/0' && this.currentForecaster.Pronostiqueurs_DateDebutPresence !== null)
                        this.hasBeenModified = true;
                }
            };
            /* Indicates that a modification has been made on end date */
            /* It's more complex because it's necessary to think about the UIB datepicker widget*/
            ForecastersController.prototype.checkEndDateIsModified = function () {
                if (this.isInCreationMode === false) {
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence !== '0/0/0' && this.currentForecaster.Pronostiqueurs_DateFinPresence != this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence)
                        this.hasBeenModified = true;
                    if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence === '0/0/0' && this.currentForecaster.Pronostiqueurs_DateFinPresence !== null)
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
                    console.log('Error during move');
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
                    console.log('Error during delete');
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
                    console.log('StandingsController $onInit(): Error during reading seasons');
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
                    console.log('StandingsController selectSeason(): Error during reading championships');
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
                    console.log('StandingsController selectChampionship(): Error during reading weeks');
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
                    console.log('StandingsController $onInit(): Error during reading standings');
                });
                this.service.getStandingsWeek(this.currentSeason, this.currentWeek, this.currentReferenceDate).then(function (standingsWeek) {
                    _this.standingsWeek = standingsWeek;
                }, function (err) {
                    console.log('StandingsController $onInit(): Error during reading standings week');
                });
                this.service.getStandingsGoal(this.currentSeason, this.currentWeek, this.currentReferenceDate).then(function (standingsGoal) {
                    _this.standingsGoal = standingsGoal;
                }, function (err) {
                    console.log('StandingsController $onInit(): Error during reading standings goal');
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
                    console.log('TrophiesController $onInit(): Error during reading seasons');
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
                    console.log('TrophiesController selectSeason(): Error during reading championships');
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
                    console.log('TrophiesController $onInit(): Error during reading trophies');
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
                    method: 'POST',
                    url: url
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'forecasters-service getForecasters: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Create a new forecaster */
            ForecastersService.prototype.createForecaster = function (forecaster) {
                var url = "./dist/create-forecaster.php";
                var data = JSON.stringify(forecaster);
                return this.http({
                    method: 'POST',
                    url: url,
                    data: { data: data }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'forecasters-service createForecaster: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Update forecaster */
            ForecastersService.prototype.updateForecaster = function (forecaster) {
                var url = "./dist/update-forecaster.php";
                var data = JSON.stringify(forecaster);
                return this.http({
                    method: 'POST',
                    url: url,
                    data: { data: data }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'forecasters-service updateForecaster: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Move forecaster to the old forecasters list */
            ForecastersService.prototype.moveForecaster = function (forecaster) {
                var url = './dist/move-forecaster.php';
                var data = JSON.stringify(forecaster);
                return this.http({
                    method: 'POST',
                    url: url,
                    data: { data: data }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'forecasters-service moveForecaster: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Delete forecaster */
            ForecastersService.prototype.deleteForecaster = function (forecaster) {
                var url = './dist/delete-forecaster.php';
                var data = JSON.stringify(forecaster);
                return this.http({
                    method: 'POST',
                    url: url,
                    data: { data: data }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'forecasters-service deleteForecaster: Server error';
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
                var url = './dist/seasons.php';
                return this.http({
                    method: 'POST',
                    url: url
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'standings-service getSeasons: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get all championships */
            StandingsService.prototype.getChampionships = function (season) {
                var url = "./dist/championships.php";
                return this.http({
                    method: 'POST',
                    url: url,
                    data: { saison: season }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'standings-service getChampionships: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get all weeks */
            StandingsService.prototype.getWeeks = function (season, championship) {
                var url = "./dist/weeks.php";
                return this.http({
                    method: 'POST',
                    url: url,
                    data: { saison: season.Saison, championnat: championship.Championnat }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'standings-service getWeeks: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get general standings for a week */
            StandingsService.prototype.getStandings = function (season, week, referenceDate) {
                var url = "./dist/standings.php";
                return this.http({
                    method: 'POST',
                    url: url,
                    data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'standings-service getStandings: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get week standings */
            StandingsService.prototype.getStandingsWeek = function (season, week, referenceDate) {
                var url = "./dist/standings-week.php";
                return this.http({
                    method: 'POST',
                    url: url,
                    data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'standings-service getStandingsWeek: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get goal standings */
            StandingsService.prototype.getStandingsGoal = function (season, week, referenceDate) {
                var url = "./dist/standings-goal.php";
                return this.http({
                    method: 'POST',
                    url: url,
                    data: JSON.stringify({ "saison": season.Saison, "journee": week.Journee, "date-reference": referenceDate })
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'standings-service getStandingsGoal: Server error';
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
                var url = './dist/seasons.php';
                return this.http({
                    method: 'POST',
                    url: url
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'trophies-service getSeasons: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get all championships */
            TrophiesService.prototype.getChampionships = function (season) {
                var url = "./dist/championships.php";
                return this.http({
                    method: 'POST',
                    url: url,
                    data: { saison: season }
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'trophies-service getChampionships: Server error';
                    console.error(errMsg); // log to console instead
                    return [];
                });
            };
            /* Get all trophies */
            TrophiesService.prototype.getTrophies = function (season, championship) {
                var url = "./dist/trophies.php";
                return this.http({
                    method: 'POST',
                    url: url,
                    data: JSON.stringify({ saison: season.Saison, championnat: championship.Championnat })
                }).then(function successCallback(response) {
                    return response.data || {};
                }, function errorCallback(error) {
                    var errMsg = (error.message) ? error.message :
                        error.status ? error.status + " - " + error.statusText : 'trophies-service getTrophies: Server error';
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
var appModule = angular.module('poulpeApp', ['ngAnimate', 'ui.router', 'ui.bootstrap', 'ui.layout']);
appModule.service('forecastersService', ['$http', function ($http) { return new Application.Services.ForecastersService($http); }]);
appModule.service('standingsService', ['$http', function ($http) { return new Application.Services.StandingsService($http); }]);
appModule.service('trophiesService', ['$http', function ($http) { return new Application.Services.TrophiesService($http); }]);
appModule.controller('HomeController', ['$scope', '$timeout', '$interval', function ($scope, $timeout, $interval) {
        return new Application.Controllers.HomeController($scope, $timeout, $interval);
    }]);
appModule.controller('ForecastersController', ['forecastersService', function (forecastersService) {
        return new Application.Controllers.ForecastersController(forecastersService);
    }]);
appModule.controller('StandingsController', ['standingsService', function (standingsService) {
        return new Application.Controllers.StandingsController(standingsService);
    }]);
appModule.controller('TrophiesController', ['trophiesService', function (trophiesService) {
        return new Application.Controllers.TrophiesController(trophiesService);
    }]);
appModule.directive('navbar', function () { return new Application.Directives.Navbar(); });
appModule.component('forecastersComponent', {
    bindings: {},
    controller: 'ForecastersController as ctrl',
    templateUrl: './dist/forecasters.html'
});
appModule.component('standingsComponent', {
    bindings: {},
    controller: 'StandingsController as ctrl',
    templateUrl: './dist/standings.html'
});
appModule.component('trophiesComponent', {
    bindings: {},
    controller: 'TrophiesController as ctrl',
    templateUrl: './dist/trophies.html'
});
appModule.config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise('/home');
        $stateProvider
            .state('home', {
            url: '/home',
            template: '',
            controller: 'HomeController'
        })
            .state('forecasters', {
            url: '/forecasters',
            template: '<forecasters-component></forecasters-component>',
            controller: 'ForecastersController'
        })
            .state('standings', {
            url: '/standings',
            template: '<standings-component></standings-component>',
            controller: 'StandingsController'
        })
            .state('trophies', {
            url: '/trophies',
            template: '<trophies-component></trophies-component>',
            controller: 'TrophiesController'
        });
    }]);

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5hdmJhci1kaXJlY3RpdmUudHMiLCJob21lLWNvbnRyb2xsZXIudHMiLCJtb2RlbHMvZm9yZWNhc3Rlci50cyIsImZvcmVjYXN0ZXJzLWNvbnRyb2xsZXIudHMiLCJtb2RlbHMvc2Vhc29uLnRzIiwibW9kZWxzL2NoYW1waW9uc2hpcC50cyIsIm1vZGVscy93ZWVrLnRzIiwibW9kZWxzL3N0YW5kaW5nLnRzIiwic3RhbmRpbmdzLWNvbnRyb2xsZXIudHMiLCJtb2RlbHMvdHJvcGh5LnRzIiwidHJvcGhpZXMtY29udHJvbGxlci50cyIsImZvcmVjYXN0ZXJzLXNlcnZpY2UudHMiLCJzdGFuZGluZ3Mtc2VydmljZS50cyIsInRyb3BoaWVzLXNlcnZpY2UudHMiLCJhcHAudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUEsSUFBTyxXQUFXLENBaUJqQjtBQWpCRCxXQUFPLFdBQVc7SUFBQyxJQUFBLFVBQVUsQ0FpQjVCO0lBakJrQixXQUFBLFVBQVUsRUFBQyxDQUFDO1FBRTlCO1lBRUM7Z0JBQ0MsTUFBTSxDQUFDLElBQUksQ0FBQyxlQUFlLEVBQUUsQ0FBQztZQUMvQixDQUFDO1lBRU8sZ0NBQWUsR0FBdkI7Z0JBQ0MsTUFBTSxDQUFDO29CQUNOLFFBQVEsRUFBRSxHQUFHO29CQUNiLFdBQVcsRUFBRSxvQkFBb0I7b0JBQ2pDLEtBQUssRUFBRSxFQUNOO2lCQUNELENBQUM7WUFDSCxDQUFDO1lBQ0YsYUFBQztRQUFELENBZEEsQUFjQyxJQUFBO1FBZFksaUJBQU0sU0FjbEIsQ0FBQTtJQUNGLENBQUMsRUFqQmtCLFVBQVUsR0FBVixzQkFBVSxLQUFWLHNCQUFVLFFBaUI1QjtBQUFELENBQUMsRUFqQk0sV0FBVyxLQUFYLFdBQVcsUUFpQmpCO0FDakJELHFDQUFxQztBQUdyQyxJQUFPLFdBQVcsQ0FZakI7QUFaRCxXQUFPLFdBQVc7SUFBQyxJQUFBLFdBQVcsQ0FZN0I7SUFaa0IsV0FBQSxXQUFXLEVBQUMsQ0FBQztRQUMvQjtZQU9DLHdCQUFZLE1B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U0FDL0IsQ0FBQzthQUNELEtBQUssQ0FBQyxhQUFhLEVBQUU7WUFDbEIsR0FBRyxFQUFFLGNBQWM7WUFDbkIsUUFBUSxFQUFFLGlEQUFpRDtZQUMzRCxVQUFVLEVBQUUsdUJBQXVCO1NBQ3RDLENBQUM7YUFDRCxLQUFLLENBQUMsV0FBVyxFQUFFO1lBQ2hCLEdBQUcsRUFBRSxZQUFZO1lBQ2pCLFFBQVEsRUFBRSw2Q0FBNkM7WUFDdkQsVUFBVSxFQUFFLHFCQUFxQjtTQUNwQyxDQUFDO2FBQ0QsS0FBSyxDQUFDLFVBQVUsRUFBRTtZQUNmLEdBQUcsRUFBRSxXQUFXO1lBQ2hCLFFBQVEsRUFBRSwyQ0FBMkM7WUFDckQsVUFBVSxFQUFFLG9CQUFvQjtTQUNuQyxDQUFDLENBQUE7SUFDVixDQUFDLENBQUMsQ0FBQyxDQUFDIiwiZmlsZSI6ImFwcC5qcyIsInNvdXJjZXNDb250ZW50IjpbIm1vZHVsZSBBcHBsaWNhdGlvbi5EaXJlY3RpdmVzIHtcclxuXHJcblx0ZXhwb3J0IGNsYXNzIE5hdmJhciB7XHJcblxyXG5cdFx0Y29uc3RydWN0b3IoKSB7XHJcblx0XHRcdHJldHVybiB0aGlzLmNyZWF0ZURpcmVjdGl2ZSgpO1xyXG5cdFx0fVxyXG5cclxuXHRcdHByaXZhdGUgY3JlYXRlRGlyZWN0aXZlKCk6IGFueSB7XHJcblx0XHRcdHJldHVybiB7XHJcblx0XHRcdFx0cmVzdHJpY3Q6ICdFJyxcclxuXHRcdFx0XHR0ZW1wbGF0ZVVybDogJy4vZGlzdC9uYXZiYXIuaHRtbCcsXHJcblx0XHRcdFx0c2NvcGU6IHtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH07XHJcblx0XHR9XHJcblx0fVxyXG59IiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cImFuZ3VsYXIuZC50c1wiIC8+XG5cblxubW9kdWxlIEFwcGxpY2F0aW9uLkNvbnRyb2xsZXJzIHtcblx0ZXhwb3J0IGNsYXNzIEhvbWVDb250cm9sbGVyIHtcblx0XHRwcml2YXRlIHNjb3BlOiBhbnk7XG5cdFx0cHJpdmF0ZSB0aW1lb3V0OiBhbnk7XHRcdFx0XHRcdFx0XHQvLyBTZXJ2aWNlIHRpbWVvdXQgdG8gY2FsbCBvbmNlIGEgZnVuY3Rpb25cblx0XHRwcml2YXRlIGludGVydmFsOiBhbnk7XHRcdFx0XHRcdFx0XHQvLyBTZXJ2aWNlIGludGVydmFsIHRvIGNhbGwgY3ljbGljYWxseSBhIGZ1bmN0aW9uXG5cblxuXG5cdFx0Y29uc3RydWN0b3IoJHNjb3BlOiBuZy5JU2NvcGUsICR0aW1lb3V0OiBuZy5JVGltZW91dFNlcnZpY2UsICRpbnRlcnZhbDogbmcuSUludGVydmFsU2VydmljZSkge1xuXHRcdH1cblx0fVxuXG59IiwiY2xhc3MgRm9yZWNhc3RlciB7XHJcblx0UHJvbm9zdGlxdWV1cjogbnVtYmVyO1xyXG5cdFByb25vc3RpcXVldXJzX05vbVV0aWxpc2F0ZXVyOiBzdHJpbmc7XHJcblx0UHJvbm9zdGlxdWV1cnNfTm9tOiBzdHJpbmc7XHJcblx0UHJvbm9zdGlxdWV1cnNfUHJlbm9tOiBzdHJpbmc7XHJcblx0UHJvbm9zdGlxdWV1cnNfUGhvdG86IHN0cmluZztcclxuXHRQcm9ub3N0aXF1ZXVyc19BZG1pbmlzdHJhdGV1cjogbnVtYmVyO1xyXG5cdFByb25vc3RpcXVldXJzX01FTDogc3RyaW5nO1xyXG5cdFByb25vc3RpcXVldXJzX01vdERlUGFzc2U6IHN0cmluZztcclxuXHRQcm9ub3N0aXF1ZXVyc19QcmVtaWVyZUNvbm5leGlvbjogbnVtYmVyO1xyXG5cdFByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZTogRGF0ZTtcclxuXHRQcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZTogRGF0ZTtcclxuXHRQcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2U6IERhdGU7XHJcblx0UHJvbm9zdGlxdWV1cnNfTGlldURlUmVzaWRlbmNlOiBzdHJpbmc7XHJcblx0UHJvbm9zdGlxdWV1cnNfQW1iaXRpb25zOiBzdHJpbmc7XHJcblx0UHJvbm9zdGlxdWV1cnNfUGFsbWFyZXM6IHN0cmluZztcclxuXHRQcm9ub3N0aXF1ZXVyc19DYXJyaWVyZTogc3RyaW5nO1xyXG5cdFByb25vc3RpcXVldXJzX0NvbW1lbnRhaXJlOiBzdHJpbmc7XHJcblx0UHJvbm9zdGlxdWV1cnNfRXF1aXBlRmF2b3JpdGU6IHN0cmluZztcclxuXHRQcm9ub3N0aXF1ZXVyc19Db2RlQ291bGV1cjogc3RyaW5nO1xyXG5cdFRoZW1lc19UaGVtZTogbnVtYmVyO1xyXG5cclxuXHRjb25zdHJ1Y3RvcigpIHtcclxuXHRcdHRoaXMuaW5pdEZpZWxkcygpO1xyXG5cdH1cclxuXHJcblx0aW5pdEZpZWxkcygpIHtcclxuXHRcdHRoaXMuUHJvbm9zdGlxdWV1cnNfTm9tVXRpbGlzYXRldXIgPSAnJztcclxuXHRcdHRoaXMuUHJvbm9zdGlxdWV1cnNfTm9tID0gJyc7XHJcblx0XHR0aGlzLlByb25vc3RpcXVldXJzX1ByZW5vbSA9ICcnO1xyXG5cdFx0dGhpcy5Qcm9ub3N0aXF1ZXVyc19QaG90byA9ICcnO1xyXG5cdFx0dGhpcy5Qcm9ub3N0aXF1ZXVyc19BZG1pbmlzdHJhdGV1ciA9IDA7XHJcblx0XHR0aGlzLlByb25vc3RpcXVldXJzX01FTCA9ICcnO1xyXG5cdFx0dGhpcy5Qcm9ub3N0aXF1ZXVyc19Nb3REZVBhc3NlID0gJyc7XHJcblx0XHR0aGlzLlByb25vc3RpcXVldXJzX1ByZW1pZXJlQ29ubmV4aW9uID0gMTtcclxuXHRcdHRoaXMuUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlID0gbnVsbDtcclxuXHRcdHRoaXMuUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UgPSBudWxsO1xyXG5cdFx0dGhpcy5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgPSBudWxsO1xyXG5cdFx0dGhpcy5Qcm9ub3N0aXF1ZXVyc19MaWV1RGVSZXNpZGVuY2UgPSAnJztcclxuXHRcdHRoaXMuUHJvbm9zdGlxdWV1cnNfQW1iaXRpb25zID0gJyc7XHJcblx0XHR0aGlzLlByb25vc3RpcXVldXJzX1BhbG1hcmVzID0gJyc7XHJcblx0XHR0aGlzLlByb25vc3RpcXVldXJzX0NhcnJpZXJlID0gJyc7XHJcblx0XHR0aGlzLlByb25vc3RpcXVldXJzX0NvbW1lbnRhaXJlID0gJyc7XHJcblx0XHR0aGlzLlByb25vc3RpcXVldXJzX0VxdWlwZUZhdm9yaXRlID0gJyc7XHJcblx0XHR0aGlzLlByb25vc3RpcXVldXJzX0NvZGVDb3VsZXVyID0gJyc7XHJcblx0XHR0aGlzLlRoZW1lc19UaGVtZSA9IDE7XHJcblx0fVxyXG59XHJcbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJhbmd1bGFyLmQudHNcIiAvPlxyXG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibW9kZWxzL2ZvcmVjYXN0ZXIudHNcIiAvPlxyXG5cclxuXHJcbm1vZHVsZSBBcHBsaWNhdGlvbi5Db250cm9sbGVyc3tcclxuXHRleHBvcnQgY2xhc3MgRm9yZWNhc3RlcnNDb250cm9sbGVyIHtcclxuXHRcdHByaXZhdGUgZm9yZWNhc3RlcnM6IGFueVtdO1xyXG5cdFx0cHJpdmF0ZSBzZXJ2aWNlOiBhbnk7XHJcblx0XHRwcml2YXRlIGN1cnJlbnRGb3JlY2FzdGVyOiBGb3JlY2FzdGVyO1xyXG5cdFx0cHJpdmF0ZSBoYXNCZWVuTW9kaWZpZWQ6IGJvb2xlYW47XHJcbiAgICAgICAgcHJpdmF0ZSBpc01vdmFibGU6IGJvb2xlYW47XHJcbiAgICAgICAgcHJpdmF0ZSBpc0RlbGV0YWJsZTogYm9vbGVhbjtcclxuXHRcdHByaXZhdGUgY3VycmVudEZvcmVjYXN0ZXJJbmRleDogbnVtYmVyO1xyXG5cdFx0cHJpdmF0ZSBpc0luQ3JlYXRpb25Nb2RlOiBib29sZWFuO1xyXG5cdFx0cHJpdmF0ZSBiaXJ0aGRheUNhbGVuZGFyID0ge1xyXG5cdFx0XHRvcGVuZWQ6IGZhbHNlXHJcblx0XHR9O1xyXG5cdFx0cHJpdmF0ZSBiZWdpbkRhdGVDYWxlbmRhciA9IHtcclxuXHRcdFx0b3BlbmVkOiBmYWxzZVxyXG5cdFx0fTtcclxuXHRcdHByaXZhdGUgZW5kRGF0ZUNhbGVuZGFyID0ge1xyXG5cdFx0XHRvcGVuZWQ6IGZhbHNlXHJcblx0XHR9O1xyXG5cclxuXHRcdHByaXZhdGUgZGF0ZU9wdGlvbnMgPSB7XHJcblx0XHRcdGZvcm1hdFllYXI6ICd5eXl5JyxcclxuXHRcdFx0bWF4RGF0ZTogbmV3IERhdGUoMjAyMCwgNSwgMjIpLFxyXG5cdFx0XHRtaW5EYXRlOiBuZXcgRGF0ZSgxOTIwLCAxLCAxKSxcclxuXHRcdFx0c3RhcnRpbmdEYXk6IDFcclxuXHRcdH07XHJcblxyXG5cdFx0XHJcblx0XHRjb25zdHJ1Y3Rvcihmb3JlY2FzdGVyc1NlcnZpY2U6IGFueSkge1xyXG5cdFx0XHR0aGlzLnNlcnZpY2UgPSBmb3JlY2FzdGVyc1NlcnZpY2U7XHJcblx0XHRcdHRoaXMuaGFzQmVlbk1vZGlmaWVkID0gZmFsc2U7XHJcbiAgICAgICAgICAgIHRoaXMuaXNNb3ZhYmxlID0gZmFsc2U7XHJcbiAgICAgICAgICAgIHRoaXMuaXNEZWxldGFibGUgPSBmYWxzZTtcclxuXHRcdFx0dGhpcy5pc0luQ3JlYXRpb25Nb2RlID0gZmFsc2U7XHJcblx0XHR9XHJcblxyXG5cdFx0JG9uSW5pdCgpIHtcclxuXHRcdFx0dGhpcy5zZXJ2aWNlLmdldEZvcmVjYXN0ZXJzKCkudGhlbigoZm9yZWNhc3RlcnMpID0+IHtcclxuXHRcdFx0XHR0aGlzLmZvcmVjYXN0ZXJzID0gZm9yZWNhc3RlcnM7XHJcblx0XHRcdH0sIChlcnIpID0+IHtcclxuXHRcdFx0XHRjb25zb2xlLmxvZygnRm9yZWNhc3RlcnNDb250cm9sbGVyICRvbkluaXQoKTogRXJyb3IgZHVyaW5nIHJlYWRpbmcnKTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyogTG9hZCB0aGUgZm9yZWNhc3RlciB0byB0aGUgZWRpdCBmb3JtICovXHJcblx0XHRlZGl0Rm9yZWNhc3Rlcihmb3JlY2FzdGVyOiBhbnksIGluZGV4OiBudW1iZXIpOiB2b2lkIHtcclxuXHRcdFx0dGhpcy5jdXJyZW50Rm9yZWNhc3RlciA9IGFuZ3VsYXIuY29weShmb3JlY2FzdGVyKTtcclxuXHRcdFx0dGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4ID0gaW5kZXg7XHJcbiAgICAgICAgICAgIHRoaXMuaXNNb3ZhYmxlID0gdHJ1ZTtcclxuICAgICAgICAgICAgdGhpcy5pc0RlbGV0YWJsZSA9IHRydWU7XHJcblxyXG5cdFx0XHQvKiBSZWZvcm1hdCB0aGUgU1FMIGRhdGUgdG8gSmF2YXNjcmlwdCBkYXRlICovXHJcblx0XHRcdGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSAhPT0gbnVsbClcclxuXHRcdFx0XHRpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UudG9TdHJpbmcoKSAhPT0gJzAvMC8wJylcdFx0XHRcdFxyXG5cdFx0XHRcdFx0dGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UgPSBuZXcgRGF0ZSh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZS50b1N0cmluZygpKTtcclxuXHJcblx0XHRcdC8qIFJlZm9ybWF0IHRoZSBTUUwgZGF0ZSB0byBKYXZhc2NyaXB0IGRhdGUgKi9cclxuXHRcdFx0aWYgKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UgIT09IG51bGwpXHJcblx0XHRcdFx0aWYgKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UudG9TdHJpbmcoKSAhPT0gJzAvMC8wJylcclxuXHRcdFx0XHRcdHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UgPSBuZXcgRGF0ZSh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlLnRvU3RyaW5nKCkpO1xyXG5cclxuXHRcdFx0LyogUmVmb3JtYXQgdGhlIFNRTCBkYXRlIHRvIEphdmFzY3JpcHQgZGF0ZSAqL1xyXG5cdFx0XHRpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgIT09IG51bGwpXHJcblx0XHRcdFx0aWYgKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZUZpblByZXNlbmNlLnRvU3RyaW5nKCkgIT09ICcwLzAvMCcpXHJcblx0XHRcdFx0XHR0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZSA9IG5ldyBEYXRlKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZUZpblByZXNlbmNlLnRvU3RyaW5nKCkpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qIE9wZW4gdGhlIFVJIGRhdGUgcGlja2VyIGRpYWxvZyAqL1xyXG5cdFx0b3BlbkJpcnRoZGF5Q2FsZW5kYXIoKTogdm9pZCB7XHJcblx0XHRcdGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSA9PT0gbnVsbCB8fCB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZS50b1N0cmluZygpID09PSAnMC8wLzAnKVxyXG5cdFx0XHRcdHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlID0gbmV3IERhdGUoKTtcclxuXHRcdFx0XHJcblx0XHRcdHRoaXMuYmlydGhkYXlDYWxlbmRhci5vcGVuZWQgPSB0cnVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qIE9wZW4gdGhlIFVJIGRhdGUgcGlja2VyIGRpYWxvZyAqL1xyXG5cdFx0b3BlbkJlZ2luRGF0ZUNhbGVuZGFyKCk6IHZvaWQge1xyXG5cdFx0XHRpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZSA9PT0gbnVsbCB8fCB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlLnRvU3RyaW5nKCkgPT09ICcwLzAvMCcpXHJcblx0XHRcdFx0dGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZSA9IG5ldyBEYXRlKCk7XHJcblxyXG5cdFx0XHR0aGlzLmJlZ2luRGF0ZUNhbGVuZGFyLm9wZW5lZCA9IHRydWU7XHJcblx0XHR9XHJcblxyXG5cdFx0LyogT3BlbiB0aGUgVUkgZGF0ZSBwaWNrZXIgZGlhbG9nICovXHJcblx0XHRvcGVuRW5kRGF0ZUNhbGVuZGFyKCk6IHZvaWQge1xyXG5cdFx0XHRpZiAodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgPT09IG51bGwgfHwgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UudG9TdHJpbmcoKSA9PT0gJzAvMC8wJylcclxuXHRcdFx0XHR0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZSA9IG5ldyBEYXRlKCk7XHJcblxyXG5cdFx0XHR0aGlzLmVuZERhdGVDYWxlbmRhci5vcGVuZWQgPSB0cnVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qIEFkZCBhIG5ldyBmb3JlY2FzdGVyIG9yIHNhdmUgdGhlIG1vZGlmaWNhdGlvbnMgbWFkZSBvbiBhbiBleGlzdGluZyBmb3JlY2FzdGVyICovXHJcblx0XHRzYXZlTW9kaWZpY2F0aW9ucygpOiB2b2lkIHtcclxuXHRcdFx0aWYgKHRoaXMuaXNJbkNyZWF0aW9uTW9kZSA9PSB0cnVlKSB7XHJcblx0XHRcdFx0dGhpcy5oYXNCZWVuTW9kaWZpZWQgPSBmYWxzZTtcclxuXHRcdFx0XHR0aGlzLmlzSW5DcmVhdGlvbk1vZGUgPSBmYWxzZTtcclxuXHRcdFx0XHR0aGlzLmZvcmVjYXN0ZXJzLnB1c2godGhpcy5jdXJyZW50Rm9yZWNhc3Rlcik7XHJcblx0XHRcdFx0dGhpcy5zZXJ2aWNlLmNyZWF0ZUZvcmVjYXN0ZXIodGhpcy5jdXJyZW50Rm9yZWNhc3RlcikudGhlbigoZGF0YSkgPT4ge1xyXG5cdFx0XHRcdH0sIChlcnIpID0+IHtcclxuXHRcdFx0XHRcdGNvbnNvbGUubG9nKCdFcnJvciBkdXJpbmcgY3JlYXRpb24nKTtcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fVxyXG5cdFx0XHRlbHNlIHtcclxuXHRcdFx0XHR0aGlzLmZvcmVjYXN0ZXJzW3RoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleF0gPSBhbmd1bGFyLmNvcHkodGhpcy5jdXJyZW50Rm9yZWNhc3Rlcik7XHJcblx0XHRcdFx0dGhpcy5oYXNCZWVuTW9kaWZpZWQgPSBmYWxzZTtcclxuXHRcdFx0XHR0aGlzLnNlcnZpY2UudXBkYXRlRm9yZWNhc3Rlcih0aGlzLmN1cnJlbnRGb3JlY2FzdGVyKS50aGVuKChkYXRhKSA9PiB7XHJcblx0XHRcdFx0fSwgKGVycikgPT4ge1xyXG5cdFx0XHRcdFx0Y29uc29sZS5sb2coJ0Vycm9yIGR1cmluZyB1cGRhdGUnKTtcclxuXHRcdFx0XHR9KTtcclxuXHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvKiBDYW5jZWwgdGhlIGNyZWF0aW9uIG9mIGEgbmV3IGZvcmVjYXN0ZXIgb3IgdGhlIG1vZGlmaWNhdGlvbnMgbWFkZSBvbiBhbiBleGlzdGluZyBmb3JlY2FzdGVyICovXHJcblx0XHRjYW5jZWxNb2RpZmljYXRpb25zKCk6IHZvaWQge1xyXG5cdFx0XHRpZiAodGhpcy5pc0luQ3JlYXRpb25Nb2RlID09PSB0cnVlKSB7XHJcblx0XHRcdFx0Ly8gSW4gY3JlYXRpb24gbW9kZSwgdGhlIGRhdGEgZG9lc24ndCBjb21lIGZyb20gdGhlIGZvcmVjYXN0ZXJzIGFycmF5XHJcblx0XHRcdFx0dGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5pbml0RmllbGRzKCk7XHJcblx0XHRcdH1cclxuXHRcdFx0ZWxzZSB7XHJcblx0XHRcdFx0dGhpcy5jdXJyZW50Rm9yZWNhc3RlciA9IGFuZ3VsYXIuY29weSh0aGlzLmZvcmVjYXN0ZXJzW3RoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleF0pO1xyXG5cdFx0XHRcdGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZU5haXNzYW5jZSAhPT0gbnVsbClcclxuXHRcdFx0XHRcdHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlID0gbmV3IERhdGUodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UudG9TdHJpbmcoKSk7XHJcblxyXG5cdFx0XHRcdGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlICE9PSBudWxsKVxyXG5cdFx0XHRcdFx0dGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZSA9IG5ldyBEYXRlKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlYnV0UHJlc2VuY2UudG9TdHJpbmcoKSk7XHJcblxyXG5cdFx0XHRcdGlmICh0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZSAhPT0gbnVsbClcclxuXHRcdFx0XHRcdHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZUZpblByZXNlbmNlID0gbmV3IERhdGUodGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UudG9TdHJpbmcoKSk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHRoaXMuaGFzQmVlbk1vZGlmaWVkID0gZmFsc2U7XHJcblx0XHRcdHRoaXMuaXNJbkNyZWF0aW9uTW9kZSA9IGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qIEluZGljYXRlcyB0aGF0IGEgbW9kaWZpY2F0aW9uIGhhcyBiZWVuIG1hZGUgKi9cclxuXHRcdHNldE1vZGlmaWVkT24oKTogdm9pZCB7XHJcblx0XHRcdGlmKHRoaXMuaXNJbkNyZWF0aW9uTW9kZSA9PT0gZmFsc2UpXHJcblx0XHRcdFx0dGhpcy5oYXNCZWVuTW9kaWZpZWQgPSB0cnVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qIEluZGljYXRlcyB0aGF0IGEgbW9kaWZpY2F0aW9uIGhhcyBiZWVuIG1hZGUgb24gYmlydGhkYXkgKi9cclxuXHRcdC8qIEl0J3MgbW9yZSBjb21wbGV4IGJlY2F1c2UgaXQncyBuZWNlc3NhcnkgdG8gdGhpbmsgYWJvdXQgdGhlIFVJQiBkYXRlcGlja2VyIHdpZGdldCovXHJcblx0XHRjaGVja0JpcnRoZGF5SXNNb2RpZmllZCgpOiB2b2lkIHtcclxuXHRcdFx0aWYodGhpcy5pc0luQ3JlYXRpb25Nb2RlID09PSBmYWxzZSkge1xyXG5cdFx0XHRcdGlmICh0aGlzLmZvcmVjYXN0ZXJzW3RoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleF0uUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlICE9PSAnMC8wLzAnICYmIHRoaXMuY3VycmVudEZvcmVjYXN0ZXIuUHJvbm9zdGlxdWV1cnNfRGF0ZURlTmFpc3NhbmNlICE9IHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UpXHJcblx0XHRcdFx0XHR0aGlzLmhhc0JlZW5Nb2RpZmllZCA9IHRydWU7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0aWYgKHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UgPT09ICcwLzAvMCcgJiYgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVOYWlzc2FuY2UgIT09IG51bGwpXHJcblx0XHRcdFx0XHR0aGlzLmhhc0JlZW5Nb2RpZmllZCA9IHRydWU7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvKiBJbmRpY2F0ZXMgdGhhdCBhIG1vZGlmaWNhdGlvbiBoYXMgYmVlbiBtYWRlIG9uIGJlZ2luIGRhdGUgKi9cclxuXHRcdC8qIEl0J3MgbW9yZSBjb21wbGV4IGJlY2F1c2UgaXQncyBuZWNlc3NhcnkgdG8gdGhpbmsgYWJvdXQgdGhlIFVJQiBkYXRlcGlja2VyIHdpZGdldCovXHJcblx0XHRjaGVja0JlZ2luRGF0ZUlzTW9kaWZpZWQoKTogdm9pZCB7XHJcblx0XHRcdGlmICh0aGlzLmlzSW5DcmVhdGlvbk1vZGUgPT09IGZhbHNlKSB7XHJcblx0XHRcdFx0aWYgKHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZSAhPT0gJzAvMC8wJyAmJiB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlICE9IHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZSlcclxuXHRcdFx0XHRcdHRoaXMuaGFzQmVlbk1vZGlmaWVkID0gdHJ1ZTtcclxuXHJcblx0XHRcdFx0aWYgKHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRGVidXRQcmVzZW5jZSA9PT0gJzAvMC8wJyAmJiB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyLlByb25vc3RpcXVldXJzX0RhdGVEZWJ1dFByZXNlbmNlICE9PSBudWxsKVxyXG5cdFx0XHRcdFx0dGhpcy5oYXNCZWVuTW9kaWZpZWQgPSB0cnVlO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0LyogSW5kaWNhdGVzIHRoYXQgYSBtb2RpZmljYXRpb24gaGFzIGJlZW4gbWFkZSBvbiBlbmQgZGF0ZSAqL1xyXG5cdFx0LyogSXQncyBtb3JlIGNvbXBsZXggYmVjYXVzZSBpdCdzIG5lY2Vzc2FyeSB0byB0aGluayBhYm91dCB0aGUgVUlCIGRhdGVwaWNrZXIgd2lkZ2V0Ki9cclxuXHRcdGNoZWNrRW5kRGF0ZUlzTW9kaWZpZWQoKTogdm9pZCB7XHJcblx0XHRcdGlmICh0aGlzLmlzSW5DcmVhdGlvbk1vZGUgPT09IGZhbHNlKSB7XHJcblx0XHRcdFx0aWYgKHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgIT09ICcwLzAvMCcgJiYgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgIT0gdGhpcy5mb3JlY2FzdGVyc1t0aGlzLmN1cnJlbnRGb3JlY2FzdGVySW5kZXhdLlByb25vc3RpcXVldXJzX0RhdGVGaW5QcmVzZW5jZSlcclxuXHRcdFx0XHRcdHRoaXMuaGFzQmVlbk1vZGlmaWVkID0gdHJ1ZTtcclxuXHJcblx0XHRcdFx0aWYgKHRoaXMuZm9yZWNhc3RlcnNbdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4XS5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgPT09ICcwLzAvMCcgJiYgdGhpcy5jdXJyZW50Rm9yZWNhc3Rlci5Qcm9ub3N0aXF1ZXVyc19EYXRlRmluUHJlc2VuY2UgIT09IG51bGwpXHJcblx0XHRcdFx0XHR0aGlzLmhhc0JlZW5Nb2RpZmllZCA9IHRydWU7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblxyXG5cdFx0LyogQ3JlYXRlIGEgbmV3IGZvcmVjYXN0ZXIgKi9cclxuXHRcdGNyZWF0ZUZvcmVjYXN0ZXIoKTogdm9pZCB7XHJcblx0XHRcdHRoaXMuY3VycmVudEZvcmVjYXN0ZXIgPSBuZXcgRm9yZWNhc3RlcigpO1xyXG5cdFx0XHR0aGlzLmhhc0JlZW5Nb2RpZmllZCA9IHRydWU7XHJcbiAgICAgICAgICAgIHRoaXMuaXNNb3ZhYmxlID0gZmFsc2U7XHJcbiAgICAgICAgICAgIHRoaXMuaXNEZWxldGFibGUgPSBmYWxzZTtcclxuXHRcdFx0dGhpcy5pc0luQ3JlYXRpb25Nb2RlID0gdHJ1ZTtcclxuXHRcdH1cclxuXHJcbiAgICAgICAgLyogTW92ZSBhIGZvcmVjYXN0ZXIgdG8gdGhlIHByZXZpb3VzIGZvcmVjYXN0ZXJzIGxpc3QgKi9cclxuICAgICAgICBtb3ZlRm9yZWNhc3RlcigpOiB2b2lkIHtcclxuICAgICAgICAgICAgdGhpcy5zZXJ2aWNlLm1vdmVGb3JlY2FzdGVyKHRoaXMuY3VycmVudEZvcmVjYXN0ZXIpLnRoZW4oKGRhdGEpID0+IHtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLmZvcmVjYXN0ZXJzLnNwbGljZSh0aGlzLmN1cnJlbnRGb3JlY2FzdGVySW5kZXgsIDEpO1xyXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleCA9IG51bGw7XHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3RlciA9IG51bGw7XHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5pc01vdmFibGUgPSBmYWxzZTtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLmlzRGVsZXRhYmxlID0gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9LCAoZXJyKSA9PiB7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Vycm9yIGR1cmluZyBtb3ZlJyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgIC8qIE1vdmUgYSBmb3JlY2FzdGVyIHRvIHRoZSBwcmV2aW91cyBmb3JlY2FzdGVycyBsaXN0ICovXHJcbiAgICAgICAgZGVsZXRlRm9yZWNhc3RlcigpOiB2b2lkIHtcclxuICAgICAgICAgICAgdGhpcy5zZXJ2aWNlLmRlbGV0ZUZvcmVjYXN0ZXIodGhpcy5jdXJyZW50Rm9yZWNhc3RlcikudGhlbigoZGF0YSkgPT4ge1xyXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZm9yZWNhc3RlcnMuc3BsaWNlKHRoaXMuY3VycmVudEZvcmVjYXN0ZXJJbmRleCwgMSk7XHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5jdXJyZW50Rm9yZWNhc3RlckluZGV4ID0gbnVsbDtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLmN1cnJlbnRGb3JlY2FzdGVyID0gbnVsbDtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLmlzTW92YWJsZSA9IGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaXNEZWxldGFibGUgPSBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH0sIChlcnIpID0+IHtcclxuICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRXJyb3IgZHVyaW5nIGRlbGV0ZScpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblx0fVxyXG5cclxufSIsImNsYXNzIFNlYXNvbiB7XG4gICAgU2Fpc29uOiBudW1iZXI7XG4gICAgXG4gICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgIHRoaXMuaW5pdEZpZWxkcygpO1xuICAgIH1cblxuICAgIGluaXRGaWVsZHMoKSB7XG4gICAgfVxufSIsImNsYXNzIENoYW1waW9uc2hpcCB7XG4gICAgQ2hhbXBpb25uYXQ6IG51bWJlcjtcbiAgICBDaGFtcGlvbm5hdHNfTm9tQ291cnQ6IHN0cmluZztcblxuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLmluaXRGaWVsZHMoKTtcbiAgICB9XG5cbiAgICBpbml0RmllbGRzKCkge1xuICAgIH1cbn0iLCJjbGFzcyBXZWVrIHtcbiAgICBKb3VybmVlOiBudW1iZXI7XG4gICAgSm91cm5lZXNfTm9tQ291cnQ6IHN0cmluZztcbiAgICBDbGFzc2VtZW50c19EYXRlUmVmZXJlbmNlOiBEYXRlO1xuXG4gICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgIHRoaXMuaW5pdEZpZWxkcygpO1xuICAgIH1cblxuICAgIGluaXRGaWVsZHMoKSB7XG4gICAgfVxufSIsImNsYXNzIFN0YW5kaW5nIHtcclxuXHRTYWlzb25zX1NhaXNvbjogbnVtYmVyO1xyXG5cdEpvdXJuZWVzX0pvdXJuZWU6IG51bWJlcjtcclxuXHRDbGFzc2VtZW50c19EYXRlUmVmZXJlbmNlOiBzdHJpbmc7XHJcblxyXG5cdENsYXNzZW1lbnRzX0NsYXNzZW1lbnRHZW5lcmFsTWF0Y2g6IG51bWJlcjtcclxuXHRDbGFzc2VtZW50c19DbGFzc2VtZW50R2VuZXJhbEJ1dGV1cjogbnVtYmVyO1xyXG5cclxuXHRDbGFzc2VtZW50c19Qb2ludHNHZW5lcmFsTWF0Y2g6IG51bWJlcjtcclxuXHRDbGFzc2VtZW50c19Qb2ludHNHZW5lcmFsQnV0ZXVyOiBudW1iZXI7XHJcblxyXG5cdFByb25vc3RpcXVldXI6IG51bWJlcjtcclxuXHRQcm9ub3N0aXF1ZXVyc19Ob21VdGlsaXNhdGV1cjogc3RyaW5nO1xyXG5cdFByb25vc3RpcXVldXJzX1Bob3RvOiBzdHJpbmc7XHJcbn1cclxuXHJcblxyXG5jbGFzcyBTdGFuZGluZ1dlZWsge1xyXG5cdFNhaXNvbnNfU2Fpc29uOiBudW1iZXI7XHJcblx0Sm91cm5lZXNfSm91cm5lZTogbnVtYmVyO1xyXG5cdENsYXNzZW1lbnRzX0RhdGVSZWZlcmVuY2U6IHN0cmluZztcclxuXHJcblx0Q2xhc3NlbWVudHNfQ2xhc3NlbWVudEpvdXJuZWVNYXRjaDogbnVtYmVyO1xyXG5cdENsYXNzZW1lbnRzX0NsYXNzZW1lbnRKb3VybmVlQnV0ZXVyOiBudW1iZXI7XHJcblxyXG5cdENsYXNzZW1lbnRzX1BvaW50c0pvdXJuZWVNYXRjaDogbnVtYmVyO1xyXG5cdENsYXNzZW1lbnRzX1BvaW50c0pvdXJuZWVCdXRldXI6IG51bWJlcjtcclxuXHJcblx0UHJvbm9zdGlxdWV1cjogbnVtYmVyO1xyXG5cdFByb25vc3RpcXVldXJzX05vbVV0aWxpc2F0ZXVyOiBzdHJpbmc7XHJcblx0UHJvbm9zdGlxdWV1cnNfUGhvdG86IHN0cmluZztcclxufVxyXG5cclxuXHJcbmNsYXNzIFN0YW5kaW5nR29hbCB7XHJcblx0U2Fpc29uc19TYWlzb246IG51bWJlcjtcclxuXHRKb3VybmVlc19Kb3VybmVlOiBudW1iZXI7XHJcblx0Q2xhc3NlbWVudHNfRGF0ZVJlZmVyZW5jZTogc3RyaW5nO1xyXG5cclxuXHRDbGFzc2VtZW50c19DbGFzc2VtZW50R2VuZXJhbEJ1dGV1cjogbnVtYmVyO1xyXG5cdENsYXNzZW1lbnRzX1BvaW50c0dlbmVyYWxCdXRldXI6IG51bWJlcjtcclxuXHJcblx0UHJvbm9zdGlxdWV1cjogbnVtYmVyO1xyXG5cdFByb25vc3RpcXVldXJzX05vbVV0aWxpc2F0ZXVyOiBzdHJpbmc7XHJcblx0UHJvbm9zdGlxdWV1cnNfUGhvdG86IHN0cmluZztcclxufVxyXG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiYW5ndWxhci5kLnRzXCIgLz5cclxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIm1vZGVscy9zZWFzb24udHNcIiAvPlxyXG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwibW9kZWxzL2NoYW1waW9uc2hpcC50c1wiIC8+XHJcbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJtb2RlbHMvd2Vlay50c1wiIC8+XHJcbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJtb2RlbHMvc3RhbmRpbmcudHNcIiAvPlxyXG5cclxuXHJcbm1vZHVsZSBBcHBsaWNhdGlvbi5Db250cm9sbGVyc3tcclxuICAgIGV4cG9ydCBjbGFzcyBTdGFuZGluZ3NDb250cm9sbGVyIHtcclxuICAgICAgICBwcml2YXRlIHNlYXNvbnM6IGFueVtdO1xyXG4gICAgICAgIHByaXZhdGUgY2hhbXBpb25zaGlwczogYW55W107XHJcbiAgICAgICAgcHJpdmF0ZSB3ZWVrczogYW55W107XHJcbiAgICAgICAgcHJpdmF0ZSBzdGFuZGluZ3M6IGFueVtdO1xyXG4gICAgICAgIHByaXZhdGUgc3RhbmRpbmdzV2VlazogYW55W107XHJcbiAgICAgICAgcHJpdmF0ZSBzdGFuZGluZ3NHb2FsOiBhbnlbXTtcclxuXHJcblxyXG4gICAgICAgIHByaXZhdGUgY3VycmVudFNlYXNvbjogU2Vhc29uO1xyXG4gICAgICAgIHByaXZhdGUgY3VycmVudENoYW1waW9uc2hpcDogQ2hhbXBpb25zaGlwO1xyXG4gICAgICAgIHByaXZhdGUgY3VycmVudFdlZWs6IFdlZWs7XHJcbiAgICAgICAgcHJpdmF0ZSBjdXJyZW50UmVmZXJlbmNlRGF0ZTogYW55O1xyXG5cclxuICAgICAgICBwcml2YXRlIHNlcnZpY2U6IGFueTtcclxuICAgICAgICBcclxuICAgICAgICBjb25zdHJ1Y3RvcihzdGFuZGluZ3NTZXJ2aWNlOiBhbnkpIHtcclxuICAgICAgICAgICAgdGhpcy5zZXJ2aWNlID0gc3RhbmRpbmdzU2VydmljZTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgICRvbkluaXQoKSB7XHJcbiAgICAgICAgICAgIC8vIEdldCBhbGwgc2Vhc29uc1xyXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0U2Vhc29ucygpLnRoZW4oKHNlYXNvbnMpID0+IHtcclxuICAgICAgICAgICAgICAgIHRoaXMuc2Vhc29ucyA9IHNlYXNvbnM7XHJcbiAgICAgICAgICAgIH0sIChlcnIpID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdTdGFuZGluZ3NDb250cm9sbGVyICRvbkluaXQoKTogRXJyb3IgZHVyaW5nIHJlYWRpbmcgc2Vhc29ucycpO1xyXG4gICAgICAgICAgICB9KTsgXHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvKiBTZWxlY3QgYSBzZWFzb24gKi9cclxuICAgICAgICBzZWxlY3RTZWFzb24oc2Vhc29uOiBTZWFzb24pOiB2b2lkIHtcclxuICAgICAgICAgICAgaWYodGhpcy5jdXJyZW50U2Vhc29uID09PSBzZWFzb24pXHJcbiAgICAgICAgICAgICAgICByZXR1cm47XHJcblxyXG4gICAgICAgICAgICB0aGlzLmN1cnJlbnRTZWFzb24gPSBzZWFzb247XHJcblxyXG4gICAgICAgICAgICB0aGlzLmNoYW1waW9uc2hpcHMgPSBbXTtcclxuICAgICAgICAgICAgdGhpcy53ZWVrcyA9IFtdO1xyXG4gICAgICAgICAgICB0aGlzLnN0YW5kaW5ncyA9IFtdO1xyXG5cclxuICAgICAgICAgICAgLy8gR2V0IGFsbCBleGlzdGluZyBjaGFtcGlvbnNoaXBzIGV4Y2VwdCB0aGUgRnJlbmNoIEN1cCBmb3IgdGhlIHNlbGVjdGVkIHNlYXNvblxyXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0Q2hhbXBpb25zaGlwcyh0aGlzLmN1cnJlbnRTZWFzb24pLnRoZW4oKGNoYW1waW9uc2hpcHMpID0+IHtcclxuICAgICAgICAgICAgICAgIHRoaXMuY2hhbXBpb25zaGlwcyA9IGNoYW1waW9uc2hpcHM7XHJcbiAgICAgICAgICAgIH0sIChlcnIpID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdTdGFuZGluZ3NDb250cm9sbGVyIHNlbGVjdFNlYXNvbigpOiBFcnJvciBkdXJpbmcgcmVhZGluZyBjaGFtcGlvbnNoaXBzJyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLyogU2VsZWN0IGEgY2hhbXBpb25zaGlwICovXHJcbiAgICAgICAgc2VsZWN0Q2hhbXBpb25zaGlwKGNoYW1waW9uc2hpcDogQ2hhbXBpb25zaGlwKTogdm9pZCB7XHJcbiAgICAgICAgICAgIHRoaXMuY3VycmVudENoYW1waW9uc2hpcCA9IGNoYW1waW9uc2hpcDtcclxuXHJcbiAgICAgICAgICAgIC8qIFNlbGVjdCBhbGwgd2Vla3MgZm9yIHRoYXQgY2hhbXBpb25zaGlwICovXHJcbiAgICAgICAgICAgIHRoaXMuc2VydmljZS5nZXRXZWVrcyh0aGlzLmN1cnJlbnRTZWFzb24sIHRoaXMuY3VycmVudENoYW1waW9uc2hpcCkudGhlbigod2Vla3MpID0+IHtcclxuICAgICAgICAgICAgICAgIHRoaXMud2Vla3MgPSB3ZWVrcztcclxuICAgICAgICAgICAgICAgIHRoaXMuc3RhbmRpbmdzID0gW107XHJcbiAgICAgICAgICAgIH0sIChlcnIpID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdTdGFuZGluZ3NDb250cm9sbGVyIHNlbGVjdENoYW1waW9uc2hpcCgpOiBFcnJvciBkdXJpbmcgcmVhZGluZyB3ZWVrcycpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8qIFNlbGVjdCBhIHdlZWsgYW5kIGEgcmVmZXJlbmNlIGRhdGUgKi9cclxuICAgICAgICBzZWxlY3RXZWVrKHdlZWs6IFdlZWssIHJlZmVyZW5jZURhdGU6IHN0cmluZyk6IGFueSB7XHJcbiAgICAgICAgICAgIHRoaXMuY3VycmVudFdlZWsgPSB3ZWVrO1xyXG4gICAgICAgICAgICB0aGlzLmN1cnJlbnRSZWZlcmVuY2VEYXRlID0gcmVmZXJlbmNlRGF0ZTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuc2VydmljZS5nZXRTdGFuZGluZ3ModGhpcy5jdXJyZW50U2Vhc29uLCB0aGlzLmN1cnJlbnRXZWVrLCB0aGlzLmN1cnJlbnRSZWZlcmVuY2VEYXRlKS50aGVuKChzdGFuZGluZ3MpID0+IHtcclxuICAgICAgICAgICAgICAgIHRoaXMuc3RhbmRpbmdzID0gc3RhbmRpbmdzO1xyXG4gICAgICAgICAgICB9LCAoZXJyKSA9PiB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnU3RhbmRpbmdzQ29udHJvbGxlciAkb25Jbml0KCk6IEVycm9yIGR1cmluZyByZWFkaW5nIHN0YW5kaW5ncycpO1xyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuc2VydmljZS5nZXRTdGFuZGluZ3NXZWVrKHRoaXMuY3VycmVudFNlYXNvbiwgdGhpcy5jdXJyZW50V2VlaywgdGhpcy5jdXJyZW50UmVmZXJlbmNlRGF0ZSkudGhlbigoc3RhbmRpbmdzV2VlaykgPT4ge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5zdGFuZGluZ3NXZWVrID0gc3RhbmRpbmdzV2VlaztcclxuICAgICAgICAgICAgfSwgKGVycikgPT4ge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1N0YW5kaW5nc0NvbnRyb2xsZXIgJG9uSW5pdCgpOiBFcnJvciBkdXJpbmcgcmVhZGluZyBzdGFuZGluZ3Mgd2VlaycpO1xyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuc2VydmljZS5nZXRTdGFuZGluZ3NHb2FsKHRoaXMuY3VycmVudFNlYXNvbiwgdGhpcy5jdXJyZW50V2VlaywgdGhpcy5jdXJyZW50UmVmZXJlbmNlRGF0ZSkudGhlbigoc3RhbmRpbmdzR29hbCkgPT4ge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5zdGFuZGluZ3NHb2FsID0gc3RhbmRpbmdzR29hbDtcclxuICAgICAgICAgICAgfSwgKGVycikgPT4ge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1N0YW5kaW5nc0NvbnRyb2xsZXIgJG9uSW5pdCgpOiBFcnJvciBkdXJpbmcgcmVhZGluZyBzdGFuZGluZ3MgZ29hbCcpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcbn0iLCJjbGFzcyBUcm9waHkge1xuICAgIFByb25vc3RpcXVldXI6IG51bWJlcjtcbiAgICBQcm9ub3N0aXF1ZXVyc19Ob21VdGlsaXNhdGV1cjogc3RyaW5nO1xuXG4gICAgVHJvcGhlZTogbnVtYmVyO1xuXG59XG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiYW5ndWxhci5kLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJtb2RlbHMvc2Vhc29uLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJtb2RlbHMvY2hhbXBpb25zaGlwLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJtb2RlbHMvdHJvcGh5LnRzXCIgLz5cblxuXG5tb2R1bGUgQXBwbGljYXRpb24uQ29udHJvbGxlcnN7XG4gICAgZXhwb3J0IGNsYXNzIFRyb3BoaWVzQ29udHJvbGxlciB7XG4gICAgICAgIHByaXZhdGUgc2Vhc29uczogYW55W107XG4gICAgICAgIHByaXZhdGUgY2hhbXBpb25zaGlwczogYW55W107XG4gICAgICAgIHByaXZhdGUgdHJvcGhpZXM6IGFueVtdO1xuXG4gICAgICAgIHByaXZhdGUgY3VycmVudFNlYXNvbjogU2Vhc29uO1xuICAgICAgICBwcml2YXRlIGN1cnJlbnRDaGFtcGlvbnNoaXA6IENoYW1waW9uc2hpcDtcblxuICAgICAgICBwcml2YXRlIHNlcnZpY2U6IGFueTtcbiAgICAgICAgXG4gICAgICAgIGNvbnN0cnVjdG9yKHRyb3BoaWVzU2VydmljZTogYW55KSB7XG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UgPSB0cm9waGllc1NlcnZpY2U7XG4gICAgICAgIH1cblxuICAgICAgICAkb25Jbml0KCkge1xuICAgICAgICAgICAgLy8gR2V0IGFsbCBzZWFzb25zXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0U2Vhc29ucygpLnRoZW4oKHNlYXNvbnMpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnNlYXNvbnMgPSBzZWFzb25zO1xuICAgICAgICAgICAgfSwgKGVycikgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdUcm9waGllc0NvbnRyb2xsZXIgJG9uSW5pdCgpOiBFcnJvciBkdXJpbmcgcmVhZGluZyBzZWFzb25zJyk7XG4gICAgICAgICAgICB9KTsgXG4gICAgICAgIH1cblxuICAgICAgICAvKiBTZWxlY3QgYSBzZWFzb24gKi9cbiAgICAgICAgc2VsZWN0U2Vhc29uKHNlYXNvbjogU2Vhc29uKTogdm9pZCB7XG4gICAgICAgICAgICBpZih0aGlzLmN1cnJlbnRTZWFzb24gPT09IHNlYXNvbilcbiAgICAgICAgICAgICAgICByZXR1cm47XG5cbiAgICAgICAgICAgIHRoaXMuY3VycmVudFNlYXNvbiA9IHNlYXNvbjtcbiAgICAgICAgICAgIHRoaXMuY2hhbXBpb25zaGlwcyA9IFtdO1xuICAgICAgICAgICAgdGhpcy50cm9waGllcyA9IFtdO1xuXG4gICAgICAgICAgICAvLyBHZXQgYWxsIGV4aXN0aW5nIGNoYW1waW9uc2hpcHMgZXhjZXB0IHRoZSBGcmVuY2ggQ3VwIGZvciB0aGUgc2VsZWN0ZWQgc2Vhc29uXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0Q2hhbXBpb25zaGlwcyh0aGlzLmN1cnJlbnRTZWFzb24pLnRoZW4oKGNoYW1waW9uc2hpcHMpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLmNoYW1waW9uc2hpcHMgPSBjaGFtcGlvbnNoaXBzO1xuICAgICAgICAgICAgfSwgKGVycikgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdUcm9waGllc0NvbnRyb2xsZXIgc2VsZWN0U2Vhc29uKCk6IEVycm9yIGR1cmluZyByZWFkaW5nIGNoYW1waW9uc2hpcHMnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgLyogU2VsZWN0IGEgY2hhbXBpb25zaGlwICovXG4gICAgICAgIHNlbGVjdENoYW1waW9uc2hpcChjaGFtcGlvbnNoaXA6IENoYW1waW9uc2hpcCk6IHZvaWQge1xuICAgICAgICAgICAgdGhpcy5jdXJyZW50Q2hhbXBpb25zaGlwID0gY2hhbXBpb25zaGlwO1xuXG4gICAgICAgICAgICAvKiBHZXQgYWxsIHRyb3BoaWVzICovXG4gICAgICAgICAgICB0aGlzLnNlcnZpY2UuZ2V0VHJvcGhpZXModGhpcy5jdXJyZW50U2Vhc29uLCB0aGlzLmN1cnJlbnRDaGFtcGlvbnNoaXApLnRoZW4oKHRyb3BoaWVzKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy50cm9waGllcyA9IHRyb3BoaWVzO1xuICAgICAgICAgICAgfSwgKGVycikgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdUcm9waGllc0NvbnRyb2xsZXIgJG9uSW5pdCgpOiBFcnJvciBkdXJpbmcgcmVhZGluZyB0cm9waGllcycpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG59IiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cImFuZ3VsYXIuZC50c1wiIC8+XHJcblxyXG5cclxubW9kdWxlIEFwcGxpY2F0aW9uLlNlcnZpY2VzIHtcclxuXHRleHBvcnQgY2xhc3MgRm9yZWNhc3RlcnNTZXJ2aWNlIHtcclxuXHRcdHByaXZhdGUgaHR0cDogYW55O1xyXG5cclxuXHRcdGNvbnN0cnVjdG9yKCRodHRwOiBuZy5JSHR0cFNlcnZpY2UpIHtcclxuXHRcdFx0dGhpcy5odHRwID0gJGh0dHA7XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdC8qIEdldCBhbGwgZm9yZWNhc3RlcnMgKi9cclxuXHRcdGdldEZvcmVjYXN0ZXJzKCk6IGFueSB7XHJcblx0XHRcdGxldCB1cmwgPSBcIi4vZGlzdC9mb3JlY2FzdGVycy5waHBcIjtcclxuXHJcblx0XHRcdHJldHVybiB0aGlzLmh0dHAoe1xyXG5cdFx0XHRcdG1ldGhvZDogJ1BPU1QnLFxyXG5cdFx0XHRcdHVybDogdXJsXHJcblx0XHRcdH0pLnRoZW4oZnVuY3Rpb24gc3VjY2Vzc0NhbGxiYWNrKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0cmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcblx0XHRcdH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuXHRcdFx0XHRsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcblx0XHRcdFx0XHRlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiAnZm9yZWNhc3RlcnMtc2VydmljZSBnZXRGb3JlY2FzdGVyczogU2VydmVyIGVycm9yJztcclxuXHRcdFx0XHRjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcclxuXHRcdFx0XHRyZXR1cm4gW107XHJcblx0XHRcdH0pO1x0XHJcblx0XHR9XHJcblxyXG5cdFx0LyogQ3JlYXRlIGEgbmV3IGZvcmVjYXN0ZXIgKi9cclxuXHRcdGNyZWF0ZUZvcmVjYXN0ZXIoZm9yZWNhc3RlcjogYW55KTogYW55IHtcclxuXHRcdFx0bGV0IHVybCA9IFwiLi9kaXN0L2NyZWF0ZS1mb3JlY2FzdGVyLnBocFwiO1xyXG5cclxuXHRcdFx0bGV0IGRhdGEgPSBKU09OLnN0cmluZ2lmeShmb3JlY2FzdGVyKTtcclxuXHJcblx0XHRcdHJldHVybiB0aGlzLmh0dHAoe1xyXG5cdFx0XHRcdG1ldGhvZDogJ1BPU1QnLFxyXG5cdFx0XHRcdHVybDogdXJsLFxyXG5cdFx0XHRcdGRhdGE6IHsgZGF0YTogZGF0YSB9XHJcblx0XHRcdH0pLnRoZW4oZnVuY3Rpb24gc3VjY2Vzc0NhbGxiYWNrKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0cmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcblx0XHRcdH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuXHRcdFx0XHRsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcblx0XHRcdFx0XHRlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiAnZm9yZWNhc3RlcnMtc2VydmljZSBjcmVhdGVGb3JlY2FzdGVyOiBTZXJ2ZXIgZXJyb3InO1xyXG5cdFx0XHRcdGNvbnNvbGUuZXJyb3IoZXJyTXNnKTsgLy8gbG9nIHRvIGNvbnNvbGUgaW5zdGVhZFxyXG5cdFx0XHRcdHJldHVybiBbXTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyogVXBkYXRlIGZvcmVjYXN0ZXIgKi9cclxuXHRcdHVwZGF0ZUZvcmVjYXN0ZXIoZm9yZWNhc3RlcjogYW55KTogYW55IHtcclxuXHRcdFx0bGV0IHVybCA9IFwiLi9kaXN0L3VwZGF0ZS1mb3JlY2FzdGVyLnBocFwiO1xyXG5cclxuXHRcdFx0bGV0IGRhdGEgPSBKU09OLnN0cmluZ2lmeShmb3JlY2FzdGVyKTtcclxuXHJcblx0XHRcdHJldHVybiB0aGlzLmh0dHAoe1xyXG5cdFx0XHRcdG1ldGhvZDogJ1BPU1QnLFxyXG5cdFx0XHRcdHVybDogdXJsLFxyXG5cdFx0XHRcdGRhdGE6IHsgZGF0YTogZGF0YSB9XHJcblx0XHRcdH0pLnRoZW4oZnVuY3Rpb24gc3VjY2Vzc0NhbGxiYWNrKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0cmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcblx0XHRcdH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuXHRcdFx0XHRsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcblx0XHRcdFx0XHRlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiAnZm9yZWNhc3RlcnMtc2VydmljZSB1cGRhdGVGb3JlY2FzdGVyOiBTZXJ2ZXIgZXJyb3InO1xyXG5cdFx0XHRcdGNvbnNvbGUuZXJyb3IoZXJyTXNnKTsgLy8gbG9nIHRvIGNvbnNvbGUgaW5zdGVhZFxyXG5cdFx0XHRcdHJldHVybiBbXTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyogTW92ZSBmb3JlY2FzdGVyIHRvIHRoZSBvbGQgZm9yZWNhc3RlcnMgbGlzdCAqL1xyXG5cdFx0bW92ZUZvcmVjYXN0ZXIoZm9yZWNhc3RlcjogYW55KTogYW55IHtcclxuXHRcdFx0bGV0IHVybCA9ICcuL2Rpc3QvbW92ZS1mb3JlY2FzdGVyLnBocCc7XHJcblxyXG5cdFx0XHRsZXQgZGF0YSA9IEpTT04uc3RyaW5naWZ5KGZvcmVjYXN0ZXIpO1xyXG5cclxuXHRcdFx0cmV0dXJuIHRoaXMuaHR0cCh7XHJcblx0XHRcdFx0bWV0aG9kOiAnUE9TVCcsXHJcblx0XHRcdFx0dXJsOiB1cmwsXHJcblx0XHRcdFx0ZGF0YTogeyBkYXRhOiBkYXRhIH1cclxuXHRcdFx0fSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcclxuXHRcdFx0XHRyZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcclxuXHRcdFx0fSwgZnVuY3Rpb24gZXJyb3JDYWxsYmFjayhlcnJvcikge1xyXG5cdFx0XHRcdGxldCBlcnJNc2cgPSAoZXJyb3IubWVzc2FnZSkgPyBlcnJvci5tZXNzYWdlIDpcclxuXHRcdFx0XHRcdGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6ICdmb3JlY2FzdGVycy1zZXJ2aWNlIG1vdmVGb3JlY2FzdGVyOiBTZXJ2ZXIgZXJyb3InO1xyXG5cdFx0XHRcdGNvbnNvbGUuZXJyb3IoZXJyTXNnKTsgLy8gbG9nIHRvIGNvbnNvbGUgaW5zdGVhZFxyXG5cdFx0XHRcdHJldHVybiBbXTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyogRGVsZXRlIGZvcmVjYXN0ZXIgKi9cclxuXHRcdGRlbGV0ZUZvcmVjYXN0ZXIoZm9yZWNhc3RlcjogYW55KTogYW55IHtcclxuXHRcdFx0bGV0IHVybCA9ICcuL2Rpc3QvZGVsZXRlLWZvcmVjYXN0ZXIucGhwJztcclxuXHJcblx0XHRcdGxldCBkYXRhID0gSlNPTi5zdHJpbmdpZnkoZm9yZWNhc3Rlcik7XHJcblxyXG5cdFx0XHRyZXR1cm4gdGhpcy5odHRwKHtcclxuXHRcdFx0XHRtZXRob2Q6ICdQT1NUJyxcclxuXHRcdFx0XHR1cmw6IHVybCxcclxuXHRcdFx0XHRkYXRhOiB7IGRhdGE6IGRhdGEgfVxyXG5cdFx0XHR9KS50aGVuKGZ1bmN0aW9uIHN1Y2Nlc3NDYWxsYmFjayhyZXNwb25zZSkge1xyXG5cdFx0XHRcdHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xyXG5cdFx0XHR9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XHJcblx0XHRcdFx0bGV0IGVyck1zZyA9IChlcnJvci5tZXNzYWdlKSA/IGVycm9yLm1lc3NhZ2UgOlxyXG5cdFx0XHRcdFx0ZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogJ2ZvcmVjYXN0ZXJzLXNlcnZpY2UgZGVsZXRlRm9yZWNhc3RlcjogU2VydmVyIGVycm9yJztcclxuXHRcdFx0XHRjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcclxuXHRcdFx0XHRyZXR1cm4gW107XHJcblx0XHRcdH0pO1xyXG5cdFx0fVxyXG5cclxuXHJcblx0fVxyXG59IiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cImFuZ3VsYXIuZC50c1wiIC8+XHJcblxyXG5cclxubW9kdWxlIEFwcGxpY2F0aW9uLlNlcnZpY2VzIHtcclxuXHRleHBvcnQgY2xhc3MgU3RhbmRpbmdzU2VydmljZSB7XHJcblx0XHRwcml2YXRlIGh0dHA6IGFueTtcclxuXHJcblx0XHRjb25zdHJ1Y3RvcigkaHR0cDogbmcuSUh0dHBTZXJ2aWNlKSB7XHJcblx0XHRcdHRoaXMuaHR0cCA9ICRodHRwO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qIEdldCBhbGwgc2Vhc29ucyAqL1xyXG5cdFx0Z2V0U2Vhc29ucygpOiBhbnkge1xyXG5cdFx0XHRsZXQgdXJsID0gJy4vZGlzdC9zZWFzb25zLnBocCc7XHJcblxyXG5cdFx0XHRyZXR1cm4gdGhpcy5odHRwKHtcclxuXHRcdFx0XHRtZXRob2Q6ICdQT1NUJyxcclxuXHRcdFx0XHR1cmw6IHVybFxyXG5cdFx0XHR9KS50aGVuKGZ1bmN0aW9uIHN1Y2Nlc3NDYWxsYmFjayhyZXNwb25zZSkge1xyXG5cdFx0XHRcdHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xyXG5cdFx0XHR9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XHJcblx0XHRcdFx0bGV0IGVyck1zZyA9IChlcnJvci5tZXNzYWdlKSA/IGVycm9yLm1lc3NhZ2UgOlxyXG5cdFx0XHRcdFx0ZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogJ3N0YW5kaW5ncy1zZXJ2aWNlIGdldFNlYXNvbnM6IFNlcnZlciBlcnJvcic7XHJcblx0XHRcdFx0Y29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcblx0XHRcdFx0cmV0dXJuIFtdO1xyXG5cdFx0XHR9KTtcdFxyXG5cdFx0fVxyXG5cclxuXHRcdC8qIEdldCBhbGwgY2hhbXBpb25zaGlwcyAqL1xyXG5cdFx0Z2V0Q2hhbXBpb25zaGlwcyhzZWFzb246IFNlYXNvbik6IGFueSB7XHJcblx0XHRcdGxldCB1cmwgPSBcIi4vZGlzdC9jaGFtcGlvbnNoaXBzLnBocFwiO1xyXG5cclxuXHRcdFx0cmV0dXJuIHRoaXMuaHR0cCh7XHJcblx0XHRcdFx0bWV0aG9kOiAnUE9TVCcsXHJcblx0XHRcdFx0dXJsOiB1cmwsXHJcblx0XHRcdFx0ZGF0YToge3NhaXNvbjogc2Vhc29ufVxyXG5cdFx0XHR9KS50aGVuKGZ1bmN0aW9uIHN1Y2Nlc3NDYWxsYmFjayhyZXNwb25zZSkge1xyXG5cdFx0XHRcdHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xyXG5cdFx0XHR9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XHJcblx0XHRcdFx0bGV0IGVyck1zZyA9IChlcnJvci5tZXNzYWdlKSA/IGVycm9yLm1lc3NhZ2UgOlxyXG5cdFx0XHRcdFx0ZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogJ3N0YW5kaW5ncy1zZXJ2aWNlIGdldENoYW1waW9uc2hpcHM6IFNlcnZlciBlcnJvcic7XHJcblx0XHRcdFx0Y29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcblx0XHRcdFx0cmV0dXJuIFtdO1xyXG5cdFx0XHR9KTtcdFxyXG5cdFx0fVxyXG5cclxuXHRcdC8qIEdldCBhbGwgd2Vla3MgKi9cclxuXHRcdGdldFdlZWtzKHNlYXNvbjogU2Vhc29uLCBjaGFtcGlvbnNoaXA6IENoYW1waW9uc2hpcCk6IGFueSB7XHJcblx0XHRcdGxldCB1cmwgPSBcIi4vZGlzdC93ZWVrcy5waHBcIjtcclxuXHJcblx0XHRcdHJldHVybiB0aGlzLmh0dHAoe1xyXG5cdFx0XHRcdG1ldGhvZDogJ1BPU1QnLFxyXG5cdFx0XHRcdHVybDogdXJsLFxyXG5cdFx0XHRcdGRhdGE6IHtzYWlzb246IHNlYXNvbi5TYWlzb24sIGNoYW1waW9ubmF0OiBjaGFtcGlvbnNoaXAuQ2hhbXBpb25uYXR9XHJcblx0XHRcdH0pLnRoZW4oZnVuY3Rpb24gc3VjY2Vzc0NhbGxiYWNrKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0cmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcblx0XHRcdH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuXHRcdFx0XHRsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcblx0XHRcdFx0XHRlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiAnc3RhbmRpbmdzLXNlcnZpY2UgZ2V0V2Vla3M6IFNlcnZlciBlcnJvcic7XHJcblx0XHRcdFx0Y29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcblx0XHRcdFx0cmV0dXJuIFtdO1xyXG5cdFx0XHR9KTtcdFxyXG5cdFx0fVxyXG5cclxuXHRcdC8qIEdldCBnZW5lcmFsIHN0YW5kaW5ncyBmb3IgYSB3ZWVrICovXHJcblx0XHRnZXRTdGFuZGluZ3Moc2Vhc29uOiBTZWFzb24sIHdlZWs6IFdlZWssIHJlZmVyZW5jZURhdGU6IHN0cmluZyk6IGFueSB7XHJcblx0XHRcdGxldCB1cmwgPSBcIi4vZGlzdC9zdGFuZGluZ3MucGhwXCI7XHJcblxyXG5cdFx0XHRyZXR1cm4gdGhpcy5odHRwKHtcclxuXHRcdFx0XHRtZXRob2Q6ICdQT1NUJyxcclxuXHRcdFx0XHR1cmw6IHVybCxcclxuXHRcdFx0XHRkYXRhOiBKU09OLnN0cmluZ2lmeSh7XCJzYWlzb25cIjogc2Vhc29uLlNhaXNvbiwgXCJqb3VybmVlXCI6IHdlZWsuSm91cm5lZSwgXCJkYXRlLXJlZmVyZW5jZVwiOiByZWZlcmVuY2VEYXRlfSlcclxuXHRcdFx0fSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcclxuXHRcdFx0XHRyZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcclxuXHRcdFx0fSwgZnVuY3Rpb24gZXJyb3JDYWxsYmFjayhlcnJvcikge1xyXG5cdFx0XHRcdGxldCBlcnJNc2cgPSAoZXJyb3IubWVzc2FnZSkgPyBlcnJvci5tZXNzYWdlIDpcclxuXHRcdFx0XHRcdGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6ICdzdGFuZGluZ3Mtc2VydmljZSBnZXRTdGFuZGluZ3M6IFNlcnZlciBlcnJvcic7XHJcblx0XHRcdFx0Y29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXHJcblx0XHRcdFx0cmV0dXJuIFtdO1xyXG5cdFx0XHR9KTtcdFxyXG5cdFx0fVxyXG5cclxuXHRcdC8qIEdldCB3ZWVrIHN0YW5kaW5ncyAqL1xyXG5cdFx0Z2V0U3RhbmRpbmdzV2VlayhzZWFzb246IFNlYXNvbiwgd2VlazogV2VlaywgcmVmZXJlbmNlRGF0ZTogc3RyaW5nKTogYW55IHtcclxuXHRcdFx0bGV0IHVybCA9IFwiLi9kaXN0L3N0YW5kaW5ncy13ZWVrLnBocFwiO1xyXG5cclxuXHRcdFx0cmV0dXJuIHRoaXMuaHR0cCh7XHJcblx0XHRcdFx0bWV0aG9kOiAnUE9TVCcsXHJcblx0XHRcdFx0dXJsOiB1cmwsXHJcblx0XHRcdFx0ZGF0YTogSlNPTi5zdHJpbmdpZnkoe1wic2Fpc29uXCI6IHNlYXNvbi5TYWlzb24sIFwiam91cm5lZVwiOiB3ZWVrLkpvdXJuZWUsIFwiZGF0ZS1yZWZlcmVuY2VcIjogcmVmZXJlbmNlRGF0ZX0pXHJcblx0XHRcdH0pLnRoZW4oZnVuY3Rpb24gc3VjY2Vzc0NhbGxiYWNrKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0cmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XHJcblx0XHRcdH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcclxuXHRcdFx0XHRsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XHJcblx0XHRcdFx0XHRlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiAnc3RhbmRpbmdzLXNlcnZpY2UgZ2V0U3RhbmRpbmdzV2VlazogU2VydmVyIGVycm9yJztcclxuXHRcdFx0XHRjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcclxuXHRcdFx0XHRyZXR1cm4gW107XHJcblx0XHRcdH0pO1x0XHJcblx0XHR9XHJcblxyXG5cdFx0LyogR2V0IGdvYWwgc3RhbmRpbmdzICovXHJcblx0XHRnZXRTdGFuZGluZ3NHb2FsKHNlYXNvbjogU2Vhc29uLCB3ZWVrOiBXZWVrLCByZWZlcmVuY2VEYXRlOiBzdHJpbmcpOiBhbnkge1xyXG5cdFx0XHRsZXQgdXJsID0gXCIuL2Rpc3Qvc3RhbmRpbmdzLWdvYWwucGhwXCI7XHJcblxyXG5cdFx0XHRyZXR1cm4gdGhpcy5odHRwKHtcclxuXHRcdFx0XHRtZXRob2Q6ICdQT1NUJyxcclxuXHRcdFx0XHR1cmw6IHVybCxcclxuXHRcdFx0XHRkYXRhOiBKU09OLnN0cmluZ2lmeSh7XCJzYWlzb25cIjogc2Vhc29uLlNhaXNvbiwgXCJqb3VybmVlXCI6IHdlZWsuSm91cm5lZSwgXCJkYXRlLXJlZmVyZW5jZVwiOiByZWZlcmVuY2VEYXRlfSlcclxuXHRcdFx0fSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcclxuXHRcdFx0XHRyZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcclxuXHRcdFx0fSwgZnVuY3Rpb24gZXJyb3JDYWxsYmFjayhlcnJvcikge1xyXG5cdFx0XHRcdGxldCBlcnJNc2cgPSAoZXJyb3IubWVzc2FnZSkgPyBlcnJvci5tZXNzYWdlIDpcclxuXHRcdFx0XHRcdGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6ICdzdGFuZGluZ3Mtc2VydmljZSBnZXRTdGFuZGluZ3NHb2FsOiBTZXJ2ZXIgZXJyb3InO1xyXG5cdFx0XHRcdGNvbnNvbGUuZXJyb3IoZXJyTXNnKTsgLy8gbG9nIHRvIGNvbnNvbGUgaW5zdGVhZFxyXG5cdFx0XHRcdHJldHVybiBbXTtcclxuXHRcdFx0fSk7XHRcclxuXHRcdH1cclxuXHR9XHJcbn0iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiYW5ndWxhci5kLnRzXCIgLz5cblxuXG5tb2R1bGUgQXBwbGljYXRpb24uU2VydmljZXMge1xuICAgIGV4cG9ydCBjbGFzcyBUcm9waGllc1NlcnZpY2Uge1xuICAgICAgICBwcml2YXRlIGh0dHA6IGFueTtcblxuICAgICAgICBjb25zdHJ1Y3RvcigkaHR0cDogbmcuSUh0dHBTZXJ2aWNlKSB7XG4gICAgICAgICAgICB0aGlzLmh0dHAgPSAkaHR0cDtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIEdldCBhbGwgc2Vhc29ucyAqL1xuICAgICAgICBnZXRTZWFzb25zKCk6IGFueSB7XG4gICAgICAgICAgICBsZXQgdXJsID0gJy4vZGlzdC9zZWFzb25zLnBocCc7XG5cbiAgICAgICAgICAgIHJldHVybiB0aGlzLmh0dHAoe1xuICAgICAgICAgICAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgICAgICAgICAgIHVybDogdXJsXG4gICAgICAgICAgICB9KS50aGVuKGZ1bmN0aW9uIHN1Y2Nlc3NDYWxsYmFjayhyZXNwb25zZSkge1xuICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZS5kYXRhIHx8IHt9O1xuICAgICAgICAgICAgfSwgZnVuY3Rpb24gZXJyb3JDYWxsYmFjayhlcnJvcikge1xuICAgICAgICAgICAgICAgIGxldCBlcnJNc2cgPSAoZXJyb3IubWVzc2FnZSkgPyBlcnJvci5tZXNzYWdlIDpcbiAgICAgICAgICAgICAgICAgICAgZXJyb3Iuc3RhdHVzID8gYCR7ZXJyb3Iuc3RhdHVzfSAtICR7ZXJyb3Iuc3RhdHVzVGV4dH1gIDogJ3Ryb3BoaWVzLXNlcnZpY2UgZ2V0U2Vhc29uczogU2VydmVyIGVycm9yJztcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgICAgICB9KTsgXG4gICAgICAgIH1cblxuICAgICAgICAvKiBHZXQgYWxsIGNoYW1waW9uc2hpcHMgKi9cbiAgICAgICAgZ2V0Q2hhbXBpb25zaGlwcyhzZWFzb246IFNlYXNvbik6IGFueSB7XG4gICAgICAgICAgICBsZXQgdXJsID0gXCIuL2Rpc3QvY2hhbXBpb25zaGlwcy5waHBcIjtcblxuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaHR0cCh7XG4gICAgICAgICAgICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICAgICAgICAgICAgdXJsOiB1cmwsXG4gICAgICAgICAgICAgICAgZGF0YToge3NhaXNvbjogc2Vhc29ufVxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiBzdWNjZXNzQ2FsbGJhY2socmVzcG9uc2UpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2UuZGF0YSB8fCB7fTtcbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uIGVycm9yQ2FsbGJhY2soZXJyb3IpIHtcbiAgICAgICAgICAgICAgICBsZXQgZXJyTXNnID0gKGVycm9yLm1lc3NhZ2UpID8gZXJyb3IubWVzc2FnZSA6XG4gICAgICAgICAgICAgICAgICAgIGVycm9yLnN0YXR1cyA/IGAke2Vycm9yLnN0YXR1c30gLSAke2Vycm9yLnN0YXR1c1RleHR9YCA6ICd0cm9waGllcy1zZXJ2aWNlIGdldENoYW1waW9uc2hpcHM6IFNlcnZlciBlcnJvcic7XG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnJNc2cpOyAvLyBsb2cgdG8gY29uc29sZSBpbnN0ZWFkXG4gICAgICAgICAgICAgICAgcmV0dXJuIFtdO1xuICAgICAgICAgICAgfSk7IFxuICAgICAgICB9XG5cbiAgICAgICAgLyogR2V0IGFsbCB0cm9waGllcyAqL1xuICAgICAgICBnZXRUcm9waGllcyhzZWFzb246IFNlYXNvbiwgY2hhbXBpb25zaGlwOiBDaGFtcGlvbnNoaXApOiBhbnkge1xuICAgICAgICAgICAgbGV0IHVybCA9IFwiLi9kaXN0L3Ryb3BoaWVzLnBocFwiO1xuXG4gICAgICAgICAgICByZXR1cm4gdGhpcy5odHRwKHtcbiAgICAgICAgICAgICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgICAgICAgICAgICB1cmw6IHVybCxcbiAgICAgICAgICAgICAgICBkYXRhOiBKU09OLnN0cmluZ2lmeSh7c2Fpc29uOiBzZWFzb24uU2Fpc29uLCBjaGFtcGlvbm5hdDogY2hhbXBpb25zaGlwLkNoYW1waW9ubmF0fSlcbiAgICAgICAgICAgIH0pLnRoZW4oZnVuY3Rpb24gc3VjY2Vzc0NhbGxiYWNrKHJlc3BvbnNlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHJlc3BvbnNlLmRhdGEgfHwge307XG4gICAgICAgICAgICB9LCBmdW5jdGlvbiBlcnJvckNhbGxiYWNrKGVycm9yKSB7XG4gICAgICAgICAgICAgICAgbGV0IGVyck1zZyA9IChlcnJvci5tZXNzYWdlKSA/IGVycm9yLm1lc3NhZ2UgOlxuICAgICAgICAgICAgICAgICAgICBlcnJvci5zdGF0dXMgPyBgJHtlcnJvci5zdGF0dXN9IC0gJHtlcnJvci5zdGF0dXNUZXh0fWAgOiAndHJvcGhpZXMtc2VydmljZSBnZXRUcm9waGllczogU2VydmVyIGVycm9yJztcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGVyck1zZyk7IC8vIGxvZyB0byBjb25zb2xlIGluc3RlYWRcbiAgICAgICAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgICAgICB9KTsgXG4gICAgICAgIH1cbiAgICB9XG59IiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cImFuZ3VsYXIuZC50c1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiYW5ndWxhci1yb3V0ZS5kLnRzXCIgLz5cblxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIm5hdmJhci1kaXJlY3RpdmUudHNcIiAvPlxuXG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiaG9tZS1jb250cm9sbGVyLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJmb3JlY2FzdGVycy1jb250cm9sbGVyLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJzdGFuZGluZ3MtY29udHJvbGxlci50c1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwidHJvcGhpZXMtY29udHJvbGxlci50c1wiIC8+XG5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJmb3JlY2FzdGVycy1zZXJ2aWNlLnRzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCJzdGFuZGluZ3Mtc2VydmljZS50c1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwidHJvcGhpZXMtc2VydmljZS50c1wiIC8+XG5cbnZhciBhcHBNb2R1bGUgPSBhbmd1bGFyLm1vZHVsZSgncG91bHBlQXBwJywgWyduZ0FuaW1hdGUnLCAndWkucm91dGVyJywgJ3VpLmJvb3RzdHJhcCcsICd1aS5sYXlvdXQnXSk7XG5cblxuYXBwTW9kdWxlLnNlcnZpY2UoJ2ZvcmVjYXN0ZXJzU2VydmljZScsIFsnJGh0dHAnLCAoJGh0dHApID0+IG5ldyBBcHBsaWNhdGlvbi5TZXJ2aWNlcy5Gb3JlY2FzdGVyc1NlcnZpY2UoJGh0dHApXSk7XG5hcHBNb2R1bGUuc2VydmljZSgnc3RhbmRpbmdzU2VydmljZScsIFsnJGh0dHAnLCAoJGh0dHApID0+IG5ldyBBcHBsaWNhdGlvbi5TZXJ2aWNlcy5TdGFuZGluZ3NTZXJ2aWNlKCRodHRwKV0pO1xuYXBwTW9kdWxlLnNlcnZpY2UoJ3Ryb3BoaWVzU2VydmljZScsIFsnJGh0dHAnLCAoJGh0dHApID0+IG5ldyBBcHBsaWNhdGlvbi5TZXJ2aWNlcy5Ucm9waGllc1NlcnZpY2UoJGh0dHApXSk7XG5cbmFwcE1vZHVsZS5jb250cm9sbGVyKCdIb21lQ29udHJvbGxlcicsIFsnJHNjb3BlJywgJyR0aW1lb3V0JywgJyRpbnRlcnZhbCcsICgkc2NvcGUsICR0aW1lb3V0LCAkaW50ZXJ2YWwpID0+IFxuXHRuZXcgQXBwbGljYXRpb24uQ29udHJvbGxlcnMuSG9tZUNvbnRyb2xsZXIoJHNjb3BlLCAkdGltZW91dCwgJGludGVydmFsKV0pO1xuXG5hcHBNb2R1bGUuY29udHJvbGxlcignRm9yZWNhc3RlcnNDb250cm9sbGVyJywgWydmb3JlY2FzdGVyc1NlcnZpY2UnLCAoZm9yZWNhc3RlcnNTZXJ2aWNlKSA9PlxuXHRuZXcgQXBwbGljYXRpb24uQ29udHJvbGxlcnMuRm9yZWNhc3RlcnNDb250cm9sbGVyKGZvcmVjYXN0ZXJzU2VydmljZSldKTtcblxuYXBwTW9kdWxlLmNvbnRyb2xsZXIoJ1N0YW5kaW5nc0NvbnRyb2xsZXInLCBbJ3N0YW5kaW5nc1NlcnZpY2UnLCAoc3RhbmRpbmdzU2VydmljZSkgPT5cbiAgICBuZXcgQXBwbGljYXRpb24uQ29udHJvbGxlcnMuU3RhbmRpbmdzQ29udHJvbGxlcihzdGFuZGluZ3NTZXJ2aWNlKV0pO1xuXG5hcHBNb2R1bGUuY29udHJvbGxlcignVHJvcGhpZXNDb250cm9sbGVyJywgWyd0cm9waGllc1NlcnZpY2UnLCAodHJvcGhpZXNTZXJ2aWNlKSA9PlxuICAgIG5ldyBBcHBsaWNhdGlvbi5Db250cm9sbGVycy5Ucm9waGllc0NvbnRyb2xsZXIodHJvcGhpZXNTZXJ2aWNlKV0pO1xuXG5hcHBNb2R1bGUuZGlyZWN0aXZlKCduYXZiYXInLCAoKSA9PiBuZXcgQXBwbGljYXRpb24uRGlyZWN0aXZlcy5OYXZiYXIoKSk7XG5cblxuXG5hcHBNb2R1bGUuY29tcG9uZW50KCdmb3JlY2FzdGVyc0NvbXBvbmVudCcsIHtcblx0YmluZGluZ3M6IHtcblxuXHR9LFxuXHRjb250cm9sbGVyOiAnRm9yZWNhc3RlcnNDb250cm9sbGVyIGFzIGN0cmwnLFxuXHR0ZW1wbGF0ZVVybDogJy4vZGlzdC9mb3JlY2FzdGVycy5odG1sJ1xufSk7XG5cbmFwcE1vZHVsZS5jb21wb25lbnQoJ3N0YW5kaW5nc0NvbXBvbmVudCcsIHtcbiAgICBiaW5kaW5nczoge1xuXG4gICAgfSxcbiAgICBjb250cm9sbGVyOiAnU3RhbmRpbmdzQ29udHJvbGxlciBhcyBjdHJsJyxcbiAgICB0ZW1wbGF0ZVVybDogJy4vZGlzdC9zdGFuZGluZ3MuaHRtbCdcbn0pO1xuXG5hcHBNb2R1bGUuY29tcG9uZW50KCd0cm9waGllc0NvbXBvbmVudCcsIHtcbiAgICBiaW5kaW5nczoge1xuXG4gICAgfSxcbiAgICBjb250cm9sbGVyOiAnVHJvcGhpZXNDb250cm9sbGVyIGFzIGN0cmwnLFxuICAgIHRlbXBsYXRlVXJsOiAnLi9kaXN0L3Ryb3BoaWVzLmh0bWwnXG59KTtcblxuYXBwTW9kdWxlLmNvbmZpZyhbJyRzdGF0ZVByb3ZpZGVyJywgJyR1cmxSb3V0ZXJQcm92aWRlcicsIGZ1bmN0aW9uKCRzdGF0ZVByb3ZpZGVyLCAkdXJsUm91dGVyUHJvdmlkZXIpIHtcbiAgICAkdXJsUm91dGVyUHJvdmlkZXIub3RoZXJ3aXNlKCcvaG9tZScpO1xuXG4gICAgJHN0YXRlUHJvdmlkZXJcbiAgICAgICAgLnN0YXRlKCdob21lJywge1xuICAgICAgICAgICAgdXJsOiAnL2hvbWUnLFxuICAgICAgICAgICAgdGVtcGxhdGU6ICcnLFxuICAgICAgICAgICAgY29udHJvbGxlcjogJ0hvbWVDb250cm9sbGVyJ1xuICAgICAgICB9KVxuICAgICAgICAuc3RhdGUoJ2ZvcmVjYXN0ZXJzJywge1xuICAgICAgICAgICAgdXJsOiAnL2ZvcmVjYXN0ZXJzJyxcbiAgICAgICAgICAgIHRlbXBsYXRlOiAnPGZvcmVjYXN0ZXJzLWNvbXBvbmVudD48L2ZvcmVjYXN0ZXJzLWNvbXBvbmVudD4nLFxuICAgICAgICAgICAgY29udHJvbGxlcjogJ0ZvcmVjYXN0ZXJzQ29udHJvbGxlcidcbiAgICAgICAgfSlcbiAgICAgICAgLnN0YXRlKCdzdGFuZGluZ3MnLCB7XG4gICAgICAgICAgICB1cmw6ICcvc3RhbmRpbmdzJyxcbiAgICAgICAgICAgIHRlbXBsYXRlOiAnPHN0YW5kaW5ncy1jb21wb25lbnQ+PC9zdGFuZGluZ3MtY29tcG9uZW50PicsXG4gICAgICAgICAgICBjb250cm9sbGVyOiAnU3RhbmRpbmdzQ29udHJvbGxlcidcbiAgICAgICAgfSlcbiAgICAgICAgLnN0YXRlKCd0cm9waGllcycsIHtcbiAgICAgICAgICAgIHVybDogJy90cm9waGllcycsXG4gICAgICAgICAgICB0ZW1wbGF0ZTogJzx0cm9waGllcy1jb21wb25lbnQ+PC90cm9waGllcy1jb21wb25lbnQ+JyxcbiAgICAgICAgICAgIGNvbnRyb2xsZXI6ICdUcm9waGllc0NvbnRyb2xsZXInXG4gICAgICAgIH0pXG59XSk7XG5cbiJdLCJzb3VyY2VSb290IjoiL3NvdXJjZS8ifQ==
