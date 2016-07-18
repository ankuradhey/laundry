window.SCHEDULER = function(deliveryMethodConfig) {
    'use strict';
    var DAYS_LENGTH = 7,
            FORMAT = ['ymd', 'dmy', 'mdy'],
            WEEK_DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            CURR_DATE = new Date(),
            createDaysArr = function(_length, _offset) {
        var days = new Array(_length || DAYS_LENGTH);
        for (var i = _offset || 0, j = 0; i < (_length || DAYS_LENGTH) + (_offset || 0); i++, j++) {
            var tmp = new Date();
            resetDate.call(tmp, deliveryMethodConfig);
            tmp.setDate(tmp.getDate() + i);
            days[j] = tmp.toString();
            
            //                    days[j].toString = function() {
            //                        return WEEK_DAYS[this.getDay()] + ' ' + this.getDate() + ' ' + MONTHS[this.getMonth() + 1];
            //                    }
            //                    days[j]['dayName'] = WEEK_DAYS[days[j].getDay()];
            //                    days[j]['monthName'] = WEEK_DAYS[days[j].getDay()];
            //                    days[j]['date'] = WEEK_DAYS[days[j].getDay()];
        }
        return days;
    },
            DELIVERY_METHODS = ['Regular', 'Express', 'Fixedslot'],
            CURR_DELIVERY_METHOD = 'Regular',
            PICKUP_SLOTS = {},
            DELIVERY_SLOTS = {},
            tempArr = [];


    function resetDate(deliveryMethodConfig) {
        this.setHours(deliveryMethodConfig.deliveryConfig[CURR_DELIVERY_METHOD].startTime.split(":")[0]);
        this.setMinutes(deliveryMethodConfig.deliveryConfig[CURR_DELIVERY_METHOD].startTime.split(":")[1]);
        this.setSeconds("0");
        this.setMilliseconds("0");
    }

    function validateTime(timeParam) {
        return /^[01][1-9]?|2[0-4]?:[0-5]?[0-9]?$/.test(timeParam);
    }

    /*
     * slotTime - in hours
     * currDate - Year/month/date format
     * startTime - 1:00
     */
    function createTimeSlots(startTime, endTime, slotTime, currDate) {
        if (!validateTime(startTime) || !validateTime(endTime)) {
            console.log(startTime, endTime)
            throw new Error('time not valid - validate time failed');
        }

        if (Object.prototype.toString.call(currDate) !== '[object Date]') {
            console.log(currDate);
            throw new Error('currDate passed not a valid date object');
        }

        var finalDate = new Date(currDate.getTime());
        finalDate.setHours(endTime.split(":")[0]);
        finalDate.setMinutes(endTime.split(":")[1]);
        finalDate.setSeconds("0");
        finalDate.setMilliseconds("0");

        currDate.setHours(startTime.split(":")[0]);
        currDate.setMinutes(startTime.split(":")[1]);
        currDate.setSeconds("0");
        currDate.setMilliseconds("0");

        while (currDate.getTime() < finalDate.getTime()) {
            tempArr.push({'startTime': currDate.setTime(currDate.getTime()), 'endTime': currDate.setTime(currDate.getTime() + slotTime * 3600000)});
            return createTimeSlots(((currDate.getHours()).toString().paddingLeft("0") + ':' + currDate.getMinutes().toString().paddingLeft("0")), endTime, slotTime, currDate);
        }

        return tempArr;
    }

    return {
        pickUpDays: function(format) {
            format = format || 'ymd';
            //check
            if (typeof format != 'string' || FORMAT.indexOf(format) === -1) {
                throw new Error('Date format error - scheduler.js - pickupdays');
            }
            return createDaysArr();
        },
        deliveryDays: function(format) {
            format = format || 'ymd';
            //check
            if (typeof format != 'string' || FORMAT.indexOf(format) === -1) {
                throw new Error('Date format error - scheduler.js - pickupdays');
            }
            //OFFSET would be 2 or 3 depending on delivery type 
            // in regular case - three days gap would be there - after 2 days
            // in delivery case - two days gap would be there - after 1 day
            return createDaysArr(10, CURR_DELIVERY_METHOD == 'Regular' ? 3 : 2);
        },
        pickupSlots: function(startTime, endTime, durationTime, currDate) {
//            tempArr = [new Date(currDate.getFullYear() + '-' + (currDate.getMonth() + 1) + '-' + currDate.getDate() + ' ' + startTime)];
            tempArr = [];
            return PICKUP_SLOTS[currDate.getDate()] = PICKUP_SLOTS[currDate.getDate()] || createTimeSlots(startTime, endTime, durationTime, currDate);
        },
        deliverySlots: function(startTime, endTime, durationTime, currDate) {
            tempArr = [];

            return DELIVERY_SLOTS[currDate.getDate()] = DELIVERY_SLOTS[currDate.getDate()] || createTimeSlots(startTime, endTime, durationTime, currDate);
        },
        /*
         * For breaking in the table columns format
         */
        pickupSlotsTable: function(startTime, endTime, durationTime, currDate, _cols) {
            var arr = this.pickupSlots(startTime, endTime, durationTime, currDate);
            _cols = _cols || 4;
            var tableArr = new Array(_cols),
                    i = 0;
            arr.forEach(function(value, index) {
                if (typeof tableArr[i] == 'undefined')
                    tableArr[i] = [];
                tableArr[i].push(value);
                tableArr[i].length >= _cols && i++;
            });
            return tableArr;
        },
        deliverySlotsTable: function(startTime, endTime, durationTime, currDate, _cols) {
            var arr = this.deliverySlots(startTime, endTime, durationTime, currDate);
            _cols = _cols || 4;
            var tableArr = new Array(_cols),
                    i = 0;
            arr.forEach(function(value, index) {
                if (typeof tableArr[i] == 'undefined')
                    tableArr[i] = [];
                tableArr[i].push(value);
                tableArr[i].length >= _cols && i++;
            });
            return tableArr;
        },
        setDelivery: function(deliveryMethod) {
            CURR_DELIVERY_METHOD = DELIVERY_METHODS.indexOf(deliveryMethod) !== -1 ? deliveryMethod : CURR_DELIVERY_METHOD;
        },
        getDelivery: function() {
            return CURR_DELIVERY_METHOD;
        },
        resetDate: resetDate,
        init: function() {
            return function(deliveryType) {
                if (!deliveryType)
                    console.warn('init() needs deliveryType argument - Regular || Express');
                this.setDelivery(deliveryType);
            }
        }()
    }
};