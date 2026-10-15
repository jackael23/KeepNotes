<template>
    <div  class="align-content-center justify-center align-center">
      <div class="mt-15">
        <v-card
          class="mx-auto pa-12 pb-8"
          elevation="8"
          max-width="448"
          rounded="lg"
        >

          <div class="text-subtitle-1 text-medium-emphasis">Email</div>
          <v-text-field
            density="compact"
            placeholder="Enter your Email"
            prepend-inner-icon="mdi-email-outline"
            variant="outlined"
            v-model="obj.email"
          ></v-text-field>
          <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
          <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
            Password
          </div>
          <v-text-field
            :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
            :type="visible ? 'text' : 'password'"
            density="compact"
            placeholder="Enter your password"
            prepend-inner-icon="mdi-lock-outline"
            variant="outlined"
            v-model="obj.password"
            @click:append-inner="visible = !visible"
          ></v-text-field>
          <span v-if="errors.password" class="error">{{ errors.password[0] }}</span>
          <v-card
            class="mb-12"
            color="surface-variant"
            variant="tonal"
          >
          </v-card>
          <v-btn
            class="mb-8"
            color="blue"
            size="large"
            variant="tonal"
            block
            @click="login()"
          >
            Log In
          </v-btn>
          <v-card-text class="text-center">
            <v-btn
            variant="text"
              class="text-blue text-decoration-none"
              @click="register()"
            >
              Sign up <v-icon icon="mdi-chevron-right"></v-icon>
            </v-btn>
          </v-card-text>
        </v-card>
      </div>
    </div>
    </template>
    <script>
    export default {
      data(){
          return{
            visible: false,
            obj: {},
            errors: {},
          }
      },
      created(){
      },
      methods:{
        login(){
          axios
          .post("/api/login", this.obj)
          .then(() => {
            this.$router.push({ name: "Home" });
          })
          .catch((error) => {
            console.log(error);
            this.errors = error.response.data.errors;
          });
        },
        register(){
          this.$router.push({ name: "Register" });
        }
      }
    }
  </script>
  <style scoped>
  .error {
    color: red;
    display: block;
    margin-top: 10px;
    margin-bottom: 15px;
}
  </style>