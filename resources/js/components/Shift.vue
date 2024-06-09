<template>
    <div id="shift">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                    <h3 class="red-lighten-3 font-weight-bold">
                        <v-icon color="grey darken-2" class="mb-1">mdi-clipboard-flow</v-icon>
                        <span class="text-decoration-underline font-weight-medium">{{$t('leaveRequest.shiftList')}}</span>
                    </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                    <v-text-field
                        append-icon="mdi-magnify"
                        single-line
                        class="txt-search"
                        v-bind:label="$t('absent.search')"
                        v-model="searchShift"
                    ></v-text-field>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" sm="6" class="text-end">
                <v-btn class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                        small
                        @click="openDialogForm">
                    <v-icon left>mdi-plus</v-icon>
                    {{$t('leaveEntry.btnAddLeaveType')}}
                </v-btn>
                <v-dialog
                    v-model="openDialog"
                    width="635"
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
                            <v-icon left color="white">mdi-plus</v-icon>
                            {{$t('leaveRequest.frmAddtime')}}
                        </span>
                        <span v-else class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-edit</v-icon>
                            {{$t('leaveRequest.frmEditTime')}}
                        </span>
                    </v-toolbar>
                    <form @submit.prevent="editMode ? updateShift() : createShift()" 
                    enctype="multipart/form-data">
                        <v-card-text>
                            <v-text-field
                                v-model="form.ShiftCode"
                                prepend-inner-icon="mdi-qrcode"
                                outlined
                                v-bind:label="$t('leaveRequest.code')"
                                class="khmer-font"
                                :error-messages="errorsMessage.ShiftCode"
                            >
                            </v-text-field>
                            <v-text-field
                                v-model="form.ShiftName"
                                prepend-inner-icon="mdi-format-list-bulleted"
                                outlined
                                v-bind:label="$t('leaveRequest.name')"
                                class="khmer-font"
                            >
                            </v-text-field>
                            <v-textarea
                                id="input-864" rows="2"
                                v-model="form.Remark"
                                prepend-inner-icon="mdi-pencil"
                                v-bind:label="$t('leaveEntry.tbRemark')"
                                class="khmer-font"
                                outlined
                            ></v-textarea>
                            <!-- Modified by Huychoung 08/09/2023 #88 -->
                            <v-row>
                                <v-col cols="10">
                                    <span class="red--text font-weight-bold khmer-font" 
                                        v-if="chDateTime.openError"
                                        small
                                    >
                                        {{chDateTime.ErrorMsg}}
                                    </span>
                                    <span 
                                        class="khmer-font font-size-small blue-grey--text" 
                                        v-else
                                        small
                                    >
                                        {{this.$t('shift.labelDateTime')}}
                                    </span>
                                </v-col>
                            <!-- form Shift Detail -->
                                <v-col cols="10">
                                    <v-btn 
                                    class="mb-1"
                                        small
                                        depressed
                                        left
                                        color="khmer-font khawin-background-color"
                                        @click="$helper.AddDetailRow(
                                            $refs.gridDetail, 
                                                {
                                                    PKID: 0,
                                                    Section: '',
                                                    TimeIn: '',
                                                    TimeOut: '',
                                                    Remark: '',
                                                }
                                            )"
                                        :disabled="isDisabledButton"
                                        >
                                        <v-icon class="white--text">mdi-plus</v-icon>
                                    </v-btn>
                                    <v-btn
                                    class="mb-1"
                                        small
                                        depressed
                                        left
                                        color="red"
                                        @click="$helper.DeleteDetailRow($refs.gridDetail)"
                                        >
                                        <v-icon class="white--text">mdi-delete</v-icon>
                                    </v-btn>
                                </v-col>
                                <v-spacer></v-spacer>
                            </v-row>
                            <template>
                                <JqxGrid
                                    ref="gridDetail" :width="600" :source="dataAdapter" :columns="columns" :autoheight="true" :editable="true">
                                </JqxGrid>
                            </template>
                            <v-divider></v-divider>
                            
                            </v-card-text>
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
        <!-- table -->
        <v-card class="mx-auto table-card v-card v-sheet theme--ligh">
            <v-data-table
                :headers="shiftHeader"
                :items="tableShiftHeader"
                :search="searchShift"
                :loading="tableLoading"
            >
                <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="editShift(item)">mdi-pencil</v-icon>
                    <v-icon small class="mr-2" @click="deleteShift(item.PKID, item.ShiftCode)">mdi-delete</v-icon>
                </template>
            </v-data-table>
            <!-- ---------------Snacbar--------------- -->
            <v-snackbar v-model="snackbar" color="cyan darken-2" dark>
                {{ alertSnackbarMsg }}
                <template v-slot:action="{ attrs }">
                    <v-btn dark text v-bind="attrs" @click="snackbar = false" small>
                        {{$t('absent.btnCancel')}}
                    </v-btn>
                </template>
            </v-snackbar>
            <!-- -------------dialogDelete------------ -->
            <DeleteDialog
                :openDeleteDialog="dialogDelete"
                @deleteItem="submitDelete"
                @cancelDialog="dialogDelete = false"
                :itemTitle="shiftTitle"
            />
            <DeleteDialog
                :openDeleteDialog="openDeleteDialog"
                @cancelDialog="openDeleteDialog = false"
            />
            <AlertMessageDialog
                :openAlertDialog="alertMessage.openAlertMsg"
                :msgDialog="alertMessage.msgDialog"
                @btnOK="alertMessage.openAlertMsg = false"
            />
        </v-card>
    </div>
</template>
<style>
    @import "jqwidgets-scripts/jqwidgets/styles/jqx.base.css";
    span[id^="jqxWidget"] 
    {
        display: none !important;
    }
    .vue__time-picker-dropdown {
        z-index: 5000;
    }
</style>
<script>
import store from '../store';
import VueTimepicker from 'vue2-timepicker';
import 'vue2-timepicker/dist/VueTimepicker.css';
import DeleteDialog from './helper_components/DeleteDialog.vue';
import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
    export default {
        name: 'Shift',
        components: { DeleteDialog, VueTimepicker, AlertMessageDialog, JqxGrid },
        data(){
            var self = this;
            return {
            openDialog: false,
            openDetail: false,
            tableShiftHeader: [],
            shiftTitle: "",
            searchShift: "",
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            btnLoading: false,
            dialogDelete: false,
            form: new Form({
                PKID: "",
                ShiftCode: "",
                ShiftName: null,
                Remark: null,
                shiftdetailFrom: [],
            }),
            editedIndex: -1,
            editedItem: {
                PKID: 0,
                Section: "",
                TimeIn: "",
                TimeOut: "",
                Remark: "",
            },
            defaultItem: {
                PKID: 0,
                Section: "",
                TimeIn: "",
                TimeOut: "",
                Remark: "",
            },
            isDisabledButton: false,
            selectedIndex: 0,
            openDeleteDialog: false,
            userData:[],
            openDeleteEditableDialog: false,
            alertMessage: {
                openAlertMsg: false,
                msgDialog: "",
            },
            // Modified by Huychoung 08/09/2023 #88
            chDateTime:{
                ErrorMsg: '',
                openError: false,
            },
            dataAdapter: new jqx.dataAdapter({
                localdata: [],
                datatype: 'array',
                datafields:
                [
                    {name: 'PKID', type: 'int'},
                    {name: 'Section', type: 'string'},
                    {name: 'TimeIn', type: 'time'},
                    {name: 'TimeOut', type: 'time'},
                    {name: 'Remark', type: 'string'},
                ]
            }),
            columns: [
                {
                    text: this.$t('leaveRequest.section'), datafield: 'Section', width: 150, columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                { 
                    text: this.$t('leaveRequest.timeIn'),datafield: 'TimeIn', width: 150, columntype: 'datetimeinput',
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToTime(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor, celltext, cellwidth, cellheight) {
                        return editor.jqxDateTimeInput({
                            formatString: 'hh:mm tt',
                            showTimeButton: true,
                            showCalendarButton: false,
                            dropDownVerticalAlignment: 'top',
                        });
                    }
                },
                { 
                    text: this.$t('leaveRequest.timeOut'),datafield: 'TimeOut', width: 150, columntype: 'datetimeinput',
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToTime(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor, celltext, cellwidth, cellheight) {
                        return editor.jqxDateTimeInput({
                            formatString: 'hh:mm tt',
                            showTimeButton: true,
                            showCalendarButton: false,
                            dropDownVerticalAlignment: 'top',
                        });
                    }
                },
                {
                    text: this.$t('leaveEntry.tbRemark'), datafield: 'Remark', width: 150, columntype: 'input',
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
            shiftDetail(){
                return [
                {text: this.$t('leaveRequest.section'),value:'Section', width: '20%'},
                {text: this.$t('leaveRequest.timeIn'),value:'TimeIn', width: '20%'},
                {text: this.$t('leaveRequest.timeOut'),value:'TimeOut', width: '20%'},
                {text: this.$t('leaveEntry.tbRemark'),value:'Remark', width: '20%'},
                {text: this.$t('leaveEntry.tbAction'), value: "actions", align: "center", sortable: false, width: "23%"},
                ]
            },
            shiftHeader(){
                return[
                    {text:this.$t('leaveRequest.code'),value:'ShiftCode'},
                    {text:this.$t('leaveRequest.name'),value:'ShiftName'},
                    {text:this.$t('leaveEntry.tbRemark'),value:'Remark'},
                    {text:this.$t('leaveEntry.tbAction'), value: "actions", align: "center", sortable: false},
                ]
            }
        },
        mounted() {
            if(this.userToken.token){
                this.ReadShift();
            }
        },
        methods: {
            ReadShift() {
                axios
                    .get(this.$globalConfig.ipHost + "api/read-shift-header", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.tableShiftHeader = response.data.data;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createShift() {
                // this.btnSaveLoading = true;
                this.tableLoading = true;
                this.form.shiftdetailFrom = this.dataAdapter.records;
                for(var i = 0; i < this.form.shiftdetailFrom.length; i++) {
                    this.form.shiftdetailFrom[i].TimeIn = this.$helper.ConvertDateToTime(this.form.shiftdetailFrom[i].TimeIn);
                    this.form.shiftdetailFrom[i].TimeOut = this.$helper.ConvertDateToTime(this.form.shiftdetailFrom[i].TimeOut);
                }
                this.form
                .post("api/create-shift", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadShift();
                    this.closeDialog();
                    this.alertSnackbarMsg = this.$t('leaveEntry.msgSaved');
                    this.snackbar = true;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                })
                .catch((errors) => {
                    this.errorsMessage = errors.response.data.errors;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                    //Modified by Huychoung 08/09/2023 #88
                    if(this.errorsMessage.shiftdetailFrom){
                        this.chDateTime.openError = true
                        this.chDateTime.ErrorMsg = this.$t('shift.msgEmpity')
                    }
                    else{
                        this.chDateTime.openError = false
                    }
                    if(this.errorsMessage.Section){
                        if(this.errorsMessage.Section == "Data is not empty.")
                        {
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('shift.msgEmpity')
                        }
                        else if(this.errorsMessage.Section == "Data is already exist."){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('shift.msgExist')
                        }else if(this.errorsMessage.Section == "Data is duplicate."){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('shift.msgDuplicate')
                        }
                    }
                });
            },
            openDialogForm() {
                this.openDialog = true;
                this.form.shiftdetailFrom = [];
            },
            closeDialog() {
                this.editMode = false;
                this.openDialog = false;
                this.btnSaveLoading = false;
                this.form.PKID = "";
                this.form.ShiftCode = "";
                this.form.ShiftName = null;
                this.form.Remark = null;
                this.form.shiftdetailFrom = [];
                this.errorsMessage = "";
                this.chDateTime.openError = false;
                this.dataAdapter._source.localdata = [];
                if(this.$refs.gridDetail !== undefined) {
                    this.$refs.gridDetail.updatebounddata('cell');
                }
            },
            editShift(item) {
                this.editMode = true;
                this.openDialog = true;
                this.form.PKID = item.PKID;
                this.form.ShiftCode = item.ShiftCode;
                this.form.ShiftName = item.ShiftName;
                this.form.Remark = item.Remark;
                this.dataAdapter._source.localdata = item.ShiftDetail;
                if(this.$refs.gridDetail !== undefined) {
                    this.$refs.gridDetail.updatebounddata('cell');
                }
                var selectedPKID = this.form.PKID;
                var filterShiftdetail = this.tableShiftHeader.filter(function(obj) {
                    return obj.PKID == selectedPKID;
                });
                this.form.shiftdetailFrom = filterShiftdetail.length > 0 ? filterShiftdetail[0].ShiftDetail : [];
            },
            deleteShift(PKID, ShiftCode) {
                this.form.PKID = PKID;
                this.shiftTitle = ShiftCode;
                this.dialogDelete = true;
            },
            async updateShift() {
                // this.btnSaveLoading = true;
                this.tableLoading = true;
                this.form.shiftdetailFrom = this.dataAdapter.records;
                for(var i = 0; i < this.form.shiftdetailFrom.length; i++) {
                    this.form.shiftdetailFrom[i].TimeIn = this.$helper.ConvertDateToTime(this.form.shiftdetailFrom[i].TimeIn);
                    this.form.shiftdetailFrom[i].TimeOut = this.$helper.ConvertDateToTime(this.form.shiftdetailFrom[i].TimeOut);
                }
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-shift/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {                                                                       
                    this.ReadShift();
                    this.closeDialog();
                    this.alertSnackbarMsg = this.$t('leaveEntry.msgUpdate');
                    this.snackbar = true;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                })
                .catch((errors) => {
                    this.errorsMessage = errors.response.data.errors;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                    //Modified by Huychoung 08/09/2023 #88
                    if(this.errorsMessage.Section){
                        if(this.errorsMessage.Section == "Data is not empty.")
                        {
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('shift.msgEmpity')
                        }
                        else if(this.errorsMessage.Section == "Data is already exist."){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('shift.msgExist')
                        }else if(this.errorsMessage.Section == "Data is duplicate."){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('shift.msgDuplicate')
                        }
                    }
                    else{
                        this.chDateTime.openError = false
                    }
                    if(this.errorMessage.shiftdetailFrom !== undefined){
                        this.chDateTime.openError = true
                        this.chDateTime.ErrorMsg = this.$t('shift.msgEmpity')
                    }
                    else{
                        this.chDateTime.openError = false
                    }
                });
            },
            async submitDelete() {
                this.btnLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                axios
                .delete("/api/delete-shift/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadShift();
                    this.dialogDelete = false;
                    this.alertSnackbarMsg = this.$t('leaveEntry.msgDelete');
                    this.snackbar = true;
                    this.btnLoading = false;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    this.btnLoading = false;
                    this.tableLoading = false;
                });
            },
        }
    }
</script>