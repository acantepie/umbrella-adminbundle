import Component from "umbrella_core/core/Component";

export default class Sidebar extends Component {
    constructor($view) {
        super($view);
        this.init();
    }

    init() {
        this.$view.metisMenu();
        $(document).on('click', '.button-menu-mobile', (e) => {
            e.preventDefault();
            $('body').toggleClass('sidebar-enable');
        });
    }
}