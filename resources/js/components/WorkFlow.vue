<template>
    <div id="employee">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                        <h3 class="grey--text text-darken-2">
                            <v-icon left class="mb-1" color="grey darken-2">mdi-clipboard-flow</v-icon>
                            <span class="text-decoration-underline font-weight-medium">{{$t('workflow.listWorkflow')}}</span>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                        <v-text-field
                            v-model="search"
                            single-line
                            append-icon="mdi-magnify"
                            v-bind:label="$t('absent.search')"
                            class="txt-search"
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" sm="6" class="text-end">
                <v-btn
                    small
                    class="font-weight-regular white--text khawin-background-color"
                    depressed
                    @click="openDialogForm"
                >
                    <v-icon left>mdi-plus</v-icon>
                    {{$t('workflow.btnAdd')}}
                </v-btn>
            </v-col>
        </v-row>
        <v-tabs v-model="tab">
            <v-tabs-slider color="transparent"></v-tabs-slider>
            <v-tab class="text-capitalize tab" ref="tab1">
                <v-icon left>mdi-clipboard-flow</v-icon>
                <span class="font-weight-medium">{{$t('workflow.listWorkflow')}}</span>
                <v-chip class="ml-2 font-weight-medium blue-grey--text text--darken-2">{{ countWorkflow }}</v-chip>
            </v-tab>
            <v-tab class="text-capitalize tab" ref="tab2">
                <v-icon left>mdi-account-group</v-icon>
                <span class="font-weight-mdium">{{$t('workflow.group')}}</span>
                <v-chip class="ml-2 font-weight-medium blue-grey--text text--darken-2">{{ countGroup }}</v-chip>
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
        <v-tabs-items v-model="tab">
            <v-tab-item>
                <v-card class="mx-auto table-card">
                    <v-data-table
                        :headers="workflowHeader"
                        :items="workflowData"
                        :loading="tableLoading"
                        :search="search"
                    >
                        <template v-slot:[`item.actions`] = " { item } ">
                            <v-icon small @click="editWorkflow(item)">mdi-pencil</v-icon>
                            <v-icon small @click="deleteWork(item.PKID, item.WorkFlowName)">mdi-delete</v-icon>
                        </template>
                    </v-data-table>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-card class="mx-auto table-card">
                    <v-data-table
                        :headers="groupHeader"
                        :items="groupData"
                        :loading="tableLoading"
                        :search="search"
                    >
                    <template
                        v-slot:[`item.actions`] = "{ item }"
                    >
                        <v-icon small @click="editGroup(item)">mdi-pencil</v-icon>
                        <v-icon small @click="deleteGroup(item.PKID,item.GroupName)">mdi-delete</v-icon>
                    </template>

                    </v-data-table>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
        <v-dialog
            v-model="openDialog" 
            width="500"
            persistent
            overlay-opacity="0.5"
        >
            <v-card>
                <v-toolbar
                    dense
                    flat
                    color="lighten-1"
                    class="khawin-background-color"
                >
                    <span v-if="editMode === false" class="khmer-font white--text">
                        <v-icon left color="white">mdi-clipboard-flow</v-icon>
                        {{$t('workflow.frmTitle')}}
                    </span>
                    <span v-else class="khmer-font white--text">
                        <v-icon left color="white">mdi-account-edit</v-icon>
                        {{$t('workflow.frmEditTitle')}}
                    </span>
                </v-toolbar>
                <!-- combobox -->

                <form @submit.prevent="editMode ? updateWorkFlow() : createWorkflow()"
                    enctype="multipart/form-data"
                >
                    <v-card-text>
                        <v-autocomplete
                            color="cyan darken-1"
                            v-bind:label="$t('parameters.txtValidation')"
                            class="khmer-font"
                            clearable
                            prepend-icon="mdi-comment-text-outline"
                            :error-messages="errorMessage.ValidationTypeID"
                            :items="ValidationTypeData"
                            :item-text="(item) => item.ValidationType"
                            item-value="PKID"
                            v-model="form.ValidationTypeID"
                            @change="selectValidationType()"
                            :disabled="editMode"
                        >
                        <template v-slot:selection="data">
                            <v-chip
                                dark
                                color="blue-grey darken-2"
                                class="text-capitalize pa-1 font-weight-regular"
                                small
                            >
                                {{data.item.ValidationType}}
                            </v-chip>
                        </template>
                    </v-autocomplete>
                        <v-text-field
                            prepend-icon="mdi-format-list-numbered"
                            class="khmer-font"
                            v-bind:label="$t('workflow.workflowCode')"
                            v-model="form.WorkFlowCode"
                            :error-messages="errorMessage.WorkFlowCode"
                        ></v-text-field>
                        <v-text-field
                            v-model="form.WorkFlowName"
                            v-bind:label="$t('workflow.workflowName')"
                            class="khmer-font"
                            prepend-icon="mdi-clipboard-flow-outline"
                            :error-messages="errorMessage.WorkFlowName"
                        ></v-text-field>
                        <v-textarea
                            v-bind:label="$t('absent.remark')"
                            class="khmer-font mb-0 pb-0"
                            prepend-inner-icon="mdi-pencil"
                            outlined
                            rows="1"
                            v-model="form.Remark"
                        ></v-textarea>
                        <span small class="red--text font-weight-bold khmer-font font-size-small mt-0 mb-3" 
                            v-if="chPosition.openError"
                        >
                        {{chPosition.ErrorMsg}}
                        </span>
                        <span small class=" khmer-font font-size-small blue-grey--text mt-0 mb-3" 
                            v-else
                        >
                            {{this.$t('workflow.labelPosition')}}
                        </span>
                        <v-card 
                            height="80px" 
                            class="overflow-y-auto"
                            outlined
                        >
                            <div
                                v-for="(item) in PositionData"
                                :key="item.id"
                                v-scroll
                            >
                                <input type="checkbox" 
                                    v-model="form.positionId"
                                    :value="item.id"
                                    :error-messages="errorMessage.positionId"
                                >
                                <label>{{ item.title }}</label>
                            </div>  
                        </v-card>
                        <v-row>
                            <v-col cols="10">
                                <span small class="red--text font-weight-bold khmer-font " 
                                    v-if="chDateTime.openError"
                                >
                                {{chDateTime.ErrorMsg}}
                                </span>
                                <span 
                                    small 
                                    class=" khmer-font font-size-small blue-grey--text " 
                                    v-else
                                >
                                {{this.$t('workflow.labelWorkflowGroup')}}
                                </span>
                            </v-col>
                            <v-col cols="2">
                                <div class="text-end">
                                    <v-btn
                                        small
                                        depressed
                                        color="khawin-background-color khmer-font mb-1"
                                        @click="addRowTable"
                                    >
                                        <v-icon small color="white">mdi-plus</v-icon>
                                    </v-btn>
                                </div>
                            </v-col>
                        </v-row>
                        <v-data-table
                            :headers="formTable"
                            :items="form.dataFormTable"
                            class="khmer-font overflow-y-auto"
                            :hide-default-footer="true"
                            height="115"
                            fixed-header
                        >
                            <template v-slot:[`item.group`]="{ item }">
                                <span v-if="item.id === editedItem.id" id="cmbGroupId">
                                    <v-autocomplete
                                        v-model="editedItem.groupId"
                                        :items="groupData"
                                        :item-text="(item) => item.GroupCode"
                                        item-value="PKID"
                                        class="khmer-font"
                                        clearable
                                        color="cyan darken-1"
                                        @change="selectCmbGroup"
                                    >
                                        <template v-slot:selection="data">
                                            <v-chip
                                                label
                                                dark
                                                color="blue-grey darken-2"
                                                class="text-capitalize pa-1 font-weight-regular"
                                                small
                                            >
                                                {{ data.item.GroupCode }}
                                            </v-chip>
                                        </template>
                                    </v-autocomplete>
                                </span>
                                <span v-else>{{item.groupName}}</span>
                            </template>
                            <template v-slot:[`item.order`]="{ item }">
                                <v-text-field v-model="editedItem.order" :hide-details="true" dense single-line v-if="item.id === editedItem.id" class="centered-input" disabled></v-text-field>
                                <span class="centeredText" v-else>{{item.order}}</span>
                            </template>
                            <template v-slot:[`item.user`]="{ item }">
                                <v-text-field v-model="editedItem.user" :hide-details="true" dense single-line v-if="item.id === editedItem.id" disabled></v-text-field>
                                <span v-else>{{item.user}}</span>
                            </template>
                            <template v-slot:[`item.actions`] = "{ item }">
                                <div v-if="item.id === editedItem.id">
                                    <v-icon small color="red" @click="closeRowItem">mdi-window-close</v-icon>
                                    <v-icon small color="green" @click="saveRowItem">mdi-content-save</v-icon>
                                </div>
                                <div v-else>
                                    <v-icon small @click="editItem(item)">mdi-pencil</v-icon>
                                    <v-icon small @click="diaglogDeleteItem(item)">mdi-delete</v-icon>
                                </div>
                            </template>
                        </v-data-table>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            small
                            depressed
                            color="grey lighten-2"
                            class="khmer-font"
                            @click="closeDialog"
                        >
                            {{$t('absent.btnCancel')}}
                        </v-btn>
                        <v-btn
                            small
                            depressed
                            color="khawin-background-color khmer-font"
                            type="submit"
                            :loading="btnSaveLoading"
                        >
                            {{$t('absent.btnSave')}}
                        </v-btn>
                    </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
        <v-dialog 
            v-model="groupDialog" 
            width="500"
            persistent
        >
            <v-card class="mx-auto table-card">
                <v-toolbar
                    dense
                    flat
                    color="lighten-1"
                    class="khawin-background-color khmer-font"
                >
                    <span class="khmer-font white--text">
                        <v-icon left color="white">mdi-account-group</v-icon>
                        {{$t('workflow.group')}}
                    </span>
                </v-toolbar>
                <form @submit.prevent="editMode ? updateGroup() : createGroup()" enctype="multipart/form-data">
                    <v-card-text>
                        <v-text-field
                            v-model="form1.GroupCode"
                            v-bind:label="$t('workflow.groupCode')"
                            prepend-icon="mdi-format-list-numbered"
                            class="khmer-font"
                            :error-messages="errorMessage.GroupCode"
                        ></v-text-field>
                        <v-text-field
                            v-model="form1.GroupName"
                            v-bind:label="$t('workflow.groupName')"
                            prepend-icon="mdi-account-outline"
                            class="khmer-font"
                            :error-messages="errorMessage.GroupName"
                        ></v-text-field>
                        <v-textarea
                            v-model="form1.Remark"
                            class="khmer-font"
                            prepend-inner-icon="mdi-pencil"
                            v-bind:label="$t('absent.remark')"
                            outlined
                            rows="1"
                        ></v-textarea>
                        <label class="khmer-font font-weight-bold grey--text">{{$t('workflow.user')}}</label><br>
                        <span class="font-weight-bold khmer-font red--text" v-if="chkUsersErrorMsg.openError">{{ chkUsersErrorMsg.errorMsg }}</span>
                            <div v-for="(item) in userData" :key="item.id" class="blockChkUser">
                                <input type="checkbox" 
                                    :value="item.id" 
                                    v-model="form1.userIds" 
                                >
                                <label>{{ item.UserID }}</label>
                            </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            small
                            depressed
                            @click="closeDialog"
                            color="grey lighten-2"
                            class="khmer-font"
                        >
                            {{$t('absent.btnCancel')}}
                        </v-btn>
                        <v-btn
                            small
                            depressed
                            class="khawin-background-color khmer-font"
                            type="submit"
                            :loading="btnSaveLoading"
                        >
                            {{$t('absent.btnSave')}}
                        </v-btn>
                    </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
        <v-snackbar v-model="snackBar" color="cyan darken-2" dark>
            {{alertSnackBar}}
            <template
                v-slot:action="{ attrs }"
            >
                <v-btn
                    v-bind="attrs"
                    text
                    dark
                    small
                    @click="snackBar = false"
                >
                    {{$t('absent.btnCancel')}}
                </v-btn>
            </template>
        </v-snackbar>
        <v-dialog
            width="350px"
            v-model="deleteDialog"
        >
            <v-card>
                <div class="text-center">
                    <v-sheet
                        class="px-3 pt-7 pb-4 mx-auto text-center d-inline-block"
                    >
                        <v-icon
                            class="text-center pb-3"
                            x-large
                            color="red lighten-2"
                        >mdi-alert</v-icon>
                        <div class="grey--text text--darken-3 text-body-2 mb-4 khmer-font">
                            {{$t('workflow.deleteMessage')}}
                            <b class="red--text text--lighten-2">{{ deleteWorkflow }}</b>
                        </div>
                        <v-btn
                            small
                            depressed
                            dark
                            class="ma-1 khmer-font"
                            :disabled="btnLoading"
                            @click="deleteDialog = false"
                        >
                            {{$t('absent.btnCancel')}}
                        </v-btn>
                        <v-btn
                            class="ma-1 khmer-font"
                            small
                            depressed
                            dark
                            color="red"
                            :loading="btnLoading"
                            @click="submitDelete"
                        >
                            {{$t('absent.btnDelete')}}
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>
        <v-dialog
            width="350px"
            v-model="deleteGroupDialog"
        >
            <v-card>
                <div class="text-center">
                    <v-sheet
                        class="px-3 pt-7 pb-4 mx-auto text-center d-inline-block"
                    >
                        <v-icon x-large color="red lighten-2" class="text-center pb-3">mdi-alert</v-icon>
                        <div class="grey--text text--darken-3 text-body-2 mb-4 khmer-font">
                            {{$t('workflow.deleteMessage')}} <b class="red--text text-lighten-2">{{ deleteGroupName }}</b>
                        </div>
                        <v-btn
                            small
                            dark
                            depressed
                            class="ma-1 khmer-font"
                            :disabled="btnLoading"
                            @click="deleteGroupDialog = false"
                            
                        >
                            {{$t('absent.btnCancel')}}
                        </v-btn>
                        <v-btn
                            small
                            dark
                            depressed
                            class="ma-1 khmer-font"
                            :loading="btnLoading"
                            @click="submitDeleteGroup"
                            color="red"
                        >
                            {{$t('absent.btnDelete')}}
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>
        <DeleteDialog 
            :openDeleteDialog="openDeleteDialog" 
            @deleteItem="deleteItem" 
            @cancelDialog="openDeleteDialog = false"
        />
        <AlertMessageDialog
            :openAlertDialog="alertMessage.openAlertMsg"
            :msgDialog="alertMessage.msgDialog"
            @btnOK="alertMessage.openAlertMsg = false"
        />
    </div>
</template>
<style scoped>
    .blockChkUser {
        height: 25px;
    }
    .centered-input >>> input {
        text-align: center;
    }
    .centeredText {
        margin: auto;
        display: table;
    }
    .v-text-field__details{
        margin-bottom: 0px;
    }
</style>
<script>
import store from '../store';
import DeleteDialog from './helper_components/DeleteDialog.vue';
import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
export default {
    components: { DeleteDialog , AlertMessageDialog},
    data: ()=>({
        openDialog: false,
        groupDialog: false,
        tab1: 1,
        tab2: 2,
        tab:null,
        dataFormTable:[],
        workflowData:[],
        groupData:[],
        tableLoading: true,
        btnSaveLoading: false,
        snackBar: false,
        errorMessage: '',
        countWorkflow: "",
        countGroup: "",
        alertSnackBar: '',
        editMode: false,
        search: "",
        deleteDialog: false,
        btnLoading:false,
        deleteWorkflow:"",
        deleteGroupName:"",
        deleteGroupDialog: false,
        form: new Form({
            PKID:'',
            WorkFlowCode:'',
            WorkFlowName:'',
            ValidationTypeID: '',
            Remark: '',
            positionId: [],
            dataFormTable: [],
        }),
        form1: new Form({
            PKID:'',
            GroupCode:'',
            GroupName:'',
            Remark: '',
            userIds: [],
        }),
        editedIndex: -1,
        editedItem: {
            id: 0,
            groupId: 0,
            groupName: "",
            order: 0,
            user: "",
        },
        defaultItem: {
            id: 0,
            groupId: 0,
            groupName: "",
            order: 0,
            user: "",
        },
        selectedIndex: 0,
        openDeleteDialog: false,
        userData:[],
        chkUsersErrorMsg: {
            openError: false,
            errorMsg: "",
        },
        ValidationTypeData: [],
        PositionData: [],
        alertMessage: {
            openAlertMsg: false,
            msgDialog: "",
        },
        chDateTime:{
            ErrorMsg: '',
            openError: false,
        },
        chPosition:{
            ErrorMsg: '',
            openError: false,
        },
        check: false,
        isDisplayPosition: false,
    }),
    methods: {
        
        ReadValidationType(){
            axios
            .get(this.$globalConfig.ipHost + "api/read-validationtype", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) => {
                this.ValidationTypeData = response.data.data
            })
            .catch((error) => {
                console.log(error)
            })
        },
        ReadUser(){
            axios
            .get(this.$globalConfig.ipHost + 'api/read-user', {
                headers: { Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
            })
            .then((response) =>{
                this.userData = response.data.data
            })
            .catch((errors) => {
                console.log(errors)
            })
        },

        ReadWorkflow(){
            axios
            .get(this.$globalConfig.ipHost + "api/read-workflow",{
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) => {
                this.workflowData = response.data.data;
                this.countWorkflow = response.data.data.length;
                this.tableLoading = false
            })
            .catch((error) => {
                console.log(error);
            })
        },

        ReadGroup(){
            axios
            .get(this.$globalConfig.ipHost + "api/read-group", {
                headers:{
                    Authorization:"Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) => {
                this.groupData = response.data.data
                this.countGroup = response.data.data.length
                this.tableLoading = false
            })
            .catch((error) => {
                console.log(error)
            })
        },

        ReadPosition(){
            axios
            .get(this.$globalConfig.ipHost + "api/read-position-type" , {
                headers:{
                    Authorization:"Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) => {
                this.PositionData = response.data.positions
            })
            .catch((error) => {
                console.log(error)
            })
        },

        createGroup(){
            this.tableLoading = true
            this.btnSaveLoading = true
            this.form1
                .post("api/create-group", {
                    headers : {
                        Authorization:"Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((response) => {
                    this.ReadGroup()
                    this.closeDialog()
                    this.alertSnackBar = this.$t('leaveEntry.msgSaved')
                    this.snackBar = true
                    this.tableLoading = false
                    this.btnSaveLoading = false
                })
                .catch((error) => {
                    this.tableLoading = false
                    this.btnSaveLoading = false
                    this.errorMessage = error.response.data.errors
                    if(this.errorMessage.userIds !== undefined) {
                        this.chkUsersErrorMsg.openError = true;
                        this.chkUsersErrorMsg.errorMsg = this.$t('workflow.userGroup');
                    } else {
                        this.chkUsersErrorMsg.openError = false;
                        this.chkUsersErrorMsg.errorMsg = "";
                    }
                })
        },

        async updateGroup(){
            this.btnSaveLoading = true
            this.tableLoading = true
            await new Promise((resolve) => setTimeout(resolve,1000))
            this.form1
            .post("/api/update-group/" + this.form1.PKID, {
                headers: {
                    Authorization:"Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) => {
                this.ReadGroup()
                this.closeDialog()
                this.tableLoading = false
                this.btnSaveLoading = false
                this.snackBar = true
                this.alertSnackBar = this.$t('leaveEntry.msgUpdate')
            })
            .catch((error) => {
                this.tableLoading = false
                this.btnSaveLoading = false
                this.errorMessage = error.response.data.errors
            })
        },

        editGroup(item){
            this.editMode = true
            this.form1.PKID = item.PKID
            this.form1.GroupCode = item.GroupCode
            this.form1.GroupName = item.GroupName
            this.form1.Remark = item.Remark
            this.form1.userIds = item.UserIds;
            this.groupDialog = true
        },

        async submitDeleteGroup(){
            this.tableLoading = true
            this.btnLoading = true
            await new Promise((resolve) => setTimeout(resolve,1000))
            axios
            .delete("/api/delete-group/" + this.form1.PKID, {
                headers: { Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
            })
            .then((response) =>{
                this.ReadGroup()
                this.deleteGroupDialog = false
                this.snackBar = true
                this.alertSnackBar = this.$t('leaveEntry.msgDelete')
                this.tableLoading = false
                this.btnLoading = false
            })
            .catch((error) =>{
                this.tableLoading = false
                this.btnLoading = false
            })
        },

        deleteGroup(group,name){
            this.form1.PKID = group
            this.deleteGroupName = name
            this.deleteGroupDialog = true
        },

        createWorkflow(){
            this.tableLoading = true;
            this.btnSaveLoading = true;
            this.form
            .post("api/create-workflow",{
                headers: { Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
            })
            .then((response) => {
                this.ReadWorkflow()
                this.ReadPosition()
                this.closeDialog()
                this.alertSnackBar = this.$t('leaveEntry.msgSaved');
                this.snackBar = true;
                this.tableLoading = false;
                this.btnSaveLoading = false;
            })
            .catch((error) => {
                this.tableLoading = false
                this.btnSaveLoading = false
                this.errorMessage = error.response.data.errors
                if(this.errorMessage.groupId !== undefined) {
                    //Modify by Theara 07/07/23 #CMS-77
                    if(this.errorMessage.groupId == 'Data is not empty.'){
                        // this.chDateTime.openError = true
                        // this.chDateTime.ErrorMsg = this.$t('workflow.groupTable')
                        this.alertMessage.openAlertMsg = true
                        this.alertMessage.msgDialog = this.$t('workflow.groupTable')
                    }
                    else if(this.errorMessage.groupId == 'Data is duplicate.'){
                        // this.chDateTime.openError = true
                        // this.chDateTime.ErrorMsg = this.$t('workflow.empity')
                        this.alertMessage.openAlertMsg = true
                        this.alertMessage.msgDialog = this.$t('workflow.empity')
                    }
                    // else{
                    //     this.chDateTime.openError = false
                    // }
                }
                //Modify by Theara 07/07/23 #CMS-77
                if(this.errorMessage.dataFormTable !== undefined) {
                    this.chDateTime.openError = true
                    this.chDateTime.ErrorMsg = this.$t('workflow.btnClick')
                }else {
                    this.chDateTime.openError = false
                }
                if(this.errorMessage.positionId !== undefined){
                    this.chPosition.openError = true
                    this.chPosition.ErrorMsg = this.$t('workflow.position')
                }else{
                    this.chPosition.openError = false
                }
            })
        },

        openDialogForm() {
            this.form.dataFormTable = [];
            let tab1 = this.$refs.tab1;
            let isTab1Active = tab1.$el.classList.contains("v-tab--active");
            if(isTab1Active) {
                this.openDialog = true;
            } else {
                this.groupDialog = true;
            }
            
        },
        closeDialog(){
            let tab1 = this.$refs.tab1;
            let isTab1Active = tab1.$el.classList.contains("v-tab--active");
            if(isTab1Active) {
                this.form.PKID = ''
                this.form.WorkFlowCode = ''
                this.form.WorkFlowName = ''
                // add
                this.form.ValidationTypeID = ''
                this.form.Remark = ''
                this.form1.GroupCode = ''
                this.form1.GroupName = ''
                this.form1.Remark = ''
                this.openDialog = false
                this.form.positionId = []
                this.chDateTime.ErrorMsg = ''
                this.chDateTime.openError = false
                this.chPosition.ErrorMsg = ''
                this.chPosition.openError = false
                //Modify by Theara 07/07/23 #CMS-77
                // this.isDisplayPosition = false
                this.PositionData = []
            } else {
                this.form1.PKID = '';
                this.form1.GroupCode = '';
                this.form1.GroupName = '';
                this.form1.Remark = '';
                this.form1.userIds = [];
                this.groupDialog = false;
                this.chkUsersErrorMsg.errorMsg = '';
                this.chkUsersErrorMsg.openError = false;
            }
            this.editMode = false
            this.btnSaveLoading = false
            this.errorMessage = "";
        },

        async submitDelete(){
            this.btnLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve,1000));
            axios
                .delete("/api/delete-workflow/" + this.form.PKID, {
                    headers:{ Authorization:"Bearer " + sessionStorage.getItem("TOKEN") },
                })
                .then((response) => {
                    this.ReadWorkflow()
                    this.deleteDialog = false
                    this.alertSnackBar = this.$t('leaveEntry.msgDelete')
                    this.snackBar = true
                    this.btnLoading = false
                    this.tableLoading = false
                })
                .catch((errors) => {
                    this.btnLoading = false
                    this.tableLoading = false
                })
        },

        deleteWork(workflow,name){
            this.form.PKID = workflow
            this.deleteWorkflow = name
            this.deleteDialog = true
        },

        editWorkflow(item){
            this.btnSaveLoading = true
            axios
            .get(this.$globalConfig.ipHost + "api/read-position-update/" + item.PKID, {
                headers: { Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
            })
            .then((response) => {
                this.editMode = true
                this.btnSaveLoading = false
                this.tableLoading = false
                this.form.PKID = item.PKID
                this.form.WorkFlowCode = item.WorkFlowCode
                this.form.WorkFlowName = item.WorkFlowName
                // add
                this.form.ValidationTypeID = item.ValidationTypeId
                this.form.Remark = item.Remark
                var workflowGroups = [];
                for(var i = 0; i < item.WorkflowGroup.length; i++) {
                    var data = {};
                    data.id = item.WorkflowGroup[i].FlowOrder;
                    data.groupId = item.WorkflowGroup[i].GroupPKID;
                    data.order = item.WorkflowGroup[i].FlowOrder;
                    for(var j = 0; j < this.groupData.length; j++) {
                        if(data.groupId == this.groupData[j].PKID) {
                            data.groupName = this.groupData[j].GroupCode;
                            data.user = this.groupData[j].UserNames;
                            break;
                        }
                    }
                    workflowGroups.push(data);
                }
                this.form.dataFormTable = workflowGroups;
                //Modify by Theara 07/07/23 #CMS-77
                // this.isDisplayPosition = true;
                this.PositionData = response.data.positionData;
                var positionId = new Array();
                for(var i = 0; i < this.PositionData.length; i++) {
                    if(this.PositionData[i].isChecked) {
                        positionId.push(this.PositionData[i].id);
                    }
                }
                this.form.positionId = positionId;
                this.openDialog = true
            })
            .catch((errors) =>{
                console.log(errors)
                this.btnSaveLoading = false
                this.tableLoading = false
            })
        },

        async updateWorkFlow(){
            this.btnSaveLoading = true
            this.tableLoading = true
            await new Promise((resolve) => setTimeout(resolve,1000))
            this.form
            .post("/api/update-workflow/" + this.form.PKID, {
                headers: { Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
            })
            .then((response) => {
                this.ReadWorkflow()
                this.ReadPosition()
                this.closeDialog()
                this.snackBar = true
                this.tableLoading = false
                this.btnSaveLoading = false
                this.alertSnackBar = this.$t('leaveEntry.msgUpdate')
            })
            .catch((errors) =>{
                this.btnSaveLoading = false
                this.tableLoading = false
                console.log(errors)
            })
        },
        addRowTable() {
            const addObj = Object.assign({}, this.defaultItem);
            addObj.id = this.form.dataFormTable.length + 1;
            addObj.order = addObj.id;
            if(this.editedIndex > -1){
                var group = this.editedItem.groupId;
                if(group == 0){
                    this.alertMessage.openAlertMsg = true
                    this.alertMessage.msgDialog =  this.$t('workflow.groupTable') 
                    return
                }
                return addObj.id = this.form.dataFormTable.length
            }
            this.form.dataFormTable.unshift(addObj);
            this.editItem(addObj);
        },
        editItem(item) {
            this.editedIndex = this.form.dataFormTable.indexOf(item);
            this.editedItem = Object.assign({}, item);
        },
        diaglogDeleteItem(item) {
            this.selectedIndex = this.form.dataFormTable.indexOf(item);
            this.openDeleteDialog = true;
        },
        deleteItem() {
            this.openDeleteDialog = false;
            this.form.dataFormTable.splice(this.selectedIndex, 1);
        },
        closeRowItem() {
            setTimeout(() => {
                var group = this.editedItem.groupId;
                if(group == 0){
                    this.form.dataFormTable.splice(this.selectedIndex, 1);
                }
                else{
                    if(this.editedIndex > -1){
                        Object.assign(this.form.dataFormTable[this.editedIndex],this.addObj);
                    }
                }
                
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            }, 300)
        },
        saveRowItem() {
            if (this.editedIndex > -1) {
                var group = this.editedItem.groupId;
                if(group == 0){
                    this.alertMessage.openAlertMsg = true;
                    this.alertMessage.msgDialog = this.$t('workflow.groupTable');
                    return;
                }
                Object.assign(this.form.dataFormTable[this.editedIndex], this.editedItem);
            }
            this.editedItem = Object.assign({}, this.defaultItem);
            this.editedIndex = -1;
            // this.closeRowItem();
        },
        
        selectCmbGroup() {
            var inputGroup = document.getElementById("cmbGroupId").getElementsByClassName("v-select__selections")[0].getElementsByTagName("input");
            inputGroup[0].style.display = "none";
            var cmbGroupId = this.editedItem.groupId;
            var cmbGroupName = "";
            var userNames = "";
            for(var i = 0; i < this.groupData.length; i++) {
                if(this.groupData[i].PKID == cmbGroupId) {
                    userNames = this.groupData[i].UserNames;
                    cmbGroupName = this.groupData[i].GroupCode;
                    break;
                }
            }
            this.editedItem.user = userNames;
            this.editedItem.groupName = cmbGroupName;
        },
        selectValidationType(){
            if(this.form.ValidationTypeID != null) {
                axios
                .get(this.$globalConfig.ipHost + "api/read-position-type/" + this.form.ValidationTypeID , {
                    headers: { Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
                })
                .then((response) => {
                    this.PositionData = response.data.positions
                    //Modify by Theara 07/07/23 #CMS-77
                    if(this.form.ValidationTypeID != null && this.PositionData.length < 1) {
                        this.chPosition.openError = true
                        this.chPosition.ErrorMsg = this.$t('workflow.NoPosition')
                    }else{
                        this.chPosition.openError = false
                    }
                })
                .catch((errors) =>{
                    console.log(errors)
                })
            }
        }
    },
    computed:{
        userToken(){
            return store.state.user;
        },
        workflowHeader(){
            return[
            {text:  this.$t('workflow.workflowCode'), value:'WorkFlowCode'},
            {text:  this.$t('workflow.workflowName'), value:'WorkFlowName'},
            // add 
            {text:  this.$t('parameters.txtValidation'), value: 'ValidationType'},
            {text:  this.$t('leaveEntry.tbRemark'), value:'Remark'},
            {text:  this.$t('workflow.listWorkflow'), value:''},
            {text:  this.$t('leaveEntry.tbAction'), sortable:false, value:'actions', align:"center"}
        ]
        },
        groupHeader()
        {
            return[
            {text:this.$t('workflow.groupCode'), value:'GroupCode'},
            {text:this.$t('workflow.groupName'), value:'GroupName'},
            {text:this.$t('workflow.createDate'), value:'CreateDate'},
            {text:this.$t('leaveEntry.tbRemark'), value:'Remark'},
            {text:this.$t('workflow.user'), value:'UserNames'},
            {text:this.$t('leaveEntry.tbAction'), sortable: false, value:'actions', align:"center"}
        ]
        },
        formTable(){
            return[
            {text:this.$t('workflow.group'), value:'group', width: '20%'},
            {text:this.$t('workflow.order'), value:'order', width: '20%'},
            {text:this.$t('workflow.user'), value:'user', width: '20%'},
            {text:this.$t('leaveEntry.tbAction'), sortable: false, value:'actions', align:"center", width: '20%'}
            ]
        }
    },
    mounted(){
        if(this.userToken.token){
            this.ReadWorkflow();
            this.ReadGroup();
            this.ReadUser();
            this.ReadValidationType();
        }
    },
}
</script>


