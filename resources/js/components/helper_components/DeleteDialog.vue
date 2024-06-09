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
                        <div class="grey--text text--darken-3 text-body-2 mb-4 khmer-font">
                            <div v-if="itemTitle === undefined || itemTitle == ''">
                                {{$t('workflow.deleteMessage')}}
                            </div>
                            <div v-else>
                                {{$t('workflow.deleteMessage')}}
                                <b class="red--text text--lighten-2">{{ itemTitle }}</b>
                                ?
                            </div>
                            
                        </div>
                        <v-btn
                            small
                            depressed
                            dark
                            class="ma-1"
                            @click="cancelDialog"
                        >
                            {{$t('absent.btnCancel')}}
                        </v-btn>
                        <v-btn
                            class="ma-1"
                            small
                            depressed
                            dark
                            color="red"
                            @click="deleteItem"
                        >
                            {{$t('absent.btnDelete')}}
                        </v-btn>
                    </v-sheet>
                </div>
            </v-card>
        </v-dialog>
</template>

<script>
export default {
    props: ['openDeleteDialog', 'itemTitle'],
    computed: {
        openDialog() {
            return this.openDeleteDialog;
        },
    },
    methods: {
        deleteItem() {
            this.$emit('deleteItem');
        },
        cancelDialog() {
            this.$emit('cancelDialog');
        }
    }
}
</script>