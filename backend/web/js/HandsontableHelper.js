var ReportView;
var HandsontableHelper={

    map:{},

    hot:{},

    cells:{},

    _map: function () {
        var $this = this;
        $this.map = {
            container: document.getElementById('repotViewExample'),
            formCampaign: $('#formCampaign'),
            formProject: $('#formProject'),
            url:'/calendar/',
            formCalendar: $('#formCalendar'),
            reportcart_modal:$('#reportcart-modal'),
            removeDateCalendar:$('#removeDate'),
            calendar_modal:$('#calendar-modal'),
            project_modal:$('#project-modal'),
            document: $(document),
            calendarUserId:$('#calendar-user_id,#calendar-year,#calendar-month,#calendar-created_by'),
            handsontableInputHolder:$('div.handsontableInputHolder'),
            calendarCreate:$("#calendarCreate"),
            projectUpdateName:$("#projectUpdateName"),
            text_danger:$("div.text-danger"),
            form_group:$(".form-group"),
            tr_table:$('td.combat'),
            start_at:$('input#calendar-end_at'),
            user_id:$("a#user_id"),
            calendar_hours:$('#calendar-hours'),
            calendarFormCancel:'.cancel-modal',
            cssloadTavleL:$('.cssload-thecube'),
            addRow:'a.addRow'
        };
    },

    init: function () {
        var $this = this;
        $this._map();
        $(window).load(function () {$('.handsontable1').addClass('handsontableBody')});
        if (ReportView) {
            $this._setDate();
            $this.map.start_at.on('change', function () {
                return $this.activeStartAt()
            });

            $this.componentDidMount();
            $this.map.formCalendar.on('pjax:success', function (data, status, xhr, options) {
                $this.onFormCalendar(data);
                $($this.map.calendarFormCancel).on('click', function () {
                    event.preventDefault();
                    $this.closeCalendarTableModal()
                });
            });

            $this.map.formProject.on('pjax:success', function (data, status, xhr, options) {
                $this.onFormProject(data);
                $($this.map.calendarFormCancel).on('click', function () {
                    event.preventDefault();
                    $this.closeProjectModal()
                });
            });

            $this.map.document.on('click', $this.map.addRow, function () {
                return $this.createProjectName();
            });

            $this.map.document.on('click', $this.map.removeDateCalendar.selector, function () {
                return $this.deleteDateCalendar()
            });
            $this.map.document.on('change', $this.map.calendarUserId.selector, function () {
                return $this.activeUser()
            });
        }

        $('.htCore').attr('id', 'valueTable');
        $this.calculationHour();
    },


    onFormProject: function (data) {
        var $this = this;
        $this.map.formProject.removeClass('hidden');
        if(data.currentTarget.textContent === 'complete') {
            $this.map.cssloadTavleL.fadeIn();
            $this.closeProjectModal();
            $this.updateTable();
            $this.map.cssloadTavleL.fadeOut();
        }
    },


    onFormCalendar: function (data) {
        var $this = this;
        $this.map.formCalendar.removeClass('hidden');
        var date=$(data.currentTarget);
        var complete=date.find($this.map.calendarCreate.selector);
        if(data.currentTarget.textContent==='complete') {
            $this.closeCalendarTableModal();$this.deleteCalendar();
        }
        if(!$(complete).get(0)) {
            $this.deleteCalendar();
            date=$.parseJSON(data.currentTarget.textContent);
            $this.closeCalendarTableModal();
            $this.addCalendar(date);
        }
    },

    closeCalendarTableModal: function () {
        var $this = this;
        $this.map.formCalendar.addClass('hidden');
        $this.map.calendar_modal.modal('hide');
    },

    closeProjectModal: function () {
        var $this = this;
        $this.map.formProject.addClass('hidden');
        $this.map.project_modal.modal('hide');
    },



    deleteDateCalendar:function () {
        var $this=this;
        $this.deleteCalendar();
    },

    createReportCart:function (cell) {
        var $this=this;
        $.pjax({
            url: $this.map.url+'create-table',
            container: $this.map.formCalendar.selector,
            push: false
        });
        $('button.calendar-button').click();
        return false;
    },

    updateReportCart:function (cell, id) {
        var $this=this;
        $.pjax({
            url: $this.map.url+'update-table',
            container: $this.map.formCalendar.selector,
            data:{id:id},
            push: false
        });
        $('button.calendar-button').click();
        return false;
    },

    updateProjectName:function (cell, id) {
        var $this=this;
        $.pjax({
            url: '/project/update',
            container: $this.map.formProject.selector,
            data:{id:id},
            push: false
        });
        $('button.project-button').click();
    },

    createProjectName:function () {
        $('button.project-button').click();
    },

    updateTable:function () {
        var $this =this;
        var Class=$("#example").attr("class");
        $("#example").empty();
        var arr = Class.match(/\w+|"[^"]+"/g);
        for(var item = 0; item < arr.length; item++) {
            if(arr[item] != 'handsontable1') {
                $("#example").removeClass(arr[item]);
            }
        }
        $this._setDate();
        $this.componentDidMount();
        $this.calculationHour();
        $('.htCore').attr('id', 'valueTable');
    },


    _setDate:function () {
        var $this=this;
        $.ajax({
            url: $this.map.url+'all-date',
            async: false,
            cache:false,
            data:{
                id: $('#calendar-user_id').val(),
                year:$('#calendar-year').val(),
                month:$('#calendar-month').val() ,
                created_by:$('#calendar-created_by').val()
            },
            success: function (data) {
                return $this.fillData(data)
            }
        });
    },

    fillData:function (data) {
        var $this = this;
        var date=$.parseJSON(data);
        weekind=$.parseJSON(date.weekind);
        project = $.parseJSON(date.project);
        holiday=$.parseJSON(date.holiday);
        calendar=$.parseJSON(date.calendar);
        vacation=$.parseJSON(date.vacation);
        cell=[];
        id=date.id;
        day=$.parseJSON(date.day);
        var i=0;
        cell.push(100);
        while(i<day.length-3){
            cell.push(30);
            i++;
        }
        cell.push(50);
        cell.push(100);
    },

    componentDidMount:function () {
        var $this = this;
        $this.hot = new Handsontable($this.map.container, $this.hotArray());
        $this.hot.view.wt.update('onCellDblClick', function (row, cell) {return $this.onCellDblClick(row,cell)});

    },



    hotArray:function () {
        var $this=this;
        return {
            data: $this.generateDataObj(project, day),
            colHeaders: day,
            rowHeaders: true,
            manualColumnResize: true,
            //minRows: length,
            colWidths: cell,
            fixedColumnsLeft: 1,
            //minSpareRows: 1,
            renderAllRows: true,
            rowHeaderWidth: 30,
            //stretchH: 'last',
            columnSummary:function () {return $this.columnSummary(day)},
            preventOverflow: 'horizontal',
            afterChange: function (changes, source) {return $this.afterChange(changes, source)},
            cells: function (row, col, prop) {return $this.cellsHot(row, col, prop)}
        }

    },


    columnSummary: function (day) {
        var $this=this;
        var columnSummary=[];
        for(var i=1;i<day.length-2;i++) {
            columnSummary.push({
                destinationRow:project.length,
                destinationColumn: i,
                type: 'custom',
                customFunction: function (endpoint) {return $this.customFunction(endpoint, this)},
                forceNumeric: true
            });
        }
        return columnSummary;

    },

    customFunction: function (endpoint, $this) {
        var evenCount = 0;
        var hotInstance = $this.hot;
        function checkRange (rowRange) {
            var i = rowRange[1]-1 || rowRange[0];
            var counter = 0;
            do {
                if (!isNaN(parseInt(hotInstance.getDataAtCell(i, endpoint.sourceColumn), 10))) {
                    counter+=parseInt(hotInstance.getDataAtCell(i, endpoint.sourceColumn), 10);
                }
                i--;
            } while (i >= rowRange[0]);
            return counter;
        }

        for (var r in endpoint.ranges) {
            if (endpoint.ranges.hasOwnProperty(r)) {
                evenCount += checkRange(endpoint.ranges[r]);
            }
        }

        if(evenCount!=0) {
            return evenCount;
        }
        return '';
    },


    cellsHot:function (row, col, prop) {
        var $this=this;
        var cellProperties = {};
        cellProperties.renderer = $this.RowRenderer;
        if(col === 0 || col === day.length-2 || col === day.length-1) {
            cellProperties.readOnly = true;
        }
        // if(row===project.length+1)
        //     cellProperties.readOnly = true;

        return cellProperties;
    },

    onCellDblClick:function (row, cell) {
        var $this=this;
        cells=cell;
        var flag=$('td.highlight');
        if(!flag.is( ".htDimmed" )) {
            var class_ = flag.attr("class");
            var id = flag.attr('id');
            var arr = class_.match(/\w+|"[^"]+"/g);
            if ($.inArray('project_name', arr) === -1) {
                if ($.inArray('update', arr) === -1) {
                    $this.createReportCart(cell);
                } else {
                    $this.updateReportCart(cell, id);
                }
            } else {
                $this.updateProjectName(cell,id);
            }

        }

    },

    afterChange:function (changes, source) {
        var $this =this;
        if (source === 'edit') {
            changes.forEach(function (item) {
                var row = item[0],
                    col = item[1],
                    prevValue = item[2],
                    value = item[3];
                cells = {
                    row: row,
                    col: col
                };
                var calendar_id=$this.identifyCellId();
                var year=$( "label#year" ).text();
                var month=$( "label#month" ).text();
                var created_by=$('#calendar-created_by :selected').val();
                var projects=project[row].id;
                changes={
                    user_id:id,
                    year:year,
                    month:month,
                    created_by:created_by,
                    day:col,
                    actual_time:value,
                    id_project:projects,
                    id:calendar_id
                };
                $this._setOptions(changes);
                $this.calculationHour();

            });

        }
        setTimeout(function () { $('div#massage').empty(); }, 5000);

    },

    identifyCellId:function () {
        var cell=document.getElementById('valueTable').rows[cells.row+1].cells;
        return parseInt(cell[cells.col+1].id);
    },

    activeUser:function () {
        var $this=this;
        $this.map.user_id.attr("href",$this.map.url+$('#calendar-user_id :selected').val()+'/view?year='+$('#calendar-year :selected').val()+'&month='+$('#calendar-month :selected').val()+'&created_by='+$('#calendar-created_by :selected').val());
    },

    activeStartAt:function () {
        var $this=this;
        cell[cells.col+1].innerHTML=$this.map.calendar_hours.val();
        $this.calculationHour();
    },

    RowRenderer:function (instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        var style,class_,id;
       if(row < project.length) {
                var i = row;
               $.each(calendar, function(index, params) {
                        if(parseInt(project[i].id) == params.id_project) {
                            if(parseInt(params.start) == col) {
                                if(params.comment != false){
                                    td.className = 'update comment';
                                    td.id = params.id;
                                }
                                else{
                                    td.className ='update';
                                    td.id=params.id;
                                }

                            }
                        }
                    });
        }
        if(col === 0 && row < project.length) {
            td.className = 'project_name';
            td.id=project[row].id;
        }
        if(row!=project.length && col!=day.length-2 && col!=day.length-1) {
            td.className =  td.className+' combat';
        }
        if(col==day.length-2&&row!=project.length) {
            td.className = td.className+' total-combat';
        }
        if(col==day.length-1&&row!=project.length) {
            td.className = td.className+' weekind-total-combat';
        }
        i=0;
        while(weekind.length > i) {
            if (col === weekind[i]) {
                style = '#EEE';
                td.className = td.className+' weekind';
            }
            i++;
        }
        i=0;
        while(vacation.length > i) {
            if (col === vacation[i] ) {
                style = '#FFB6C1';
                td.className = td.className+' holiday';
            }
            i++;
        }
        i=0;
        while(holiday.length > i) {
            if (col === holiday[i] ) {
                style = '#FFB6C1';
                td.className = td.className+' holiday';
            }
            i++;
        }
        td.style.background=style;
    },

    generateDataObj:function (project, day) {
        var data = [];
        for (var i = 0; i < project.length; i++) {
            data[i] = [];
            for (var j = 0; j < day.length; j++) {
                if(j < 1){
                    data[i][j] = project[i].name;
                } else {
                    data[i][j] = '';
                }
                for (var c = 0; c < day.length; c++) {
                    for(var k = 0; k < calendar.length; k++){
                        if(c == parseInt(calendar[k].start) && project[i].id == calendar[k].id_project) {
                            data[i][c] = calendar[k].actual_time;
                        }
                    }
                }
            }
        }
        for (i = 0; i < 1; i++) {
            data.push([]);
        }
        return data;
    },

    calculationHour:function () {
        $('tr').each(function () {
            var sum = 0;
            $(this).find('.combat').each(function () {
                var combat = $(this).text();
                if (!isNaN(combat) && combat.length !== 0) {
                    sum += parseFloat(combat);
                }
            });
            $('.total-combat', this).html(sum);
            sum = 0;
            $(this).find('.weekind,.holiday').each(function () {
                var combat = $(this).text();
                if (!isNaN(combat) && combat.length !== 0) {
                    sum += parseFloat(combat);
                }
            });
            $('.weekind-total-combat', this).html(sum);
        });
        var sum = 0;
        $(".total-combat").each(function () {
            sum += parseFloat($(this).text());
        });
        $('#all-total-combat').val(sum);
        sum = 0;
        $(".weekind-total-combat").each(function () {
            sum += parseFloat($(this).text());
        });
        $('#weekind-all-total-combat').val(sum);
    },

    addCalendar:function (date) {
        var $this=this;
        $this.deleteCalendar();
        var cell=document.getElementById('valueTable').rows[cells.row+1].cells;
        cell[cells.col+1].id=date.id;
        cell[cells.col+1].innerHTML=date.actual_time;
        if(date.comment!=false){
            $this.hot.setCellMeta(cells.row, cells.col, 'className', 'update comment');
        } else {
            $this.hot.setCellMeta(cells.row, cells.col, 'className', 'update');
        }
        $this.hot.render();
    },

    deleteCalendar:function () {
        var $this=this;
        $this.hot.setCellMeta(cells.row,cells.col,'className','');
        var cell=document.getElementById('valueTable').rows[cells.row+1].cells;
        cell[cells.col+1].id='';
        cell[cells.col+1].innerHTML='';
        $this.hot.render();

    },

    _setOptions:function (changes) {
        var $this=this;
        $.ajax({
            url: $this.map.url+'add-report',
            data:{changes:changes},
            async: false,
            cache: false,
            success: function (json) {
                var massage=$.parseJSON (json);
                switch (massage.option){
                    case 'delete':
                        $this.deleteCalendar(parseInt(massage.id));
                        break;
                    case 'update':
                        $this.addCalendar($.parseJSON(massage.date));
                        break;
                    case 'error':
                        $this.errorMessage(massage);
                        break;
                }
            }
        });
    },

    errorMessage:function (massage) {
        array='<div class="alert-'+massage.type+' alert fade in">'+
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+
            massage.text+
            '</div>';
        $(array).appendTo('#massage');
    }

};
$(function() {
    HandsontableHelper.init();
});