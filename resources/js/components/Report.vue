<template>
  <div id="report">
    <h3 class="grey--text text--darken-2">
      <v-icon class="mb-1" color="grey darken-2">mdi-chart-bar</v-icon>
      <span class="text-decoration-underline font-weight-medium">{{ $t('report.title') }} {{ countReport }}</span>

      <v-btn
        v-if="reportData.length > 0"
        small
        color="pink accent-4"
        class="ma-2"
        dark
        @click="generatePDF"
        text
        fab
      >
        <v-icon large>mdi-file-pdf-box</v-icon>
      </v-btn>
    </h3>

    <v-row class="flex-row-reverse">
      <v-col cols="12" sm="12" md="4">
        <v-card class="pa-5">
          <v-alert
            v-model="alert"
            class="alert-report-message font-weight-bold"
            text
            prominent
            type="error"
            icon="mdi-cloud-alert"
          >
            {{ $t('report.smgAlert') }}
          </v-alert>

          <form @submit.prevent="getReport()">
            <v-menu
                v-model="menu2"
                :close-on-content-click="false"
                :nudge-right="40"
                :nudge-top="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    :value="dateRangeText"
                    v-bind:label="$t('report.txtDate')"
                    prepend-inner-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    outlined
                    clearable
                    @click:clear="clearDate()"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="form.dates"
                range
                @input="menu2 = true"
              ></v-date-picker>
            </v-menu>

            <v-autocomplete
                v-model="form.employee_id"
                :items="employeeData"
                :item-text="(item) => item.name"
                item-value="id"
                clearable
                v-bind:label="$t('report.txtSelectEmp')"
                prepend-inner-icon="mdi-account-tie"
                outlined
                multiple
                @click:clear="clearEmployee()"
            >
              <template v-slot:selection="data">
                <v-chip
                  v-bind="data.attrs"
                  :input-value="data.selected"
                  class="mb-1"
                  close
                  @click="data.select"
                  @click:close="remove(data.item)"
                >
                  <v-avatar left>
                    <v-avatar
                      class="white--text"
                      v-if="data.item.image == 'default.png'"
                      :color="'#' + data.item.profile_color"
                    >
                      <h6>
                        {{
                          data.item.name
                            .split(" ")
                            .map((x) => x[0].toUpperCase())
                            .join("")
                        }}
                      </h6>
                    </v-avatar>
                    <v-img
                      v-else
                      :src="'/employees/' + data.item.image"
                      sizes="40"
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
                    size="40"
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
                    sizes="40"
                  ></v-img>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title>
                    {{ data.item.name }} -
                    <v-chip small label> {{ data.item.position.title }} </v-chip>
                  </v-list-item-title>
                </v-list-item-content>
              </template>
            </v-autocomplete>

            <v-btn
                class="khawin-background-color"
                small
                dark
                type="submit"
                :loading="btnSaveLoading"
                block
            >
              <v-icon left> mdi-chart-bar </v-icon>
              {{ $t('report.btnReport') }}
            </v-btn>
          </form>
        </v-card>
      </v-col>

      <!-- ============Table============== -->
      <v-col cols="12" sm="12" md="8" class="justify-center">
        <v-card class="pa-2">
          <v-data-table
            :headers="headers"
            :items="reportData"
            :expanded.sync="expanded"
            item-key="name"
            show-expand
            class="data-table"
            :footer-props="{
                'items-per-page-text':$t('report.tbPagination')
            }"
          >
            <template v-slot:[`item.absent_count`]="item">
              <v-chip
                color="blue-grey"
                dark
                small
                class="absent_count_chip white--text font-weight-medium"
              >
                {{ item.item.absent_count }}
              </v-chip>
            </template>

            <template v-slot:[`item.name`]="item">
              <v-avatar
                size="32"
                class="ma-1 white--text"
                left
                v-if="item.item.pic == 'default.png'"
                :color="'#' + item.item.profile_color"
              >
                {{
                  item.item.name
                    .split(" ")
                    .map((x) => x[0].toUpperCase())
                    .join("")
                }}
              </v-avatar>

              <v-avatar size="32" class="ma-1" left v-else>
                <v-img :src="'/employees/' + item.item.pic" />
              </v-avatar>

              <span>{{ item.item.name }}</span>
            </template>

            <template v-slot:[`item.absent_total`]="item">
              <v-chip
                class="
                    absent_total-chip
                    pa-2
                    total-day
                    font-weight-bold
                    orange--text
                    text--darken-3
                "
                small
                outlined
              >
                {{ item.item.absent_total }} {{ $t('report.day') }}
              </v-chip>
            </template>

            <template v-slot:[`item.position`]="item">
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
                    {{ item.item.position }}
                </v-chip>
            </template>

            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-4 simple-table-report">
                <v-simple-table dense>
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>{{ $t('report.tbAbsent') }}</th>
                        <th>{{ $t('report.tbDay') }}</th>
                        <th>{{ $t('report.tbDate') }}</th>
                        <th>{{ $t('report.tbDescription') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(data, index) in item.data" :key="index">
                        <td>
                          <h5 class="grey--text text--darken-2">
                            {{ index + 1 }}
                          </h5>
                        </td>
                        <td>
                          <span
                            v-if="data.absent == 'fullday'"
                            class="deep-orange--text"
                            >{{ $t('report.fullDay') }}</span
                          >
                          <span v-else class="blue-grey--text">{{ $t('report.halfDay') }}</span>
                          <span v-if="data.absent_time">
                            <v-chip
                              v-if="data.absent_time == 'morning'"
                              class="absent-day-chip indigo--text"
                              small
                              label
                              outlined
                              color="indigo"
                            >
                             {{ $t('report.morning') }}
                            </v-chip>

                            <v-chip
                              v-if="data.absent_time == 'afternoon'"
                              class="absent-day-chip orange--text"
                              label
                              small
                              outlined
                              color="orange darken-3"
                            >
                              {{ $t('report.afternoon') }}
                            </v-chip>
                          </span>
                        </td>
                        <td class="text-lowercase">
                            <v-chip
                              color="grey lighten-4"
                              class="pa-1 date-chip font-weight-medium"
                              small
                              label
                            >
                              <span v-if="data.day == 'Monday'">{{ $t('report.monday') }}</span>
                              <span v-if="data.day == 'Tuesday'">{{ $t('report.tuesday') }}</span>
                              <span v-if="data.day == 'Wednesday'">{{ $t('report.wednesday') }}</span>
                              <span v-if="data.day == 'Thursday'">{{ $t('report.thursday') }}</span>
                              <span v-if="data.day == 'Friday'">{{ $t('report.friday') }}</span>
                              <span v-if="data.day == 'Saturday'">{{ $t('report.saturday') }}</span>
                              <span v-if="data.day == 'Sunday'">{{ $t('report.sunday') }}</span>
                            </v-chip>
                        </td>
                        <td>
                          <v-chip
                            color="grey lighten-4"
                            class="pa-1 date-chip pink--text font-weight-bold"
                            small
                            label
                          >
                            <span v-if="data.date.split('-')[1] == 1">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.january') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 2">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.february') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 3">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.march') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 4">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.april') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 5">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.may') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 6">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.june') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 7">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.july') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 8">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.august') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 9">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.september') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 10">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.october') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 11">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.november') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                            <span v-if="data.date.split('-')[1] == 12">
                                {{ formatDate(data.date).split('-')[0] }}
                                -{{ $t('report.december') }}
                                -{{ formatDate(data.date).split('-')[2] }}
                            </span>
                          </v-chip>
                        </td>
                        <td>{{ data.description }}</td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </td>
            </template>

            <template slot="no-data">
                {{ $t('report.notReport') }}
            </template>

          </v-data-table>

          <!-- <v-data-table
            :headers="headers"
            :items="reportData"
            :loading="tableLoading"
            loading-text="Loading Report data"
            group-by="id"
            dense
          >
            <template
              v-slot:[`group.header`]="{
                group,
                headers,
                toggle,
                isOpen,
                items,
              }"
            >
              <td :colspan="headers.length" class="group-header">
                <v-btn
                  @click="toggle"
                  small
                  icon
                  :ref="group"
                  :data-open="isOpen"
                >
                  <v-icon v-if="isOpen">mdi-chevron-up</v-icon>
                  <v-icon v-else>mdi-chevron-down</v-icon>
                </v-btn>

                <v-chip class="ma-1 font-weight-medium" small>
                  <v-avatar
                    left
                    v-if="items[0].pic == 'default.png'"
                    class="white--text"
                    :color="'#' + items[0].profile_color"
                  >
                    {{
                      items[0].name
                        .split(" ")
                        .map((x) => x[0].toUpperCase())
                        .join("")
                    }}
                  </v-avatar>
                  <v-avatar left v-else>
                    <v-img :src="'/employees/' + items[0].pic" />
                  </v-avatar>
                  {{ items[0].name }} :
                  <span class="red--text font-weight-bold ml-1">{{
                    items[0].absent_total
                  }}</span>
                </v-chip>
              </td>
            </template>

            <template v-slot:[`item`]="item">
              <v-simple-table dense id="report-simple-table">
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th>absent</th>
                      <th>day</th>
                      <th>date</th>
                      <th>description</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in item.item.absents" :key="item.name">
                      <td>{{ item.absent }}</td>
                      <td>{{ item.day }}</td>
                      <td>{{ item.date }}</td>
                      <td>{{ item.description }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </template>
          </v-data-table> -->

          <!-- <v-data-table
            :headers="headers"
            :items="reportData"
            :loading="tableLoading"
            loading-text="Loading Report data"
            group-by="employee.id"
            dense
          >
            <template
              v-slot:[`group.header`]="{
                group,
                headers,
                toggle,
                isOpen,
                items,
              }"
            >
              <td :colspan="headers.length" class="group-header">
                <v-btn
                  @click="toggle"
                  small
                  icon
                  :ref="group"
                  :data-open="isOpen"
                >
                  <v-icon v-if="isOpen">mdi-chevron-up</v-icon>
                  <v-icon v-else>mdi-chevron-down</v-icon>
                </v-btn>

                <v-chip class="ma-1 font-weight-medium" small>
                  <v-avatar
                    left
                    v-if="items[0].employee.pic == 'default.png'"
                    class="white--text"
                    :color="'#' + items[0].employee.profile_color"
                  >
                    {{
                      items[0].employee.name
                        .split(" ")
                        .map((x) => x[0].toUpperCase())
                        .join("")
                    }}
                  </v-avatar>
                  <v-avatar left v-else>
                    <v-img :src="'/employees/' + items[0].employee.pic" />
                  </v-avatar>
                  {{ items[0].employee.name }} :
                  <span class="red--text font-weight-bold ml-1">{{
                    items[0].employee.absent_total
                  }}</span>
                </v-chip>
              </td>
            </template>

            <template v-slot:[`item.employee.absents`]="item">
              <v-simple-table>
                <div v-for="data in item" :key="data.id">
                  <div v-for="absent in data" :key="absent.id">
                    <tbody>
                      <tr
                        v-for="(absentdata, index) in absent.absents"
                        :key="absentdata.id"
                      >
                        <td>
                          <v-chip x-small>{{ index + 1 }}</v-chip>
                        </td>
                        <td>{{ absentdata.date }}</td>
                      </tr>
                    </tbody>
                  </div>
                </div>
              </v-simple-table>
            </template>
          </v-data-table> -->

          <!-- ------------old-table------------ -->
          <!-- <v-data-table
            :headers="headers"
            :items="reportData"
            :loading="tableLoading"
            loading-text="Loading Report data"
            dense
            group-by="employee.id"
          >
            <template
              v-slot:[`group.header`]="{
                group,
                headers,
                toggle,
                isOpen,
                items,
              }"
            >
              <td :colspan="headers.length" class="group-header">
                <v-btn
                  @click="toggle"
                  small
                  icon
                  :ref="group"
                  :data-open="isOpen"
                >
                  <v-icon v-if="isOpen">mdi-chevron-up</v-icon>
                  <v-icon v-else>mdi-chevron-down</v-icon>
                </v-btn>

                <v-chip class="ma-1 font-weight-medium" small>
                  <v-avatar
                    left
                    v-if="items[0].employee.pic == 'default.png'"
                    class="white--text"
                    :color="'#' + items[0].employee.profile_color"
                  >
                    {{
                      items[0].employee.name
                        .split(" ")
                        .map((x) => x[0].toUpperCase())
                        .join("")
                    }}
                  </v-avatar>
                  <v-avatar left v-else>
                    <v-img :src="'/employees/' + items[0].employee.pic" />
                  </v-avatar>
                  {{ items[0].employee.name }} :
                  <span class="red--text font-weight-bold ml-1">{{
                    items.length
                  }}</span>
                </v-chip>
              </td>
            </template>

            <template v-slot:[`item.id`]="item">
              <v-chip
                x-small
                color="grey darken-1"
                class="count-absent-chip"
                dark
              >
                {{ item.index + 1 }}
              </v-chip>
            </template>

            <template v-slot:[`item.absent`]="{ item }">
              <span
                v-if="item.absent == 'fullday'"
                class="orange--text text--darken-3"
              >
                full day
              </span>
              <span v-if="item.absent == 'halfday'" class="blue-grey--text">
                half day
                <span v-if="item.absent_time">
                  <v-chip
                    v-if="item.absent_time == 'morning'"
                    label
                    class="text-lowercase report-chip"
                    outlined
                    color="indigo"
                  >
                    {{ item.absent_time }}
                  </v-chip>

                  <v-chip
                    v-if="item.absent_time == 'afternoon'"
                    label
                    class="text-lowercase report-chip"
                    outlined
                    color="orange"
                  >
                    {{ item.absent_time }}
                  </v-chip>
                </span>
              </span>
            </template>

            <template v-slot:[`item.day`]="{ item }">
              <span class="text-lowercase"> {{ item.day }}</span>
            </template>

            <template v-slot:[`item.date`]="{ item }">
              <v-chip text-color="blue-grey darken-2" label color="white">
                <v-avatar left class="mr-0">
                  <v-icon small>mdi-calendar-month</v-icon>
                </v-avatar>
                {{ formatDate(item.date) }}
              </v-chip>
            </template>
          </v-data-table> -->

          <!-- ==============PDF==================== -->

          <vue-html2pdf
            :show-layout="false"
            :float-layout="true"
            :enable-download="false"
            :preview-modal="true"
            :paginate-elements-by-height="1400"
            filename="flavourmap.pdf"
            :pdf-quality="9"
            :manual-pagination="false"
            pdf-format="a4"
            pdf-orientation="portrait"
            pdf-content-width="800px"
            ref="html2Pdf"
          >
            <section slot="pdf-content">
              <!-- PDF Content Here -->
              <section class="pdf-item" id="pdf-content">
                <h1 class="title-hearder khmer-font">
                  <v-icon>mdi-file-document-outline</v-icon> {{ $t('report.pdfTitle') }}
                </h1>
                <p class="report-date" v-if="form.dates[0]">
                  Date : {{ formatDate(form.dates[0]) }}
                  <span v-if="form.dates[1]">
                    To Date : {{ formatDate(form.dates[1]) }}</span
                  >
                </p>

                <div class="report-date-time">
                  <p>
                    {{ $t('report.pdfReportBy') }}:
                    {{ user.data.name }}
                  </p>
                  <p>
                    {{ $t('report.pdfDateReport') }}:
                    <v-icon small>mdi-calendar-month</v-icon>
                    {{
                      formatDate(
                        new Date().toJSON().slice(0, 10).replace(/-/g, "-")
                      )
                    }}
                  </p>
                </div>

                <div id="pdf-table">
                  <v-data-table
                    :headers="headersPdf"
                    :items="reportData"
                    :loading="tableLoading"
                    loading-text="Loading Report data"
                    dense
                    :hide-default-footer="true"
                    group-by="id"
                  >
                    <template v-slot:[`group.header`]="{ headers, items }">
                      <td :colspan="headers.length" class="group-header">
                        <v-chip small label>
                          <h4 class="pdf-employee-name">{{ items[0].name }}</h4>
                          <v-icon x-small>mdi-circle-small</v-icon>
                          <span class="count-absent-chip-report">{{
                            items[0].absent_total
                          }}</span
                          >{{ $t('report.day') }}</v-chip
                        >
                      </td>
                    </template>

                    <template v-slot:[`item.name`]="{ item }">
                      <v-simple-table dense>
                        <template v-slot:default>
                          <tbody>
                            <tr v-for="(data, index) in item.data" :key="index">
                              <td>
                                <h5>
                                  {{ index + 1 }}
                                </h5>
                              </td>
                              <td>
                                <span v-if="data.absent == 'fullday'"
                                  >{{ $t('report.fullDay') }}</span
                                >
                                <span v-else>{{ $t('report.halfDay') }}</span>
                                <span v-if="data.absent_time">
                                  <v-chip
                                    v-if="data.absent_time == 'morning'"
                                    class="absent-day-chip indigo--text"
                                    small
                                    label
                                    outlined
                                  >
                                    {{ $t('report.morning') }}
                                  </v-chip>

                                  <v-chip
                                    v-if="data.absent_time == 'afternoon'"
                                    label
                                    small
                                    outlined
                                  >
                                    {{ $t('report.afternoon') }}
                                  </v-chip>
                                </span>
                              </td>

                                <td class="pdf-report-day">
                                    <span v-if="data.day == 'Monday'">{{ $t('report.monday') }}</span>
                                    <span v-if="data.day == 'Tuesday'">{{ $t('report.tuesday') }}</span>
                                    <span v-if="data.day == 'Wednesday'">{{ $t('report.wednesday') }}</span>
                                    <span v-if="data.day == 'Thursday'">{{ $t('report.thursday') }}</span>
                                    <span v-if="data.day == 'Friday'">{{ $t('report.friday') }}</span>
                                    <span v-if="data.day == 'Saturday'">{{ $t('report.saturday') }}</span>
                                    <span v-if="data.day == 'Sunday'">{{ $t('report.sunday') }}</span>
                                </td>
                              <td>
                                <span v-if="data.date.split('-')[1] == 1">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.january') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 2">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.february') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 3">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.march') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 4">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.april') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 5">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.may') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 6">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.june') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 7">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.july') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 8">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.august') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 9">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.september') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 10">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.october') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 11">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.november') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                                <span v-if="data.date.split('-')[1] == 12">
                                    {{ formatDate(data.date).split('-')[0] }}
                                    -{{ $t('report.december') }}
                                    -{{ formatDate(data.date).split('-')[2] }}
                                </span>
                              </td>
                              <td>{{ data.description }}</td>
                            </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                    </template>
                  </v-data-table>
                  <div class="border-bottom"></div>
                </div>
              </section>
            </section>
          </vue-html2pdf>
          <!-- --------- -->
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import moment from "moment";
import jsPDF from "jspdf";
import "jspdf-autotable";
import store from "../store";

export default {
  data() {
    return {
      tableLoading: false,
      expanded: [],
      menu2: false,
      alert: false,
      btnPDF: false,
      alertMessageText: "",
      btnSaveLoading: false,
        // =====================================
        //   headers: [
        //     { text: "Employee", value: "name" },
        //     { text: "Absent Count", value: "absent_count", align: "left"},
        //     { text: "Total Absent", value: "absent_total" },
        //     { text: "Position", value: "position" },
        //   ],

    //   headersPdf: [
    //     {
    //       align: "start",
    //       value: "id",
    //     },
    //     { text: "Absence List", value: "name" },
    //   ],
      reportData: [],
      employeeData: [],
      countReport: "",
      form: new Form({
        employee_id: [],
        dates: [],
      }),

      check: {
        weekly: false,
        monthly: false,
        yearly: false,
      },
      // ================================
      heading: "Absence Report",
    };
  },
  computed: {
    dateRangeText() {
      return this.form.dates.join(" ~ ");
    },
    computedDateFormattedMomentjs() {
      return this.form.dates
        ? moment(this.form.dates).format("DD/MM/YYYY")
        : "";
    },

    user(){
        return store.state.user;
    },

    headers(){
        return[
            { text: this.$t('report.tbEmployee'), value: "name" },
            { text: this.$t('report.tbAbsentCount'), value: "absent_count", align: "center"},
            { text: this.$t('report.tbTotalAbsent'), value: "absent_total" },
            { text: this.$t('report.tbPosition'), value: "position" },
        ]
    },

    headersPdf(){
        return[
            { align: "start", value: "id"},
            { text: this.$t('report.pdfHeaderTb'), value: "name" },
        ]
    }
  },
  mounted() {
    this.ReadEmployee();
  },

  methods: {
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

    remove(item) {
      const index = this.form.employee_id.indexOf(item.id);
      if (index >= 0) this.form.employee_id.splice(index, 1);
    },

    getReport() {
      this.btnSaveLoading = true;
      this.tableLoading = true;

      if (this.form.employee_id.length == 0 && this.form.dates.length == 0) {
        this.alertMessageText = "Please choose any Date or Employee.";
        this.alert = true;
        this.btnSaveLoading = false;
        this.tableLoading = false;
      } else {
        this.form
          .post("/api/read-report/", {
            headers: {
              Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
            },
          })
          .then((response) => {
            if (response.data.length > 0) {
              this.reportData = response.data;
              this.countReport = response.data.length;
              this.btnPDF = true;
              this.alert = false;
            }
            this.btnSaveLoading = false;
            this.tableLoading = false;
          })
          .catch((errors) => {
            console.log(errors);
          });
      }
    },

    formatDate(value) {
      return moment(value).format("DD-MM-YYYY");
    },

    clearDate() {
      this.form.dates = [];
      this.reportData = [];
      this.countReport = "";
    },
    clearEmployee() {
      this.form.employee_id = [];
      this.reportData = [];
      this.countReport = "";
    },

    generatePDF() {
      this.$refs.html2Pdf.generatePdf();
    },

    // ===================
    // generatePDF() {
    //   let bodyData = [];
    //   this.reportData.forEach((value, index) => {
    //     bodyData.push(value);

    //     value.absents.forEach((absents, index) => {
    //       bodyData.push(absents.absent);
    //     });
    //   });

    //   console.log(bodyData);

    //   const columns = [
    //     { title: "Employee", dataKey: "name" },
    //     { title: "Absent Count", dataKey: "absent_count" },
    //     { title: "Absent Total", dataKey: "absent_total" },
    //     { title: "Position", dataKey: "position" },
    //   ];
    //   const doc = new jsPDF({
    //     orientation: "portrait",
    //     unit: "in",
    //     format: "letter",
    //   });
    //   doc.setFontSize(16).text(this.heading, 0.5, 1.0);
    //   doc.setLineWidth(0.01).line(0.5, 1.1, 8.0, 1.1);
    //   doc.autoTable({
    //     columns,
    //     body: bodyData,
    //     margin: { left: 0.5, top: 1.25 },
    //   });
    //   doc.output("dataurlnewwindow");
    // },
  },
};
</script>
