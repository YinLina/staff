<template>
  <div id="user">
    <div>
      <v-row class="mb-1">
        <v-col cols="sm-8">
          <h3 class="grey--text text--darken-2">
            <v-icon class="mb-1" color="grey darken-2"
              >mdi-account-multiple</v-icon
            >
            <span class="text-decoration-underline font-weight-medium">{{ $t('user.listTitle') }}</span>
            <v-chip color="grey lighten-2 grey--text text--darken-3">{{
              userCount
            }}</v-chip>
          </h3>
        </v-col>
        <v-col cols="sm-4" class="text-right">
          <v-btn
            small
            class="add-user white--text font-weight-regular khawin-background-color"
            @click="openDialog"
            ><v-icon left>mdi-plus</v-icon>{{ $t('user.btnAdd') }}</v-btn
          >
        </v-col>
      </v-row>

      <!-- -------table---- -->
      <v-card class="mx-auto table-card">
        <v-data-table
            :headers="headers"
            :items="userData"
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

          <!-- <template v-slot:[`item.UserID`]="{ item }">
            <v-avatar
              size="40"
              class="m-1"
              left
              v-if="item.employee.pic == 'default.png'"
              :color="'#' + item.employee.profile_color"
            >
              {{
                item.UserID
                  .split(" ")
                  .map((x) => x[0].toUpperCase())
                  .join("")
              }}
            </v-avatar>
            <v-avatar size="37" class="m-1" left v-else>
              <v-img :src="'/profiles/' + item.employee.pic" />
            </v-avatar>
            <small
              class="
                font-weight-medium
                grey--text
                text--darken-2 text-capitalize
                user-name
              "
            >
              {{ item.UserID }}
            </small>
          </template> -->
          
          <template v-slot:[`item.email`]="{ item }"></template>
          
          <template v-slot:[`item.role`]="{ item }">
            <v-chip
                small
                outlined
                class="
                    font-weight-medium
                    grey--text
                    text--darken-2
                    text-capitalize
                    user-name
                "
            >
                {{ item.role.role }}
            </v-chip>
          </template>

          <template v-slot:[`item.phone_number`]="{ item }"></template>

          <template v-slot:[`item.actions`]="{ item }">
            <v-icon x-small class="mr-2" @click="editUser(item)"
              >mdi-pencil</v-icon
            >
            <v-icon x-small class="mr-2" @click="deleteUser(item.id, item.UserID)"
              >mdi-delete</v-icon
            >
          </template>
        </v-data-table>
      </v-card>

        <!-- --------User-Insert-Form------ -->
        <v-dialog v-model="userForm" width="550" persistent overlay-opacity="0.5">
            <v-card>
                <v-toolbar
                    dense
                    flat
                    color="lighten-1"
                    class="user-form-dialog khawin-background-color"
                >
                    <span v-if="editMode === false" class="white--text khmer-font">
                      <v-icon left color="white">mdi-account-plus</v-icon>
                          {{ $t('user.frmTitleAdd') }}
                      </span>
                    <span v-else class="white--text khmer-font">
                      <v-icon left dark>mdi-account-edit</v-icon>
                          {{ $t('user.frmTitleAdd') }}
                    </span>
                </v-toolbar>

                <form
                    @submit.prevent="editMode ? updateUser() : createUser()"
                    enctype="multipart/form-data"
                    class="user-form"
                >
                        <v-card-text>
                          <v-row>
                            <v-col sm="8">
                              <v-autocomplete
                                  v-model="form.role_id"
                                  :items="roleData"
                                  :item-text="(item) => item.role"
                                  item-value="id"
                                  class="khmer-font"
                                  prepend-icon="mdi-shield-account"
                                  color="cyan darken-1"
                                  v-bind:label="$t('employee.role')"
                                  dense
                                  :error-messages="errorsMessage.role_id"
                              ></v-autocomplete>

                              <!-- <v-text-field
                                v-model="form.name"
                                v-bind:label="$t('user.txtUserID')"
                                class="khmer-font"
                                color="cyan darken-1"
                                prepend-icon="mdi-account-edit"
                                :error-messages="errorsMessage.name"
                              ></v-text-field> -->
                              <v-text-field
                                v-model="form.userId"
                                v-bind:label="$t('user.txtUserID')"
                                class="khmer-font"
                                color="cyan darken-1"
                                prepend-icon="mdi-account-edit"
                                :error-messages="errorsMessage.userId"
                              ></v-text-field>

                              <v-autocomplete
                                v-model="form.employee_id"
                                :items="staffActiveData"
                                :item-text="(item) => item.fullName"
                                item-value="id"
                                class="khmer-font"
                                clearable
                                v-bind:label="$t('user.txtUserName')"
                                color="cyan darken-1"
                                prepend-icon="mdi-account"
                                :error-messages="errorsMessage.employee_id"
                              >
                              <template v-slot:selection="data">
                                <v-chip
                                    label
                                    dark
                                    color="blue-grey darken-2"
                                    class="text-capitalize pa-1 font-weight-regular"
                                    small
                                >
                                    {{ data.item.fullName }}
                                </v-chip>
                              </template>
                            </v-autocomplete>
                            <!-- ==================================== -->
                            <v-row>
                                <v-col sm="11">
                                </v-col>
                                <v-col sm="1">
                                    <v-icon small class="white--text">mdi-plus</v-icon>
                                </v-col>
                            </v-row>

                            <!-- ==================================== -->

                            <v-text-field
                                v-model="form.password"
                                color="cyan darken-1"
                                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                :type="showPassword ? 'text' : 'password'"
                                v-bind:label="$t('user.txtPassword')"
                                class="khmer-font"
                                hint="At least 8 characters"
                                prepend-icon="mdi-key-variant"
                                @click:append="showPassword = !showPassword"
                            ></v-text-field>

                            <v-text-field
                                v-model="form.password_confirmation"
                                color="cyan darken-1"
                                :append-icon="showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
                                :type="showPasswordConfirm ? 'text' : 'password'"
                                v-bind:label="$t('user.txtPasswordConfir')"
                                class="khmer-font"
                                hint="At least 8 characters"
                                prepend-icon="mdi-key-variant"
                                @click:append="showPasswordConfirm = !showPasswordConfirm"
                                :error-messages="errorsMessage.password"
                            ></v-text-field>
                          </v-col>
                          <v-col sm="4">
                            </v-col>
                        </v-row>
                      </v-card-text>
                    <v-card-actions class="justify-end">
                    <v-btn
                        depressed
                        dark
                        color="grey lighten-1"
                        small
                        @click="closeDialog"
                        >{{ $t('user.btnCancel') }}</v-btn
                    >
                    <v-btn
                        depressed
                        dark
                        class="khawin-background-color"
                        small
                        type="submit"
                        :loading="btnSaveLoading"
                        >{{ $t('user.btnSave') }}</v-btn
                    >
                    </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
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
                {{ $t('user.deleteMessage') }}
                <b class="red--text tex--lighten-2">{{ userNameDelete }}</b> ?
              </div>

              <v-btn
                :disabled="btnLoading"
                class="ma-1 khmer-font"
                depressed
                small
                @click="dialogDelete = false"
              >
                {{ $t('user.btnCancel') }}
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
                {{ $t('user.btnDelete') }}
              </v-btn>
            </v-sheet>
            
          </div>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogCheckUser" max-width="350px">
        <v-card>
          <div class="text-center">
            <v-sheet class="px-5 pt-7 pb-4 mx-auto text-center d-inline-block">
              <v-icon class="text-center pb-3" x-large color="red lighten-2"
                >mdi-alert</v-icon
              >
              <div class="grey--text text--darken-3 text-body-2 mb-4">
                
                <b class="red--text tex--lighten-2">{{ checkUserName }}</b> ?
              </div>

              <v-btn
                :disabled="btnLoading"
                class="ma-1 khmer-font"
                depressed
                small
                @click="dialogCheckUser = false"
              >
                {{ $t('user.btnCancel') }}
              </v-btn>

            </v-sheet>
            
          </div>
        </v-card>
      </v-dialog>
    </div>
  </div>
</template>

<style scoped>
.file-input__input {
  width: 0.1px;
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}

.file-input__label {
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  border-radius: 4px;
  font-size: 14px;
  color: #fff;
  font-size: 12px;
  padding: 2px 8px;
  background-color: #0d5ca7;
}

.file-input__label svg {
  height: 16px;
  margin-right: 4px;
}
</style>

<script>
import store from '../store';
export default {
  data() {
    return {
      editMode: false,
      search: "",
      snackbar: false,
      tableLoading: true,
      showPassword: false,
      showPasswordConfirm: false,
      userData: [],
      userCount: "",
      userForm: false,
      form: new Form({
        id: "",
        role_id: "",
        userId: "",
        image: null,
        password: "",
        password_confirmation: "",
        employee_id: "",
      }),

      roleData: [],
      preview_profile: null,
      btnSaveLoading: false,
      btnLoading: false,
      image_validation: "",
      alertSnackbarMsg: "",
      errorsMessage: "",
      dialogDelete: false,
      userNameDelete: "",
      checkUserName: '',
      dialogCheckUser: false,
      staffActiveData: [],
    };
  },

    computed: {
        userToken() {
            return store.state.user;
        },

        formTitle() {
            return this.editMode === false ? "Add User" : "Edit User";
        },

        headers(){
            return[
                {
                    text: "#",
                    align: "start",
                    value: "id",
                },
                { text: this.$t('user.tbName'), value: "UserID" },
                { text: this.$t('user.tbRole'), value: "role" },
                { text: this.$t('absent.tbEmp'), value: "fullName" },
                { text: this.$t('user.tbUpdate_Delete'), sortable: false, align: "center", value: "actions" },
            ]
        }
    },

    mounted() {
        if(this.userToken.token){
            this.ReadUser();
            this.ReadRole();
            this.ReadStaffActive();
        }
        this.activateMultipleDraggableDialogs();
    },

    methods: {
        ReadUser() {
            axios
            .get(this.$globalConfig.ipHost + "api/read-user", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.userData = response.data.data;
                for(var i = 0; i < this.userData.length; i++) {
                  if(this.userData[i].employee != null) {
                    var concatStr = this.userData[i].employee.last_name != "" ? " " : "";
                    this.userData[i].fullName = this.userData[i].employee.first_name + concatStr + this.userData[i].employee.last_name;
                    // this.userData[i].phone = this.userData[i].employee.phone_number[0].phone;
                    this.userData[i].User_id = this.userData[i].employee.User_id;
                  } else {
                    this.userData[i].fullName = "";
                    var employee = { pic: "default.png" };
                    this.userData[i].employee = employee;
                  }
                }
                this.userCount = response.data.data.length;
                this.tableLoading = false;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        ReadRole(){
            axios
            .get(this.$globalConfig.ipHost + "api/role-user", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.roleData = response.data.data;
            })
            .catch((error) => {
                console.log(error);
            });
        },
        //----------------------------------------

        openDialog() {
            this.userForm = true;
        },

        closeDialog() {
            this.editMode = false;
            this.userForm = false;
            this.form.role_id = "";
            this.form.userId = "";
            this.form.image=null;
            this.preview_profile = null;
            this.preview_profile_edit = null;
            this.form.password = "";
            this.form.password_confirmation = "";
            this.tableLoading = false;
            this.errorsMessage = "";
            this.btnSaveLoading = false;
        },

        createImage(file) {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.preview_profile = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        onFileChange(file) {
            if (!file) {
                return;
            }
            this.createImage(file);
        },
        clearImage() {
         this.preview_profile = null;
         this.form.image = null;
        },
        // ---------------------------------

        createUser() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form.post("api/create-user", {
              headers: {
                  Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
              },
            })
            .then((response) => {
                console.log(response)
                this.ReadUser();
                this.closeDialog();
                this.alertSnackbarMsg = this.$t('user.savedMsg');
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

        editUser(user) {
            this.editMode = true;
            this.form.id = user.id;
            this.form.role_id = user.role_id;
            this.form.role_id = user.role.id
            this.form.userId = user.UserID;
            this.form.employee_id = user.employee != null ? user.employee.id : 0;
            this.preview_profile_edit = user.profile;
            this.userForm = true;
        },

        async updateUser() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
                .post("/api/update-user/" + this.form.id, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
                })
                .then((response) => {
                    this.ReadUser();
                    this.closeDialog();
                    this.alertSnackbarMsg = this.$t('user.updateMsg');
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
        deleteUser(user, name) {
            this.form.id = user;
            this.userNameDelete = name;
            this.dialogDelete = true;
        },

        async submitDelete() {
        this.btnLoading = true;
        this.tableLoading = true;
        await new Promise((resolve) => setTimeout(resolve, 1000));
        axios
            .delete("/api/delete-user/" + this.form.id, {
            headers: {
                Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
            },
            })
            .then((response) => {
                this.ReadUser();
                this.dialogDelete = false;
                this.alertSnackbarMsg = this.$t('user.deleteMsg');;
                this.snackbar = true;
                // this.alertSnackbarMsg = response.data.message;
                if(response.data.message){
                  this.alertSnackbarMsg = this.$t('user.deleteMsg');
                  this.snackbar = true;
                }
                if(response.data.checkUser){
                  this.dialogCheckUser = true;
                  this.checkUserName = response.data.checkUser
                }
                
                this.btnLoading = false;
                this.tableLoading = false;
            })
            .catch((error) => {
            this.btnLoading = false;
            this.tableLoading = false;
            });
        },
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
    },
};
</script>
