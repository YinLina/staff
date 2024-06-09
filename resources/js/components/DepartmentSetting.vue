<template>
    <div>
        <v-row id="department">
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="6">
                        <h3 class="grey--text text--darken-2 mt-5">
                            <v-icon color="grey darken-2">mdi-domain</v-icon>
                            <span class="text-decoration-underline font-weight-medium">{{$t('department.listDepartment')}}</span>
                            <v-chip color="grey lighten-2 grey--text text--darken-3">{{
                                departmentCount
                            }}</v-chip>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="6">
                        <v-text-field
                            append-icon="mdi-magnify"
                            single-line
                            v-bind:label="$t('absent.search')"
                            v-model="searchDepartment"
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-col>

            <!-- add employee button -->
            <v-col cols="12" sm="6" class="text-end">
                <v-btn small class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                    @click="openDialogForm">
                    <v-icon left>mdi-plus</v-icon>
                    {{$t('leaveEntry.btnAddLeaveType')}}
                </v-btn>
                <v-dialog
                    v-model="openDialog"
                    width="500"
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
                            {{$t('department.frmAddDepartment')}}
                        </span>
                        <span v-else class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-edit</v-icon>
                            {{$t('department.frmEditDepartment')}}
                        </span>
                    </v-toolbar> 
                    <form @submit.prevent="editMode ? updateDepartment() : createDepartment()">
                        <v-card-text>
                            <v-text-field
                                v-model="form.Department"
                                prepend-inner-icon="mdi-domain"
                                class="khmer-font"
                                v-bind:label="$t('department.department')"
                                :error-messages="errorsMessage.Department"
                            ></v-text-field>
                            
                            <v-autocomplete
                                v-model="form.ParentDepartment_PKID" 
                                :items="department"
                                :item-text="(item) => item.Department"
                                v-bind:label="$t('department.parentDepartment')" 
                                prepend-icon="mdi-domain"
                                color="cyan darken-1"
                                class="khmer-font"
                                item-value="PKID"   
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
                            
                            <v-textarea
                                v-model="form.Remark"
                                v-bind:label="$t('department.Remark')"
                                rows="2"
                                prepend-inner-icon="mdi-pencil"
                                class="khmer-font"
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
            </v-col>
        </v-row>

        <!-- table -->
        <v-card class="mx-auto table-card v-card v-sheet theme--ligh">
            <v-data-table
                :headers="headers"
                :items="department"
                :loading="tableLoading"
                :search="searchDepartment"
            >
                <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="editDepartment(item)">mdi-pencil</v-icon>
                    <v-icon small class="mr-2" @click="deleteDepartment(item.PKID, item.Department)">mdi-delete</v-icon>
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
                            <div class="grey--text text--darken-3 text-body-2 mb-4">
                                {{$t(
                                    'workflow.deleteMessage'
                                )}}
                                <b class="red--text tex--lighten-2">{{ departmentTitle }}</b>
                                ?
                            </div>

                            <v-btn :disabled="btnLoading" class="ma-1" depressed small @click="dialogDelete = false">
                                {{$t(
                                    'absent.btnCancel'
                                )}}
                            </v-btn>

                            <v-btn :loading="btnLoading" class="ma-1" dark color="red" small depressed
                                @click="submitDelete">
                                {{$t('absent.btnDelete')}}
                            </v-btn>
                        </v-sheet>
                    </div>
                </v-card>
            </v-dialog>

            <!-- ----------dialogWarning------------ -->
            <v-dialog v-model="dialogWarning" max-width="330px">
                <v-card>
                    <div class="text-center">
                        <v-sheet class="px-7 pt-7 pb-4 mx-auto text-center d-inline-block">
                            <v-icon class="text-center pb-3" x-large color="red lighten-2">mdi-alert</v-icon>
                            <div class="grey--text text--darken-3 text-body-2 mb-4">
                                {{ $t('department.department') }}
                                <b class="red--text tex--lighten-2">
                                    {{ departmentTitle }}
                                </b> 
                                {{$t(
                                    'department.warningMessage'
                                )}}
                                <div class="text-left red--text tex--lighten-2">
                                    <b>
                                        <span v-for="data in this.usedData">
                                        <span  v-if="data != ''"><br/>- {{ data }}</span>
                                        </span>
                                    </b>   
                                </div>   
                            </div>

                            <v-btn :disabled="btnLoading" class="ma-1" depressed small @click="dialogWarning = false">
                                {{$t(
                                    'absent.btnCancel'
                                )}}
                            </v-btn>
                        </v-sheet>
                    </div>
                </v-card>
            </v-dialog>
        </v-card>
    </div>
</template>

<script>
import store from "../store";
    export default {
        name: 'Department',
        data: ()=> ({
            openDialog: false,

            //--------header table---------
            //-------- value table --------
            department: [],
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                PKID: "",
                Department: "",
                ParentDepartment_PKID: "",
                Remark: null,
            }),
            btnLoading: false,
            dialogDelete: false,
            dialogWarning: false,
            departmentTitle: "",
            searchDepartment: "",
            departmentCount: "",
            parentDepartmentData: [],
            usedData: [],
        }),  
        computed: {
            userToken() {
                return store.state.user;
            },
            headers(){
                return[
                {text:this.$t('department.department'),value:'Department'},
                {text:this.$t('department.parentDepartment'),value:'ParentDepartment'},
                {text:this.$t('leaveEntry.tbRemark'),value:'Remark'},
                {text:this.$t('leaveEntry.tbAction'), value: "actions", align: "center", sortable: false,},
            ]
            }
        },
        mounted() {
            if(this.userToken.token){
                this.ReadDepartment();   
            }
        },
        methods: { 
            ReadDepartment() {    
                axios
                .get(this.$globalConfig.ipHost + "api/read-department", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.department = response.data.data;
                    this.departmentCount = response.data.data.length;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createDepartment() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                this.form
                .post("api/create-department", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadDepartment();
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
                this.form.PKID = "";
                this.form.Department = "";
                this.form.ParentDepartment_PKID = "";
                this.form.Remark = null;
            },
            editDepartment(item) {
                this.editMode = true;
                this.form.PKID = item.PKID;
                this.form.Department = item.Department;
                this.form.ParentDepartment_PKID = item.ParentDepartment_PKID;
                this.form.Remark = item.Remark;
                this.openDialog = true;
            },
            deleteDepartment(PKID, Department) {
                this.form.PKID = PKID;
                this.departmentTitle = Department;
                this.dialogDelete = true;
                
            },
            async updateDepartment() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-department/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadDepartment();
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
                .delete("/api/delete-department/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    if(response.data.errorMessage !== undefined) {
                        this.dialogWarning = true;
                        this.usedData = response.data.errorMessage.split(";");
                    } else {
                        this.ReadDepartment();
                        this.alertSnackbarMsg =this.$t('leaveEntry.msgDelete');
                        this.snackbar = true;
                    }
                    this.dialogDelete = false;
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