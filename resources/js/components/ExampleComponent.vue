<template>
  <v-app>
    <h1>Hello</h1>
    <router-link link to="/dashboard">Dashboard</router-link>
    <router-view />
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      drawer: null,
    };
  },
  mounted() {
    console.log("Component mounted.");
  },

  methods: {
    click() {
      alert();
    },
  },
};
</script>
