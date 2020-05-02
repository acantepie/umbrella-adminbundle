import 'umbrella_core/vendor/_vendor';
import 'umbrella_core/jquery-plugins/_jquery_plugins';

// components
import Sidebar from "umbrella_admin/components/Sidebar";
import Layout from "umbrella_admin/components/Layout";

import DataTable from "umbrella_core/components/DataTable";
import Select2 from "umbrella_core/components/Select2";
import AsyncSelect2 from "umbrella_core/components/AsyncSelect2";
import TagsInput from "umbrella_core/components/TagsInput";
import DatePicker from "umbrella_core/components/DatePicker";
import DateTimePicker from "umbrella_core/components/DateTimePicker";
import FileUpload from "umbrella_core/components/FileUpload";
import Collection from "umbrella_core/components/Collection";

// Default jquery plugin options
$.fn.dataTable.ext.errMode = 'throw';
$.toast.options.position = 'bottom-right';

// App
import UmbrellaApp from "umbrella_core/core/UmbrellaApp";
import JsResponseHandler from "umbrella_core/core/AjaxJsResponseHandler";

const app = new UmbrellaApp();
window.app = app;

app.use('[data-mount=Sidebar]', Sidebar);
app.use('[data-mount=Layout]', Layout);

app.use('[data-mount=DataTable]', DataTable);
app.use('.js-select2', Select2);
app.use('.js-async-select2', AsyncSelect2);
app.use('.js-umbrella-tag', TagsInput);
app.use('.js-datepicker', DatePicker);
app.use('.js-datetimepicker', DateTimePicker);
app.use('.js-umbrella-fileupload', FileUpload);
app.use('.js-umbrella-collection', Collection);

app.useAjaxHandler('default', new JsResponseHandler());
