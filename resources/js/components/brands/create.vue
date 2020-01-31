<template>
    <div class="card-body">
        <div class="pl-lg-4">
            <br>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="form-control-label" for="input-name">Brand Logo</label>
                        <div class="card-profile-image">
                            <a href="#">
                                <img :src="brand.logo_src" class="" @click="triggerLogoInput" :class="{'img-not-validated':errors.logo_file}" >
                                <input type="file" class="custom-file-input" lang="en" v-on:change="logoChange" style="display: none;" ref="logo_file">
                                <div class="invalid-feedback" style="display: block;" v-if="errors.logo_file">
                                  {{errors.logo_file[0]}}
                                </div>
                            </a>
                        </div>            
                        <div class="invalid-feedback" style="display: block;" v-if="errors.logo_file">
                            {{errors.logo_file[0]}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="form-control-label" for="input-name">Brand Name</label>
                        <input 
                            :class="{'not-validated':errors.name}" 
                            type="text" 
                            :id="'input-'+key" 
                            placeholder="Enter Brand Name" 
                            v-model="brand.name"
                            class="form-control">
                        <div class="invalid-feedback" style="display: block;" v-if="errors.name">
                            {{errors.name[0]}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="form-control-label" for="input-name">Categories / Tags</label>
                        <v-select
                            autocomplete="off"
                            class="form-control form-control-sm padding-rn country_code"
                            v-model="value"
                            :options="options"
                            :reduce="data => data.name"
                            label="name"
                            multiple 
                        />
                        <div class="invalid-feedback" style="display: block;" v-if="errors.category_ids">
                            {{errors.category_ids[0]}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="form-control-label" for="input-name">Brand Name</label>
                        <textarea 
                            rows="4" 
                            :class="{'not-validated':errors[key]}" 
                            class="form-control" 
                            placeholder="Enter Description" 
                            v-model="brand.description">        
                        </textarea>
                        <div class="invalid-feedback" style="display: block;" v-if="errors.description">
                            {{errors.description[0]}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-outline-primary" @click="save">Create</button>
        </div>
    </div>
</template>

<script>
    import {fields} from '../../config/fields'
    import vSelect from 'vue-select'
    import 'vue-select/dist/vue-select.css'

  export default{
    components:{vSelect},
    data(){
      return{
        value: null,
        options: [
            { name: 'Vue.js', language: 'JavaScript' },
            { name: 'Rails', language: 'Ruby' },
            { name: 'Sinatra', language: 'Ruby' },
            { name: 'Laravel', language: 'PHP', $isDisabled: true },
            { name: 'Phoenix', language: 'Elixir' }
        ],
        brand:{
          logo_file:'',
          logo_src:window.location.origin+'/files/brands/no_image.png',
        },
        errors:{},
      }
    },
    created(){
      this.$store.commit('changeCurrentPage', 'createBrand')
      this.$store.commit('changeCurrentMenu', 'settingsMenu')
    },
    mounted(){

    },
    methods:{
      save(){
        let formData = new FormData()
        for (var key in this.brand) {
            formData.append(key, this.brand[key]);
        }
        axios.post('/categories',formData)
        .then((response) => {
          console.log(response.data)
          this.errors = {}
          this.brand = {}
          this.$parent.addBtn = true
          this.$parent.getBrands()
          showNotify('success',response.data)
        })
        .catch((error) => {
          this.errors = error.response.data.errors
          for (var prop in error.response.data.errors) {
            showNotify('danger',error.response.data.errors[prop])
          }  
        })
      },
      triggerLogoInput() {
        console.log(this.$refs.logo_file)
          this.$refs.logo_file[0].click()
      },
      logoChange(e) {
        this.brand.logo_file = e.target.files[0]
        this.brand.logo_src = URL.createObjectURL(this.brand.logo_file)
      }
    },
    computed: {
      fields(){
        return fields.createBrand
      }
    },

  }

</script>
<style>
  .mx-datepicker{
    width: unset;
    display: unset;
  }
  .mx-datepicker-popup{
    top: 0 !important;
  }
  .not-validated{
    border-color: #fb6340;
  }
  .form-control .vs__dropdown-toggle {
    border: 0px !important;
  }
  .img-not-validated{
    border: 3px solid red !important;
  }
</style>