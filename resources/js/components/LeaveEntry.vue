<template>
    <div id="leaveEntry">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                        <h3 class="grey--text text--darken-2">
                            <v-icon color="grey darken-2">mdi-account-group-outline</v-icon>
                            <span class="text-decoration-underline font-weight-medium">{{$t('leaveEntry.listLeaveEntry')}}</span>
                            <v-chip color="grey grey--text text--darken-3 lighten-2">{{ count }}</v-chip>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                        <v-text-field
                            v-model="searchLeaveEntry"
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
                    @click="openBtnDialog=true"
                >
                    {{$t('leaveEntry.btnAdd')}}
                </v-btn>
                <v-btn
                    small
                    class="font-weight-regular pa-2 khawin-background-color white--text"
                    @click="openDialog=true"
                >
                    <v-icon left>mdi-plus</v-icon>
                    {{$t('workflow.btnAdd')}}
                </v-btn>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" sm="2">
                <v-card
                    class="mx-auto"
                    max-width="300"
                >
                    
                    <v-list-item class="font-weight-regular khawin-background-color white-text-label" >
                        <v-checkbox
                            class="cms-font-size-attendance"
                            v-bind:label="$t('leaveEntry.filterByDate')"
                            v-model="FilterByDate"
                            @click="ReadLeaveEntry">
                        </v-checkbox>  
                    </v-list-item>
                        
                    <v-list-item>
                            <v-menu
                                :close-on-content-click="false"
                                max-width="290"
                                v-model="ChooseStartDate"
                                
                            >
                                <template
                                    v-slot:activator="{ on, attrs }"
                                >
                                    <v-text-field
                                        :value="StartDateFormat"
                                        v-on="on"
                                        v-bind="attrs"
                                        class="khmer-font mt-0"
                                        color="cyan darken-1"
                                        readonly
                                        prepend-inner-icon="mdi-calendar"
                                        v-bind:label="$t('leaveEntry.startDate')"
                                    ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        @input="ChooseStartDate = false"
                                        color="cyan darken-1"
                                        v-model="addItem.StartDate"
                                    >
                                    </v-date-picker>
                                </v-menu>
                        </v-list-item>

                        <v-list-item>
                                <v-menu
                                :close-on-content-click="false"
                                max-width="290"
                                v-model="ChooseEndDate"
                            >
                                <template
                                    v-slot:activator="{ on, attrs }"
                                >
                                    <v-text-field
                                        :value="EndDateFormat"
                                        v-on="on"
                                        v-bind="attrs"
                                        class="khmer-font mt-0"
                                        color="cyan darken-1"
                                        readonly
                                        prepend-inner-icon="mdi-calendar"
                                        v-bind:label="$t('leaveEntry.endDate')"
                                    ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        @input="ChooseEndDate = false"
                                        color="cyan darken-1"
                                        v-model="addItem.EndDate"
                                    >
                                    </v-date-picker>
                                </v-menu>
                            </v-list-item>
                        <v-list-item class="font-weight-regular khawin-background-color ">
                            <span class="white--text cms-font-size-attendance">
                                {{$t('leaveEntry.filterByType')}}
                            </span>
                        </v-list-item>
                            <v-list-item
                                v-for="(item, index) in ValidationTypeData"
                                :key="index"
                            >
                                <v-checkbox
                                    class="my-0"
                                    :label="item.ValidationType"
                                    :value="item.PKID"
                                    v-model="SearchByValidation"
                                    @click="ReadLeaveEntry"                              
                                >
                                </v-checkbox>
                            </v-list-item>
                        <v-list-item class="font-weight-regular khawin-background-color">
                            <span class="white--text cms-font-size-attendance">
                                {{$t('leaveEntry.quickFilter')}}
                            </span>
                        </v-list-item>
                            <v-list-item>
                                <v-radio-group  class="cms-font-size-attendance" v-model="SearchByDate"
                                >
                                    <v-radio v-bind:label="$t('leaveEntry.today')" v-bind:value="1" @click="ReadLeaveEntry"></v-radio>
                                    <v-radio v-bind:label="$t('leaveEntry.weekly')" v-bind:value="2" @click="ReadLeaveEntry"></v-radio>
                                    <v-radio v-bind:label="$t('leaveEntry.monthly')" v-bind:value="3" @click="ReadLeaveEntry"></v-radio>
                                    <v-radio v-bind:label="$t('leaveEntry.yearly')" v-bind:value="4" @click="ReadLeaveEntry"></v-radio>
                                </v-radio-group>
                            </v-list-item>
                            <v-btn
                                class="font-weight-regular mb-2 pa-2 khawin-background-color white--text"
                                small
                                width="100%"
                            >
                                <v-icon left>mdi-magnify</v-icon>
                                {{$t('leaveEntry.search')}}
                            </v-btn>
                </v-card>
            </v-col>
            <v-col cols="12" sm="10">
                <v-data-table
                    :headers="header"
                    :items="LeaveEntryData"
                    :loading="tableLoading"
                    :search="searchLeaveEntry"
                >
                    <template
                        v-slot:[`item.actions`]="{ item }" 
                    >
                        <v-icon 
                            small 
                            class="mr-2" 
                            @click="editLeaveEntry(item)" 
                            :disabled="item.StatusId > 0 ? true : false"
                        >
                            mdi-pencil
                        </v-icon>
                        <v-icon small class="mr-2" @click="deleteLeaveEntry(item.PKID, item.Staff)">mdi-delete</v-icon>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>

        <!-- modified by huychoung cms #100 12/10/2023 -->

        <v-dialog
            width="800"
            v-model="openBtnDialog"
            persistent
        >
           <v-card>
            <v-toolbar
                dense
                flat
                color="khawin-background-color"
            >
                <span class="khmer-font white--text" v-if="editMode === false">
                    <v-icon color="white" left>mdi-account-group-outline</v-icon>
                    {{$t('leaveEntry.frmAdd')}}
                </span>
                <span class="khmer-font white--text" v-else>
                    <v-icon color="white" left>mdi-account-group-outline</v-icon>
                    {{$t('leaveEntry.frmEdit')}}
                </span>
            </v-toolbar>

                <form>
                    <v-card-text>
                        <v-autocomplete
                                v-model="form.DepartmentPKID"
                                :items="departmentData"
                                :item-text="(item) => item.Department"
                                item-value="PKID"
                                class="khmer-font"
                                clearable
                                v-bind:label="$t('leaveEntry.department')"
                                color="cyan darken-1"
                                prepend-icon="mdi-account"
                                :error-messages="errorMessage.DepartmentPKID"
                                @change="GetAllEmployeesByDepartment()"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        class="text-capitalize pa-1 font-weight-regular"
                                        small
                                    >
                                        {{ data.item.Department }}
                                    </v-chip>
                                </template>
                            </v-autocomplete>

                            <JqxGrid
                                ref="gridDetail" :width="760" :source="dataAdapter" :columns="columns" :autoheight="true" :editable="true">
                            </JqxGrid>
                            
                    </v-card-text>
                    <v-card-actions class="khmer-font">
                    <v-spacer></v-spacer>
                    <v-btn 
                        color="grey lighten-2" 
                        small 
                        depressed
                        class="khmer-font" 
                        @click="closeDialog">{{$t('absent.btnCancel')}}</v-btn>
                    <v-btn
                        small
                        class="khmer-font white--text"
                        color="pink"
                        depressed
                        @click="addStaffApplyLeaveHeader()"
                        :loading="btnSaveLoading"
                    >
                        {{$t('absent.btnSave')}}
                    </v-btn>
                    </v-card-actions>
                </form>
           </v-card>
        </v-dialog>

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
                    <span class="khmer-font white--text" v-if="editMode === false">
                        <v-icon color="white" left>mdi-account-group-outline</v-icon>
                        {{$t('leaveEntry.frmTitle')}}
                    </span>
                    <span class="khmer-font white--text" v-else>
                        <v-icon color="white" left>mdi-account-group-outline</v-icon>
                        {{$t('leaveEntry.frmEditTitle')}}
                    </span>
                </v-toolbar>
                <form
                    @submit.prevent=" editMode ? updateLeaveEntry() : createLeaveEntry()"
                    enctype="multipart/form-data"
                >
                    <v-card-text>
                        <v-autocomplete
                                v-model="form.StaffPKID"
                                :items="staffActiveData"
                                :item-text="(item) => item.first_name + ' ' + item.last_name"
                                item-value="id"
                                class="khmer-font"
                                clearable
                                v-bind:label="$t('absent.tbEmp')"
                                color="cyan darken-1"
                                prepend-icon="mdi-account"
                                @change="ChangeShift()"
                                :error-messages="errorMessage.StaffPKID"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        class="text-capitalize pa-1 font-weight-regular"
                                      small
                                    >
                                        {{ data.item.first_name + " " + data.item.last_name}}
                                    </v-chip>
                                </template>
                            </v-autocomplete>
                            <v-autocomplete
                                clearable
                                prepend-icon="mdi-text-box-edit"
                                class="khmer-font"
                                :items="ValidationData"
                                item-value="PKID"
                                :item-text="(item)=>item.ValidationType"
                                color="cyan darken-1"
                                v-model="form.ValidationType"
                                v-bind:label="$t('parameters.txtValidation')"
                                :error-messages="errorMessage.ValidationType"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        small
                                    >
                                        {{data.item.ValidationType}}
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
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        class="text-capitalize pa-1 font-weight-regular"
                                        small
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
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        class="text-capitalize pa-1 font-weight-regular"
                                        small
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
                            ></v-textarea>
                            <v-row>
                                <v-col cols="12" sm="10" md="8">
                                    <span
                                        small
                                        class="khmer-font red--text font-weight-bold font-size-small"
                                        v-if="checkDateTime.openMsg"
                                    >
                                        {{checkDateTime.msgError}}
                                    </span>
                                    <span
                                        v-else
                                        small
                                        class="khmer-font blue-grey--text font-weight-regular font-size-small"
                                    >
                                        {{this.$t('leaveEntry.labelDateTime')}}
                                    </span>
                                    
                                </v-col>
                                <v-col cols="12" sm="2" md="4">
                                    <div class="text-end">
                                        <v-btn
                                            class="mb-1"
                                            small
                                            depressed
                                            left
                                            color="khmer-font khawin-background-color"
                                            @click="AddRow"
                                        >
                                        <v-icon class="white--text">mdi-plus</v-icon>
                                    </v-btn>
                                    </div>
                                </v-col>
                            </v-row>
                            <v-data-table
                                class="khmer-font"
                                :headers="HeaderForm"
                                :items="form.DataForm"
                                :hide-default-footer="true"
                            >
                                <template
                                    v-slot:[`item.AttendanceDate`] ="{ item }"
                                >
                                    <v-menu
                                        :close-on-content-click="false"
                                        max-width="290"
                                        v-model="ChoseDateAbsent"
                                        v-if="item.PKID == addItemObj.PKID"
                                    >
                                        <template
                                            v-slot:activator="{ on, attrs }"
                                        >
                                            <v-text-field
                                                :value="DateFormat"
                                                v-on="on"
                                                v-bind="attrs"
                                                class="khmer-font"
                                                color="cyan darken-1"
                                                readonly
                                                v-bind:label="$t('absent.attendance')"
                                                prepend-inner-icon="mdi-calendar"
                                                :error-messages="errorMessage.id"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            @input="ChoseDateAbsent = false"
                                            color="cyan darken-1"
                                            v-model="addItemObj.AttendanceDate"
                                        >
                                        </v-date-picker>
                                    </v-menu>
                                    <span v-else>{{ item.AttendanceDate }}</span>
                                </template>
                                
                                <template v-slot:[`item.TimeInTimeOutCode`]="{ item }">
                                    <span v-if="item.PKID === addItemObj.PKID" id="cmbTimeInTimeOut">
                                        <v-autocomplete
                                            v-model="addItemObj.TimeInTimeOutId"
                                            :items="TimeInTimeOutArr"
                                            :item-text="(item) => item.TimeInTimeOut"
                                            item-value="PKID"
                                            class="khmer-font"
                                            color="cyan darken-1"
                                            @change="selectCmbTimeInTimeOut"
                                        >
                                            <template v-slot:selection="data">
                                                <v-chip
                                                    label
                                                    dark
                                                    color="blue-grey darken-2"
                                                    class="text-capitalize pa-1 font-weight-bold"
                                                    small
                                                >
                                                    {{ data.item.TimeInTimeOut }}
                                                </v-chip>
                                            </template>
                                        </v-autocomplete>
                                    </span>
                                    <span v-else>{{ item.TimeInTimeOutCode }}</span>
                                </template>
                                <template
                                    v-slot:[`item.actions`] = "{ item }"
                                >
                                    <span v-if="item.PKID == addItemObj.PKID">
                                        <v-icon small @click="CloseRow" color="red">mdi-window-close</v-icon>
                                        <v-icon small @click="SaveRow" color="yellow">mdi-content-save</v-icon>
                                    </span>
                                    <span v-else>
                                        <v-icon small @click="editItem(item)">mdi-pencil</v-icon>
                                        <v-icon small @click="DeleteRow(item)">mdi-delete</v-icon>
                                    </span>
                                </template>
                            </v-data-table>
                            <v-divider></v-divider>
                    </v-card-text>
                    <v-card-actions class="khmer-font">
                    <v-spacer></v-spacer>
                    <v-btn 
                        color="grey lighten-2" 
                        small 
                        depressed
                        class="khmer-font" 
                        @click="closeDialog">{{$t('absent.btnCancel')}}</v-btn>
                    <v-btn
                        small
                        class="khmer-font white--text"
                        color="pink"
                        depressed
                        type="submit"
                        :loading="btnSaveLoading"
                    >
                        {{$t('absent.btnSave')}}
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
                            <b class="red--text tex--lighten-2 khmer-font">{{ staffNameDialog }}</b>
                            ?
                        </div>

                        <v-btn :disabled="btnLoading" class="ma-1 khmer-font" depressed small @click="dialogDelete = false">
                            {{$t('absent.btnCancel')}}
                        </v-btn>

                        <v-btn :loading="btnLoading" class="ma-1 khmer-font" dark color="red" small depressed
                            @click="submitDelete">
                            {{$t('absent.btnDelete')}}
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>
        <DeleteDialog
            :openDeleteDialog="openDeleteDialog" 
            @deleteItem="deleteItem" 
            @cancelDialog="openDeleteDialog = false"
        />
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
        name:'LeaveEntry',
        data(){
            var self = this;
            return {
                openDialog:false,
                openBtnDialog: false,
                btnSearch: false,
                DataForm:[],
                LeaveEntryData:[],
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
                    ShiftHeaderPKID: "",
                    LeaveReasonPKID:"",
                    LeaveTypePKID:"",
                    ValidationType: "",
                    DataForm: [],
                    detailGridData: [],
                    DepartmentPKID: "",
                }),
                leaveReasonData: [],
                leaveTypeData: [],
                staffActiveData: [],
                staffNameDialog: "",
                dialogDelete: false,
                btnLoading: false,
                searchLeaveEntry: "",
                editedIndex: -1,
                selectRow: 0,
                openDeleteDialog: false,
                addItem : {
                    StartDate: "",
                    EndDate: "",
                },
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
                ChooseStartDate: false,
                ChooseEndDate: false,
                TimeInTimeOutArr: [],
                TimeInTimeOutDropDown: [],
                alertMessage: {
                    openAlertMsg: false,
                    msgDialog: "",
                },

                departmentData: [],
                ValidationTypeData: [],   
                ValidationData: [],
                SearchByDate: "",
                SearchByValidation: [],
                checkDateTime:{
                    msgError: "",
                    openMsg: false
                },
                FilterByDate: false,
                dataAdapter: new jqx.dataAdapter({
                    localdata: [],
                    datatype: 'array',
                    detailGridData: [],
                    datafields:
                    [
                        {name: 'PKID', type: 'integer'},
                        {name: 'StaffId', type: 'int'},
                        {name: 'Staff', type: 'string'},
                        {name: 'LeaveType', type: 'string'},
                        {name: 'LeaveTypePKID', type: 'integer'},
                        {name: 'AttendanceDate', type: 'date'},
                        {name: 'TimeInTimeOutCode', type: 'string'},
                        {name: 'TimeInTimeOutId', type: 'integer'},
                        {name: 'Permission', type: 'bool'},
                        {name: 'Absent', type: 'bool'},
                        {name: 'LeaveReasonPKID', type: 'integer'},
                        {name: 'LeaveReason', type: 'string'},
                    ]
                }),
                columns: [
                    {text: this.$t('leaveEntry.tbEmpName'), datafield: 'Staff', width: 130, align: 'center', editable: false },
                    {text: this.$t('leaveEntry.tbType'), datafield: 'LeaveType', width: 100, align: 'center', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            editor[0].onselect = function(e) {
                                var selectedRowIndex = self.$refs.gridDetail.getselectedrowindex();
                                var selectedRowData = self.$refs.gridDetail.getrowdatabyid(selectedRowIndex);
                                selectedRowData.LeaveTypePKID = e.args.item.originalItem.PK_ID;
                                selectedRowData.LeaveType = e.args.item.originalItem.LeaveType;
                            };
                            return editor.jqxDropDownList({
                                autoDropDownHeight: true, source: self.leaveTypeData, displayMember: 'LeaveType', valueMember: 'PK_ID',
                            });
                        }
                    },
                    {text: this.$t('leaveEntry.tbAttendanceDate'), datafield: 'AttendanceDate', width: 120, align: 'center', columntype: 'datetimeinput',
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
                    {text: this.$t('leaveEntry.tbTime'), datafield: 'TimeIntimeOutCode', width: 100, align: 'center', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            var dropDownData = self.TimeInTimeOutDropDown[row];
                            var dropDownObj = this;
                            var tmpEditor = editor;
                                editor[0].onselect = function(e) {
                                    var selectedRowIndex = self.$refs.gridDetail.getselectedrowindex();
                                    var selectedRowData = self.$refs.gridDetail.getrowdatabyid(selectedRowIndex);
                                    if(e.args.item != null) {
                                        selectedRowData.TimeIntimeOutCode = e.args.item.originalItem.TimeInPlusTimeOut;
                                        selectedRowData.TimeInTimeOutId = e.args.item.originalItem.PKID;
                                    } else {
                                        dropDownObj.createeditor(selectedRowIndex, '', tmpEditor);
                                    }
                                };
                                return editor.jqxDropDownList({ 
                                    autoDropDownHeight: true, source: dropDownData, displayMember: 'TimeInPlusTimeOut', valueMember: 'PKID', 
                                });
                            }
                    },
                    {
                        text: this.$t('leaveEntry.tbPermission'), columntype: 'checkbox', datafield: 'Permission', width: 100, align: 'center', editable: true,
                    },
                    {
                        text: this.$t('leaveEntry.tbAbsent'), columntype: 'checkbox', datafield: 'Absent', width: 100, align: 'center', editable: true, 
                    },
                    {
                        text: this.$t('leaveEntry.tbReason'), datafield: 'LeaveReason', width: 110, align: 'center', columntype: 'dropdownlist',
                        createeditor: function (row, value, editor) {
                            editor[0].onselect = function(e) {
                                var selectedRowIndex = self.$refs.gridDetail.getselectedrowindex();
                                var selectedRowData = self.$refs.gridDetail.getrowdatabyid(selectedRowIndex);
                                selectedRowData.LeaveReasonPKID = e.args.item.originalItem.PK_ID;
                                selectedRowData.LeaveReason = e.args.item.originalItem.LeaveReason;
                            };
                            return editor.jqxDropDownList({
                                autoDropDownHeight: true, source: self.leaveReasonData, displayMember: 'LeaveReason', valueMember: 'PK_ID',
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
            //modify mony
            StartDateFormat(){
                if(this.addItem.StartDate != "" && this.addItem.EndDate != "" && this.FilterByDate) {
                    this.ReadLeaveEntry();
                }
                return this.addItem.StartDate ? moment(this.addItem.StartDate).format("DD/MM/YYYY") : ""; 
            },
            EndDateFormat(){
                return this.addItem.EndDate ? moment(this.addItem.EndDate).format("DD/MM/YYYY") : ""; 
            },
            header(){
                return[
                    {text: this.$t('leaveEntry.tbEmpName'), value:'Staff'},
                    {text: this.$t('leaveEntry.tbReason'), value:'LeaveReason'},
                    {text: this.$t('leaveEntry.tbStatus'), value:'Status'},
                    {text: this.$t('leaveEntry.tbType'), value:'LeaveType'},
                    {text: this.$t('parameters.txtValidation'), value:'Validationtype'},
                    //mdif huychoung 06/07/2023 #75
                    {text: this.$t('leaveEntry.tbAttendanceDate'), value:'AttendanceDate'},
                    {text: this.$t('leaveEntry.tbAction'), value:'actions',sortable:false, align:"center"},
                ]
            },
            HeaderForm(){
                return[
                    {text: this.$t('leaveEntry.tbAbsent'), value:'AttendanceDate'},
                    {text: this.$t('leaveEntry.tbTime'), value:'TimeInTimeOutCode'},
                    {text: this.$t('leaveEntry.tbAction'), value:'actions',sortable:false, align:"center"},
                ]
            },
            
        },
        mounted(){
            //modify by Theara #CMS-79 14/07/23
            // if((this.userToken.employee) && this.userToken.employee !== undefined) {       
            //     this.form.ShiftHeaderPKID = this.userToken.employee.ShiftHeaderPKID;
            // } else {
            //     this.form.ShiftHeaderPKID = 0;
            // }
            if(this.userToken.token){
                this.ReadLeaveEntry();
                this.ReadStaffActive();
                this.ReadLeaveReason();
                this.ReadLeaveType();
                this.ReadShiftDetail();
                this.ReadValidationType();
                this.ReadValidation();
                this.ReadDepartment();
            }
            this.activateMultipleDraggableDialogs();
        },
        methods:{
            ReadDepartment(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-department", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.departmentData = response.data.data;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            ReadValidationType(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-validationtype", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((response) => {
                    this.ValidationData = response.data.data
                })
                .catch((error) => {
                    console.log(error)
                })
            },
            ReadValidation(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-validationtype", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((response) =>{
                    this.ValidationTypeData = response.data.data
                })
                .catch((error) => {
                    console.log(error)
                })
            },
            ReadStaffActive() {
                axios
                    .get(this.$globalConfig.ipHost + "api/active-employee", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.staffActiveData = response.data.data;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            ReadShiftDetail(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-shift",{
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((respone) => {
                    this.ShidtDetailData = respone.data.data;
                })
                .catch((error) => {
                    console.log(error)
                })
            },
            ReadLeaveEntry(){
                var filterByDate = this.FilterByDate;
                var quickFilter = this.SearchByDate;
                var typeFilter = this.SearchByValidation;
                var startDate = this.addItem.StartDate;
                var endDate = this.addItem.EndDate;
                if(filterByDate) {
                    if(startDate == "") {
                        return;
                    }
                    if(endDate == "") {
                        return;
                    }
                    quickFilter = "";
                } else {
                    startDate = "";
                    endDate = "";
                }
                var formFilterData = {
                    quickFilter: quickFilter != "" ? quickFilter : 0,
                    typeFilter: typeFilter,
                    startDate: startDate,
                    endDate: endDate,
                }
                axios
                .post(this.$globalConfig.ipHost + "api/read-leave-entry", formFilterData, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((Response) => {
                    this.LeaveEntryData = Response.data.data
                    this.count = Response.data.data.length
                })
                .catch((error) => {
                    console.log(error)
                })
            },
            createLeaveEntry(){
                this.tableLoading = true;
                // this.btnSaveLoading = true;
                // this.form.detailGridData = this.dataAdapter.records;
                // for(var i = 0; i < this.form.detailGridData.length; i++) {
                //     this.form.detailGridData[i] = this.$helper.ConvertDateToStr(this.form.detailGridData[i].AttendanceDate);
                // }
                this.form
                .post("api/create-leave-entry",{
                    headers:{ Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
                })
                .then((Response) =>{
                    this.ReadLeaveEntry()
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
                    //modify by Theara #CMS-78 11/07/23
                    if(this.errorMessage.DataForm){
                        this.checkDateTime.openMsg = true
                        this.checkDateTime.msgError = this.$t('leaveEntry.msgEmpity')
                        // this.alertMessage.openAlertMsg = true
                        // this.alertMessage.msgDialog = this.$t('leaveEntry.msgEmpity')
                    }
                    else{
                        this.checkDateTime = false
                        //this.alertMessage.openAlertMsg = false
                    }
                    if(this.errorMessage.AttendanceDate){
                        if(this.errorMessage.AttendanceDate == 'Data can not be empity.'){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('leaveEntry.msgEmpity')
                            // this.checkDateTime.openMsg = true
                            // this.checkDateTime.msgError = this.$t('leaveEntry.msgEmpity')
                        }else if(this.errorMessage.AttendanceDate == 'Data is already existed.'){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('leaveEntry.msgExist')
                            // this.checkDateTime.openMsg = true
                            // this.checkDateTime.msgError = this.$t('leaveEntry.msgEmpity')
                        }else if(this.errorMessage.AttendanceDate == 'Data is duplicated.'){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('leaveEntry.msgDuplicate')
                            // this.checkDateTime.openMsg = true
                            // this.checkDateTime.msgError = this.$t('leaveEntry.msgEmpity')
                        }
                        //else{
                            //this.alertMessage.openAlertMsg = false
                            //this.checkDateTime = false
                        //}
                    }
                })
            },
            
            closeDialog(){
                this.editMode = false
                this.btnSaveLoading = false
                this.openDialog = false
                this.openBtnDialog = false
                this.form.PKID = 0;
                this.form.StartDate = "";
                this.form.EndDate = "";
                this.form.Remark = "";
                this.form.LeaveReasonPKID = "";
                this.form.LeaveTypePKID = "";
                this.form.LeaveEntryData = [];
                this.form.DataForm = [];
                this.errorMessage = "";
                this.form.ValidationType = "";
                this.form.StaffPKID = "";
                this.form.DepartmentPKID = "";
                this.checkDateTime.openMsg = false
                debugger;
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
            editLeaveEntry(item) {
                this.editMode = true;
                this.form.PKID = item.PKID;
                this.form.Remark = item.Remark;
                this.form.StaffPKID = item.StaffId;
                this.form.LeaveReasonPKID = item.LeaveReasonPKID
                this.form.LeaveTypePKID = item.LeaveTypePKID
                this.form.ValidationType = item.ValidationTypeID
                // this.form.DepartmentPKID = item.DepartmentPKID;
                // if(this.$refs.gridDetail !== undefined) {
                //     this.$refs.gridDetail.updatebounddata('cell');
                // }
                for(var i = 0; i < item.editTableArr.length; i++) {
                    var objModel = {};
                    objModel.PKID = item.editTableArr[i].pkId;
                    objModel.TimeInTimeOutId = item.editTableArr[i].pkId;
                    objModel.AttendanceDate = item.editTableArr[i].attendanceDate;
                    if(item.editTableArr[i].timeIn != null || item.editTableArr[i].timeOut != null) {
                        objModel.TimeInTimeOutCode = item.editTableArr[i].timeIn + "-" + item.editTableArr[i].timeOut;
                    } else {
                        objModel.TimeInTimeOutCode = "";
                    }
                    this.form.DataForm.push(objModel);
                }
                this.openDialog = true;
                this.openBtnDialog = true;
            },
            async updateLeaveEntry() {
                // this.btnSaveLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-leave-entry/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadLeaveEntry();
                    this.closeDialog();
                    this.alertSnackBarMsg = this.$t('leaveEntry.msgUpdate');
                    this.snackBar = true;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                })
                .catch((errors) => {
                    this.errorMessage = errors.response.data.errors;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                    //modify by Theara #CMS-78
                    if(this.errorMessage.DataForm){
                        // this.checkDateTime.openMsg = true
                        // this.checkDateTime.msgError = this.$t('leaveEntry.msgEmpity')
                        this.alertMessage.openAlertMsg = true
                        this.alertMessage.msgDialog = this.$t('leaveEntry.msgEmpity')
                    }
                    else{
                        this.checkDateTime = false
                    }
                    if(this.errorMessage.AttendanceDate){
                        if(this.errorMessage.AttendanceDate == 'Data can not be empity.'){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('leaveEntry.msgEmpity')
                            
                        }else if(this.errorMessage.AttendanceDate == 'Data is already existed.'){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('leaveEntry.msgExist')
                            
                        }else if(this.errorMessage.AttendanceDate == 'Data is duplicated.'){
                            this.alertMessage.openAlertMsg = true
                            this.alertMessage.msgDialog = this.$t('leaveEntry.msgDuplicate')
                            
                        }
                        
                    }
                });
            },
            deleteLeaveEntry(id, staffName) {
                this.form.PKID = id;
                this.staffNameDialog = staffName;
                this.dialogDelete = true;
            },
            async submitDelete() {
                this.btnLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                axios
                    .delete("/api/delete-leave-entry/" + this.form.PKID, {
                        headers: {
                            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                        },
                    })
                    .then((response) => {
                        this.ReadLeaveEntry();
                        this.dialogDelete = false;
                        this.alertSnackBarMsg = this.$t('leaveEntry.msgDelete');
                        this.snackBar = true;
                        this.btnLoading = false;
                        this.tableLoading = false;
                    })
                    .catch((error) => {
                        this.btnLoading = false;
                        this.tableLoading = false;
                    });
            },
            //modify by Theara #CMS-79 14/07/23
            ChangeShift(){
                this.TimeInTimeOutArr = [];
                var staffId = this.form.StaffPKID;
                var selectedStaff = this.staffActiveData.filter(function(item) {
                    return staffId == item.id;
                });
                if(selectedStaff.length > 0) {
                    for(var i = 0; i < this.ShidtDetailData.length; i++) {
                        if(this.ShidtDetailData[i].ShiftDetail.length > 0) {
                            if(this.ShidtDetailData[i].PKID == selectedStaff[0].ShiftName.PKID) {
                                for(var j = 0; j < this.ShidtDetailData[i].ShiftDetail.length; j++) {
                                    var timeIn = this.ShidtDetailData[i].ShiftDetail[j].TimeIn;
                                    var timeOut = this.ShidtDetailData[i].ShiftDetail[j].TimeOut;
                                    var concatTimeInTimeOut = timeIn + "-" + timeOut;
                                    var objDataForm = {
                                        PKID: this.ShidtDetailData[i].ShiftDetail[j].PKID,
                                        TimeInTimeOut: concatTimeInTimeOut
                                    };
                                    this.TimeInTimeOutArr.push(objDataForm);
                                }
                            break;
                            }
                        }
                    }
                }
            },
            AddRow(){
                const addObj = Object.assign({}, this.defaultItem);
                //modify by Theara #CMS-78
                addObj.PKID = this.form.DataForm.length + 1;
                addObj.order = addObj.PKID;
                var attendance = this.addItemObj.AttendanceDate
                var time = this.addItemObj.TimeInTimeOutId
                if(this.editedIndex > -1){
                    if(attendance =="" || time == ""){
                        this.alertMessage.openAlertMsg = true
                        this.alertMessage.msgDialog = this.$t('applyLeave.msgDateTime')
                        return
                    }
                }
                this.form.DataForm.unshift(addObj);
                this.editItem(addObj);
            },
            editItem(item) {
                this.editedIndex = this.form.DataForm.indexOf(item);
                this.addItemObj = Object.assign({}, item);
            },
            DeleteRow(item){
                this.selectRow = this.form.DataForm.indexOf(item);
                this.openDeleteDialog = true;
            },
            deleteItem(){
                //Change
                this.openDeleteDialog = false;
                this.form.DataForm.splice(this.selectRow,1);
            },
            SaveRow(){
                //modify by Theara #CMS-78
                if(this.editedIndex > -1) {
                    var selectedAttendanceDate = this.addItemObj.AttendanceDate;
                    var selectedTimeInTimeOutCode = this.addItemObj.TimeInTimeOutCode;
                    if(selectedAttendanceDate == "") {
                        this.alertMessage.openAlertMsg = true;
                        this.alertMessage.msgDialog = this.$t('leaveEntry.validationDate');
                        return;
                    }else if(selectedTimeInTimeOutCode == "") {
                        this.alertMessage.openAlertMsg = true;
                        this.alertMessage.msgDialog = this.$t('leaveEntry.validationTime');
                        return;
                    }
                    Object.assign(this.form.DataForm[this.editedIndex],this.addItemObj);
                }
                this.addItemObj = Object.assign({},this.defaultItem)
                this.editedIndex = -1;
            },
            CloseRow(){
                setTimeout(() =>{
                    //modify by Theara #CMS-78
                    var attendance = this.addItemObj.AttendanceDate;
                    var time = this.addItemObj.TimeInTimeOutCode;
                    if(attendance !== "" || time == "") {
                        this.form.DataForm.splice(this.selectRow,1);
                    } else {
                        if(this.editedIndex > -1){
                            Object.assign(this.form.DataForm[this.editedIndex],this.addItemObj);
                        }
                    }
                    
                    this.addItemObj = Object.assign({}, this.defaultItem);
                    this.editedIndex = -1;
                }, 300);
            },
            selectCmbTimeInTimeOut() {
                var inputGroup = document.getElementById("cmbTimeInTimeOut").getElementsByClassName("v-select__selections")[0].getElementsByTagName("input");
                inputGroup[0].style.display = "none";
                var cmbTimeInTimeOutId = this.addItemObj.TimeInTimeOutId;
                var cmbTimeInTimeOutCode = "";
                for(var i = 0; i < this.TimeInTimeOutArr.length; i++) {
                    if(this.TimeInTimeOutArr[i].PKID == cmbTimeInTimeOutId) {
                        cmbTimeInTimeOutCode = this.TimeInTimeOutArr[i].TimeInTimeOut;
                        break;
                    }
                }
                this.addItemObj.TimeInTimeOutCode = cmbTimeInTimeOutCode;
            },
            GetAllEmployeesByDepartment() {
                axios
                    .get(this.$globalConfig.ipHost + "api/read-employees-by-department-id/" + this.form.DepartmentPKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    var dataRes = response.data.Data;
                    var detailGridData = [];
                    for(var i = 0; i < dataRes.length; i++) {
                        var fullName = dataRes[i].first_name + " " + dataRes[i].last_name;
                        var detailGridObj = {
                            StaffId: dataRes[i].staffId,
                            Staff: fullName,
                            LeaveType: "",
                            AttendanceDate: "",
                            TimeIntimeOutCode: "",
                            TimeInTimeOuts: "",
                            Permission: "",
                            Absent: "",
                            LeaveReason: "",
                        }
                        detailGridData.push(detailGridObj);
                        this.TimeInTimeOutDropDown.push(dataRes[i].TimeInTimeOuts);
                    }
                    this.dataAdapter._source.localdata = detailGridData;
                    if(this.$refs.gridDetail !== undefined) {
                        this.$refs.gridDetail.updatebounddata('cell');
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            addStaffApplyLeaveHeader() {
                var formStaffApplyLeaveHeade = new Form({
                    DataForm: [],
                });
                formStaffApplyLeaveHeade.DataForm = this.dataAdapter.records;
                for(var i = 0; i < formStaffApplyLeaveHeade.DataForm.length; i++) {
                    formStaffApplyLeaveHeade.DataForm[i].AttendanceDate = this.$helper.ConvertDateToStr(formStaffApplyLeaveHeade.DataForm[i].AttendanceDate);
                }
                formStaffApplyLeaveHeade    
                .post("api/create-staff-apply-leave-header",{
                    headers:{ Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
                })
                .then((response) =>{
                    this.ReadLeaveEntry();
                    this.openBtnDialog = false;
                    this.closeDialog();
                    this.alertSnackBarMsg = this.$t('leaveEntry.msgSaved')
                    this.snackBar = true
                    this.btnSaveLoading = false
                    this.tableLoading = false
                })
                .catch((errors) =>{
                    this.btnSaveLoading = false
                    this.tableLoading = false
                    this.errorMessage = errors.response.data.errors
                })        
            }
        }
    }
</script>
