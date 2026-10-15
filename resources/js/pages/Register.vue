<template>
    <div  class="align-content-center justify-center align-center">
      <div class="mt-9">
        <v-card
          class="mx-auto pa-12 pb-8"
          elevation="8"
          max-width="448"
          rounded="lg"
        >
          <div class="text-subtitle-1 text-medium-emphasis">Username</div>
    
          <v-text-field
            density="compact"
            placeholder="Enter your Username"
            prepend-inner-icon="mdi-account"
            variant="outlined"
            v-model="obj.name"
          ></v-text-field>
          <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>

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
            @click:append-inner="visible = !visible"
            v-model="obj.password"
          ></v-text-field>
          <span v-if="errors.password" class="error">{{ errors.password[0] }}</span>


          <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
            Confirm Password
          </div>
    
          <v-text-field
            :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
            :type="visible ? 'text' : 'password'"
            density="compact"
            placeholder="Confirm password"
            prepend-inner-icon="mdi-lock-outline"
            variant="outlined"
            @click:append-inner="visible = !visible"
            v-model="obj.password_confirmation"
          ></v-text-field>
    
          <v-card
            color="surface-variant"
            variant="tonal"
          >
          </v-card>
          <v-card-text class="text-center">
            <v-btn
              class="text-blue text-decoration-none"
              @click="registerUser()"
            >
              Register
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
      methods:{
        registerUser() {
          axios
          .post("/api/register", this.obj)
          .then(() => {
            this.$router.push({ name: "Login" });
          })
          .catch((error) => {
            console.log(error);
            this.errors = error.response.data.errors;
          });
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