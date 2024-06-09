<template>
    <div id="Menu">
        <v-row>
          <v-col cols="12" sm="6">
             <v-row>
                <v-col cols="12" sm="12" md="5">
                   <h3 class="red-lighten-3 font-weight-bold">
                      <v-icon color="grey darken-2" class="mb-1">mdi-format-list-bulleted</v-icon>
                      <span class="text-decoration-underline font-weight-medium">{{$t('menuSetting.menuList')}}</span>
                      <v-chip color="grey lighten-2 grey--text text--darken-3">{{countMenu}}</v-chip>
                   </h3>
                </v-col>
                <v-col cols="12" sm="12" md="7">
                   <v-text-field
                      append-icon="mdi-magnify"
                      single-line
                      class="txt-search "
                      v-model="searchMenu"
                      v-bind:label="$t('absent.search')"
                   ></v-text-field>
                </v-col>
             </v-row>
          </v-col>

          <v-col cols="12" sm="6" class="text-end">
            <v-btn small class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                   @click="openDialog = true">
                <v-icon left>mdi-plus</v-icon>
                {{$t('leaveEntry.btnAddLeaveType')}}
            </v-btn>
            <v-dialog
                v-model="openDialog"
                width="500"
                persistent
            >
                <v-card>
                    <!-- <header class="user-form-dialog khawin-background-color v-sheet theme--light v-toolbar v-toolbar--dense v-toolbar--flat lighten-1" style="height: 48px;">
                      <div class="v-toolbar__content" style="height: 48px;">
                         <span class="white--text khmer-font">
                            <i aria-hidden="true" class="v-icon notranslate v-icon--left mdi mdi-format-list-bulleted theme--light white--text"></i>
                        {{$t('menuSetting.txtMenuCode')}}
                         </span>
                      </div>
                   </header> -->
                   <v-toolbar
                        dense
                        flat
                        color="lighten-1"
                        class="khawin-background-color"
                    >
                        <span v-if="editMode === false" class="khmer-font white--text">
                            <v-icon left color="white">mdi-plus</v-icon>
                            {{$t('menuSetting.frmAddMenu')}}
                        </span>
                        <span v-else class="khmer-font white--text">
                            <v-icon left color="white">mdi-account-edit</v-icon>
                            {{$t('menuSetting.frmEditMenu')}}
                        </span>
                    </v-toolbar>
                   <form @submit.prevent="editMode ? updateMenu() : createMenu()">
                   <v-card-text>
                    <v-text-field
                    v-model="form.id"
                    :error-messages="errorsMessage.id"
                    v-bind:label="$t('menuSetting.txtMenuCode')"
                    outlined
                    prepend-inner-icon="mdi-television-guide">
                    </v-text-field>
                    <v-text-field
                    v-model="form.name"
                    :error-messages="errorsMessage.name"
                    v-bind:label="$t('menuSetting.menuName')"
                    outlined
                    prepend-inner-icon="mdi-television-guide">
                    </v-text-field>
                    <v-text-field
                    v-model="form.url"
                    :error-messages="errorsMessage.url"
                    v-bind:label="$t('menuSetting.connection')"
                    outlined
                    prepend-inner-icon="mdi-domain">
                    </v-text-field>
                    <v-text-field
                    v-model="form.parent_id"
                    :error-messages="errorsMessage.parent_id"
                    v-bind:label="$t('menuSetting.MainCode')"
                    outlined
                    prepend-inner-icon="mdi-clipboard-text">
                    </v-text-field>
                    <v-text-field
                    v-model="form.icon"
                    :error-messages="errorsMessage.icon"
                    v-bind:label="$t('menuSetting.Sign')"
                    outlined
                    prepend-inner-icon="mdi-garage">
                    </v-text-field>
                    <v-text-field
                    v-model="form.type"
                    :error-messages="errorsMessage.type"
                    v-bind:label="$t('menuSetting.Type')"
                    outlined
                    prepend-inner-icon="mdi-content-copy">
                    </v-text-field>
                    <v-text-field
                    v-model="form.order"
                    :error-messages="errorsMessage.order"
                    v-bind:label="$t('menuSetting.Order')"
                    outlined
                    prepend-inner-icon="mdi-account-arrow-left">
                    </v-text-field>
                    <v-text-field
                    v-model="form.is_disable"
                    :error-messages="errorsMessage.is_disable"
                    v-bind:label="$t('menuSetting.Closed')"
                    outlined
                    prepend-inner-icon="mdi-account-arrow-left">
                    </v-text-field>
                    <!-- button cancel and save -->
                    <v-card-actions class="khmer-font">
                    <v-spacer></v-spacer>
                    <v-btn color="grey lighten-2" small depressed @click="closeDialog">{{$t('absent.btnCancel')}}</v-btn>
                    <v-btn color="khawin-background-color khmer-font" dark small type="submit" :loading="btnSaveLoading">{{$t('absent.btnSave')}}</v-btn>
                    </v-card-actions>
                    </v-card-text>
                   </form>
                </v-card>
            </v-dialog>
          </v-col>
        </v-row>
        <!-- table -->
       <v-card class="mx-auto table-card v-card v-sheet theme--ligh">
            <v-data-table
                :headers = "header"
                :items="menuData"
                :loading="tableLoading"
                :search="searchMenu"
            >
         <template v-slot:[`item.actions`]="{ item }">
           <v-icon small class="mr-2" @click="editMenu(item)">mdi-pencil</v-icon>
           <v-icon small class="mr-2" @click="deleteMenu(item.id, item.name)">mdi-delete</v-icon>
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
                        <div class="grey--text text--darken-3 text-body-2 mb-4 khmer-font">
                            {{$t('workflow.deleteMessage')}}
                            <b class="red--text tex--lighten-2">{{ menuTitle }}</b>
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
       </v-card>

    </div>
</template>
<script>
import store from "../store";
export default {
    name:'Menu',
    data:()=>({
        openDialog:false,
        menuData: [],
        tableLoading: true,
            btnSaveLoading: false,
            editMode: false,
            errorsMessage: "",
            alertSnackbarMsg: "",
            snackbar: false,
            form: new Form({
                id: "",
                name: null,
                url:null,
                parent_id:"",
                icon:null,
                type:null,
                order:"",
                deduction: null,
                is_disable:"",
            }),
            btnLoading: false,
            dialogDelete: false,
            menuTitle: "",
            searchMenu: "",
            countMenu:"",
    }),
    computed: {
        userToken() {
            return store.state.user;
        },
        header(){
            return[
            {text:  this.$t('menuSetting.txtMenuCode'),value:'id'},
            {text:  this.$t('menuSetting.menuName'),value:'name'},
            {text:  this.$t('menuSetting.connection'),value:'url'},
            {text:  this.$t('menuSetting.MainCode'),value:'parent_id'},
            {text:  this.$t('menuSetting.Sign'),value:'icon'},
            {text:  this.$t('menuSetting.Type'),value:'type'},
            {text:  this.$t('menuSetting.Order'),value:'order'},
            {text:  this.$t('menuSetting.Closed'),value:'is_disable'},
            {text:  this.$t('leaveEntry.tbAction'), value: "actions", align: "center"},
        ]
        }
    },
    mounted() {
        if(this.userToken.token){
            this.ReadMenu();
        }
    },
    methods: {
        ReadMenu() {
            axios
                .get(this.$globalConfig.ipHost + "api/read-menuSystem", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.menuData = response.data.data;
                this.countMenu = this.menuData.length
                this.tableLoading = false;
            })
            .catch((error) => {
                console.log(error);
                this.tableLoading = false;
            });
        },
        createMenu() {
                this.btnSaveLoading = true;
                this.tableLoading = true;
                this.form
                .post(this.$globalConfig.ipHost + "api/create-menuSystem", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadMenu();
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
        closeDialog() {
            this.editMode = false;
            this.openDialog = false;
            this.btnSaveLoading = false;
            this.tableLoading = false;
            this.form.id = "";
            this.form.name = null;
            this.form.url = null;
            this.form.parent_id = "";
            this.form.icon=null;
            this.form.type=null;
            this.form.order="";
            this.form.is_disable="";
        },
        editMenu(item) {
            this.editMode = true;
            this.form.id = item.id;
            this.form.name = item.name;
            this.form.url = item.url;
            this.form.parent_id = item.parent_id;
            this.form.icon = item.icon;
            this.form.type=item.type;
            this.form.order=item.order;
            this.form.is_disable=item.is_disable;
            this.openDialog = true;
        },
        // openDialogForm(){
        //     this.openDialog = true;
        // },
        deleteMenu(PK_ID,menuSystem) {
            this.form.id = PK_ID;
            this.menuTitle = menuSystem;
            this.dialogDelete = true;
        },
       async submitDelete() {
            this.btnLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            axios
            .delete("/api/delete-menuSystem/" + this.form.id, {
            headers: {
                Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
            },
            })
            .then((response) => {
                this.ReadMenu();
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
        async updateMenu() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
            .post("/api/update-menuSystem/" + this.form.id, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReadMenu();
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
    },
}
</script>

