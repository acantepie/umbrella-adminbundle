// vendors

require('umbrella_core/vendor/jquery/jquery');
require('umbrella_core/vendor/jquery-minicolors/jquery-minicolors');
require('umbrella_core/vendor/mustache/mustache');
require('umbrella_core/vendor/bootstrap/bootstrap');
require('umbrella_core/vendor/select2/select2');
require('umbrella_core/vendor/bootstrap-tagsinput/bootstrap-tagsinput');
require('umbrella_core/vendor/toastr/toastr');
require('umbrella_core/vendor/bootstrap-datepicker/bootstrap-datepicker');
require('umbrella_core/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker');
require('umbrella_core/vendor/datatables/datatable');
require('umbrella_core/vendor/material-design-icons/material-design-icons');
require('jquery-ui-sortable-npm'); // needed for nestedSortable
require('nestedSortable');

// umbrella services

const Kernel = require('umbrella_core/services/Kernel');
window.Kernel = new Kernel();
window.Api = require('umbrella_core/components/appproxy/Api');
window.ConfirmModal = require('umbrella_core/components/confirmModal/ConfirmModal');
window.Spinner = require('umbrella_core/components/spinner/Spinner');

const Bindings  = require('umbrella_core/services/Bindings');

// umbrella components

window.Kernel.registerComponent('DataTable', require('umbrella_core/components/datatable/DataTable'));
window.Kernel.registerComponent('Tree', require('umbrella_core/components/tree/Tree'));
window.Kernel.registerComponent('Form', require('umbrella_core/components/form/Form'));
window.Form = require('umbrella_core/components/form/Form');

// custom ui helpers

require('./components/ui-nav');
require('./components/ui-scroll-to');
window.Utils = require('umbrella_core/utils/Utils');


window.mountApp = function () {

    let $body = $('body');

    $.fn.dataTable.ext.errMode = 'throw';

    // some bind
    new Bindings($body);

    // mount components
    window.Kernel.mountComponents($body);

    //Pace.start();
};

