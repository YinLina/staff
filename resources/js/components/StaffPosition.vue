<template>
    <div id="staff_position">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                        <h3 class="grey--text text--darken-2">
                            <v-icon color="grey darken-2">mdi-account-group-outline</v-icon>
                            <span class="text-decoration-underline font-weight-medium">
                                {{$t('staffPosition.StaffPosition')}}
                            </span>
                            <v-chip color="grey grey--text text--darken-3 lighten-2">{{ count }}</v-chip>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                        <v-text-field
                            v-model="searchStaffPosition"
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
                    {{$t('staffPosition.btnAdd')}}
                </v-btn>
            </v-col>
        </v-row>
        <v-data-table
            :headers="header"
            :items="StaffPositionData"
            :loading="tableLoading"
            :search="searchStaffPosition"
        >
            <template
                v-slot:[`item.actions`]="{ item }" 
            >
                <v-icon 
                    small 
                    @click="editStaffPosition(item)"
                >
                    mdi-pencil
                </v-icon>
                <v-icon 
                    small
                    @click="deleteStaffPosition(item.PKID,item.EmployeeId)"
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
                        {{$t('menu.StaffPosition')}}
                    </span>
                </v-toolbar>
                <form
                    @submit.prevent=" editMode ? updateStaffPosition() : createStaffPosition()"
                    enctype="multipart/form-data" 
                >
                    <v-card-text>

                        <v-autocomplete
                            v-model="form.Employees_id"
                            :items="staffActiveData"
                            :item-text="(item) => item.first_name + ' ' + item.last_name"
                            item-value="id"
                            class="khmer-font"
                            clearable
                            v-bind:label="$t('staffPosition.employee')"
                            color="cyan darken-1"
                            prepend-icon="mdi-account"
                            :error-messages="errorsMessage.Employees_id"
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
                            v-model="form.Positions_id"
                            :items="positionData"
                            :item-text="(item) => item.title"
                            item-value="id"
                            class="khmer-font"
                            clearable
                            v-bind:label="$t('staffPosition.position')"
                            color="cyan darken-1"
                            prepend-icon="mdi-account-star"
                            :error-messages="errorsMessage.Positions_id"
                        >
                            <template v-slot:selection="data">
                                <v-chip
                                    label
                                    dark
                                    color="blue-grey darken-2"
                                    class="text-capitalize pa-1 font-weight-regular"
                                    small
                                >
                                    {{ data.item.title }}
                                </v-chip>
                            </template>
                        </v-autocomplete>

                        <div>    
                            <v-menu
                                :close-on-content-click="false"
                                max-width="290"
                                v-model="ChoseStartDate"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        :value="StartDateFormat"
                                        v-on="on"
                                        v-bind="attrs"
                                        class="khmer-font"
                                        color="cyan darken-1"
                                        v-bind:label="$t('staffPosition.startDate')"
                                        prepend-icon="mdi-calendar"
                                        :error-messages="errorsMessage.StartDate"
                                    ></v-text-field>
                                </template>
                                    <v-date-picker
                                        @input="ChoseStartDate = false"
                                        color="cyan darken-1"
                                        v-model="form.StartDate"
                                    >
                                    </v-date-picker>
                                </v-menu>
                            </div>

                            <div>    
                            <v-menu
                                :close-on-content-click="false"
                                max-width="290"
                                v-model="ChoseEndDate"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        :value="EndDateFormat"
                                        v-on="on"
                                        v-bind="attrs"
                                        class="khmer-font"
                                        color="cyan darken-1"
                                        v-bind:label="$t('staffPosition.endDate')"
                                        prepend-icon="mdi-calendar"
                                    ></v-text-field>
                                </template>
                                    <v-date-picker
                                        @input="ChoseEndDate = false"
                                        color="cyan darken-1"
                                        v-model="form.EndDate"
                                    >
                                    </v-date-picker>
                                </v-menu>
                            </div>
                            
                            <v-textarea
                                v-model="form.Remark"
                                prepend-inner-icon="mdi-pencil"
                                class="khmer-font"
                                v-bind:label="$t('absent.remark')"
                                rows="1"
                                outlined
                            ></v-textarea>
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
                        color="khawin-background-color"
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
                            <b class="red--text tex--lighten-2">{{ staffPositionDialog }}</b>
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
    </div>
</template>

<script>
    import moment from 'moment';
    import store from '../store';
    import DeleteDialog from './helper_components/DeleteDialog.vue';
    import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
    export default {
        components: { DeleteDialog, AlertMessageDialog },
        name:'StaffPosition',
        data:()=>({
            openDialog:false,
            positionData:[],
            staffActiveData: [],
            StaffPositionData: [],
            count: "",
            tableLoading:false,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackBarMsg: "",
            snackBar: false,
            form: new Form({
                PKID: "",
                Employees_id: "",
                Positions_id: "",
                StartDate: "",
                EndDate: "",
                Remark:"",
            }),
            dialogDelete: false,
            btnLoading: false,
            searchStaffPosition: "",
            openDeleteDialog: false,
            staffPositionDialog: "",
            addItemObj: {
                StartDate: "",
                EndDate: "",
            },
            ChoseStartDate: false,
            ChoseEndDate: false,
            alertMessage: {
                openAlertMsg: false,
                msgDialog: "",
            },
            
        }),
        computed:{
            userToken() {
                return store.state.user;
            },

            StartDateFormat(){
                return this.form.StartDate ? moment(this.form.StartDate).format("DD/MM/YYYY") : ""; 
            },
            EndDateFormat(){
                return this.form.EndDate ? moment(this.form.EndDate).format("DD/MM/YYYY") : ""; 
            },
            header(){
                return [
                    {text: this.$t('staffPosition.employee'), value:'EmployeeId'},
                    {text: this.$t('staffPosition.position'), value:'PositionId'},
                    {text: this.$t('staffPosition.startDate'), value:'StartDate'},
                    {text: this.$t('staffPosition.endDate'), value:'EndDate'},
                    {text: this.$t('leaveEntry.tbRemark'), value:'Remark'},
                    {text: this.$t('leaveEntry.tbAction'), value:'actions',sortable:false, align:"center"},
                ]
            },
        },
        mounted(){

            if(this.userToken.token){
                this.ReadStaffActive();
                this.ReadPosition();
                this.ReadStaffPosition();
            }
        },
        methods:{
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
            ReadPosition() {
            axios
                .get(this.$globalConfig.ipHost + "api/read-position", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.positionData = response.data.data;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            ReadStaffPosition(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-staff-position", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((Response) => {
                    this.StaffPositionData = Response.data.data
                    this.count = Response.data.data.length
                    })
                .catch((error) => {
                    console.log(error)
                })
            },
            createStaffPosition(){
                this.tableLoading = true
                this.btnSaveLoading = true
                this.form
                .post("api/create-staff-position",{
                    headers:{ Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
                })
                .then((response) =>{
                    this.ReadStaffPosition()
                    this.closeDialog()
                    this.alertSnackBarMsg = this.$t('leaveEntry.msgSaved')
                    this.snackBar = true
                    this.btnSaveLoading = false
                    this.tableLoading = false
                })
                .catch((errors) =>{
                    this.btnSaveLoading = false
                    this.tableLoading = false
                    this.errorsMessage = errors.response.data.errors    
                })
            },
            
            closeDialog(){
                this.editMode = false
                this.btnSaveLoading = false
                this.openDialog = false
                this.form.PKID = "";
                this.form.Employees_id = "";
                this.form.Positions_id = "";
                this.form.StartDate = "";
                this.form.EndDate = "";
                this.form.Remark = "";
                this.form.StaffPositionData = [];
                this.errorsMessage = "";
            },
        
            editStaffPosition(item) {
                this.editMode = true
                this.form.PKID = item.PKID;
                this.form.Employees_id = item.Employees_id;
                this.form.Positions_id = item.Positions_id;
                this.form.StartDate = item.StartDate;
                this.form.EndDate = item.EndDate;
                this.form.Remark = item.Remark;
                this.openDialog = true
            },
            async updateStaffPosition() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-staff-position/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadStaffPosition();
                    this.closeDialog();
                    this.alertSnackBarMsg = this.$t('leaveEntry.msgUpdate')
                    this.snackBar = true;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                })
                .catch((errors) => {
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                });
            },
            deleteStaffPosition(id, EmployeeId) {
                this.form.PKID = id;
                this.staffPositionDialog = EmployeeId;
                this.dialogDelete = true;
            },
            async submitDelete() {
                this.btnLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                axios
                    .delete("/api/delete-staff-position/" + this.form.PKID, {
                        headers: {
                            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                        },
                    })
                    .then((response) => {
                        this.ReadStaffPosition();
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
