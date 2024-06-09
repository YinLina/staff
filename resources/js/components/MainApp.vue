<template>
    <div>
        <v-app v-if="!loggedIn">
            <v-container class="login-container" fill-height fluid>
                <router-view />
            </v-container>
        </v-app>

        <v-app v-if="loggedIn" id="main-app">
            <v-navigation-drawer :width="navigation.width" color="grey lighten-5" app v-model="drawer" :mini-variant="mini" permanent ref="drawer">
                <v-card flat class="menu-card khawin-background-color">
                    <v-list class="profile">
                        <v-list-item link class="text-center pa-0">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <v-avatar color="grey lighten-5" size="50" v-show="!mini">
                                        <span v-if="employee.pic == 'default.png'" class="
                                        blue--text
                                        text--lighten-1
                                        headline-1
                                        font-weight-medium
                                        "
                                        >
                                            {{
                                                user.data.UserID
                                                    .split(" ")
                                                    .map((x) => x[0].toUpperCase())
                                                    .join("")
                                            }}
                                        </span>

                                        <v-img v-else :src="'/employees/' + employee.pic" />
                                    </v-avatar>
                                    <v-avatar color="grey lighten-5" size="40" v-show="mini">
                                        <span v-if="employee.pic == 'default.png'" class="
                                        blue--text
                                        text--lighten-1
                                        headline-1
                                        font-weight-medium
                                        "
                                    >
                                            {{
                                                user.data.UserID
                                                    .split(" ")
                                                    .map((x) => x[0].toUpperCase())
                                                    .join("")
                                            }}
                                        </span>

                                        <v-img v-else :src="'/employees/' + employee.pic" />
                                    </v-avatar>
                                    <h5 class="
                                    mt-2
                                    mb-0
                                    white--text
                                    text--darken-1
                                    font-weight-regular
                                " v-show="!mini">
                                        {{ employee.first_name + " " + employee.last_name }}
                                    </h5>
                                </v-list-item-title>
                                <v-list-item-subtitle class="white--text text--darken-1" v-show="!mini">
                                    {{ user.data.email }}
                                </v-list-item-subtitle>

                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-card>

                <div id="menu">
                    <!-- --------------large----------------- -->
                    <v-treeview
                        v-model="tree"
                        :open="initiallyOpen"
                        :items="menu.menu"
                        item-key="name"
                        open-on-click
                        expand-icon="mdi-menu-down"
                        class="tree-menu"
                        v-show="!mini"
                    >
                        <template v-slot:prepend="{ item }">
                            <v-icon>{{ item.icon }}</v-icon>
                        </template>
                        <template slot="label" slot-scope="props">
                            <v-list dense nav>
                                <v-list-item link :to="props.item.children.length > 0 ? '' : props.item.url">
                                    <v-list-item-content v-show="!mini" :class="!mini ? '' : 'v-treeview-node-test'">
                                        <v-list-item-title class="text-capitalize khmer-font">
                                            <span v-if="props.item.name == 'dashboard'">{{ $t('menu.dashboard') }}</span>
                                            <span v-if="props.item.name == 'position'">{{ $t('menu.position') }}</span>
                                            <span v-if="props.item.name == 'absent'">{{ $t('menu.absent') }}</span>
                                            <span v-if="props.item.name == 'employee'">{{ $t('menu.employee') }}</span>
                                            <span v-if="props.item.name == 'employeeList'">{{ $t('menu.employeeList') }}</span>
                                            <span v-if="props.item.name == 'absentList'">{{ $t('menu.absentList') }}</span>
                                            <span v-if="props.item.name == 'report'">{{ $t('menu.report') }}</span>
                                            <span v-if="props.item.name == 'user'">{{ $t('menu.user') }}</span>
                                            <span v-if="props.item.name == 'setting'">{{ $t('menu.setting') }}</span>
                                            <span v-if="props.item.name == 'account'">{{ $t('menu.account') }}</span>
                                            <span v-if="props.item.name == 'role'">{{ $t('menu.role') }}</span>
                                            <span v-if="props.item.name == 'Inventory'">{{ $t('menu.Inventory') }}</span>
                                            <span v-if="props.item.name == 'AddStock'">{{ $t('menu.AddStock') }}</span>
                                            <span v-if="props.item.name == 'issueItem'">{{ $t('menu.issueItem') }}</span>
                                            <span v-if="props.item.name == 'ReturnItem'">{{ $t('menu.ReturnItem') }}</span>
                                            <span v-if="props.item.name == 'designationSetting'">{{ $t('menu.designationSetting') }}</span>
                                            <span v-if="props.item.name == 'resignationSetting'">{{ $t('menu.resignationSetting') }}</span>
                                            <span v-if="props.item.name == 'leaveTypeSetting'">{{ $t('menu.leaveTypeSetting') }}</span>
                                            <span v-if="props.item.name == 'humanResourceSetting'">{{ $t('menu.humanResourceSetting') }}</span>
                                            <span v-if="props.item.name == 'branchSetting'">{{ $t('menu.branchSetting') }}</span>
                                            <span v-if="props.item.name == 'departmentSetting'">{{ $t('menu.departmentSetting') }}</span>
                                            <span v-if="props.item.name == 'leaveReason'">{{ $t('menu.leaveReason') }}</span>
                                            <span v-if="props.item.name == 'inventorySetting'">{{ $t('menu.inventorySetting') }}</span>
                                            <span v-if="props.item.name == 'addItem'">{{ $t('menu.addItem') }}</span>
                                            <span v-if="props.item.name == 'AssetCategory'">{{ $t('menu.AssetCategory') }}</span>
                                            <span v-if="props.item.name == 'itemSupplier'">{{ $t('menu.itemSupplier') }}</span>
                                            <span v-if="props.item.name == 'itemStore'">{{ $t('menu.itemStore') }}</span>
                                            <span v-if="props.item.name == 'HumanResource'">{{ $t('menu.HumanResource') }}</span>
                                            <span v-if="props.item.name == 'StaffAttendance'">{{ $t('menu.StaffAttendance') }}</span>
                                            <span v-if="props.item.name == 'PayRoll'">{{ $t('menu.PayRoll') }}</span>
                                            <span v-if="props.item.name == 'ApplyLeave'">{{ $t('menu.ApplyLeave') }}</span>
                                            <span v-if="props.item.name == 'ApproveStaffLeave'">{{ $t('menu.ApproveStaffLeave') }}</span>
                                            <span v-if="props.item.name == 'StaffRating'">{{ $t('menu.StaffRating') }}</span>
                                            <span v-if="props.item.name == 'StaffResignation'">{{ $t('menu.StaffResignation') }}</span>
                                            <span v-if="props.item.name == 'StaffInformation'">{{ $t('menu.StaffInformation') }}</span>
                                            <span v-if="props.item.name == 'systemSetting'">{{ $t('menu.systemSetting') }}</span>
                                            <span v-if="props.item.name == 'accountSystemSetting'">{{ $t('menu.accountSystemSetting') }}</span>
                                            <span v-if="props.item.name == 'userSystemSetting'">{{ $t('menu.userSystemSetting') }}</span>
                                            <span v-if="props.item.name == 'roleSystemSetting'">{{ $t('menu.roleSystemSetting')}}</span>
                                            <span v-if="props.item.name == 'parameter'">{{ $t('menu.parameter') }}</span>
                                            <span v-if="props.item.name == 'WorkFlow'">{{ $t('menu.WorkFlow') }}</span>
                                            <span v-if="props.item.name == 'menuSystemSetting'">{{ $t('menu.menuSystemSetting') }}</span>
                                            <span v-if="props.item.name == 'shift'">{{ $t('menu.shift') }}</span>
                                            <span v-if="props.item.name == 'validation'">{{ $t('menu.validation') }}</span>
                                            <span v-if="props.item.name == 'leaveRequest'">{{ $t('menu.leaveRequest') }}</span>
                                            <span v-if="props.item.name == 'Staff'">{{ $t('menu.Staff') }}</span>
                                            <span v-if="props.item.name == 'Attendance'">{{ $t('menu.Attendance') }}</span>
                                            <span v-if="props.item.name == 'LeaveEntry'">{{ $t('menu.LeaveEntry') }}</span>
                                            <span v-if="props.item.name == 'CalculatePayroll'">{{ $t('menu.CalculatePayroll') }}</span>
                                            <span v-if="props.item.name == 'BasicSalary'">{{ $t('menu.BasicSalary') }}</span>
                                            <span v-if="props.item.name == 'Holiday'">{{ $t('menu.Holiday') }}</span>
                                            <span v-if="props.item.name == 'StaffPosition'">{{ $t('menu.StaffPosition') }}</span>
                                            <span v-if="props.item.name == 'DocumentForm'">{{ $t('menu.DocumentForm') }}</span>
                                            <span v-if="props.item.name == 'Document'">{{ $t('menu.Document') }}</span>
                                            <span v-if="props.item.name == 'SystemLog'">{{ $t('menu.SystemLog') }}</span>
                                            <span v-if="props.item.name == 'StaffShift'">{{ $t('menu.StaffShift') }}</span>
                                        </v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </template>
                    </v-treeview>
                    <!-- --------------mini----------------- -->
                    <v-list v-show="mini" class="small-menu">
                        <v-list-item v-for="(item, i) in menu.menu"
                            :key="i"
                            link
                            :to="item.children.length > 0 ? '' : item.url"
                            class="tree-menu"
                        >
                            <v-menu
                                offset-x :nudge-width="200"
                                transition="slide-x-transition"
                                :content-class="item.children.length > 0 ? 'arrow-menu' : ''"
                                open-on-hover
                            >
                                <!-- open-on-hover -->
                                <template v-slot:activator="{ on, attrs }">
                                    <v-list-item-icon v-bind="attrs" v-on="on">
                                        <v-icon v-text="item.icon"></v-icon>
                                    </v-list-item-icon>
                                </template>
                                <v-card>
                                    <span v-for="(menu, index) in menu.menu" :key="index">
                                        <span v-if="(item.id == menu.id) && item.children.length > 0">
                                            <v-treeview
                                                hoverable
                                                open-all
                                                dense
                                                :items="[menu]"
                                                open-on-click
                                                expand-icon="mdi-menu-down"
                                                class="tree-menu"
                                            >
                                                <template v-slot:prepend="{ item }">
                                                    <v-icon v-if="item.icon" class="pa-0 ma-0">{{ item.icon }}</v-icon>
                                                </template>
                                                <template slot="label" slot-scope="props">
                                                    <v-list dense nav>
                                                        <v-list-item class="pa-0 ma-0 pr-2 mr-2" link
                                                            :to="props.item.children.length > 0 ? '' : props.item.url">
                                                            <v-list-item-content>
                                                                <v-list-item-title class="text-capitalize khmer-font">
                                                                    <span v-if="props.item.name == 'dashboard'">{{ $t('menu.dashboard') }}</span>
                                                                    <span v-if="props.item.name == 'employeeList'">{{ $t('menu.employeeList') }}</span>
                                                                    <span v-if="props.item.name == 'absentList'">{{ $t('menu.absentList') }}</span>
                                                                    <span v-if="props.item.name == 'position'">{{ $t('menu.position') }}</span>
                                                                    <span v-if="props.item.name == 'absent'">{{ $t('menu.absent') }}</span>
                                                                    <span v-if="props.item.name == 'employee'">{{ $t('menu.employee') }}</span>
                                                                    <span v-if="props.item.name == 'report'">{{ $t('menu.report') }}</span>
                                                                    <span v-if="props.item.name == 'user'">{{ $t('menu.user') }}</span>
                                                                    <span v-if="props.item.name == 'setting'">{{ $t('menu.setting') }}</span>
                                                                    <span v-if="props.item.name == 'account'">{{ $t('menu.account') }}</span>
                                                                    <span v-if="props.item.name == 'role'">{{ $t('menu.role') }}</span>
                                                                    <!-- =========================================== -->
                                                                    <!-- <span v-if="props.item.name == 'inventory'">{{ $t('menu.inventory') }}</span>
                                                                    <span>{{ $t('menu.addstock') }}</span> -->
                                                                    <span v-if="props.item.name == 'humanResourceSetting'">{{ $t('menu.humanResourceSetting') }}</span>
                                                                    <span v-if="props.item.name == 'branchSetting'">{{ $t('menu.branchSetting') }}</span>
                                                                    <span v-if="props.item.name == 'departmentSetting'">{{ $t('menu.departmentSetting') }}</span>
                                                                    <span v-if="props.item.name == 'designationSetting'">{{ $t('menu.designationSetting') }}</span>
                                                                    <span v-if="props.item.name == 'resignationSetting'">{{ $t('menu.resignationSetting') }}</span>
                                                                    <span v-if="props.item.name == 'leaveTypeSetting'">{{ $t('menu.leaveTypeSetting') }}</span>
                                                                    <span v-if="props.item.name == 'leaveReason'">{{ $t('menu.leaveReason') }}</span>
                                                                    <span v-if="props.item.name == 'inventorySetting'">{{ $t('menu.inventorySetting') }}</span>
                                                                    <span v-if="props.item.name == 'addItem'">{{ $t('menu.addItem') }}</span>
                                                                    <span v-if="props.item.name == 'systemSetting'">{{ $t('menu.systemSetting') }}</span>
                                                                    <span v-if="props.item.name == 'AssetCategory'">{{ $t('menu.AssetCategory') }}</span>
                                                                    <span v-if="props.item.name == 'itemSupplier'">{{ $t('menu.itemSupplier') }}</span>
                                                                    <span v-if="props.item.name == 'itemStore'">{{ $t('menu.itemStore') }}</span>
                                                                    <span v-if="props.item.name == 'parameter'">{{ $t('menu.parameter') }}</span>
                                                                    <span v-if="props.item.name == 'WorkFlow'">{{ $t('menu.WorkFlow') }}</span>
                                                                    <span v-if="props.item.name == 'shift'">{{ $t('menu.shift') }}</span>
                                                                    <span v-if="props.item.name == 'validation'">{{ $t('menu.validation') }}</span>
                                                                    <span v-if="props.item.name == 'leaveRequest'">{{ $t('menu.leaveRequest') }}</span>
                                                                    <span v-if="props.item.name == 'LeaveEntry'">{{ $t('menu.LeaveEntry') }}</span>
                                                                    <span v-if="props.item.name == 'CalculatePayroll'">{{ $t('menu.CalculatePayroll') }}</span>
                                                                    <span v-if="props.item.name == 'BasicSalary'">{{ $t('menu.BasicSalary') }}</span>
                                                                    <span v-if="props.item.name == 'ApplyLeave'">{{ $t('menu.ApplyLeave') }}</span>
                                                                    <span v-if="props.item.name == 'Attendance'">{{ $t('menu.Attendance') }}</span>
                                                                    <span v-if="props.item.name == 'HumanResource'">{{ $t('menu.HumanResource') }}</span>
                                                                    <span v-if="props.item.name == 'Staff'">{{ $t('menu.Staff') }}</span>
                                                                    <span v-if="props.item.name == 'StaffInformation'">{{ $t('menu.StaffInformation') }}</span>
                                                                    <span v-if="props.item.name == 'Holiday'">{{ $t('menu.Holiday') }}</span>
                                                                    <span v-if="props.item.name == 'StaffPosition'">{{ $t('menu.StaffPosition') }}</span>

                                                                    <!-- =============Fix by Theara============= -->
                                                                    <span v-if="props.item.name == 'StaffRating'">{{ $t('menu.StaffRating') }}</span>
                                                                    <span v-if="props.item.name == 'StaffResignation'">{{ $t('menu.StaffResignation') }}</span>
                                                                    <span v-if="props.item.name == 'AddStock'">{{ $t('menu.AddStock') }}</span>
                                                                    <span v-if="props.item.name == 'issueItem'">{{ $t('menu.issueItem') }}</span>
                                                                    <span v-if="props.item.name == 'ReturnItem'">{{ $t('menu.ReturnItem') }}</span>
                                                                    <span v-if="props.item.name == 'menuSystemSetting'">{{ $t('menu.menuSystemSetting') }}</span>
                                                                    <span v-if="props.item.name == 'Inventory'">{{ $t('menu.Inventory') }}</span>
                                                                    <span v-if="props.item.name == 'PayRoll'">{{ $t('menu.PayRoll') }}</span>
                                                                    <span v-if="props.item.name == 'DocumentForm'">{{ $t('menu.DocumentForm') }}</span>
                                                                    <span v-if="props.item.name == 'Document'">{{ $t('menu.Document') }}</span>
                                                                    <span v-if="props.item.name == 'SystemLog'">{{ $t('menu.SystemLog') }}</span>
                                                                    <span v-if="props.item.name == 'StaffShift'">{{ $t('menu.StaffShift') }}</span>
                                                                </v-list-item-title>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </v-list>
                                                </template>
                                            </v-treeview>
                                        </span>
                                    </span>
                                </v-card>
                            </v-menu>
                        </v-list-item>
                    </v-list>
                </div>

                <template v-slot:append>
                    <v-btn block depressed class="my-2 khmer-font" color="blue-grey lighten-4" @click="logout"
                        v-show="!mini">
                        {{ $t('menu.signout') }}
                        <v-icon small>mdi-logout-variant</v-icon>
                    </v-btn>

                    <v-btn block large depressed class="khmer-font" color="blue-grey lighten-4" @click="logout"
                        v-show="mini">
                        <v-icon>mdi-logout-variant</v-icon>
                    </v-btn>
                </template>
            </v-navigation-drawer>

            <!-- app-bar -->
            <v-app-bar color="white" app flat dense elevate-on-scroll elevation="3">
                <v-app-bar-nav-icon @click.stop="resizeSideBar" color="blue-grey darken-3"></v-app-bar-nav-icon>

                <v-toolbar-title class="grey--text text--darken-2">
                    <h4 class="khmer-font font-weight-medium">{{ $t('app.title') }}</h4>
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <!-- content-class="elevation-0" -->
                <v-menu offset-y open-on-hover>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn v-bind="attrs" v-on="on" depressed text rounded
                            class="text-lowercase font-weight-regular khmer-font">
                            <v-icon small>mdi-translate</v-icon>
                            {{ $t('languages.language') }}
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item link>
                            <v-img height="20" width="20" :src="'/image/englishflag.png'"></v-img>
                            <v-list-item-title @click="langChanged('english')" class="ml-1 khmer-font">
                                {{ $t('languages.english') }}
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item link>
                            <v-img height="20" width="20" :src="'/image/cambodaiflag.png'"></v-img>

                            <v-list-item-title @click="langChanged('khmer')" class="ml-1 khmer-font">
                                {{ $t('languages.khmer') }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>

                <v-btn class="text-capitalize khmer-font pa-1 py-1 app-date" color="grey lighten-2 indigo--text"
                    depressed @click="openDialog">
                    <v-icon small left>mdi-calendar-month</v-icon>

                    <span v-if="formatDate(new Date()).split(',')[0] == 'Monday'">
                        <span>{{ $t('app.monday') }},</span>
                        {{ formatDate(new Date()).split(',')[1] }}
                    </span>

                    <span v-if="formatDate(new Date()).split(',')[0] == 'Tuesday'">
                        <span>{{ $t('app.tuesday') }},</span>
                        {{ formatDate(new Date()).split(',')[1] }}
                    </span>

                    <span v-if="formatDate(new Date()).split(',')[0] == 'Wednesday'">
                        <span>{{ $t('app.wednesday') }},</span>
                        {{ formatDate(new Date()).split(',')[1] }}
                    </span>

                    <span v-if="formatDate(new Date()).split(',')[0] == 'Thursday'">
                        <span>{{ $t('app.thursday') }},</span>
                        {{ formatDate(new Date()).split(',')[1] }}
                    </span>

                    <span v-if="formatDate(new Date()).split(',')[0] == 'Friday'">
                        <span>{{ $t('app.friday') }},</span>
                        {{ formatDate(new Date()).split(',')[1] }}
                    </span>

                    <span v-if="formatDate(new Date()).split(',')[0] == 'Saturday'">
                        <span>{{ $t('app.saturday') }},</span>
                        {{ formatDate(new Date()).split(',')[1] }}
                    </span>

                    <span v-if="formatDate(new Date()).split(',')[0] == 'Sunday'">
                        <!-- <span :class="getColor(formatDate(new Date()).split(',')[0])+'--text'">{{ $t('app.sunday') }},</span> -->
                        <span>{{ $t('app.sunday') }},</span>
                        {{ formatDate(new Date()).split(',')[1] }}
                    </span>
                </v-btn>
            </v-app-bar>

            <!-- content -->
            <v-main class="mt-5 mx-2">
                <v-container fluid>
                    <router-view />
                </v-container>
            </v-main>
        </v-app>

        <!-- -------dialog-------- -->
        <v-dialog v-model="calendarDialog" fullscreen hide-overlay transition="dialog-bottom-transition"
            class="calendarDialog" id="calendarDialog">
            <v-card flat>
                <v-toolbar class="khawin-background-color" dark extended flat>
                    <v-btn icon @click="calendarDialog = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>
                <v-card class="mx-auto" max-width="1000" style="margin-top: -64px;">
                    <v-toolbar flat>
                        <v-toolbar-title class="blue-grey--text text--darken-2">
                            <div class="d-inline-flex align-center">
                                <v-icon small color="blue-grey darken-1">mdi-calendar-month</v-icon>
                                <h5 class="khmer-font font-weight-medium">{{ $t('holiday.title') }} </h5>
                                <v-chip class="ml-1 pa-1 font-weight-bold" label text-color="light-blue darken-3"
                                    @click="chooseHolidayYear">
                                    {{ new Date().getFullYear() }}
                                </v-chip>
                            </div>
                        </v-toolbar-title>
                        <v-spacer></v-spacer>

                        <v-btn depressed class="khawin-background-color khmer-font" small dark
                            @click="openDialogInsertForm()">
                            <v-icon left>mdi-plus-circle</v-icon>
                            {{ $t('holiday.addHoliday') }}
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" sm="12" md="8">
                                <v-data-table class="khmer-font" :headers="headers" :items="holidayData"
                                    :items-per-page="-1">
                                    <template v-slot:[`item.id`]="item">
                                        {{ item.index + 1 }}
                                    </template>

                                    <template v-slot:[`item.day`]="{ item }">
                                        <v-chip v-if="formatCompareDate(new Date()) > item.date"
                                            :text-color="getColor(item.day)"
                                            class="text-capitalize absent-day-chip text-decoration-line-through font-weight-medium"
                                            small>
                                            <span v-if="item.day == 'Monday'">{{ $t('absent.monday') }}</span>
                                            <span v-if="item.day == 'Tuesday'">{{ $t('absent.tuesday') }}</span>
                                            <span v-if="item.day == 'Wednesday'">{{ $t('absent.wednesday') }}</span>
                                            <span v-if="item.day == 'Thursday'">{{ $t('absent.thursday') }}</span>
                                            <span v-if="item.day == 'Friday'">{{ $t('absent.friday') }}</span>
                                            <span v-if="item.day == 'Saturday'">{{ $t('absent.saturday') }}</span>
                                            <span v-if="item.day == 'Sunday'">{{ $t('absent.sunday') }}</span>
                                        </v-chip>

                                        <v-chip v-else dark :color="getColor(item.day)"
                                            class="text-capitalize absent-day-chip" small>
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
                                        <v-chip class="pa-1 holiday-dates font-weight-bold" small color="red lighten-5"
                                            text-color="red darken-3" label>
                                            <v-avatar left class="mr-0">
                                                <v-icon small>mdi-calendar-month</v-icon>
                                            </v-avatar>

                                            <span v-if="formatCompareDate(new Date()) > item.date"
                                                class="text-decoration-line-through">
                                                {{ formatHolidayDate(item.date) }}
                                            </span>
                                            <span v-else>
                                                {{ formatHolidayDate(item.date) }}
                                            </span>

                                        </v-chip>
                                    </template>

                                    <template v-slot:[`item.holiday`]="{ item }">
                                        <span v-if="formatCompareDate(new Date()) > item.date"
                                            class="text-decoration-line-through">
                                            {{ item.holiday }}
                                        </span>
                                        <span v-else>
                                            {{ item.holiday }}
                                        </span>
                                    </template>

                                    <template v-slot:[`item.actions`]="{ item }">
                                        <v-icon small class="mr-2" @click="editHoliday(item)">mdi-pencil</v-icon>
                                        <v-icon small class="mr-2" @click="deleteHoliday(item.id)">mdi-delete</v-icon>
                                    </template>
                                </v-data-table>
                            </v-col>
                            <v-col cols="12" sm="12" md="4">
                                <v-date-picker v-model="holidayDates" multiple readonly color="red"></v-date-picker>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-card>
        </v-dialog>

        <!-- --------Absent-Insert-Form------ -->
        <v-dialog v-model="holidayForm" width="500" persistent overlay-opacity="0">
            <v-card>
                <v-toolbar dense flat color="lighten-1" class="user-form-dialog khawin-background-color">
                    <span v-if="editMode === false" class="white--text">
                        <v-icon left color="white">mdi-calendar-month</v-icon>
                        <!-- {{ formTitle }} -->
                        {{ $t('holiday.frmtitleAdd') }}
                    </span>
                    <span v-else class="white--text">
                        <v-icon left dark>mdi-calendar-month</v-icon> {{ $t('holiday.frmtitleEdit') }}
                    </span>
                </v-toolbar>

                <form @submit.prevent="editMode ? updateHoliday() : createHoliday()">
                    <v-card-text>
                        <v-menu v-model="holidayDateChoose" :close-on-content-click="false" max-width="290">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field class="khmer-font" :value="computedDateFormattedMomentjs"
                                    v-bind:label="$t('holiday.txtHolidayDate')" color="cyan darken-1"
                                    prepend-inner-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" outlined
                                    :error-messages="errorsMessage.date"></v-text-field>
                            </template>
                            <v-date-picker v-model="form.date" @input="holidayDateChoose = false"></v-date-picker>
                        </v-menu>

                        <v-textarea v-model="form.holiday" outlined color="cyan darken-1" class="khmer-font"
                            v-bind:label="$t('holiday.txtHolidayDetail')" rows="3"
                            prepend-inner-icon="mdi-calendar-text" :error-messages="errorsMessage.holiday"></v-textarea>
                    </v-card-text>

                    <v-card-actions class="card-action">
                        <v-spacer></v-spacer>
                        <v-btn small color="grey lighten-2" depressed @click="closeDialog">
                            {{ $t('holiday.btnCancel') }}
                        </v-btn>
                        <v-btn class="khawin-background-color" dark small depressed type="submit"
                            :loading="btnSaveLoading">
                            {{ $t('holiday.btnSave') }}
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
                    {{ $t('holiday.btnCancel') }}
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
                            {{$t('absent.deleteMessage')}}
                        </div>

                        <v-btn :disabled="btnLoading" class="ma-1" depressed small @click="dialogDelete = false">
                            {{ $t('holiday.btnCancel') }}
                        </v-btn>

                        <v-btn :loading="btnLoading" class="ma-1" dark color="red" small depressed
                            @click="submitDelete">
                            {{$t('employee.btnDelete')}}
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

    <style scoped>
    .v-text-field--outlined>>>fieldset {
        border-color: rgba(177, 177, 177, 0.644);
    }
    .v-menu__content{
        background-attachment: red !important;
    }
</style>

<script>
    import AOS from "aos";
    import "aos/dist/aos.css";
    import moment from "moment";
    import store from "../store";
AOS.init();
export default {
    data() {
        return {
            dialogTest: true,
            tree: [],
            initiallyOpen: ['public'],
            mini: false,
            selectedItem: 1,
            drawer: null,
            btnLoading: false,
            cardLoading: false,
            showPassword: false,
            email: "",
            password: "",
            errorsMessage: {
                email: "",
                password: "",
            },
            authData: "",
            authName: "",

            editMode: false,
            holidayForm: false,
            btnSaveLoading: false,
            calendarDialog: false,
            holidayDateChoose: false,
            snackbar: false,
            alertSnackbarMsg: "",
            dialogDelete: false,
            form: new Form({
                id: "",
                date: "",
                holiday: "",
            }),
            holidayData: [],
            holidayDates: [],
            holidayDatesCount: "",

            // -------------logout------------
            events: ['click', 'mousemove', 'mousedown', 'scroll', 'keypress', 'load'],
            warningTimer: null,
            logoutTimer: null,
            navigation: {
                width: 230,
                borderSize: 3
            }
        };
    },

    computed: {
        loggedIn() {
            return store.getters.loggedIn;
        },

        user() {

            return store.state.user;
        },

        employee() {
            if(store.state.user.employee.pic === undefined) {
                store.state.user.employee.pic = "default.png";
                store.state.user.employee.profile_color = "3a9dca";
            }
            return store.state.user.employee;
        },

        formTitle() {
            return this.editMode === false ? "Add Holiday" : "Edit Holiday";
        },

        computedDateFormattedMomentjs() {
            return this.form.date
                ? moment(this.form.date).format("dddd, DD/ MM/ YYYY")
                : "";
        },

        headers() {
            return [
                {
                    text: '#',
                    align: 'start',
                    value: 'id'
                },
                { text: this.$t('holiday.tbDay'), value: 'day' },
                { text: this.$t('holiday.tbDate'), value: 'date' },
                { text: this.$t('holiday.tbHoliday'), value: 'holiday' },
                { text: this.$t('holiday.tbAction'), sortable: false, value: "actions" },
            ]
        },

        menu(){
            return store.state.user;
        }
    },

    mounted() {
        if (localStorage.Lang != null) this.$i18n.locale = localStorage.Lang;

        // ====logout=======
        this.events.forEach(function(event){
            window.addEventListener(event, this.resetTime);
        }, this)
        if(this.user.token != null) {
            this.withDirectives();
            this.setEvents();
        } else {
            this.$root.$on('mainAppComponent', () => {
                this.withDirectives();
                this.setEvents();
            });
        }
    },

    methods: {
        langChanged(lang) {
            this.$i18n.locale = lang;
            localStorage.Lang = lang
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

        formatDate(value) {
            return moment(value).format("dddd, DD/MM/YYYY");
        },

        chooseHolidayYear() {
            console.log('hello')
        },

        logout() {
            this.$store.dispatch("logout").then(() => {
                this.$router.push({ name: "login" });
            });
            this.$root.$on('mainAppComponent', () => {
                this.withDirectives();
                this.setEvents();
            });
        },

        // =====================HOLIDAY=======================
        formatHolidayDate(value) {
            return moment(value).format("DD/MM/YYYY");
        },

        formatCompareDate(value) {
            return moment(value).format("YYYY-MM-DD");
        },

        getColor(day) {
            if (day == "Monday") return "orange darken-2";
            else if (day == "Tuesday") return "purple";
            else if (day == "Wednesday") return "light-green";
            else if (day == "Thursday") return "green";
            else if (day == "Friday") return "blue";
            else if (day == "Saturday") return "pink darken-2";
            else if (day == "Sunday") return "red";
        },

        getDataHoliday() {
            var token = sessionStorage.getItem("TOKEN");
            if (token) {
                console.log('logged in');
            }
        },

        ReadHoliday() {
            axios
                .get(this.$globalConfig.ipHost + "api/read-holiday", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.holidayData = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        ReadHolidayDates() {
            axios
                .get(this.$globalConfig.ipHost + "api/read-holiday-date", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.holidayDates = response.data;
                    this.holidayDatesCount.length;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        openDialog() {
            this.ReadHoliday();
            this.ReadHolidayDates();
            this.calendarDialog = true;
        },

        openDialogInsertForm() {
            this.holidayForm = true;
        },

        closeDialog() {
            this.editMode = false;
            this.holidayForm = false;
            this.form.date = "";
            this.form.holiday = "";
        },

        createHoliday() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            this.form
                .post("api/create-holiday", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadHoliday();
                    this.ReadHolidayDates();
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

        editHoliday(holiday) {
            this.editMode = true;
            this.form.id = holiday.id;
            this.form.day = holiday.day;
            this.form.date = holiday.date;
            this.form.holiday = holiday.holiday;
            this.holidayForm = true;
        },

        async updateHoliday() {
            this.btnSaveLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            this.form
                .post("/api/update-holiday/" + this.form.id, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadHoliday();
                    this.ReadHolidayDates();
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

        deleteHoliday(holiday_id) {
            this.form.id = holiday_id;
            this.dialogDelete = true;
        },

        async submitDelete() {
            this.btnLoading = true;
            this.tableLoading = true;
            await new Promise((resolve) => setTimeout(resolve, 1000));
            axios
                .delete("/api/delete-holiday/" + this.form.id, {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
                    },
                })
                .then((response) => {
                    this.ReadHoliday();
                    this.ReadHolidayDates();
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

        // ========logout========
        setTimers: function(){
            this.warningTimer = setTimeout(this.warningMessage, 30 * 60 * 1000);
        },
        warningMessage: function(){
            if(sessionStorage.getItem('TOKEN') !== null){
                this.logout();
            }
        },

        resetTime: function() {
            clearTimeout(this.warningTimer);
            this.setTimers();
        },

        withDirectives() {
            if(this.$refs.drawer !== undefined) {
                let i = this.$refs.drawer.$el.querySelector(
                ".v-navigation-drawer__border"
                );
                i.style.width = this.navigation.borderSize + "px";
                i.style.cursor = "ew-resize";
            }
        },

        setEvents() {
            if(this.$refs.drawer !== undefined) {
                const minSize = 3;
                const el = this.$refs.drawer.$el;
                const drawerBorder = el.querySelector(".v-navigation-drawer__border");
                const vm = this;
                var direction = el.classList.contains("v-navigation-drawer--right") ? "right" : "left";
                function resize(e) {
                    document.body.style.cursor = "ew-resize";
                    let f = direction === "right" ? document.body.scrollWidth - e.clientX : e.clientX;
                    el.style.width = f + "px";
                }

                drawerBorder.addEventListener(
                    "mousedown",
                    (e) => {
                    if (e.offsetX < minSize) {
                        el.style.transition = "initial";
                        document.addEventListener("mousemove", resize, false);
                    }
                    },
                    false
                );

                document.addEventListener(
                    "mouseup",
                    (e) => {
                        if(!Boolean(e.target.closest(".v-toolbar__content"))) {
                            el.style.transition = "";
                            var drawerWidth = el.style.width;
                            drawerWidth = drawerWidth.toString().replace("px", "");
                            this.navigation.width = parseInt(drawerWidth);
                            document.body.style.cursor = "";
                            document.removeEventListener("mousemove", resize, false);
                        } 
                    },
                    false
                );
            }
        },
        resizeSideBar() {
            this.mini = !this.mini;
            const el = this.$refs.drawer.$el;
            if(this.mini) {
                el.style.width = "56px";
            } else {
                el.style.width = this.navigation.width + "px";
            }
        }
    },
};
</script>
