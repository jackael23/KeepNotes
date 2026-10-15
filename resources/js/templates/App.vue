<template>
    <v-app>
      <v-app-bar
      v-if="this.$router.currentRoute.value.name != 'Login' && this.$router.currentRoute.value.name != 'Register'"
      >
      <!-- v-if="$route.name != 'login'" -->
      <h1 class="ml-4">Keep Notes</h1>
        <v-spacer></v-spacer>
        <div class="button-container">
          <!-- <v-btn class="white-text mr-3" text>Home</v-btn>|
          <v-btn class="white-text mx-3"  text>About</v-btn>|
          <v-btn class="white-text mx-3"  text>Contact</v-btn> -->
          <v-btn @click="logout()" class="mr-4" icon><v-icon>mdi-logout</v-icon></v-btn>
        </div>
      </v-app-bar>
  
      <v-navigation-drawer
          v-model="drawer"
          :rail="rail"
          permanent
          @click="rail = false"
          v-if="this.$router.currentRoute.value.name != 'Login' &&
          this.$router.currentRoute.value.name != 'Register'"
        >
          <!-- <v-list-item
            prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
            title="John Leider"
            nav
          >
            <template v-slot:append>
              <v-btn
                icon="mdi-chevron-left"
                variant="text"
                @click.stop="rail = !rail"
              ></v-btn>
            </template>
          </v-list-item>
  
          <v-divider></v-divider> -->
  
          <v-list density="compact" nav>
            <v-list-item 
            v-for="(items, index) in drawerItems"
            :key="index"
            :to="items.path"
            :value="items">
              <template v-slot:prepend>
                <v-icon :icon="items.icon"></v-icon>
              </template>
              <v-list-item-title v-text="items.title"></v-list-item-title>
            </v-list-item>
          </v-list>
        </v-navigation-drawer>
  
      <v-main class="bg-grey-lighten-3">
        <router-view/>
      </v-main>
    </v-app>
  </template>
  
  <script>
import { VBtn } from 'vuetify/lib/components/index.mjs';

  
  export default {
    name: 'App',
    data() {
      return {
        drawer: true,
        rail: false,
        drawerItems: [
          {
            title: 'Notes',
            path: '/home',
            icon: 'mdi-note-outline'
          },
          {
            title: 'Reminder',
            path: '/reminder',
            icon:'mdi-calendar-clock'
          },
          {
            title: 'Archive',
            path: '/archive',
            icon:'mdi-archive-outline'
          },
          {
            title: 'Trash',
            path: '/trash',
            icon:'mdi-delete-outline'
          },
          // {
          //   title: 'test',
          //   path: '/test',
          //   icon:'mdi-delete-outline'
          // },
        ],
      };
    },
    created() {
      console.log(this.$router.currentRoute.value.name)
    },
    methods:{
      logout(){
        axios
        .post("/api/logout")
        .then((response) => this.$router.push({ name: "Login" }))
        .catch((error) => console.log(error));
      }
    },
    components: { VBtn }
}
  </script>
  <style>
    .button {
        margin-right: 10px !important;
    }
  </style>
  