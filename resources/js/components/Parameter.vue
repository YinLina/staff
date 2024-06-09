<template>
    <div id="ParameterCode">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>

                    <v-col cols="12" sm="12" md="6">
                        <h3 class="grey--text text--darken-2 mt-5">
                            <v-icon color="grey darken-2">mdi-circle</v-icon>
                            <span class="text-decoration-underline font-weight-medium">{{$t('parameters.listParameter')}}</span>

                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="6">
                        <v-text-field

                            v-bind:label="$t('absent.search')"
                            append-icon="mdi-magnify"
                            single-line
                            v-model="searchParameter"
                           
                            class="txt-search"
                       
                        ></v-text-field>

                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" sm="6" class="text-end">
                <v-btn
                    small
                    class="font-weight-regular pa-2 khawin-background-color white--text"
                    @click="openDialogForm"
                >
                   <v-icon color="white--text">mdi-plus</v-icon>
                        {{$t('workflow.btnAdd')}}
                </v-btn>
            </v-col>
        </v-row>
        
        <v-dialog
            width="500"
            v-model="openDialog"
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
                    {{$t('parameters.frmTitle')}}
                </span>
                <span v-else class="khmer-font white--text">
                    <v-icon left color="white">mdi-circle</v-icon>
                    {{$t('parameters.frmEditTitle')}}

                </span>
                </v-toolbar>
                <form @submit.prevent="editMode ? updateParameter() : createParameter()">
                    <v-card-text>
                        <v-text-field
                            v-model="form.parameterCode"
                            class="khmer-font"
                            prepend-inner-icon="mdi-circle"
                            v-bind:label="$t('parameters.parameter')"

                            :error-messages="errorsMessage.parameterCode"
                        ></v-text-field>

                        <v-text-field
                            v-model="form.ValueOne"
                            prepend-inner-icon="mdi-numeric-1-box-multiple-outline"
                            :error-messages="errorsMessage.ValueOne"
                            v-bind:label="$t('parameters.value1')"
                            class="khmer-font"
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="form.ValueTwo"
                            v-bind:label="$t('parameters.value2')"
                            class="khmer-font"
                            prepend-inner-icon="mdi-numeric-2-box-multiple-outline"
                        ></v-text-field>

                        <v-textarea
                            v-model="form.Remark"
                            prepend-inner-icon="mdi-pencil"
                            v-bind:label="$t('absent.remark')"
                            rows="1"
                            class="khmer-font"
                            outlined
                        >
                        </v-textarea>

                        <v-row>
                            <v-col cols="12" sm="12" md="6">
                                <v-text-field
                                    v-model="form.textKH"
                                    v-bind:label="$t('parameters.txtkh')"
                                    class="khmer-font"
                                    prepend-inner-icon="mdi-translate"
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.textEN"
                                    v-bind:label="$t('parameters.txten')"
                                    class="khmer-font"
                                    prepend-inner-icon="mdi-translate"
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.textVN"
                                    v-bind:label="$t('parameters.txtvn')"
                                    class="khmer-font"
                                    prepend-inner-icon="mdi-translate"
                                ></v-text-field>
                            
                            </v-col>
                            <v-col cols="12" sm="12" md="6">
                                <v-text-field
                                    v-model="form.textTH"
                                    v-bind:label="$t('parameters.txtth')"
                                    class="khmer-font"
                                    prepend-inner-icon="mdi-translate"
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.textCH"
                                    v-bind:label="$t('parameters.txtch')"
                                    class="khmer-font"
                                    prepend-inner-icon="mdi-translate"
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.textFR"
                                    v-bind:label="$t('parameters.txtfr')"
                                    class="khmer-font"
                                    prepend-inner-icon="mdi-translate"
                                ></v-text-field>
                            </v-col>
                        </v-row>
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
            :items="parameterData"
            :loading="tableLoading"
            :search="searchParameter"
        >
            <template v-slot:[`item.actions`]="{ item }">
                <v-icon small class="mr-2" @click="editParameter(item)">mdi-pencil</v-icon>
                <v-icon small class="mr-2" @click="deleteParameter(item.PKID, item.ParameterCode)">mdi-delete</v-icon>
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
        <DeleteDialog
            :openDeleteDialog="dialogDelete"
            @deleteItem="submitDelete"
            @cancelDialog="dialogDelete = false"
            :itemTitle="parameterTitle"
        />
    </div>
</template>
<script>
    import store from "../store";
    import DeleteDialog from "./helper_components/DeleteDialog.vue";
    export default {
        name:'Parameter',
        components: { DeleteDialog },
        data:()=>({
            openDialog:false,
            parameterData: [],
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                PKID: "",
                parameterCode: "",
                ValueOne: "",
                ValueTwo: null,
                Remark: "",
                textKH: "",
                textEN: "",
                textVN: "",
                textTH: "",
                textCH: "",
                textFR: ""
            }),

            btnLoading: false,
            dialogDelete: false,
            parameterTitle: "",
            searchParameter: "",
            countParameter: "",
        }),
        computed: {
            userToken() {
                return store.state.user;
            },
            header(){
                return[
                {text: this.$t('parameters.parameter'),value:'ParameterCode'},
                {text:this.$t('parameters.value1'),value:'ValueOne'},
                {text:this.$t('parameters.value2'),value:'ValueTwo'},
                {text:this.$t('leaveEntry.tbRemark'),value:'Remark'},
                {text:this.$t('parameters.txtkh'),value:'TextKH'},
                {text:this.$t('parameters.txten'),value:'TextEN'},
                {text:this.$t('parameters.txtvn'),value:'TextVN'},
                {text:this.$t('parameters.txtth'),value:'TextTH'},
                {text:this.$t('parameters.txtch'),value:'TextCH'},
                {text:this.$t('parameters.txtfr'),value:'TextFR'},
                {text:this.$t('leaveEntry.tbAction'), sortable: false, align: "center", value:'actions'}
            ]
            }
        },
        mounted() {
            if(this.userToken.token){
                this.ReadParameter();
            }
        },
        methods: {
            ReadParameter() {
                axios
                    .get(this.$globalConfig.ipHost + "api/read-parameter", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.parameterData = response.data.data;
                    this.countParameter = response.data.data.length
                    this.tableLoading = false;
                    this.btnSaveLoading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createParameter() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                this.form
                .post(this.$globalConfig.ipHost + "api/create-parameter", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadParameter();
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
            closeDialog() {
                this.editMode = false;
                this.openDialog = false;
                this.btnSaveLoading = false;
                this.tableLoading = false;
                this.form.PKID = 0;
                this.form.parameterCode = ""
                this.form.ValueOne = ""
                this.form.ValueTwo = "";
                this.form.Remark = "";
                this.form.textKH = "";
                this.form.textEN = "";
                this.form.textVN = "";
                this.form.textTH = "";
                this.form.textCH = "";
                this.form.textFR = "";
            },
            editParameter(item) {
                this.editMode = true;
                this.form.PKID = item.PKID;
                this.form.parameterCode = item.ParameterCode;
                this.form.ValueOne = item.ValueOne;
                this.form.ValueTwo = item.ValueTwo;
                this.form.Remark = item.Remark;
                this.form.textKH = item.TextKH;
                this.form.textEN = item.TextEN;
                this.form.textVN = item.TextVN;
                this.form.textTH = item.TextTH;
                this.form.textCH = item.TextCH;
                this.form.textFR = item.TextFR;
                this.openDialog = true;

            },
            openDialogForm() {
                this.openDialog = true;
            },
            deleteParameter(PKID,parameterCode){
                this.form.PKID = PKID;
                this.parameterTitle = parameterCode;
                this.dialogDelete = true;
            },
            async updateParameter() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-parameter/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadParameter();
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
                .delete("/api/delete-parameter/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadParameter();
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
            }
        },
    }
</script>
