class Layout {
    constructor($body) {
        this.$body = $body;
        this.$window = $(window);
        this.init();
    }

    init() {
        this.$window.on('resize', function (e) {
            e.preventDefault();
            this.adjustLayout();
        });

        this.adjustLayout();
    }

    adjustLayout() {
        // in case of small size, add class enlarge to have minimal menu
        if (this.$window.width() >= 767 && this.$window.width() <= 1028) {
            this.activateCondensedSidebar();
        } else {
            this.desactivateCondensedSidebar();
        }
    };

    activateCondensedSidebar() {
        this.$body.attr('data-leftbar-compact-mode', 'condensed');
    }

    desactivateCondensedSidebar() {
        this.$body.attr('data-leftbar-compact-mode', false);
    }
}

module.exports = Layout;