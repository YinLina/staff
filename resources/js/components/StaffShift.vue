<template>
    <div id="staffShift">
        <v-row>
        <v-col cols="12" sm="6">
            <v-row>
                <v-col cols="12" sm="12" md="5">
                   <h3 class="red-lighten-3 font-weight-bold">
                      <v-icon color="grey darken-2" class="mb-1">mdi-alarm</v-icon>
                      <span class="text-decoration-underline font-weight-medium">{{$t('menu.StaffShift')}}</span>
                      <v-chip color="grey lighten-2 grey--text text--darken-3">{{ StaffShiftCount }}</v-chip>
                   </h3>
                </v-col>
                <v-col cols="12" sm="12" md="7">
                   <v-text-field
                      v-model="StaffShiftSearch"
                      append-icon="mdi-magnify"
                      single-line
                      class="txt-search"
                      v-bind:label="$t('absent.search')"
                   ></v-text-field>
                </v-col>
            </v-row>
        </v-col>

        <v-col cols="12" sm="6" class="text-end">
            <v-btn class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                small
                @click="openDialogForm">
                <v-icon left>mdi-plus</v-icon>
                   {{$t('staffShift.addShift')}}
            </v-btn>
            <v-btn
                small
                class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                @click="deleteStaffShift()" 
                >
                {{ $t('staffShift.deleteShift') }}
            </v-btn>

            <v-dialog
                v-model="openDialog"
                width="500"
                persistent
                >
                <v-card>
                    <v-toolbar
                    dense
                    flat
                    color="lighten-1"
                    class="khawin-background-color"
                    >
                        <span v-if="editMode === false" class="khmer-font white--text">
                            <v-icon left color="white">mdi-alarm</v-icon>
                            {{$t('staffShift.addShift')}}
                        </span>
                        <span v-else class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-arrow-left</v-icon>
                            {{$t('staffShift.editShift')}}
                        </span>
                    </v-toolbar> 
                
                    <form @submit.prevent="editMode ? updateStaffShift() : createStaffShift()">
                    <v-card-text>    
                        <v-autocomplete
                            v-model="form.Employees_id" 
                            :items="employeeData"
                            :item-text="(item) => item.first_name+ ' ' + item.last_name"
                            item-value="id"
                            v-bind:label="$t('staffShift.tbEmployeeName')" 
                            prepend-icon="mdi-account"
                            color="cyan darken-1"
                            class="khmer-font"
                            clearable
                            :error-messages="errorsMessage.Employees_id"
                        >
                            <template v-slot:selection="data">
                                <v-chip
                                    label
                                    dark
                                    color="blue-grey darken-2"
                                    class="text-capitalize pa-1 font-weight-regular"
                                    small
                                    >
                                    {{ data.item.first_name + " " + data.item.last_name }}
                                </v-chip>
                            </template>
                        </v-autocomplete>

                        <v-autocomplete
                            v-model="form.ShiftHeader_PKID" 
                            :items="ShiftData"
                            :item-text="(item) => item.ShiftName"
                            item-value="PKID"
                            v-bind:label="$t('staffShift.tbShiftName')" 
                            prepend-icon="mdi-account-arrow-left"
                            color="cyan darken-1"
                            class="khmer-font"
                            clearable
                            :error-messages="errorsMessage.ShiftHeader_PKID"   
                        >
                            <template v-slot:selection="data">
                                <v-chip
                                    label
                                    dark
                                    color="blue-grey darken-2"
                                    class="text-capitalize pa-1 font-weight-regular"
                                    small
                                    >
                                    {{ data.item.ShiftName }}
                                </v-chip>
                            </template>
                        </v-autocomplete>

                        <v-menu 
                            :close-on-content-click="false" 
                            max-width="290" 
                            v-model="ChooseStartDate"
                            >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    :value="DateFormated"
                                    v-on="on"
                                    v-bind="attrs"
                                    class="khmer-font"
                                    color="cyan darken-1"
                                    v-bind:label="$t('staffShift.tbStartDate')"
                                    prepend-icon="mdi-calendar"
                                    clearable
                                    :error-messages="errorsMessage.StartDate"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                @input="ChooseStartDate = false"
                                color="cyan darken-1"
                                v-model="form.StartDate">
                            </v-date-picker>
                        </v-menu>

                        <v-menu 
                            :close-on-content-click="false" 
                            max-width="290" 
                            v-model="ChooseEndDate"
                            >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    :value="DateFormat"
                                    v-on="on"
                                    v-bind="attrs"
                                    class="khmer-font"
                                    color="cyan darken-1"
                                    v-bind:label="$t('staffShift.tbEndDate')"
                                    prepend-icon="mdi-calendar"
                                    clearable
                                    :error-messages="errorsMessage.EndDate"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                @input="ChooseEndDate = false"
                                color="cyan darken-1"
                                v-model="form.EndDate">
                            </v-date-picker>
                        </v-menu>

                        <v-text-field
                            v-model="form.Remark"
                            prepend-icon="mdi-pencil"
                            class="khmer-font"
                            v-bind:label="$t('staffShift.tbRemark')"
                            clearable
                        ></v-text-field>
                    </v-card-text>

                    <!-- button cancel and save -->
                    <v-card-actions class="khmer-font">
                        <v-spacer></v-spacer>
                        <v-btn color="grey lighten-2" small depressed @click="closeDialog">{{$t('absent.btnCancel')}}</v-btn>
                        <v-btn color="khawin-background-color khmer-font" dark small type="submit" :loading="btnSaveLoading">{{$t('absent.btnSave')}}</v-btn>
                    </v-card-actions>
                </form>
            </v-card>
            </v-dialog>
        </v-col>
        </v-row>
          
        <!----- data table staff shift ----->
        <v-card class="mx-auto table-card v-card v-sheet theme--ligh">
            <AlertMessageDialog
                :openAlertDialog="alertMessage.openAlertMsg"
                :msgDialog="alertMessage.msgDialog"
                @btnOK="alertMessage.openAlertMsg = false"
            />
        </v-card>
        <template>
            <JqxGrid ref="gridStaffShift" :width="'100%'" :source="dataAdapter" :columns="columns" :pageable="true" :columnsresize="true" :editable="false" @celldoubleclick="onCelldoubleclick"></JqxGrid>
        </template>
        <!-- ---------------Snacbar--------------- -->
        <v-snackbar v-model="snackbar" color="cyan darken-2" dark>
            {{ alertSnackbarMsg }}
            <template v-slot:action="{ attrs }">
                <v-btn dark text v-bind="attrs" @click="snackbar = false" small>
                        {{$t('absent.btnCancel')}}
                </v-btn>
            </template>
        </v-snackbar> 
        
    </div>
</template>

<style>
    @import "jqwidgets-scripts/jqwidgets/styles/jqx.base.css";
</style>

<script>
import store from "../store";
import moment from "moment";
import DeleteDialog from './helper_components/DeleteDialog.vue';
import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
window.downloadDocument = function(id) {
    axios
    .get(Vue.prototype.$globalConfig.ipHost + "api/download-document/" + id, {
        headers: {
            Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
        }
    })
    .then((Response) => {
        var tempElement = document.createElement("a");
        tempElement.href = Response.data;
        tempElement.download = Response.headers.filename
        tempElement.click();
    })
    .catch((error) => {
        console.log(error)
    })
};

export default{
    components: { DeleteDialog, AlertMessageDialog ,JqxGrid },
    name:'StaffShift',
    data() {
        var self = this;
        return {
            openDialog: false, 
            StaffShiftCount: "", 
            StaffShiftTitle: "",
            StaffShiftSearch: "",
            StaffShiftData: [], 
            ShiftData: [],
            employeeData: [],
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                PKID: "",
                Employees_id: "",
                ShiftHeader_PKID: "",
                StartDate: "",
                EndDate: "",
                Remark: "",
            }),
            btnLoading: false,
            dialogDelete: false,
            ChooseStartDate: false,
            ChooseEndDate: false,
            alertMessage: {
                openAlertMsg: false,
                msgDialog: "",
            },
            staffShiftForm: new Form({
                StaffShiftDataForm: [],
                Employees_id: 0,
            }),
            dataAdapter: new jqx.dataAdapter({
                localdata: [],
                datatype: 'array',
                datafields:
                [
                    { name: 'PKID', type: 'int' },
                    { name: 'CheckBox', type: 'string' },
                    { name: 'Employees_id', type: 'int' },
                    { name: 'ShiftHeader_PKID', type: 'int' },
                    { name: 'StartDate', type: 'date'},
                    { name: 'EndDate', type: 'date' },
                    { name: 'Remark', type: 'string' },
                    { name: 'Employees_Name', type: 'string' },
                    { name: 'ShiftName', type: 'string' },
                ]
            }),
            columns: [
                {
                    text: this.$t('document.tbCheckbox'), datafield: 'CheckBox', width: '3%', align: 'center', editable: true,
                    renderer: function () {
                        return '<div style="margin-left: 10px; margin-top: 12px;"><input type="checkbox" id="selectAll" onclick="selectAllChkBox(' + '\'selectCheckbox\'' + ')" /></div>';
                    },
                    cellsrenderer: function(row, columnfield, value, defaulthtml, columnproperties) {
                        return '<div style="margin-left: 10px; margin-top: 12px;"><input type="checkbox" class="selectCheckbox" onclick="selectChkBox(' + '\'selectCheckbox\'' + ')" /></div>';
                    }
                },
                {
                    text: this.$t('staffShift.tbEmployeeName'), datafield: 'Employees_Name', width: '23%', columntype: 'input', editable: false,
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('staffShift.tbShiftName'), datafield: 'ShiftName', width: '22%', columntype: 'input', editable: false,
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                { 
                    text: this.$t('staffShift.tbStartDate'),datafield: 'StartDate', width: '17%', columntype: 'datetimeinput',  align:"center", editable: false,
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                { 
                    text: this.$t('staffShift.tbEndDate'),datafield: 'EndDate', width: '17%', columntype: 'datetimeinput',  align:"center", editable: false,
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                {
                    text: this.$t('staffShift.tbRemark'), datafield: 'Remark', width: '21%', columntype: 'input', editable: false,
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
            ],
        }                 
    },
    computed: {
        userToken() {    
            return store.state.user;
        },
        DateFormated(){
            return this.form.StartDate.toString() ? moment(this.form.StartDate).format("DD/MM/YYYY") : ""; 
        },
        DateFormat(){
            return this.form.EndDate.toString() ? moment(this.form.EndDate).format("DD/MM/YYYY") : ""; 
        },
        computedDateFormattedStartDate() {
            return this.form.StartDate
            ? moment(this.form.StartDate).format("DD/MM/YYYY")
            : "";
        },
    },
    mounted() {   
        if (this.userToken.token){
            this.ReadStaffShift();
            this.ReadStaffActive();
            this.ReadShift();
        }
    },
    methods: {
        ReadShift() {
            axios
                .get(this.$globalConfig.ipHost + "api/read-shift", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ShiftData = response.data.shift;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        ReadStaffActive() {
            axios
            .get(this.$globalConfig.ipHost + "api/active-employee", {
            headers: {
                Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
            },
            })
            .then((response) => {
                this.employeeData = response.data.data;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        ReadStaffShift() {       
            axios
            .get(this.$globalConfig.ipHost + "api/read-staff-shift", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {  
                this.StaffShiftData = response.data.data;
                this.dataAdapter._source.localdata = this.StaffShiftData;
                    if(this.$refs.gridStaffShift !== undefined) {
                        this.$refs.gridStaffShift.height = this.$helper.GetGridHeight();
                        this.$refs.gridStaffShift.updatebounddata('cell');
                    }
                this.StaffShiftCount = response.data.data.length;
                this.tableLoading = false;
            })
            .catch((error) => {   
                console.log(error);
            });
        },

        createStaffShift() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form.StartDate = this.$helper.ConvertDateToStr(this.form.StartDate);
            this.form.EndDate = this.$helper.ConvertDateToStr(this.form.EndDate);
            this.form
            .post("api/create-staff-shift", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadStaffShift();
                this.closeDialog();
                this.alertSnackbarMsg = this.$t('staffShift.savedMsg');
                this.snackbar = true;
                this.btnSaveLoading = false;
                this.tableLoading = false;
            })
            .catch((errors) => {
                this.errorsMessage = errors.response.data.errors;
                this.btnSaveLoading = false;
                this.tableLoading = false;
            });
        },

        openDialogForm() {
            this.openDialog = true;
        },

        closeDialog() {
            this.editMode = false;
            this.openDialog = false;
            this.btnSaveLoading = false;
            this.form.PKID = "";
            this.form.Employees_id = "";
            this.form.ShiftHeader_PKID = "";
            this.form.StartDate = "";
            this.form.EndDate = "";
            this.form.Remark = "";
            this.errorsMessage = "";
        },

        editStaffShift(item) {
            this.editMode = true;
            this.form.PKID = item.PKID;
            this.form.Employees_id = item.Employees_id;
            this.form.ShiftHeader_PKID = item.ShiftHeader_PKID;
            this.form.StartDate = this.$helper.ConvertDateToStr(item.StartDate);
            this.form.EndDate = this.$helper.ConvertDateToStr(item.EndDate);
            this.form.Remark = item.Remark;
            this.openDialog = true;
        },

        async updateStaffShift() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form.StartDate = this.$helper.ConvertDateToStr(this.form.StartDate);
            this.form.EndDate = this.$helper.ConvertDateToStr(this.form.EndDate);
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form.post("/api/update-staff-shift/" + this.form.PKID, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadStaffShift();
                this.closeDialog();
                this.alertSnackbarMsg = this.$t('staffShift.updateMsg');
                this.snackbar = true;
                this.btnSaveLoading = false;
                this.tableLoading = false;
            })
            .catch((errors) => {
                this.errorsMessage = errors.response.data.errors;
                this.btnSaveLoading = false;
                this.tableLoading = false;
            });
        },
        onCelldoubleclick() {
            var dataItem = this.$helper.GetSelectedDataItem(this.$refs.gridStaffShift);
            this.editStaffShift(dataItem);
        },
        deleteStaffShift() {
            var staffShiftForm = new Form({
                selectedIds: [],
            });
            var staffShiftRef = this.$refs.gridStaffShift;
            var checkbox = document.getElementsByClassName('selectCheckbox');
            for(var i = 0; i < checkbox.length; i++) {
                var isChecked = checkbox[i].checked;
                if(isChecked) {
                    var rowElement = checkbox[i].parentElement.parentElement.parentElement;
                    var rowId = rowElement.getAttribute("row-id");
                    var dataItem = staffShiftRef.getrowdata(rowId);
                    staffShiftForm.selectedIds.push(dataItem.PKID);
                }
            }
            if(staffShiftForm.selectedIds.length == 0) {
                this.alertMessage.openAlertMsg = true;
                this.alertMessage.msgDialog = "Please select an item!";
                return;
            }
            staffShiftForm.post("/api/delete_staff_shift_by_ids", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadStaffShift();
            })
            .catch((errors) => {
                console.log(errors);
            });
        },
    },
}
</script>
