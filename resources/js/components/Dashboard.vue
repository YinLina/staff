<template>
  <div id="dashboard">
    <h3 class="grey--text text--darken-2 mb-2">
      <v-icon class="mb-1" color="grey darken-2">mdi-view-dashboard</v-icon>
      <span class="text-decoration-underline">{{ $t('dashboard.title') }}</span>
    </h3>

    <!-- ===========skeleton=============== -->
    <v-row v-show="skeletonLoading">
      <v-col cols="12" sm="12" md="4">
        <v-card class="dashboard-card" elevation="1">
          <v-card-title class="grey--text text--darken-2">
            <v-skeleton-loader
              type="table-heading"
              width="100%"
            ></v-skeleton-loader>
          </v-card-title>

          <v-card-text>
            <v-row>
              <v-col cols="4" class="text-center">
                <v-icon size="70" color="indigo">mdi-account-multiple</v-icon>
              </v-col>
              <v-col cols="8" class="text-right">
                <v-skeleton-loader
                  type="list-item-two-line"
                  width="50%"
                ></v-skeleton-loader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="12" md="4">
        <v-card class="dashboard-card" elevation="1" height="180">
          <v-card-title class="grey--text text--darken-2">
            <v-skeleton-loader
              type="table-heading"
              width="100%"
            ></v-skeleton-loader>
          </v-card-title>

          <v-card-text>
            <v-row>
              <v-col cols="4" class="text-center">
                <v-icon size="70" color="teal">mdi-account-tie</v-icon>
              </v-col>
              <v-col cols="8" class="text-right">
                <v-skeleton-loader
                  type="list-item-two-line"
                  width="50%"
                ></v-skeleton-loader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="12" md="4">
        <v-card class="dashboard-card" elevation="1">
          <v-card-title class="grey--text text--darken-2">
            <v-skeleton-loader
              type="table-heading"
              width="100%"
            ></v-skeleton-loader>
          </v-card-title>

          <v-card-text>
            <v-row>
              <v-col cols="4" class="text-center">
                <v-icon size="70" color="orange darken-3"
                  >mdi-account-cancel</v-icon
                >
              </v-col>
              <v-col cols="8" class="text-right">
                <v-skeleton-loader
                  type="list-item-three-line"
                  width="50%"
                ></v-skeleton-loader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <!-- ===========/skeleton=============== -->

    <!-- ================Card=============== -->
    <v-row v-show="!skeletonLoading">
      <!-- Users -->
      <v-col cols="12" sm="12" md="4">
        <v-card class="dashboard-card user" elevation="1" height="180">
          <v-card-title class="grey--text text--darken-2">
            <h4 class="indigo--text">{{$t('dashboard.validation')}}</h4>
            <v-spacer></v-spacer>
            <h2 v-if="CountLeaveRequest < 10" class="indigo--text">0{{ CountLeaveRequest }}</h2>
            <h2 v-else class="indigo--text">{{ CountLeaveRequest }}</h2>
          </v-card-title>

          <v-card-text>
            <v-row>
              <v-col cols="4" class="text-center">
                <v-icon size="70" color="indigo">mdi-account-multiple</v-icon>
              </v-col>
              <v-col cols="8" class="text-end">
                  <h4
                      class="font-weight-medium blue-grey--text text--darken-1 text-capitalize"
                      v-for="(item,index) in CountWorkFromHome"
                      :key="index"
                  >
                      <router-link link to="leaveRequest">
                        <p 
                          class="font-weight-medium text-capitalize blue-grey--text text--darken-1"
                        >
                          {{item.validationType}}
                          :<span v-if="item.total < 10">0{{item.total}}</span><span v-else>{{item.total}}</span>
                        </p>
                      </router-link>
                  </h4>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Employee -->
      <v-col cols="12" sm="12" md="4">
        <v-card class="dashboard-card employee" elevation="1" height="180">
          <v-card-title class="grey--text text--darken-2">
            <h4 class="teal--text">{{ $t('dashboard.employee') }}</h4>
            <v-spacer></v-spacer>
            <h2 class="teal--text" v-if="employee.all < 10">
              0{{ employee.all }}
            </h2>
            <h2 class="teal--text" v-else>
              {{ employee.all }}
            </h2>
          </v-card-title>

          <v-card-text>
            <v-row>
              <v-col cols="4" class="text-center">
                <v-icon size="70" color="teal">mdi-account-tie</v-icon>
              </v-col>
              <v-col cols="8" class="text-right">
                <h4
                    v-for="(item, index) in employee.positionsData" :key="index"
                    class="font-weight-medium text-capitalize blue-grey--text text--darken-1"
                >
                  {{ item.positions }} : <span v-if="item.count < 9">0{{ item.count }}</span>
                  <span v-else>{{ item.count }}</span>
                </h4>

              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Absent -->
      <!-- <v-col cols="12" sm="12" md="4">
        <v-card class="dashboard-card absent" elevation="1" height="180">
          <v-card-title class="grey--text text--darken-2">
            <h4 class="orange--text text--darken-3">{{ $t('dashboard.absent') }}</h4>
            <v-spacer></v-spacer>
            <h2 class="orange--text text--darken-3">
              <span v-if="absent.all > 10">{{ absent.all }}</span>
              <span v-else> 0{{ absent.all }}</span>
            </h2>
          </v-card-title>

          <v-card-text>
            <v-row>
              <v-col cols="4" class="text-center">
                <v-icon size="70" color="orange darken-3">mdi-account-cancel</v-icon>
              </v-col>
              <v-col cols="8" class="text-right">
                <h4 class="font-weight-medium blue-grey--text text--darken-1" v-if="absent.weekCount < 10">
                  {{ $t('dashboard.weekly') }} : 0{{ absent.weekCount }}
                </h4>
                <h4 class="font-weight-medium blue-grey--text text--darken-1" v-else>
                  {{ $t('dashboard.weekly') }} : {{ absent.weekCount }}
                </h4>

                <h4 class="font-weight-medium blue-grey--text text--darken-1" v-if="absent.monthly < 10">
                  {{ $t('dashboard.monthly') }} : 0{{ absent.monthly }}
                </h4>
                <h4 class="font-weight-medium blue-grey--text text--darken-1" v-else>
                  {{ $t('dashboard.monthly') }} : {{ absent.monthly }}
                </h4>

                <h4 class="font-weight-medium blue-grey--text text--darken-1" v-if="absent.year < 10">
                  {{ $t('dashboard.yearly') }} : 0{{ absent.year }}
                </h4>
                <h4 class="font-weight-medium blue-grey--text text--darken-1" v-else>
                  {{ $t('dashboard.yearly') }} : {{ absent.year }}
                </h4>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col> -->
      <!-- New Dashboard -->
      <v-col cols="12" sm="12" md="8">
        <v-card class="dashboard-card absent" elevation="1">
          <v-card-title class="grey--text text--darken-2">
            <h4 class="orange--text text--darken-3">{{$t('dashboard.todayAbsent')}}</h4>
            <v-spacer></v-spacer>
            <h2 class="orange--text text--darken-3">
              <span v-if="absent.all > 10">{{ table.TodayAbsentCount }}</span>
              <span v-else> 0{{ table.TodayAbsentCount }}</span>
            </h2>
          </v-card-title>

          <v-card-text>
            <v-card class="mx-auto" outlined>
              <div class="ma-1">
                <v-icon small>mdi-table-account</v-icon>
                {{$t('absent.today')}}
              </div>
              <v-data-table
                :headers="headerAbsent"
                :items="table.TodayAbsent"
                hide-default-footer
              >
                <template v-slot:[`item.no`]="{index}">
                  <span>
                    {{ index + 1 }}
                  </span>
                </template>
                <template v-slot:[`item.name`]="{item}">
                  <span>
                    {{item.name}}
                  </span>
                </template>
                <template v-slot:[`item.Absent`]="{item}">
                  <span>
                    {{item.absentTime}}
                  </span>
                </template>
                <template v-slot:[`item.ValidationType`]="{item}">
                  <span>{{item.validationType
                    }}</span>
                </template>
              </v-data-table>
            </v-card>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- ================Table================= -->
    <v-card class="mt-5">
      <v-card-title>
        <h5 class="grey--text text--darken-2 font-weight-medium">
          <v-icon>mdi-account-cancel</v-icon> {{ $t('dashboard.absenceSummary') }}
        </h5>
      </v-card-title>
      <v-card-text>
        <v-row>
          <!-- ---weekly--- -->
          <v-col cols="12" sm="12" md="4">
            <v-card class="mx-auto" outlined>
              <div class="ma-2">
                <v-icon small>mdi-table-account</v-icon> {{ $t('dashboard.weekly') }}
              </div>
              <v-data-table
                :headers="headers"
                :items="table.weeklyAbsent"
                loading-text="Loading Report data"
                dense
                :items-per-page="-1"
                :expanded.sync="expandedWeekly"
                show-expand
                hide-default-footer
                fixed-header
              >
                <template v-slot:expanded-item="{ headers, item }">
                  <td :colspan="headers.length" class="pa-1">
                    <v-simple-table dense>
                      <template v-slot:default>
                        <tbody>
                          <tr
                            v-for="(data, index) in item.absents"
                            :key="index"
                          >
                            <td>
                              <h5 class="grey--text text--darken-2">
                                {{ index + 1 }}
                              </h5>
                            </td>
                            <td>
                              <span
                                v-if="data.absent == 'fullday'"
                                class="deep-orange--text"
                                >{{ $t('dashboard.fullDay') }}</span
                              >
                              <span v-else class="blue-grey--text"
                                >{{ $t('dashboard.halfDay') }}</span
                              >
                            </td>
                            <td class="text-lowercase">
                                <span v-if="data.day == 'Monday'">{{ $t('dashboard.monday') }}</span>
                                <span v-if="data.day == 'Tuesday'">{{ $t('dashboard.tuesday') }}</span>
                                <span v-if="data.day == 'Wednesday'">{{ $t('dashboard.wednesday') }}</span>
                                <span v-if="data.day == 'Thursday'">{{ $t('dashboard.thursday') }}</span>
                                <span v-if="data.day == 'Friday'">{{ $t('dashboard.friday') }}</span>
                                <span v-if="data.day == 'Saturday'">{{ $t('dashboard.saturday') }}</span>
                                <span v-if="data.day == 'Sunday'">{{ $t('dashboard.sunday') }}</span>
                            </td>
                            <td>
                              <span>{{ formatDate(data.date) }}</span>
                            </td>

                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </td>
                </template>

                <template v-slot:[`item.name`]="item">
                  <v-avatar
                    size="23"
                    class="ma-1 white--text"
                    left
                    v-if="item.item.pic == 'default.png'"
                    :color="'#' + item.item.profile_color"
                  >
                    <small>{{
                      item.item.name
                        .split(" ")
                        .map((x) => x[0].toUpperCase())
                        .join("")
                    }}</small>
                  </v-avatar>

                  <v-avatar size="22" class="ma-1" left v-else>
                    <v-img :src="'/employees/' + item.item.pic" />
                  </v-avatar>
                  <span class="name-employee-table">{{ item.item.name }}</span>
                </template>

                <template v-slot:[`item.absent_total`]="item">
                  <v-chip
                    class="pa-2 count_chip font-weight-medium"
                    small
                    outlined
                  >
                    <span class="font-weight-bold orange--text text--darken-3"
                      >{{ item.item.absent_total }} {{ $t('dashboard.day') }}</span
                    >
                  </v-chip>
                </template>
              </v-data-table>
            </v-card>
          </v-col>

          <!-- --Monthly-- -->
          <v-col cols="12" sm="12" md="4">
            <v-card class="mx-auto" outlined>
              <div class="ma-2">
                <v-icon small>mdi-table-account</v-icon> {{ $t('dashboard.monthly') }}
              </div>
              <v-data-table
                :headers="headers"
                :items="table.monthlyAbsent"
                loading-text="Loading Report data"
                dense
                :items-per-page="-1"
                :expanded.sync="expandedMonthly"
                show-expand
                hide-default-footer
                fixed-header
              >
                <template v-slot:expanded-item="{ headers, item }">
                  <td :colspan="headers.length" class="pa-1">
                    <v-simple-table dense>
                      <template v-slot:default>
                        <tbody>
                          <tr
                            v-for="(data, index) in item.absents"
                            :key="index"
                          >
                            <td>
                              <h5 class="grey--text text--darken-2">
                                {{ index + 1 }}
                              </h5>
                            </td>
                            <td>
                              <span
                                v-if="data.absent == 'fullday'"
                                class="deep-orange--text"
                                >{{ $t('dashboard.fullDay') }}</span
                              >
                              <span v-else class="blue-grey--text"
                                >{{ $t('dashboard.halfDay') }}</span
                              >
                            </td>
                            <td class="text-lowercase">
                                <span v-if="data.day == 'Monday'">{{ $t('dashboard.monday') }}</span>
                                <span v-if="data.day == 'Tuesday'">{{ $t('dashboard.tuesday') }}</span>
                                <span v-if="data.day == 'Wednesday'">{{ $t('dashboard.wednesday') }}</span>
                                <span v-if="data.day == 'Thursday'">{{ $t('dashboard.thursday') }}</span>
                                <span v-if="data.day == 'Friday'">{{ $t('dashboard.friday') }}</span>
                                <span v-if="data.day == 'Saturday'">{{ $t('dashboard.saturday') }}</span>
                                <span v-if="data.day == 'Sunday'">{{ $t('dashboard.sunday') }}</span>
                            </td>
                            <td>
                              {{ formatDate(data.date) }}
                            </td>

                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </td>
                </template>

                <template v-slot:[`item.name`]="item">
                  <v-avatar
                    size="23"
                    class="ma-1 white--text"
                    left
                    v-if="item.item.pic == 'default.png'"
                    :color="'#' + item.item.profile_color"
                  >
                    <small>{{
                      item.item.name
                        .split(" ")
                        .map((x) => x[0].toUpperCase())
                        .join("")
                    }}</small>
                  </v-avatar>

                  <v-avatar size="22" class="ma-1" left v-else>
                    <v-img :src="'/employees/' + item.item.pic" />
                  </v-avatar>
                  <span class="name-employee-table">{{ item.item.name }}</span>
                </template>

                <template v-slot:[`item.absent_total`]="item">
                  <v-chip
                    class="pa-2 count_chip font-weight-medium"
                    small
                    outlined
                  >
                    <span>{{ item.item.absent_total }} {{ $t('dashboard.day') }}</span
                    >
                  </v-chip>
                </template>
              </v-data-table>
            </v-card>
          </v-col>

          <!-- ----Yearly------ -->
          <v-col cols="12" sm="12" md="4">
            <v-card class="mx-auto" outlined>
              <div class="ma-2">
                <v-icon left small>mdi-table-account</v-icon> {{ $t('dashboard.yearly') }}
              </div>
              <v-data-table
                :headers="headers"
                :items="table.yearlyAbsent"
                loading-text="Loading Report data"
                dense
                :items-per-page="-1"
                :expanded.sync="expandedYearly"
                show-expand
                hide-default-footer
                fixed-header
              >
                <template v-slot:expanded-item="{ headers, item }">
                  <td :colspan="headers.length" class="pa-1">
                    <v-simple-table dense>
                      <template v-slot:default>
                        <tbody>
                          <tr
                            v-for="(data, index) in item.absents"
                            :key="index"
                          >
                            <td>
                              <h5 class="grey--text text--darken-2">
                                {{ index++ }}
                              </h5>
                            </td>
                            <td>
                              <span
                                v-if="data.absent == 'fullday'"
                                class="deep-orange--text"
                                >{{ $t('dashboard.fullDay') }}</span
                              >
                              <span v-else class="blue-grey--text"
                                >{{ $t('dashboard.halfDay') }}</span
                              >
                            </td>
                            <td class="text-lowercase">
                              <span v-if="data.day == 'Monday'">{{ $t('dashboard.monday') }}</span>
                              <span v-if="data.day == 'Tuesday'">{{ $t('dashboard.tuesday') }}</span>
                              <span v-if="data.day == 'Wednesday'">{{ $t('dashboard.wednesday') }}</span>
                              <span v-if="data.day == 'Thursday'">{{ $t('dashboard.thursday') }}</span>
                              <span v-if="data.day == 'Friday'">{{ $t('dashboard.friday') }}</span>
                              <span v-if="data.day == 'Saturday'">{{ $t('dashboard.saturday') }}</span>
                              <span v-if="data.day == 'Sunday'">{{ $t('dashboard.sunday') }}</span>
                            </td>
                            <td>
                              {{ formatDate(data.date) }}
                            </td>

                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </td>
                </template>

                <template v-slot:[`item.name`]="item">
                  <v-avatar
                    size="23"
                    class="ma-1 white--text"
                    left
                    v-if="item.item.pic == 'default.png'"
                    :color="'#' + item.item.profile_color"
                  >
                    <small>{{
                      item.item.name
                        .split(" ")
                        .map((x) => x[0].toUpperCase())
                        .join("")
                    }}</small>
                  </v-avatar>

                  <v-avatar size="22" class="ma-1" left v-else>
                    <v-img :src="'/employees/' + item.item.pic" />
                  </v-avatar>
                  <span class="name-employee-table">{{ item.item.name }}</span>
                </template>

                <template v-slot:[`item.absent_total`]="item">
                  <v-chip
                    class="pa-2 count_chip font-weight-medium"
                    small
                    outlined
                  >
                    <span class="font-weight-bold orange--text text--darken-3"
                      >{{ item.item.absent_total }} {{ $t('dashboard.day') }}</span
                    >
                  </v-chip>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
  import moment from "moment";
import store from "../store";
  export default {
      data() {
          return {
          expandedWeekly: [],
          expandedMonthly: [],
          expandedYearly: [],
          skeletonLoading: false,
          weeklyAbsentData: [],
          monthlyAbsentData: [],
          absent: {
              all: "",
              weekCount: "",
              monthly: "",
              year: "",
          },
          employee: {
              all: "",
              senior: "",
              junior: "",
              positionsData: [],
          },
          user: {
              total: "",
              role: [],
          },
          table: {
              weeklyAbsent: [],
              monthlyAbsent: [],
              yearlyAbsent: [],
              TodayAbsent: [],
              TodayAbsentCount: "",
          },
            CountLeaveRequest: "",
            CountWorkFromHome: [],
          };
      },

    computed: {
        loggedIn() {
            return store.getters.loggedIn;
        },

        userToken() {
            return store.state.user;
        },
        headers(){
          return [
              { text: this.$t('dashboard.employee'), value: "name", align: "start" },
              { text: this.$t('dashboard.total'), value: "absent_total" },
          ]
        },
        headerAbsent(){
          return[
            {text: this.$t('dashboard.No'), value: 'no' },
            {text: this.$t('dashboard.employee'), value: 'name' },
            {text: this.$t('dashboard.absent'), value: 'Absent'},
            {text: this.$t('parameters.txtValidation'), value: 'ValidationType'}
          ]
        }
    },

    mounted() {
        if(this.userToken.token){
            this.getReport();
            this.$root.$emit('mainAppComponent');
        }
    },

    methods: {
        formatDate(value) {
            return moment(value).format("DD-MM-YYYY");
        },

        getColor(day) {
            if (day == "Monday") return "orange";
            else if (day == "Tuesday") return "purple";
            else if (day == "Wednesday") return "light-green";
            else if (day == "Thursday") return "green";
            else if (day == "Friday") return "blue";
            else if (day == "Saturday") return "pink";
            else if (day == "Sunday") return "red";
        },

        getReport() {
            if(this.loggedIn){
                axios
                .get("/api/dashboard-data/", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
                })
                .then((response) => {
                    // ----------absent---------
                    this.absent.all = response.data.absent.absentCount;
                    this.absent.weekCount = response.data.absent.absentWeekCount;
                    this.absent.monthly = response.data.absent.absentMonthCount;
                    this.absent.year = response.data.absent.absentYearCount;

                    // ----------employee---------
                    this.employee.all = response.data.employee.allEmployee;
                    this.employee.senior = response.data.employee.senior;
                    this.employee.junior = response.data.employee.junior;

                    this.employee.positionsData = response.data.employee.positions;

                    // ----------User-------------
                    this.user.total = response.data.user.user;
                    this.user.role = response.data.user.role;
                    this.CountLeaveRequest = response.data.leaverequest.leaverequestCount;
                    this.CountWorkFromHome = response.data.leaverequest.CountWorkFromHome;

                    // ---------Table-------------
                    this.table.weeklyAbsent = response.data.absent.weeklyAbsents;
                    this.table.monthlyAbsent = response.data.absent.monthlyAbsents;
                    this.table.yearlyAbsent = response.data.absent.yearAbsents;
                    this.table.TodayAbsent = response.data.absent.TodayAbsent;
                    this.table.TodayAbsentCount = response.data.absent.TodayAbsent.length;

                    // ------------Logout-----------                    
                    setTimeout( () => {
                        this.logout();
                    }, 30 * 60 * 1000);

                })
                .catch((errors) => {
                    console.log(errors);
                });
            }
        },

        logout() {
            this.email = "";
            this.password = "";
            this.$store.dispatch("destroyToken").then(() => {
                this.$router.push({ name: "login" });
            });
        },

      },
  };
</script>
