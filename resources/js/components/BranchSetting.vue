<template>
    <div id="branch">
        <v-row>
          <v-col cols="12" sm="6">
             <v-row>
                <v-col cols="12" sm="12" md="5">
                   <h3 class="red-lighten-3 font-weight-bold">
                      <v-icon color="grey darken-2" class="mb-1">mdi-store-24-hour</v-icon>
                      <span class="text-decoration-underline font-weight-medium">{{$t('branch.branchList')}}</span>
                      <v-chip color="grey lighten-2 grey--text text--darken-3">{{ branchCount }}</v-chip>
                   </h3>
                </v-col>
                <v-col cols="12" sm="12" md="7">
                   <v-text-field
                      v-model="searchBranch"
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
                   {{$t('leaveEntry.btnAddLeaveType')}}
            </v-btn>
        </v-col>
        </v-row>
            <v-dialog
                v-model="openDialog"
                width="500">
 
                <v-card>
                    <v-toolbar
                    dense
                    flat
                    color="lighten-1"
                    class="khawin-background-color"
                >
                    <span v-if="editMode === false" class="khmer-font white--text">
                        <v-icon left color="white">mdi-store-24-hour</v-icon>
                        {{$t('branch.frmAddBranch')}}
                    </span>
                    <span v-else class="khmer-font white--text">
                        <v-icon left color="white">mdi-store-24-hour</v-icon>
                        {{$t('branch.frmEditBranch')}}
                    </span>
                </v-toolbar>  
                <form @submit.prevent="editMode ? updateBranch() : createBranch()">                 
                    <v-card-text>          
                        <v-text-field
                            v-model="form.branchCode"
                            v-bind:label="$t('branch.txtCode')"
                            prepend-inner-icon="mdi-identifier"
                            class="khmer-font"
                            :error-messages="errorsMessage.branchCode">
                        </v-text-field>

                        <v-text-field
                            v-model="form.branchName"
                            v-bind:label="$t('branch.txtBranch')"
                            prepend-inner-icon="mdi-store-24-hour"
                            class="khmer-font"
                            :error-messages="errorsMessage.branchName">    
                        </v-text-field>

                        <v-text-field
                            v-model="form.location"
                            v-bind:label="$t('branch.txtLocation')"
                            prepend-inner-icon="mdi-map-marker"
                            class="khmer-font">
                        </v-text-field>

                        <v-row>
                            <v-col>
                                <v-menu
                                    v-model="createdDateChoose"
                                    :close-on-content-click="false"
                                    offset-y
                                    min-width="290px"
                                >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="form.createdDate"
                                        class="khmer-font"
                                        :value="DateFormattedMomentjs"
                                        v-bind:label="$t('branch.txtStartDate')"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>

                                <v-date-picker
                                    v-model="form.createdDate"
                                    @input="createdDateChoose = false"
                                ></v-date-picker>
                            </v-menu>
                            </v-col>
                            <v-col>
                                <v-menu
                                    v-model="closedDateChoose"
                                    :close-on-content-click="false"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="290px"
                                >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="form.closedDate"
                                        class="khmer-font"
                                        v-bind:label="$t('branch.txtEndDate')"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        :value="DateFormattedClosedDate"
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="form.closedDate"
                                    @input="closedDateChoose = false"
                                ></v-date-picker>
                            </v-menu>
                            </v-col>
                        </v-row>                 

                        <v-textarea 
                            id="input-864" rows="3"
                            class="khmer-font"
                            v-model="form.remark"
                            prepend-inner-icon="mdi-pencil"
                            v-bind:label="$t('leaveEntry.tbRemark')"
                            outlined
                        ></v-textarea>
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
        <!-- table -->
        <v-card class="mx-auto table-card v-card v-sheet theme--ligh">
            <v-data-table
                :headers="headers"
                :items="BranchResources"
                :loading="tableLoading"
                :search="searchBranch"
            >

                <template v-slot:[`item.actions`]="{ item }">
                        <v-icon small class="mr-2" @click="editBranch(item)">mdi-pencil</v-icon>
                        <v-icon small class="mr-2" @click="deleteBranch(item.PKID, item.Branch)">mdi-delete</v-icon>
                </template>
            </v-data-table>
        </v-card>

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
                        <div class="grey--text text--darken-3 text-body-2 mb-4">
                            {{$t('workflow.deleteMessage')}}
                            <b class="red--text tex--lighten-2">{{ branchTitle }}</b>
                            ?
                        </div>

                        <v-btn :disabled="btnLoading" class="ma-1" depressed small @click="dialogDelete = false">
                            {{$t('absent.btnCancel')}}
                        </v-btn>

                        <v-btn :loading="btnLoading" class="ma-1" dark color="red" small depressed
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
import moment, { relativeTimeThreshold } from 'moment';
import store from "../store";
export default{
    name: 'BranchSetting',
    data() {
        return { 
            openDialog: false, 
            createdDateChoose: false,
            closedDateChoose: false,
            branchCount: "",
            BranchResources: [], 
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                id: "",
                branchCode: "",
                branchName: "",
                location: "",
                createdDate: "",
                closedDate: "",
                remark: "",
            }),
            btnLoading: false,
            dialogDelete: false,
            branchTitle: "",
            searchBranch: "",
        };                  
    },
    computed: {
            DateFormattedMomentjs () {
                return this.form.createdDate ? moment(this.form.createdDate).format('DD/MM/YYYY') : "";
            },
            DateFormattedClosedDate(){
                return this.form.closedDate ? moment(this.form.closedDate).format('DD/MM/YYYY') : "";
            },
            userToken() {    
                return store.state.user;
            },
            headers(){
                return [
                {text: this.$t('branch.txtCode'), value: 'BranchCode'},
                {text: this.$t('branch.txtBranch'), value: 'BranchName'},
                {text: this.$t('branch.txtLocation'), value: 'Location'},
                {text: this.$t('branch.txtStartDate'), value: 'CreatedDate'},
                {text: this.$t('branch.txtEndDate'), value: 'ClosedDate'},
                {text: this.$t('leaveEntry.tbRemark'), value: 'Remark'},
                {text: this.$t('leaveEntry.tbAction'), sortable: false, align: "center", value: 'actions' }
                ]
            }

    },
    mounted() {    
        if (this.userToken.token){
            this.ReadBranch();
            
            
        }
    },
    methods: {
        ReadBranch() {        
            axios
                .get(this.$globalConfig.ipHost + "api/read-branch", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {    
                    this.BranchResources = response.data.data;
                    this.branchCount = response.data.data.length;
                    this.tableLoading = false;
                    
                })
                .catch((error) => {   
                    console.log(error);
                });
        },

        createBranch() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form
            .post("api/create-branch", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),   
                },
            })
            .then((response) => {
                this.ReadBranch();
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
            this.form.branchCode = "";
            this.form.branchName = "";
            this.form.location = "";
            this.form.createdDate = "";
            this.form.closedDate = "";
            this.form.remark = "";
        },
        editBranch(item) {
            this.editMode = true;
            this.form.id = item.PKID;
            this.form.branchCode = item.BranchCode;
            this.form.branchName = item.BranchName;
            this.form.location = item.Location;
            this.form.createdDate = item.CreatedDate;
            this.form.closedDate = item.ClosedDate;
            this.form.remark = item.Remark;
            this.openDialog = true;
        },
        deleteBranch(id, branch) {
            this.form.id = id;
            this.branchTitle = branch;
            this.dialogDelete = true;
        },
        async updateBranch() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
            .post("/api/update-branch/" + this.form.id, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadBranch();
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
                .delete("/api/delete-branch/" + this.form.id, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadBranch();
                    this.dialogDelete = false;
                    this.alertSnackbarMsg = this.$t('leaveEntry.msgDelete');
                    this.snackbar = true;
                    this.btnLoading = false;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    this.btnLoading = false;
                    this.tableLoading = false;
                }
            );
        },
    }
}
</script>