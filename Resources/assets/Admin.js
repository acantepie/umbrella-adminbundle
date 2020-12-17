import 'umbrella_core/vendor/_vendor';
import 'umbrella_core/jquery-plugins/_jquery_plugins';

// components
import Sidebar from "umbrella_admin/components/Sidebar";
import Layout from "umbrella_admin/components/Layout";
import Notification from "umbrella_admin/components/Notification";

import DataTable from "umbrella_core/components/DataTable";
import Select2 from "umbrella_core/components/Select2";
import AsyncSelect2 from "umbrella_core/components/AsyncSelect2";
import TagsInput from "umbrella_core/components/TagsInput";
import DatePicker from "umbrella_core/components/DatePicker";
import DateTimePicker from "umbrella_core/components/DateTimePicker";
import FileUpload from "umbrella_core/components/FileUpload";
import Collection from "umbrella_core/components/Collection";

// js response action
import ShowToast from "umbrella_core/jsresponse/action/ShowToast";
import OpenModal from "umbrella_core/jsresponse/action/OpenModal";
import CloseModal from "umbrella_core/jsresponse/action/CloseModal";
import Eval from "umbrella_core/jsresponse/action/Eval";
import Redirect from "umbrella_core/jsresponse/action/Redirect";
import Reload from "umbrella_core/jsresponse/action/Reload";
import RemoveHtml from "umbrella_core/jsresponse/action/RemoveHtml";
import UpdateHtml from "umbrella_core/jsresponse/action/UpdateHtml";
import ReloadTable from "umbrella_core/jsresponse/action/ReloadTable";

// Default jquery plugin options
$.fn.dataTable.ext.errMode = 'throw';
$.toast.options.position = 'bottom-right';

// App
import UmbrellaApp from "umbrella_core/core/UmbrellaApp";

const app = new UmbrellaApp();
window.app = app;

app.use('[data-mount=Sidebar]', Sidebar);
app.use('[data-mount=Layout]', Layout);
app.use('[data-mount=DataTable]', DataTable);
app.use('[data-mount=Notification]', Notification);

app.use('.js-select2', Select2);
app.use('.js-async-select2', AsyncSelect2);
app.use('.js-tag', TagsInput);
app.use('.js-datepicker', DatePicker);
app.use('.js-datetimepicker', DateTimePicker);
app.use('.js-umbrella-fileupload', FileUpload);
app.use('.js-umbrella-collection', Collection);

app.jsResponseHandler.registerAction('toast', new ShowToast());
app.jsResponseHandler.registerAction('open_modal', new OpenModal());
app.jsResponseHandler.registerAction('close_modal', new CloseModal());
app.jsResponseHandler.registerAction('eval', new Eval());
app.jsResponseHandler.registerAction('redirect', new Redirect());
app.jsResponseHandler.registerAction('reload', new Reload());
app.jsResponseHandler.registerAction('update', new UpdateHtml());
app.jsResponseHandler.registerAction('remove', new RemoveHtml());
app.jsResponseHandler.registerAction('reload_table', new ReloadTable());