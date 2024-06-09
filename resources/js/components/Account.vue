<template>
  <div id="account">
    <v-tabs v-model="tab" color="cyan darken-1">
      <v-tabs-slider color="cyan darken-1"></v-tabs-slider>
      <!-- <v-tab key="username" class="font-weight-medium text-capitalize"
        ><v-icon>mdi-account</v-icon> {{ $t('account.tabAccount') }}</v-tab
      > -->
      <v-tab key="password" class="font-weight-medium text-capitalize"
        ><v-icon>mdi-lock</v-icon> {{ $t('account.tabPassword') }}</v-tab
      >
    </v-tabs>

    <v-tabs-items v-model="tab">
      <!-- <v-tab-item key="account">
        <v-card
          id="account-setting"
          class="mx-auto ma-6"
          max-width="350"
          outlined
        >
          <v-progress-linear
            :active="loading"
            :indeterminate="loading"
            absolute
            top
            color="cyan darken-1"
          ></v-progress-linear>

          <v-form
            @submit.prevent="UpdateProfile()"
            enctype="multipart/form-data"
          >
            <div class="d-flex justify-center mt-6 profile-img">
              <div>
                <v-avatar size="70" class="ma-1">
                  <v-img
                    v-if="preview_profile"
                    :src="preview_profile"
                    class="img-fluid rounded-sm"
                  ></v-img>
                  <v-img
                    v-else-if="authProfile == 'default.png'"
                    :src="'/image/default.png'"
                  />
                  <v-img v-else :src="'/profiles/' + authProfile" />
                </v-avatar>
                <v-file-input
                  v-model="form.profile"
                  hide-input
                  hide-details
                  @change="onFileChange"
                  accept="image/png, image/jpeg, image/bmp"
                  prepend-icon="mdi-camera"
                ></v-file-input>
              </div>
            </div>

            <v-card-text>
              <v-text-field
                v-model="form.name"
                color="cyan darken-1"
                v-bind:label="$t('account.txtName')"
                class="khmer-font"
                outlined
                prepend-inner-icon="mdi-account"
                :error-messages="errorsMessage.name"
              ></v-text-field>

              <v-text-field
                v-model="form.email"
                color="cyan darken-1"
                v-bind:label="$t('account.txtEmail')"
                class="khmer-font"
                outlined
                prepend-inner-icon="mdi-email"
                :error-messages="errorsMessage.email"
              ></v-text-field>

              <v-btn
                depressed
                small
                block
                color="cyan darken-1"
                type="submit"
                :loading="btnLoading"
                dark
                >{{ $t('account.btnSave') }}</v-btn
              >
            </v-card-text>
          </v-form>
        </v-card>
      </v-tab-item> -->

      <v-tab-item key="password">
        <v-card class="mx-auto ma-6" max-width="350" outlined>
          <v-progress-linear
            :active="loading"
            :indeterminate="loading"
            absolute
            top
            color="cyan darken-1"
          ></v-progress-linear>

          <v-form
            @submit.prevent="PasswordUpdate()"
            enctype="multipart/form-data"
          >
            <v-card-title>{{ $t('account.Password') }}</v-card-title>
            <v-card-text>
              <v-alert
                v-if="msgResponse"
                text
                prominent
                type="error"
                icon="mdi-alert-circle"
                class="pa-2"
              >
                <h5>{{ msgResponse }}</h5>
              </v-alert>

              <v-text-field
                v-model="formPassword.old_password"
                color="cyan darken-1"
                :append-icon="password ? 'mdi-eye' : 'mdi-eye-off'"
                :type="password ? 'text' : 'password'"
                v-bind:label="$t('account.txtPassword')"
                class="khmer-font"
                @click:append="password = !password"
                :error-messages="errorsMessage.old_password"
              ></v-text-field>

              <v-text-field
                v-model="formPassword.password"
                color="cyan darken-1"
                :append-icon="confirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="confirmPassword ? 'text' : 'password'"
                v-bind:label="$t('account.txtNewPassword')"
                class="khmer-font"
                @click:append="confirmPassword = !confirmPassword"
                :error-messages="errorsMessage.password"
              ></v-text-field>

              <v-text-field
                v-model="formPassword.password_confirmation"
                color="cyan darken-1"
                :append-icon="newPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="newPassword ? 'text' : 'password'"
                v-bind:label="$t('account.txtConfirPassword')"
                class="khmer-font"
                @click:append="newPassword = !newPassword"
                :error-messages="errorsMessage.password_confirmation"
              ></v-text-field>

              <v-btn
                depressed
                small
                block
                color="cyan darken-1"
                type="submit"
                :loading="btnLoading"
                dark
                >{{ $t('account.btnSave') }}</v-btn
              >
            </v-card-text>
          </v-form>
        </v-card>
      </v-tab-item>
    </v-tabs-items>

    <!-- ---------------Snacbar--------------- -->
    <v-snackbar v-model="snackbar" color="cyan darken-2" dark top right>
      {{ alertSnackbarMsg }}
      <template v-slot:action="{ attrs }">
        <v-btn dark text v-bind="attrs" @click="snackbar = false" small>
          {{ $t('account.msgClose') }}
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import store from '../store';

export default {
  data() {
    return {
      tab: null,
      snackbar: false,
      alertSnackbarMsg: "",
      loading: false,
      btnLoading: false,
      password: false,
      confirmPassword: false,
      newPassword: false,
      preview_profile: null,

      authProfile: null,

      errorsMessage: "",
      msgResponse: "",

      form: new Form({
        name: "",
        email: "",
        profile: null,
      }),

      formPassword: new Form({
        old_password: "",
        password: "",
        password_confirmation: "",
      }),
    };
  },

  computed: {

    userToken() {
        return store.state.user;
    },

    authData() {
      return JSON.parse(localStorage.getItem("auth"));
    },

    user(){
        return store.state.user;
    },
  },

  mounted() {
    if (this.user.data) {
        this.form.name = this.user.data.name;
        this.form.email = this.user.data.email;
        this.authProfile = this.user.data.profile;
    }
  },

  methods: {
    // ---------------------------------
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
    UpdateProfile() {
      this.btnLoading = true;
      this.loading = true;
      setTimeout(
        () =>
          this.$store
            .dispatch("updateProfifle", {
              id: this.userToken.data.id,
              name: this.form.name,
              email: this.form.email,
              profile: this.preview_profile,
            })
            .then((response) => {
              if (response.status == 200) {
                this.btnLoading = false;
                this.loading = false;
                // this.alertSnackbarMsg = response.data.message;
                this.alertSnackbarMsg = this.$t('account.updateMsg');
                this.snackbar = true;

                if (this.user.data.email !== response.data.user.email) {
                  this.alertSnackbarMsg =
                    "Email has been update please login again.";
                  this.snackbar = true;
                  this.$store.dispatch("logout").then(() => {
                    this.$router.push({ name: "login" });
                    });
                }
              }
            })
            .catch((errors) => {
              this.errorsMessage = errors.response.data.errors;
              this.btnLoading = false;
              this.loading = false;
            }),
        3000
      );
    },

    PasswordUpdate() {
      this.btnLoading = true;
      this.loading = true;
      this.formPassword
        .post("api/update-password/" + this.userToken.data.id, {
          headers: {
            Authorization: "Bearer " + sessionStorage.getItem("TOKEN"),
          },
        })
        .then((response) => {
          if (response.data.message !== "Password update Success") {
            this.msgResponse = response.data.message;
          } else {
            this.alertSnackbarMsg = this.$t('account.passUpdateMsg');
            this.snackbar = true;
            this.$store.dispatch("logout").then(() => {
              this.$router.push({ name: "login" });
            });
          }

          this.btnLoading = false;
          this.loading = false;
        })
        .catch((errors) => {
          this.errorsMessage = errors.response.data.errors;
          this.btnLoading = false;
          this.loading = false;
        });
    },
  },
};
</script>
