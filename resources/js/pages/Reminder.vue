<template>
    <v-container>
      <div  class="d-flex py-4">
        <div class="mx-6 d-flex flex-grow-1 flex-shrink-0">
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
        </div>
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
              <v-card @click="openEdit(item)" flat>
              <v-card-title class="align-self-center">
                {{ item.title }}
              </v-card-title>
              <hr>
              <v-card-text>
                <div style="min-height:110px; max-height:110px; overflow:hidden" v-html="item.content"></div>
              </v-card-text>
            </v-card>
              <v-card-text class="py-0 ma-0">
                <v-chip 
                  v-if="item.reminder"
                  variant="outlined" 
                  size="small" 
                  closable
                  @click:close="chipClick(item)"
                  >
                  {{item.reminder}}
                  </v-chip>
              </v-card-text>
              <v-card-actions class="pt-0">
                <v-tooltip location="bottom" text="Restore">
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

                <v-tooltip location="bottom" text="Trash">
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
        <v-dialog
        v-model="AddDialog"
        max-width="400"
        >
          <v-card>
            <v-card-title>
              <v-text-field v-model="obj.title" mt-2 mx-auto label="Title" variant="underlined"></v-text-field>
            </v-card-title>
            <!-- <editor
              :init="tiny_init"
              v-model="obj.content"
              height="400"
              id="tinymce"
            /> -->
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn @click="saveNotes()" color="info">Save</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
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
        notesData: [],
        chip: false,
      }
    },
    created(){
      this.getNotes()

    },

    computed:{
      filterNotes() {
      return this.notesData.filter((r) => {
        let search = new RegExp(this.search.toUpperCase(), "g");
        if (!this.search) {
          return r;
        } else {
          return JSON.stringify(Object.values(r))
            .toUpperCase()
            .match(search);
        }
      })
    },
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
            return r.reminder
          })
        })
    },

    actionEdit(item,action){
        console.log(action)
        console.log(item)
        this.obj = Object.assign({},item)
        if(action === 'archive'){
          this.obj.reminder = moment().format('YYYY-MM-DD')
        }
        else{
          this.obj.trash = moment().format('YYYY-MM-DD')
        }
        axios.post('api/edit_action', this.obj)
          .then( () => {
            this.AddDialog = false;
            this.getNotes();
        });
      },
      chipClick(item){
        this.obj = Object.assign({},item)
        this.obj.reminder = null
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