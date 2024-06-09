<template>
    <div id="position">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                        <h3 class="grey--text text--darken-2">
                            <v-icon class="mb-1" color="grey darken-2">mdi-badge-account-outline</v-icon>
                            <span class="text-decoration-underline font-weight-medium">{{ $t('position.titleList') }}</span>
                            <v-chip color="grey lighten-2 grey--text text--darken-3">{{
                                positionCount
                            }}</v-chip>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                        <v-text-field
                            v-model="searchAbsent"
                            append-icon="mdi-magnify"
                            class="txt-search"
                            single-line
                            v-bind:label="$t('position.search')"
                        >
                        </v-text-field>
                    </v-col>
                </v-row>
            </v-col>

            <v-col cols="12" sm="6" class="text-end">
                <v-btn small class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
                    @click="openDialog">
                    <v-icon left>mdi-clipboard-account</v-icon> {{ $t('position.addPosition') }}
                </v-btn>
            </v-col>
        </v-row>

        <!-- -------table---- -->
        <v-card class="mx-auto table-card">
            <v-data-table
                :headers="headers"
                :items="absentData"
                :search="searchAbsent"
                :loading="tableLoading"  
                  
            >
            <!-- Remove sub table modified by huychoung 11/08/2023 #83 -->

                <template v-slot:[`item.title`]="{ item }">
                    <v-avatar left>
                        <v-icon color="blue-grey darken-1">mdi-account-star</v-icon>
                    </v-avatar>
                    <span class="text-capitalize blue-grey--text text--darken-2">
                        {{ item.title }}
                    </span>
                </template>

                <template v-slot:[`item.employee_count`]="{ item }">
                    <v-chip
                        class="ma-0 font-weight-medium"
                        color="blue-grey"
                        text-color="white"
                        small
                    >
                        {{ item.employee_count }}
                    </v-chip>
                </template>

                <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="editPosition(item)">mdi-pencil</v-icon>
                    <v-icon small class="mr-2" @click="deletePosition(item.id, item.title, item)">mdi-delete</v-icon>
                </template>

                <template v-slot:no-results>
                    <span>{{ $t('position.tabelNotFound') }}</span>
                </template>

            </v-data-table>
        </v-card>

        <!-- --------Absent-Insert-Form------ -->
        <v-dialog v-model="positionForm" width="500" persistent overlay-opacity="0.5">
            <v-card>
                <v-toolbar dense flat color="lighten-1" class="user-form-dialog khawin-background-color">
                    <span v-if="editMode === false" class="white--text khmer-font">
                        <v-icon left color="white">mdi-badge-account-outline</v-icon>
                        {{ $t('position.formTitleAdd') }}
                    </span>
                    <span v-else class="white--text khmer-font">
                        <v-icon left dark>mdi-badge-account-outline</v-icon>
                         {{ $t('position.formTitleEdit') }}
                    </span>
                </v-toolbar>

                <form @submit.prevent="editMode ? updatePosition() : createPosition()">
                    <v-card-text>
                        <v-text-field
                            v-model="form.title"
                            v-bind:label="$t('position.txtPosition')"
                            prepend-icon="mdi-account-star"
                            class="khmer-font"
                            :error-messages="errorsMessage.title"
                        ></v-text-field>

                        <v-autocomplete
                                v-model="form.ParentPositions_id" 
                                :items="absentData"
                                :item-text="(item) => item.title"
                                v-bind:label="$t('position.parentPosition')" 
                                prepend-icon="mdi-account-star"
                                color="cyan darken-1"
                                class="khmer-font"
                                item-value="id"   
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

                            <v-autocomplete
                                v-model="form.Department_PKID" 
                                :items="departmentData"
                                :item-text="(item) => item.Department"
                                v-bind:label="$t('position.Department')" 
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

                    <v-card-actions class="card-action khmer-font">
                        <v-spacer></v-spacer>
                        <v-btn small color="grey lighten-2" depressed @click="closeDialog">
                            {{ $t('position.btnCancel') }}
                        </v-btn>
                        <v-btn class="khawin-background-color khmer-font" dark small depressed type="submit" :loading="btnSaveLoading">
                            {{ $t('position.btnSave') }}
                        </v-btn>
                    </v-card-actions>
                </form>
            </v-card>
        </v-dialog>

        <!-- ---------------Snacbar--------------- -->
        <v-snackbar v-model="snackbar" color="cyan darken-2" dark>
            {{ alertSnackbarMsg }}
            <template v-slot:action="{ attrs }">
                <v-btn dark text v-bind="attrs" @click="snackbar = false" small>
                    {{ $t('position.msgClose') }}
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
                            {{ $t('position.deleteMessage') }}
                            <b class="red--text tex--lighten-2">{{ positionTitle }}</b>
                            ?
                            <p>{{ $t('position.deleteMessageWarning') }}!</p>
                        </div>

                        <v-btn :disabled="btnLoading" class="ma-1" depressed small @click="dialogDelete = false">
                            {{ $t('position.btnCancel') }}
                        </v-btn>

                        <v-btn :loading="btnLoading" class="ma-1" dark color="red" small depressed
                            @click="submitDelete">
                            {{ $t('position.btnDelete') }}
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>

        <!-- Modify by Huychoung 02/08/2023 #82  -->
        <!-- Dialog warning Not Allowed to Delete if position is being used -->
        <v-dialog v-model="dialogWarning" max-width="330px">
            <v-card>
                <div class="text-center">
                    <v-sheet class="px-7 pt-7 pb-4 mx-auto text-center d-inline-block">
                        <v-icon class="text-center pb-3" x-large color="red lighten-2">mdi-alert</v-icon>
                        <div class="grey--text text--darken-3 text-body-2 mb-4">
                            {{ $t('position.notAllowedWarningMessage') }}
                            <b class="red--text tex--lighten-2">{{ positionTitle }}</b>
                            !
                            <p>{{ $t('position.warningMessage') }}</p>
                        </div>

                        <v-btn :disabled="btnLoading" class="ma-1" depressed small @click="dialogWarning = false">
                            {{ $t('position.btnCancel') }}
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from "moment";
import store from "../store";
export default {
    data() {
        return {
            searchAbsent: "",
            snackbar: false,
            editMode: false,
            tableLoading: true,
            absentData: [],
            employeeData: [],
            departmentData: [],
            positionCount: "",
            positionForm: false,
            form: new Form({
                id: "",
                title: "",
                ParentPositions_id: "",
                Department_PKID: "",
                Remark: "",
            }),
            btnSaveLoading: false,
            btnLoading: false,
            alertSnackbarMsg: "",
            errorsMessage: "",
            dialogDelete: false,
            dialogWarning: false,
            positionTitle: "",
        };
    },
    computed: {

        userToken() {
            return store.state.user;
        },

        formTitle() {
            return this.editMode === false ? "Add Position" : "Edit Position";
        },
        computedDateFormattedMomentjs() {
            return this.form.date
                ? moment(this.form.date).format("dddd, DD/ MM/ YYYY")
                : "";
        },

        // ====================================
        headers(){
            return [
                // { text: this.$t('position.positionTable'), value: 'title' },
                { text: this.$t('position.positionTable'), value: 'title' },
                { text: this.$t('position.parentPosition'), value: 'ParentPosition' },
                { text: this.$t('position.Department'), value: 'department.Department' },
                { text: this.$t('position.employeeCountTable'), value: 'employee_count' },
                { text: this.$t('department.Remark'), value: 'Remark' },
                { text: this.$t('position.edit_delete'), sortable: false, align: "center", value: "actions" },
            ]
        }
    },
    mounted() {
        if(this.userToken.token){
            this.ReadDepartment();
            this.ReadPosition();
            this.activateMultipleDraggableDialogs();
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
                
                this.departmentData = response.data.data;
                this.tableLoading = false;
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
                this.absentData = response.data.data;
                this.positionCount = response.data.data.length;
                this.tableLoading = false;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        openDialog() {
            this.positionForm = true;
        },

        closeDialog() {
            this.editMode = false;
            this.positionForm = false;
            this.form.id = "";
            this.form.title = "";
            this.form.ParentPositions_id = "";
            this.form.Department_PKID = "";
            this.form.Remark = "";
            this.btnSaveLoading = false;
        },

        createPosition() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form
                .post("api/create-position", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadPosition();
                    this.closeDialog();
                    // this.alertSnackbarMsg = response.data.message;
                    this.alertSnackbarMsg = this.$t('position.savedMsg');
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

        editPosition(position) {
            this.editMode = true;
            this.form.id = position.id;
            this.form.title = position.title;
            this.form.ParentPositions_id = position.ParentPositions_id;
            this.form.Department_PKID = position.Department_PKID;
            this.form.Remark = position.Remark;
            this.positionForm = true;
        },

        async updatePosition() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
                .post("/api/update-position/" + this.form.id, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadPosition();
                    this.closeDialog();
                    this.alertSnackbarMsg = this.$t('position.updateMsg');
                    this.snackbar = true;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                })
                .catch((errors) => {
                    this.errorsMessage = errors.response.data.errors;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                }
            );
        },

        // Modify by Huychoung 03/08/2023 #82
        deletePosition(position, name, item) {
            this.form.id = position;
            this.positionTitle = name;
            var employeeCount = item.employee_count;
            if (employeeCount > 0) {
                this.dialogWarning = true;             
            }
            else {
                this.dialogDelete = true;
            }        
        },
        
        // Modified by Huychoung 11/08/2023 #83
        async submitDelete() {
            this.btnLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            axios
                .delete("/api/delete-position/" + this.form.id, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    if (response.data.message == 'Cannot deleted') {
                        this.dialogWarning = true;
                        // alert('Sorry, you are not allowed to delete this position.')
                    }
                    else {
                        this.ReadPosition();
                        // this.alertSnackbarMsg = response.data.message;
                        this.alertSnackbarMsg = this.$t('position.deleteMsg');
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
    },
};
</script>
