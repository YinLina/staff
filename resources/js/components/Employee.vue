<template>
    <div id="employee">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                    <h3 class="grey--text text--darken-2">
                        <v-icon class="mb-1" color="grey darken-2">mdi-account-tie</v-icon>
                        <span class="text-decoration-underline font-weight-medium">{{ $t('employee.list') }}</span>
                    </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="6">
                        <v-text-field
                            v-show="!cardView"
                            class="txt-search"
                            v-model="searchEmployee"
                            append-icon="mdi-magnify"
                            v-bind:label="$t('employee.search')"
                            single-line
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" sm="6" class="text-end">
                <v-btn
                    key="employeeForm"
                    small
                    class="font-weight-regular pa-2 khawin-background-color white--text"
                    v-model="tabAddEmp"
                    @click="addEmployee"
                >
                    <v-icon left>mdi-plus</v-icon>
                    {{ $t('employee.btnAddEmp')}}
                </v-btn>
            </v-col>
        </v-row>

        <!-- -------table of tab------ -->
        
        <v-tabs v-model="tab">
            <v-tabs-slider color="transparent"></v-tabs-slider>
            <v-tab class="text-capitalize" value="1" ref="tabActive">
                <v-icon class="mr-2" left>mdi-account-multiple</v-icon>
                <span class="font-weight-medium">{{ $t('employee.activeEmp') }}</span>
                <v-chip small class="ml-2 font-weight-medium blue-grey--text text--darken-2">
                    {{ employeeActiveCount }}
                </v-chip>
            </v-tab>
            <!-- <v-tab class="text-capitalize " value="2" ref="tabInactive">
                <v-icon class="mr-2" left>mdi-account-multiple-minus</v-icon>
                <span class="font-weight-medium">{{ $t('employee.inctiveEmp') }}</span>
                <v-chip small class="ml-2 font-weight-medium blue-grey--text text--darken-2">
                    {{ employeeInactiveCount }}
                </v-chip>
            </v-tab> -->
            <v-tab class="text-capitalize " :aria-selected="value" ref="tabAddEmp">
                <v-icon left>mdi-account-multiple-plus</v-icon>
                <span class="font-weight-medium">{{$t('employee.tabAddEmp')}}</span>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">

            <!---------- table active employee ---------->

            <v-tab-item  key="activeEmployee">
            <v-card class="mx-auto table-card" v-show="!cardView">
                <v-data-table
                    :headers="ActiveEmployeeHeaders"
                    :items="employeeActiveData"
                    :search="searchEmployee"
                    :loading="tableLoading"
                    loading-text="Loading users data"
                    :footer-props="{
                        'items-per-page-text':$t('employee.tablePagination')
                    }"
                >
                <template v-slot:[`item.id`]="item">
                    {{ item.index + 1 }}
                </template>

                <!-- add staff code -->
                <template v-slot:[`item.ParaData`]="{ item }">
                    <v-chip
                        label
                        dark
                        color="blue-grey darken-2"
                        class="text-capitalize pa-1 font-weight-medium"
                        outlined
                        small
                    >
                        <v-avatar left>
                            <v-icon small>mdi-barcode-scan</v-icon>
                        </v-avatar>
                        {{ item.ParameterPKID }}
                    </v-chip>
                </template>

                <!-- modify LINA 07/07/2023 #cms-74 (remove imag_profile on fullName) -->
                <template v-slot:[`item.fullName`]="{ item }">
                    <span class="
                        font-weight-bold
                        blue-grey--text
                        text--darken-3 text-capitalize
                        employee-name"
                    >
                        {{ item.fullName }}
                    </span>
                </template>

                <!-- modify LINA 07/07/2023 #cms-74 (remove icon gender)-->
                <template v-slot:[`item.gender`]="{ item }">
                    <v-chip
                    v-if="item.gender == 'male'"
                        color="transparent"
                        text-color="grey darken-2"
                        class="pl-1 py-0"
                    >
                        {{ $t('employee.tbGenderM') }}
                    </v-chip>

                    <v-chip
                        v-else
                        color="white"
                        text-color="grey darken-2"
                        class="pl-1 py-0"
                    >
                        {{ $t('employee.tbGenderF') }}
                    </v-chip>
                </template>

                <template v-slot:[`item.position.title`]="{ item }">
                    <v-chip
                        label
                        dark
                        color="blue-grey darken-2"
                        class="text-capitalize pa-1 font-weight-medium"
                        outlined
                        small
                    >
                        <v-avatar left>
                            <v-icon small>mdi-account-star</v-icon>
                        </v-avatar>
                        <!-- {{ item.position.title }} -->
                    </v-chip>
                </template>

                <template v-slot:[`item.start_date`]="{ item }">
                    <v-chip
                        class="pa-1 start-date"
                        small
                        color="teal"
                        text-color="white"
                        label
                    >
                    <v-avatar left>
                        <v-icon x-small>mdi-calendar-month</v-icon>
                    </v-avatar>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 1">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.january') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 2">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.february') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 3">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.march') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 4">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.april') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 5">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.may') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 6">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.june') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 7">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.july') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 8">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.august') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 9">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.september') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 10">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.october') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                        <span v-if="formatDate(item.start_date).split('/')[1] == 11">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.november') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                         <span v-if="formatDate(item.start_date).split('/')[1] == 12">
                            {{ formatDate(item.start_date).split('/')[0] }}
                            /{{ $t('employee.december') }}
                            /{{ formatDate(item.start_date).split('/')[2] }}
                        </span>
                    </v-chip>
                </template>

                <template v-slot:[`item.phone_number`]="{ item }">
                    <span v-for="number in item.phone_number" :key="number.id">
                    <v-chip
                        v-if="number.phone != null"
                        class="pa-1"
                        small
                        color="grey lighten-2"
                        text-color="blue-grey darken-3"
                        label
                    >
                        {{ number.phone }}
                        <v-avatar class="mr-0">
                        <v-icon x-small>mdi-phone</v-icon>
                        </v-avatar>
                    </v-chip>
                    <br />
                    </span>
                </template>

                <template v-slot:[`item.shift.title`]="{ item }">
                    <v-chip
                        label
                        dark
                        color="blue-grey darken-2"
                        class="text-capitalize pa-1 font-weight-medium"
                        outlined
                        small
                    >
                        <v-avatar left>
                            <v-icon small>mdi-account-star</v-icon>
                        </v-avatar>
                        {{ item.shift.title }}
                    </v-chip>
                </template>

                <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small ref="tabAddEmp" class="mr-2" @click="editEmployee(item)">
                        mdi-pencil</v-icon>
                    <v-icon
                        small
                        class="mr-2"
                        @click="deleteEmployee(item.id, item.fullName)"
                    >mdi-delete</v-icon>
                </template>

                <template v-slot:no-results>
                    <span>{{ $t('employee.tabelNotFound') }}</span>
                </template>
            </v-data-table>
          </v-card>

          <!-- card list-->
          <v-card color="grey lighten-4" class="pa-5 table-card" v-show="cardView">
            <v-flex class="d-flex flex-wrap justify-center">
                <v-card
                    v-for="(item, index) in employeeActiveData"
                    :key="index"
                    class="ma-3 employeeCard"
                    width="344"
                    height="210"
                    max-width="344"
                >
                    <v-row class="ma-0">
                        <v-col cols="5">
                            <v-img
                                v-if="item.image == 'default.png'"
                                height="150"
                                width="130"
                                class="mt-5 cardDefaulProfileImg"
                            >
                                <span class="cardEmpName">{{
                                    item.fullName
                                    .split(" ")
                                    .map((x) => x[0].toUpperCase())
                                    .join("")
                                }}</span>
                            </v-img>

                            <v-img
                                v-else
                                height="150"
                                width="130"
                                :src="'/employees/' + item.image"
                                class="mt-5 cardImg"
                            >
                            </v-img>

                        </v-col>
                        <v-col cols="7" class="cardDetail pa-0 grey--text text--lighten-4">
                            <div class="cardNum cyan--text text--darken-2">{{ index + 1 }}</div>
                            <div class="background-card-position"></div>
                            <h5 class="cardPosition grey--text text--lighten-4">
                                <!-- {{ item.position.title }} -->
                            </h5>
                            <h4>{{ item.name }}</h4>
                            <p><v-icon dark x-small>mdi-email</v-icon> {{ item.email }}</p>
                            <p v-if="item.gender == 'male'"><v-icon dark small>mdi-human-male</v-icon> {{ item.gender }}</p>
                            <p v-if="item.gender == 'female'"><v-icon dark small>mdi-human-female</v-icon> {{ item.gender }}</p>
                            <p><v-icon dark x-small>mdi-calendar-month</v-icon> {{ formatDate(item.start_date) }}</p>
                            <span v-for="number in item.phone_number" :key="number.id">
                                <p v-if="number.phone != null" class="cardPhoneNum"><v-icon dark x-small>mdi-phone</v-icon> {{ number.phone }}</p>
                            </span>

                            <div class="cardAction">
                                <v-icon dark small class="mr-2" @click="editEmployee(item)">mdi-pencil</v-icon>
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="deleteEmployee(item.id, item.name)"
                                >
                                    mdi-delete
                                </v-icon>
                            </div>
                        </v-col>
                    </v-row>
                </v-card>
            </v-flex>
          </v-card>
        </v-tab-item>

        <!------------ table inactive employee ----------->

        <!-- <v-tab-item  key="inactiveEmployee">
          <v-card class="mx-auto table-card">
            <v-data-table
                :headers="InactiveEmployeeHeaders"
                :items="employeeInactiveData"
                :search="searchEmployee"
                :loading="tableLoading"
                loading-text="Loading users data"
                :footer-props="{
                    'items-per-page-text':$t('employee.tablePagination')
                }"
            >
                <template v-slot:[`item.id`]="item">
                    {{ item.index + 1 }}
                </template>

                <template v-slot:[`item.fullName`]="{ item }">
                    <v-avatar
                        size="40"
                        class="ma-1 white--text"
                        left
                        v-if="item.image == 'default.png'"
                        :color="'#' + item.profile_color"
                    >
                        {{
                            item.fullName
                            .split(" ")
                            .map((x) => x[0].toUpperCase())
                            .join("")
                        }}
                    </v-avatar>

                    <v-avatar size="38" class="ma-2" left v-else>
                    <v-img :src="'/employees/' + item.image" />
                    </v-avatar>

                    <span
                        class="
                            font-weight-medium
                            blue-grey--text
                            text--darken-3 text-capitalize
                            employee-name
                        "
                    >
                        {{ item.fullName }}
                    </span>
                </template> -->

                <!-- modify LINA 10/07/2023 #cms-74 -->

                <!-- <template v-slot:[`item.gender`]="{ item }">
                    <v-chip
                        v-if="item.gender == 'male'"
                        small
                        color="white"
                        text-color="grey darken-2"
                        class="pl-1 py-0"
                    > -->
                        <!-- {{ $t('employee.tbGenderM') }}
                    </v-chip>

                    <v-chip
                        v-else
                        small
                        color="white"
                        text-color="grey darken-2"
                        class="pl-1 py-0"
                    >
                        {{ $t('employee.tbGenderF') }}
                    </v-chip>
                </template>

                <template v-slot:[`item.position.title`]="{ item }">
                     <v-chip
                        label
                        dark
                        color="blue-grey darken-2"
                        class="text-capitalize pa-1 font-weight-medium"
                        small
                    >
                        <v-avatar left>
                            <v-icon small>mdi-account-star</v-icon>
                        </v-avatar>
                        {{ item.position.title }}
                    </v-chip>
                </template>

                <template v-slot:[`item.start_date`]="{ item }">
                    <v-chip
                        class="p-1 start-date mt-1 font-weight-bold"
                        small
                        color="teal"
                        label
                        outlined
                    >
                        <v-avatar left class="mr-0">
                            <v-icon small>mdi-calendar-arrow-right</v-icon>
                        </v-avatar>
                        {{ formatDate(item.start_date) }}
                    </v-chip> -->

                    <!-- =======LEAVE==DATE====== -->
                    <!-- <br>
                    <v-chip
                        class="p-1 start-date mt-1 mb-1 font-weight-bold"
                        small
                        color="orange darken-3"
                        label
                        outlined
                    >
                        <v-avatar left class="mr-0">
                            <v-icon small>mdi-calendar-arrow-left</v-icon>
                        </v-avatar>
                        {{ formatDate(item.leave_date) }}
                    </v-chip>
                </template>

                <template v-slot:[`item.phone_number`]="{ item }">
                    <span v-for="number in item.phone_number" :key="number.id">
                        <v-chip
                            v-if="number.phone != null"
                            class="p-1 start-date"
                            small
                            color="grey lighten-2"
                            text-color="blue-grey darken-3"
                            label
                        >
                            {{ number.phone }}
                            <v-avatar class="mr-0">
                            <v-icon x-small>mdi-phone</v-icon>
                            </v-avatar>
                        </v-chip>
                        <br />
                    </span>
                </template>

                <template v-slot:[`item.shift.title`]="{ item }">
                     <v-chip
                        label
                        dark
                        color="blue-grey darken-2"
                        class="text-capitalize pa-1 font-weight-medium"
                        small
                    >
                        <v-avatar left>
                            <v-icon small>mdi-account-star</v-icon>
                        </v-avatar>
                        {{ item.shift.title }}
                    </v-chip>
                </template>

                <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="editEmployee(item)">mdi-pencil</v-icon>
                    <v-icon
                        small
                        class="mr-2"
                        @click="deleteEmployee(item.id, item.name)">
                        mdi-delete
                    </v-icon>
                </template>
                <template v-slot:no-results>
                    <span>{{ $t('employee.tabelNotFound') }}</span>
                </template>
            </v-data-table>
          </v-card>
        </v-tab-item> -->

        <!----------- table add employee ---------->
        <v-tab-item  ref="tabAddEmp" >
            <v-card color="grey lighten-4" class="mx-auto table-card">
            <v-card>
  
                <form @submit.prevent="editMode ? updateEmployee() : createEmployee()"
                    enctype="multipart/form-data">
                    <v-card-text>
                        <!-- <v-card > -->
                            <v-expansion-panels>
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        <span class="font-weight-medium">{{$t('employee.frmAddEmp')}}</span>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-row>
                                            <v-col sm="10">
                                                <v-col>
                                                    <v-row>
                                                        <div class="labelClass">
                                                            <v-text-field
                                                                v-model="form.ParameterPKID"
                                                                v-bind:label="$t('employee.tbStaffCode')"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-barcode-scan"
                                                                disabled
                                                                item-text="ParaData"
                                                            ></v-text-field>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-text-field
                                                                v-model="form.firstName"
                                                                v-bind:label="$t('employee.txtFirstName')"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-account-tie"
                                                                :error-messages="errorsMessage.firstName"
                                                            ></v-text-field>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-text-field
                                                                v-model="form.lastName"
                                                                v-bind:label="$t('employee.txtLastName')"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-account-tie"
                                                                :error-messages="errorsMessage.lastName"
                                                            ></v-text-field>
                                                        </div>
                                                    </v-row>
                                                </v-col>
                                                <v-col>
                                                    <v-row>
                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="28px"
                                                                v-model="form.otherName"
                                                                v-bind:label="$t('employee.tbOtherName')"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-account-tie"
                                                            ></v-text-field>
                                                        </div>
                                                        <div class="labelClass">
                                                            <v-radio-group
                                                                height="29px"
                                                                row
                                                                v-model="form.gender"
                                                                class="p-0 m-1 ml-5 employee-ra"
                                                                :error-messages="errorsMessage.gender"
                                                            >
                                                                <v-radio v-bind:label="$t('employee.genderM')" value="male"></v-radio>
                                                                <v-radio v-bind:label="$t('employee.genderF')" value="female"></v-radio>
                                                            </v-radio-group>
                                                        </div>
                                                        <div class="labelClass">
                                                            <v-menu
                                                                v-model="absentDateChoose"
                                                                :close-on-content-click="false"
                                                                max-width="290"
                                                            >
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-text-field
                                                                    height="28px"
                                                                    v-bind:label="$t('employee.txtStartDate')"
                                                                    color="cyan darken-1"
                                                                    class="khmer-font"
                                                                    :value="computedDateFormattedMomentjs"
                                                                    prepend-icon="mdi-calendar"
                                                                    readonly
                                                                    v-bind="attrs"
                                                                    v-on="on"
                                                                    :error-messages="errorsMessage.start_date"
                                                                ></v-text-field>
                                                            </template>
                                                            <v-date-picker
                                                                v-model="form.start_date"
                                                                @input="absentDateChoose = false"
                                                            ></v-date-picker>
                                                            </v-menu>
                                                        </div>
                                                    </v-row>
                                                </v-col>
                                                <v-col>
                                                    <v-row>
                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtNationalID')"
                                                                v-model="form.nationalID"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-angular"
                                                            ></v-text-field>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-menu
                                                                    v-model="birthDateChoose"
                                                                    :close-on-content-click="false"
                                                                    max-width="290"
                                                                >
                                                                <template v-slot:activator="{ on, attrs }">
                                                                    <v-text-field
                                                                        height="24px"
                                                                        v-bind:label="$t('employee.txtBirthDate')"
                                                                        color="cyan darken-1"
                                                                        class="khmer-font"
                                                                        :value="computedDateFormattedBirthDate"
                                                                        prepend-icon="mdi-calendar-text"
                                                                        readonly
                                                                        v-bind="attrs"
                                                                        v-on="on"
                                                                    ></v-text-field>
                                                                </template>
                                                                <v-date-picker
                                                                    v-model="form.birthDate"
                                                                    @input="birthDateChoose = false"
                                                                ></v-date-picker>
                                                            </v-menu>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtBirthPlace')"
                                                                v-model="form.birthPlace"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-home"
                                                            ></v-text-field>
                                                        </div>
                                                    </v-row>
                                                </v-col>
                                                <v-col>
                                                    <v-row>
                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtHouseNo')"
                                                                v-model="form.houseNo"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-home-map-marker"
                                                            ></v-text-field>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtStreetNo')"
                                                                v-model="form.streetNo"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-directions-fork"
                                                            ></v-text-field>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-autocomplete
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtProvince')"
                                                                v-model="form.province"
                                                                :items="provinceData"
                                                                :item-text="(item) => item.ProvinceName"
                                                                item-value="ProvinceCode"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-map-marker-radius"
                                                                color="cyan darken-1"
                                                                @change="SelectProvince()"
                                                            >
                                                            </v-autocomplete>
                                                        </div>
                                                    </v-row>
                                                </v-col>
                                                <v-col>
                                                    <v-row>
                                                        <div class="labelClass">
                                                            <v-autocomplete
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtDistrict')"
                                                                v-model="form.district"
                                                                :items="districtData"
                                                                :item-text="(item) => item.DistrictName"
                                                                item-value="DistrictCode"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-map-marker-radius"
                                                                color="cyan darken-1"
                                                                @change="SelectDistrict()"
                                                            >
                                                            </v-autocomplete>
                                                        </div>
                                                        <div class="labelClass">
                                                            <v-autocomplete
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtCommune')"
                                                                v-model="form.commune"
                                                                :items="communeData"
                                                                :item-text="(item) => item.CommuneName"
                                                                item-value="CommuneCode"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-map-marker-radius"
                                                                color="cyan darken-1"
                                                                @change="SelectCommune()"
                                                            >
                                                            </v-autocomplete>
                                                        </div>
                                                        <div class="labelClass">
                                                            <v-autocomplete
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtVillage')"
                                                                v-model="form.villageCode"
                                                                :items="villageData"
                                                                :item-text="(item) => item.VillageName"
                                                                item-value="VillageCode"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-map-marker-radius"
                                                                color="cyan darken-1"
                                                            >
                                                            </v-autocomplete>
                                                        </div>
                                                    </v-row>
                                                </v-col>
                                                <v-col>
                                                    <v-row>
                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="24px"
                                                                v-model="form.email"
                                                                v-bind:label="$t('employee.txtEmail')"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-email"
                                                                :error-messages="errorsMessage.email"
                                                            ></v-text-field>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtFacebook')"
                                                                v-model="form.facebook"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-facebook"
                                                            ></v-text-field>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="24px"
                                                                v-bind:label="$t('employee.txtTelegram')"
                                                                v-model="form.telegram"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-send"
                                                            ></v-text-field>
                                                        </div>
                                                    </v-row>
                                                </v-col>

                                                <v-col>
                                                    <v-row>
                                                        <div class="labelClass">
                                                            <v-autocomplete
                                                                height="23px"
                                                                v-model="form.role_id"
                                                                :items="roleDatas"
                                                                :item-text="(item) => item.name"
                                                                item-value="id"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-shield-account"
                                                                color="cyan darken-1"
                                                                v-bind:label="$t('employee.role')"
                                                                :error-messages="errorsMessage.role_id"
                                                            ></v-autocomplete>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-text-field
                                                                height="23px"
                                                                v-bind:label="$t('employee.txtOtherContact')"
                                                                v-model="form.otherContact"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-phone-classic"
                                                            ></v-text-field>
                                                        </div>

                                                        <div class="labelClass">
                                                            <v-row> 
                                                                <v-col sm="13">
                                                                    <span
                                                                        v-for="(number, index) in form.phone_number"
                                                                        :key="index"
                                                                        >
                                                                        <v-text-field
                                                                            height="23px"
                                                                            v-model="number.phone"
                                                                            color="cyan darken-1"
                                                                            class="khmer-font"
                                                                            v-bind:label="$t('employee.txtPhoneNum')"
                                                                            prepend-icon="mdi-phone"
                                                                            v-mask="'###-###-####'"
                                                                            :error-messages="errorsMessage.number"
                                                                        ></v-text-field>
                                                                        <small v-if="index !== 0" class="btn-remove-phoneNum">
                                                                            <v-icon @click="removePhone(index)">mdi-close</v-icon>
                                                                        </small>
                                                                    </span>
                                                                </v-col>
                                                                <v-col sm="1">
                                                                    <v-icon class="icon-add-phone" @click="addPhone"
                                                                        >mdi-plus</v-icon
                                                                    >
                                                                </v-col>
                                                            </v-row>  
                                                        </div>
                                                    </v-row>
                                                </v-col>

                                                <!-- <v-col>
                                                    <v-row>  
                                                        <div class="labelClass">
                                                            <v-autocomplete
                                                                height="23px"
                                                                v-model="form.position_id"
                                                                :items="positionData"
                                                                :item-text="(item) => item.title"
                                                                item-value="id"
                                                                class="khmer-font"
                                                                clearable
                                                                v-bind:label="$t('employee.selectPosition')"
                                                                color="cyan darken-1"
                                                                prepend-icon="mdi-account-star"
                                                                :error-messages="errorsMessage.position_id"
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
                                                        </div> -->
                                                        
                                                        <!-- <div class="labelClass">
                                                            <v-autocomplete
                                                                height="23px"
                                                                v-model="form.shift_header_PKID"
                                                                :items="shiftData"
                                                                :item-text="(item) => item.Time"
                                                                item-value="PKID"
                                                                class="khmer-font"
                                                                clearable
                                                                v-bind:label="$t('employee.selectShift')"
                                                                color="cyan darken-1"
                                                                prepend-icon="mdi-clock-outline"
                                                                :error-messages="errorsMessage.shift_header_PKID"
                                                            >
                                                                <template v-slot:selection="data">
                                                                    <v-chip
                                                                        label
                                                                        dark
                                                                        color="blue-grey darken-2"
                                                                        class="text-capitalize pa-1 font-weight-regular"
                                                                        small
                                                                    >
                                                                        {{ data.item.Time }}
                                                                    </v-chip>
                                                                </template>
                                                            </v-autocomplete>
                                                        </div> -->

                                                        <!-- <div class="labelClass">
                                                            <v-text-field
                                                                height="23px"
                                                                v-bind:label="$t('employee.txtUserID')"
                                                                v-model="form.UserID"
                                                                color="cyan darken-1"
                                                                class="khmer-font"
                                                                prepend-icon="mdi-account-circle"
                                                                :error-messages="errorsMessage.UserID"
                                                                :disabled = 'isDisabledColunm'
                                                            ></v-text-field>
                                                        </div>
                                                    </v-row>
                                                </v-col> -->
                                            </v-col>
                                            <!-- ---------------------- -->
                                            <v-col sm="2" class="text-end">
                                                <v-spacer></v-spacer>
                                                <v-list-item-avatar
                                                    tile
                                                    size="150"
                                                    color="grey lighten-2"
                                                    height="180"
                                                    class="rounded-sm khmer-font"
                                                >
                                                    <v-img
                                                        v-if="preview_profile"
                                                        :src="preview_profile"
                                                    ></v-img>

                                                    <v-img
                                                        v-if="
                                                            preview_profile_edit &&
                                                            preview_profile_edit != 'default.png'
                                                        "
                                                        :src="'/employees/' + preview_profile_edit"
                                                        class="img-fluid rounded-sm khmer-font"
                                                    ></v-img>
                                                </v-list-item-avatar>
                                                <v-file-input
                                                    prepend-icon="mdi-image-multiple"
                                                    height="18px"
                                                    v-model="form.image"
                                                    class="khmer-font"
                                                    v-bind:label="$t('employee.empImage')"
                                                    @change="onFileChange"
                                                    @click:clear="clearImage()"
                                                    :error-messages="errorsMessage.image"
                                                />
                                            </v-col>
                                        </v-row>
                                        
                                        <v-card-actions class="card-action">
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                small
                                                color="grey lighten-2"
                                                class="khmer-font"
                                                depressed
                                                @click="closeDialog"
                                            >
                                                {{ $t('employee.btnCancel') }}
                                            </v-btn>
                                            <v-btn
                                                small
                                                depressed
                                                type="submit"
                                                :loading="btnSaveLoading"
                                                class="khawin-background-color khmer-font"
                                                dark
                                            >
                                                {{ $t('employee.btnSave') }}
                                            </v-btn>
                                        </v-card-actions>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        <!-- </v-card> -->
                    </v-card-text>

                    <!-- modify Lina 11/07/2023 #cms-69 (add 6 card) -->
                    <v-card-text>
                        <!-- -------- add card of registration --------- -->
                        <v-card class="my-5">
                            <v-expansion-panels>
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        <span class="font-weight-medium">{{$t('employee.ChildList')}}</span>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-row>
                                            <v-col cols="6">
                                                <v-btn 
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="khmer-font khawin-background-color"
                                                    @click="$helper.AddDetailRow(
                                                        $refs.gridDetail, 
                                                            {
                                                                PKID: 0,
                                                                FullName: '',
                                                                Gender: '',
                                                                DateOfBirth: '',
                                                                Remark: '',
                                                            }
                                                        )"
                                                    >
                                                        <v-icon class="white--text">mdi-plus</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="red"
                                                    @click="$helper.DeleteDetailRow($refs.gridDetail)"
                                                >
                                                    <v-icon class="white--text">mdi-minus</v-icon>
                                                </v-btn>
                                            </v-col>
                                            
                                            <v-col cols="6">
                                                <v-card-actions class="card-action">
                                                    <v-spacer></v-spacer>
                                                    <v-btn
                                                        small
                                                        depressed
                                                        @click="CreateOrUpdateChildren"
                                                        class="khawin-background-color khmer-font"
                                                        dark
                                                    >
                                                        {{ $t('employee.btnSave') }}
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-col>
                                        </v-row>

                                        <template>
                                            <JqxGrid
                                                ref="gridDetail" :width="'100%'" :source="dataAdapter" :columns="columns" :autoheight="true" :editable="true" >
                                            </JqxGrid>
                                        </template>

                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-card>

                        <v-card >
                            <v-expansion-panels class="my-5">
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        <span class="font-weight-medium">{{$t('employee.EducationList')}}</span>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-row>
                                            <v-col cols="6">
                                                <v-btn 
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="khmer-font khawin-background-color"
                                                    @click="$helper.AddDetailRow(
                                                        $refs.gridEducation, 
                                                            {
                                                                PKID: 0,
                                                                SchoolName: '',
                                                                Address: '',
                                                                StartDate: '',
                                                                EndDate: '',
                                                                Skill: '',
                                                                Detail: '',
                                                            }
                                                        )"
                                                    >
                                                        <v-icon class="white--text">mdi-plus</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="red"
                                                    @click="$helper.DeleteDetailRow($refs.gridEducation)"
                                                >
                                                    <v-icon class="white--text">mdi-minus</v-icon>
                                                </v-btn>
                                            </v-col>
                                            
                                            <v-col cols="6">
                                                <v-card-actions class="card-action">
                                                    <v-spacer></v-spacer>
                                                    <v-btn
                                                        small
                                                        depressed
                                                        @click="CreateOrUpdateEducation"
                                                        class="khawin-background-color khmer-font"
                                                        dark
                                                    >
                                                        {{ $t('employee.btnSave') }}
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-col>
                                        </v-row>
                                        <template>
                                            <JqxGrid
                                                ref="gridEducation" :width="'100%'" :source="dataAdapterEducation" :columns="columnsEducation" :autoheight="true" :editable="true" >
                                            </JqxGrid>
                                        </template>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-card>

                        <v-card >
                            <v-expansion-panels class="my-5">
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        <span class="font-weight-medium">{{$t('employee.OtherSkillList')}}</span>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-row>
                                            <v-col cols="6">
                                                <v-btn 
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="khmer-font khawin-background-color"
                                                    @click="$helper.AddDetailRow(
                                                        $refs.gridOtherSkill, 
                                                            {
                                                                PKID: 0,
                                                                TrainingPlace: '',
                                                                AddressOtherSkill: '',
                                                                StartDateOtherSkill: '',
                                                                EndDateOtherSkill: '',
                                                                Skill: '',
                                                                Detail: '',
                                                            }
                                                        )"
                                                    >
                                                        <v-icon class="white--text">mdi-plus</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="red"
                                                    @click="$helper.DeleteDetailRow($refs.gridOtherSkill)"
                                                >
                                                    <v-icon class="white--text">mdi-minus</v-icon>
                                                </v-btn>
                                            </v-col>
                                            
                                            <v-col cols="6">
                                                <v-card-actions class="card-action">
                                                    <v-spacer></v-spacer>
                                                    <v-btn
                                                        small
                                                        depressed
                                                        @click="CreateOrUpdateOtherSkill"
                                                        class="khawin-background-color khmer-font"
                                                        dark
                                                    >
                                                        {{ $t('employee.btnSave') }}
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-col>
                                        </v-row>
                                        <template>
                                            <JqxGrid
                                                ref="gridOtherSkill" :width="'100%'" :source="dataAdapterOtherSkill" :columns="columnsOtherSkill" :autoheight="true" :editable="true" >
                                            </JqxGrid>
                                        </template>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-card>

                        <v-card >
                            <v-expansion-panels class="my-5">
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        <span class="font-weight-medium">{{$t('employee.ExperienceList')}}</span>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-row>
                                            <v-col cols="6">
                                                <v-btn 
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="khmer-font khawin-background-color"
                                                    @click="$helper.AddDetailRow(
                                                        $refs.gridExperience, 
                                                            {
                                                                PKID: 0,
                                                                Company: '',
                                                                AddressExperience: '',
                                                                StartDateExperience: '',
                                                                EndDateExperience: '',
                                                                Position: '',
                                                                Detail: '',
                                                            }
                                                        )"
                                                    >
                                                        <v-icon class="white--text">mdi-plus</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="red"
                                                    @click="$helper.DeleteDetailRow($refs.gridExperience)"
                                                >
                                                    <v-icon class="white--text">mdi-minus</v-icon>
                                                </v-btn>
                                            </v-col>
                                            
                                            <v-col cols="6">
                                                <v-card-actions class="card-action">
                                                    <v-spacer></v-spacer>
                                                    <v-btn
                                                        small
                                                        depressed
                                                        @click="CreateOrUpdateExperience"
                                                        class="khawin-background-color khmer-font"
                                                        dark
                                                    >
                                                        {{ $t('employee.btnSave') }}
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-col>
                                        </v-row>
                                        <template>
                                            <JqxGrid
                                                ref="gridExperience" :width="'100%'" :source="dataAdapterExperience" :columns="columnsExperience" :autoheight="true" :editable="true" >
                                            </JqxGrid>
                                        </template>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-card>

                        <v-card >
                            <v-expansion-panels class="my-5">
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        <span class="font-weight-medium">{{$t('employee.LanguageList')}}</span>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-row>
                                            <v-col cols="6">
                                                <v-btn 
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="khmer-font khawin-background-color"
                                                    @click="$helper.AddDetailRow(
                                                        $refs.gridLanguage, 
                                                            {
                                                                PKID: 0,
                                                                Language: '',
                                                                Level: '',
                                                                Detail: '',
                                                            }
                                                        )"
                                                    >
                                                        <v-icon class="white--text">mdi-plus</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="red"
                                                    @click="$helper.DeleteDetailRow($refs.gridLanguage)"
                                                >
                                                    <v-icon class="white--text">mdi-minus</v-icon>
                                                </v-btn>
                                            </v-col>
                                            
                                            <v-col cols="6">
                                                <v-card-actions class="card-action">
                                                    <v-spacer></v-spacer>
                                                    <v-btn
                                                        small
                                                        depressed
                                                        @click="CreateOrUpdateLanguage"
                                                        class="khawin-background-color khmer-font"
                                                        dark
                                                    >
                                                        {{ $t('employee.btnSave') }}
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-col>
                                        </v-row>
                                        <template>
                                            <JqxGrid
                                                ref="gridLanguage" :width="'100%'" :source="dataAdapterLanguage" :columns="columnsLanguage" :autoheight="true" :editable="true" >
                                            </JqxGrid>
                                        </template>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-card>

                        <v-card >
                            <v-expansion-panels class="my-5">
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        <span class="font-weight-medium">{{$t('employee.ReferenceList')}}</span>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-row>
                                            <v-col cols="6">
                                                <v-btn 
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="khmer-font khawin-background-color"
                                                    @click="$helper.AddDetailRow(
                                                        $refs.gridReference, 
                                                            {
                                                                PKID: 0,
                                                                FullName: '',
                                                                Position: '',
                                                                Company: '',
                                                                Email: '',
                                                                PhoneNumber: '',
                                                            }
                                                        )"
                                                    >
                                                        <v-icon class="white--text">mdi-plus</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    class="mb-1"
                                                    small
                                                    depressed
                                                    left
                                                    color="red"
                                                    @click="$helper.DeleteDetailRow($refs.gridReference)"
                                                >
                                                    <v-icon class="white--text">mdi-minus</v-icon>
                                                </v-btn>
                                            </v-col>
                                            
                                            <v-col cols="6">
                                                <v-card-actions class="card-action">
                                                    <v-spacer></v-spacer>
                                                    <v-btn
                                                        small
                                                        depressed
                                                        @click="CreateOrUpdateReference"
                                                        class="khawin-background-color khmer-font"
                                                        dark
                                                    >
                                                        {{ $t('employee.btnSave') }}
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-col>
                                        </v-row>
                                        <template>
                                            <JqxGrid
                                                ref="gridReference" :width="'100%'" :source="dataAdapterReference" :columns="columnsReference" :autoheight="true" :editable="true" >
                                            </JqxGrid>
                                        </template>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-card>
                    </v-card-text>
                    <!-- ----------------------------------- -->
                </form>  
            </v-card>
            </v-card>
        </v-tab-item>
    </v-tabs-items>

    <!-- --------employee-update-Form------ -->
    <!-- <v-dialog
        width="550"
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
                <v-icon left color="white">mdi-account-plus</v-icon>
                {{ $t('employee.frmAddEmp') }}
            </span>
            <span v-else class="white--text khmer-font">
                <v-icon left dark>mdi-account-edit</v-icon>
                {{ $t('employee.frmEditEmp') }}
            </span>
        </v-toolbar>

        <form @submit.prevent="editMode ? updateEmployee() : createEmployee()"
        enctype="multipart/form-data">
            <v-card-text>
                <v-row>
                    <v-col sm="8">
                        <v-text-field
                            v-model="form.firstName"
                            v-bind:label="$t('employee.txtFirstName')"
                            color="cyan darken-1"
                            class="khmer-font"
                            prepend-icon="mdi-account-tie"
                            :error-messages="errorsMessage.firstName"
                        ></v-text-field>

                        <v-text-field
                            v-model="form.lastName"
                            v-bind:label="$t('employee.txtLastName')"
                            color="cyan darken-1"
                            class="khmer-font"
                            prepend-icon="mdi-account-tie"
                            :error-messages="errorsMessage.lastName"
                        ></v-text-field>

                        <v-radio-group
                            v-model="form.gender"
                            row
                            :error-messages="errorsMessage.gender"
                            class="p-0 m-0 ml-5 employee-radio"
                        >
                            <v-radio v-bind:label="$t('employee.genderM')" value="male"></v-radio>
                            <v-radio v-bind:label="$t('employee.genderF')" value="female"></v-radio>
                        </v-radio-group>

                        <v-text-field
                            v-model="form.otherName"
                            v-bind:label="$t('employee.txtOtherName')"
                            color="cyan darken-1"
                            class="khmer-font"
                            prepend-icon="mdi-email"
                        ></v-text-field>

                        <v-text-field
                            v-model="form.email"
                            v-bind:label="$t('employee.txtEmail')"
                            color="cyan darken-1"
                            class="khmer-font"
                            prepend-icon="mdi-email"
                            :error-messages="errorsMessage.email"
                        ></v-text-field> -->

                        <!-- ==========POSITION============ -->
                        <!-- <v-autocomplete
                            v-model="form.position_id"
                            :items="positionData"
                            :item-text="(item) => item.title"
                            item-value="id"
                            class="khmer-font"
                            clearable
                            v-bind:label="$t('employee.selectPosition')"
                            color="cyan darken-1"
                            prepend-icon="mdi-account-star"
                            :error-messages="errorsMessage.position_id"
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
                        </v-autocomplete> -->

                    <!-- ----------- -->
                    <!-- <v-menu
                        v-model="absentDateChoose"
                        :close-on-content-click="false"
                        max-width="290"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-bind:label="$t('employee.txtStartDate')"
                                color="cyan darken-1"
                                class="khmer-font"
                                :value="computedDateFormattedMomentjs"
                                prepend-icon="mdi-calendar"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                :error-messages="errorsMessage.start_date"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="form.start_date"
                            @input="absentDateChoose = false"
                        ></v-date-picker>
                    </v-menu> -->

                    <!-- ==================================== -->
                        <!-- <v-row>
                            <v-col sm="11">
                            <span
                                v-for="(number, index) in form.phone_number"
                                :key="index"
                            >
                                <v-text-field
                                    v-model="number.phone"
                                    color="cyan darken-1"
                                    class="khmer-font"
                                    v-bind:label="$t('employee.txtPhoneNum')"
                                    prepend-icon="mdi-phone"
                                    v-mask="'###-###-####'"
                                ></v-text-field>
                                <small v-if="index !== 0" class="btn-remove-phoneNum">
                                <v-icon @click="removePhone(index)">mdi-close</v-icon>
                                </small>
                            </span>
                            </v-col>
                            <v-col sm="1">
                            <v-icon class="icon-add-phone" @click="addPhone">
                                mdi-plus</v-icon>
                            </v-col>
                        </v-row> -->

                        <!-- add shift field -->
                        <!-- <v-autocomplete
                            v-model="form.shift_header_PKID"
                            :items="shiftData"
                            :item-text="(item) => item.Time"
                            item-value="PKID"
                            class="khmer-font"
                            clearable
                            v-bind:label="$t('employee.selectShift')"
                            color="cyan darken-1"
                            prepend-icon="mdi-clock-outline"
                            :error-messages="errorsMessage.shift_header_PKID"
                        >
                            <template v-slot:selection="data">
                                <v-chip
                                    label
                                    dark
                                    color="blue-grey darken-2"
                                    class="text-capitalize pa-1 font-weight-regular"
                                    small
                                >
                                    {{ data.item.Time }}
                                </v-chip>
                            </template>
                        </v-autocomplete>

                        <v-autocomplete
                            v-model="form.role_id"
                            :items="roleDatas"
                            :item-text="(item) => item.name"
                            item-value="id"
                            class="khmer-font"
                            prepend-icon="mdi-shield-account"
                            color="cyan darken-1"
                            v-bind:label="$t('employee.role')"
                            dense
                            :error-messages="errorsMessage.role_id"
                        ></v-autocomplete> -->

                        <!-- <v-alert
                            v-if="editMode == true"
                            color="red lighten-4"
                            dense
                            class="red--text text--darken-2 khmer-font"
                        >
                           <v-checkbox
                                v-model="form.is_inactived"
                                v-bind:label="$t('employee.isInactive')"
                                color="red"
                                class="pa-0 ma-0"
                                hide-details
                            ></v-checkbox> -->
                            <!-- <v-menu
                                v-model="leaveDateChoose"
                                :close-on-content-click="false"
                                max-width="290"
                                v-if="form.is_inactived"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-bind:label="$t('employee.leavedDate')"
                                        :value="computedDateFormattedLeaveDate"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        :error-messages="errorsMessage.leave_date"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="form.leave_date"
                                    @input="leaveDateChoose = false"
                                ></v-date-picker>
                            </v-menu> -->
                        <!-- </v-alert> -->
                    <!-- </v-col>

                    <v-col sm="4">
                        <v-list-item-avatar
                            tile
                            size="150"
                            color="grey lighten-2"
                            height="180"
                            class="rounded-sm khmer-font"
                        >
                            <v-img
                                v-if="preview_profile"
                                :src="preview_profile"
                                class="img-fluid rounded-sm khmer-font"
                            ></v-img>

                            <v-img
                                v-if="
                                    preview_profile_edit &&
                                    preview_profile_edit != 'default.png'
                                "
                                :src="'/employees/' + preview_profile_edit"
                                class="img-fluid rounded-sm khmer-font"
                            ></v-img>
                        </v-list-item-avatar>
                        <v-file-input
                            show-size
                            v-model="form.image"
                            @change="onFileChange"
                            class="khmer-font"
                            prepend-icon="mdi-image-multiple"
                            v-bind:label="$t('employee.empImage')"
                            @click:clear="clearImage()"
                            :error-messages="errorsMessage.image"
                        />
                    </v-col>
                </v-row>
            </v-card-text>

            <v-card-actions class="card-action">
                <v-spacer></v-spacer>
                <v-btn
                    small
                    color="grey lighten-2"
                    class="khmer-font"
                    depressed
                    @click="closeDialog"
                >
                    {{ $t('employee.btnCancel') }}
                </v-btn>
                <v-btn
                    small
                    depressed
                    type="submit"
                    :loading="btnSaveLoading"
                    class="khawin-background-color khmer-font"
                    dark
                >
                    {{ $t('employee.btnSave') }}
                </v-btn>
            </v-card-actions>
        </form>
        </v-card>
    </v-dialog> -->
    
    <!-- ---------------Snacbar--------------- -->
    <v-snackbar v-model="snackbar" color="cyan darken-2" dark>
        {{ alertSnackbarMsg }}
        <template v-slot:action="{ attrs }">
            <v-btn dark text v-bind="attrs" @click="snackbar = false" small>
                {{ $t('employee.msgClose') }}
            </v-btn>
        </template>
    </v-snackbar>

    <!-- ----------dialogDelete------------ -->
    <v-dialog v-model="dialogDelete" max-width="350px">
    <v-card>
        <div class="text-center">
            <v-sheet class="px-3 pt-7 pb-4 mx-auto text-center d-inline-block">
            <v-icon class="text-center pb-3" x-large color="red lighten-2">
                mdi-alert
            </v-icon>
            <div class="grey--text text--darken-3 text-body-2 mb-4">
                {{ $t('employee.deleteMessage') }}
                <b class="red--text tex--lighten-2">{{ userNameDelete }}</b> ?
            </div>

            <v-btn
                :disabled="btnLoading"
                class="ma-1"
                depressed
                small
                @click="dialogDelete = false"
            >
                Cancel
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
                Delete
            </v-btn>
            </v-sheet>
        </div>
    </v-card>
    </v-dialog>

    <!-- -----------dialogWarning save list----------- -->
    <v-dialog v-model="dialogSaveList" max-width="350px">
    <v-card>
        <div class="text-center">
            <v-sheet class="px-3 pt-7 pb-4 mx-auto text-center d-inline-block">
                <v-icon class="text-center pb-3" x-large color="red lighten-2">
                    mdi-alert
                </v-icon>
                <div class="red--text tex--lighten-2">
                    {{ $t('employee.warningSaveMessage') }}
                </div>

                <v-btn
                    :disabled="btnLoading" class="ma-1" depressed small
                    @click="dialogSaveList = false"
                >
                    {{$t('absent.btnCancel')}}
                </v-btn>
            </v-sheet>
        </div>
    </v-card>
    </v-dialog>

    <!-- ----------dialogWarning------------ -->
    <v-dialog v-model="dialogWarning" max-width="330px">
        <v-card>
            <div class="text-center">
                <v-sheet class="px-7 pt-7 pb-4 mx-auto text-center d-inline-block">
                    <v-icon class="text-center pb-3" x-large color="red lighten-2">mdi-alert</v-icon>
                        <div class="grey--text text--darken-3 text-body-2 mb-4">
                            {{ $t('employee.employeeName') }}
                            <b class="red--text tex--lighten-2">
                                {{ userNameDelete }}
                            </b> 
                            {{$t('department.warningMessage')}}
                            <div class="text-left red--text tex--lighten-2">
                                <b>
                                    <span v-for="data in this.usedData">
                                        <span  v-if="data != ''"><br/>- {{ data }}</span>
                                    </span>
                                </b>   
                            </div>   
                        </div>
                        
                        <v-btn :disabled="btnLoading" class="ma-1 khmer-font" depressed small @click="dialogWarning = false">
                            {{$t('absent.btnCancel')}}
                        </v-btn>
                        </v-sheet>
                    </div>
                </v-card>
        </v-dialog>
    </div>
</template>

<!-- modify LINA 11/07/2023 #cms-69 change height in table add employee -->
<style scoped>
    .labelClass {
        height: 60px;
        width: 325px;
    }
    .font-size {
        /* font-size: 1.0em; */
        font-size: 16px;
    }

    @import "jqwidgets-scripts/jqwidgets/styles/jqx.base.css";
</style>

<script>
import moment from "moment";
import DeleteDialog from './helper_components/DeleteDialog.vue';
import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
import JqxEditor from "jqwidgets-scripts/jqwidgets-vue/vue_jqxeditor.vue";
import JqxInput from "jqwidgets-scripts/jqwidgets-vue/vue_jqxinput.vue";
import JqxDropDownButton from "jqwidgets-scripts/jqwidgets-vue/vue_jqxdropdownbutton.vue";
import store from "../store";
export default {
    components: {DeleteDialog, AlertMessageDialog, JqxGrid, JqxEditor, JqxInput, JqxDropDownButton},
    name:'Employee',
    data() {
        var self = this;
        return {
            tab: null,
            tabActive: 1,
            tabInactive: 2,
            tabAddEmp: 3,
            value:false,
            searchEmployee: "",
            snackbar: false,
            editMode: false,
            tableLoading: true,
            absentDateChoose: false,
            leaveDateChoose: false,
            birthDateChoose: false,
            cardView: false,
            employeeActiveData: [],
            employeeInactiveData: [],
            employeeActiveCount: "",
            employeeInactiveCount: "",
            positionData: [],
            provinceData: [],
            districtData: [],
            communeData: [],
            villageData: [],
            shiftData: [],
            birthDateFrom: [],
            startDateForm: [],
            endDateFrom: [],
            shiftDetailData: [],
            employeeForm: false,
            
            form: new Form({
                id: "",
                firstName: "",
                lastName: "",
                gender: "male",
                otherName: "",
                email: "",
                start_date: "",
                // position_id: "",
                // shift_header_PKID: "",
                phone_number: [{ phone: "" }],
                image: null,
                is_inactived: false,
                leave_date: "",
                role_id:"",
                ParameterPKID:"",
                //modify LINA 26/07/2023 #cms-80
                nationalID: "",
                birthDate: "",
                birthPlace: "",
                houseNo: "",
                streetNo: "",
                province: "",
                district: "",
                commune: "",
                villageCode: "",
                facebook: "",
                telegram: "",
                otherContact: "",
                // UserID: "",
            }),

            preview_profile: null,
            preview_profile_edit: null,
            btnSaveLoading: false,
            btnLoading: false,
            image_validation: "",
            alertSnackbarMsg: "",
            alertSnackbarMsgChildren: "",
            errorsMessage: "",
            dialogDelete: false,
            dialogSaveList: false,
            userNameDelete: "",
            listDataFrom: "",
            roleName:'role',
            userID:'userName',
            roleDatas: [],
            ParaData:[],
            isUpdate: false,
            isDisabledColunm: false,
            dialogWarning: false,
            dialogWarningList: false,
            usedData: [],
            dataFrom: [],

            //---------------------------------
            //Modify by LINA 04-10-2023 #cms-89
            ChildrenData: [],
            EducationData: [],
            OtherSkillData: [],
            ExperienceData: [],
            ReferenceData: [],
            LanguageData: [],
            childrenForm: new Form({
                DataForm: [],
                Employee_PKID: 0,
            }),
            educationForm: new Form({
                EducationDataForm: [],
                Employee_PKID: 0,
            }),
            otherSkillForm: new Form({
                OtherSkillDataForm: [],
                Employee_PKID: 0,
            }),
            experienceForm: new Form({
                ExperienceDataForm: [],
                Employee_PKID: 0,
            }),
            languageForm: new Form({
                LanguageDataForm: [],
                Employee_PKID: 0,
            }),
            referenceForm: new Form({
                ReferenceDataForm: [],
                Employee_PKID: 0,
            }),
            alertMessage: {
                openAlertMsg: false,
                msgDialog: "",
            },
            chDateTime:{
                ErrorMsg: '',
                openError: false,
            },
            //----- create children list -----
            dataAdapter: new jqx.dataAdapter({
                localdata: [],
                datatype: 'array',
                datafields:
                [
                    {name: 'PKID', type: 'int'},
                    {name: 'FullName', type: 'string'},
                    {name: 'Gender', type: 'string'},
                    {name: 'DateOfBirth', type: 'date'},
                    {name: 'Remark', type: 'string'},
                ]
            }),
            columns: [
                {
                    text: this.$t('employee.tbChildName'), datafield: 'FullName', width: '30%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbGender'), datafield: 'Gender', width: '20%', columntype: 'dropdownlist',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        editor[0].onselect = function(e) {
                            var selectedRowIndex = self.$refs.gridDetail.getselectedrowindex();
                            var selectedRowData = self.$refs.gridDetail.getrowdatabyid(selectedRowIndex);
                        };
                        return editor.jqxDropDownList({ 
                            autoDropDownHeight: true, source: ["M", "F"],
                        });
                    },
                },
                { 
                    text: this.$t('employee.txtBirthDate'),datafield: 'DateOfBirth', width: '25%', columntype: 'datetimeinput',  align:"center",
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                {
                    text: this.$t('employee.Remark'), datafield: 'Remark', width: '25%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
            ],
            //-------------------------------------
            dataAdapterEducation: new jqx.dataAdapter({
                localdata: [],
                datatype: 'array',
                datafields:
                [
                    {name: 'PKID', type: 'int'},
                    {name: 'SchoolName', type: 'string'},
                    {name: 'Address', type: 'string'},
                    {name: 'StartDate', type: 'date'},
                    {name: 'EndDate', type: 'date'},
                    {name: 'Skill', type: 'string'},
                    {name: 'Detail', type: 'string'},
                ]
            }),
            columnsEducation: [
                {
                    text: this.$t('employee.tbSchoolName'), datafield: 'SchoolName', width: '15%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbAddress'), datafield: 'Address', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                { 
                    text: this.$t('employee.tbStartDate'),datafield: 'StartDate', width: '15%', columntype: 'datetimeinput',  align:"center",
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                { 
                    text: this.$t('employee.tbEndDate'),datafield: 'EndDate', width: '15%', columntype: 'datetimeinput',  align:"center",
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                {
                    text: this.$t('employee.tbSkill'), datafield: 'Skill', width: '15%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbDetail'), datafield: 'Detail', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
            ],
            //----- create other skill -----
            dataAdapterOtherSkill: new jqx.dataAdapter({
                localdata: [],
                datatype: 'array',
                datafields:
                [
                    {name: 'PKID', type: 'int'},
                    {name: 'TrainingPlace', type: 'string'},
                    {name: 'AddressOtherSkill', type: 'string'},
                    {name: 'StartDateOtherSkill', type: 'date'},
                    {name: 'EndDateOtherSkill', type: 'date'},
                    {name: 'Skill', type: 'string'},
                    {name: 'Detail', type: 'string'},
                ]
            }),
            columnsOtherSkill: [
                {
                    text: this.$t('employee.tbTrainingPlace'), datafield: 'TrainingPlace', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbAddress'), datafield: 'AddressOtherSkill', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                { 
                    text: this.$t('employee.tbStartDate'),datafield: 'StartDateOtherSkill', width: '15%', columntype: 'datetimeinput',  align:"center",
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                { 
                    text: this.$t('employee.tbEndDate'),datafield: 'EndDateOtherSkill', width: '15%', columntype: 'datetimeinput',  align:"center",
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                {
                    text: this.$t('employee.tbSkill'), datafield: 'Skill', width: '15%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbDetail'), datafield: 'Detail', width: '15%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
            ],
            //----- create experience -----
            dataAdapterExperience: new jqx.dataAdapter({
                localdata: [],
                datatype: 'array',
                datafields:
                [
                    {name: 'PKID', type: 'int'},
                    {name: 'Company', type: 'string'},
                    {name: 'AddressExperience', type: 'string'},
                    {name: 'StartDateExperience', type: 'date'},
                    {name: 'EndDateExperience', type: 'date'},
                    {name: 'Position', type: 'string'},
                    {name: 'Detail', type: 'string'},
                ]
            }),
            columnsExperience: [
                {
                    text: this.$t('employee.tbCompany'), datafield: 'Company', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbAddress'), datafield: 'AddressExperience', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                { 
                    text: this.$t('employee.tbStartDate'),datafield: 'StartDateExperience', width: '15%', columntype: 'datetimeinput',  align:"center",
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                { 
                    text: this.$t('employee.tbEndDate'),datafield: 'EndDateExperience', width: '15%', columntype: 'datetimeinput',  align:"center",
                    renderer: function (row, column, value) {
                        return '<div id="jqxTimePicker" style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                    cellsrenderer: function (row, column, value) {
                        value = self.$helper.ConvertDateToStr(value);
                        return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                    },
                    createeditor: function (row, value, editor) {
                        return editor.jqxDateTimeInput({
                            formatString: "yyyy-MM-dd"
                        });
                    }
                },
                {
                    text: this.$t('employee.tbPosition'), datafield: 'Position', width: '15%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbDetail'), datafield: 'Detail', width: '15%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
            ],
            //----- create language -----
            dataAdapterLanguage: new jqx.dataAdapter({
                localdata: [],
                datatype: 'array',
                datafields:
                [
                    {name: 'PKID', type: 'int'},
                    {name: 'Language', type: 'string'},
                    {name: 'Level', type: 'string'},
                    {name: 'Detail', type: 'string'},
                ]
            }),
            columnsLanguage: [
                {
                    text: this.$t('employee.tbLanguage'), datafield: 'Language', width: '40%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbLevel'), datafield: 'Level', width: '30%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbDetail'), datafield: 'Detail', width: '30%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
            ],
            //----- create referience -----
            dataAdapterReference: new jqx.dataAdapter({
                localdata: [],
                datatype: 'array',
                datafields:
                [
                    {name: 'PKID', type: 'int'},
                    {name: 'FullName', type: 'string'},
                    {name: 'Position', type: 'string'},
                    {name: 'Company', type: 'string'},
                    {name: 'Email', type: 'string'},
                    {name: 'PhoneNumber', type: 'string'},
                ]
            }),
            columnsReference: [
                {
                    text: this.$t('employee.tbFullName'), datafield: 'FullName', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.txtPosition'), datafield: 'Position', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbCompany'), datafield: 'Company', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbEmail'), datafield: 'Email', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
                {
                    text: this.$t('employee.tbPhoneNum'), datafield: 'PhoneNumber', width: '20%', columntype: 'input',
                    renderer: function (row, column, value) {
                        return '<div style="text-align: center; margin-top: 8px;">' + row + '</div>';
                    },
                },
            ],
        }
    },
    computed: {
        userToken() {
            return store.state.user;
        },
        formTitle() {
            return this.editMode === false ? "Add Employee" : "Edit Employee";
        },
        DateFormat(){
            return this.addItemObj.DateOfBirth ? moment(this.addItemObj.DateOfBirth).format("DD/MM/YYYY") : ""; 
        },
        computedDateFormattedMomentjs() {
            return this.form.start_date
            ? moment(this.form.start_date).format("DD/MM/YYYY")
            : "";
        },

        computedDateFormattedLeaveDate() {
            return this.form.leave_date
            ? moment(this.form.leave_date).format("DD/MM/YYYY")
            : "";
        },

        computedDateFormattedBirthDate() {
            return this.form.birthDate
            ? moment(this.form.birthDate).format("DD/MM/YYYY")
            : "";
        },

        ActiveEmployeeHeaders(){
            return [
                {
                    text: "#",
                    align: "start",
                    value: "id",
                },
                //modify LINA 06/07/2023 #cms-74
                { text: this.$t('employee.tbStaffCode'), value: "StaffCodeID"},
                { text: this.$t('employee.tbName'), value: "fullName" },
                { text: this.$t('employee.tbGender'), value: "gender" },
                { text: this.$t('employee.tbEmail'), value: "email" },
                // { text: this.$t('employee.tbPosition'), value: "position" },
                { text: this.$t('employee.tbPhoneNum'), value: "phone_number" },
                // first shiftname is employeeData and second shiftname is column name
                // { text: this.$t('employee.tbShift'), value: "ShiftName.ShiftName" },
                { text: this.$t('employee.tbEditDelete'), sortable: false, align: "center", value: "actions" },
            ]
        },

        InactiveEmployeeHeaders(){
            return [
                //modify LINA 06/07/2023 #cms-74 
                { text: "#", align: "start", value: "id"},
                { text: this.$t('employee.tbStaffCode'), value: "StaffCodeID"},
                { text: this.$t('employee.tbName'), value: "fullName" },
                { text: this.$t('employee.tbGender'), value: "gender" },
                { text: this.$t('employee.tbEmail'), value: "email" },
                // { text: this.$t('employee.tbPosition'), value: "position" },
                { text: this.$t('employee.tbStartStopDate'), value: "start_date" },
                { text: this.$t('employee.tbPhoneNum'), value: "phone_number" },
                { text: this.$t('employee.tbEditDelete'), sortable: false, align: "center", value: "actions" },
            ]
        },
        headers(){
            return [
                { text: this.$t('position.positionTable'), value: 'title' },
                { text: this.$t('position.employeeCountTable'), value: 'employee_count' },
                { text: this.$t('position.edit_delete'), sortable: false, align: "center", value: "actions" },
            ]
        }
    },

    mounted() {
        if(this.userToken.token){
            this.ReadEmployeeActive();
            this.ReadEmployeeInactive();
            // this.ReadPosition();
            this.ReadProvince();
            this.ReadShift();
            this.activateMultipleDraggableDialogs();
            this.ReadRole();
            this.ReadParameter();
        }
    },
    methods: {
        ReadParameter(){
            axios
            .get(this.$globalConfig.ipHost + 'api/get-staff-code' , {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) =>{
                this.ParaData = response.data.ParameterData
            })
            .catch((error)=>{
                console.log(error)
            })
        },
        ReadRole() {
            axios
            .get(this.$globalConfig.ipHost + "api/role-data", {
            headers: {
                Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                for(var i = 0; i < response.data.length; i++) {
                    this.roleDatas.push(response.data[i].role);
                }
            })
            .catch((error) => {
                console.log(error);
            });
        },
        ReadEmployeeActive() {
            axios
                .get(this.$globalConfig.ipHost + "api/active-employee", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
                })
                .then((response) => {
                    this.employeeActiveData = response.data.data;
                    this.employeeActiveCount = response.data.data.length;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        ReadEmployeeInactive() {
            axios
                .get(this.$globalConfig.ipHost + "api/inactive-employee", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.employeeInactiveData = response.data.data;
                    this.employeeInactiveCount = response.data.data.length;
                    this.tableLoading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ReadPosition() {
        //     axios
        //         .get(this.$globalConfig.ipHost + "api/read-position", {
        //             headers: {
        //                 Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
        //             },
        //         })
        //         .then((response) => {
        //             this.positionData = response.data.data;
        //         })
        //         .catch((error) => {
        //             console.log(error);
        //         });
        // },

        ReadShift() {
            axios
                .get(this.$globalConfig.ipHost + "api/read-shift-detail", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                
                this.shiftData = response.data.shift;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        // modify Lina 01/08/2023 #cms-80
        ReadProvince() {
            axios
                .get(this.$globalConfig.ipHost + "api/read-province", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.provinceData = response.data.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        SelectProvince(){
            axios
            .get(this.$globalConfig.ipHost + 'api/get-district-by-code/' + this.form.province , {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) =>{
                this.districtData = response.data.districtListByProvinceCode;
            })
            .catch((error)=>{
                console.log(error)
            })
        },
        SelectDistrict(){
            axios
            .get(this.$globalConfig.ipHost + 'api/get-commune-by-code/' + this.form.district , {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) =>{
                this.communeData = response.data.communeListByDistrictCode;
            })
            .catch((error)=>{
                console.log(error)
            })
        },
        SelectCommune(){
            axios
            .get(this.$globalConfig.ipHost + 'api/get-village-by-code/' + this.form.commune , {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                }
            })
            .then((response) =>{
                this.villageData = response.data.villageListByCommuneCode;
            })
            .catch((error)=>{
                console.log(error)
            })
        },
        // ----------------------------------------------

        listViewChang(){
            this.cardView = this.cardView ? false : true;
        },

        formatDate(value) {
            return moment(value).format("DD/MM/YYYY");
        },

        addPhone: function () {
            this.form.phone_number.push({ phone: "" });
        },

        removePhone(index) {
            this.form.phone_number.splice(index, 1);
        },

        openDialog() {
            this.employeeForm = true;
            this.childrenForm.DataForm = [];
            this.educationForm.EducationDataForm = [];
            this.otherSkillForm.OtherSkillDataForm = [];
            this.experienceForm.ExperienceDataForm = [];
            this.languageForm.LanguageDataForm = [];
            this.referenceForm.ReferenceDataForm = [];
        },

        ReadEmployeeChildren(){
            axios
                .get(this.$globalConfig.ipHost + "api/read-children-list/", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ChildrenData = response.data.data
            })
            .catch((error) => {
                console.log(error);
            });
        },
        ReadEmployeeEducation(){
            axios
                .get(this.$globalConfig.ipHost + "api/read-education-list/", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.EducationData = response.data.data
            })
            .catch((error) => {
                console.log(error);
            });
        },
        ReadEmployeeExperience(){
            axios
                .get(this.$globalConfig.ipHost + "api/read-experience-list/", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ExperienceData = response.data.data
            })
            .catch((error) => {
                console.log(error);
            });
        },
        ReadEmployeelanguage(){
            axios
                .get(this.$globalConfig.ipHost + "api/read-language-list/", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.LanguageData = response.data.data
            })
            .catch((error) => {
                console.log(error);
            });
        },
        
        ReadEmployeeRefernece(){
            axios
                .get(this.$globalConfig.ipHost + "api/read-reference-list/", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                this.ReferenceData = response.data.data
            })
            .catch((error) => {
                console.log(error);
            });
        },

        // Modify by LINA 01/10/2023 #cms-89
        CreateOrUpdateChildren(){
            this.childrenForm.DataForm = this.dataAdapter.records;
            this.childrenForm.Employee_PKID = this.form.id;
            for(var i = 0; i < this.dataAdapter.records.length; i++) {
                this.childrenForm.DataForm[i].DateOfBirth = this.$helper.ConvertDateToStr(this.dataAdapter.records[i].DateOfBirth);
            }
            this.childrenForm
                .post("api/create-or-update-emp-child", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                if(this.form.id == 0) {
                    this.dialogSaveList = true;
                }else{
                    this.ReadEmployeeActive();
                    this.alertSnackbarMsg = this.$t('employee.saveChildren');
                    this.snackbar = true;
                }
            })
            .catch((errors) => {
                
            });
        },
        //-----------------------------------
        CreateOrUpdateEducation(){
            this.educationForm.EducationDataForm = this.dataAdapterEducation.records;
            this.educationForm.Employee_PKID = this.form.id;
            for(var i = 0; i < this.dataAdapterEducation.records.length; i++) {
                this.educationForm.EducationDataForm[i].StartDate = this.$helper.ConvertDateToStr(this.dataAdapterEducation.records[i].StartDate);
                this.educationForm.EducationDataForm[i].EndDate = this.$helper.ConvertDateToStr(this.dataAdapterEducation.records[i].EndDate);
            }
            this.educationForm.post("api/create-or-update-emp-education", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                if(this.form.id == 0) {
                    this.dialogSaveList = true;
                }else{
                    this.ReadEmployeeActive();
                    this.alertSnackbarMsg = this.$t('employee.saveEducation');
                    this.snackbar = true;
                }
            })
            .catch((errors) => {
            });
        },
        //-----------------------------------
        CreateOrUpdateOtherSkill(){
            this.otherSkillForm.OtherSkillDataForm = this.dataAdapterOtherSkill.records;
            this.otherSkillForm.Employee_PKID = this.form.id;
            for(var i = 0; i < this.dataAdapterOtherSkill.records.length; i++) {
                this.otherSkillForm.OtherSkillDataForm[i].StartDateOtherSkill = this.$helper.ConvertDateToStr(this.dataAdapterOtherSkill.records[i].StartDateOtherSkill);
                this.otherSkillForm.OtherSkillDataForm[i].EndDateOtherSkill = this.$helper.ConvertDateToStr(this.dataAdapterOtherSkill.records[i].EndDateOtherSkill);
            }
            this.otherSkillForm.post("api/create-or-update-emp-other-skill", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                if(this.form.id == 0) {
                    this.dialogSaveList = true;
                }else{
                    this.ReadEmployeeActive();
                    this.alertSnackbarMsg = this.$t('employee.saveOtherSkill');
                    this.snackbar = true;
                }
            })
            .catch((errors) => {
                this.errorsMessage = errors.response.data.errors;
            });
        },
        //-----------------------------------
        CreateOrUpdateExperience(){
            this.experienceForm.ExperienceDataForm = this.dataAdapterExperience.records;
            this.experienceForm.Employee_PKID = this.form.id;
            for(var i = 0; i < this.dataAdapterExperience.records.length; i++) {
                this.experienceForm.ExperienceDataForm[i].StartDateExperience = this.$helper.ConvertDateToStr(this.dataAdapterExperience.records[i].StartDateExperience);
                this.experienceForm.ExperienceDataForm[i].EndDateExperience = this.$helper.ConvertDateToStr(this.dataAdapterExperience.records[i].EndDateExperience);
            }
            this.experienceForm.post("api/create-or-update-emp-experience", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                if(this.form.id == 0) {
                    this.dialogSaveList = true;
                }else{
                    this.ReadEmployeeActive();
                    this.alertSnackbarMsg = this.$t('employee.saveExperience');
                    this.snackbar = true;
                }
            })
            .catch((errors) => {
                this.errorsMessage = errors.response.data.errors;
            });
        },
        //-----------------------------------
        CreateOrUpdateLanguage(){
            this.languageForm.LanguageDataForm = this.dataAdapterLanguage.records;
            this.languageForm.Employee_PKID = this.form.id;
            this.languageForm.post("api/create-or-update-emp-language", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                // alert("success");
                if(this.form.id == 0) {
                    this.dialogSaveList = true;
                }else{
                    this.ReadEmployeeActive();
                    this.alertSnackbarMsg = this.$t('employee.saveLanguage');
                    this.snackbar = true;
                }
            })
            .catch((errors) => {
                this.errorsMessage = errors.response.data.errors;
            });
        },
        //-----------------------------------
        CreateOrUpdateReference(){
            this.referenceForm.ReferenceDataForm = this.dataAdapterReference.records;
            this.referenceForm.Employee_PKID = this.form.id;
            this.referenceForm.post("api/create-or-update-emp-reference", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
            })
            .then((response) => {
                if(this.form.id == 0) {
                    this.dialogSaveList = true;
                }else{
                    this.ReadEmployeeActive();
                    this.alertSnackbarMsg = this.$t('employee.saveReference');
                    this.snackbar = true;
                }
            })
            .catch((errors) => {
                this.errorsMessage = errors.response.data.errors;
            });
        },

        closeDialog() {
            this.editMode = false;
            this.employeeForm = false;
            this.form.role_id = "";
            this.form.ParameterPKID = "";
            this.form.firstName = "";
            this.form.lastName = "";
            this.form.otherName = "";
            this.form.email = "";
            this.form.gender = "male";
            // this.form.position_id = "";
            // this.form.shift_header_PKID = "";
            this.form.phone_number = [{ phone: "" }];
            this.form.image = null;
            this.form.start_date = "";
            this.preview_profile = null;
            this.preview_profile_edit = null;
            //modify LINA 26/07/2023 #cms-80
            this.form.nationalID = "";
            this.form.birthDate = "";
            this.form.birthPlace = "";
            this.form.houseNo = "";
            this.form.streetNo = "";
            this.form.province = "";
            this.form.district = "";
            this.form.commune = "";
            this.form.villageCode = "";
            this.form.facebook = "";
            this.form.telegram = "";
            this.form.otherContact = "";
            // this.form.UserID = "";
            this.form.password = "";
            this.form.password_confirmation = "";
            this.tableLoading = false;
            this.isDisabledColunm = false;
            this.errorsMessage = "";
            this.btnSaveLoading = false;

            this.chDateTime.openError = false;
            this.dataAdapter._source.localdata = [];
            if(this.$refs.gridDetail !== undefined) {
                this.$refs.gridDetail.updatebounddata('cell');
            }
            this.dataAdapterEducation._source.localdata = [];
            if(this.$refs.gridEducation !== undefined) {
                this.$refs.gridEducation.updatebounddata('cell');
            }
            this.dataAdapterOtherSkill._source.localdata = [];
            if(this.$refs.gridOtherSkill !== undefined) {
                this.$refs.gridOtherSkill.updatebounddata('cell');
            }
            this.dataAdapterExperience._source.localdata = [];
            if(this.$refs.gridExperience !== undefined) {
                this.$refs.gridExperience.updatebounddata('cell');
            }
            this.dataAdapterLanguage._source.localdata = [];
            if(this.$refs.gridLanguage !== undefined) {
                this.$refs.gridLanguage.updatebounddata('cell');
            }
            this.dataAdapterReference._source.localdata = [];
            if(this.$refs.gridReference !== undefined) {
                this.$refs.gridReference.updatebounddata('cell');
            }
        },
        addEmployee() {
            this.tab = 1;
            this.form.id = 0;
            this.closeDialog();
        },
        addPhone: function () {
            this.form.phone_number.push({ phone: "" });
        },
        removePhone(index) {
            this.form.phone_number.splice(index, 1);
        },

        // --------------image-----------------
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

        createEmployee() {
            this.btnSaveLoading = true;
            // Modify by LINA 02/10/2023 #cms-89
            this.form.birthDateFrom = this.dataAdapter.records;
            for(var i = 0; i < this.form.birthDateFrom.length; i++) {
                this.form.birthDateFrom[i].DateOfBirth = this.$helper.ConvertDateToStr(this.form.birthDateFrom[i].DateOfBirth);
            }

            this.form.startDateForm = this.dataAdapterEducation.records;
            this.form.endDateFrom = this.dataAdapterEducation.records;
            for(var i = 0; i < this.form.startDateForm.length; i++) {
                this.form.startDateForm[i].StartDate = this.$helper.ConvertDateToStr(this.form.startDateForm[i].StartDate);
            }
            for(var i = 0; i < this.form.endDateFrom.length; i++) {
                this.form.endDateFrom[i].EndDate = this.$helper.ConvertDateToStr(this.form.endDateFrom[i].EndDate);
            }

            this.form.startDateForm = this.dataAdapterOtherSkill.records;
            this.form.endDateFrom = this.dataAdapterOtherSkill.records;
            for(var i = 0; i < this.form.startDateForm.length; i++) {
                this.form.startDateForm[i].StartDateOtherSkill = this.$helper.ConvertDateToStr(this.form.startDateForm[i].StartDateOtherSkill);
            }
            for(var i = 0; i < this.form.endDateFrom.length; i++) {
                this.form.endDateFrom[i].EndDateOtherSkill = this.$helper.ConvertDateToStr(this.form.endDateFrom[i].EndDateOtherSkill);
            }

            this.form.startDateForm = this.dataAdapterExperience.records;
            this.form.endDateFrom = this.dataAdapterExperience.records;
            for(var i = 0; i < this.form.startDateForm.length; i++) {
                this.form.startDateForm[i].StartDateExperience = this.$helper.ConvertDateToStr(this.form.startDateForm[i].StartDateExperience);
            }
            for(var i = 0; i < this.form.endDateFrom.length; i++) {
                this.form.endDateFrom[i].EndDateExperience = this.$helper.ConvertDateToStr(this.form.endDateFrom[i].EndDateExperience);
            }

            this.tableLoading = true;
            this.form.post("api/create-employee", {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
                })
                .then((response) => {
                    this.ReadEmployeeActive();
                    this.ReadEmployeeInactive();
                    this.closeDialog();
                    this.alertSnackbarMsg = this.$t('employee.savedMsg');
                    this.snackbar = true;
                    this.btnSave = false;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                })
                .catch((errors) => {
                    this.errorsMessage = errors.response.data.errors;
                    this.btnSave = false;
                    this.btnSaveLoading = false;
                    this.tableLoading = false;
                });
        },

        editEmployee(employee) {
            this.editMode = true;
            this.form.id = employee.id;
            if (employee.gender == "male") {
                this.form.gender = "male";
            } else if (employee.gender == "female") {
                this.form.gender = "female";
            }
            this.form.firstName = employee.first_name;
            this.form.lastName = employee.last_name;
            this.form.email = employee.email;
            this.form.otherName = employee.OtherName;
            this.form.phone_number = employee.phone_number;
            this.preview_profile_edit = employee.image;
            // this.form.position_id = employee.position.id;
            this.form.role_id = employee.role.id;
            this.form.shift_header_PKID = employee.ShiftName != null ? employee.ShiftName.PKID : 0;
            this.form.start_date = employee.start_date;
            this.form.is_inactived = employee.is_inactived;
            this.form.leave_date = employee.leave_date;
            this.form.ParameterPKID = employee.StaffCodeID;
            //modify LINA 26/07/2023 #cms-80
            this.form.nationalID = employee.NationalID;
            this.form.birthDate = employee.BirthDate;
            this.form.birthPlace = employee.BirthPlace;
            this.form.houseNo = employee.HouseNo;
            this.form.streetNo = employee.StreetNo;
            this.form.province = employee.ProvinceId;
            this.form.district = employee.DistrictCode;
            this.form.commune = employee.CommuneCode;
            this.form.villageCode = employee.VillageCode;
            this.form.facebook = employee.Facebook;
            this.form.telegram = employee.Telegram;
            this.form.otherContact = employee.OtherContact;
            // this.form.UserID = employee.User_id;
            // this.isDisabledColunm = employee.UserID == 0 ? false : true;
            this.districtData = employee.districtListByProvinceCode;
            this.communeData = employee.communeListByDistrictCode;
            this.villageData = employee.villageListByCommuneCode;

            this.dataAdapter._source.localdata = employee.employeeChildren;
            if(this.$refs.gridDetail !== undefined) {
                this.$refs.gridDetail.updatebounddata('cell');
            }
            this.dataAdapterEducation._source.localdata = employee.employeeEducation;
            if(this.$refs.gridEducation !== undefined) {
                this.$refs.gridEducation.updatebounddata('cell');
            }
            this.dataAdapterOtherSkill._source.localdata = employee.employeeOtherSkill;
            if(this.$refs.gridOtherSkill !== undefined) {
                this.$refs.gridOtherSkill.updatebounddata('cell');
            }
            this.dataAdapterExperience._source.localdata = employee.employeeExperience;
            if(this.$refs.gridExperience !== undefined) {
                this.$refs.gridExperience.updatebounddata('cell');
            }
            this.dataAdapterLanguage._source.localdata = employee.employeeLanguge;
            if(this.$refs.gridLanguage !== undefined) {
                this.$refs.gridLanguage.updatebounddata('cell');
            }
            this.dataAdapterReference._source.localdata = employee.employeeReference;
            if(this.$refs.gridReference !== undefined) {
                this.$refs.gridReference.updatebounddata('cell');
            }

            this.employeeForm = true;
            this.tab = 1;
        },

        async updateEmployee() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form.birthDateFrom = this.dataAdapter.records;
                for(var i = 0; i < this.form.birthDateFrom.length; i++) {
                    this.form.birthDateFrom[i].DateOfBirth = this.$helper.ConvertDateToStr(this.form.birthDateFrom[i].DateOfBirth);
                }
            this.form.startDateForm = this.dataAdapterEducation.records;
            this.form.endDateFrom = this.dataAdapterEducation.records;
                for(var i = 0; i < this.form.startDateForm.length; i++) {
                    this.form.startDateForm[i].StartDate = this.$helper.ConvertDateToStr(this.form.startDateForm[i].StartDate);
                }
                for(var i = 0; i < this.form.endDateFrom.length; i++) {
                    this.form.endDateFrom[i].EndDate = this.$helper.ConvertDateToStr(this.form.endDateFrom[i].EndDate);
                }
            this.form.startDateForm = this.dataAdapterOtherSkill.records;
            this.form.endDateFrom = this.dataAdapterOtherSkill.records;
                for(var i = 0; i < this.form.startDateForm.length; i++) {
                    this.form.startDateForm[i].StartDateOtherSkill = this.$helper.ConvertDateToStr(this.form.startDateForm[i].StartDateOtherSkill);
                }
                for(var i = 0; i < this.form.endDateFrom.length; i++) {
                    this.form.endDateFrom[i].EndDateOtherSkill = this.$helper.ConvertDateToStr(this.form.endDateFrom[i].EndDateOtherSkill);
                }
            this.form.startDateForm = this.dataAdapterExperience.records;
            this.form.endDateFrom = this.dataAdapterExperience.records;
                for(var i = 0; i < this.form.startDateForm.length; i++) {
                    this.form.startDateForm[i].StartDateExperience = this.$helper.ConvertDateToStr(this.form.startDateForm[i].StartDateExperience);
                }
                for(var i = 0; i < this.form.endDateFrom.length; i++) {
                    this.form.endDateFrom[i].EndDateExperience = this.$helper.ConvertDateToStr(this.form.endDateFrom[i].EndDateExperience);
                }

            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
                .post("/api/update-employee/" + this.form.id, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
                })
                .then((response) => {
                    this.ReadEmployeeActive();
                    this.ReadEmployeeInactive();
                    this.closeDialog();
                    this.alertSnackbarMsg = this.$t('employee.updateMsg');
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

        deleteEmployee(user, fullName) {
            this.form.id = user;
            this.userNameDelete = fullName;
            this.dialogDelete = true;
        },

        async submitDelete() {
            this.btnLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            axios
                .delete("/api/delete-employee/" + this.form.id, {
                headers: {
                    Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                },
                })
                .then((response) => {
                    if(response.data.errorMessage !== undefined) {
                        this.dialogWarning = true;
                        this.usedData = response.data.errorMessage.split(";");
                    }
                    else {
                        this.ReadEmployeeActive();
                        this.ReadEmployeeInactive();
                        this.alertSnackbarMsg = this.$t('employee.deleteMsg');
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