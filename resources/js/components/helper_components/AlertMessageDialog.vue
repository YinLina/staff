<template>
    <v-dialog
            width="350px"
            v-model="openDialog"
        >
            <v-card>
                <div class="text-center">
                    <v-sheet
                        class="px-3 pt-7 pb-4 mx-auto text-center d-inline-block"
                    >
                        <v-icon
                            class="text-center pb-3"
                            x-large
                            color="red lighten-2"
                        >mdi-alert</v-icon>
                        <div class="red--text text--darken-1 text-body-2 mb-4 font-weight-bold">
                            {{ msgDialog }}
                        </div>
                        <v-btn
                            small
                            depressed
                            dark
                            class="ma-1 khmer-font"
                            color="red"
                            @click="btnOK"
                        >
                            OK
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>
</template>

<script>
export default {
    props: ['openAlertDialog', 'msgDialog'],
    computed: {
        openDialog() {
            return this.openAlertDialog;
        },
    },
    methods: {
        btnOK() {
            this.$emit('btnOK');
        }
    }
}
</script>