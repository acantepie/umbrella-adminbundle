export default class Sidebar {
    constructor($view) {
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