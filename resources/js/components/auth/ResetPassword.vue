<template>
  <v-row>
    <v-col cols="12" sm="8" md="4" class="ma-auto">
      <v-card>
        <v-progress-linear
          absolute
          top
          color="blue"
          :active="cardLoading"
          :indeterminate="cardLoading"
        ></v-progress-linear>
        <v-card-title>
          <v-icon large left>mdi-lock-reset</v-icon> Reset
          Password</v-card-title
        >
        <v-card-text>
          <v-alert
            v-if="this.responseMsg"
            data-aos="zoom-in"
            text
            prominent
            type="error"
            icon="mdi-alert-circle"
            class="pa-2"
          >
            <h5>{{ this.responseMsg }}</h5>
          </v-alert>

          <!-- ----------success-alert----- -->
          <v-alert dense text type="success" v-if="isResetSuccess">
            <v-row align="center">
              <v-col class="grow"> Password Reset Succeefully </v-col>
              <v-col class="shrink">
                <router-link link to="login">
                  <v-btn small color="teal" depressed>login</v-btn>
                </router-link>
              </v-col>
            </v-row>
          </v-alert>

          <v-text-field
            v-model="email"
            label="Email"
            type="text"
            outlined
            :rules="emailRules"
            prepend-inner-icon="mdi-email"
            :error-messages="errorsMessage.email"
          ></v-text-field>

          <v-text-field
            v-model="password"
            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.min]"
            :type="showPassword ? 'text' : 'password'"
            label="New Password"
            outlined
            hint="At least 6 characters"
            prepend-inner-icon="mdi-lock"
            counter
            @click:append="showPassword = !showPassword"
            :error-messages="errorsMessage.password"
          ></v-text-field>

          <v-text-field
            v-model="password_confirmation"
            :append-icon="showPassConfirm ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.min]"
            :type="showPassConfirm ? 'text' : 'password'"
            label="Confirm Password"
            prepend-inner-icon="mdi-lock-check"
            outlined
            @click:append="showPassConfirm = !showPassConfirm"
          ></v-text-field>
        </v-card-text>

        <v-card-actions>
          <v-btn
            block
            depressed
            color="blue"
            class="grey--text text--lighten-3"
            :disabled="disablebtnUpdate"
            :loading="btnLoading"
            @click="updatePassword"
            >Update Password</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  data() {
    return {
      cardLoading: false,
      btnLoading: false,
      showPassword: false,
      showPassConfirm: false,
      btndisabled: true,
      isResetSuccess: false,

      rules: {
        required: (value) => !!value || "Required.",
        min: (v) => v.length >= 6 || "Min 6 characters",
        emailMatch: () => `The email and password you entered don't match`,
      },

      emailRules: [
        (v) => !!v || "E-mail is required",
        (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
      ],

      email: "",
      password: "",
      password_confirmation: "",

      responseMsg: "",
      errorsMessage: {
        email: "",
        password: "",
        password_confirmation: "",
      },

      status: false,
    };
  },

  computed: {
    disablebtnUpdate: function () {
      return this.password.length >= 6 &&
        this.password_confirmation.length >= 6 &&
        this.email
        ? false
        : true;
    },
  },

  mounted() {
    // console.log(this.$route.query.token);
  },

  methods: {
    updatePassword() {
      this.btnLoading = true;
      this.cardLoading = true;

      axios.defaults.headers.common["Authorization"] =
        "Bearer " + sessionStorage.getItem("access_token");
      axios
        .post("api/password/reset", {
          token: this.$route.query.token,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
        })
        .then((response) => {
          if (response.status == 200) {
            this.responseMsg = "";
            this.isResetSuccess = true;
            this.btnLoading = false;
            this.cardLoading = false;
          }
        })
        .catch((error) => {
          this.btnLoading = false;
          this.cardLoading = false;
          this.responseMsg = error.response.data.message;
        });
    },
  },
};
</script>
