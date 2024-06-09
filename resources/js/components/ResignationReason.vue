<template>
    <div id="resignationReason">
        <v-row>
          <v-col cols="12" sm="6">
             <v-row>
                <v-col cols="12" sm="12" md="5">
                   <h3 class="red-lighten-3 font-weight-bold">
                      <v-icon color="grey darken-2" class="mb-1">mdi-account-minus</v-icon>
                      <span class="text-decoration-underline font-weight-medium">{{$t('resignationReason.resignReason')}}</span>
                      <v-chip color="grey lighten-2 grey--text text--darken-3">{{ ResignationReasonCount }}</v-chip>
                   </h3>
                </v-col>
                <v-col cols="12" sm="12" md="7">
                   <v-text-field
                      v-model="searchResignationReason"
                      append-icon="mdi-magnify"
                      single-line
                      class="txt-search"
                      v-bind:label="$t('absent.search')"
                   ></v-text-field>
                </v-col>
             </v-row>
          </v-col>

          <v-col cols="12" sm="6" class="text-end">
            <!-- Modify by Lina 05/12/2023 #cms-108 -->
            <v-btn class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                small
                @click="excelBtnOnClick"
                >
                   {{$t('resignationReason.exportExcel')}}
            </v-btn>
            <v-btn class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                small
                @click="pdfBtnOnClick"
                >
                   {{$t('resignationReason.exportPDF')}}
            </v-btn>

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
                width="500"
                persistent>
                <v-card>
                    <v-toolbar
                    dense
                    flat
                    color="lighten-1"
                    class="khawin-background-color"
                    >
                        <span v-if="editMode === false" class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-minus</v-icon>
                            {{$t('resignationReason.addFrm')}}
                        </span>
                        <span v-else class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-minus</v-icon>
                            {{$t('resignationReason.editFrm')}}
                        </span>
                    </v-toolbar> 
                <form @submit.prevent="editMode ? updateResignationReason() : createResignationReason()">
                   <v-card-text>    
                    <v-text-field
                        v-model="form.ReasonCode"
                        prepend-icon="mdi-account-multiple"
                        class="khmer-font"
                        v-bind:label="$t('resignationReason.reasonCode')"
                        clearable
                        :error-messages="errorsMessage.ReasonCode"
                    ></v-text-field>
                    <v-text-field
                        v-model="form.Description"
                        prepend-icon="mdi-account-details"
                        class="khmer-font"
                        v-bind:label="$t('resignationReason.description')"
                        clearable
                        :error-messages="errorsMessage.Description"
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
          
        <!-- table -->
        <template>
            <JqxGrid ref="resignationReasonGrid" :width="'100%'" :source="dataAdapter" 
                :columns="columns" :pageable="true" :editable="false" @celldoubleclick="onCelldoubleclick"></JqxGrid>
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
                            <b class="red--text tex--lighten-2">{{ resignationReasonTitle }}</b>
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
import JqxButton from "jqwidgets-scripts/jqwidgets-vue/vue_jqxbuttons.vue";
export default{
    components: {JqxGrid, JqxButton, DeleteDialog, AlertMessageDialog},
    name:'StaffResignation',
    data() {
        var self = this;
        return {
            openDialog: false, 
            ResignationReasonCount: "", 
            ResignationReasonData: [], 
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                PKID: "",
                ReasonCode: "",
                Description: "",
            }),
            btnLoading: false,
            dialogDelete: false,
            resignationReasonTitle: "",
            searchResignationReason: "",
            dataAdapter: new jqx.dataAdapter({
                localdata: [],
                ResignationReasonData: [],
                datatype: 'array',
                datafields:
                [
                    { name: 'PKID', type: 'int' },
                    { name: 'ReasonCode', type: 'string' },
                    { name: 'Description', type: 'string' },
                   
                ],    
            }),
            columns: [
                {text: this.$t('resignationReason.reasonCode'), datafield: 'ReasonCode', width: '50%', align: 'center', editable: false },
                {text: this.$t('resignationReason.description'), datafield:'Description', width: '50%', align:"center", editable: false },
            ]
        }                 
    },
    computed: {
        userToken() {    
            return store.state.user;
        },
    },
    mounted() {    
        if (this.userToken.token){
            this.ReadResignationReason();   
        }
    },
    methods: {
        //Modify by Lina 05/12/2023 #cms-108
        excelBtnOnClick: function () {
            this.$refs.resignationReasonGrid.exportdata('xls', 'jqxGrid');
        },
        pdfBtnOnClick: function () {
            this.$refs.resignationReasonGrid.exportdata('pdf', 'jqxGrid');
        },
        //----------------------------------
        ReadResignationReason() {       
            axios
            .get(this.$globalConfig.ipHost + "api/read-resignation-reason", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {    
                this.ResignationReasonData = response.data.data;
                this.dataAdapter._source.localdata = this.ResignationReasonData;
                    if(this.$refs.resignationReasonGrid !== undefined) {
                        this.$refs.resignationReasonGrid.height = this.$helper.GetGridHeight();
                        this.$refs.resignationReasonGrid.updatebounddata('cell');
                    }
                this.ResignationReasonCount = response.data.data.length;
                this.tableLoading = false;
                
            })
            .catch((error) => {   
                console.log(error);
            });
        },

        createResignationReason() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form
            .post("api/create-resignation-reason", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadResignationReason();
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
            this.form.ReasonCode = "";
            this.form.Description = "";
            this.errorsMessage = "";
        },

        editResignationReason(item) {
            this.editMode = true;
            this.form.PKID = item.PKID;
            this.form.ReasonCode = item.ReasonCode;
            this.form.Description = item.Description;
            this.openDialog = true;
        },

        onCelldoubleclick() {
            var dataItem = this.$helper.GetSelectedDataItem(this.$refs.resignationReasonGrid);
            this.editResignationReason(dataItem);
        },

        deleteResignationReason(id, ReasonCode) {
            this.form.PKID = id;
            this.resignationReasonTitle = ReasonCode;
            this.dialogDelete = true;
        },

        async updateResignationReason() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
            .post("/api/update-resignation-reason/" + this.form.PKID, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadResignationReason();
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
                .delete("/api/delete-resignation-reason/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadResignationReason();
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
    },
}
</script>
