var CallendarHelper={

    map:{},

    holiday:{},

    project:{},

    id:{},

    TYPE_READ: 1,


    selectors: {
        formCalendar: '#formCalendar',
    },

    _map: function () {
        var $this = this;
        $this.map = {
            calendar: $('#calendar'),
            document: $(document),
            body: $('body'),
            calendar_modal:$('#calendar-modal'),
            formCalendar: $('#formCalendar'),
            newProject:'a.newProject',
            url:'/calendar/',
            calendarCreate:"#calendarCreate",
            removeCalendarDate:'#removeCalendarDate',
            clickEvent: 'a.calendar',
            filter: 'input:checkbox',
            select_all: '.select_all',
            userCheckbox: $('.user'),
            projectCheckbox: $('.project'),
            findCheck:'input:checkbox:checked',
            calendarUserId:'#calendar-user_id',
            calendarProjectId:'#calendar-project_id',
            fcday:'div#calendar td.fc-day',
            checkboxAll:'#checkcoxAll',
            start_at:$('input#calendar-end_at'),
            assignToMe: '#assign-to-me',
            noProjectsAssigned: $('#no-projects-assigned'),
            showEstimateLink: '#show-estimate',
            estimateBlock: '#estimate-block',
            calendarFormCancel:'.cancel-modal',
            monthButton:'.month-button',
            basicWeekButton: '.basicWeek-button',
        };
    },

    _setHoliday:function () {
        var $this=this;
        $.ajax({
            url: $this.map.url+'all-holiday-config',
            async: false,
            cache: false,
            success: function (json) {
                return $this.holiday = $.parseJSON(json);
            }
        });
    },

    init: function () {
        var $this = this;
        $this._map();
        $this.map.start_at.on('change', function () {
            return $this.activeStartAt()
        });
        $this.map.body.on('click', $this.map.assignToMe, function () {
            return $this.assignToMe($this.map.assignToMe)
        });
        $this.map.body.on('click', $this.map.showEstimateLink, function () {
            return $this.showEstimate($this.map.showEstimateLink)
        });
        $this.map.body.on('change', $this.map.calendarUserId, function () {
            return $this.activeProjectUser()
        });
        $this.map.body.on('click', $this.map.removeCalendarDate, function () {
            return $this.deleteCalendarDate()
        });
        $this.map.formCalendar.on('pjax:success', function (data, status, xhr, options) {
            $this.onFormCalendar(data);
            $this.activeProjectUser();
            $($this.map.calendarFormCancel).on('click', function () {
                event.preventDefault();
                $this.closeCalendarModal()
            });
        });
        $this.map.body.on('click', $this.map.fcday, $this.map.newProject, function () {
            return $this.visibleModel()
        });
        $this.map.body.on('click', $this.map.newProject, function () {
            return $this.visibleModel()
        });
        $this.map.body.on('click', $this.map.select_all, function () {
            $this.selectAll(this);
            $this.map.calendar.fullCalendar('removeEvents');
            $this.filterProjectEvent()
        });
        $this.map.body.on('click', $this.map.filter, function () {
            $this.map.calendar.fullCalendar('removeEvents');
            $this.filterProjectEvent()
        });
        $this.map.body.on('click', $this.map.checkboxAll, function () {
            return $this.checkboxAll()
        });
        $this.map.body.on('click', $this.map.clickEvent, function () {
            return $this.clickCalendarEvent()
        });
        
        $this.map.body.on('click',$this.map.monthButton, function () {
            $('button.fc-month-button').click();
            $($this.map.monthButton).addClass('state-active');
            $($this.map.basicWeekButton).removeClass('state-active');
        });
        $this.map.body.on('click',$this.map.basicWeekButton, function () {
            $('button.fc-basicWeek-button').click()
            $($this.map.basicWeekButton).addClass('state-active');
            $($this.map.monthButton).removeClass('state-active');
        });
        
        $this._setHoliday();

    },

    onFormCalendar: function (data) {
        var $this = this;
        $this.map.formCalendar.removeClass('hidden');
        var date = $(data.currentTarget);
        var complete = date.find($this.map.calendarCreate);
        if (data.currentTarget.textContent === 'complete') {
            $this.closeCalendarModal();
        }
        if (!$(complete).get(0)) {
            date = $.parseJSON(data.currentTarget.textContent);
            $this.map.calendar.fullCalendar('removeEvents', date.id);
            if (date.start && date.end) {
                $this.map.calendar.fullCalendar('renderEvent', date, true);
            }
            $this.closeCalendarModal();
        }
    },

    closeCalendarModal: function () {
        var $this = this;
        $this.map.formCalendar.addClass('hidden');
        $this.map.calendar_modal.modal('hide');
    },

    visibleModel:function () {
        var $this=this;
        $.pjax({
            url: $this.map.url+'create',
            container: $this.selectors.formCalendar,
            push: false,
        });
        $('button.calendar-button').click();

    },

    clickCalendarEvent:function () {
        var $this=this;
        $.pjax({
            url: $this.map.url+'update',
            container: $this.selectors.formCalendar,
            data:{id:$this.id},
            push: false,
        });
        $('button.calendar-button').click();
    },

    deleteCalendarDate:function () {
        var $this=this;
        $this.map.calendar.fullCalendar('removeEvents',$this.id);
    },

    activeProjectUser:function () {
        var $this = this;
        $.ajax({
            url: $this.map.url+'active-project-user',
            type:'post',
            async: false,
            data:{user_id:$('#calendar-user_id :selected').val()}, //togo
            cache:false,
            success: function(json) {
                var project=$.parseJSON(json),
                    calendarProject = $($this.map.calendarProjectId),
                    selectedProjectId = calendarProject.val();

                calendarProject.html('').append($("<option >", {text: 'Select...'}));
                if (project.length > 0) {
                    $this.map.noProjectsAssigned.addClass('hidden');
                    $.each(project, function (key, item) {
                        calendarProject.append($("<option >", {
                            value: item.id,
                            text: item.name,
                            selected: selectedProjectId == item.id
                        }));
                    });
                } else {
                    $this.map.noProjectsAssigned.removeClass('hidden');
                }
            }
        });
    },

    selectAll:function (event) {
        if($(event).is('#users')) {
            $("input.user:checkbox").prop('checked', $(event).prop("checked"));
        } else {
            $("input.project:checkbox").prop('checked', $(event).prop("checked"));
        }
    },

    filterProjectEvent:function () {
        var $this=this;
        var id_project = [];
        $this.map.projectCheckbox.find($this.map.findCheck).each(function () {
            var checkbox_value = $(this).val();
            id_project.push(checkbox_value);
        });
        var id_user = [];
        $this.map.userCheckbox.find($this.map.findCheck).each(function () {
            var checkbox_value = $(this).val();
            id_user.push(checkbox_value);
        });
        if(id_project.length != 0 && id_user.length != 0) {
            $.ajax({
                url: $this.map.url+'filter-calendar',
                async: false,
                type:'post',
                data:{user:id_user,project:id_project},
                cache:false,
                success: function (json) {
                    var data=$.parseJSON(json);
                    $.each(data, function (index, params) {
                        $this.map.calendar.fullCalendar('renderEvent', params, true);
                    })
                }
            });
        }
    },

    assignToMe: function (element) {
        var $this = this,
            userId = $(element).data('user-id');

        $($this.map.calendarUserId).val(userId);
        // $this.activeProjectUser();

        return false;
    },

    showEstimate: function (element) {
        var $this = this;

        $(element).addClass('hidden');
        $($this.map.estimateBlock).removeClass('hidden');

        return false;
    },

    setViewHolidayConfig: function (params, date, element) {
        var $this=this;
        if ((Date.parse(date)/1000) == parseInt(params.date)) {
            element.css("background", $this.typeHoliday(parseInt(params.type)));
            var wraper=$("<div />", {
                "class" : 'holiday',
            }).css({
                "word-wrap":'break-word',
                "margin":'20px 5px 0 5px',
            }).appendTo(element);

            $("<label />", {
                "text": params.name
            }).appendTo(wraper);
        }
    },

    typeHoliday:function (type) {
        switch(type){
            case 0:
                return '#fcf0b1';
            case 1:
                return '#8ae6d8';
            case 2:
                return '#bbe7fa';
        }
    },

    setAllHolidayConfig: function (date, element) {
        var $this=this;
        $.each($this.holiday, function (index, params) {
            $this.setViewHolidayConfig(params, date, element);
        });
    },
};
$(function () {
    CallendarHelper.init();
});