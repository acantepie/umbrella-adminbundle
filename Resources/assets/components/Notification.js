import Component from "umbrella_core/core/Component";

export default class Notification extends Component {

    static poll = 10000; // 10s

    constructor($view) {
        super($view);

        this.refreshUrl = $view.data('refresh-url');
        this.refreshXhr = null;

        this._bind();
    }

    _bind() {
        this.$view.on('shown.bs.dropdown', () => {
            this._refresh(true);
        });

        this.$view.on('click', '[data-href]', (e) => {
            window.location.href = $(e.currentTarget).data('href');
        });
    }

    /**
     * Refresh Notifications
     */
    _refresh(poll = true) {
        if (this.refreshXhr) {
            this.refreshXhr.abort();
        }

        if (this._isOpen()) {
            $.get(this.refreshUrl, (notifications) => {
                this._renderList(notifications);
                if (poll) {
                    setTimeout(() => {
                        this._refresh()
                    }, Notification.poll);
                }
            });
        }
    }

    /**
     * Render list of notifications
     */
    _renderList(notifications) {
        const $list = this.$view.find('.js-notificarion-list .simplebar-content');
        $list.html('');

        console.log($list[0]);

        for (let notification of notifications) {
            const tpl = this._getTemplate(notification);
            if (tpl) {
                $list.append(mustache.render(tpl, notification));
            }
        }

        if (notifications.length === 0) {
            $list.append(mustache.render($('#notification-empty-tpl').html()));
        }
    }

    _isOpen() {
        return this.$view.find('.dropdown-menu').hasClass('show');
    }

    _getTemplate(notification) {
        const tplId = `#notification-fw-${notification.state}-tpl`;
        if ($(tplId).length) {
            return $(tplId).html();
        } else {
            console.warn('No template found with id ' + tplId);
            return false;
        }
    }
}
