<template>
    <div class="card-body">
        <div class="pl-lg-4">
            <br />
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group featured_image">
                        <a href="#">
                            <img
                                id="featured_image"
                                :src="offer.image_src"
                                @click="triggerImageInput"
                                :class="{
                                    'img-not-validated': errors.image_file
                                }"
                            />
                            <input
                                type="file"
                                class="custom-file-input"
                                lang="en"
                                v-on:change="imageChange"
                                style="display: none;"
                                ref="image_file"
                            />
                            <div
                                class="invalid-feedback"
                                style="display: block;"
                                v-if="errors.image_file"
                            >{{ errors.image_file[0] }}</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-control-label" for="input-title">Title</label>
                                <input
                                    type="text"
                                    id="input-title"
                                    placeholder="Enter Offer Title"
                                    v-model="offer.title"
                                    class="form-control"
                                    :class="{ 'not-validated': errors.title }"
                                />
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.title"
                                >{{ errors.title[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label" for="input-expires_in">Validity</label>
                                <input
                                    type="date"
                                    id="input-expires_in"
                                    placeholder="Valid Till"
                                    v-model="offer.expires_in"
                                    class="form-control"
                                    :class="{ 'not-validated': errors.expires_in }"
                                />
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.expires_in"
                                >{{ errors.expires_in[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label" for="input-tag">Categories / Tags</label>
                                <multiselect 
                                    v-model="offer.categories" 
                                    :options="categories"
                                    label="name"
                                    :multiple="true"
                                    placeholder="Add relevant Tags" 
                                    track-by="name"
                                ></multiselect>
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.categories"
                                >{{ errors.categories[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label" for="input-tag">Brand</label>
                                <multiselect 
                                    v-model="offer.brand" 
                                    :options="brands"
                                    label="name"
                                    placeholder="Add Brand" 
                                    track-by="name"
                                ></multiselect>
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.brand"
                                >{{ errors.brand[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label" for="input-tag">Source</label>
                                <select v-model="offer.source" class="form-control">
                                    <option selected value="" disabled>Select Source</option>
                                    <option>Facebook</option>
                                    <option>Instagram</option>
                                    <option>Newspaper</option>
                                    <option>Website</option>
                                    <option>Direct</option>
                                    <option>Others</option>
                                </select>
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.source"
                                >{{ errors.source[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label" for="input-name">Description</label>
                                <textarea
                                    rows="4"
                                    :class="{ 'not-validated': errors.description }"
                                    class="form-control"
                                    placeholder="Enter Description"
                                    v-model="offer.description"
                                ></textarea>
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.description"
                                >{{ errors.description[0] }}</div>
                            </div>
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
import { fields } from "../../config/fields";
import Multiselect from 'vue-multiselect';

export default {
    components: { Multiselect },
    data() {
        return {
            value: null,
            categories: [],
            brands:[],
            options: [
                { name: "Vue.js", language: "JavaScript" },
                { name: "Rails", language: "Ruby" },
                { name: "Sinatra", language: "Ruby" },
                { name: "Laravel", language: "PHP", $isDisabled: true },
                { name: "Phoenix", language: "Elixir" }
            ],
            offer: {},
            errors: {}
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "createOffer");
        this.$store.commit("changeCurrentMenu", "settingsMenu");
    },
    mounted() {
        this.getCategories();
        this.getBrands();
        this.reset();
    },
    methods: {
        reset(){
            this.errors = {};
            this.offer = {
                title: "",
                categories:[],
                description: "",
                image_file: "",
                image_src: window.location.origin + "/files/offers/no_image.png"
            };
        },
        getCategories(){
            axios.get("/categories").then(response => this.categories = response.data );
        },
        getBrands(){
            axios.get("/brands").then(response => this.brands = response.data );
        },
        save() {
            let formData = new FormData();
            for (var key in this.offer) {
                formData.append(key, this.offer[key]);
            }
            formData.append('categories', JSON.stringify(this.offer.categories));
            formData.append('brand', JSON.stringify(this.offer.brand));
            axios
                .post("/offers", formData)
                .then(response => {
                    this.reset();
                    this.$parent.addBtn = true;
                    this.$parent.getOffers();
                    showNotify("success", response.data);
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    for (var prop in error.response.data.errors) {
                        showNotify("danger", error.response.data.errors[prop]);
                    }
                });
        },
        triggerImageInput() {
            this.$refs.image_file.click();
        },
        imageChange(e) {
            this.offer.image_file = e.target.files[0];
            this.offer.image_src = URL.createObjectURL(this.offer.image_file);
        }
    },
    computed: {
        fields() {
            return fields.createBrand;
        }
    }
};
</script>
<style>
#featured_image{
    width: 300px;
    max-width: 400px; 
}
.featured_image{
    text-align: center;
}
.mx-datepicker {
    width: unset;
    display: unset;
}
.mx-datepicker-popup {
    top: 0 !important;
}
.not-validated {
    border-color: #fb6340;
}
.form-control .vs__dropdown-toggle {
    border: 0px !important;
}
.img-not-validated {
    border: 3px solid red !important;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
