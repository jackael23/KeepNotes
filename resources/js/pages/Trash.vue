<template>
    <v-container>
      <div  class="d-flex py-4">
        <!-- <div class="mx-6 d-flex flex-grow-1 flex-shrink-0">
          <v-responsive
            max-width="880"
          >
            <v-text-field
              append-inner-icon="mdi-magnify"
              density="compact"
              label="Search templates"
              variant="solo"
              hide-details
              single-line
              max-width="4"
            ></v-text-field>
          </v-responsive>
          <v-btn @click="openDialog()" class="ml-4 mt-1">
            <v-icon left>mdi-plus</v-icon> ADD
          </v-btn>
        </div> -->
        <div class="mr-8 flex-grow-0 flex-shrink-1">
        </div>
      </div>
      <div class="d-flex align-content-center flex-wrap">
        <template v-for="(item,i) in notesData" :key="i">
          <div class="px-2 mx-5 mb-2">
            <v-card
              width="300"
              height="280px"
              :color="item.color"
            >
              <v-card flat>
                <v-card-title class="align-self-center">
                    {{ item.title }}
                </v-card-title>
                <hr>
                <v-card-text>
                    <div style="min-height:150px; max-height:150px; overflow:hidden" v-html="item.content"></div>
                </v-card-text>
                </v-card>
                <v-card-text class="py-0 ma-0">
                    <v-chip v-if="item.reminder" variant="outlined" size="small" closable>{{item.reminder}}</v-chip>
                </v-card-text>
                <v-card-actions class="pt-0">
                  <v-tooltip location="bottom" text="Restore">
                    <template v-slot:activator="{ props }">
                      <v-btn
                        v-bind="props"
                        @click="actionEdit(item,'restore')"
                        color="medium-emphasis"
                        icon="mdi-restore"
                        size="small"
                      ></v-btn>
                    </template>
                  </v-tooltip>

                  <v-tooltip location="bottom" text="Archive">
                    <template v-slot:activator="{ props }">
                      <v-btn
                        v-bind="props"
                        @click="actionEdit(item,'archive')"
                        color="medium-emphasis"
                        icon="mdi-archive-outline"
                        size="small"
                      ></v-btn>
                    </template>
                  </v-tooltip>

                  <v-tooltip location="bottom" text="Delete Permanently">
                    <template v-slot:activator="{ props }">
                      <v-btn
                        v-bind="props"
                        @click="actionEdit(item,'trash')"
                        color="medium-emphasis"
                        icon="mdi-delete-outline"
                        size="small"
                      ></v-btn>
                    </template>
                  </v-tooltip>
                </v-card-actions>
            </v-card>
          </div>
        </template>
      </div>
    </v-container>
  </template>
  <script>
import axios from 'axios'
import moment from 'moment'
  export default {
    data() {
      return {
        AddDialog: false,
        obj: {},
        notesData: []
      }
    },
    created(){
      this.getNotes()

    },
    methods: {
      openDialog(){
        this.AddDialog = true
      },
      saveNotes(){
        axios.post('api/add_note', this.obj )
        .then((res) => {
          this.getNotes()
          this.obj = {}
          this.AddDialog = false
        })
      },
      getNotes(){
      axios.get('api/get_notes')
        .then( (res) => {
          this.notesData = res.data.filter(r => {
            return r.trash && !r.delete_at
          })
        })
    },

    actionEdit(item,action){
        console.log(action)
        console.log(item)
        this.obj = Object.assign({},item)
        if(action === 'restore'){
          this.obj.trash = null
        }
        else if(action === 'archive'){
          this.obj.trash = null
          this.obj.archive = moment().format('YYYY-MM-DD')
        }
        else{
          this.obj.deleted_at = moment().format('YYYY-MM-DD')
        }
        console.log(this.obj)
        axios.post('api/edit_action', this.obj)
          .then( () => {
            this.getNotes();
        });
      }
    },
  }
  </script>
  <style scoped>
  </style>