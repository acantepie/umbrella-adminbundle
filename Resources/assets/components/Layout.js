import Component from "umbrella_core/core/Component";

export default class Layout extends Component {

    constructor($view) {
        super($view);
        this.$window = $(window);
        this.init();
    }

    init() {
        this.$window.on('resize', (e) => {
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
        this.$view.attr('data-leftbar-compact-mode', 'condensed');
    }

    desactivateCondensedSidebar() {
        this.$view.attr('data-leftbar-compact-mode', false);
    }
}