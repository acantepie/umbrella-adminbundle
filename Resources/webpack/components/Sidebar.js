import KernelComponent from "umbrella_core/core/KernelComponent";

export default class Sidebar extends KernelComponent {
    constructor($view) {
        super();

        this.$view = $view;
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