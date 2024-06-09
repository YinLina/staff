<template>
    <div id="holiday_menu">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                        <h3 class="grey--text text--darken-2">
                            <v-icon color="grey darken-2">mdi-calendar</v-icon>
                            <span class="text-decoration-underline font-weight-medium">
                                {{$t('Holiday.holiday')}}
                            </span>
                            <v-chip color="grey grey--text text--darken-3 lighten-2">{{ count }}</v-chip>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                        <v-text-field
                            v-model="searchHoliday"
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
                    {{$t('Holiday.btnAddHoliday')}}
                </v-btn>
            </v-col>
        </v-row>
        <v-data-table
            :headers="header"
            :items="HolidayData"
            :loading="tableLoading"
            :search="searchHoliday"
        >
            <template
                v-slot:[`item.actions`]="{ item }" 
            >
                <v-icon 
                    small 
                    @click="editHoliday(item)"
                >
                    mdi-pencil
                </v-icon>
                <v-icon 
                    small
                    @click="deleteHoliday(item.PKID,item.Description)"
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
                        <v-icon color="white" left>mdi-calendar</v-icon>
                        {{$t('menu.Holiday')}}
                    </span>
                </v-toolbar>
                <form
                    @submit.prevent=" editMode ? updateHoliday() : createHoliday()"
                    enctype="multipart/form-data" 
                >
                    <v-card-text>
                        <v-text-field
                            v-model="form.Description"
                            class="khmer-font"
                            prepend-icon="mdi-account"
                            v-bind:label="$t('Holiday.Description')"
                            :error-messages="errorMessage.Description"
                        >
                        </v-text-field>
                        <div>    
                            <v-menu
                                :close-on-content-click="false"
                                max-width="290"
                                v-model="ChoseHolidayDate"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        :value="DateFormat"
                                        v-on="on"
                                        v-bind="attrs"
                                        class="khmer-font"
                                        color="cyan darken-1"
                                        v-bind:label="$t('Holiday.HolidayDate')"
                                        prepend-icon="mdi-calendar"
                                        :error-messages="errorMessage.HolidayDate"
                                    ></v-text-field>
                                </template>
                                    <v-date-picker
                                        @input="ChoseHolidayDate = false"
                                        color="cyan darken-1"
                                        v-model="form.HolidayDate"
                                    >
                                    </v-date-picker>
                                </v-menu>
                            </div>

                        <v-checkbox
                            v-model="form.Allowed"
                            v-bind:label="$t('Holiday.Allowed')">
                        </v-checkbox>
                        
                        <v-autocomplete
                                v-model="form.ParameterPKID"
                                :items="ParameterData"
                                :item-text="(item) => item.HolidayType"
                                item-value="HolidayTypeID"
                                class="khmer-font"
                                clearable
                                v-bind:label="$t('Holiday.HolidayType')"
                                color="cyan darken-1"
                                prepend-icon="mdi-text-box-edit"
                                :error-messages="errorMessage.ParameterPKID"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        label
                                        dark
                                        color="blue-grey darken-2"
                                        class="text-capitalize pa-1 font-weight-regular"
                                        small
                                    >
                                        {{ data.item.HolidayType }}
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
                            <b class="red--text tex--lighten-2">{{ holidayDialog }}</b>
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
        name:'Holiday',
        data:()=>({
            openDialog:false,
            DataForm:[],
            HolidayData:[],
            count: "",
            tableLoading:false,
            btnSaveLoading: false,
            editMode: false,
            errorMessage: "",
            alertSnackBarMsg: "",
            snackBar: false,
            form: new Form({
                PKID: "",
                Description: "",
                HolidayDate: "",
                Allowed: true,
                HolidayType: "",
                Remark:"",
                DataForm: [],
                ParameterPKID: "",
            }),
            dialogDelete: false,
            btnLoading: false,
            searchHoliday: "",
            openDeleteDialog: false,
            holidayDialog: "",
            addItemObj: {
                HolidayDate: "",
            },
            ChoseHolidayDate: false,
            alertMessage: {
                openAlertMsg: false,
                msgDialog: "",
            },
            ParameterData:[],
            
        }),
        computed:{
            userToken() {
                return store.state.user;
            },

            DateFormat(){
                return this.form.HolidayDate ? moment(this.form.HolidayDate).format("DD/MM/YYYY") : ""; 
            },
            header(){
                return [
                    {text: this.$t('Holiday.Description'), value:'Description'},
                    {text: this.$t('Holiday.HolidayDate'), value:'HolidayDate'},
                    {text: this.$t('Holiday.Allowed'), value:'Allowed'},
                    {text: this.$t('Holiday.HolidayType'), value:'HolidayTypeStr',},
                    {text: this.$t('leaveEntry.tbRemark'), value:'Remark'},
                    {text: this.$t('leaveEntry.tbAction'), value:'actions',sortable:false, align:"center"},
                ]
            },
        },
        mounted(){

            if(this.userToken.token){
                this.ReadParameter();
                this.ReadHoliday();
            }
        },
        methods:{
            ReadParameter(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-holidayType-for-combobox",{
                    headers: { Authorization: "Bearer " + sessionStorage.getItem("TOKEN")}
                })
                .then((response) => {
                    this.ParameterData = response.data.data
                })
                .catch((error) => {
                    console.log(error)
                })
            },
            ReadHoliday(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-holiday", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((Response) => {
                    this.HolidayData = Response.data.data
                    // Loop HolidayData 
                    for(var i = 0; i < this.HolidayData.length; i++) {
                        // Check id of holidayType in parameterdata and return as a new array
                        var holidayIds = this.ParameterData.map(function(item) {
                            return item.HolidayTypeID
                            // Return new array as id in holidayType
                        });
                        
                        var index = holidayIds.indexOf(this.HolidayData[i].HolidayType);
                        // Compare index and id of HolidayType
                        if(index > -1) {
                            // Return string of HolidayType if string == index
                            this.HolidayData[i].HolidayTypeStr = this.ParameterData[index].HolidayType;
                        }
                    }
                    this.count = Response.data.data.length
                    })
                .catch((error) => {
                    console.log(error)
                })
            },
            createHoliday(){
                this.tableLoading = true
                this.btnSaveLoading = true
                this.form
                .post("api/create-holiday",{
                    headers:{ Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
                })
                .then((response) =>{
                    this.ReadHoliday()
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
                })
            },
            
            closeDialog(){
                this.editMode = false
                this.btnSaveLoading = false
                this.openDialog = false
                this.form.PKID = 0;
                this.form.Description = "";
                this.form.HolidayDate = "";
                this.form.Allowed = true;
                this.form.HolidayType = "";
                this.form.Remark = "";
                this.form.HolidayData = [];
                this.form.DataForm = [];
                this.errorMessage = "";
                this.form.ParameterPKID = "";
            },
        
            editHoliday(item) {
                this.editMode = true
                this.form.PKID = item.PKID;
                this.form.Description = item.Description;
                this.form.HolidayDate = item.HolidayDate;
                this.form.Allowed = item.Allowed;
                this.form.ParameterPKID = item.HolidayType;
                this.form.Remark = item.Remark;
                this.openDialog = true
            },
            async updateHoliday() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-holiday/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadHoliday();
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
            deleteHoliday(id, Description) {
                this.form.PKID = id;
                this.holidayDialog = Description;
                this.dialogDelete = true;
            },
            async submitDelete() {
                this.btnLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                axios
                    .delete("/api/delete-holiday/" + this.form.PKID, {
                        headers: {
                            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                        },
                    })
                    .then((response) => {
                        this.ReadHoliday();
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
