<template>
  <div id="absent">
    <v-row>
      <v-col cols="12" sm="6">
        <v-row>
          <v-col cols="12" sm="12" md="5">
            <h3 class="grey--text text--darken-2">
              <v-icon class="mb-1" color="grey darken-2"
                >mdi-account-cancel</v-icon
              >
              <span class="text-decoration-underline font-weight-medium">{{ $t('absent.listTitle') }}</span>
              <v-chip color="grey lighten-2 grey--text text--darken-3">{{
                absentCount
              }}</v-chip>
            </h3>
          </v-col>
          <v-col cols="12" sm="12" md="7">
            <v-text-field
                v-model="searchAbsent"
                append-icon="mdi-magnify"
                class="txt-search"
                v-bind:label="$t('absent.search')"
                single-line
            ></v-text-field>
          </v-col>
        </v-row>
      </v-col>

      <v-col cols="12" sm="6" class="text-end">
        <v-btn
          small
          class="add-user white--text pa-2 font-weight-regular mb-2 khawin-background-color"
          @click="openDialog"
          ><v-icon left>mdi-plus</v-icon>{{ $t('absent.btnAdd') }}</v-btn
        >
      </v-col>
    </v-row>

    <!-- -------table---- -->
    <v-card class="mx-auto table-card">
        <v-data-table
            :headers="headers"
            :items="absentData"
            :search="searchAbsent"
            :loading="tableLoading"
            loading-text="Loading users data"
            :group-desc="true"
            group-by="year_month"
            :footer-props="{
                'items-per-page-text':$t('absent.tbPagination')
            }"
        >
            <template v-slot:[`group.header`]="{ group, headers, toggle, isOpen, items,}">
                <td :colspan="headers.length" class="group-header">
                    <v-btn @click="toggle" small icon :ref="group" :data-open="isOpen">
                        <v-icon v-if="isOpen">mdi-chevron-up</v-icon>
                        <v-icon v-else>mdi-chevron-down</v-icon>
                    </v-btn>
                    <v-chip
                        class="pa-2 py-2"
                        color="blue-grey"
                        text-color="white"
                        small
                    >
                        <!-- {{ formatDateGroup(group).split(',')[0] }} -->
                        <!-- {{ formatDateGroup(group) }} -->

                        <span v-if="formatDateGroup(group).split(',')[0] == 'January'">
                            {{ $t('absent.january') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'February'">
                            {{ $t('absent.february') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'March'">
                            {{ $t('absent.march') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'April'">
                            {{ $t('absent.april') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'May'">
                            {{ $t('absent.may') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'June'">
                            {{ $t('absent.june') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'July'">
                            {{ $t('absent.july') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'August'">
                            {{ $t('absent.august') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'September'">
                            {{ $t('absent.september') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'October'">
                            {{ $t('absent.october') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'November'">
                            {{ $t('absent.november') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>
                        <span v-if="formatDateGroup(group).split(',')[0] == 'December'">
                            {{ $t('absent.december') }}, {{ formatDateGroup(group).split(',')[1] }}
                        </span>

                        <v-avatar right class="blue-grey darken-3">
                            {{ items.length }}
                        </v-avatar>
                    </v-chip>
                </td>
            </template>

            <template v-slot:[`item.id`]="item">
                {{ item.index + 1 }}
            </template>

            <template v-slot:[`item.employee.name`]="{ item }">
                <v-chip
                    class="font-weight-medium absent-employee-name"
                    label
                    color="transparent"
                >
                    <v-avatar
                        left
                        class="white--text font-weight-regular absent-profile-avatar"
                        v-if="item.employee.image == 'default.png'"
                        :color="'#' + item.employee.profile_color"
                    >
                    {{
                        item.employee.name
                        .split(" ")
                        .map((x) => x[0].toUpperCase())
                        .join("")
                    }}
                    </v-avatar>
                    <v-avatar left v-else>
                        <v-img :src="'/employees/' + item.employee.image" />
                    </v-avatar>
                    {{ item.employee.name }}
                </v-chip>
            </template>

            <template v-slot:[`item.absent`]="{ item }">
                <span class="font-weight-medium">
                    <span v-if="item.absent == 'fullday'" class="orange--text font-weight-medium">
                        {{ $t('absent.fullDay') }}
                    </span>
                    <span v-if="item.absent == 'halfday'" class="blue-grey--text">
                        {{ $t('absent.halfDay') }}
                    </span>
                </span>
                <span v-if="item.absent_time">
                    <v-chip
                        v-if="item.absent_time == 'morning'"
                        class="absent-day-chip indigo--text"
                        label
                        small
                        outlined
                        color="indigo"
                    >
                        {{ $t('absent.morning') }}
                        <v-icon right small>mdi-weather-sunset</v-icon>
                    </v-chip>

                    <v-chip
                        v-if="item.absent_time == 'afternoon'"
                        class="absent-day-chip orange--text"
                        label
                        small
                        outlined
                        color="orange darken-3"
                    >
                        {{ $t('absent.afternoon') }}
                    <v-icon right x-small>mdi-white-balance-sunny</v-icon>
                    </v-chip>
                </span>
            </template>

            <template v-slot:[`item.day`]="{ item }">
                <!-- dark -->
                <!-- :color="getColor(item.day)" -->
                <v-chip
                  class="text-capitalize absent-day-chip"
                  small
                  label
                  outlined
                >
                  <span v-if="item.day == 'Monday'">{{ $t('absent.monday') }}</span>
                  <span v-if="item.day == 'Tuesday'">{{ $t('absent.tuesday') }}</span>
                  <span v-if="item.day == 'Wednesday'">{{ $t('absent.wednesday') }}</span>
                  <span v-if="item.day == 'Thursday'">{{ $t('absent.thursday') }}</span>
                  <span v-if="item.day == 'Friday'">{{ $t('absent.friday') }}</span>
                  <span v-if="item.day == 'Saturday'">{{ $t('absent.saturday') }}</span>
                  <span v-if="item.day == 'Sunday'">{{ $t('absent.sunday') }}</span>
                </v-chip>
            </template>

            <template v-slot:[`item.date`]="{ item }">
                <v-chip
                    class="pa-1 py-0 absent-date font-weight-bold"
                    small
                    color="transparent"
                    outlined
                    text-color="pink"

                >
                    <v-avatar left class="mr-0">
                        <v-icon small>mdi-calendar-month</v-icon>
                    </v-avatar>
                    {{ formatDate(item.date) }}
                </v-chip>
            </template>

            <template v-slot:[`item.actions`]="{ item }">
            <v-icon small class="mr-2" @click="editAbsent(item)"
                >mdi-pencil</v-icon
            >
            <v-icon
                small
                class="mr-2"
                @click="deleteAbsent(item.id, item.employee.name)"
                >mdi-delete</v-icon
            >
            </template>

            <template v-slot:no-results>
                <span>{{ $t('absent.tabelNotFound') }}</span>
            </template>
      </v-data-table>
    </v-card>

    <!-- --------Absent-Insert-Form------ -->
    <v-dialog v-model="absentForm" width="500" persistent overlay-opacity="0.5">
        <v-card>
            <v-toolbar dense flat color="lighten-1" class="user-form-dialog khawin-background-color">
                <span v-if="editMode === false" class="white--text khmer-font">
                    <v-icon left color="white">mdi-account-cancel</v-icon>
                    {{ $t('absent.frmTitleAdd') }}
                </span>
                <span v-else class="white--text khmer-font">
                    <v-icon left dark>mdi-account-cancel</v-icon>
                    {{ $t('absent.frmTitleEdit') }}
                </span>
            </v-toolbar>

            <form
                @submit.prevent="editMode ? updateAbsent() : createAbsent()"
                enctype="multipart/form-data"
            >
                <v-card-text>
                    <!-- ================= -->
                    <v-autocomplete
                        v-model="form.employee_id"
                        :items="employeeData"
                        :item-text="(item) => item.name"
                        item-value="id"
                        clearable
                        v-bind:label="$t('absent.frmSelect')"
                        class="khmer-font"
                        color="cyan darken-1"
                        prepend-inner-icon="mdi-account-tie"
                        outlined
                        :error-messages="errorsMessage.employee_id"
                    >
                    <template v-slot:selection="data">
                        <v-chip
                            v-bind="data.attrs"
                            :input-value="data.selected"
                            class="mb-1"
                        >
                        <v-avatar left>
                            <v-avatar
                                v-if="data.item.image == 'default.png'"
                                class="white--text"
                                :color="'#' + data.item.profile_color"
                            >
                            <h5 class="font-weight-regular">
                                {{
                                data.item.name
                                    .split(" ")
                                    .map((x) => x[0].toUpperCase())
                                    .join("")
                                }}
                            </h5>
                            </v-avatar>
                            <v-img
                            v-else
                            :src="'/employees/' + data.item.image"
                            sizes="35"
                            ></v-img>
                        </v-avatar>
                        {{ data.item.name }}
                        </v-chip>
                    </template>

                    <template v-slot:item="data">
                        <v-list-item-avatar>
                            <v-avatar
                                v-if="data.item.image == 'default.png'"
                                :color="'#' + data.item.profile_color"
                                size="35"
                            >
                                <span class="white--text">{{
                                data.item.name
                                    .split(" ")
                                    .map((x) => x[0].toUpperCase())
                                    .join("")
                                }}</span>
                            </v-avatar>

                            <v-img
                                v-else
                                :src="'/employees/' + data.item.image"
                                sizes="35"
                            ></v-img>
                        </v-list-item-avatar>
                        <v-list-item-content>
                        <v-list-item-title>
                            {{ data.item.name }} -
                            <v-chip
                                label
                                dark
                                color="blue-grey darken-2"
                                class="text-capitalize pa-1"
                                outlined
                                small
                            >
                                <v-avatar left>
                                    <v-icon small>mdi-account-star</v-icon>
                                </v-avatar>
                                {{ data.item.position.title }}
                            </v-chip>
                        </v-list-item-title>
                        </v-list-item-content>
                    </template>
                    </v-autocomplete>

                    <!-- ======================== -->
                    <v-row>
                        <v-col cols="8">
                            <v-radio-group
                                v-model="form.absent"
                                row
                                class="ma-0 pa-0"
                                :error-messages="errorsMessage.absent"
                            >
                                <v-chip>
                                    <v-radio
                                        class="font-weight-medium"
                                        color="orange"
                                        v-bind:label="$t('absent.frmFullDay')"
                                        value="fullday"
                                    ></v-radio>
                                </v-chip>
                                <v-chip class="font-weight-medium ml-3">
                                    <v-radio
                                        v-bind:label="$t('absent.frmHalfDay')"
                                        value="halfday">
                                    </v-radio>
                                </v-chip>
                            </v-radio-group>
                        </v-col>
                        <v-col cols="4" class="ma-0 pa-0">
                            <v-radio-group
                                v-if="form.absent == 'halfday'"
                                v-model="form.absent_time"
                                column
                                class="ma-0 pa-3"
                                :error-messages="errorsMessage.absent_time"
                            >
                            <v-radio
                                class="font-weight-medium"
                                v-bind:label="$t('absent.frmMorning')"
                                value="morning"
                                color="success"
                            ></v-radio>

                            <v-radio
                                class="font-weight-medium"
                                value="afternoon"
                                v-bind:label="$t('absent.frmAfternoon')"
                                color="warning"
                            ></v-radio>
                            </v-radio-group>
                        </v-col>
                    </v-row>

                    <!-- ======================== -->

                    <v-menu
                        v-model="absentDateChoose"
                        :close-on-content-click="false"
                        max-width="290"
                    >
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                            :value="computedDateFormattedMomentjs"
                            v-bind:label="$t('absent.frmAbsentDate')"
                            color="cyan darken-1"
                            class="khmer-font"
                            prepend-inner-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                            outlined
                            :error-messages="errorsMessage.date"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="form.date"
                        color="cyan darken-1"
                        @input="absentDateChoose = false"
                    ></v-date-picker>
                    </v-menu>

                    <v-textarea
                        v-model="form.description"
                        outlined
                        color="cyan darken-1"
                        v-bind:label="$t('absent.frmDescription')"
                        class="khmer-font"
                        rows="3"
                    ></v-textarea>
                </v-card-text>

                <v-card-actions class="card-action">
                    <v-spacer></v-spacer>
                    <v-btn small color="grey lighten-2" class="khmer-font" depressed @click="closeDialog">
                        {{ $t('absent.frmCancel') }}
                    </v-btn>
                    <v-btn
                        class="khawin-background-color khmer-font"
                        small
                        depressed
                        type="submit"
                        dark
                        :loading="btnSaveLoading"
                    >
                        {{ $t('absent.frmSave') }}
                    </v-btn>
                </v-card-actions>
            </form>
        </v-card>
    </v-dialog>

    <!-- ---------------Snacbar--------------- -->
    <v-snackbar v-model="snackbar" color="indigo lighten-1" dark>
      {{ alertSnackbarMsg }}
      <template v-slot:action="{ attrs }">
        <v-btn dark text v-bind="attrs" @click="snackbar = false" small>
          {{ $t('absent.msgClose') }}
        </v-btn>
      </template>
    </v-snackbar>

    <!-- ----------dialogDelete------------ -->
    <v-dialog v-model="dialogDelete" max-width="330px">
      <v-card>
        <div class="text-center">
            <v-sheet class="px-7 pt-7 pb-4 mx-auto text-center d-inline-block">
                <v-icon class="text-center pb-3" x-large color="red lighten-2">mdi-alert</v-icon>
                <h3 class="grey--text text--darken-2 khmer-font">{{ $t('absent.deleteMsgQ') }}</h3>
                <p class="khmer-font">{{ $t('absent.deleteMsgAlert') }}
                    <span class="font-weight-medium">{{ employeeName }}</span>
                    {{ $t('absent.deleteMsgAlertAbsen') }}
                </p>
                <v-btn
                    :disabled="btnLoading"
                    class="ma-1 khmer-font"
                    depressed
                    small
                    @click="dialogDelete = false"
                >
                    {{ $t('absent.btnCancel') }}
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
                    {{ $t('absent.btnDelete') }}
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
      absentDateChoose: false,
      editMode: false,
      searchAbsent: "",
      snackbar: false,
      editMode: false,
      tableLoading: true,
      absentData: [],
      employeeData: [],
      absentCount: "",
      absentForm: false,
      form: new Form({
        id: "",
        employee_id: "",
        absent: "",
        absent_time: "",
        day: "",
        date: "",
        description: "",
      }),
      btnSaveLoading: false,
      btnLoading: false,
      image_validation: "",
      alertSnackbarMsg: "",
      errorsMessage: "",
      dialogDelete: false,
      employeeName: "",
    };
  },
    computed: {
        userToken() {
            return store.state.user;
        },

        formTitle() {
            return this.editMode === false ? "Add Absent" : "Edit Absent";
        },

        computedDateFormattedMomentjs() {
            return this.form.date
                ? moment(this.form.date).format("dddd, DD/ MM/ YYYY")
                : "";
        },

        headers(){
            return[
                {
                    text: "#",
                    align: "start",
                    value: "no",
                },
                { text: this.$t('absent.tbEmp'), value: "employee.name" },
                { text: this.$t('absent.tbAbsent'), value: "absent" },
                { text: this.$t('absent.tbDay'), value: "day" },
                { text: this.$t('absent.tbDate'), value: "date" },
                { text: this.$t('absent.tbEmp'), value: "year_month" },
                { text: this.$t('absent.tbDescription'), value: "description" },
                { text: this.$t('absent.tbEdit_Delete'), sortable: false, align: "center", value: "actions" },
            ]
        }
    },

    mounted() {
        if(this.userToken.token){
            this.ReadAbsent();
            this.ReadEmployee();
            this.activateMultipleDraggableDialogs();
        }
    },

  methods: {
    ReadAbsent() {
      axios
        .get(this.$globalConfig.ipHost + "api/read-absent", {
          headers: {
            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
          },
        })
        .then((response) => {
            this.absentData = response.data;
            this.absentCount = response.data.length;
            this.tableLoading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    ReadEmployee() {
      axios
        .get(this.$globalConfig.ipHost + "api/active-employee", {
          headers: {
            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
          },
        })
        .then((response) => {
          this.employeeData = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    formatDate(value) {
      return moment(value).format("DD/MM/YYYY");
    },

    formatDateGroup(value) {
      return moment(value).format("MMMM, YYYY");
    },

    getColor(day) {
        if (day == "Monday") return "orange darken-2";
        else if (day == "Tuesday") return "purple";
        else if (day == "Wednesday") return "light-green";
        else if (day == "Thursday") return "green";
        else if (day == "Friday") return "blue";
        else if (day == "Saturday") return "pink darken-4";
        else if (day == "Sunday") return "red";
    },

    openDialog() {
      this.absentForm = true;
    },

    closeDialog() {
      this.editMode = false;
      this.absentForm = false;
      this.form.employee_id = "";
      this.form.date = "";
      this.form.description = "";
      this.tableLoading = false;
      this.errorsMessage = "";
      this.form.absent = "";
      this.form.absent_time = "";
      this.btnSaveLoading = false;
    },

    createAbsent() {
      this.btnSaveLoading = true;
      this.tableLoading = true;
      this.form
        .post("api/create-absent", {
          headers: {
            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
          },
        })
        .then((response) => {
            this.ReadAbsent();
            this.closeDialog();
            // this.alertSnackbarMsg = response.data.message;
            this.alertSnackbarMsg = this.$t('absent.savedMsg');
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

    editAbsent(absent) {
      this.editMode = true;
      this.form.id = absent.id;
      this.form.employee_id = absent.employee.id;
      this.form.day = absent.day;
      this.form.date = absent.date;
      this.form.description = absent.description;
      this.form.absent = absent.absent;
      this.form.absent_time = absent.absent_time;
      this.absentForm = true;
    },

    async updateAbsent() {
      this.btnSaveLoading = true;
      this.tableLoading = true;
      await new Promise((resolve) => setTimeout(resolve, 1000));
      this.form
        .post("/api/update-absent/" + this.form.id, {
          headers: {
            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
          },
        })
        .then((response) => {
          this.ReadAbsent();
          this.closeDialog();
            //   this.alertSnackbarMsg = response.data.message;
            this.alertSnackbarMsg = this.$t('absent.updateMsg');
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

    deleteAbsent(absent, name) {
      this.form.id = absent;
      this.employeeName = name;
      this.dialogDelete = true;
    },

    async submitDelete() {
      this.btnLoading = true;
      this.tableLoading = true;
      await new Promise((resolve) => setTimeout(resolve, 1000));
      axios
        .delete("/api/delete-absent/" + this.form.id, {
          headers: {
            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
          },
        })
        .then((response) => {
          this.ReadAbsent();
          this.dialogDelete = false;
            //   this.alertSnackbarMsg = response.data.message;
            this.alertSnackbarMsg = this.$t('absent.deleteMsg');
          this.snackbar = true;
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
