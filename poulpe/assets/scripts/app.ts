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

let appModule = angular.module("poulpeApp", ["ngAnimate", "ui.router", "ui.bootstrap", "ui.layout"]);


appModule.service("forecastersService", ["$http", ($http) => new Application.Services.ForecastersService($http)]);
appModule.service("standingsService", ["$http", ($http) => new Application.Services.StandingsService($http)]);
appModule.service("trophiesService", ["$http", ($http) => new Application.Services.TrophiesService($http)]);

appModule.controller("HomeController", ["$timeout", "$interval", ($timeout, $interval) =>	new Application.Controllers.HomeController($timeout, $interval)]);

appModule.controller("ForecastersController", ["forecastersService", (forecastersService) =>	new Application.Controllers.ForecastersController(forecastersService)]);

appModule.controller("StandingsController", ["standingsService", (standingsService) =>
    new Application.Controllers.StandingsController(standingsService)]);

appModule.controller("TrophiesController", ["trophiesService", (trophiesService) =>
    new Application.Controllers.TrophiesController(trophiesService)]);

appModule.directive("navbar", () => new Application.Directives.Navbar());

appModule.component("forecastersComponent", {
  bindings: {
  },
  controller: "ForecastersController as ctrl",
  templateUrl: "./dist/forecasters.html"
});

appModule.component("standingsComponent", {
    bindings: {
    },
    controller: "StandingsController as ctrl",
    templateUrl: "./dist/standings.html"
});

appModule.component("trophiesComponent", {
    bindings: {
    },
    controller: "TrophiesController as ctrl",
    templateUrl: "./dist/trophies.html"
});

appModule.config(["$stateProvider", "$urlRouterProvider", function($stateProvider, $urlRouterProvider) {
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
