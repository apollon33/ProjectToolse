var ordersVacation;
var CallendarVacationHelper={

    map:{},

    id:{},

    selectors: {
        formVacation: '#formVacation',
    },

    _map: function () {
        var $this = this;
        $this.map = {
            calendar: $('#vacationCalendar'),
            body: $('body'),
            vacation_modal:$('#vacation-modal'),
            formVacation: $('#formVacation'),
            newVacarion:'a.newVacarion',
            url:'/vacation/',
            vacationCreate:"#vacationCreate",
            removeVacationDate:'#removeVacationDate',
            clickEvent: 'a.vacationCalendar',
            filter: '#filter',
            userCheckbox: $('.user'),
            chooseAll: '.choose-all',
            findCheck:'input:checkbox:checked',
            fcday:'div#vacationCalendar td.fc-day',
            checkboxAll:'#checkcoxAll',
            start_at:$('input#calendar-end_at'),
            calendarFormCancel:'.cancel-modal',
            typeVacation: '.field-vacation-type',
            checkboxVacation: '.field-vacation-checkbox',
            hasError: '.has-error',
        };
    },

    init: function () {
        var $this = this;
        $this._map();
        $this.map.start_at.on('change',function () {return $this.activeStartAt()});
        $this.map.body.on('click',$this.map.removeVacationDate,function () {return $this.deleteVacationDate()});
        $this.map.formVacation.on('pjax:success', function (data, status, xhr, options) {
            $($this.map.calendarFormCancel).on('click', function () {event.preventDefault();$this.closeCalendarVacationModal()});
            $this.onFormVacation(data);
            if($($this.map.typeVacation).is($this.map.hasError))
                $($this.map.checkboxVacation).removeClass('hidden');
        });
        $this.map.body.on('click', $this.map.chooseAll, function () {
            $this.chooseAll(this);
            $this.map.calendar.fullCalendar('removeEvents');
            $this.filterVacationEvent()
        });
        $this.map.body.on('click',$this.map.fcday,function () {return $this.visibleModel()});
        $this.map.body.on('click',$this.map.newVacarion,function () {return $this.visibleModel()});
        $this.map.body.on('click',$this.map.filter, function () {$this.map.calendar.fullCalendar('removeEvents');  $this.filterVacationEvent()});
        $this.map.body.on('click',$this.map.checkboxAll, function () {return $this.checkboxAll()});
        $this.map.body.on('click',$this.map.clickEvent, function () {return $this.clickCalendarEvent()});
    },

    onFormVacation: function (data) {
        var $this = this;
        $this.map.formVacation.removeClass('hidden');
        var date=$(data.currentTarget);
        var complete=date.find($this.map.vacationCreate);
        if(data.currentTarget.textContent==='complete') {
            $this.closeCalendarVacationModal();
        }
        if(!$(complete).get(0)) {
            date=$.parseJSON(data.currentTarget.textContent);
            $this.map.calendar.fullCalendar('removeEvents', date.id);
            $this.map.calendar.fullCalendar('renderEvent', date, true);
            $this.closeCalendarVacationModal();
        }
    },

    closeCalendarVacationModal: function () {
        var $this = this;
        $this.map.formVacation.addClass('hidden');
        $this.map.vacation_modal.modal('hide');
    },

    renderView: function (view, element) {
        console.log(view)
    },

    chooseAll:function (event) {
        $("input.user:checkbox").prop('checked', $(event).prop("checked"));
    },

    visibleModel:function () {
        var $this=this;
        $.pjax({
            url:  $this.map.url+'create',
            container: $this.selectors.formVacation,
            push: false,
        });
        $('button.vacation-button').click();

    },

    clickCalendarEvent:function () {
        var $this=this;
        $.pjax({
            url: $this.map.url+'update',
            container: $this.selectors.formVacation,
            data:{id:$this.id},
            push: false,
        });
        $('button.vacation-button').click();
    },

    deleteVacationDate:function () {
        var $this=this;
        $this.map.calendar.fullCalendar('removeEvents',$this.id);
    },

    filterVacationEvent:function () {
        var $this=this;
        var id_user = [];
        $this.map.userCheckbox.find($this.map.findCheck).each(function () {
            var checkbox_value = $(this).val();
            id_user.push(checkbox_value);
        });
        $this.map.calendar.fullCalendar('removeEvents');
        $.ajax({
            url: $this.map.url+'filter-vacation',
            async: false,
            type: 'post',
            data:  {user:id_user},
            cache: false,
            success: function (json) {
                var data=json;
                $.each(data, function (index, params) {
                    $this.map.calendar.fullCalendar('renderEvent',  params, true);
                })
            }
        });
    },

    checkboxAll:function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    },

};
$(function () {
    CallendarVacationHelper.init();
});
