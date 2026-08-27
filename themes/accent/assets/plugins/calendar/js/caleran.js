/**
 *
                 888
                  888
                  888
 .d8888b  8888b.  888  .d88b.  888d888 8888b.  88888b.
d88P"        "88b 888 d8P  Y8b 888P"      "88b 888 "88b
888      .d888888 888 88888888 888    .d888888 888  888
Y88b.    888  888 888 Y8b.     888    888  888 888  888
 "Y8888P "Y888888 888  "Y8888  888    "Y888888 888  888


 * @name: caleran - the date range picker
 * @description: An inline/popup date range picker
 * @version: 1.3.0
 * @author: Taha PAKSU <tpaksu@gmail.com>
 *
 * // changelog
 *
 * v1.0.0
 * ------
 * - released first version
 *
 * v1.0.1 (quick patch)
 * ------
 * - before updating the input, added check for null variables
 *
 * v1.1.0
 * ------
 * - added `ondraw` event
 * - added `disableDays` function to disable custom days
 * - fixed startOnMonday if moment.js locale already starts at Monday
 * - fixed deprecated jQuery.size() warnings
 * - added `autoCloseOnSelect` option to close when a date/range is selected
 * - added Show & Hide methods (showDropdown/hideDropdown)
 * - changed all locale aware weekdays to act constant in every locale (0-Sunday and 6-Saturday)
 *
 * v1.1.1
 * ------
 * - added event handler checks before event.stopPropagation occurances
 * - seperated click & tap events on mobile and desktop
 * - checked jQuery UI tap event exist before loading hammer.js
 *
 * v1.1.2
 * ------
 * - fixed range click locale bug (which causes wrong start date output)
 *
 * v1.1.3
 * ------
 * - fixed apply button click event on mobile screens
 * - added `disabledRanges` option to specify schedule like selections
 * - added `countinuous` option to only allow continuous range selection
 *
 * v1.2.0
 * ------
 * - added direction parameter to `onbeforemonthchange` event
 * - added quick year and month switching feature
 * - added multiple calendar support for mobile
 *
 * v1.2.1
 * ------
 *  - fixed IE10 compatibility on JS and CSS
 *  - made some optimizations
 *
 * v1.2.2
 * ------
 * - fixed uninitialized startDateBackup variable bug
 * - added browserSync support
 *
 * v1.2.3
 * ------
 * - fixed event duplication on document click
 * - fixed outside triggers closing dropdown
 * - fixed target element confusion when different target option is specified
 * - added startEmpty option
 * - fixed multiple instance closing issues
 * - added missing event parameters to hideDropdown method
 *
 * v.1.3.0
 * -------
 * - fixed autoCloseOnSelect on singleDate version / mobile views
 * - changed code to make clicking on disabled days select start/end date
 * - added some transition delays to make it smoother
 * - added keyboard navigation (`enableKeyboard` option)
 *      up: previous week
 *      down: next week
 *      left: previous day
 *      right: next day
 *      space: select day
 *      pageup: previous month
 *      pagedown next month
 *      shift + pageup: previous year
 *      shift + pagedown: next year
 * - added easy year switch buttons on year list
 * - fixed startEmpty cell selected classes
 * - added destroy method and some extra tests
 * - fix custom target element reading in fetchInputs method
 *
 * Usage:
 * ------
 * $(".selector").caleran({options});
 */
;
( function ( $, window, document, undefined ) {
    /**
     *  The main caleran class
     *  @class caleran
     */
    var caleran = function ( elem, options ) {
        this.elem = elem;
        this.$elem = $( elem );
        this.options = options;
        this.metadata = this.$elem.data( "plugin-options" );
    };
    /**
     * Prototype of caleran plugin
     * @prototype
     */
    caleran.prototype = {
        /////////////////////////////////////////////////////////////////////
        // public properties that can be set through plugin initialization //
        /////////////////////////////////////////////////////////////////////
        public: {
            startDate: moment().startOf( 'day' ), // the selected start date, initially today
            endDate: moment().startOf( 'day' ), // the selected end date, initially today
            format: "L", // the default format for showing in input, default short date format of locale
            dateSeparator: " - ", // if not used as a single date picker, this will be the seperator
            calendarCount: 2, // how many calendars will be shown in the plugin screen
            inline: false, // display as an inline input replacement
            minDate: null, // minimum selectable date, default null (no minimum)
            maxDate: null, // maximum selectable date, default null (no maximum)
            showHeader: true, // visibility of the part which displays the selected start and end dates
            showFooter: true, // visibility of the part which contains user defined ranges
            startOnMonday: false, // if you want to start the calendars on monday, set this to true
            container: "body", // the container of the dropdowns
            oneCalendarWidth: 230, // the width of one calendar, if two calendars are shown, the input width will be 2 * this setting.
            enableKeyboard: true,
            showOn: "bottom", // or "top"           // dropdown placement position relative to input element
            locale: moment.locale(), // moment locale setting, different inputs: https://momentjs.com/docs/#/i18n/changing-locale/ , available locales: https://momentjs.com/ (bottom of the page)
            singleDate: false, // if you want a single date picker, set this to true
            target: null, // the element to update after selection, defaults to the element that is instantiated on
            autoCloseOnSelect: false,
            startEmpty: false,
            ranges: [ { // default range objects array, one range is defined like : {title(string), startDate(moment object), endDate(moment object) }
                title: "Today",
                startDate: moment(),
                endDate: moment()
            }, {
                title: "3 Days",
                startDate: moment(),
                endDate: moment().add( 2, "days" )
            }, {
                title: "5 Days",
                startDate: moment(),
                endDate: moment().add( 4, "days" )
            }, {
                title: "1 Week",
                startDate: moment(),
                endDate: moment().add( 6, "days" )
            }, {
                title: "Till Next Week",
                startDate: moment(),
                endDate: moment().endOf( "week" ) // if you use Monday as week start, you should use "isoweek" instead of "week"
            }, {
                title: "Till Next Month",
                startDate: moment(),
                endDate: moment().endOf( "month" )
            }],
            rangeLabel: "Ranges: ", // the title of defined ranges
            cancelLabel: "Cancel",
            applyLabel: "Apply",
            onbeforeselect: function () {
                return true;
            }, // event which is triggered before selecting the end date ( a range selection is completed )
            onafterselect: function () { }, // event which is triggered after selecting the end date ( the input value changed )
            onbeforeshow: function () { }, // event which is triggered before showing the dropdown
            onbeforehide: function () { }, // event which is triggered before hiding the dropdown
            onaftershow: function () { }, // event which is triggered after showing the dropdown
            onafterhide: function () { }, // event which is triggered after hiding the dropdown
            onfirstselect: function () { }, // event which is triggered after selecting the first date of ranges
            onrangeselect: function () { }, // event which is triggered after selecting a range from the defined range links
            onbeforemonthchange: function () {
                return true;
            }, // event which fires before changing the first calendar month of multiple calendars, or the month of a single calendar
            onaftermonthchange: function () { }, // event which fires after changing the first calendar month of multiple calendars, or the month of a single calendar
            ondraw: function () { },
            oninit: function () { },
            disableDays: function () {
                return false;
            },
            disabledRanges: [],
            continuous: false,
            enableMonthSwitcher: true,
            enableYearMonthSwitcher: true,
            enableYearSwitcher: true
        },
        //////////////////////////////////////////
        // private variables for internal usage //
        //////////////////////////////////////////
        private: {
            startSelected: false,
            currentDate: moment().startOf( 'day' ),
            endSelected: true,
            hoverDate: null,
            keyboardHoverDate: null,
            headerStartDay: null,
            headerStartDate: null,
            headerStartWeekday: null,
            headerEndDay: null,
            headerEndDate: null,
            headerEndWeekday: null,
            swipeTimeout: null,
            isMobile: false,
            valElements: [ "BUTTON", "OPTION", "INPUT", "LI", "METER", "PROGRESS", "PARAM" ],
            dontHideOnce: false,
            initiator: null,
            initComplete: false,
            startDateBackup: null,
            firstValueSelected: false,
            throttleTimeout: null
        },
        /**
         * initialize the plugin
         * @return caleran object
         */
        init: function () {
            this.config = $.extend( {}, this.public, this.options, this.metadata );
            this.globals = $.extend( {}, this.private );
            this.globals.isMobile = this.checkMobile();
            this.applyConfig();
            this.fetchInputs();
            this.drawUserInterface();
            this.addInitialEvents();
            this.addKeyboardEvents();
            this.$elem.data( "caleran", this );
            this.config.oninit( this );
            this.globals.initComplete = true;
            return this;
        },
        /**
         * validates start and end dates,
         * swaps dates if end > start,
         * sets visible month of first selection
         *
         * @return void
         */
        validateDates: function () {
            // validate start & end dates
            var swap;
            if ( moment( this.config.startDate ).locale( this.config.locale ).isValid() && moment( this.config.endDate ).locale( this.config.locale ).isValid() ) {
                this.config.startDate = moment( this.config.startDate ).locale( this.config.locale );
                this.config.endDate = moment( this.config.endDate ).locale( this.config.locale );
                if ( this.config.startDate.isAfter( this.config.endDate ) ) {
                    swap = this.config.startDate.clone();
                    this.config.startDate = this.config.endDate.clone();
                    this.config.endDate = swap.clone();
                    swap = null;
                }
            } else {
                this.config.startDate = moment();
                this.config.endDate = moment();
            }
            this.globals.currentDate = moment( this.config.startDate );
            // validate min & max dates
            if ( this.config.minDate !== null && moment( this.config.minDate ).locale( this.config.locale ).isValid() ) {
                this.config.minDate = moment( this.config.minDate ).locale( this.config.locale );
            } else {
                this.config.minDate = null;
            }
            if ( this.config.maxDate !== null && moment( this.config.maxDate ).locale( this.config.locale ).isValid() ) {
                this.config.maxDate = moment( this.config.maxDate ).locale( this.config.locale );
            } else {
                this.config.maxDate = null;
            }
            if ( this.config.minDate !== null && this.config.maxDate !== null && this.config.minDate.isAfter( this.config.maxDate ) ) {
                swap = this.config.minDate.clone();
                this.config.minDate = this.config.maxDate.clone();
                this.config.maxDate = swap.clone();
                swap = null;
            }
            if(this.checkRangeContinuity() === false || this.isDisabled(this.config.startDate) || this.isDisabled(this.config.endDate)) {
                this.config.startEmpty = true;
                this.globals.firstValueSelected = false;
                if ( $.inArray( this.config.target.get( 0 ).tagName, this.globals.valElements ) !== -1 ) {
                    this.config.target.val("");
                } else {
                    this.config.target.text("");
                }
            }
        },
        /**
         * sets config variables and relations between them,
         * for example "inline" property converts the input to hidden input,
         * applies default date from input to plugin and vice versa .. etc.
         *
         * @return void
         */
        applyConfig: function () {
            // set global moment.js locale
            moment.locale( this.config.locale );
            // set target element to be updated
            if ( this.config.target === null ) this.config.target = this.$elem;
            // create container relative to environment and settings
            if ( this.globals.isMobile === false ) {
                if ( this.config.inline === true ) {
                    this.container = this.$elem.wrapAll( "<div class='caleran-container caleran-inline' tabindex='1'></div>" ).parent();
                    this.input = $( "<div class='caleran-input'></div>" ).appendTo( this.container );
                    this.elem.type = "hidden";
                } else {
                    this.container = $( "<div class='caleran-container caleran-popup'><div class='caleran-box-arrow-top'></div></div>" ).appendTo( this.config.container );
                    this.input = $( "<div class='caleran-input'></div>" ).appendTo( this.container );
                    this.container.hide();
                }
                this.input.css( "width", ( this.config.calendarCount * this.config.oneCalendarWidth ) + "px" );
                if ( this.config.inline === false ) {
                    this.setViewport();
                }
            } else {
                //this.config.calendarCount = 1;
                this.container = $( "<div class='caleran-container-mobile'></div>" ).appendTo( this.config.container );
                this.input = $( "<div class='caleran-input'></div>" ).appendTo( this.container );
                this.input.hide();
                // disable the soft keyboard on mobile devices
                this.$elem.on( "focus", function () {
                    $( this ).blur();
                } );
            }
        },
        clearInput: function () {
          this.globals.firstValueSelected = false;
          this.globals.startEmpty = true;
          this.config.target.val('').trigger('change');
        },
        /**
         * Parse input from the source element's value and apply to config
         * @return void
         */
        fetchInputs: function () {
            moment.locale( this.config.locale );
            var elValue = null;
            if ( $.inArray( this.config.target.get( 0 ).tagName, this.globals.valElements ) !== -1 ) {
                elValue = this.config.target.val();
            } else {
                elValue = this.config.target.text();
            }
            if ( this.config.singleDate === false && elValue.indexOf( this.config.dateSeparator ) > 0 ) {
                var parts = elValue.split( this.config.dateSeparator );
                if ( parts.length == 2 ) {
                    if ( moment( parts[ 0 ], this.config.format ).isValid() && moment( parts[ 1 ], this.config.format ).isValid() ) {
                        this.config.startDate = moment( parts[ 0 ], this.config.format );
                        this.config.endDate = moment( parts[ 1 ], this.config.format );
                    }
                }
            } else if ( this.config.singleDate === true ) {
                var value = elValue;
                if ( moment( value, this.config.format ).isValid() ) {
                    this.config.startDate = moment( value, this.config.format );
                    this.config.endDate = moment( value, this.config.format );
                }
            }
            // validate inputs
            this.validateDates();
        },
        /**
         * Draws the plugin interface when needed
         * @return void
         */
        drawUserInterface: function () {
            this.drawHeader();
            this.calendars = this.input.find( ".caleran-calendars" ).first();
            var nextCal = this.globals.currentDate.clone();
            for ( var calendarIndex = 0; calendarIndex < this.config.calendarCount; calendarIndex++ ) {
                this.drawCalendarOfMonth( nextCal );
                nextCal = nextCal.month( nextCal.month() + 1 );
            }
            // remove last border
            this.calendars.find( ".caleran-calendar" ).last().addClass( "no-border-right" );
            this.drawArrows();
            this.drawFooter();
            this.reDrawCells();
            this.updateInput( false );
            if ( this.config.inline === false ) {
                this.setViewport();
            }
        },
        /**
         * Draws the header part of the plugin, which contains start and end date displays
         * @return void
         */
        drawHeader: function () {
            var headers = "<div class='caleran-header'>" + "<div class='caleran-header-start'>" + "<div class='caleran-header-start-day'></div>" + "<div class='caleran-header-start-date'></div>" + "<div class='caleran-header-start-weekday'></div>" + "</div>";
            if ( this.config.singleDate === false ) {
                headers += "<div class='caleran-header-separator'><i class='fa fa-chevron-right'></i></div>" + "<div class='caleran-header-end'>" + "<div class='caleran-header-end-day'></div>" + "<div class='caleran-header-end-date'></div>" + "<div class='caleran-header-end-weekday'></div>" + "</div>";
            }
            headers += "</div><div class='caleran-calendars'></div>";
            this.input.append( headers );
            if ( this.config.showHeader === false ) {
                this.input.find( ".caleran-header" ).hide();
            }
            this.globals.headerStartDay = this.input.find( ".caleran-header-start-day" );
            this.globals.headerStartDate = this.input.find( ".caleran-header-start-date" );
            this.globals.headerStartWeekday = this.input.find( ".caleran-header-start-weekday" );
            this.globals.headerEndDay = this.input.find( ".caleran-header-end-day" );
            this.globals.headerEndDate = this.input.find( ".caleran-header-end-date" );
            this.globals.headerEndWeekday = this.input.find( ".caleran-header-end-weekday" );
            this.updateHeader();
        },
        /**
         * Updates the start and end dates in the header
         * @return void
         */
        updateHeader: function () {
            if ( this.config.startDate !== null ) {
                this.globals.headerStartDay.text( this.config.startDate.date() );
                if ( this.globals.isMobile ) this.globals.headerStartDate.text( moment.monthsShort( this.config.startDate.month() ) + " " + this.config.startDate.year() );
                else this.globals.headerStartDate.text( moment.months( this.config.startDate.month() ) + " " + this.config.startDate.year() );
                this.globals.headerStartWeekday.text( moment.weekdays( this.config.startDate.day() ) );
            } else {
                this.globals.headerStartDay.text( "" );
                this.globals.headerStartDate.text( "" );
                this.globals.headerStartWeekday.text( "" );
            }
            if ( this.config.singleDate === false ) {
                if ( this.config.endDate !== null ) {
                    this.globals.headerEndDay.text( this.config.endDate.date() );
                    if ( this.globals.isMobile ) this.globals.headerEndDate.text( moment.monthsShort( this.config.endDate.month() ) + " " + this.config.endDate.year() );
                    else this.globals.headerEndDate.text( moment.months( this.config.endDate.month() ) + " " + this.config.endDate.year() );
                    this.globals.headerEndWeekday.text( moment.weekdays( this.config.endDate.day() ) );
                } else {
                    this.globals.headerEndDay.text( "" );
                    this.globals.headerEndDate.text( "" );
                    this.globals.headerEndWeekday.text( "" );
                }
            }
        },
        /**
         * Updates the connected input element value when the value is chosen
         * @return void
         */
        updateInput: function ( withEvents ) {
            if ( this.config.startEmpty && !this.globals.firstValueSelected ){
                return;
            }
            if ( this.config.singleDate === true ) {
                if ( this.config.startDate === null ) return;
            } else {
                if ( this.config.startDate === null || this.config.endDate === null ) return;
            }
            if ( $.inArray( this.config.target.get( 0 ).tagName, this.globals.valElements ) !== -1 ) {
                var current_val = this.config.target.val();
                var new_val;
                if ( this.config.singleDate === false ) {
                  new_val = this.config.startDate.locale( this.config.locale ).format( this.config.format ) + this.config.dateSeparator + this.config.endDate.locale( this.config.locale ).format( this.config.format );
                } else {
                  new_val = this.config.startDate.locale( this.config.locale ).format( this.config.format );
                }
                if(current_val !== new_val){
                  this.config.target.val( new_val );
                  if(withEvents){
                    this.config.target.trigger('change');
                  }
                }
            } else {
                if ( this.config.singleDate === false ) this.config.target.text( this.config.startDate.locale( this.config.locale ).format( this.config.format ) + this.config.dateSeparator + this.config.endDate.locale( this.config.locale ).format( this.config.format ) );
                else this.config.target.text( this.config.startDate.locale( this.config.locale ).format( this.config.format ) );
            }
            if ( this.globals.initComplete === true && withEvents === true ) this.config.onafterselect( this, this.config.startDate, this.config.endDate );
        },
        /**
         * Draws the arrows of the month switcher
         * @return void
         */
        drawArrows: function () {
            if ( this.container.find( ".caleran-title" ).length > 0 ) {
                if ( this.globals.isMobile ) {
                    this.container.find( ".caleran-title" ).prepend( "<div class='caleran-prev'><i class='fa fa-arrow-left'></i></div>" );
                    this.container.find( ".caleran-title" ).append( "<div class='caleran-next'><i class='fa fa-arrow-right'></i></div>" );
                } else {
                    this.container.find( ".caleran-title" ).first().prepend( "<div class='caleran-prev'><i class='fa fa-arrow-left'></i></div>" );
                    this.container.find( ".caleran-title" ).last().append( "<div class='caleran-next'><i class='fa fa-arrow-right'></i></div>" );
                }
            }
        },
        /**
         * Draws a single calendar
         * @param  [momentobject] _month: The moment object containing the desired month, for example given "18/02/2017" as moment object draws the calendar of February 2017.
         * @return void
         */
        drawCalendarOfMonth: function ( _month ) {
            moment.locale( this.config.locale );
            var startOfWeek = moment.localeData( this.config.locale ).firstDayOfWeek();
            var calendarStart = moment( _month ).startOf( "month" ).subtract( 1, "day" ).endOf( "month" ).startOf( "week" );
            if ( startOfWeek == 1 && this.config.startOnMonday === false ) {
                calendarStart.subtract( 1, "days" );
                startOfWeek = 0;
            } else if ( startOfWeek === 0 && this.config.startOnMonday === true ) {
                calendarStart.add( 1, "days" );
                startOfWeek = 1;
            }
            var calendarOutput = "<div class='caleran-calendar' data-month='" + _month.month() + "'>";
            var boxCount = 0;
            var monthClass = "",
                yearClass = "";
            if ( this.config.enableMonthSwitcher ) monthClass = " class='caleran-month-switch'";
            if ( this.config.enableYearSwitcher ) yearClass = " class='caleran-year-switch'";

            calendarOutput += "<div class='caleran-title'><b" + monthClass + ">" + moment.months( _month.month() ) + "</b>&nbsp;<span" + yearClass + ">" + _month.year() + "</span></div>";
            calendarOutput += "<div class='caleran-days-container'>";
            for ( var days = startOfWeek; days < startOfWeek + 7; days++ ) {
                calendarOutput += "<div class='caleran-dayofweek'>" + moment.weekdaysShort( days % 7 ) + "</div>";
            }
            while ( boxCount < 42 ) {
                cellDate = calendarStart.startOf( 'day' ).unix();
                cellStyle = ( _month.month() == calendarStart.month() ) ? "caleran-day" : "caleran-disabled";
                calendarOutput += "<div class='" + cellStyle + "' data-value='" + cellDate + "'><span>" + calendarStart.date() + "</span></div>";
                boxCount++;
                calendarStart.add( 1, "days" );
            }
            calendarOutput += "</div>";
            calendarOutput += "</div>";
            this.calendars.append( calendarOutput );
        },
        /**
         * Draws the footer of the plugin, which contains range selector links
         * @return void
         */
        drawFooter: function () {
            if ( this.config.singleDate === false && this.config.showFooter === true ) {
                this.input.append( "<div class='caleran-ranges'></div>" );
                var ranges = this.input.find( ".caleran-ranges" );
                ranges.append( "<i class='fa fa-retweet'></i><div class='caleran-range-header'>" + this.config.rangeLabel + "</div>" );
                for ( var range_id in this.config.ranges ) {
                    ranges.append( "<div class='caleran-range' data-id='" + range_id + "'>" + this.config.ranges[ range_id ].title + "</div>" );
                }
            }
            if ( this.globals.isMobile ) {
                if ( this.config.singleDate === true || this.config.showFooter === false ) {
                    this.input.append( "<div class='caleran-filler'></div>" );
                }
                this.input.append( "<div class='caleran-footer'></div>" );
                this.footer = this.input.find( ".caleran-footer" );
                this.footer.append( "<button class='caleran-cancel'>" + this.config.cancelLabel + "</button>" );
                this.footer.append( "<button class='caleran-apply'>" + this.config.applyLabel + "</button>" );
            }
        },
        /**
         * Draws next month's calendar (just calls this.reDrawCalendars with an 1 month incremented currentDate)
         * Used in the next arrow click event
         *
         * @return void
         */
        drawNextMonth: function ( event ) {
            event = event || window.event;
            event.target = event.target || event.srcElement;
            if ( this.globals.swipeTimeout === null ) {
                var that = this;
                this.globals.swipeTimeout = setTimeout( function () {
                    if ( that.config.onbeforemonthchange( that, that.globals.currentDate.startOf( "month" ), "next" ) === true ) {
                        var buffer = that.calendars.get( 0 ).scrollTop;
                        that.globals.currentDate.add( 1, "month" );
                        that.reDrawCalendars();
                        that.calendars.get( 0 ).scrollTop = buffer;
                        that.config.onaftermonthchange( that, that.globals.currentDate.startOf( "month" ) );
                    }
                    that.globals.swipeTimeout = null;
                }, 100 );
            }
            if ( event && typeof event.stopPropagation === "function" ) event.stopPropagation();
        },
        /**
         * Draws previous month's calendar (just calls this.reDrawCalendars with an 1 month decremented currentDate)
         * Used in the prev arrow click event
         *
         * @return void
         */
        drawPrevMonth: function ( event ) {
            event = event || window.event;
            event.target = event.target || event.srcElement;
            if ( this.globals.swipeTimeout === null ) {
                var that = this;
                this.globals.swipeTimeout = setTimeout( function () {
                    if ( that.config.onbeforemonthchange( that, that.globals.currentDate.startOf( "month" ), "prev" ) === true ) {
                        var buffer = that.calendars.get( 0 ).scrollTop;
                        that.globals.currentDate.subtract( 1, "month" );
                        that.reDrawCalendars();
                        that.calendars.get( 0 ).scrollTop = buffer;
                        that.config.onaftermonthchange( that, that.globals.currentDate.startOf( "month" ) );
                    }
                    that.globals.swipeTimeout = null;
                }, 100 );
            }
            if ( event && typeof event.stopPropagation === "function" ) event.stopPropagation();
        },
        /**
         * Day cell click event handler
         * @param  [eventobject] e : The event object which contains the clicked cell in e.target property, which enables us to select dates
         * @return void
         */
        cellClicked: function ( e ) {
            e = e || window.event;
            e.target = e.target || e.srcElement;

            if ( $( e.target ).hasClass( "caleran-day" ) === false ) e.target = $( e.target ).closest( ".caleran-day" ).get( 0 );
            var cell = $( e.target ).data( "value" );
            var selectedMoment = moment.unix( cell );
            if ( this.config.singleDate === false ) {
                if ( this.globals.startSelected === false ) {
                    this.globals.startDateBackup = this.config.startDate.clone();
                    this.config.startDate = selectedMoment;
                    this.config.endDate = null;
                    this.globals.startSelected = true;
                    this.globals.endSelected = false;
                    if ( typeof this.footer != "undefined" ) this.footer.find( ".caleran-apply" ).attr( "disabled", "disabled" );
                    this.config.onfirstselect( this, this.config.startDate );
                } else {
                    if ( selectedMoment.isBefore( this.config.startDate ) ) {
                        var start = this.config.startDate.clone();
                        this.config.startDate = selectedMoment.clone();
                        selectedMoment = start;
                    }
                    this.globals.startDateBackup = null;
                    this.config.endDate = selectedMoment;
                    this.globals.endSelected = true;
                    this.globals.startSelected = false;
                    this.globals.hoverDate = null;

                    if ( this.config.onbeforeselect( this, this.config.startDate, this.config.endDate ) === true &&
                        this.checkRangeContinuity() === true ) {
                        this.globals.firstValueSelected = true;
                        this.updateInput( true );
                    }
                    else this.fetchInputs();
                    if ( this.config.autoCloseOnSelect && (this.globals.isMobile === true || this.config.inline === false) ) this.hideDropdown( e );
                    if ( typeof this.footer != "undefined" ) this.footer.find( ".caleran-apply" ).removeAttr( "disabled" );
                }
            } else {
                this.config.startDate = selectedMoment;
                this.config.endDate = selectedMoment;
                this.globals.endSelected = true;
                this.globals.startSelected = false;
                this.globals.hoverDate = null;
                if ( this.config.onbeforeselect( this, this.config.startDate, this.config.endDate ) === true ) {
                    this.globals.firstValueSelected = true;
                    this.updateInput( true );
                } else {
                    this.fetchInputs();
                }
                if ( this.config.autoCloseOnSelect && (this.globals.isMobile === true || this.config.inline === false) ) this.hideDropdown( e );
            }
            this.reDrawCells();
            this.updateHeader();
            if ( e && typeof e.stopPropagation === "function" ) {
                e.stopPropagation();
                e.returnValue = false;
            }
        },
        /**
         * Checks if the defined range is continous (doesn't include disabled ranges or disabled days by callback)
         * @return boolean is continuous or not
         */
        checkRangeContinuity: function () {
            if ( this.config.continuous === false ) {
                return true;
            } else {
                var daysInRange = this.config.endDate.diff( this.config.startDate, "days" );
                var startDate = moment( this.config.startDate );
                for ( var i = 0; i <= daysInRange; i++ ) {
                    if ( $.grep( this.config.disabledRanges, function ( e ) {
                        return startDate.isBetween( e.start, e.end, "day", "[]" );
                    } ).length > 0 || this.config.disableDays( startDate ) === true ) {
                        //alert("Caleran: Selected range contains disabled days. Reverting selection to previous input. [Selected Range: "+this.config.startDate.format("L") + " - " + this.config.endDate.format("L") + "]");
                        return false;
                    }
                    startDate.add( 1, "days" );
                }
                return true;
            }
        },
        /**
         * Checks if given moment value is disabled for that calendar
         * @param [moment] day : The day to be checked
         * @return [boolean] If the day is disabled or not
         */
        isDisabled: function(day){
            if(this.config.disableDays(day.startOf("day")) === true) return true;
            for(var rangeIndex in this.config.disabledRanges){
                var range = this.config.disabledRanges[rangeIndex];
                if(day.isBetween(range.start, range.end, "day","[]")) return true;
            }
            return false;
        },
        /**
         * Event triggered when a day is hovered
         * @param  [eventobject] e : The event object which contains the hovered cell in e.target property, which enables us to style hovered dates
         * @return void
         */
        cellHovered: function ( e ) {
            e = e || window.event;
            e.target = e.target || e.srcElement;
            if ( $( e.target ).hasClass( "caleran-day" ) === false ) e.target = $( e.target ).closest( ".caleran-day" ).get( 0 );
            var cell = $( e.target ).data( "value" );
            this.globals.hoverDate = moment.unix( cell );
            this.globals.keyboardHoverDate = null;
            if ( this.globals.startSelected === true ) this.reDrawCells();
            if ( e && typeof e.stopPropagation === "function" ) {
                e.stopPropagation();
                e.returnValue = false;
            }
        },
        /**
         * Just a calendar refresher to be used with the new variables, updating the calendar view
         * @return void
         */
        reDrawCalendars: function () {
            this.input.empty();
            this.drawUserInterface();
            this.container.focus();
        },
        monthSwitchClicked: function () {
            var that = this;
            var min_year = 0;
            var max_year = 0;
            var min_month = 0;
            var max_month = 0;
            var currentYear = this.globals.currentDate.get( 'year' );
            if(that.config.minDate !== null){
              min_year = parseInt(that.config.minDate.format('Y'));
              min_month = (min_year == currentYear) ? parseInt(that.config.minDate.format('M')) : 0;
            }
            if(that.config.maxDate !== null){
              max_year = parseInt(that.config.maxDate.format('Y'));
              max_month = (max_year == currentYear) ? parseInt(that.config.maxDate.format('M')) : 0;
            }

            this.calendars.get( 0 ).scrollTop = 0;
            var monthSelector = $( "<div class='caleran-month-selector'></div>" ).appendTo( this.calendars );
            var currentMonth = this.globals.currentDate.get( 'month' );
            
            var max_month_exceeded = false;
            var min_month_exceeded = false;
            for ( var m = 0; m < 12; m++ ) {
              var cell_class = '';
              max_month_exceeded = min_month && (m < (min_month-1));
              min_month_exceeded = max_month && (m > (max_month-1));
              if(max_month_exceeded || min_month_exceeded){
                cell_class += ' disabled';
              }
                
                monthSelector.append( "<div class='caleran-ms-month" + ( ( currentMonth == m ) ? " current" : "" ) + cell_class + "' data-month='" + m + "'>" + moment.months( m ) + "</div>" );
            }
            monthSelector.fadeIn( 100 ).css( "display", "flex" );
            monthSelector.find( ".caleran-ms-month" ).off( "click" ).on( "click", function ( event ) {
                that.globals.currentDate.set( "month", $( this ).data( "month" ) );
                that.calendars.find( ".caleran-month-selector" ).fadeOut(200,function(){
                    $(this).remove();
                    that.reDrawCalendars();
                });
                event.stopPropagation();
            } );
        },
        yearSwitchClicked: function () {
            var that = this;
            var min_year = 0;
            var max_year = 0;
            var max_year_exceeded = false;
            var min_year_exceeded = false;
            if(that.config.minDate !== null){
              min_year = parseInt(that.config.minDate.format('Y'));
            }
            if(that.config.maxDate !== null){
              max_year = parseInt(that.config.maxDate.format('Y'));
            }
            this.calendars.get( 0 ).scrollTop = 0;
            var yearSelector = $( "<div class='caleran-year-selector'></div>" ).appendTo( this.calendars );
            var currentYear = this.globals.currentDate.get( 'year' );
            
            var cell_class = '';
            if(min_year && ((currentYear - 7) < min_year)){
              cell_class += ' disabled';
            }
            yearSelector.append( "<div class='caleran-ys-year-prev" + cell_class + "'><i class='fa fa-angle-double-left'></i></div>" );
            yearSelector.data("year",currentYear);
            for ( var year = currentYear - 6; year < currentYear + 7; year++ ) {
              cell_class = '';
              min_year_exceeded = min_year && year < min_year;
              max_year_exceeded = max_year && year > max_year;
              if(min_year_exceeded || max_year_exceeded){
                cell_class += ' disabled';
              }
                yearSelector.append( "<div class='caleran-ys-year" + ( ( currentYear == year ) ? " current" : "" ) + cell_class + "' data-year='" + year + "'>" + year + "</div>" );
            }
            var cell_class = '';
            if(max_year && ((currentYear + 8) > max_year)){
              cell_class += ' disabled';
            }
            yearSelector.append( "<div class='caleran-ys-year-next" + cell_class + "'><i class='fa fa-angle-double-right'></i></div>" );
            yearSelector.fadeIn( 100 ).css( "display", "flex" );
            $(document).off( "click.caleranys" ).on( "click.caleranys", ".caleran-ys-year:not(.disabled)", function ( event ) {
                that.globals.currentDate.set( "year", $( this ).data( "year" ) );
                that.calendars.find( ".caleran-year-selector" ).fadeOut(200,function(){
                    $(this).remove();
                    if(that.config.enableYearMonthSwitcher){
                      that.monthSwitchClicked();
                    } else {
                      that.reDrawCalendars();
                    }
                });
                event.stopPropagation();
            } );
            $(document).off("click.caleranysprev").on("click.caleranysprev", ".caleran-ys-year-prev:not(.disabled)", function ( event ) {
                var min_year = 0;
                var max_year = 0;
                var max_year_exceeded = false;
                var min_year_exceeded = false;
                if(that.config.minDate !== null){
                  min_year = parseInt(that.config.minDate.format('Y'));
                }
                if(that.config.maxDate !== null){
                  max_year = parseInt(that.config.maxDate.format('Y'));
                }
                var currentYear = yearSelector.data("year") - 13;
                var currentYearNow = that.globals.currentDate.get( 'year' );
                yearSelector.data("year",currentYear);
                yearSelector.empty();
                
                
                var cell_class = '';
                if(min_year && ((currentYear - 7) < min_year)){
                  cell_class += ' disabled';
                }
                yearSelector.append( "<div class='caleran-ys-year-prev" + cell_class + "'><i class='fa fa-angle-double-left'></i></div>" );
                for ( var year = currentYear - 6; year < currentYear + 7; year++ ) {
                  cell_class = '';
                  min_year_exceeded = min_year && year < min_year;
                  max_year_exceeded = max_year && year > max_year;
                  if(min_year_exceeded || max_year_exceeded){
                    cell_class += ' disabled';
                  }
                  yearSelector.append( "<div class='caleran-ys-year" + ( ( currentYearNow == year ) ? " current" : "" ) + cell_class + "' data-year='" + year + "'>" + year + "</div>" );
                }
                var cell_class = '';
                if(max_year && ((currentYear + 8) > max_year)){
                  cell_class += ' disabled';
                }
                yearSelector.append( "<div class='caleran-ys-year-next" + cell_class + "'><i class='fa fa-angle-double-right'></i></div>" );
                event.stopPropagation();
            });
            $(document).off("click.caleranysnext").on("click.caleranysnext", ".caleran-ys-year-next:not(.disabled)", function ( event ) {
                var min_year = 0;
                var max_year = 0;
                var max_year_exceeded = false;
                var min_year_exceeded = false;
                if(that.config.minDate !== null){
                  min_year = parseInt(that.config.minDate.format('Y'));
                }
                if(that.config.maxDate !== null){
                  max_year = parseInt(that.config.maxDate.format('Y'));
                }
                var currentYear = yearSelector.data("year") + 13;
                var currentYearNow = that.globals.currentDate.get( 'year' );
                yearSelector.data("year",currentYear);
                yearSelector.empty();
                
                var cell_class = '';
                if(min_year && ((currentYear - 7) < min_year)){
                  cell_class += ' disabled';
                }
                yearSelector.append( "<div class='caleran-ys-year-prev" + cell_class + "'><i class='fa fa-angle-double-left'></i></div>" );
                for ( var year = currentYear - 6; year < currentYear + 7; year++ ) {
                  cell_class = '';
                  min_year_exceeded = min_year && year < min_year;
                  max_year_exceeded = max_year && year > max_year;
                  if(min_year_exceeded || max_year_exceeded){
                    cell_class += ' disabled';
                  }
                    yearSelector.append( "<div class='caleran-ys-year" + ( ( currentYearNow == year ) ? " current" : "" ) + cell_class + "' data-year='" + year + "'>" + year + "</div>" );
                }
                var cell_class = '';
                if(max_year && ((currentYear + 8) > max_year)){
                  cell_class += ' disabled';
                }
                yearSelector.append( "<div class='caleran-ys-year-next" + cell_class + "'><i class='fa fa-angle-double-right'></i></div>" );
                event.stopPropagation();
            });
        },
        /**
         * Hides the caleran dropdown
         * @return void
         */
        hideDropdown: function ( e ) {
            e = e || window.event;
            e.target = e.target || e.srcElement;

            if ( this.globals.initiator === e.target ) {
                return;
            }
            if ( this.input.is( ":visible" ) ) {
                this.config.onbeforehide( this );
                if ( this.globals.isMobile ) {
                    this.input.css( {
                        "display": "none"
                    } );
                    $( "body" ).removeClass( "caleran-open" );
                } else {
                    this.container.css( {
                        "display": "none"
                    } );
                }
                this.globals.hoverDate = null;
                if ( this.globals.startDateBackup !== null ) {
                    this.config.startDate = this.globals.startDateBackup;
                    this.globals.startSelected = false;
                }
                this.config.onafterhide( this );
            }
        },
        /**
         * Shows the caleran dropdown
         * @return void
         */
        showDropdown: function ( e ) {
            e = e || window.event;
            e.target = e.target || e.srcElement;
            if ( !this.input.is( ":visible" ) ) {
                if ( e.target !== this.elem ) {
                    this.globals.dontHideOnce = true;
                    this.globals.initiator = e.target;
                }
                this.fetchInputs();
                this.reDrawCalendars();
                this.config.onbeforeshow( this );
                if ( this.globals.isMobile ) {
                    this.input.css( {
                        "display": "flex"
                    } );
                    $( "body" ).addClass( "caleran-open" );
                } else {
                    this.container.css( {
                        "display": "block"
                    } );
                }
                this.setViewport();
                this.config.onaftershow( this );
            }
        },
        /**
         * When only a cell style update is needed, this function is used. This gives us the possibility to change styles without re-drawing the calendars.
         * @return void
         */
        reDrawCells: function () {
            var that = this;
            var cells = this.container.find( ".caleran-day, .caleran-disabled" );
            for ( var i = 0; i < cells.length; i++ ) {
                var cell = $( cells[ i ] );
                var cellDate = cell.attr( "data-value" );
                var cellMoment = moment.unix( cellDate ).locale( that.config.locale );
                var cellStyle = "caleran-day";
                var cellDay = cellMoment.day();
                // is weekend day (saturday or sunday) check
                if ( cellDay == 6 || cellDay === 0 ) cellStyle += " caleran-weekend";
                // is today check
                if ( moment().isSame( cellMoment, "day" ) ) cellStyle += " caleran-today";
                if(that.config.startEmpty === false || that.globals.firstValueSelected ){
                    // is the start of the range check
                    if ( that.config.singleDate === false && that.config.startDate !== null && that.config.startDate.isSame( cellMoment, "day" ) ) cellStyle += " caleran-start";
                    // is the end of the range check
                    if ( that.config.singleDate === false && that.config.endDate !== null && that.config.endDate.isSame( cellMoment, "day" ) ) cellStyle += " caleran-end";
                    // is between the start and the end range check
                    if ( that.config.singleDate === false && that.config.startDate !== null && that.config.endDate !== null && cellMoment.isBetween( that.config.startDate, that.config.endDate, 'day', '[]' ) ) cellStyle += " caleran-selected";
                    // is the selected date of single date picker
                    if ( that.config.singleDate === true && that.config.startDate !== null && that.config.startDate.isSame( cellMoment, 'day' ) ) cellStyle += " caleran-selected";
                }
                // hovered date check
                if ( that.globals.startSelected === true && that.globals.endSelected === false && that.globals.hoverDate !== null) {
                    if ( cellMoment.isBetween( that.globals.hoverDate, that.config.startDate, "day", "[]" ) ||
                        (that.globals.hoverDate !== null && cellMoment.isBetween( that.config.startDate, that.globals.hoverDate, "day", "[]" ))) {
                        cellStyle += " caleran-hovered";
                    }
                }
                if(that.config.enableKeyboard == true && that.globals.keyboardHoverDate !== null){
                    if(that.globals.startSelected === false){
                        if(that.globals.keyboardHoverDate.isSame(cellMoment, "day")){
                            cellStyle += " caleran-hovered";
                        }
                    }else{
                        if(cellMoment.isBetween(that.config.startDate, that.globals.keyboardHoverDate, "day", "[]") ||
                        cellMoment.isBetween(that.globals.keyboardHoverDate, that.config.startDate, "day", "[]")){
                            cellStyle += " caleran-hovered";
                        }
                    }
                }
                // check disabling scenarios
                // 1. user defined disabling by array or by callback
                var dayDisabledInPredefinedRange = ( that.config.disabledRanges.length > 0 && $.grep( that.config.disabledRanges, function ( e ) {
                    return cellMoment.isBetween( e.start, e.end, "day", "[]" );
                } ).length > 0 ) || that.config.disableDays( cellMoment ) === true;
                if ( dayDisabledInPredefinedRange ||
                    // 3. after the maximum date
                    ( that.config.maxDate !== null && cellMoment.isAfter( that.config.maxDate, 'day' ) ) ||
                    // 4. before the minimum date
                    ( that.config.minDate !== null && cellMoment.isBefore( that.config.minDate, 'day' ) ) ) {
                    cellStyle = "caleran-disabled";
                }else if(cellMoment.month() != cell.closest( ".caleran-calendar" ).data( "month" )){
                    // 2. not the same month of the calendar
                    cellStyle += " caleran-disabled";
                    cellStyle = cellStyle.replace("caleran-weekend","");
                }
                if ( dayDisabledInPredefinedRange ) {
                    cellStyle += " caleran-disabled-range";
                }
                cell.attr( "class", cellStyle );
            };

            this.attachEvents();
            this.config.ondraw( this );
        },
        /**
         * Event triggered when a range link is clicked in the footer of the plugin
         * @param  [eventobject] e the clicked range link
         * @return void
         */
        rangeClicked: function ( e ) {
            e = e || window.event;
            e.target = e.target || e.srcElement;
            if ( !e.target.hasAttribute( "data-id" ) ) return;
            var range_id = $( e.target ).attr( "data-id" );
            this.globals.currentDate = this.config.ranges[ range_id ].startDate.startOf( 'day' ).clone().locale( this.config.locale );
            this.config.startDate = this.config.ranges[ range_id ].startDate.startOf( 'day' ).clone().locale( this.config.locale );
            this.config.endDate = this.config.ranges[ range_id ].endDate.startOf( 'day' ).clone();
            if ( this.checkRangeContinuity() === false ) {
                this.fetchInputs();
            } else {
                this.config.onrangeselect( this, this.config.ranges[ range_id ] );
                this.reDrawCalendars();
            }
            if ( e && typeof e.stopPropagation === "function" ) e.stopPropagation();
        },
        /**
         * Fixes the view positions of dropdown calendar plugin
         * @return void
         */
        setViewport: function () {
            if ( this.globals.isMobile === true ) {
                /*var midHeight = this.input.innerHeight() -
                this.container.find(".caleran-header").outerHeight() -
                this.container.find(".caleran-title").outerHeight() -
                this.container.find(".caleran-footer").outerHeight() -
                this.container.find(".caleran-ranges").outerHeight();
                this.container.find(".caleran-day, .caleran-disabled, .caleran-dayofweek").outerHeight(midHeight / 8);*/
            } else {
                var vp = {
                    top: window.scrollY || window.pageYOffset,
                    left: window.scrollX || window.pageXOffset,
                    bottom: ( window.scrollY || window.pageYOffset ) + window.innerHeight,
                    right: ( window.scrollX || window.pageXOffset ) + window.innerWidth
                };
                var inputdim = this.getDimensions( this.$elem, true );
                var calerandim = this.getDimensions( this.container, true );
                if ( ( this.config.showOn == "top" && inputdim.offsetTop - calerandim.height < vp.top ) || ( this.config.showOn != "top" && inputdim.offsetTop + inputdim.height + calerandim.height < vp.bottom ) ) {
                    // show on bottom
                    this.container.css( "top", inputdim.offsetTop + inputdim.height + 8 );
                    this.container.find( ".caleran-box-arrow-bottom" ).removeClass( "caleran-box-arrow-bottom" ).addClass( "caleran-box-arrow-top" );
                } else {
                    // show on top
                    this.container.css( "top", inputdim.offsetTop - calerandim.height - 8 );
                    this.container.find( ".caleran-box-arrow-top" ).removeClass( "caleran-box-arrow-top" ).addClass( "caleran-box-arrow-bottom" );
                }
                if ( inputdim.offsetLeft + calerandim.width > vp.right ) {
                    this.container.css( "left", vp.right - calerandim.width );
                } else if ( inputdim.offsetLeft < vp.left ) {
                    this.container.css( "left", vp.left );
                } else {
                    this.container.css( "left", inputdim.offsetLeft - 10 );
                }
                this.container.find( ".caleran-box-arrow-top, .caleran-box-arrow-bottom" ).css( "margin-left", inputdim.offsetLeft - parseInt( this.container.css( "left" ), 10 ) );
            }
        },
        /**
         * Helper method for getting an element's inner/outer dimensions and positions
         * @param  [DOMelement] elem  The element whose dimensions and position are wanted
         * @param  [boolean]     outer true/false variable which tells the method to return inner(false) or outer(true) dimensions
         * @return [object]      an user defined object which contains the width and height of the element and top and left positions relative to the viewport
         */
        getDimensions: function ( elem, outer ) {
            var result = {
                width: ( outer ) ? elem.outerWidth() : elem.innerWidth(),
                height: ( outer ) ? elem.outerHeight() : elem.innerHeight(),
                offsetTop: elem.offset().top,
                offsetLeft: elem.offset().left
            };
            return result;
        },
        /**
         * Attaches the related events on the elements after render/update
         * @return void
         */
        attachEvents: function () {
            var clickNextEvent = $.proxy( this.drawNextMonth, this );
            var clickPrevEvent = $.proxy( this.drawPrevMonth, this );
            var clickCellEvent = $.proxy( this.cellClicked, this );
            var hoverCellEvent = $.proxy( this.cellHovered, this );
            var rangeClickedEvent = $.proxy( this.rangeClicked, this );
            var monthSwitchClickEvent = $.proxy( this.monthSwitchClicked, this );
            var yearSwitchClickEvent = $.proxy( this.yearSwitchClicked, this );
            var clickEvent = "click.caleran";
            this.container.find( ".caleran-next" ).off( clickEvent ).one( clickEvent, clickNextEvent );
            this.container.find( ".caleran-prev" ).off( clickEvent ).one( clickEvent, clickPrevEvent );
            this.container.find( ".caleran-day" ).off( clickEvent ).on( clickEvent, clickCellEvent );
            this.container.find( ".caleran-day" ).off( "mouseover.caleran" ).on( "mouseover.caleran", hoverCellEvent );
            this.container.find( ".caleran-disabled" ).not(".caleran-day").off( clickEvent );
            this.container.find( ".caleran-range" ).off( clickEvent ).on( clickEvent, rangeClickedEvent );
            this.container.find( ".caleran-month-switch " ).off( clickEvent ).on( clickEvent, monthSwitchClickEvent );
            this.container.find( ".caleran-year-switch " ).off( clickEvent ).on( clickEvent, yearSwitchClickEvent );

            if ( this.globals.isMobile === true ) {
                // check if jQuery Mobile is loaded
                if ( typeof $.fn.swiperight === "function" ) {
                    this.input.find( ".caleran-calendars" ).css( "touch-action", "none" );
                    this.input.find( ".caleran-calendars" ).on( "swipeleft", clickNextEvent );
                    this.input.find( ".caleran-calendars" ).on( "swiperight", clickPrevEvent );
                } else {
                    var hammer = new Hammer( this.input.find( ".caleran-calendars" ).get( 0 ) );
                    hammer.off( "swipeleft" ).on( "swipeleft", clickNextEvent );
                    hammer.off( "swiperight" ).on( "swiperight", clickPrevEvent );
                }
                this.input.find( ".caleran-cancel" ).off( "click.caleran" ).on( "click.caleran", $.proxy( function ( event ) {
                    this.hideDropdown( event );
                }, this ) );
                this.input.find( ".caleran-apply" ).off( "click.caleran" ).on( "click.caleran", $.proxy( function ( event ) {
                    if ( this.config.onbeforeselect( this, this.config.startDate, this.config.endDate ) === true && this.checkRangeContinuity() === true ) {
                        this.globals.firstValueSelected = true;
                        this.updateInput( true );
                    } else {
                        this.fetchInputs();
                    }
                    this.hideDropdown( event );
                }, this ) );
            }
        },
        /**
         * Events per instance
         */
        addInitialEvents: function () {
                $( document ).on( "click", $.proxy( function ( event ) {
                    if ( this.globals.isMobile === false && this.config.inline === false ) {
                        event = event || window.event;
                        event.target = event.target || event.srcElement;
                        if ( $( this.container ).find( $( event.target ) ).length === 0 &&
                            !$( this.elem ).is( event.target ) && $( this.input ).is( ":visible" ) ) {
                            this.hideDropdown( event );
                        }
                    }
                }, this ) );

                var eventClick = "click.caleran";
                if(this.config.enableKeyboard) eventClick = "click.caleran focus.caleran";
                this.$elem.off(eventClick).on( eventClick, $.proxy( this.debounce(function ( event ) {
                    if(this.$elem.is('readonly') || this.$elem.is('disabled')){
                      return;
                    }
                    event = event || window.event;
                    event.target = event.target || event.srcElement;
                    if ( $( this.input ).is( ":visible" ) && !$( this.config.target ).is( event.target ) ) {
                        this.hideDropdown( event );
                    } else {
                        this.showDropdown( event );
                    }
                }, 200, true), this ));

        },
        debounce: function(func, wait, immediate) {
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    context.globals.throttleTimeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !context.globals.throttleTimeout;
                clearTimeout(context.globals.throttleTimeout);
                context.globals.throttleTimeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },
        /**
         * Attaches keyboard events if enabled
         */
        addKeyboardEvents: function()
        {
            if(this.config.enableKeyboard){
                var keyDownEvent = $.proxy(function(event){
                    var keycode = (event.which) ? event.which : event.keyCode;
                    if(this.globals.keyboardHoverDate === null) {
                        if(this.config.startDate === null){
                            this.globals.keyboardHoverDate = moment({
                                day: 1,
                                month: this.calendars.first().data("month")
                            });
                        }else{
                            this.globals.keyboardHoverDate = this.config.startDate.clone();
                        }
                    }
                    var shouldReDraw = false, shouldPrevent = false;
                    var add_value = 0;
                    var add_type = 'day';
                    switch(keycode){
                        case 37: // left
                            add_value = -1;
                        break;
                        case 38: // top
                            add_value = -1;
                            add_type = 'week';
                        break;
                        case 39: // right
                            add_value = 1;
                        break;
                        case 40: // bottom
                            add_value = 1;
                            add_type = 'week';
                        break;
                        case 32: // space
                            this.input.find(".caleran-day[data-value='"+this.globals.keyboardHoverDate.startOf("day").unix()+"']").first().trigger("click.caleran");
                            shouldReDraw = false;
                            shouldPrevent = true;
                        break;
                        case 33: // page up
                            if(event.shiftKey) {
                                add_value = -1;
                                add_type = 'years';
                            } else {
                                add_value = -1;
                                add_type = 'months';
                            }
                        break;
                        case 34: // page down
                            if(event.shiftKey) {
                                add_value = 1;
                                add_type = 'years';
                            } else {
                                add_value = 1;
                                add_type = 'months';
                            }
                        break;
                        case 27: // esc
                            this.hideDropdown(event);
                            shouldPrevent = true;
                        break;
                        case 9: // tab
                            var $selected_cell = this.input.find(".caleran-day[data-value='"+this.globals.keyboardHoverDate.startOf("day").unix()+"']" + (this.config.startEmpty !== false ? '.caleran-hovered' : '') + ":visible:not(.caleran-selected)").first();
                            if($selected_cell.length){
                              $selected_cell.trigger("click.caleran");
                              shouldReDraw = false;
                              shouldPrevent = false;
                            } else {
                              this.hideDropdown(event);
                            }
                        break;
                        case 36: //home
                            if(event.shiftKey){
                                this.globals.keyboardHoverDate = moment();
                                shouldReDraw = true;
                                shouldPrevent = false;
                            }
                        break;
                        default:
                          this.hideDropdown(event);
                        break;
                    }
                    if(add_value){
                      this.globals.keyboardHoverDate.add(add_value,add_type);
                      shouldReDraw = true;
                      shouldPrevent = false;
                      if((this.config.minDate && this.globals.keyboardHoverDate.isBefore(this.config.minDate)) || 
                        (this.config.maxDate && this.globals.keyboardHoverDate.isAfter(this.config.maxDate))){
                        this.globals.keyboardHoverDate.add(-add_value,add_type);
                        shouldReDraw = false;
                      }
                    }
                    if(shouldReDraw || shouldPrevent){
                        if(this.globals.keyboardHoverDate.isBefore(moment.unix(this.input.find(".caleran-day:first").attr('data-value')))
                            || this.globals.keyboardHoverDate.isAfter(moment.unix(this.input.find(".caleran-day:last").attr('data-value')))){
                            if(add_value){
                              this.globals.keyboardHoverDate.add(add_value,add_type);
                              shouldReDraw = false;
                            }
                            this.globals.currentDate = this.globals.keyboardHoverDate.clone().startOf("month");
                            this.reDrawCalendars();
                            shouldReDraw = false;
                        }
                        if(shouldReDraw) {
                            this.globals.hoverDate = null;
                            this.reDrawCells();
                        }
                        if (shouldPrevent && event && typeof event.preventDefault === "function" ) event.preventDefault();
                        if (shouldPrevent && event && typeof event.stopPropagation === "function" ) event.stopPropagation();
                        if(shouldPrevent){
                          return false;
                        }
                    }
                },this);
                this.$elem.off("keydown.caleran").on("keydown.caleran", keyDownEvent);
                this.container.off("keydown.caleran").on("keydown.caleran", keyDownEvent);
            }
        },
        destroy: function(){
            if(this.config.inline){
                this.input.remove();
                this.$elem.unwrap(".calentim-container");
                this.$elem.removeData("calentim");
                this.elem.type='text';
            }else{
                this.container.remove();
                this.$elem.removeData("calentim");
            }
        },
        /**
         * Code from http://detectmobilebrowser.com/
         * Detects if the browser is mobile
         * @return [boolean] if the browser is mobile or not
         */
        checkMobile: function () {
            return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(( navigator.userAgent || navigator.vendor || window.opera ) ) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(( navigator.userAgent || navigator.vendor || window.opera ).substr( 0, 4 ) );
        }
    };
    caleran.defaults = caleran.prototype.defaults;
    /**
     * The main handler of caleran plugin
     * @param  [object] options javascript object which contains element specific or range specific options
     * @return [caleran] plugin reference
     */
    $.fn.caleran = function ( options ) {
        return this.each( function () {
            new caleran( this, options ).init();
        } );
    };
} )( jQuery, window, document );
