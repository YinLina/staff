<template>
    <div id="leave-reason">
        <v-row>
          <v-col cols="12" sm="6">
             <v-row>
                <v-col cols="12" sm="12" md="5">
                   <h3 class="red-lighten-3 font-weight-bold">
                      <v-icon color="grey darken-2" class="mb-1">mdi-account-arrow-left</v-icon>
                      <span class="text-decoration-underline font-weight-medium">{{$t('leaveEntry.leaveReason')}}</span>
                      <v-chip color="grey lighten-2 grey--text text--darken-3">{{ leaveReasonCount }}</v-chip>
                   </h3>
                </v-col>
                <v-col cols="12" sm="12" md="7">
                   <v-text-field
                      v-model="searchLeaveReason"
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
                   {{$t('document.btnAdd')}}
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
                            <v-icon left color="white">mdi-account-arrow-left</v-icon>
                            {{$t('leaveEntry.frmAddReason')}}
                        </span>
                        <span v-else class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-arrow-left</v-icon>
                            {{$t('leaveEntry.frmEditReason')}}
                        </span>
                    </v-toolbar> 
                <form @submit.prevent="editMode ? updateLeaveReason() : createLeaveReason()">
                   <v-card-text>    
                        <v-text-field
                            v-model="form.leaveReason"
                            v-bind:label="$t('leaveEntry.leaveReason')"
                            outlined
                            prepend-inner-icon="mdi-account-arrow-left"
                            :error-messages="errorsMessage.leaveReason">
                    </v-text-field>

                    <v-textarea 
                        v-model="form.remark"
                        class="khmer-font"
                        prepend-inner-icon="mdi-pencil"
                        v-bind:label="$t('absent.remark')"
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
                :headers = "headers"
                :items = "leaveReasonData"
                :loading="tableLoading"
                :search="searchLeaveReason"
            >

            <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="editLeaveReason(item)">mdi-pencil</v-icon>
                    <v-icon small class="mr-2" @click="deleteLeaveReason(item.PK_ID, item.LeaveReason)">mdi-delete</v-icon>
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
                        <div class="grey--text text--darken-3 text-body-2 mb-4 khmer-font">
                            {{$t('workflow.deleteMessage')}}
                            <b class="red--text tex--lighten-2">{{ leaveReasonTitle }}</b>
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
import store from "../store";
export default{
    name: 'leaveReasonSetting',
    data() {
        return {
            openDialog: false, 
            leaveReasonCount: "",
            
        leaveReasonData: [], 
        tableLoading: true,
        btnSaveLoading: false,
        editMode: false,
        errorsMessage: "",
        alertSnackbarMsg: "",
        snackbar: false,
        form: new Form({
            id: "",
            leaveReason: "",
            remark: "",
        }),
        btnLoading: false,
        dialogDelete: false,
        leaveReasonTitle: "",
        searchLeaveReason: "",
        };                  
    },
    computed: {
        userToken() {    
            return store.state.user;
        },
        headers(){
            return[
                {text: this.$t('leaveEntry.tbReason'), value: 'LeaveReason'},
                {text: this.$t('leaveEntry.tbRemark'), value: 'Remark'},
                {text: this.$t('leaveEntry.tbAction'), sortable:false, align: "center", value: 'actions' }
            ]
        }
    },
    mounted() {    
        if (this.userToken.token){
            this.ReadLeaveReason();
            
        }
    },
    methods: {
        ReadLeaveReason() {        
            axios
            .get(this.$globalConfig.ipHost + "api/read-leave-reason", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {    
                this.leaveReasonData = response.data.data;
                this.leaveReasonCount = response.data.data.length;
                this.tableLoading = false;
                
            })
            .catch((error) => {   
                console.log(error);
            });
        },

        createLeaveReason() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form
            .post("api/create-leave-reason", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadLeaveReason();
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
            this.form.leaveReason = "";
            this.form.remark = "";
        },

        editLeaveReason(item) {
            this.editMode = true;
            this.form.id = item.PK_ID;
            this.form.leaveReason = item.LeaveReason;
            this.form.remark = item.Remark;
            this.openDialog = true;
        },

        deleteLeaveReason(id, leaveReason) {
            this.form.id = id;
            this.leaveReasonTitle = leaveReason;
            this.dialogDelete = true;
        },

        async updateLeaveReason() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
            .post("/api/update-leave-reason/" + this.form.id, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadLeaveReason();
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
                    .delete("/api/delete-leave-reason/" + this.form.id, {
                        headers: {
                            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                        },
                    })
                    .then((response) => {
                        this.ReadLeaveReason();
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
    },
}
</script>
