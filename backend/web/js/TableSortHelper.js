var TableSortHelper = {
    map: {},

    _map: function() {
        var $this = this;
        $this.map = {
            tableBody: $(".sortable-table tbody"),
            refreshLink: $('a.refresh-link')
        };
    },

    init: function () {
        var $this = this;
        $this._map();

        if ($this.map.tableBody.length > 0) {
            $this.sortableTable();
        }
    },

    sortableTable: function () {
        var $this = this,
            url = $this.map.refreshLink.attr('href'),
            path = url.split("/"),
            area = path[0] != '' ? path[0] : path[1],
            controller = path[1] != '' ? path[1] : path[0];
        var pathController = controller.split("?");
        controller = pathController[0] != '' ? pathController[0] : pathController[1];

        // Make table sortable
        $this.map.tableBody.sortable({
            helper: $this.fixHelperModified,
            update: function (event, ui) {
                var sortedList = $this.map.tableBody.sortable('toArray').toString();
                $.ajax({
                    url: '/' + controller + '/sort',
                    type: 'POST',
                    data: {sortedList: sortedList}
                });
            }
        }).disableSelection();
    },

    // Helper function to keep table row from collapsing when being sorted
    fixHelperModified: function (e, tr) {
        var originals = tr.children(),
            helper = tr.clone();

        helper.children().each(function (index) {
            $(this).width(originals.eq(index).width())
        });
        return helper;
    }
};

$(function() {
    TableSortHelper.init();
});
