var reportCart;
var ReportCartHelper={

    map:{},

    hot:{},

    _map: function () {
        var $this = this;
        $this.map = {
            url:'/calendar/',
            viewCalendar: $('#viewCalendar'),
            document: $(document),
            calendarUserId:$('#calendar-user_id,#calendar-year,#calendar-month,#calendar-created_by'),
            calendar_modal:$('#calendar-modal'),
            form:$('#reportCart'),
        };
    },

    init: function () {
        var $this = this;
        $this._map();
        $this.map.document.on('change', $this.map.calendarUserId.selector, function () {return $this.activeUser()});
        if(reportCart) {
            $this._setDate();
            Handsontable.renderers.registerRenderer('negativeValueRenderer', ReportCartHelper.negativeValueRenderer);
            $this.hotArray();
        }
    },

    activeUser:function () {
        var $this = this;
        $("a#user_id-reportCart").attr("href",$this.map.url+'reportcart'+'?id='+$('#calendar-user_id :selected').val()+'&year='+$('#calendar-year :selected').val()+'&month='+$('#calendar-month :selected').val()+'&created_by='+$('#calendar-created_by :selected').val());
    },

    hotArray:function () {
        var $this=this;
        $.each(date, function (index, params) {
            calendar=$.parseJSON(params.calendar);
            project=$.parseJSON(params.project);
            vacation=$.parseJSON(params.vacation);
            $this.hot = new Handsontable(document.getElementById('example-'+params.user_id), $this.hotArrays());
            $this.calculationHourReportCart(params.user_id);
        });
    },

    hotArrays:function () {
        var $this=this;
        return {
            data: HandsontableHelper.generateDataObj(project, day),
            colHeaders: day,
            rowHeaders: true,
            manualColumnResize: true,
            minRows: project.length+1,
            colWidths: cell,
            rowHeaderWidth: 30,
            readOnly: true,
            columnSummary:function () {return HandsontableHelper.columnSummary(day)},
            fixedColumnsLeft: 1,
            preventOverflow: 'horizontal',
            cells: function (row, col, prop) {
                var cellProperties = {};
                cellProperties.renderer = "negativeValueRenderer";
                return cellProperties;
            },
        }
    },

    _setDate:function () {
        var $this=this;
        $.ajax({
            url: $this.map.url+'all-report-cart',
            async: false,
            cache:false,
            data:{
                id: $('#calendar-user_id').val(),
                year:$('#calendar-year').val(),
                month:$('#calendar-month').val() ,
                created_by:$('#calendar-created_by').val()
            },
            success: function (data) { return $this.fillData(data)}
        });
    },

    fillData:function (data) {
        var object=$.parseJSON(data);
        date=object.reportcart;
        weekind=$.parseJSON(object.weekind);
        holiday=$.parseJSON(object.holiday);
        cell=[];
        id=date.id;
        day=$.parseJSON(object.day);
        var i=0;
        cell.push(100);
        while(i<day.length-3){
            cell.push(30);
            i++;
        }
        cell.push(50);
        cell.push(100);
    },

    negativeValueRenderer:function (instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        if(row != project.length && col != day.length-2 && col != day.length-1) {
            td.className =  td.className+' combat';
        }
        if(col == day.length-2 && row != project.length) {
            td.className = td.className+' total-combat';
        }
        if(col == day.length-1 && row != project.length) {
            td.className = td.className+' weekind-total-combat';
        }
        var i=0;
        while(weekind.length>i) {
            if (col === weekind[i]) {
                td.style.background = '#EEE';
                td.className = td.className+' weekind';
            }
            i++;
        }
        i=0;
        while(vacation.length > i) {
            if (col === vacation[i] ) {
                td.style.background = '#FFB6C1';
                td.className = td.className+' holiday';
            }
            i++;
        }
        i=0;
        while(holiday.length > i) {
            if (col === holiday[i] ) {
                td.style.background = '#FFB6C1';
                td.className = td.className+' holiday';
            }
            i++;
        }
    },

    calculationHourReportCart:function (id) {
        $('#example-'+id+' tr').each(function () {
            var sum = 0;
            $(this).find('.combat').each(function () {
                var combat = $(this).text();
                if (!isNaN(combat) && combat.length !== 0) {
                    sum += parseFloat(combat);
                }
            });
            $('.total-combat', this).html(sum);
        });
        $('#example-'+id+' tr').each(function () {
            var sum = 0;
            $(this).find('.weekind,.holiday').each(function () {
                var combat = $(this).text();
                if (!isNaN(combat) && combat.length !== 0) {
                    sum += parseFloat(combat);
                }
            });
            $('.weekind-total-combat', this).html(sum);
        });
        var sum = 0;
        $('div#example-'+id+' .total-combat').each(function () {
            sum += parseFloat($(this).text());
        });
        $('#all-reportcart-'+id).val(sum);
        sum = 0;
        $('div#example-'+id+' .weekind-total-combat').each(function () {
            sum += parseFloat($(this).text());
        });
        $('#weekind-all-reportcart-'+id).val(sum);
    },
};
$(function () {
    ReportCartHelper.init();
});

