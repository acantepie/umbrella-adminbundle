import Kernel from "umbrella_core/core/Kernel";
import DataTable from "umbrella_core/components/DataTable";
import Form from "umbrella_core/components/form/Form";
import Sidebar from "umbrella_admin/components/Sidebar";
import Layout from "umbrella_admin/components/Layout";
import JsResponseHandler from "umbrella_core/core/JsResponseHandler";

// vendors
import 'umbrella_core/vendor/jquery/jquery';
import 'umbrella_core/vendor/mustache/mustache';
import 'umbrella_core/vendor/bootstrap/bootstrap';
import 'umbrella_core/vendor/select2/select2';
import 'umbrella_core/vendor/bootstrap-tagsinput/bootstrap-tagsinput';
import 'umbrella_core/vendor/bootstrap-datepicker/bootstrap-datepicker';
import 'umbrella_core/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker';
import 'umbrella_core/vendor/datatables/datatable';

import 'metismenu';
import 'simplebar';
import 'jquery-toast-plugin';
import 'jquery-treegrid/js/jquery.treegrid';

// plugins
import 'umbrella_core/plugins/serializeFormToFormData';
import 'umbrella_core/plugins/serializeFormToJson';
import 'umbrella_core/plugins/confirm';

// Default jquery plugin options
$.fn.dataTable.ext.errMode = 'throw';
$.toast.options.position = 'bottom-right';

// Core services
window.Kernel = new Kernel();

// Core components
window.Kernel.registerComponent('DataTable', DataTable);
window.Kernel.registerComponent('Form', Form);

// Admin components
window.Kernel.registerComponent('Sidebar', Sidebar);
window.Kernel.registerComponent('Layout', Layout);

// Ajax handler
window.Kernel.registerAjaxHandler('jsresponse', JsResponseHandler);