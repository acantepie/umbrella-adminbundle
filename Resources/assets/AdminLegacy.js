import 'umbrella_core/vendor/_vendor';
import 'umbrella_core/plugins/_plugins';

// Kernel
import Kernel from "umbrella_core/core/Kernel";
import JsResponseHandler from "umbrella_core/core/JsResponseHandler";

// components
import DataTable from "umbrella_core/components_legacy/DataTable";
import Form from "umbrella_core/components_legacy/form/Form";
import Sidebar from "umbrella_admin/components/Sidebar";
import Layout from "umbrella_admin/components/Layout";


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