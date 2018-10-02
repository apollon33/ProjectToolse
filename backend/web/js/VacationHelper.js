var calendarVacation;
var VacationHelper = {

    map: {},

    hot: {},

    data: {},

    day: {},

    cell: {},

    cells: {},

    selectors: {
        body: 'body',
        container: '#tableVacation',
        url: '/vacation/',
        vacationChoose: '#vacation-year,#vacation-month',
        typeVacation: '.field-vacation-type',
        checkboxVacation: '.field-vacation-checkbox',
        hasError: '.has-error',
        startAt: 'input#calendar-end_at',
        periodVacation: 'a#period-vacation',
        vacationForm: '#vacationForm',
        formVacation: '#formVacation',
        vacationModal: '#vacation-modal',
        removeVacationDate: '#removeVacationDate',
        calendarFormCancel: '.cancel-modal',
    },

    elements: {},

    initElements: function () {
        var $this = this;
        $this.elements = {
            body: $($this.selectors.body),
            container: $($this.selectors.container).get(0),
            startAtCalendar: $($this.selectors.startAt),
            periodVacation: $($this.selectors.periodVacation),
            vacationCreate: $($this.selectors.vacationForm),
            formVacation: $($this.selectors.formVacation),
            vacationModal: $($this.selectors.vacationModal),
        }
    },

    numberDaysMonth: function () {
        var day = [],
            cell = [];
        for (var i = 1; i < this.data.day + 1; i++) {
            day.push(i);
            cell.push(i === 1 ? 200 : 30);
        }
        this.cell = cell;
        this.day = this.data.headerTable.concat(day);
    },

    init: function () {
        var $this = this;
        if (calendarVacation) {
            $this.initElements();
            $this._setDate();
            $this.numberDaysMonth();
            $this.elements.startAtCalendar.on('change', function () {
                return $this.activeStartAt()
            });
            $this.elements.formVacation.on('pjax:success', function (data, status, xhr, options) {
                $this.onFormVacation(data);
                if($($this.map.typeVacation).is($this.map.hasError))
                    $($this.map.checkboxVacation).removeClass('hidden');
                $($this.map.calendarFormCancel).on('click', function(){event.preventDefault();$this.closeCalendarVacationTableModal()});

            });
            Handsontable.renderers.registerRenderer('negativeValueRenderer', $this.negativeValueRenderer);
            var hot = new Handsontable($this.elements.container, $this.tableArray());
            hot.view.wt.update('onCellDblClick', function (row, cell) {
                return $this.onCellDblClick(row, cell)
            });
            $this.elements.body.on('change', $this.selectors.vacationChoose, function () {
                return $this.activePeriod()
            });
            $this.elements.body.on('click', $this.selectors.removeVacationDate, function () {
                return $this.deleteVacationDate()
            });
        }
    },


    onFormVacation: function (data) {
        var $this = this;
        $this.elements.formVacation.removeClass('hidden');
        var date = $(data.currentTarget);
        var complete = date.find($this.elements.vacationCreate.selector);
        if (data.currentTarget.textContent === 'complete') {
            $this.closeCalendarModal();
        }
        if (!$(complete).get(0)) {
            date = $.parseJSON(data.currentTarget.textContent);
            $this.RedrawVacationDate(date);
        }
    },


    closeCalendarVacationTableModal: function() {
        var $this = this;
        $this.elements.formVacation.addClass('hidden');
        $this.elements.vacationModal.modal('hide');
    },

    deleteVacationDate: function () {
        var $this = this;
        var cell = document.getElementById('valueTable').rows[$this.cells.row + 2].cells;
        var id = cell[$this.cells.col + 1].id;
        var day = 0;
        $.each(cell, function (index, params) {
            if (parseInt(cell[index].id) === parseInt(id)) {
                cell[index].style.background = "";
                cell[index].classList = "";
                cell[index].id = "";
                day++;
            }
        });
        var cell = document.getElementById('valueTable2').rows[$this.cells.row + 2].cells;
        var type = $('#vacation-type').val();
        cell[parseInt(type) - 1].innerHTML = (cell[parseInt(type) - 1].innerHTML - day);

        var sum = 0;
        for (var i = 0; i < 3; i++) {
            if (cell[i].innerHTML !== '')
                sum += parseInt(cell[i].innerHTML);
        }
        cell[3].innerHTML=sum;
        $this.closeCalendarVacationTableModal();
    },

    RedrawVacationDate: function (date) {
        var $this = this;
        var cell = document.getElementById('valueTable').rows[$this.cells.row + 2].cells;
        $.each(cell, function (index) {
            if (parseInt(cell[index].id) === date.id) {
                cell[index].style.background = "";
                cell[index].classList = "";
                cell[index].id = "";
            }
        });
        var start = date.start.split('-');
        start = parseInt(start[2]);
        var end = date.end.split('-');
        end = parseInt(end[2]);
        var day = start;
        if (start < end) {
            while (day <= end) {
                if ((day + 1) < cell.length) {
                    cell[day + 1].style.background = $this.typeVaction(date.type);
                    cell[day + 1].classList = 'update';
                    cell[day + 1].id = date.id;
                }
                day++;
            }
        } else {
            for (var i = 1; i <= date.days; i++) {
                if ((start + i) < cell.length) {
                    cell[start + i].style.background = $this.typeVaction(date.type);
                    cell[start + i].classList = 'update';
                    cell[start + i].id = date.id;
                }
            }
        }
        var cell = document.getElementById('valueTable2').rows[$this.cells.row + 2].cells;

        day = parseInt(cell[date.type - 1].innerHTML);
        cell[date.type - 1].innerHTML = '';
        cell[date.type - 1].innerHTML = (day + date.days);
        var sum = 0;
        for (var i = 0; i < 3; i++) {
            if (cell[i].innerHTML !== '')
                sum += parseInt(cell[i].innerHTML);
        }
        cell[3].innerHTML=sum;
        $this.closeCalendarVacationTableModal();
    },


    _setDate: function () {
        var $this = this;
        $.ajax({
            url: $this.selectors.url + 'all-general-vacation',
            async: false,
            cache: false,
            data: {
                month: $('#vacation-month').val(),
                year: $('#vacation-year').val(),
            },
            success: function (data) {
                $this.data = data;
            }
        });
    },

    tableArray: function () {
        var $this = this; 
        return {
            data: $this.generateDataObj($this.data.users, $this.day),
            colHeaders: $this.day,
            nestedHeaders: [
                [{label: '', colspan: $this.day.length},],
                $this.day
            ],
            rowHeaders: true,
            manualColumnResize: true,
            colWidths: $this.cell,
            readOnly: true,
            fixedColumnsLeft: 1,
            preventOverflow: 'horizontal',
            cells: function (row, col, prop) {
                var cellProperties = {};
                cellProperties.renderer = "negativeValueRenderer";
                if (col <= $this.data.headerTable.length)
                    cellProperties.readOnly = true;
                return cellProperties;
            },
        }

    },

    activePeriod: function () {
        var $this = this;
        $this.elements.periodVacation.attr("href", $this.selectors.url + 'generalvacation?year=' + $('#vacation-year :selected').val() + '&month=' + $('#vacation-month :selected').val());
    },

    negativeValueRenderer: function (instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        var $this = this.VacationHelper;
        var style;
        var class_;
        var id;
        i = 0;
        if (col < 7) {
            td.title = $this.data.headerTable[col];
        }
        if (col > 5) {
            var cols = col - ($this.data.headerTable.length - 1);
            while ($this.data.weekind.length > i) {
                if (cols === $this.data.weekind[i]) {
                    style = '#EEE';
                    class_ = class_ + ' weekind';
                    td.title = $this.data.titleWeekind;
                }
                i++;
            }
            i = 0;
            while ($this.data.holiday.length > i) {
                if (cols === $this.data.holiday[i]['day']) {
                    style = '#FFB6C1';
                    class_ = class_ + ' holiday';
                    td.title = $this.data.holiday[i]['name'];
                }
                i++;
            }
            if (cols != 0) {
                var i = row;
                var user = [];
                $.each($this.data.users, function (index, name) {
                    var eventData = {
                        user_id: parseInt(index),
                        fullName: name,
                    };
                    user.push(eventData);
                });
                $.each($this.data.vacation, function (index, params) {
                    if (parseInt(moment(new Date(params.start)).format("M")) === parseInt(moment(new Date(params.end)).format("M"))) {
                        if (cols >= parseInt(moment(new Date(params.start)).format("D")) && cols <= parseInt(moment(new Date(params.end)).format("D")))
                            if (user[i].user_id === params.user_id) {
                                style = $this.typeVaction(params.type);
                                class_ = class_ + ' update';
                                id = params.id;
                                td.title = $this.data.typeVacation[params.type];
                            }
                    } else {
                        if (parseInt(moment(new Date(params.start)).format("M")) === parseInt($("#vacation-month").val())) {
                            if (cols >= parseInt(moment(new Date(params.start)).format("D")))
                                if (user[i].user_id === params.user_id) {
                                    style = $this.typeVaction(params.type);
                                    class_ = class_ + ' update';
                                    id = params.id;
                                    td.title = $this.data.typeVacation[params.type];
                                }
                        } else {
                            if (cols <= parseInt(moment(new Date(params.end)).format("D")))
                                if (user[i].user_id === params.user_id) {
                                    style = $this.typeVaction(params.type);
                                    class_ = class_ + ' update';
                                    id = params.id;
                                    td.title = $this.data.typeVacation[params.type];
                                }
                        }
                    }

                });
            }
            td.style.background = style;
            td.className = class_;
            td.id = id;

        }
    },

    typeVaction: function (type) {
        switch (type) {
            case 1:
                return '#64bf4e';
            case 2:
                return '#d61e11';
            case 3:
                return '#007dd1';
        }
    },

    generateDataObj: function () {
        var $this = this;

        var start = [];
        var data = [];
        var i = 0;

        $.each($this.data.yearVacation, function (index, params) {
            var eventData = {
                'start': parseInt(moment(new Date(params.start)).format("D")),
                'end': parseInt(moment(new Date(params.end)).format("D")),
                'user_id': params.user_id,
                'type': params.type,
                'day': params.days,
            };
            start.push(eventData);
        });

        $.each($this.data.users, function (index, name) {
            data[i] = [];
            for (var j = 0; j < $this.day.length; j++) {
                switch (j) {
                    case 0:
                        data[i][j] = name;
                        break;
                    case 1:
                        data[i][j] = $this.data.experience[index]['experience'];
                        break;
                    case 2:
                        data[i][j] = $this.data.leftVacation[index]['day'];
                        break;
                    default:
                        data[i][j] = '';
                        for (var k = 0; k < start.length; k++)
                            if (index == start[k].user_id)
                                switch (j) {
                                    case $this.data.headerTable.length - 1:
                                        data[i][j] = parseInt(data[i][j] + start[k].day);
                                        break;
                                    default:
                                        if (j == start[k].type + 2)
                                            data[i][j] = parseInt(data[i][j] + start[k].day);
                                }
                }
            }
            i++;
        });
        return data;
    },

    onCellDblClick: function (row, cell) {
        var $this = this;
        $this.cells = cell;
        var class_ = $("td.highlight").attr("class");
        var id = $("td.highlight").attr('id');
        var arr = class_.match(/\w+|"[^"]+"/g);
        if ($.inArray('update', arr) === -1)
            $this.createReportCart(cell);
        else
            $this.updateReportCart(cell, id);


    },

    createReportCart: function (cell) {
        var $this = this;
        $.pjax({
            url: $this.selectors.url + 'create',
            container: $this.selectors.formVacation,
            push: false,
        });
        $('button.vacation-button').click();
    },

    updateReportCart: function (cell, id) {
        var $this = this;
        $.pjax({
            url: $this.selectors.url + 'update',
            container: $this.selectors.formVacation,
            data: {id: id},
            push: false,
        });
        $('button.vacation-button').click();
    },

};
$(function () {
    VacationHelper.init();
});

