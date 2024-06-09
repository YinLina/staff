import Vue from "vue";
import Vuex from 'Vuex';
Vue.use(Vuex)

export default new Vuex.Store({
    state(){
        return{
            user:{
                data: JSON.parse(localStorage.getItem('USER')) || null,
                token: sessionStorage.getItem('TOKEN') || null,
                menu: JSON.parse(localStorage.getItem('MENU')) || null,
                credentials: null,
                // =========== Theara #CMS-43 Remember Password=================
                password:null,
                user: null,
                remember: false,
                employee: JSON.parse(localStorage.getItem('EMPLOYEE')) || null,
            }
        }
    },

    getters: {
        loggedIn(state){
            return state.user.token != null;
        },
    },

    mutations: {
        setCredentials(state, message){
            state.user.credentials = message
        },

        retrieveToken(state, token){
            state.token = token
        },

        setAuth(state, data){
            state.auth = data
        },

        deleteAuth(state){
            state.auth = null
        },

        destroyToken(state){
            state.token = null
        },

        // ----------------------------
        retrieveMenus(state, data){
            state.menus = data
        },

        // ========================
        setUser(state, userData){
            state.user.data = userData.user;
            state.user.token = userData.token;
            state.user.menu = userData.menu;
            state.user.employee = userData.employee;
            sessionStorage.setItem('TOKEN', userData.token);
            localStorage.setItem('USER', JSON.stringify(userData.user));
            localStorage.setItem('MENU', JSON.stringify(userData.menu));
            localStorage.setItem('EMPLOYEE', JSON.stringify(userData.employee));
        },

        updateAuth(state, user){
            state.user.data = user.name;
        }
    },

    actions: {
        updateProfifle(context, data){
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + sessionStorage.getItem("TOKEN")
            return new Promise((resolve, reject) => {
                axios.post('api/update-profile/' + data.id, {
                    name: data.name,
                    email: data.email,
                    profile: data.profile,
                })
                .then(response => {
                    const user = response.data.user;
                    localStorage.setItem("auth", JSON.stringify(user));
                    context.commit('updateAuth', user)
                    resolve(response)
                })
                .catch(error => {
                    reject(error)
                })
            })
        },

        token(context, credentials){
            return new Promise(( resolve, reject ) => {
                axios.post('api/login', {
                    email: credentials.email,
                    password: credentials.password
                })
                .then(response => {

                    const token = response.data.token;
                    const user = response.data.user;

                    sessionStorage.setItem('access_token', token)
                    context.commit('retrieveMenus', response.data.menu)

                    context.commit('retrieveToken', token)
                    localStorage.setItem("auth", JSON.stringify(user));
                    context.commit('setAuth', user)

                    resolve(response)
                })
                .catch(error => {
                    context.commit('setCredentials', error.response.data.message);
                    reject(error)
                })
            })
        },

        // =====================================
        login({ commit }, user){
            this.state.user.credentials = null;
            return new Promise(( resolve, reject ) => {
                axios.post('api/login', {
                    //email: user.email,
                    userId: user.userId,
                    password: user.password,
                    // =========== Theara #CMS-43 Remember Password=================
                    isRememberPass: user.rememberPassword,
                })
                .then(response => {
                    localStorage.setItem('LOGININFOID', JSON.stringify(response.data.getInfoLoginId));
                    commit('setUser', response.data);
                    resolve(response);
                })
                .catch(error => {
                    if(error){
                        this.state.user.credentials = 'Credentials not match.'
                    }
                    reject(error);
                })
            })
        },

        logout({ commit }){
            return new Promise((resolve, reject) => {
                axios.post('/api/logout', sessionStorage.getItem('TOKEN') ,{
                    headers: { "Authorization" : 'Bearer ' + sessionStorage.getItem('TOKEN')
                }})
                .then(response => {
                    commit('setUser', {
                        user: {},
                        token: null,
                        menu: null,
                    });
                    // =========== Theara #CMS-43 Remember Password=================
                    if(response.data.Key.length > 0) {
                        this.state.user.password = response.data.Key[0]['Password']
                        this.state.user.user = response.data.Key[0]['UserID']
                        this.state.user.remember = response.data.Key[0]['Remember']
                    }else{
                        this.state.user.password = ''
                        this.state.user.user = ''
                        this.state.user.remember = ''
                    }
                    
                    sessionStorage.removeItem('TOKEN');
                    localStorage.removeItem('USER');
                    localStorage.removeItem('MENU');
                    localStorage.removeItem('EMPLOYEE');
                    this.state.user.credentials = null;

                    resolve(response)
                })
                .catch(error => {
                    reject(error)
                })
            })
        },

        destroyToken(context){
            if(context.getters.loggedIn){
                return new Promise((resolve, reject) => {
                    axios.post('/api/logout', sessionStorage.getItem('TOKEN') ,{
                        headers: { "Authorization" : 'Bearer ' + sessionStorage.getItem('TOKEN')
                    }})
                    .then(response => {

                        sessionStorage.removeItem('TOKEN');
                        localStorage.removeItem('auth');
                        localStorage.removeItem('vuex');

                        context.commit('destroyToken');
                        context.commit('deleteAuth');
                        context.commit('setCredentials', null);
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
                })
            }
        }
    }
});
