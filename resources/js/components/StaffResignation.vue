<template>
    <div id="staffResignation">
        <v-row>
          <v-col cols="12" sm="6">
             <v-row>
                <v-col cols="12" sm="12" md="5">
                   <h3 class="red-lighten-3 font-weight-bold">
                      <v-icon color="grey darken-2" class="mb-1">mdi-account-arrow-left</v-icon>
                      <span class="text-decoration-underline font-weight-medium">{{$t('staffResignation.staffResign')}}</span>
                      <v-chip color="grey lighten-2 grey--text text--darken-3">{{ StaffResignationCount }}</v-chip>
                   </h3>
                </v-col>
                <v-col cols="12" sm="12" md="7">
                   <v-text-field
                      v-model="searchStaffResignation"
                      append-icon="mdi-magnify"
                      single-line
                      class="txt-search"
                      v-bind:label="$t('absent.search')"
                   ></v-text-field>
                </v-col>
             </v-row>
          </v-col>

          <v-col cols="12" sm="6" class="text-end">
            <!-- Modify by Lina 23/01/2024 #cms-107 -->
            <v-btn class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                small
                @click="excelBtnOnClick"
                >
                   {{$t('resignationReason.exportExcel')}}
            </v-btn>
            <v-btn class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                small
               @click="openDialogForm"
               >
                <v-icon left>mdi-plus</v-icon>
                   {{$t('document.btnAdd')}}
            </v-btn>
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
                        <span v-if="editMode === false" class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-arrow-left</v-icon>
                            {{$t('staffResignation.addFrm')}}
                        </span>
                        <span v-else class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-arrow-left</v-icon>
                            {{$t('staffResignation.editFrm')}}
                        </span>
                    </v-toolbar> 
                <form @submit.prevent="editMode ? updateStaffResignation() : createStaffResignation()">
                   <v-card-text>    
                    <v-autocomplete
                        v-model="form.Employee_PKID" 
                        :items="employeeData"
                        :item-text="(item) => item.first_name+ ' ' + item.last_name"
                        item-value="id"
                        v-bind:label="$t('staffResignation.employeePKID')" 
                        prepend-icon="mdi-account"
                        color="cyan darken-1"
                        class="khmer-font"
                        clearable
                        :error-messages="errorsMessage.Employee_PKID"
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
                        v-model="form.ResignationReason_PKID" 
                        :items="resignationReasonData"
                        :item-text="(item) => item.Description"
                        item-value="PKID"
                        v-bind:label="$t('staffResignation.resignationReasonPKID')" 
                        prepend-icon="mdi-account-arrow-left"
                        color="cyan darken-1"
                        class="khmer-font"
                        clearable
                        :error-messages="errorsMessage.ResignationReason_PKID"   
                    >
                        <template v-slot:selection="data">
                            <v-chip
                                label
                                dark
                                color="blue-grey darken-2"
                                class="text-capitalize pa-1 font-weight-regular"
                                small
                                >
                                {{ data.item.Description }}
                            </v-chip>
                        </template>
                    </v-autocomplete>

                    <v-text-field
                        v-model="form.Description"
                        prepend-icon="mdi-account-details"
                        class="khmer-font"
                        v-bind:label="$t('staffResignation.description')"
                        clearable
                        :error-messages="errorsMessage.Description"
                    ></v-text-field>

                    <div>
                        <v-menu :close-on-content-click="false" max-width="290" v-model="ChoseResignedDate">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    :value="DateFormat"
                                    v-on="on"
                                    v-bind="attrs"
                                    class="khmer-font"
                                    color="cyan darken-1"
                                    v-bind:label="$t('staffResignation.resignedDate')"
                                    prepend-icon="mdi-calendar"
                                    clearable
                                    :error-messages="errorsMessage.ResignedDate"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                @input="ChoseResignedDate = false"
                                color="cyan darken-1"
                                v-model="form.ResignedDate">
                            </v-date-picker>
                        </v-menu>
                    </div>
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
        <!-- <v-card class="mx-auto table-card v-card v-sheet theme--ligh"> -->
            <!-- <v-data-table
                :headers = "headers"
                :items = "StaffResignationData"
                :loading="tableLoading"
                :search="searchStaffResignation"
            >
            <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="editStaffResignation(item)">mdi-pencil</v-icon>
                    <v-icon small class="mr-2" @click="deleteStaffResignation(item.PK_ID, item.LeaveReason)">mdi-delete</v-icon>
            </template>
            </v-data-table> -->
        <!-- </v-card> -->
        
        <template>
            <JqxGrid ref="staffResignationGrid" :width="'100%'" :source="dataAdapter" :columns="columns" :pageable="true" :autoheight="false" :editable="false" @celldoubleclick="onCelldoubleclick"></JqxGrid>
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

        <!-- ----------dialogDelete------------ -->
        <v-dialog v-model="dialogDelete" max-width="330px">
            <v-card>
                <div class="text-center">
                    <v-sheet class="px-7 pt-7 pb-4 mx-auto text-center d-inline-block">
                        <v-icon class="text-center pb-3" x-large color="red lighten-2">mdi-alert</v-icon>
                        <div class="grey--text text--darken-3 text-body-2 mb-4 khmer-font">
                            {{$t('workflow.deleteMessage')}}
                            <b class="red--text tex--lighten-2">{{ staffResignationTitle }}</b>
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

<style>
    @import "jqwidgets-scripts/jqwidgets/styles/jqx.base.css";
</style>

<script>
import store from "../store";
import moment from "moment";
import DeleteDialog from './helper_components/DeleteDialog.vue';
import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
export default{
    components: { DeleteDialog, AlertMessageDialog, JqxGrid },
    name:'StaffResignation',
    data() {
        var self = this;
        return {
            openDialog: false, 
            StaffResignationCount: "", 
            resignationReasonData: [], 
            employeeData: [],
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                PKID: "",
                Employee_PKID: "",
                ResignationReason_PKID: "",
                Description: "",
                ResignedDate: "",
            }),
            btnLoading: false,
            dialogDelete: false,
            ChoseResignedDate: false,
            staffResignationTitle: "",
            searchStaffResignation: "",
            dataAdapter: new jqx.dataAdapter({
                localdata: [],
                StaffResignationData: [],
                datatype: 'array',
                datafields:
                [
                    { name: 'PKID', type: 'int' },
                    { name: 'Employee_PKID', type: 'int' },
                    { name: 'EmployeeName', type: 'string' },
                    { name: 'ResignationReason_PKID', type: 'int' },
                    { name: 'ResignationReason', type: 'string'},
                    { name: 'Description', type: 'string' },
                    { name: 'ResignedDate', type: 'date' },
                    
                ],    
            }),
            columns: [
                {text: this.$t('staffResignation.employeePKID'), datafield: 'EmployeeName', width: '25%', align: 'center' },
                {text: this.$t('staffResignation.resignationReasonPKID'), datafield:'ResignationReason', width: '25%', align:"center",},
                {text: this.$t('staffResignation.description'), datafield:'Description', width: '25%', align:"center",},
                {text: this.$t('staffResignation.resignedDate'), datafield:'ResignedDate', width: '25%', align:"center",
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
            ]
        }                 
    },
    computed: {
        userToken() {    
            return store.state.user;
        },
        DateFormat(){
            debugger;
                return this.form.ResignedDate ? moment(this.form.ResignedDate).format("DD/MM/YYYY") : ""; 
            },
    },
    mounted() {    
        if (this.userToken.token){
            this.ReadStaffResignation();
            this.ReadStaffActive();
            this.ReadStaffResignationReason();
        }
    },
    methods: {
        //Modify by Lina 23/01/2024 #cms-107
        excelBtnOnClick: function () {
            this.$refs.staffResignationGrid.exportdata('xls', 'jqxGrid');
        },

        ReadStaffResignationReason(){
            axios
            .get(this.$globalConfig.ipHost + "api/read-staff-resignation-reason", {
            headers: {
                Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
            },
            })
            .then((response) => {
                this.resignationReasonData = response.data.resignationReason;
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
        ReadStaffResignation() {       
            axios
            .get(this.$globalConfig.ipHost + "api/read-staff-resignation", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {    
                this.StaffResignationData = response.data.data;
                this.dataAdapter._source.localdata = this.StaffResignationData;
                    if(this.$refs.staffResignationGrid !== undefined) {
                        this.$refs.staffResignationGrid.height = this.$helper.GetGridHeight();
                        this.$refs.staffResignationGrid.updatebounddata('cell');
                    }
                this.StaffResignationCount = response.data.data.length;
                this.tableLoading = false;
                
            })
            .catch((error) => {   
                console.log(error);
            });
        },

        createStaffResignation() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form.ResignedDate = this.$helper.ConvertDateToStr(this.form.ResignedDate);
            this.form
            .post("api/create-staff-resignation", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadStaffResignation();
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
            this.form.Employee_PKID = "";
            this.form.ResignationReason_PKID = "";
            this.form.Description = "";
            this.form.ResignedDate = "";
            this.errorsMessage = "";
        },

        editStaffResignation(item) {
            this.editMode = true;
            this.form.PKID = item.PKID;
            this.form.Employee_PKID = item.Employee_PKID;
            this.form.ResignationReason_PKID = item.ResignationReason_PKID;
            this.form.Description = item.Description;
            this.form.ResignedDate = this.$helper.ConvertDateToStr(item.ResignedDate);
            this.openDialog = true;
        },

        onCelldoubleclick() {
            var dataItem = this.$helper.GetSelectedDataItem(this.$refs.staffResignationGrid);
            this.editStaffResignation(dataItem);
        },

        deleteStaffResignation(id, EmployeeName) {
            this.form.PKID = id;
            this.staffResignationTitle = EmployeeName;
            this.dialogDelete = true;
        },

        async updateStaffResignation() {
            debugger
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form.ResignedDate = this.$helper.ConvertDateToStr(this.form.ResignedDate);
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
            .post("/api/update-staff-resignation/" + this.form.PKID, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadStaffResignation();
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
                .delete("/api/delete-staff-resignation/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadStaffResignation();
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
