var ActivityHelper = {
    map: {},

    _map: function() {
        var $this = this;
        $this.map = {
            calendar: $('#calendar'),
            userSelect: $('.activity-calendar select#user_id'),
            activitySearchForm: $('.activity-calendar form#search-activity'),
            activitySearchDate: $('.activity-calendar #activitysearch-date'),
            calendarCell: 'td.fc-day.fc-widget-content.fc-past',
            calendarNextButton: '.activity-calendar .fc-next-button',
            statedisabledClass: 'fc-state-disabled'
        };
    },

    init: function() {
        var $this = this;
        $this._map();

        $this.map.userSelect.change(function() { return $this.changeUser(this); });
    },

    openActivities: function(date) {
        var $this = this;

        formatedDate = $.fullCalendar.formatDate(date, "MMM DD, YYYY");

        $this.map.activitySearchDate.val(formatedDate);
        $this.map.activitySearchForm.submit();

        return false;
    },

    changeUser: function(element) {
        var $this = this;

        var view = $this.map.calendar.fullCalendar('getView');
        $this.renderActivities(view, $this.map.calendar);

        return true;
    },

    renderView: function(view, element) {
        var $this = this;

        $this.hidePastDates(view);
        $this.renderActivities(view, element);

        return true;
    },

    hidePastDates: function(view) {
        var $this = this,
            calendarNextButton =    $($this.map.calendarNextButton);

        var minDate = moment(),
            maxDate = moment().add(2, 'weeks');

        if (maxDate >= view.start && maxDate <= view.end) {
            calendarNextButton.prop('disabled', true);
            calendarNextButton.addClass($this.map.statedisabledClass);
        } else {
            calendarNextButton.removeClass($this.map.statedisabledClass);
            calendarNextButton.prop('disabled', false);
        }

        return false;
    },

    renderActivities: function(view, element) {
        var $this = this;

        $.ajax({
            url: '/activity/calculations',
            type: 'POST',
            data: {
                userId: $this.map.userSelect.val(),
                startDate: view.start.format(),
                endDate: view.end.format()
            },
            success: function (response) {
                for(var cellName in response.statistics) {
                    var cell = $(element).find($this.map.calendarCell + '[data-date=' + cellName + ']');

                    if (cell.length > 0) {
                        var statistic = response.statistics[cellName];
                        cell.html(statistic.content);
                    }
                }
            }
        });

        return true;
    }
};

$(function() {
    ActivityHelper.init();
});