<template>
    <div id="Apply_Leave">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                        <h3 class="grey--text text--darken-2">
                            <v-icon color="grey darken-2">mdi-account-group-outline</v-icon>
                            <span class="text-decoration-underline font-weight-medium">
                                {{$t('absent.listTitle')}}
                            </span>
                            <v-chip color="grey grey--text text--darken-3 lighten-2">{{ count }}</v-chip>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                        <v-text-field
                            v-model="searchApplyLeave"
                            single-line
                            class="txt-search"
                            append-icon="mdi-magnify"
                            v-bind:label="$t('absent.search')"
                        ></v-text-field>

                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" sm="6" class="text-end">
                <v-btn
                    small
                    class="font-weight-regular pa-2 khawin-background-color white--text"
                    @click="openDialog=true"
                >
                    <v-icon left>mdi-plus</v-icon>
                    {{$t('absent.btnApplyLeave')}}
                </v-btn>
            </v-col>
        </v-row>
        <v-data-table
            :headers="header"
            :items="ApplyLeaveData"
            :loading="tableLoading"
            :search="searchApplyLeave"
        >
            <template
                v-slot:[`item.actions`]="{ item }" 
            >
                <v-icon 
                    small 
                    @click="editApplyleave(item)"
                >
                    mdi-pencil
                </v-icon>
                <v-icon 
                    small
                    @click="deleteApplyLeave(item.PKID, item.Staff)"
                    :disabled="item.StatusID == 0 ? false : true"
                >
                    mdi-delete
                </v-icon>
            </template>
        </v-data-table>
        <v-dialog
            width="500"
            v-model="openDialog"
            persistent
        >
            <v-card>
                <v-toolbar
                    dense
                    flat
                    color="khawin-background-color"
                >
                    <span class="khmer-font white--text">
                        <v-icon color="white" left>mdi-account-group-outline</v-icon>
                        {{$t('menu.ApplyLeave')}}
                    </span>
                </v-toolbar>
                <form
                    @submit.prevent=" editMode ? updateApplyLeave() : createApplyLeave()"
                    enctype="multipart/form-data" 
                >
                    <v-card-text>
                        <v-text-field
                            v-model="form.StaffName"
                            class="khmer-font"
                            prepend-icon="mdi-account"
                            v-bind:label="$t('absent.tbEmp')"
                            :error-messages="errorMessage.StaffPKID"
                            disabled
                        >
                        </v-text-field>
                        <v-autocomplete
                                v-model="form.ParameterPKID"
                                :items="ParameterData"
                                :item-text="(item) => item.ValidationType"
                                item-value="PKID"
                                class="khmer-font"
                                clearable
                                v-bind:label="$t('parameters.txtValidation')"
                                color="cyan darken-1"
                                prepend-icon="mdi-text-box-edit"
                                :error-messages="errorMessage.ParameterPKID"
                                :disabled="isDisabledButton"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        class="text-capitalize pa-1 font-weight-regular"
                                        small
                                        :disabled="isDisabledButton"
                                    >
                                        {{ data.item.ValidationType }}
                                    </v-chip>
                                </template>
                            </v-autocomplete>
                            <v-autocomplete
                                v-model="form.LeaveReasonPKID"
                                :items="leaveReasonData"
                                :item-text="(item) => item.LeaveReason"
                                item-value="PK_ID"
                                class="khmer-font"
                                clearable
                                v-bind:label="$t('absent.reason')"
                                color="cyan darken-1"
                                prepend-icon="mdi-text-box-edit"
                                :error-messages="errorMessage.LeaveReasonPKID"
                                :disabled="isDisabledButton"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        class="text-capitalize pa-1 font-weight-regular"
                                        small
                                        :disabled="isDisabledButton"
                                    >
                                        {{ data.item.LeaveReason }}
                                    </v-chip>
                                </template>
                            </v-autocomplete>
                            <v-autocomplete
                                v-model="form.LeaveTypePKID"
                                :items="leaveTypeData"
                                :item-text="(item) => item.LeaveType"
                                item-value="PK_ID"
                                class="khmer-font"
                                clearable
                                v-bind:label="$t('absent.type')"
                                color="cyan darken-1"
                                prepend-icon="mdi-thermostat-auto"
                                :error-messages="errorMessage.LeaveTypePKID"
                                :disabled="isDisabledButton"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        class="text-capitalize pa-1 font-weight-regular"
                                        small
                                        :disabled="isDisabledButton"
                                    >
                                        {{ data.item.LeaveType }}
                                    </v-chip>
                                </template>
                            </v-autocomplete>
                            <v-textarea
                                v-model="form.Remark"
                                prepend-inner-icon="mdi-pencil"
                                class="khmer-font"
                                v-bind:label="$t('absent.remark')"
                                rows="1"
                                outlined
                                :error-messages="errorMessage.Remark"
                                :disabled="isDisabledButton"
                            ></v-textarea>
                            
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
                                        {{this.$t('applyLeave.labelDateTime')}}
                                    </span>
                                </v-col>
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
                                                        AttendanceDate: '',
                                                        TimeInTimeOutCode: '',
                                                        TimeInTimeOutId: 0,
                                                    }
                                                )"
                                        :disabled="isDisabledButton"
                                    >
                                        <v-icon class="white--text" small>mdi-plus</v-icon>
                                    </v-btn>
                                    <v-btn
                                        class="mb-1"
                                        small
                                        depressed
                                        left
                                        color="red"
                                        @click="$helper.DeleteDetailRow($refs.gridDetail)"
                                    >
                                        <v-icon class="white--text" small>mdi-delete</v-icon>
                                    </v-btn>
                                </v-col>
                            </v-row>
                            <template>
                                <JqxGrid 
                                    ref="gridDetail" :width="460" :source="dataAdapter" :columns="columns" :autoheight="true" :editable="true">
                                </JqxGrid>
                            </template>
                        <v-divider></v-divider>
                    </v-card-text>
                    <v-card-actions class="khmer-font">
                    <v-spacer></v-spacer>
                    <v-btn 
                        color="grey lighten-2" 
                        small 
                        depressed
                        @click="closeDialog">{{$t('absent.btnCancel')}}</v-btn>
                    <v-btn
                        small
                        class="khmer-font white--text"
                        color="pink"
                        depressed
                        type="submit"
                        @click="form.isSubmit = false"
                        :disabled="isDisabledButton"
                        :loading="btnSaveLoading"
                        ref="save"
                    >
                        {{$t('absent.btnSave')}}
                    </v-btn>
                    <v-btn 
                        color="khawin-background-color"
                        class="khmer-font white--text"
                        small
                        type="submit"
                        @click="form.isSubmit = true"
                        :disabled="isDisabledButton"
                        ref="submit"
                    >
                        {{$t('absent.btnSubmit')}}
                    </v-btn>
                    </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
        <v-snackbar
            v-model="snackBar"
            color="cyan darken-2"
            dark
        >
            <template v-slot:action="{ attrs }">
                {{ alertSnackBarMsg }}
                <v-btn
                    dark
                    text
                    v-bind="attrs"
                    small
                    @click="snackBar = false"
                >
                    {{$t('absent.btnCancel')}}
                </v-btn>
            </template>
        </v-snackbar>

        <!-- ----------dialogDelete------------ -->
        <v-dialog v-model="dialogDelete" max-width="330px">
            <v-card>
                <div class="text-center">
                    <v-sheet class="px-7 pt-7 pb-4 mx-auto text-center d-inline-block">
                        <v-icon class="text-center pb-3" x-large color="red lighten-2">mdi-alert</v-icon>
                        <div class="grey--text text--darken-3 text-body-2 mb-4">
                            {{$t('workflow.deleteMessage')}}
                            <b class="red--text tex--lighten-2">{{ staffNameDialog }}</b>
                            ?
                        </div>

                        <v-btn :disabled="btnLoading" class="ma-1 khmer-font" depressed small @click="dialogDelete = false">
                            {{$t('absent.btnCancel')}}
                        </v-btn>

                        <v-btn 
                            :loading="btnLoading" 
                            class="ma-1 khmer-font" 
                            dark 
                            color="red" 
                            small 
                            depressed
                            @click="submitDelete"
                        >
                            {{$t('absent.btnDelete')}}
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>
        <AlertMessageDialog
            :openAlertDialog="alertMessage.openAlertMsg"
            :msgDialog="alertMessage.msgDialog"
            @btnOK="alertMessage.openAlertMsg = false"
        />
    </div>
</template>
<style>
    @import "jqwidgets-scripts/jqwidgets/styles/jqx.base.css";
    span[id^="jqxWidget"] 
    {
        display: none !important;
    }
</style>
<script>
    import moment from 'moment';
    import store from '../store';
    import DeleteDialog from './helper_components/DeleteDialog.vue';
    import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
    import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
    export default {
        components: { DeleteDialog, AlertMessageDialog, JqxGrid },
        name:'ApplyLeave',
        data() {
            var self = this;
            return {
                openDialog:false,
                DataForm:[],
                ApplyLeaveData:[],
                count: "",
                tableLoading:false,
                btnSaveLoading: false,
                editMode: false,
                errorMessage: "",
                alertSnackBarMsg: "",
                snackBar: false,
                form: new Form({
                    PKID: "",
                    Remark:"",
                    StaffPKID:"",
                    StaffName:"",
                    ShiftHeaderPKID: 0,
                    LeaveReasonPKID:"",
                    LeaveTypePKID:"",
                    DataForm: [],
                    isSubmit: false,
                    ParameterPKID: "",
                }),
                leaveReasonData: [],
                leaveTypeData: [],
                staffNameDialog: "",
                dialogDelete: false,
                btnLoading: false,
                searchApplyLeave: "",
                editedIndex: -1,
                selectRow: 0,
                addItemObj: {
                    AttendanceDate: "",
                    TimeInTimeOutId: 0,
                    TimeInTimeOutCode: "",
                    PKID: 0,
                    order: 0,
                },
                defaultItem: {
                    AttendanceDate: "",
                    TimeInTimeOutId: 0,
                    TimeInTimeOutCode: "",
                    PKID: 0,
                    order: 0,
                },
                ShidtDetailData: [],
                ChoseDateAbsent: false,
                TimeInTimeOutArr: [],
                alertMessage: {
                    openAlertMsg: false,
                    msgDialog: "",
                },
                isDisabledButton: false,
                chDateTime:{
                    ErrorMsg: '',
                    openError: false,
                },
                ParameterData:[],
                dataAdapter: new jqx.dataAdapter({
                    localdata: [],
                    datatype: 'array',
                    datafields:
                    [
                        { name: 'PKID', type: 'int' },
                        { name: 'AttendanceDate', type: 'date' },
                        { name: 'TimeInTimeOutCode', type: 'string' },
                        { name: 'TimeInTimeOutId', type: 'int' },
                    ]
                }),
                columns: [
                    { 
                        text: this.$t('absent.attendance'), datafield: 'AttendanceDate', width: 230, columntype: 'datetimeinput',
                        renderer: function (row, column, value) {
                            return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
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
                        text: this.$t('absent.absentTime'), datafield: 'TimeInTimeOutCode', width: 230, columntype: 'dropdownlist',
                        renderer: function (row, column, value) {
                            return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                        },
                        createeditor: function (row, value, editor) {
                            editor[0].onselect = function(e) {
                                var selectedRowIndex = self.$refs.gridDetail.getselectedrowindex();
                                var selectedRowData = self.$refs.gridDetail.getrowdatabyid(selectedRowIndex);
                                selectedRowData.TimeInTimeOutId = e.args.item.originalItem.PKID;
                            };
                            return editor.jqxDropDownList({ 
                                autoDropDownHeight: true, source: self.TimeInTimeOutArr, displayMember: 'ShiftDetail', valueMember: 'ShiftDetailPKID', 
                            });
                        }
                    },
                ]
            }
        },
        computed:{
            userToken() {
                return store.state.user;
            },
            DateFormat(){
                return this.addItemObj.AttendanceDate ? moment(this.addItemObj.AttendanceDate).format("DD/MM/YYYY") : ""; 
            },
            header(){
                return [
                    {text: this.$t('leaveEntry.tbEmpName'), value:'Staff'},
                    {text: this.$t('leaveEntry.tbReason'), value:'LeaveReason'},
                    {text: this.$t('leaveEntry.tbType'), value:'LeaveType'},
                    {text: this.$t('leaveEntry.tbStatus'), value:'Status',},
                    {text: this.$t('leaveEntry.tbRemark'), value:'Remark'},
                    {text: this.$t('leaveEntry.tbAction'), value:'actions',sortable:false, align:"center"},
                ]
            },
            HeaderForm(){
                return [
                    {text: this.$t('leaveEntry.tbAbsent'), value:'AttendanceDate'},
                    {text: this.$t('leaveEntry.tbTime'), value:'TimeInTimeOutCode'},
                    {text: this.$t('leaveEntry.tbAction'), value:'actions',sortable:false, align:"center"},
                ]
            }
        },
        mounted(){
            if(!Array.isArray(this.userToken.employee) && this.userToken.employee !== undefined) {
                this.form.StaffPKID = this.userToken.employee.id;
                this.form.StaffName = this.userToken.employee.first_name + " " + this.userToken.employee.last_name;
                this.form.ShiftHeaderPKID = this.userToken.employee.id;
            } else {
                this.form.StaffPKID = 0;
                this.form.ShiftHeaderPKID = 0;
                this.form.StaffName = "";
            }
            if(this.userToken.token){
                this.ReadApplyLeave();
                this.ReadLeaveReason();
                this.ReadLeaveType();
                this.ReadShiftDetail();
                this.ReadParameter();
            }
        },
        methods:{
            ReadParameter(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-validationtype",{
                    headers: {Authorization: "Bearer " + sessionStorage.getItem("TOKEN")}
                })
                .then((response) => {
                    this.ParameterData = response.data.data
                })
                .catch((error) => {
                    console.log(error)
                })
            },
            ReadShiftDetail(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-shift-employee/" + this.form.ShiftHeaderPKID,{
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((respone) => {
                    this.TimeInTimeOutArr = respone.data.data
                })
                .catch((error) => {
                    console.log(error)
                })
            },
            ReadApplyLeave(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-apply-leave/" + this.$i18n.locale, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((Response) => {
                    this.ApplyLeaveData = Response.data.data
                    this.count = Response.data.data.length
                    })
                .catch((error) => {
                    console.log(error)
                })
            },
            createApplyLeave(){
                this.tableLoading = true
                // this.btnSaveLoading = true
                this.form.DataForm = this.dataAdapter.records;
                for(var i = 0; i < this.form.DataForm.length; i++) {
                    this.form.DataForm[i].AttendanceDate = this.$helper.ConvertDateToStr(this.form.DataForm[i].AttendanceDate);
                }
                this.form
                .post("api/create-apply-leave-save-or-submit",{
                    headers:{ Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
                })
                .then((response) =>{
                    this.ReadApplyLeave()
                    this.closeDialog()
                    this.alertSnackBarMsg = this.$t('leaveEntry.msgSaved')
                    this.snackBar = true
                    this.btnSaveLoading = false
                    this.tableLoading = false
                })
                .catch((errors) =>{
                    this.btnSaveLoading = false
                    this.tableLoading = false
                    this.errorMessage = errors.response.data.errors
                    //Modify by Theara 07/07/23 #CMS-77
                    if(this.errorMessage.DataForm){
                        this.chDateTime.openError = true
                        this.chDateTime.ErrorMsg = this.$t('applyLeave.msgEmpity')
                    }
                    else{
                        this.chDateTime.openError = false
                    }
                    if(this.errorMessage.AttendanceDate){
                        if(this.errorMessage.AttendanceDate == "Data is not empty.")
                        {
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('applyLeave.msgEmpity')
                        }
                        else if(this.errorMessage.AttendanceDate == "Data is already exist."){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('applyLeave.msgExist')
                        }else if(this.errorMessage.AttendanceDate == "Data is duplicate."){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('applyLeave.msgDuplicate')
                        }
                    }
                })
            },
            closeDialog(){
                this.editMode = false
                this.btnSaveLoading = false
                this.openDialog = false
                this.form.PKID = 0;
                this.form.StartDate = "";
                this.form.EndDate = "";
                this.form.Remark = "";
                this.form.LeaveReasonPKID = "";
                this.form.LeaveTypePKID = "";
                this.form.ApplyLeaveData = [];
                this.form.DataForm = [];
                this.errorMessage = "";
                this.isSubmit= false;
                this.isDisabledButton = false;
                this.form.ParameterPKID = "";
                this.chDateTime.openError = false;
                this.dataAdapter._source.localdata = [];
                if(this.$refs.gridDetail !== undefined) {
                    this.$refs.gridDetail.updatebounddata('cell');
                }
            },
            ReadLeaveReason() {
                axios
                    .get(this.$globalConfig.ipHost + "api/read-leave-reason", {
                        headers: {
                            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                        },
                    })
                .then((response) => {
                    this.leaveReasonData = response.data.data;
                })
                .catch((error) => {   
                    console.log(error);
                });
            },
            ReadLeaveType() {
                axios
                .get(this.$globalConfig.ipHost + "api/read-leave-type", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.leaveTypeData = response.data.data;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            editApplyleave(item) {
                this.editMode = true
                this.form.PKID = item.PKID;
                this.form.Remark = item.Remark;
                this.form.StaffPKID = item.StaffPKID;
                this.form.ParameterPKID = item.ValidationTypeID;
                this.form.LeaveReasonPKID = item.LeaveReason != null ? item.LeaveReasonPKID : 0;
                this.form.LeaveTypePKID = item.LeaveType != null ? item.LeaveTypePKID : 0;
                this.dataAdapter._source.localdata = item.editTableArr;
                if(this.$refs.gridDetail !== undefined) {
                    this.$refs.gridDetail.updatebounddata('cell');
                }
                this.isDisabledButton = item.StatusID == 0 ? false : true;
                this.openDialog = true
            },
            async updateApplyLeave() {
                // this.btnSaveLoading = true;
                this.tableLoading = true;
                this.form.DataForm = this.dataAdapter.records;
                for(var i = 0; i < this.form.DataForm.length; i++) {
                    this.form.DataForm[i].AttendanceDate = this.$helper.ConvertDateToStr(this.form.DataForm[i].AttendanceDate);
                }
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-apply-leave-save-or-submit/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadApplyLeave();
                    this.closeDialog();
                    this.alertSnackBarMsg = this.$t('applyLeave.msgSubmit')
                    this.snackBar = true;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                })
                .catch((errors) => {
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                    this.errorMessage = errors.response.data.errors
                    //Modify by Theara 07/07/23 #CMS-77
                    if(this.errorMessage.AttendanceDate){
                        if(this.errorMessage.AttendanceDate == "Data is not empty.")
                        {
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('applyLeave.msgEmpity')
                        }
                        else if(this.errorMessage.AttendanceDate == "Data is already exist."){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('applyLeave.msgExist')
                        }else if(this.errorMessage.AttendanceDate == "Data is duplicate."){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('applyLeave.msgDuplicate')
                        }
                    }
                    if(this.errorMessage.DataForm !== undefined){
                        this.chDateTime.openError = true
                        this.chDateTime.ErrorMsg = this.$t('applyLeave.msgEmpity')
                    }
                    else{
                        this.chDateTime.openError = false
                    }
                });
            },
            deleteApplyLeave(id, staffName) {
                this.form.PKID = id;
                this.staffNameDialog = staffName;
                this.dialogDelete = true;
            },
            async submitDelete() {
                this.btnLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                axios
                    .delete("/api/delete-apply-leave/" + this.form.PKID, {
                        headers: {
                            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                        },
                    })
                    .then((response) => {
                        this.ReadApplyLeave();
                        this.dialogDelete = false;
                        this.alertSnackBarMsg = this.$t('leaveEntry.msgDelete')
                        this.snackBar = true;
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
