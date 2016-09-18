module Application.Directives {

	export class Navbar {

		constructor() {
			return this.createDirective();
		}

		private createDirective(): any {
			return {
				restrict: 'E',
				templateUrl: './dist/navbar.html',
				scope: {
				}
			};
		}
	}
}