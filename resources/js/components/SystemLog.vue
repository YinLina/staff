<template>
    <div id="SystemLog">
        <v-row>
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="12" md="5">
                        <h3 class="grey--text text--darken-2">
                            <v-icon color="grey darken-2">mdi-badge-account</v-icon>
                            <span class="text-decoration-underline font-weight-medium">
                                {{$t('systemLog.listSystemLog')}}
                            </span>
                        </h3>
                    </v-col>
                    <v-col cols="12" sm="12" md="7">
                        <v-text-field
                            v-model="search"
                            single-line
                            class="txt-search"
                            append-icon="mdi-magnify"
                            v-bind:label="$t('absent.search')"
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

            <template>
                <JqxGrid 
                    ref="systemLogGrid" :width="'100%'" :source="dataAdapter" :columns="columns" :autoheight="false" 
                    :columnsresize="true" :sortable="true" :pageable="true">
                </JqxGrid>
            </template>
    </div>
</template>

<style>
    @import "jqwidgets-scripts/jqwidgets/styles/jqx.base.css";
    span[id^="jqxWidget"] 
    {
        color: transparent !important;
    }
</style>

<script>
    import moment from 'moment';
    import store from '../store';
    import DeleteDialog from './helper_components/DeleteDialog.vue';
    import AlertMessageDialog from './helper_components/AlertMessageDialog.vue';
    import JqxGrid from "jqwidgets-scripts/jqwidgets-vue/vue_jqxgrid.vue";
    export default {
        components: { DeleteDialog, AlertMessageDialog, JqxGrid },
        name:'SystemLog',
        data() {
            //modify LINA 28/08/2023 #cms-91
            let self = this;
            return {
                search: "",
                dataAdapter: new jqx.dataAdapter({
                    localdata: [],
                    SystemLogData: [],
                    datatype: 'array',
                    datafields:
                    [
                        { name: 'MenuName', type: 'string' },
                        { name: 'User_PKID', type: 'string' },
                        { name: 'Action', type: 'string' },
                        { name: 'OldData', type: 'string' },
                        { name: 'NewData', type: 'string' },
                        { name: 'LogType', type: 'string' },
                        { name: 'Messaage', type: 'string' },
                        { name: 'HostName', type: 'string' },
                        { name: 'DateTime', type: 'date' },
                    ]
                }),
                columns: [
                    {text: this.$t('systemLog.tbMenuName'), datafield:'MenuName', width: '12%', align:"center",},
                    {text: this.$t('systemLog.tbUser'), datafield:'User_PKID', width: '6%', align:"center",},
                    {text: this.$t('systemLog.tbAction'), datafield:'Action', width: '6%', align:"center",},
                    {text: this.$t('systemLog.tbOldData'), datafield:'OldData', width: '18%', align:"center",},
                    {text: this.$t('systemLog.tbNewData'), datafield:'NewData', width: '18%', align:"center",},
                    {text: this.$t('systemLog.tbLogType'), datafield:'LogType', width: '8%', align:"center",},
                    {text: this.$t('systemLog.tbMessage'), datafield:'Messaage', width: '10%', align:"center",},
                    {text: this.$t('systemLog.tbHostName'), datafield:'HostName', width: '10%', align:"center",},
                    {
                        text: this.$t('systemLog.tbDateTime'), datafield:'DateTime', width: '12%', align:"center",
                        cellsrenderer: function (row, column, value) {
                            var date = self.$helper.ConvertDateToStr(value);
                            var time = "";
                            if(value instanceof Date) {
                                time = value.getHours() + ":" + value.getMinutes() + ":" + value.getSeconds();
                                value = date + " " + time;
                            }  
                            return '<div class="jqx-grid-cell-left-align" style="margin-top: 8px;">' + value + '</div>';
                        },
                    }
                ]
            }
        },
        computed:{
            userToken() {
                return store.state.user;
            },
        },
        mounted(){
            if(this.userToken.token){
                this.ReadSystemLog();
            }
        },
        methods:{
            //modify LINA 06/09/2023 #cms-91
            ReadSystemLog(){
                axios
                .get(this.$globalConfig.ipHost + "api/read-system-log/", {
                    headers: {
                        Authorization: "Bearer " + sessionStorage.getItem("TOKEN")
                    }
                })
                .then((Response) => {
                    this.SystemLogData = Response.data.data;
                    this.dataAdapter._source.localdata = this.SystemLogData;
                    if(this.$refs.systemLogGrid !== undefined) {
                        this.$refs.systemLogGrid.height = this.$helper.GetGridHeight();
                        this.$refs.systemLogGrid.updatebounddata('cell');
                    }
                })
                .catch((error) => {
                    console.log(error)
                })
            },
        }
    }
</script>
