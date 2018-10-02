var DesignHelper = {
    map: {},

    _map: function () {
        var $this = this;
        $this.map = {
            sideBar: $('.sidebar'),
            sideBarScroller: $('.sidebar .scroller'),
            sideBarToggle: $('.sidebar-toggle .toggle'),
            sideBarElement: $('.sidebar-menu li'),
            sideBarSubElement: $('.nav.sidebar-menu li .dropdown-menu li'),
            openSidebar: 'open-sidebar'
        };
    },

    init: function () {
        var $this = this;
        $this._map();

        if ($this.map.sideBarElement.hasClass('active')) {
            $this.map.sideBarScroller.animate({
                scrollTop: $('.active').offset().top - 100
            }, 100);
            $this.map.sideBar.animate({
                scrollTop: $('.active').offset().top - 100
            }, 100);
            $this.map.sideBar.find('li.active').toggleClass('open');
        } else {
            $('body').toggleClass($this.map.openSidebar);
        }

        $this.map.sideBarToggle.on('click', function () {
            $('body').toggleClass($this.map.openSidebar);
        });

        $this.map.sideBarElement.on('click', function () {
            $('body').addClass($this.map.openSidebar);
        });

        // Active menu elements handling
        $this.map.sideBarSubElement.each(function() {
            var currentRoute = window.location.pathname;
            if ($(this).find('a').attr('href') === currentRoute) {
                $(this).addClass('active');
                $(this).closest('li.dropdown').addClass('active open');
                $('body').addClass($this.map.openSidebar);
            }
        });

    },

};

$(function () {
    DesignHelper.init();
});

