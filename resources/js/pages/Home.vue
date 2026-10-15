<template>
    <v-container>
      <div  class="d-flex py-4">
        <div class="mx-6 d-flex flex-grow-1 flex-shrink-0">
          <v-responsive
            max-width="880"
          >
            <v-text-field
              v-model="search"
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
        </div>
        <div class="mr-8 flex-grow-0 flex-shrink-1">
        </div>
      </div>
      <div class="d-flex align-content-center flex-wrap">
        <template v-for="(item,i) in filterNotes" :key="i">
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
                <div style="min-height:120px; max-height:120px; overflow:hidden" v-html="item.content"></div>
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
              <v-tooltip location="bottom" text="Reminder">
                <template v-slot:activator="{ props }">
                  <v-btn
                  v-bind="props"
                  @click="reminderDialog(item)"
                  color="medium-emphasis"
                  icon="mdi-calendar-clock"
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
        style="z-index: 1000;"
        persistent
        no-click-animation
        >
          <v-card>
            <v-card-title>
              <v-text-field v-model="obj.title" mt-2 mx-auto label="Title" variant="underlined"></v-text-field>
            </v-card-title>
            <editor
              :init="tiny_init"
              v-model="obj.content"
              height="400"
              id="tinymce"
            />
            <v-card-actions>
              <v-btn @click="closeAddDialog()">Close</v-btn>
              <v-spacer></v-spacer>
              <v-btn @click="saveNotes()">{{dialogText}}</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="openreminderDialog" max-width="500">
          <v-card>
            <v-card-text>
              <v-text-field v-model="datereminder" type="datetime-local" label="Date"></v-text-field>
              <!-- <v-text-field v-model="timereminder" type="time" label="Date" @change="handleChange"></v-text-field> -->
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                @click="saveReminder()"
                text="Save"
              ></v-btn>
            </v-card-actions>
          </v-card>
      </v-dialog>
      </div>
    </v-container>
  </template>
  <script>
  import Editor from "@tinymce/tinymce-vue";

  import "tinymce/tinymce";
import "tinymce/themes/silver";
import 'tinymce/icons/default'
import "tinymce/skins/ui/oxide/skin.css";
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/media';
import 'tinymce/plugins/help';
import 'tinymce/plugins/table';
import 'tinymce/plugins/importcss';
import 'tinymce/plugins/directionality';
import 'tinymce/plugins/visualchars';
import 'tinymce/plugins/template';
import 'tinymce/plugins/imagetools';
import 'tinymce/plugins/textpattern';
import 'tinymce/plugins/noneditable';
import 'tinymce/plugins/emoticons';
import 'tinymce/plugins/autosave';
import 'tinymce/plugins/nonbreaking'
import axios from 'axios'
import moment from 'moment'
  export default {
    components: {
    'editor': Editor
},
    data() {
      return {
        tiny_init : {
        height: 300,
        z_index: 10000,
        branding: false,
        statusbar: false,
        noneditable_noneditable_class: 'mceNonEditable',
        selector: 'textarea#image-tools',
        visual: false,
        inline: false,
        image_title: true,
        table_responsive_width: false,
        menubar: false,
        plugins: [
          // 'export',
          //'print',
          'pagebreak',
          'table',
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table imagetools',
          'nonbreaking',
          'thumbnail'
        ],
        nonbreaking_force_tab: true,
        toolbar: 'fontselect  | fontsizeselect | bold italic',
        skin: false,
        content_css: false,
        fontsize_formats : "8px 9px 10px 11px 12px 14px 16px 20px 24px 36px 40px 49px 72px",
        font_formats : `Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial    Black=arial black,
            avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,
            courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,
            helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva;
            Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats`,
      },
        AddDialog: false,
        obj: {},
        notesData: [],
        dialogText: "",
        chip: false,
        search: "",
        openreminderDialog: false,
        datereminder: '',
        timereminder: '',
        user:{}
      }
    },
    created(){
      this.getUser()
      this.getNotes()
      setInterval(()=>{
        this.reminderFunc()
      },1000)
    },
    watch:{
    },
    computed:{
      rem(){
        return this.notesData.filter((r) => {
          if(r.reminder === moment().format('YYYY-MM-DD HH:mm:ss')){
            return r
            
          }
        })
      },
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
        this.dialogText = 'Add'
        this.AddDialog = true
      },
      saveNotes(){
        if(this.dialogText === 'Add'){
        axios.post('api/add_note', this.obj )
          .then(() => {
            this.getNotes()
            this.obj = {}
            this.AddDialog = false
          })
        }else{
          axios.post('api/edit_note', this.obj)
          .then( () => {
            this.AddDialog = false;
            this.getNotes();
          });
        }
      },
      getNotes(){
      axios.get('api/get_notes')
        .then( (res) => {
          console.log(res.data);
          this.notesData = res.data.filter(r => {
            // The API already returns only the signed-in user's notes.
            return !r.archive && !r.trash && !r.deleted_at
          })
        })
      },

      getUser(){
      axios.get('api/user')
        .then( (res) => {
          this.user = res.data
          console.log(this.user.name);
        })
      },
      openEdit(item){
        console.log(item),
        this.dialogText = 'Edit'
        this.obj = Object.assign({},item)
        this.AddDialog = true
      },

      actionEdit(item,action){
        console.log(action)
        console.log(item)
        this.obj = Object.assign({},item)
        if(action === 'reminder'){
          this.obj.reminder = moment().format('YYYY-MM-DD')
        }else if(action === 'archive'){
          this.obj.archive = moment().format('YYYY-MM-DD')
        }else{
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
      },

      reminderDialog(item){
        this.obj = Object.assign({},item)
        this.openreminderDialog = true
      },
      saveReminder(){
        let dateTime = this.datereminder + " "+ this.timereminder
        this.obj.reminder = dateTime
        axios.post('api/edit_action', this.obj)
          .then( () => {
            this.openreminderDialog = false;
            this.getNotes();
            console.log(this.obj)
        });
      },
      closeAddDialog(){
        this.AddDialog = false;
      },
      aReminder(item){
        console.log("aReminder",item)
      },

      checkReminders() {
      const now = new Date();
      this.notesData.forEach((rem, index) => {
        if (rem.reminder <= now) {
          alert(`test`);
          // this.reminders.splice(index, 1);
        }
      });
    },

    reminderFunc(){
        const reminderarr = this.notesData.map(r => r)
        if (!reminderarr || typeof reminderarr[Symbol.iterator] !== 'function') {
          console.error('reminderarr is not iterable');
          return;
        }
        

        for (const data of reminderarr) {
          if (data.reminder !== null) {
            if (data.reminder <= moment().format('YYYY-MM-DD hh:mm:ss')) {
              console.log('yooh');
              alert(`your reminder ${data.title}`)
              data.reminder = null
              axios.post('api/edit_action', data)
                .then( () => {
                  this.getNotes();
              });
            } else {
              console.log('mali');
            }
          }
        }
      },
  
    },
  }
  </script>
  <style scoped>
  </style>