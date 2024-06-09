<template>
    <div id="role">
        <v-row class="mb-1">
            <v-col cols="sm-8">
                <h3 class="grey--text text--darken-2">
                    <v-icon class="mb-1" color="grey darken-2">mdi-shield-account</v-icon>
                    <span class="font-weight-medium">{{ $t('role.role') }}</span>
                    <v-chip color="grey lighten-2 grey--text text--darken-3">3</v-chip>
                </h3>
            </v-col>
            <v-col cols="sm-4" class="text-right">
                <v-btn
                    small
                    class="add-user white--text font-weight-regular khawin-background-color"
                    @click="openDialog"
                >
                <v-icon left>mdi-shield-account</v-icon>{{ $t('role.btnAddRole') }}</v-btn>
            </v-col>
        </v-row>

        <v-dialog
            v-model="roleForm"
            scrollable
            max-width="500px"
            persistent
            overlay-opacity="0.5"
        >
            <v-card>
                <v-toolbar
                    dense
                    flat
                    color="lighten-1"
                    class="user-form-dialog khawin-background-color"
                >
                    <span v-if="editMode === false" class="white--text khmer-font">
                        <v-icon left color="white">mdi-shield-account</v-icon>
                        {{ $t('role.frmNameAdd') }}
                        </span>
                    <span v-else class="white--text khmer-font"
                    ><v-icon left dark>mdi-account-edit</v-icon>{{ $t('role.frmNameEdit') }}</span
                    >
                </v-toolbar>
                    <v-card-text class="px-5 py-0 pt-5 khmer-font">
                        <v-text-field
                                v-model="form.role"
                                v-bind:label="$t('role.txtRole')"
                                placeholder="e.g. admin, user, etc"
                                outlined
                                clearable
                                prepend-inner-icon="mdi-shield-account"
                                :error-messages="errorsMessage.role"
                        ></v-text-field>

                        <v-textarea
                            v-model="form.description"
                            v-bind:label="$t('role.txtDescrition')"
                            placeholder="description..."
                            outlined
                            clearable
                            prepend-inner-icon="mdi-pencil"
                            rows="2"
                            :error-messages="errorsMessage.description"
                        ></v-textarea>
                        </v-card-text>

                        <v-card-subtitle class="py-0 ma-0 px-5 font-weight-medium khmer-font">
                            {{ $t('role.lblSelectMenu') }}
                            <br>
                            <small v-if="errorsMessage.menu" class="red--text">{{ errorsMessage.menu[0] }}</small>
                        </v-card-subtitle>
                        <v-card-text
                            class="blue-grey lighten-5 pa-0 ma-0 mx-auto"
                            style="height: 250px; width: 90%;"
                        >
                            <v-treeview
                                :items="menus"
                                activatable
                                open-on-click
                                selectable
                                dense
                                open-all
                                v-model="form.menu"
                                :error-messages="errorsMessage.menu"
                            >
                                <template v-slot:prepend="{ item }">
                                    <v-icon v-if="item.icon" small class="pa-0 ma-0">{{ item.icon }}</v-icon>
                                </template>
                                <template v-slot:label="{ item }">
                                    <div class="treeview-item pa-0 ma-0 khmer-font">
                                        <span v-if="item.name == 'dashboard'">{{ $t('menu.dashboard') }}</span>
                                        <span v-if="item.name == 'position'">{{ $t('menu.position') }}</span>
                                        <span v-if="item.name == 'absent'">{{ $t('menu.absent') }}</span>
                                        <span v-if="item.name == 'employeeList'">{{ $t('menu.employeeList') }}</span>
                                        <span v-if="item.name == 'report'">{{ $t('menu.report') }}</span>
                                        <span v-if="item.name == 'absentList'">{{ $t('menu.absentList') }}</span>
                                        <span v-if="item.name == 'user'">{{ $t('menu.user') }}</span>
                                        <span v-if="item.name == 'setting'">{{ $t('menu.setting') }}</span>
                                        <span v-if="item.name == 'account'">{{ $t('menu.account') }}</span>
                                        <span v-if="item.name == 'role'">{{ $t('menu.role') }}</span>
                                        <span v-if="item.name == 'Inventory'">{{ $t('menu.Inventory') }}</span>
                                        <span v-if="item.name == 'AddStock'">{{ $t('menu.AddStock') }}</span>
                                        <span v-if="item.name == 'issueItem'">{{ $t('menu.issueItem') }}</span>
                                        <span v-if="item.name == 'ReturnItem'">{{ $t('menu.ReturnItem') }}</span>
                                        <span v-if="item.name == 'humanResourceSetting'">{{ $t('menu.humanResourceSetting') }}</span>
                                        <span v-if="item.name == 'branchSetting'">{{ $t('menu.branchSetting') }}</span>
                                        <span v-if="item.name == 'departmentSetting'">{{ $t('menu.departmentSetting') }}</span>
                                        <span v-if="item.name == 'resignationSetting'">{{ $t('menu.resignationSetting') }}</span>
                                        <span v-if="item.name == 'leaveTypeSetting'">{{ $t('menu.leaveTypeSetting') }}</span>
                                        <span v-if="item.name == 'leaveReason'">{{ $t('menu.leaveReason') }}</span>
                                        <span v-if="item.name == 'inventorySetting'">{{ $t('menu.inventorySetting') }}</span>
                                        <span v-if="item.name == 'addItem'">{{ $t('menu.addItem') }}</span>
                                        <span v-if="item.name == 'AssetCategory'">{{ $t('menu.AssetCategory') }}</span>
                                        <span v-if="item.name == 'itemSupplier'">{{ $t('menu.itemSupplier') }}</span>
                                        <span v-if="item.name == 'itemStore'">{{ $t('menu.itemStore') }}</span>
                                        <span v-if="item.name == 'HumanResource'">{{ $t('menu.HumanResource') }}</span>
                                        <span v-if="item.name == 'StaffInformation'">{{ $t('menu.StaffInformation') }}</span>
                                        <span v-if="item.name == 'StaffAttendance'">{{ $t('menu.StaffAttendance') }}</span>
                                        <span v-if="item.name == 'PayRoll'">{{ $t('menu.PayRoll') }}</span>
                                        <span v-if="item.name == 'ApplyLeave'">{{ $t('menu.ApplyLeave')}}</span>
                                        <span v-if="item.name == 'ApproveStaffLeave'">{{ $t('menu.ApproveStaffLeave') }}</span>
                                        <span v-if="item.name == 'StaffRating'">{{ $t('menu.StaffRating') }}</span>
                                        <span v-if="item.name == 'StaffResignation'">{{ $t('menu.StaffResignation') }}</span>
                                        <span v-if="item.name == 'systemSetting'">{{ $t('menu.systemSetting') }}</span>
                                        <span v-if="item.name == 'AccountSystemSetting'">{{ $t('menu.AccountSystemSetting') }}</span>
                                        <span v-if="item.name == 'UserSystemSetting'">{{ $t('menu.UserSystemSetting') }}</span>
                                        <span v-if="item.name == 'RoleSystemSetting'">{{ $t('menu.RoleSystemSetting') }}</span>
                                        <span v-if="item.name == 'parameter'">{{ $t('menu.parameter') }}</span>
                                        <span v-if="item.name == 'WorkFlow'">{{ $t('menu.WorkFlow') }}</span>
                                        <span v-if="item.name == 'menuSystemSetting'">{{ $t('menu.menuSystemSetting') }}</span>
                                        <span v-if="item.name == 'shift'">{{ $t('menu.shift') }}</span>
                                        <span v-if="item.name == 'validation'">{{ $t('menu.validation') }}</span>
                                        <span v-if="item.name == 'leaveRequest'">{{ $t('menu.leaveRequest') }}</span>
                                        <span v-if="item.name == 'Staff'">{{ $t('menu.Staff') }}</span>
                                        <span v-if="item.name == 'Attendance'">{{ $t('menu.Attendance') }}</span>
                                        <span v-if="item.name == 'LeaveEntry'">{{ $t('menu.LeaveEntry') }}</span>
                                        <span v-if="item.name == 'CalculatePayroll'">{{ $t('menu.CalculatePayroll') }}</span>
                                        <span v-if="item.name == 'BasicSalary'">{{ $t('menu.BasicSalary') }}</span>
                                        <span v-if="item.name == 'Holiday'">{{ $t('menu.Holiday') }}</span>
                                        <span v-if="item.name == 'StaffPosition'">{{ $t('menu.StaffPosition') }}</span>
                                        <span v-if="item.name == 'DocumentForm'">{{ $t('menu.DocumentForm') }}</span>
                                        <span v-if="item.name == 'Document'">{{ $t('menu.Document') }}</span>
                                        <span v-if="item.name == 'SystemLog'">{{ $t('menu.SystemLog') }}</span>
                                        <span v-if="item.name == 'StaffShift'">{{ $t('menu.StaffShift') }}</span>
                                    </div>
                                </template>

                            </v-treeview>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions class="justify-end khmer-font">
                            <v-btn
                                color="blue darken-1"
                                text
                                @click="closeDialog"
                            >
                                {{ $t('role.btnCancel') }}
                            </v-btn>
                            <v-btn
                                color="blue darken-1"
                                text
                                :loading="btnSaveLoading"
                                @click="editMode ? updateRole() : createRole()"
                                ref="login()"
                            >
                            <!-- @submit.prevent="login()" editMode ? updateRole() : createRole()"-->
                                {{ $t('role.btnSave') }}
                            </v-btn>
                        </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- table -->
        <v-card class="mx-auto table-card">
            <v-data-table
                :headers="headers"
                :items="roles"
                :search="search"
                :loading="tableLoading"
                loading-text="Loading users data"
                :footer-props="{
                    'items-per-page-text':$t('user.tbPagination')
                }"
            >
                <template v-slot:[`item.id`]="item">
                    {{ item.index + 1 }}
                </template>

                <template v-slot:[`item.role.name`]="{ item }">
                    <span class="text-capitalize">
                        {{ item.role.name }}
                    </span>
                </template>

                <template v-slot:[`item.menu_count`]="{ item }">
                    <v-menu
                        open-on-hover
                        offset-x
                        max-width="250"
                        max-height="330"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-chip
                                color="indigo"
                                v-bind="attrs"
                                v-on="on"
                                small
                                outlined
                            >
                                <span>{{ item.menu_count }}</span>
                            </v-chip>
                        </template>

                        <v-card>
                            <v-card-subtitle
                                class="blue-grey lighten-4 pa-2 ma-1"
                            >
                                <span class="font-weight-medium text-capitalize">
                                    {{ item.role.name }}
                                </span>
                            </v-card-subtitle>
                            <v-card-text>
                                <v-treeview
                                    dense
                                    open-all
                                    :items="item.menu"
                                >
                                    <template v-slot:prepend="{ item }">
                                        <v-icon v-if="item.icon" small class="pa-0 ma-0">{{ item.icon }}</v-icon>
                                    </template>
                                    <template v-slot:label="{ item }">
                                        <div class="treeview-item pa-0 ma-0 khmer-font">
                                            <span v-if="item.name == 'dashboard'">{{ $t('menu.dashboard') }}</span>
                                            <span v-if="item.name == 'position'">{{ $t('menu.position') }}</span>
                                            <span v-if="item.name == 'absent'">{{ $t('menu.absent') }}</span>
                                            <span v-if="item.name == 'employeeList'">{{ $t('menu.employeeList') }}</span>
                                            <span v-if="item.name == 'absentList'">{{ $t('menu.absentList') }}</span>
                                            <span v-if="item.name == 'report'">{{ $t('menu.report') }}</span>
                                            <span v-if="item.name == 'user'">{{ $t('menu.user') }}</span>
                                            <span v-if="item.name == 'setting'">{{ $t('menu.setting') }}</span>
                                            <span v-if="item.name == 'account'">{{ $t('menu.account') }}</span>
                                            <span v-if="item.name == 'role'">{{ $t('menu.role') }}</span>
                                            <span v-if="item.name == 'Inventory'">{{ $t('menu.Inventory') }}</span>
                                            <span v-if="item.name == 'AddStock'">{{ $t('menu.AddStock') }}</span>
                                            <span v-if="item.name == 'issueItem'">{{ $t('menu.issueItem') }}</span>
                                            <span v-if="item.name == 'ReturnItem'">{{ $t('menu.ReturnItem') }}</span>
                                            <span v-if="item.name == 'humanResourceSetting'">{{ $t('menu.humanResourceSetting') }}</span>
                                            <span v-if="item.name == 'branchSetting'">{{ $t('menu.branchSetting') }}</span>
                                            <span v-if="item.name == 'departmentSetting'">{{ $t('menu.departmentSetting') }}</span>
                                            <span v-if="item.name == 'resignationSetting'">{{ $t('menu.resignationSetting') }}</span>
                                            <span v-if="item.name == 'leaveTypeSetting'">{{ $t('menu.leaveTypeSetting') }}</span>
                                            <span v-if="item.name == 'leaveReason'">{{ $t('menu.leaveReason') }}</span>
                                            <span v-if="item.name == 'inventorySetting'">{{ $t('menu.inventorySetting') }}</span>
                                            <span v-if="item.name == 'addItem'">{{ $t('menu.addItem') }}</span>
                                            <span v-if="item.name == 'AssetCategory'">{{ $t('menu.AssetCategory') }}</span>
                                            <span v-if="item.name == 'itemSupplier'">{{ $t('menu.itemSupplier') }}</span>
                                            <span v-if="item.name == 'itemStore'">{{ $t('menu.itemStore') }}</span>
                                            <span v-if="item.name == 'HumanResource'">{{ $t('menu.HumanResource') }}</span>
                                            <span v-if="item.name == 'StaffAttendance'">{{ $t('menu.StaffAttendance') }}</span>
                                            <span v-if="item.name == 'PayRoll'">{{ $t('menu.PayRoll') }}</span>
                                            <span v-if="item.name == 'ApplyLeave'">{{ $t('menu.ApplyLeave') }}</span>
                                            <span v-if="item.name == 'ApproveStaffLeave'">{{ $t('menu.ApproveStaffLeave') }}</span>
                                            <span v-if="item.name == 'StaffRating'">{{ $t('menu.StaffRating') }}</span>
                                            <span v-if="item.name == 'StaffResignation'">{{ $t('menu.StaffResignation') }}</span>
                                            <span v-if="item.name == 'systemSetting'">{{$t('menu.systemSetting') }}</span>
                                            <span v-if="item.name == 'AccountSystemSetting'">{{$t('menu.AccountSystemSetting')}}</span>
                                            <span v-if="item.name == 'UserSystemSetting'">{{$t('menu.UserSystemSetting')}}</span>
                                            <span v-if="item.name == 'RoleSystemSetting'">{{$t('menu.RoleSystemSetting')}}</span>
                                            <span v-if="item.name == 'parameter'">{{$t('menu.parameter')}}</span>
                                            <span v-if="item.name == 'WorkFlow'">{{$t('menu.WorkFlow')}}</span>
                                            <span v-if="item.name == 'menuSystemSetting'">{{$t('menu.menuSystemSetting')}}</span>
                                            <span v-if="item.name == 'shift'">{{$t('menu.shift')}}</span>
                                            <span v-if="item.name == 'validation'">{{ $t('menu.validation') }}</span>
                                            <span v-if="item.name == 'leaveRequest'">{{ $t('menu.leaveRequest') }}</span>
                                            <span v-if="item.name == 'Staff'">{{ $t('menu.Staff') }}</span>
                                            <span v-if="item.name == 'Attendance'">{{ $t('menu.Attendance') }}</span>
                                            <span v-if="item.name == 'LeaveEntry'">{{ $t('menu.LeaveEntry') }}</span>
                                            <span v-if="item.name == 'CalculatePayroll'">{{ $t('menu.CalculatePayroll') }}</span>
                                            <span v-if="item.name == 'BasicSalary'">{{ $t('menu.BasicSalary') }}</span>
                                            <span v-if="item.name == 'Holiday'">{{ $t('menu.Holiday') }}</span>
                                            <span v-if="item.name == 'StaffPosition'">{{ $t('menu.Position') }}</span>
                                            <!-- Modify by Huychoung -->
                                            <span v-if="item.name == 'DocumentForm'">{{ $t('menu.DocumentForm') }}</span>
                                            <span v-if="item.name == 'Document'">{{ $t('menu.Document') }}</span>
                                            <span v-if="item.name == 'SystemLog'">{{ $t('menu.SystemLog') }}</span>
                                            <span v-if="item.name == 'StaffShift'">{{ $t('menu.StaffShift') }}</span>
                                            </div>
                                        </template>
                                </v-treeview>
                            </v-card-text>
                        </v-card>
                    </v-menu>
                </template>

                <template v-slot:[`item.menu`]="{ item }">
                    <v-chip
                        v-for="menu in item.menu" :key="menu.id"
                        outlined
                        small
                        class="ma-1"
                    >
                        {{ menu.name }}
                    </v-chip>
                </template>

                <template v-slot:[`item.user`]="{ item }">
                    <span v-for="user in item.user" :key="user.id">
                        <v-chip
                            small
                            outlined
                        >
                            <v-icon left small>mdi-account</v-icon>
                            {{ user.name }}
                        </v-chip>
                    </span>
                </template>

                <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="editUser(item)">mdi-pencil</v-icon>
                    <v-icon small class="mr-2" @click="deleteRole(item.role.id, item.role.name)">mdi-delete</v-icon>
                </template>

            </v-data-table>
        </v-card>

        <!-- ---------------Snacbar--------------- -->
      <v-snackbar v-model="snackbar" color="cyan darken-2" dark>
        {{ alertSnackbarMsg }}
        <template v-slot:action="{ attrs }">
          <v-btn dark text v-bind="attrs" @click="snackbar = false" small>
            {{ $t('user.msgClose') }}
          </v-btn>
        </template>
      </v-snackbar>

      <!-- ----------dialogDelete------------ -->
      <v-dialog v-model="dialogDelete" max-width="350px">
        <v-card>
          <div class="text-center">
            <v-sheet class="px-5 pt-7 pb-4 mx-auto text-center d-inline-block">
              <v-icon class="text-center pb-3" x-large color="red lighten-2"
                >mdi-alert</v-icon
              >
              <div class="grey--text text--darken-3 text-body-2 mb-4">
                <!-- {{ $t('user.deleteMessage') }} -->
                Are you sure delete role
                <b class="red--text tex--lighten-2">{{ roleNameDelete }}</b> ?
              </div>

              <v-btn
                :disabled="btnLoading"
                class="ma-1"
                depressed
                small
                @click="dialogDelete = false"
              >
                {{ $t('user.btnCancel') }}
              </v-btn>

              <v-btn
                :loading="btnLoading"
                class="ma-1"
                dark
                color="red"
                small
                depressed
                @click="submitDelete"
              >
                {{ $t('user.btnDelete') }}
              </v-btn>
            </v-sheet>
          </div>
        </v-card>
      </v-dialog>
    </div>

</template>

<script>
    import store from '../store';
    export default {
        data() {
            return {
                menus: [],
                roles: [],
                selectedItem: 1,
                roleForm: false,
                editMode: false,
                btnSaveLoading: false,
                form: new Form({
                    id: '',
                    role: '',
                    description: '',
                    menu: []
                }),
                errorsMessage: "",
                search: "",
                tableLoading: true,
                btnSaveLoading: false,
                btnLoading: false,
                alertSnackbarMsg: "",
                dialogDelete: false,
                snackbar: false,
                dialogDelete: false,
                roleNameDelete: "",

                testing: [],
            }
        },

        computed: {
            userToken() {
                return store.state.user;
            },

            formTitle() {
                return this.editMode === false ? "Add Role" : "Edit Role";
            },

            headers(){
            return[
                {
                    text: "#",
                    align: "start",
                    value: "id",
                },
                { text: this.$t('role.tbRole'), value: "role.name" },
                { text: this.$t('role.tbDescription'), value: "role.description" },
                { text: this.$t('user.tbUpdate_Delete'), sortable: false, align: "center", value: "actions" },
            ]
        }
        },

        mounted(){
            if(this.userToken.token){
                this.ReadMenu();
                this.ReadRole();
            }
        },

        methods:{

            createRole(){
                this.tableLoading = true;
                this.btnSaveLoading = true;
                this.form
                .post("api/create-role", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    if(response.status == 200){
                        this.alertSnackbarMsg = response.data.message;
                        this.snackbar = true;
                        this.btnSaveLoading = false;
                        this.tableLoading = false;
                        this.ReadRole();
                        this.closeDialog();
                    }
                })
                .catch((errors) => {
                    this.errorsMessage = errors.response.data.errors;
                    this.btnSaveLoading = false;
                });
            },

            openDialog() {
                this.roleForm = true;
            },

            closeDialog() {
                this.editMode = false;
                this.roleForm = false;
                this.form.role = '',
                this.form.description= '',
                this.form.menu = []
                this.errorsMessage = "";
            },

            ReadMenu() {
                axios
                .get(this.$globalConfig.ipHost + "api/menu-data", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    if(response.status == 200){
                        this.menus = response.data;
                        this.tableLoading = false;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            ReadRole() {
                axios
                .get(this.$globalConfig.ipHost + "api/role-data", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.roles = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            editUser(menu){
                this.form.id = menu.role.id;
                this.editMode = true;
                this.form.role = menu.role.name;
                this.form.description = menu.role.description;
                this.form.menu = menu.role.menu;
                this.roleForm = true;
            },

            async updateRole(){
                this.btnSaveLoading = true;
                this.tableLoading = true;
                await new Promise((resolve) => setTimeout(resolve, 1000));
                this.form
                .post("api/update-role/" + this.form.id, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                // .then((response) => {
                //     if(response.status == 200){
                //         this.alertSnackbarMsg = response.data.message;
                //         this.tableLoading = false;
                //         this.snackbar = true;
                //         this.ReadRole();
                //         this.closeDialog();
                //         this.btnSaveLoading = false;
                //     }

                // modify LINA 20/07/2023 #cms-18
                .then((response) => {
                    if (response.status == null) {
                        this.alertSnackbarMsg = response.data.message;
                        this.closeDialog();
                    } else{
                        this.snackbar = true;
                        this.alertSnackbarMsg = "Role has been update please login again.";
                        this.$store.dispatch("logout").then(() => {
                        this.$router.push({ name: "login" });
                        });
                    }
                    this.ReadRole();
                    this.closeDialog();
                    this.tableLoading = false;
                    this.btnSaveLoading = false;
                })
                .catch((errors) => {
                    this.errorsMessage = errors.response.data.errors;
                    this.btnSaveLoading = false;
                });
            },

            deleteRole(id, name){
                this.form.id = id;
                this.dialogDelete = true;
                this.roleNameDelete = name;
            },

            async submitDelete() {
                this.btnLoading = true;
                this.tableLoading = true;
                axios
                .delete("/api/delete-role/" + this.form.id, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
                })
                .then((response) => {
                    if(response.status == 200){
                        this.ReadRole();
                        this.dialogDelete = false;
                        // this.alertSnackbarMsg = this.$t('user.deleteMsg');
                        this.alertSnackbarMsg = 'Role deleted successfully';
                        this.snackbar = true;
                        this.btnLoading = false;
                        this.tableLoading = false;
                    }
                })
                    .catch((error) => {
                    this.btnLoading = false;
                    this.tableLoading = false;
                });

            }
        }
    }
</script>
