/// <reference path="angular.d.ts" />
/// <reference path="models/forecaster.ts" />


module Application.Controllers {
    export class ForecastersController {
        private forecasters: any[];
        private service: any;
        private currentForecaster: Forecaster;
        private hasBeenModified: boolean;
        private isMovable: boolean;
        private isDeletable: boolean;
        private currentForecasterIndex: number;
        private isInCreationMode: boolean;
        private birthdayCalendar = {
            opened: false
        };

        private beginDateCalendar = {
            opened: false
        };

        private endDateCalendar = {
            opened: false
        };

        private dateOptions = {
            formatYear: "yyyy",
            maxDate: new Date(2020, 5, 22),
            minDate: new Date(1920, 1, 1),
            startingDay: 1
        };


        constructor(forecastersService: any) {
            this.service = forecastersService;
            this.hasBeenModified = false;
            this.isMovable = false;
            this.isDeletable = false;
            this.isInCreationMode = false;
        }

        $onInit() {
            this.service.getForecasters().then((forecasters) => {
                this.forecasters = forecasters;
            }, (err) => {
                console.log("ForecastersController $onInit(): Error during reading");
            });
        }

        /* Load the forecaster to the edit form */
        editForecaster(forecaster: any, index: number): void {
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
        }

        /* Open the UI date picker dialog */
        openBirthdayCalendar(): void {
            if (this.currentForecaster.Pronostiqueurs_DateDeNaissance === null || this.currentForecaster.Pronostiqueurs_DateDeNaissance.toString() === "0/0/0")
                this.currentForecaster.Pronostiqueurs_DateDeNaissance = new Date();

            this.birthdayCalendar.opened = true;
        }

        /* Open the UI date picker dialog */
        openBeginDateCalendar(): void {
            if (this.currentForecaster.Pronostiqueurs_DateDebutPresence === null || this.currentForecaster.Pronostiqueurs_DateDebutPresence.toString() === "0/0/0")
                this.currentForecaster.Pronostiqueurs_DateDebutPresence = new Date();

            this.beginDateCalendar.opened = true;
        }

        /* Open the UI date picker dialog */
        openEndDateCalendar(): void {
            if (this.currentForecaster.Pronostiqueurs_DateFinPresence === null || this.currentForecaster.Pronostiqueurs_DateFinPresence.toString() === "0/0/0")
                this.currentForecaster.Pronostiqueurs_DateFinPresence = new Date();

            this.endDateCalendar.opened = true;
        }

        /* Add a new forecaster or save the modifications made on an existing forecaster */
        saveModifications(): void {
            if (this.isInCreationMode === true) {
                this.hasBeenModified = false;
                this.isInCreationMode = false;
                this.forecasters.push(this.currentForecaster);
                this.service.createForecaster(this.currentForecaster).then((data) => {
                }, (err) => {
                    console.log("Error during creation");
                });
            }
            else {
                this.forecasters[this.currentForecasterIndex] = angular.copy(this.currentForecaster);
                this.hasBeenModified = false;
                this.service.updateForecaster(this.currentForecaster).then((data) => {
                }, (err) => {
                    console.log("Error during update");
                });

            }
        }

        /* Cancel the creation of a new forecaster or the modifications made on an existing forecaster */
        cancelModifications(): void {
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
        }

        /* Indicates that a modification has been made */
        setModifiedOn(): void {
            if (this.isInCreationMode === false)
                this.hasBeenModified = true;
        }

        /* Indicates that a modification has been made on birthday */
        /* It"s more complex because it"s necessary to think about the UIB datepicker widget*/
        checkBirthdayIsModified(): void {
            if (this.isInCreationMode === false) {
                if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance !== "0/0/0" && this.currentForecaster.Pronostiqueurs_DateDeNaissance !== this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance)
                    this.hasBeenModified = true;

                if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDeNaissance === "0/0/0" && this.currentForecaster.Pronostiqueurs_DateDeNaissance !== null)
                    this.hasBeenModified = true;
            }
        }

        /* Indicates that a modification has been made on begin date */
        /* It"s more complex because it"s necessary to think about the UIB datepicker widget*/
        checkBeginDateIsModified(): void {
            if (this.isInCreationMode === false) {
                if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence !== "0/0/0" && this.currentForecaster.Pronostiqueurs_DateDebutPresence !== this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence)
                    this.hasBeenModified = true;

                if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateDebutPresence === "0/0/0" && this.currentForecaster.Pronostiqueurs_DateDebutPresence !== null)
                    this.hasBeenModified = true;
            }
        }

        /* Indicates that a modification has been made on end date */
        /* It"s more complex because it"s necessary to think about the UIB datepicker widget*/
        checkEndDateIsModified(): void {
            if (this.isInCreationMode === false) {
                if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence !== "0/0/0" && this.currentForecaster.Pronostiqueurs_DateFinPresence !== this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence)
                    this.hasBeenModified = true;

                if (this.forecasters[this.currentForecasterIndex].Pronostiqueurs_DateFinPresence === "0/0/0" && this.currentForecaster.Pronostiqueurs_DateFinPresence !== null)
                    this.hasBeenModified = true;
            }
        }


        /* Create a new forecaster */
        createForecaster(): void {
            this.currentForecaster = new Forecaster();
            this.hasBeenModified = true;
            this.isMovable = false;
            this.isDeletable = false;
            this.isInCreationMode = true;

        }

        /* Move a forecaster to the previous forecasters list */
        moveForecaster(): void {
            this.service.moveForecaster(this.currentForecaster).then((data) => {
                this.forecasters.splice(this.currentForecasterIndex, 1);
                this.currentForecasterIndex = null;
                this.currentForecaster = null;
                this.isMovable = false;
                this.isDeletable = false;
            }, (err) => {
                console.log("Error during move");
            });
        }

        /* Move a forecaster to the previous forecasters list */
        deleteForecaster(): void {
            this.service.deleteForecaster(this.currentForecaster).then((data) => {
                this.forecasters.splice(this.currentForecasterIndex, 1);
                this.currentForecasterIndex = null;
                this.currentForecaster = null;
                this.isMovable = false;
                this.isDeletable = false;
            }, (err) => {
                console.log("Error during delete");
            });
        }
    }

}
