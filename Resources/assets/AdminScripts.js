import Kernel from "umbrella_core/core/Kernel";
import Api from "umbrella_core/components/appproxy/Api";
import ConfirmModal from "umbrella_core/components/confirmModal/ConfirmModal";
import DataTable from "umbrella_core/components/datatable/DataTable";
import Tree from "umbrella_core/components/tree/Tree";
import Form from "umbrella_core/components/form/Form";
import Sidebar from "umbrella_admin/components/Sidebar";
import Layout from "umbrella_admin/components/Layout";
import Bindings from "umbrella_core/core/Bindings";


// vendors
import 'umbrella_core/vendor/jquery/jquery';
import 'umbrella_core/vendor/jquery-minicolors/jquery-minicolors';
import 'umbrella_core/vendor/mustache/mustache';
import 'umbrella_core/vendor/bootstrap/bootstrap';
import 'umbrella_core/vendor/select2/select2';
import 'umbrella_core/vendor/bootstrap-tagsinput/bootstrap-tagsinput';
import 'umbrella_core/vendor/toastr/toastr';
import 'umbrella_core/vendor/bootstrap-datepicker/bootstrap-datepicker';
import 'umbrella_core/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker';
import 'umbrella_core/vendor/datatables/datatable';
import 'umbrella_core/vendor/material-design-icons/material-design-icons';
import 'jquery-ui-sortable-npm'; // needed for nestedSortable
import 'nestedSortable';
import 'metismenu';
import 'simplebar';

// Core services
window.Kernel = new Kernel();
window.Api = Api;
window.ConfirmModal = ConfirmModal;

// Core components
window.Kernel.registerComponent('DataTable', DataTable);
window.Kernel.registerComponent('Tree', Tree);
window.Kernel.registerComponent('Form', Form);

// Admin components
window.Kernel.registerComponent('Sidebar', Sidebar);
window.Kernel.registerComponent('Layout', Layout);


window.mountApp = function () {
    $.fn.dataTable.ext.errMode = 'throw';

    // some bind
    new Bindings($('body'));

    // mount components
    window.Kernel.mountComponents($('html'));
};

