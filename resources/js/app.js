window.Vue = require('vue').default;
window.axios = require('axios');
var engFile = require('../js/translate/english');
var khFile = require('../js/translate/khmer');
var globalConfigFile = require('../js/globalconfig');
import Vue from 'vue';
import store from './store';
import { helper } from './helper';

Vue.config.productionTip = false;
import Vuetify from '../plugins/vuetify';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import 'viewerjs/dist/viewer.css';
import Viewer from 'v-viewer';
Vue.use(Viewer)

import VueMask from 'v-mask'
Vue.use(VueMask);

import mixins from 'vuetify-multiple-draggable-dialogs';
Vue.mixin(mixins);

import VueHtml2pdf from 'vue-html2pdf';
Vue.use(VueHtml2pdf)

import "chart.js";
import "hchs-vue-charts";
Vue.use(window.VueCharts);

import VueI18n from 'vue-i18n';
Vue.use(VueI18n)

import { Form } from 'vform';
window.Form = Form;

import Login from './components/auth/Login';
import ForgotPassword from './components/ForgotPassword';
import ResetPassword from './components/auth/ResetPassword'
import Dashboard from './components/Dashboard';
import User from './components/User';
import Position from './components/Position';
import Employee from './components/Employee';
import Absent from './components/Absent';
import Report from './components/Report';
import Account from './components/Account';
import Menu from './components/RoleMenu';
import PageNotFound from './components/PageNotFound';
import Inventory from './components/Inventory';
import AddStock from './components/AddStock';
import IssueItem from './components/IssueItem';
import ReturnItem from './components/ReturnItem';
import ResignationReason from './components/ResignationReason';
import LeaveTypeSetting from './components/LeaveTypeSetting';
import HumanResourceSetting from './components/HumanResourceSetting';
import BranchSetting from './components/BranchSetting';
import DepartmentSetting from './components/DepartmentSetting';
import LeaveReasonSetting from'./components/LeaveReasonSetting';
import AddItem from './components/AddItem';
import AssetCategory from './components/AssetCategory';
import ItemSupplier from './components/ItemSupplier';
import ItemStore from './components/ItemStore';
import InventorySetting from './components/InventorySetting';
import HumanResource from './components/HumanResource';
import StaffAttendance from'./components/StaffAttendance';
import PayRoll from'./components/PayRoll';
import ApplyLeave from'./components/ApplyLeave';
import ApproveStaffLeave from'./components/ApproveStaffLeave';
import StaffRating from'./components/StaffRating';
import StaffResignation from'./components/StaffResignation';
import StaffInformation from './components/Employee';
import SystemSetting from './components/SystemSetting';
import AccountSystemSetting from './components/AccountSystemSetting';
import UserSystemSetting from './components/UserSystemSetting';
import RoleSystemSetting from './components/RoleSystemSetting';
import Parameter from'./components/Parameter';
import WorkFlow from'./components/WorkFlow';
import MenuSystem from './components/MenuSystem';
import Shift from './components/Shift';
import Validation from './components/Validation';
import LeaveRequest from './components/LeaveRequest';
import Staff from './components/Staff';
import Attendance from './components/Attendance';
import LeaveEntry from './components/LeaveEntry';
import CalculatePayroll from './components/CalculatePayroll';
import BasicSalary from './components/BasicSalary';
import Holiday from './components/Holiday';
import StaffPosition from './components/StaffPosition';
import DocumentForm from './components/DocumentForm';
import Document from './components/Document';
import SystemLog from './components/SystemLog';
import StaffShift from './components/StaffShift';

let routes = [
    {
        path:'/ReturnItem',
        name:'ReturnItem',
        component:ReturnItem,
        meta:{
            tokenRequired:true,
            title: 'ReturnItem'
        }
    }
    ,
    {
        path: '/issue-item',
        name: 'issue-item',
        component: IssueItem,
        meta:{
            tokenRequired: true,
            title: 'IssueItem'
        }
    },

    {
        path: '/inventory',
        name: 'inventory',
        component: Inventory,
        meta: {
            tokenRequired: true,
            title: 'Inventory'
        }
    },
    {
        path: '/AddStock',
        name: ' AddStock',
        component: AddStock,
        meta: {
            tokenRequired:true,
            title: 'AddStock'
        }
    },
    {
        path: '/login',
        name:'login',
        component: Login,
        meta: {
            requiresVisitor: true,
            title: 'Login'
        }
    },
    {
        path:'/humanResource',
        name:'HumanResource',
        component: HumanResource,
        meta:{
            tokenRequired:true,
            title: 'HumanResource'
        }
    },
    {
        path:'/validation',
        name:'validation',
        component: Validation,
        meta:{
            tokenRequired:true,
            title: 'Validation'
        }
    },
    {
        path:'/leaveRequest',
        name:'leaveRequest',
        component: LeaveRequest,
        meta:{
            tokenRequired:true,
            title: 'LeaveRequest'
        }
    },
    {
        path: '/StaffAttendance',
        name: 'StaffAttendance',
        component: Absent,
        meta: {
            tokenRequired: true,
            title: 'StaffAttendance'
        }
    },
    {
        path: '/PayRoll',
        name: 'PayRoll',
        component: PayRoll,
        meta: {
            tokenRequired:true,
            title: 'PayRoll'
        }
    },
    {
        path: '/ApplyLeave',
        name: 'ApplyLeave',
        component: ApplyLeave,
        meta: {
            tokenRequired:true,
            title: 'ApplyLeave',
        }
    },
    {
        path: '/ApproveStaffLeave',
        name: 'ApproveStaffLeave',
        component: ApproveStaffLeave,
        meta: {
            tokenRequired:true,
            title: 'ApproveStaffLeave',
        }
    },
    {
        path: '/Staff',
        name: 'Staff',
        component: Staff,
        meta: {
            tokenRequired:true,
            title: 'Staff',
        }
    },
    {
        path: '/Attendance',
        name: 'Attendance',
        component: Attendance,
        meta: {
            tokenRequired:true,
            title: 'Attendance',
        }
    },
    {
        path: '/LeaveEntry',
        name: 'LeaveEntry',
        component: LeaveEntry,
        meta: {
            tokenRequired:true,
            title: 'LeaveEntry',
        }
    },
    {
        path: '/CalculatePayroll',
        name: 'CalculatePayroll',
        component: CalculatePayroll,
        meta: {
            tokenRequired:true,
            title: 'CalculatePayroll',
        }
    },
    {
        path: '/BasicSalary',
        name: 'BasicSalary',
        component: BasicSalary,
        meta: {
            tokenRequired:true,
            title: 'BasicSalary',
        }
    },
    {
        path: '/StaffRating',
        name: 'StaffRating',
        component: StaffRating,
        meta: {
            tokenRequired:true,
            title: 'StaffRating',
        }
    },
    {
        path: '/StaffResignation',
        name: 'StaffResignation',
        component: StaffResignation,
        meta: {
            tokenRequired:true,
            title: 'StaffResignation',
        }
    },
    {
        path: '/forgotPassword',
        name:'forgotPassword',
        component: ForgotPassword,
        meta: {
            requiresVisitor: true,
            title: 'Forgot-Password'
        }
    },
    {
        path: '/reset-password',
        name:'reset-password',
        component: ResetPassword,
        meta: {
            requiresVisitor: true,
            title: 'Reset-Password'
        }
    },
    {
        path: '/',
        name:'dashboard',
        component: Dashboard,
        meta: {
            tokenRequired: true,
            title: 'Dashboard',
        }
    },
    {
        path: '/user',
        name:'user',
        component: User,
        meta: {
            tokenRequired: true,
            title: 'User'
        }
    },
    {
        path: '/position',
        name:'position',
        component: Position,
        meta: {
            tokenRequired: true,
            title: 'Position'
        }
    },
    {
        path: '/employee',
        name:'employee',
        component: Employee,
        meta: {
            tokenRequired: true,
            title: 'Employee'
        }
    },
    {
        path: '/absent',
        name:'absent',
        component: Absent,
        meta: {
            tokenRequired: true,
            title: 'Absent'
        }
    },
    ,
    {
        path: '/report',
        name:'report',
        component: Report,
        meta: {
            tokenRequired: true,
            title: 'Report'
        }
    },
    {
        path: '/account',
        name:'account',
        component: Account,
        meta: {
            tokenRequired: true,
            title: 'Account'
        }
    },
    {
        path: '/role-menu',
        name:'role',
        component: Menu,
        meta: {
            tokenRequired: true,
            title: 'Menu'
        }
    },
    {
        path: '/PageNotFound',
        name: 'PageNotFound',
        component: PageNotFound,
        meta: {
            title: 'Page Not Found',
        }
    },
    {
        path: '/humanResourceSetting',
        name:'humanResourceSetting',
        component: HumanResourceSetting,
        meta: {
            tokenRequired: true,
            title: 'Human Resource'
        }
    },
    {
        path: '/branchSetting',
        name: 'branchSetting',
        component: BranchSetting,
        meta: {
            tokenRequired: true,
            title: 'Branch'
        }
    },
    {
        path: '/departmentSetting',
        name: 'departmentSetting',
        component: DepartmentSetting,
        meta: {
            tokenRequired: true,
            title: 'Department'
        }
    },
    {
        path: '/resignationSetting',
        name: 'resignationSetting',
        component: ResignationReason,
        meta: {
            tokenRequired: true,
            title: 'Resignation '
        }
    },
    {
        path: '/leaveTypeSetting',
        name: 'leaveTypeSetting',
        component: LeaveTypeSetting,
        meta: {
            tokenRequired: true,
            title: 'LeaveType'
        }
    },
    {
        path: '/leaveReason',
        name: 'leaveReason',
        component: LeaveReasonSetting,
        meta: {
            tokenRequired: true,
            title: 'LeaveReason'
        }
    },
    {
        path: '/inventorySetting',
        name: 'inventorySetting',
        component: InventorySetting,
        meta: {
            tokenRequired: true,
            title: 'Inventory'
        }
    },
    {
        path: '/addItem',
        name: 'addItem',
        component: AddItem,
        meta: {
            tokenRequired: true,
            title: 'AddItem'
        }
    },
    {
        path: '/AssetCategory',
        name: 'AssetCategory',
        component: AssetCategory,
        meta: {
            tokenRequired: true,
            title: 'Asset Category'
        }
    },
    {
        path: '/itemSupplier',
        name: 'itemSupplier',
        component: ItemSupplier,
        meta: {
            tokenRequired: true,
            title: 'ItemSupplier'
        }
    },
    {
        path: '/itemStore',
        name: 'itemStore',
        component: ItemStore,
        meta: {
            tokenRequired: true,
            title: 'ItemStore'
        }
    },
    {
        path: '/StaffInformation',
        name: 'StaffInformation',
        component: StaffInformation,
        meta: {
            tokenRequired: true,
            title: 'StaffInformation'
        }
    },
    {
        path: '/systemSetting',
        name:'systemSetting',
        component: SystemSetting,
        meta: {
            tokenRequired: true,
            title: 'System Setting'
        }
    },
    {
        path: '/accountSystemSetting',
        name:'accountSystemSetting',
        component: AccountSystemSetting,
        meta: {
            tokenRequired: true,
            title: 'Account'
        }
    },
    {
        path: '/userSystemSetting',
        name:'userSystemSetting',
        component: UserSystemSetting,
        meta: {
            tokenRequired: true,
            title: 'User'
        }
    },
    {
        path: '/roleSystemSetting',
        name:'roleSystemSetting',
        component: RoleSystemSetting,
        meta: {
            tokenRequired: true,
            title: 'Role'
        }
    },
    {
        path: '/parameter',
        name:'parameter',
        component: Parameter,
        meta: {
            tokenRequired: true,
            title: 'Parameter'
        }
    },
    {
        path: '/WorkFlow',
        name:'WorkFlow',
        component:WorkFlow,
        meta: {
            tokenRequired: true,
            title: 'WorkFlow'
        }
    },
    {
        path: '/menuSystemSetting',
        name: 'menuSystemSetting',
        component: MenuSystem,
        meta: {
            tokenRequired: true,
            title: 'Menu'
        }
    },
    {
        path: '/shift',
        name: 'shift',
        component: Shift,
        meta: {
            tokenRequired: true,
            title: 'Shift'
        }
    },
    //modify by Huychoung 11/07/2023 #58
    {
        path: '/Holiday',
        name: 'Holiday',
        component: Holiday,
        meta: {
            tokenRequired: true,
            title: 'Holiday'
        }
    },
    //modify by Huychoung 26/07/2023 #81
    {
        path: '/StaffPosition',
        name: 'StaffPosition',
        component: StaffPosition,
        meta: {
            tokenRequired: true,
            title: 'StaffPosition'
        }
    },
    //modify by LINA 24/08/2023 #91
    {
        path: '/SystemLog',
        name: 'SystemLog',
        component: SystemLog,
        meta: {
            tokenRequired: true,
            title: 'System Log'
        }
    },
    {
        path: '/DocumentForm',
        name: 'DocumentForm',
        component: DocumentForm,
        meta: {
            tokenRequired: true,
            title: 'DocumentForm'
        }
    },
    {
        path: '/Document',
        name: 'Document',
        component: Document,
        meta: {
            tokenRequired: true,
            title: 'Document'
        }
    },
    {
        path: '/StaffShift',
        name: 'StaffShift',
        component: StaffShift,
        meta: {
            tokenRequired: true,
            title: 'Staff Shift'
        }
    },
];

const router = new VueRouter({
    // mode: 'history',
    routes
});

router.afterEach((to, from) => {
    Vue.nextTick(() => {
        document.title = to.meta.title;
    });
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.tokenRequired)) {
      if (!store.getters.loggedIn) {
        next({ name: 'login'})
      } else {
        next()
      }
    } if (to.matched.some(record => record.meta.requiresVisitor)) {
        if (store.getters.loggedIn) {
          next({ name: 'dashboard'})
        } else {
          next()
        }
      }else {
      next()
    }

    if (!to.matched.length) {
        next('/PageNotFound');
    } else {
        next();
    }
})


const messages = {
    // ============English==============
    english: engFile.eng,
    // ============Khmer==============
    khmer: khFile.kh,
};

const i18n = new VueI18n({
    locale: 'english',
    messages
});

Vue.prototype.$globalConfig = globalConfigFile;
Vue.prototype.$helper = helper;

Vue.component('main-component', require('./components/MainApp.vue').default);

const app = new Vue({
    el: '#app',
    store,
    vuetify: Vuetify,
    router,
    i18n,
});
