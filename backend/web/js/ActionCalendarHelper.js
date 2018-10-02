var ordersActionCalendar;
var ActionCalendarHelper={

    map:{},

    id:{},

    _map: function () {
        var $this = this;
        $this.map = {
            document: $(document),
            eventForm: $('#eventForm'),
            eventModel: $('#eventModel'),
            eventCreate: $('#eventCreate'),
            holidayList: $('#holidayList'),
            url:'/actioncalendar/',
            clickEvent: 'a.actionCelendar',
            prevButton:'.prev-button',
            nextButton:'.next-button',
            todayButton:'.today-button',
        };
    },

    init: function () {
        var $this = this;
        $this._map();
        $this.map.eventForm.on('pjax:success', function(data, status, xhr, options) {
            $this.onFormEvent(data,this);
        });
        $this.map.holidayList.on('pjax:success', function(data, status, xhr, options) {
            $this.onHolidayList(data,this);
        });
        $this.map.document.on('click',$this.map.clickEvent, function () {return $this.clickActionCalendarEvent(); });
        $this.map.document.on('click',$this.map.prevButton, function () { $('button.fc-prev-button').click(); });
        $this.map.document.on('click',$this.map.nextButton, function () { $('button.fc-next-button').click(); });
        $this.map.document.on('click',$this.map.todayButton, function () { $('button.fc-today-button').click(); });
    },

    onFormEvent: function(data,list) {
        var $this = this,
            message = $(link).data('confirm');
    },

    onHolidayList:function (data,list) {
        var $this = this;
        $.pjax.reload({container:"#trash"});
    },

    clickActionCalendarEvent:function () {
        var $this=this;
        var url=$this.map.url+'view';
        $.pjax({
            url: url,
            container: $this.map.eventModel.selector,
            data: { id:$this.id },
            push: false,
        });
        $('button.calendar-button').click();
    },

}
$(function () {
    ActionCalendarHelper.init();
});