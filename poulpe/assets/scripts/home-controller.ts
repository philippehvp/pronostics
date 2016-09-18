/// <reference path="angular.d.ts" />


module Application.Controllers {
	export class HomeController {
		private scope: any;
		private timeout: any;							// Service timeout to call once a function
		private interval: any;							// Service interval to call cyclically a function



		constructor($scope: ng.IScope, $timeout: ng.ITimeoutService, $interval: ng.IIntervalService) {
		}
	}

}