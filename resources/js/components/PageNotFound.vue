<template>
    <v-container fill-height>
        <v-row class="justify-center align-center">
            <v-col class="text-center">
               <div class="mx-auto">
                    <h1>404</h1>
                    <h2>Page Not Found!</h2>
                    <v-btn small depressed link to="/login" v-show="!loggedIn">login page</v-btn>
               </div>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {}
        },

        computed: {
            loggedIn() {
                return this.$store.getters.loggedIn;
            },
        },
    }
</script>
