<template>
    <div id="document">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                        <h3 class="grey--text text--darken-2">
                            <v-icon color="grey darken-2">mdi-account-group-outline</v-icon>
                            <span class="text-decoration-underline font-weight-medium">
                                {{$t('document.documentList')}}
                            </span>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                        <v-text-field
                            single-line
                            class="txt-search"
                            append-icon="mdi-magnify"
                            label="search">  
                        </v-text-field>
                    </v-col>
                </v-row>
            </v-col>

            <v-col cols="12" sm="6" class="text-end">
                <v-btn
                    small
                    class="font-weight-regular pa-2 khawin-background-color white--text"
                    @click="openDialogForm=true"
                >
                    {{ $t('document.btnAdd') }}
                </v-btn>

                <v-btn
                    small
                    class="font-weight-regular pa-2 khawin-background-color white--text"
                    @click="deleteDocument()" 
                >
                    {{ $t('document.btnDelete') }}
                </v-btn>
                    <v-dialog
                        width="60%"
                        v-model="openDialogForm"
                        persistent>
                        <v-card>
                            <v-toolbar
                                dense
                                flat
                                color="khawin-background-color"
                            >
                                <span class="khmer-font white--text">
                                    <v-icon left color="white">mdi-plus</v-icon>
                                    {{$t('document.frmAddDocument')}}
                                </span>
                            </v-toolbar>
                            <br>
                            <v-card-text>
                                <template>
                                    <JqxFileUpload ref="fileUpload" :width="fileUploadWidth" :multipleFilesUpload="false" :fileInputName="'fileToUpload'" :rtl="true" 
                                                :cancelTemplate="'danger'" :browseTemplate="'success'" :uploadTemplate="'primary'">
                                    </JqxFileUpload>
                                </template>        
                            </v-card-text>
                            <form @submit.prevent="editMode ? updateDocument() : createDocument()">
                                <v-card-text>       
                                    <v-row>
                                        <v-col cols="12" sm="4">
                                            <v-text-field
                                                v-model="form.DocumentCode"
                                                prepend-inner-icon="mdi-file-code-outline"
                                                class="khmer-font"
                                                v-bind:label="$t('document.frmDocumentCode')"
                                                clearable 
                                                :error-messages="errorsMessage.DocumentCode"
                                            ></v-text-field>
                                        </v-col>
                                        
                                        <v-col cols="12" sm="4">
                                            <v-text-field
                                                v-model="form.Title"
                                                prepend-inner-icon="mdi-file-document-edit-outline"
                                                class="khmer-font"
                                                v-bind:label="$t('document.frmTitle')" 
                                                clearable  
                                                :error-messages="errorsMessage.Title"          
                                            ></v-text-field>
                                        </v-col>

                                        <v-col cols="12" sm="4">
                                            <v-autocomplete
                                                v-model="form.Type"
                                                :items="ParameterData"
                                                :item-text="(item) => item.DocumentType"
                                                item-value="DocumentTypeID"
                                                class="khmer-font"
                                                prepend-inner-icon="mdi-format-list-bulleted-type"
                                                clearable
                                                v-bind:label="$t('document.frmType')"
                                                color="cyan darken-1"   
                                                :error-messages="errorsMessage.Type"
                                            >
                                                <template v-slot:selection="data">
                                                    <v-chip
                                                        label
                                                        dark
                                                        color="blue-grey darken-2"
                                                        class="text-capitalize pa-1 font-weight-regular"
                                                        small
                                                    >
                                                        {{ data.item.DocumentType }}
                                                    </v-chip>
                                                </template>
                                            </v-autocomplete>
                                        </v-col>       
                                    </v-row>

                                    <template>
                                        <JqxTabs ref="myTabs" style="float: left;"
                                                :width="width" :hight="150">
                                            <ul>
                                                <li>Khmer</li>
                                                <li>English</li>
                                            </ul>
                                            <div>
                                                <br>
                                                <label class="tab">Description(KH)</label>
                                                <JqxInput
                                                    ref="descKHRef" :width="840" :height="25" :source="dataAdapter" v-model="form.DescriptionKH">   
                                                </JqxInput>
                                                <br><br>
                                                <!-- <label class="tab pt-2">Content (KH)</label> -->
                                                <span class="doubleTab">
                                                    <JqxEditor ref="contentKHRef" :width="getWidth" :height="320" :editable="true" ></JqxEditor>
                                                </span>
                                            </div>
                                            <div>
                                                <br>
                                                <label class="tab">Description(EN)</label>
                                                <JqxInput
                                                    ref="descENRef" :width="840" :height="25" :source="dataAdapter" v-model="form.DescriptionEN">   
                                                </JqxInput>
                                                <br><br>
                                                <!-- <label class="tab pt-2">Content (EN)</label> -->
                                                <span class="doubleTab">
                                                    <JqxEditor ref="contentENRef" :width="getWidth" :height="320" :editable="true" ></JqxEditor>
                                                </span>          
                                            </div>
                                        </JqxTabs>
                                    </template>       
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

        <!-- <v-data-table
            :headers="header"
            :items="DocumentData"
            :Loading="tableLoading">

            <template v-slot:[`item.actions`]="{ item }">
                <v-icon small @click="editDocument(item)">mdi-pencil</v-icon>
                <v-icon small @click="deleteDocument(item.PKID)">mdi-delete</v-icon>
            </template>
        </v-data-table> -->
        

    <template>
        <JqxGrid ref="documentGrid" :width="'100%'" :source="dataAdapter" :columns="columns" :pageable="true" :autoheight="false" :editable="false" :selectionmode="'multiplerows'" @celldoubleclick="onCelldoubleclick"></JqxGrid>
    </template>

   
    <!-- <AlertMessageDialog
        :openAlertDialog="alertMessage.openAlertMsg"
        :msgDialog="alertMessage.msgDialog"
        @btnOK="alertMessage.openAlertMsg = false"
    /> -->
    </div>
</template>

<style>
@import "jqwidgets-scripts/jqwidgets/styles/jqx.base.css";
.jqx-file-upload-button-upload, .jqx-file-upload-button-cancel, .jqx-file-upload-file-upload {
    display: none;
}
.iconscontainer {
    position: unset !important;
}
</style>
<script>
import moment from 'moment';
import store from '../store';
import DeleteDialog from './helper_components/DeleteDialog.vue';
import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
import JqxEditor from "jqwidgets-scripts/jqwidgets-vue/vue_jqxeditor.vue";
import JqxTabs from 'jqwidgets-scripts/jqwidgets-vue/vue_jqxtabs.vue';
import JqxInput from "jqwidgets-scripts/jqwidgets-vue/vue_jqxinput.vue";
import JqxFileUpload from "jqwidgets-scripts/jqwidgets-vue/vue_jqxfileupload.vue";
window.downloadDocument = function(id) {
    axios
    .get(Vue.prototype.$globalConfig.ipHost + "api/download-document/" + id, {
        headers: {
            Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
        }
    })
    .then((Response) => {
        var tempElement = document.createElement("a");
        tempElement.href = Response.data;
        tempElement.download = Response.headers.filename
        tempElement.click();
    })
    .catch((error) => {
        console.log(error)
    })
};
export default {
components: { DeleteDialog, AlertMessageDialog, JqxGrid, JqxEditor, JqxTabs, JqxInput, JqxFileUpload},
name:'Document',
data() {
    return {
        openDialogForm: false,
        tableLoading: false,
        btnLoading: false,
        btnSaveLoading: false,
        dialogDelete: false,
        editMode: false,
        errorsMessage: "",
        fileUploadWidth: "100%",
        getWidth: "100%",
        width: "100%",
        snackBar: false,
        alertSnackBarMsg: "",
        ParameterData: [],
        form: new Form({
            PKID: "",
            DocumentCode: "",
            Title: "",
            Type: "",
            ContentKH: "",
            ContentEN: "",
            DescriptionKH: "",
            DescriptionEN: "", 
            FileUpload: "",
            FileName: "",
        }),
        // width:1200,
        // alertMessage: {
        //     openAlertMsg: false,
        //     msgDialog: "",
        // },
        dataAdapter: new jqx.dataAdapter({
            localdata: [],
            DocumentData: [],
            datatype: 'array',
            datafields:
            [
                { name: 'PKID', type: 'int' },
                { name: 'CheckBox', type: 'string' },
                { name: 'DocumentCode', type: 'string' },
                { name: 'Title', type: 'string' },
                { name: 'Type', type: 'int' },
                { name: 'StrType', type: 'string' },
                { name: 'DescriptionKH', type: 'string' },
                { name: 'DescriptionEN', type: 'string' },
                { name: 'ContentKH', type: 'string' },
                { name: 'ContentEN', type: 'string' },
                { name: 'FileName', type: 'string' },
                { name: 'Action', type: ''},
            ],    
        }),
        columns: [
            {
                text: this.$t('document.tbCheckbox'), datafield: 'CheckBox', width: '3%', align: 'center', editable: true,
                renderer: function () {
                    return '<div style="margin-left: 10px; margin-top: 12px;"><input type="checkbox" id="selectAll" onclick="selectAllChkBox(' + '\'selectCheckbox\'' + ')" /></div>';
                },
                cellsrenderer: function(row, columnfield, value, defaulthtml, columnproperties) {
                    return '<div style="margin-left: 10px; margin-top: 12px;"><input type="checkbox" class="selectCheckbox" onclick="selectChkBox(' + '\'selectCheckbox\'' + ')" /></div>';
                }
            },
            {text: this.$t('document.tbDocumentCode'), datafield:'DocumentCode', width: '13%', align:"center", editable: false,},
            {text: this.$t('document.tbTitle'), datafield:'Title', width: '15%', align:"center", editable: false,},
            {text: this.$t('document.tbType'), datafield:'StrType', width: '15%', align:"center",},
            {text: this.$t('document.tbDescriptionKH'), datafield:'DescriptionKH', width: '17%', align:"center", editable: false,},
            {text: this.$t('document.tbDescriptionEN'), datafield:'DescriptionEN', width: '17%', align:"center", editable: false,},
            // {text: this.$t('document.tbContentKH'), datafield:'ContentKH', width: '14%', align:"center", editable: false,},
            // {text: this.$t('document.tbContentEN'), datafield:'ContentEN', width: '14%', align:"center", editable: false,},
            {
                text: this.$t('document.tbFileName'), datafield:'FileName', width: '20%', align:"center", editable: false,
                cellsrenderer: function(row, columnfield, value, defaulthtml, columnproperties, rowdata) {
                    return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;text-align:center;"><a><button onclick="downloadDocument(' + rowdata.PKID + ')">' + value + '</button></a></div>';
                }
            },
            ]
        }
    },

    computed:{
        userToken() {
            return store.state.user;
        },   
    },
    mounted(){
        if(this.userToken.token){             
            this.ReadDocument();
            this.ReadParameter();   
                     
        }
    },
    methods:{
        ReadParameter(){
            axios
            .get(this.$globalConfig.ipHost + "api/read-document-type",{
                headers: { Authorization: "Bearer " + sessionStorage.getItem("TOKEN")}
            })
            .then((response) => {
                this.ParameterData = response.data.data
            })
            .catch((error) => {
                console.log(error)
            })
        },
        ReadDocument(){
            axios
            .get(this.$globalConfig.ipHost + "api/read-document/", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((Response) => {
                this.DocumentData = Response.data.data
                this.dataAdapter._source.localdata = this.DocumentData;
                if(this.$refs.documentGrid !== undefined) {
                    this.$refs.documentGrid.height = this.$helper.GetGridHeight();
                    this.$refs.documentGrid.updatebounddata('cell');
                }
            })
            .catch((error) => {
                console.log(error)
            })
        },
        setValueFormContent() {
            this.form.ContentKH = this.$refs.contentKHRef.val();
            this.form.ContentEN = this.$refs.contentENRef.val();
        },
        async createDocument() {
            // if(this.form.DocumentCode != "" && this.form.Type != "" && this.form.Title != "") {
            //     if(this.form.DescriptionEN == "" || this.form.DescriptionKH == "") {
            //         this.alertMessage.openAlertMsg = true;
            //         this.alertMessage.msgDialog = "Description can not be null!";
            //         return;
            //     }
            // }
            let fileUploadElement = this.$refs.fileUpload.$el;
            let inputFileElement = fileUploadElement.querySelector("input.jqx-file-upload-file-input");
            let fileUpload = inputFileElement.files.length > 0 ? inputFileElement.files[0] : null;
            let strBase64 = "";
            if(fileUpload != null) {
                await this.setFileUploadValue(fileUpload)
                    .then((value) => {
                        strBase64 = value;
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                this.form.FileUpload = strBase64;
                this.form.FileName = fileUpload.name;
            }
            this.setValueFormContent();
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form
            .post("api/create-document", {
                headers:{ Authorization: "Bearer " + sessionStorage.getItem("TOKEN") }
            })
            .then((response) =>{
                this.ReadDocument()
                this.closeDialog()
                this.alertSnackBarMsg = this.$t('leaveEntry.msgSaved')
                this.snackBar = true
                this.btnSaveLoading = false
                this.tableLoading = false
            })
            .catch((errors) =>{
                this.btnSaveLoading = false
                this.tableLoading = false
                this.errorsMessage = errors.response.data.errors;
            })
        },

        openDialogFrm() {
            this.openDialogForm = true;
            this.editMode = true;
        },
        
        closeDialog(){
            this.editMode = false
            this.btnSaveLoading = false
            this.openDialogForm = false;
            this.openDialog = false
            this.form.PKID = 0;
            this.form.DocumentCode = "";
            this.form.Title = "";
            this.form.Type = "";
            this.form.ContentEN = "";
            this.form.ContentKH = "";
            this.form.DescriptionEN = "";
            this.form.DescriptionKH = "";
            this.form.FileName = "";
            this.form.FileUpload = null;
            this.$refs.contentENRef.val('');
            this.$refs.contentKHRef.val('');
            this.$refs.descENRef.val('');
            this.$refs.descKHRef.val(''); 
            let fileUploadElement = this.$refs.fileUpload.$el;
            let inputFileElement = fileUploadElement.querySelector("input.jqx-file-upload-file-input");
            let isHasFileUpload = inputFileElement.files.length > 0 ? true : false;
            if(isHasFileUpload) {
                this.$refs.fileUpload.cancelFile(0);
            }
            if(this.$refs.fileUpload.$el.firstChild.localName != "button") {
                this.$refs.fileUpload.$el.firstChild.innerHTML = "";
            }
            this.errorsMessage = "";
        },
        editDocument(item) {
            this.editMode = true
            this.form.PKID = item.PKID;
            this.form.DocumentCode = item.DocumentCode;
            this.form.Title = item.Title;
            this.form.Type = item.Type;
            this.form.DescriptionEN = item.DescriptionEN;
            this.form.DescriptionKH = item.DescriptionKH;
            this.form.ContentEN = item.ContentEN;
            this.form.ContentKH = item.ContentKH;
            this.openDialogForm = true;
        },
        async updateDocument() {
            // if(this.form.DocumentCode != "" && this.form.Type != "" && this.form.Title != "") {
            //     if(this.form.DescriptionEN == "" || this.form.DescriptionKH == "") {
            //         this.alertMessage.openAlertMsg = true;
            //         this.alertMessage.msgDialog = "Description can not be null!";
            //         return;
            //     }
            // }
            let fileUploadElement = this.$refs.fileUpload.$el;
            let inputFileElement = fileUploadElement.querySelector("input.jqx-file-upload-file-input");
            let fileUpload = inputFileElement.files.length > 0 ? inputFileElement.files[0] : null;
            let strBase64 = "";
            if(fileUpload != null) {
                await this.setFileUploadValue(fileUpload)
                    .then((value) => {
                        strBase64 = value;
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                this.form.FileUpload = strBase64;
                this.form.FileName = fileUpload.name;
            }
            this.setValueFormContent();
            this.btnSaveLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
            .post("/api/update-document/" + this.form.PKID, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadDocument();
                this.closeDialog();
                this.alertSnackBarMsg = this.$t('applyLeave.msgSubmit')
                this.snackBar = true;
                this.btnSaveLoading = false;
                this.tableLoading = false;
            })
            .catch((errors) => {
                this.btnSaveLoading = false;
                this.tableLoading = false;
                this.errorsMessage = errors.response.data.errors;
            });
        },
        deleteDocument() {
            var docForm = new Form({
                selectedIds: [],
            });
            var documentRef = this.$refs.documentGrid;
            var checkbox = document.getElementsByClassName('selectCheckbox');
            for(var i = 0; i < checkbox.length; i++) {
                var isChecked = checkbox[i].checked;
                if(isChecked) {
                    var rowElement = checkbox[i].parentElement.parentElement.parentElement;
                    var rowId = rowElement.getAttribute("row-id");
                    var dataItem = documentRef.getrowdata(rowId);
                    docForm.selectedIds.push(dataItem.PKID);
                }
            }
            if(docForm.selectedIds.length == 0) {
                this.alertMessage.openAlertMsg = true;
                this.alertMessage.msgDialog = "Please select an item!";
                return;
            }
            docForm
            .post("/api/delete-document_by_ids", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadDocument();
            })
            .catch((errors) => {
                console.log(errors);
            });
        },
        setFileUploadValue(fileUpload) {
            const reader = new FileReader();
            return new Promise((resolve, reject) => {
                reader.onload = (e) => {
                    resolve(e.target.result);
                }
                reader.onerror = () => {
                    reject('oops, something went wrong with the file reader.')
                }
                reader.readAsDataURL(fileUpload);
            });
        },
        onCelldoubleclick() {
            var dataItem = this.$helper.GetSelectedDataItem(this.$refs.documentGrid);
            this.editDocument(dataItem);
            var fileNameElement = document.createElement('span');
            fileNameElement.innerHTML = dataItem.FileName;
            if(this.$refs.contentENRef !== undefined) {
                this.$refs.contentENRef.val(dataItem.ContentEN);
                this.$refs.contentKHRef.val(dataItem.ContentKH);
                this.$refs.descENRef.val(dataItem.DescriptionEN);
                this.$refs.descKHRef.val(dataItem.DescriptionKH);
                if(this.$refs.fileUpload.$el.firstChild.localName != "button") {
                    this.$refs.fileUpload.$el.firstChild.innerHTML = dataItem.FileName; 
                } else {
                    this.$refs.fileUpload.$el.insertBefore(fileNameElement, this.$refs.fileUpload.$el.firstChild);
                }
            } else {
                var VueWindow = this;
                setTimeout(function() {
                    VueWindow.$refs.contentENRef.val(dataItem.ContentEN);
                    VueWindow.$refs.contentKHRef.val(dataItem.ContentKH);
                    VueWindow.$refs.descENRef.val(dataItem.DescriptionEN);
                    VueWindow.$refs.descKHRef.val(dataItem.DescriptionKH);
                    VueWindow.$refs.fileUpload.$el.insertBefore(fileNameElement, VueWindow.$refs.fileUpload.$el.firstChild);
                }, 1000);
            }
        },
    }
}
</script>