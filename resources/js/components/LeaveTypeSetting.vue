<template>
    <div id="leaveType_setting">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="6">
                        <h3 class="grey--text text--darken-2 mt-5">
                            <v-icon color="grey darken-2">mdi-account-cancel</v-icon>
                            <span class="text-decoration-underline font-weight-medium">{{$t('leaveEntry.tbType')}}</span>
                            <v-chip class="grey lighten-2 grey--text text--darken-3" >{{ leaveTypeCount }}</v-chip>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="6">
                        <v-text-field
                            v-model="searchLeaveType"
                            append-icon="mdi-magnify"
                            single-line
                            v-bind:label="$t('absent.search')"
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" sm="6" class="text-end">
                <v-btn
                    color="white--text khawin-background-color mt-5 font-weight-regular pa-2"
                    small
                    @click="openDialogForm"
                >
                <v-icon color="white--text">mdi-plus</v-icon>
                {{$t('leaveEntry.btnAddLeaveType')}}</v-btn>
            </v-col>
        </v-row>
        <v-dialog
            width="500"
            v-model="openDialog"
        >
            <v-card>
                <v-toolbar
                    dense
                    flat
                    color="lighten-1"
                    class="khawin-background-color"
                >
                    <span v-if="editMode === false" class="khmer-font white--text">
                        <v-icon left color="white">mdi-account-cancel</v-icon>
                        {{$t('leaveEntry.frmLeaveType')}}
                    </span>
                    <span v-else class="khmer-font white--text">
                        <v-icon left color="white">mdi-account-cancel</v-icon>
                        {{$t('leaveEntry.frmEditLeaveType')}}
                    </span>
                </v-toolbar>
                <form @submit.prevent="editMode ? updateLeaveType() : createLeaveType()">
                    <v-card-text>
                        <v-text-field
                            v-model="form.leaveType"
                            class="khmer-font"
                            prepend-inner-icon="mdi-account-cancel"
                            v-bind:label="$t('leaveEntry.tbType')"
                            :error-messages="errorsMessage.leaveType"
                        ></v-text-field>
                        <v-text-field
                            v-model="form.allowance"
                            prepend-inner-icon="mdi-cash-multiple"
                            v-bind:label="$t('leaveEntry.txtAllowance')"
                            class="khmer-font"
                            type="number"
                            step="any"
                            :error-messages="errorsMessage.allowance"
                        >

                        </v-text-field>
                        <v-text-field
                            v-model="form.deduction"
                            v-bind:label="$t('leaveEntry.txtDeduction')"
                            class="khmer-font"
                            type="number"
                            step="any"
                            prepend-inner-icon="mdi-cash-multiple"
                            :error-messages="errorsMessage.deduction"
                        ></v-text-field>
                        <v-textarea
                            v-model="form.remark"
                            prepend-inner-icon="mdi-pencil"
                            v-bind:label="$t('absent.remark')"
                            class="khmer-font"
                            outlined
                        >
                        </v-textarea>
                    </v-card-text>
                    <v-card-actions class="khmer-font">
                        <v-spacer></v-spacer>
                        <v-btn color="grey lighten-2" small depressed @click="closeDialog">{{$t('absent.btnCancel')}}</v-btn>
                        <v-btn color="khawin-background-color khmer-font" dark small type="submit" :loading="btnSaveLoading">{{$t('absent.btnSave')}}</v-btn>
                    </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
        <v-data-table
            :headers="header"
            :items="leaveTypeData"
            :loading="tableLoading"
            :search="searchLeaveType"
        >
            <template v-slot:[`item.actions`]="{ item }">
                <v-icon small class="mr-2" @click="editLeaveType(item)">mdi-pencil</v-icon>
                <v-icon small class="mr-2" @click="deleteLeaveType(item.PK_ID, item.LeaveType)">mdi-delete</v-icon>
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
        <!-- ----------dialogDelete------------ -->
        <v-dialog v-model="dialogDelete" max-width="330px">
            <v-card>
                <div class="text-center">
                    <v-sheet class="px-7 pt-7 pb-4 mx-auto text-center d-inline-block">
                        <v-icon class="text-center pb-3" x-large color="red lighten-2">mdi-alert</v-icon>
                        <div class="grey--text text--darken-3 text-body-2 mb-4 khmer-font">
                            {{$t('workflow.deleteMessage')}}
                            <b class="red--text tex--lighten-2">{{ leaveTypeTitle }}</b>
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
    </div>
</template>
<script>
    import store from "../store";
    export default {
        name:'LeaveTypeSetting',
        data:()=>({
            openDialog:false,
            leaveTypeData: [],
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                id: "",
                leaveType: "",
                allowance: null,
                deduction: null,
                remark: "",
            }),
            btnLoading: false,
            dialogDelete: false,
            leaveTypeTitle: "",
            searchLeaveType: "",
            leaveTypeCount: 0,
        }),
        computed: {
            userToken() {
                return store.state.user;
            },
            header(){
                return [
                {text: this.$t('leaveEntry.tbType'),value:'LeaveType'},
                {text: this.$t('leaveEntry.txtAllowance'),value:'Allowance'},
                {text: this.$t('leaveEntry.txtDeduction'),value:'Deduction'},
                {text: this.$t('leaveEntry.tbRemark'),value:'Remark'},
                {text: this.$t('leaveEntry.tbAction'), sortable: false, align: "center", value:'actions'}
            ]
            }
        },
        mounted() {
            if(this.userToken.token){
                this.ReadLeaveType();
            }
        },
        methods: {
            ReadLeaveType() {
                axios
                    .get(this.$globalConfig.ipHost + "api/read-leave-type", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.leaveTypeData = response.data.data;
                    this.leaveTypeCount = response.data.data.length;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createLeaveType() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                this.form
                .post("api/create-leave-type", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadLeaveType();
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
                });
            },
            openDialogForm() {
                this.openDialog = true;
            },
            closeDialog() {
                this.editMode = false;
                this.openDialog = false;
                this.btnSaveLoading = false;
                this.form.id = "";
                this.form.leaveType = "";
                this.form.allowance = null;
                this.form.deduction = null;
                this.form.remark = "";
                this.errorsMessage = "";
            },
            editLeaveType(item) {
                this.editMode = true;
                this.form.id = item.PK_ID;
                this.form.leaveType = item.LeaveType;
                this.form.allowance = item.Allowance;
                this.form.deduction = item.Deduction;
                this.form.remark = item.Remark;
                this.openDialog = true;
            },
            deleteLeaveType(id, leaveType) {
                this.form.id = id;
                this.leaveTypeTitle = leaveType;
                this.dialogDelete = true;
            },
            async updateLeaveType() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-leave-type/" + this.form.id, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadLeaveType();
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
                });
            },
            async submitDelete() {
                this.btnLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                axios
                    .delete("/api/delete-leave-type/" + this.form.id, {
                        headers: {
                            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                        },
                    })
                    .then((response) => {
                        this.ReadLeaveType();
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