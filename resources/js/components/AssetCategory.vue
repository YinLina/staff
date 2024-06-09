<template>
    <div id="AssetCategory">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                <v-col cols="12" sm="12" md="5">
                    <h3 class="red-lighten-3 font-weight-bold">
                        <v-icon color="grey darken-2" class="mb-1">mdi-format-list-bulleted</v-icon>
                        <span class="text-decoration-underline font-weight-medium">ប្រភេទសម្ភារៈ</span>
                    </h3>
                </v-col>
                <v-col cols="12" sm="12" md="7">
                    <v-text-field 
                        append-icon="mdi-magnify"
                        single-line
                        class="txt-search"
                        placeholder="Search"
                        v-model="searchAssetCategory"
                    >
                    </v-text-field>
                </v-col>
                </v-row>
            </v-col>
            <!-- add employee button -->
            <v-col cols="12" sm="6" class="text-end">
                <v-btn small class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                    @click="openDialogForm">
                    <v-icon left>mdi-clipboard-account</v-icon>
                    បន្ថែមប្រភេទសម្ភារៈ 
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
                            Add asset category
                        </span>
                        <span v-else class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-edit</v-icon>
                            Edit asset category
                        </span>
                    </v-toolbar>
                    <form @submit.prevent="editMode ? updateAssetCategory() : createAssetCategory()">
                        <v-card-text>
                            <v-text-field
                                v-model="form.AssetCategory"
                                prepend-inner-icon="mdi-format-list-bulleted"
                                placeholder="ឈ្មោះសម្ភារៈ"
                                class="khmer-font"
                                outlined
                                :error-messages="errorsMessage.AssetCategory"
                            ></v-text-field>
                            <v-textarea
                                v-model="form.Remark"
                                prepend-inner-icon="mdi-pencil"
                                label="សម្គាល់"
                                class="khmer-font"
                                outlined
                            ></v-textarea>
                        </v-card-text>
                        <!-- button cancel and save -->
                        <v-card-actions class="khmer-font">
                            <v-spacer></v-spacer>
                            <v-btn color="grey lighten-2" small depressed @click="closeDialog">បោះបង់</v-btn>
                            <v-btn color="khawin-background-color khmer-font" dark small type="submit" :loading="btnSaveLoading">រក្សាទុក</v-btn>
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
                :items="AssetCategory"
                :loading="tableLoading"
                :search="searchAssetCategory"
            >
                <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="editAssetCatecory(item)">mdi-pencil</v-icon>
                    <v-icon small class="mr-2" @click="deleteAssetCategory(item.PKID, item.AssetCategory)">mdi-delete</v-icon>
                </template>
            </v-data-table>
            <!-- ---------------Snacbar--------------- -->
            <v-snackbar v-model="snackbar" color="cyan darken-2" dark>
                {{ alertSnackbarMsg }}
                <template v-slot:action="{ attrs }">
                    <v-btn dark text v-bind="attrs" @click="snackbar = false" small>
                        Close
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
                                Are you sure to delete
                                <b class="red--text tex--lighten-2">{{ AssetCategoryTitle }}</b>
                                ?
                            </div>

                            <v-btn :disabled="btnLoading" class="ma-1" depressed small @click="dialogDelete = false">
                                Cancel
                            </v-btn>

                            <v-btn :loading="btnLoading" class="ma-1" dark color="red" small depressed
                                @click="submitDelete">
                                Delete
                            </v-btn>
                        </v-sheet>
                    </div>
                </v-card>
            </v-dialog>
        </v-card>
    </div> 
</template>

<!-- ---------------------------------------------------------- -->
<script>
    import store from "../store";
    export default {
        name: 'AssetCategory',
        data: ()=> ({
            openDialog: false,
        
            //--------header table---------
            headers: [
                    {text:'ប្រភេទសម្ភារៈ',value:'AssetCategory'},
                    {text:'សម្គាល់',value:'Remark'},
                    {text:'កែ | លុប', value: "actions", align: "center", sortable: false},
            ],
            //-------- value table --------
            AssetCategory: [],
            tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                PKID: "",
                AssetCategory: "",
                Remark: null,
            }),
            btnLoading: false,
            dialogDelete: false,
            AssetCategoryTitle: "",
            searchAssetCategory: "",
        }),  
        computed: {
            userToken() {
                return store.state.user;
            },
        },
        mounted() {
            if(this.userToken.token){
                this.ReadAssetCategory();
            }
        },
        methods: {
            ReadAssetCategory() {
                axios
                    .get(this.$globalConfig.ipHost + "api/read-asset-category", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.AssetCategory = response.data.data;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createAssetCategory() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                this.form
                .post("api/create-asset-category", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadAssetCategory();
                    this.closeDialog();
                    this.alertSnackbarMsg = response.data.message;
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
                this.form.AssetCategory = "";
                this.form.Remark = null;
            },
            editAssetCatecory(item) {
                this.editMode = true;
                this.form.PKID = item.PKID;
                this.form.AssetCategory = item.AssetCategory;
                this.form.Remark = item.Remark;
                this.openDialog = true;
            },
            deleteAssetCategory(PKID, AssetCategory) {
                this.form.PKID = PKID;
                this.AssetCategoryTitle = AssetCategory;
                this.dialogDelete = true;
            },
            async updateAssetCategory() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("/api/update-asset-category/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {                                                                                                                                                                                                                                                                                                                                                                                                                                           
                    this.ReadAssetCategory();
                    this.closeDialog();
                    this.alertSnackbarMsg = response.data.message;
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
                .delete("/api/delete-asset-category/" + this.form.PKID, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadAssetCategory();
                    this.dialogDelete = false;
                    this.alertSnackbarMsg = response.data.message;
                    this.snackbar = true;
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