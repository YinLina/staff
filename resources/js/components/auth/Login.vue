<template>
  <v-row align="center" justify="center">
    <v-col cols="12" md="6">
      <v-card class="pa-5 card-background">
        <v-card height="auto" class="mt-3">
          <v-progress-linear
            absolute
            top
            color="blue"
            :active="cardLoading"
            :indeterminate="cardLoading"
          ></v-progress-linear>

                <v-row>
                    <v-col
                        cols="12"
                        sm="12"
                        md="5"
                        class="d-flex justify-center align-center d-sm-none d-md-flex login-logo"
                        data-aos="fade-right"
                    >
                    <v-img
                        class="login-logo"
                        max-height="300"
                        max-width="100%"
                        :src="'/image/41291-human-resources-approval-animation.gif'"
                    ></v-img>
                    </v-col>
                    <v-col
                        cols="12"
                        sm="12"
                        md="7"
                        class="pa-0 frm-login"
                        data-aos="fade-up"
                        data-aos-anchor-placement="top-bottom"
                    >
                    <form  @submit.prevent="login()">
                        <v-card elevation="0" class="ma-5">
                            <span class="title blue--text khmer-font">{{ $t('login.title') }}</span>
                            <v-card-text class="mt-2 pb-0">
                            <p v-if="!this.$store.state.user.credentials" class="khmer-font subtitle-1">{{ $t('login.description') }}</p>

                            <v-alert
                                v-if="this.$store.state.user.credentials"
                                data-aos="zoom-in"
                                text
                                prominent
                                type="error"
                                icon="mdi-alert-circle"
                                class="pa-0"
                            >
                                <!-- <h5>{{ this.$store.state.user.credentials }}</h5> -->
                                <h5>{{ $t('login.credentials') }}</h5>
                            </v-alert>
                            <v-text-field
                                v-model="user.userId"
                                class="khmer-font"
                                v-bind:label="$t('login.userId')"
                                type="text"
                                outlined
                                prepend-inner-icon="mdi-email"
                                :error-messages="errorsMessage.email"
                                autofocus
                            ></v-text-field>

                            <v-text-field
                                v-model="user.password"
                                class="khmer-font"
                                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                :type="showPassword ? 'text' : 'password'"
                                v-bind:label="$t('login.password')"
                                prepend-inner-icon="mdi-lock"
                                outlined
                                @click:append="showPassword = !showPassword"
                                :error-messages="errorsMessage.password"
                            ></v-text-field>
                            
                            </v-card-text>
                            <v-card-actions class="ml-3">
                                <!-- //========= Theara ======================== -->
                                <v-checkbox
                                    label="Remember Password"
                                    class="font-weight-regular checkbox-color"
                                    color="indigo"
                                    v-model="user.rememberPassword"
                                >
                                </v-checkbox>
                            <v-spacer></v-spacer>
                            <div class="d-flex flex-column justify-end align-end">
                                <v-btn
                                    class="khmer-font"
                                    color="info"
                                    depressed
                                    :loading="btnLoading"
                                    type="submit"
                                >
                                    <v-icon left>mdi-lock</v-icon>
                                    {{ $t('login.login') }}
                                </v-btn>
                                <router-link link to="forgotPassword">
                                    <p class="subtitle-2 blue--text mt-4">{{ $t('login.forgotPass') }}</p>
                                </router-link>
                                <div class="d-flex flex-row">
                                        <v-btn
                                            icon
                                            @click="langChanged('khmer')"
                                        >
                                            <v-img
                                                max-height="25"
                                                max-width="25"
                                                :src="'/image/Flag_of_Cambodia.svg'"
                                            ></v-img>
                                        </v-btn>

                                        <v-btn
                                            icon
                                            @click="langChanged('english')"
                                        >
                                            <v-img
                                                max-height="25"
                                                max-width="25"
                                                :src="'/image/Flag_of_Great_Britain.png'"
                                            ></v-img>
                                        </v-btn>
                                    </div>
                            </div>
                            </v-card-actions>
                        </v-card>
                    </form>
                    </v-col>
                </v-row>
        </v-card>
      </v-card>
    </v-col>
  </v-row>
</template>
<script>
import store from '../../store';

export default {
  name: "login",

  data() {
    return {
        languagesName: '',
        cardLoading: false,
        btnLoading: false,
        showPassword: false,
        user:{
            email: "",
            password: "",
            userId: "",
            //========= Theara ========================
            rememberPassword: false
        },
        errorsMessage: {
            email: "",
            password: "",
            userId: "",
            rememberPassword: false
        },
        userLogin: [],
    };
  },

    mounted(){
        // =========== Theara #CMS-43 Remember Password=================
        var loginInfoID = localStorage.getItem('LOGININFOID');
        if(this.$store.state.user.user && this.$store.state.user.password){
            this.user.userId = this.$store.state.user.user
            this.user.password = this.$store.state.user.password
            this.user.rememberPassword = this.$store.state.user.remember
        }else{
            this.user.userId = this.$store.state.user.user
            this.user.password = this.$store.state.user.password
            this.user.rememberPassword = this.$store.state.user.remember
        }
        this.languagesName = localStorage.getItem('Lang');
        if(loginInfoID != null && loginInfoID != "") {
            this.readRememberPassword(loginInfoID);
        }
    },

    methods: {
        // =========== Theara #CMS-43 Remember Password=================
    readRememberPassword(id){
        axios
        .get(this.$globalConfig.ipHost + "api/get-user-login/" + id, {
            headers: {Authorization: "Bearer " + sessionStorage.getItem("TOKEN")}
        })
        .then((response) => {
            this.userLogin = response.data.userLogin
            for(var i=0; i<this.userLogin.length; i++){
                this.user.userId = this.userLogin[i].UserID;
                this.user.password = this.userLogin[i].Password;
                if(this.userLogin[i].Remember){
                    this.user.rememberPassword = this.userLogin[i].Remember;
                }else{
                    this.user.userId = ''
                    this.user.password = ''
                }
            }
        })
        .catch((error) => {
            console.log(error)
        })
    },
    langChanged(lang){
        this.$i18n.locale = lang;
        localStorage.Lang = lang;
        this.languagesName = lang;
    },
    login() {
        if (this.user.userId.length == 0 || this.user.password.length == 0) {
            this.errorsMessage.email = "The user id field is required.";
            this.errorsMessage.password ="The password field is required.";
        }
        else {
            this.cardLoading = true;
            store.dispatch("login", this.user).then(() => {
                this.btnLoading = true;
                this.$router.push({ name: "dashboard" });
            });
        }
    },
  },
};
</script>
