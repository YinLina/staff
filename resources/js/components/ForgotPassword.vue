<template>
    <v-row align="center" justify="center" id="forgot-password">
        <v-col cols="12" md="6">
            <v-card class="pa-5 card-background">
                <v-card
                    v-show="!emailLinkSent"
                    height="350"
                    class="mt-3"
                    data-aos="zoom-in"
                    data-aos-duration="500"
                >
                <v-progress-linear
                    absolute
                    top
                    color="blue"
                    :active="cardLoading"
                    :indeterminate="cardLoading"
                ></v-progress-linear>

                <v-row>
                    <v-col cols="12" sm="5" class="d-flex justify-center align-center">
                        <v-img
                            class="forgot-pass-logo"
                            max-height="300"
                            max-width="100%"
                            :src="'/image/83593-email-template.gif'"
                        ></v-img>
                    </v-col>
                    <v-col cols="12" sm="7" class="pa-0">
                        <v-card elevation="0" class="ma-5">
                            <v-card-title>{{ $t('forgotPass.title') }}</v-card-title>

                            <v-card-text class="mt-1 pb-0">
                                <p class="forgot-description">
                                    {{ $t('forgotPass.description1') }} <br />
                                    {{ $t('forgotPass.description2') }}
                                </p>

                                <v-text-field
                                    v-model="email"
                                    v-bind:label="$t('forgotPass.email')"
                                    type="text"
                                    outlined
                                    :rules="emailRules"
                                    prepend-inner-icon="mdi-email"
                                    :error-messages="errorsMessage.email"
                                ></v-text-field>
                            </v-card-text>
                            <v-card-actions class="ml-3 action">
                            <router-link link to="login">
                                <p class="subtitle-2 blue--text">{{ $t('forgotPass.backToLogin') }}</p>
                            </router-link>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="info"
                                depressed
                                :loading="btnLoading"
                                @click="submitResetPassword"
                            >
                                <v-icon left>mdi-send</v-icon>
                                {{ $t('forgotPass.btnResetPass') }}
                            </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
                </v-card>

                <!-- ----------------------- -->
                <v-card v-show="emailLinkSent" class="mt-3" height="350">
                <v-card-text class="d-flex d-flex justify-center align-center">
                    <div class="text-center">
                    <v-img
                        class="ma-auto"
                        max-height="170"
                        max-width="170"
                        :src="'/image/69380-success-check.gif'"
                    ></v-img>
                    <span class="subtitle-1 font-weight-medium">
                        Please check your email <br />
                        {{ successMsg }}
                    </span>
                    </div>
                </v-card-text>
                </v-card>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
export default {
  data() {
    return {
      isFormValid: false,
      cardLoading: false,
      btnLoading: false,
      btndisabled: true,
      emailLinkSent: false,
      successMsg: "",
      email: "",
      emailRules: [
        (v) => !!v || "E-mail is required",
        (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
      ],

      errorsMessage: {
        email: "",
        password: "",
      },
    };
  },

  methods: {
    submitResetPassword() {
      this.btnLoading = true;
      this.cardLoading = true;

      if (this.email.length == 0) {
        this.errorsMessage.email = "Enter your email address!";
        this.btnLoading = false;
        this.cardLoading = false;
      } else {
        axios
          .post("api/forgot-password", {
            email: this.email,
          })
          .then((response) => {
            if (response.status == 200) {
              this.successMsg = response.data.status;
              this.emailLinkSent = true;
              this.btnLoading = false;
              this.cardLoading = false;
            }
          })
          .catch((errors) => {
            this.errorsMessage = errors.response.data.errors;
            this.btnLoading = false;
            this.cardLoading = false;
          });
      }
    },
  },
};
</script>
