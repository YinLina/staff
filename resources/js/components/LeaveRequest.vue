<template>
    <div id="employee">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                    <h3 class="red-lighten-3 font-weight-bold">
                        <v-icon color="grey darken-2" class="mb-1">mdi-timetable</v-icon>
                        <span class="text-decoration-underline font-weight-medium">{{$t('leaveRequest.list')}}</span>
                    </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                    <v-text-field
                        v-model="searchLeaveRequest"
                        append-icon="mdi-magnify"
                        single-line
                        class="txt-search"
                        v-bind:label="$t('absent.search')"
                    ></v-text-field>
                    </v-col>
                </v-row>
            </v-col>

        </v-row>
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
                        <span class="khmer-font white--text">
                            <v-icon left color="white">mdi-timetable</v-icon>
                            {{$t('absent.LeaveDetail')}}
                        </span>
                    </v-toolbar> 
                <form @submit.prevent="updateStatus()"
                    enctype="multipart/form-data">
                    <v-card-text>    
                        <v-text-field
                            v-model="form.staffName"
                            v-bind:label="$t('absent.tbEmp')"
                            prepend-icon="mdi-account"
                            disabled
                            class="khmer-font"    
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="form.leaveReason"
                            v-bind:label="$t('reason')"
                            disabled
                            prepend-icon="mdi-account-arrow-left"
                            class="khmer-font"
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="form.leaveType"
                            v-bind:label="$t('absent.type')"
                            prepend-icon="mdi-account-cancel"
                            disabled
                            class="khmer-font"
                        >
                        </v-text-field>

                        <v-textarea 
                            v-model="form.remark"
                            class="khmer-font"
                            prepend-icon="mdi-pencil"
                            v-bind:label="$t('absent.remark')"
                            rows="1"
                            disabled
                        ></v-textarea>

                        <v-textarea 
                            v-model="form.comment"
                            class="khmer-font"
                            prepend-inner-icon="mdi-pencil"
                            v-bind:label="$t('leaveRequest.comment')"
                            rows="1"
                            outlined
                        ></v-textarea>
                    
                        <v-data-table
                            :headers="formTable"
                            :items="editTableArr"
                            class="khmer-font"
                            :hide-default-footer="true"
                        >
                            <template v-slot:[`item.attendanceDate`]="{ item }">
                                <span>{{item.attendanceDate}}</span>
                            </template>
                            <template v-slot:[`item.TimeInTimeOut`]="{ item }">
                                <span>{{item.timeIn+' - '+item.timeOut}}</span>
                            </template>
                        </v-data-table>
                    </v-card-text>

                    <!-- button cancel and save -->
                    <v-card-actions class="khmer-font">
                        <v-spacer></v-spacer>
                        <v-btn 
                            color="red lighten-2" 
                            small 
                            depressed 
                            type="submit" 
                            @click="form.checkBtn=false"
                        >
                            {{$t('leaveRequest.btnReject')}}
                        </v-btn>
                        <v-btn color="khawin-background-color khmer-font" dark small depressed type="submit"  @click="form.checkBtn=true">{{$t('leaveRequest.btnApprove')}}</v-btn>
                        <v-btn color="grey lighten-2" @click="closeDialog" small depressed >{{$t('absent.btnCancel')}}</v-btn>
                    </v-card-actions>
                </form>
                </v-card>
            </v-dialog>

            <v-tabs v-model="tab">
            <v-tabs-slider color="transparent"></v-tabs-slider>
            <v-tab class="text-capitalize tab" ref="tab1">  
                <v-icon left>mdi-timetable</v-icon>
                <span class="font-weight-medium">{{$t('leaveRequest.applied')}}</span>
                <v-chip class="ml-2 font-weight-medium blue-grey--text text--darken-2">{{ leaveRequestCount }}</v-chip>
            </v-tab>
            <v-tab class="text-capitalize tab" ref="tab2">
                <v-icon left>mdi-history</v-icon>
                <span class="font-weight-mdium">{{$t('leaveRequest.history')}}</span>
                <v-chip class="ml-2 font-weight-medium blue-grey--text text--darken-2">{{ validationHistoryCount }}</v-chip>
            </v-tab>
            <v-spacer></v-spacer>
            <v-btn
                icon
                fab
                class="mx-2 mt-1"
                small
                depressed
            >
                <v-icon>mdi-view-grid</v-icon>
            </v-btn>
        </v-tabs>
        <!-- table -->
        <v-tabs-items v-model="tab">
            <v-tab-item>
                <v-card class="mx-auto table-card v-card v-sheet theme--ligh">
                    <v-data-table
                        :headers = "LeaveRequestHeaders"
                        :items = "leaveRequestData"
                        :search="searchLeaveRequest"
                    >

            <template v-slot:[`item.actions`]="{ item }">
                <v-icon small class="mr-2" @click="openLeaveRequestDetail(item)">mdi-pencil</v-icon>
            </template>
        
            </v-data-table>
            </v-card>
        </v-tab-item>
        <v-tab-item>
                <v-card class="mx-auto table-card">
                    <v-data-table
                        :headers="ValidationHistoryHeaders"
                        :items="leaveRequestData"
                        :search="searchLeaveRequest"
                    >
                    </v-data-table>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
    </div>
</template>

<script>
import store from "../store";
export default{
    name: 'leaveRequest',
    data() {
        return {    
            tab1: 1,
            tab2: 2,
            tab: null,
            leaveRequestCount: "",
            validationHistoryCount: "",
            leaveRequestData: [],
            openDialog: false, 
            btnSaveLoading: false,
            btnLoading: false,
            searchLeaveRequest: "",
            form: new Form({   
                PKID:"",
                staffName: "",
                leaveReason: "",
                leaveType: "",
                remark: "",
                comment: "",
                checkBtn: false,
                leaveRequestData: [],
            }),
            editMode: false,
            searchLeaveRequest: "",
            addItemObj: {
                    AttendanceDate: "",
                    TimeIn: "",
                    TimeOut: "",
                    id: 0,
                    order: 0,
                },
            editTableArr: [],
            approve: "",
            reject: "",
            };     
        },
        computed: {
                userToken() {    
                    return store.state.user;    
            },
            LeaveRequestHeaders(){
                return[
                {text: this.$t('leaveEntry.tbEmpName'), value: 'StaffName'},
                {text: this.$t('leaveEntry.submiteDate'), value: 'SubmittedDate'}, 
                {text: this.$t('leaveEntry.tbReason'), value: 'LeaveReason'},
                {text: this.$t('leaveEntry.tbStatus'), value: 'Status'},
                {text: this.$t('leaveEntry.detail'), sortable: false, align: "center", value: 'actions' }
                ]
            },
            ValidationHistoryHeaders(){
                return[
                    {text: this.$t('leaveEntry.tbEmpName'), value: 'StaffName'},
                    {text: this.$t('leaveEntry.submiteDate'), value: 'SubmittedDate'}, 
                    {text: this.$t('leaveEntry.tbReason'), value: 'LeaveReason'},
                    {text: this.$t('leaveEntry.tbStatus'), value: 'Status'},
                ]
            },
            formTable(){
                return[
                    {text:this.$t('absent.attendance'), value:'attendanceDate', width: '20%'},
                    {text:this.$t('absent.absentTime'), value:'TimeInTimeOut', width: '20%'},
                ]
            }
        },
        mounted() {    
            if (this.userToken.token){
                this.ReadLeaveRequest();
                this.ReadApplyLeave();
            }
        },
        methods: {
            ReadLeaveRequest() {  
                axios
                .get(this.$globalConfig.ipHost + "api/read-leave-request", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.leaveRequestData = response.data.data;
                    this.leaveRequestCount = response.data.data.length;
                    this.validationHistoryCount = response.data.data.length;
                    this.tableLoading = false;
                    
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            ReadApplyLeave(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-apply-leave/" + this.$i18n.locale, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((Response) => {
                    this.count = Response.data.data.length
                })
                .catch((error) => {
                    console.log(error)
                })
            },
            openLeaveRequestDetail(item) {
                this.openDialog = true; 
                this.form.staffName = item.StaffName;
                this.form.leaveReason = item.LeaveReason;
                this.form.leaveType = item.LeaveType;
                this.form.remark = item.Remark;
                this.form.comment = item.Comment;
                this.editTableArr = item.editTableArr;      
            },
            closeDialog() {
                this.editMode= false;
                this.btnSaveLoading= false;
                this.openDialog = false;
                this.checkBtn = false;
                this.form.staffName = "";
                this.form.leaveReason = "";
                this.form.leaveType = "";
                this.form.remark = "";
                this.form.comment = "";
                this.errorMessage = "";
            },
            async updateStatus(){
                this.tableLoading = true;
                // this.btnSaveLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                    this.form
                    .post("/api/update-leave-request-status-approve/" + this.form.PKID, {
                        headers:{ Authorization:"Bearer " + sessionStorage.getItem("TOKEN") },
                    })
                    .then((response) => {
                        this.ReadApplyLeave();
                        this.ReadLeaveRequest();
                        this.closeDialog();
                        this.leaveRequestData = response.data.data;
                        this.alertSnackbarMsg = this.$t('leaveRequest.msgApprove');
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
            openLeaveRequestDetail(item) {
                this.openDialog = true;
                this.form.PKID = item.PKID;
                this.form.staffName = item.StaffName;
                this.form.leaveReason = item.LeaveReason;
                this.form.leaveType = item.LeaveType;
                this.form.remark = item.Remark;
                this.form.comment = item.Comment;
                this.editTableArr = item.editTableArr;
            },
        },
    }
</script>
