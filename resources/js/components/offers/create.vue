<template>
    <div class="card-body">
        <div class="pl-lg-4">
            <br />
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="form-control-label" for="input-name">Featured Image</label>
                        <div class="card-profile-image">
                            <a href="#">
                                <img
                                    :src="offer.image_src"
                                    class
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
                        <div
                            class="invalid-feedback"
                            style="display: block;"
                            v-if="errors.image_file"
                        >{{ errors.image_file[0] }}</div>
                    </div>
                </div>
                <div class="col-lg-4">
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
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="form-control-label" for="input-expiry">Validity</label>
                        <input
                            type="date"
                            id="input-expiry"
                            placeholder="Valid Till"
                            v-model="offer.expiry"
                            class="form-control"
                            :class="{ 'not-validated': errors.expiry }"
                        />
                        <div
                            class="invalid-feedback"
                            style="display: block;"
                            v-if="errors.expiry"
                        >{{ errors.expiry[0] }}</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="form-control-label" for="input-tag">Categories / Tags</label>
                        <multiselect 
                            v-model="value" 
                            :options="categories"
                            label="name"
                            :multiple="true"
                            placeholder="Add relevant Tags" 
                            track-by="name"
                        ></multiselect>
                        <div
                            class="invalid-feedback"
                            style="display: block;"
                            v-if="errors.category_ids"
                        >{{ errors.category_ids[0] }}</div>
                    </div>
                </div>
                <div class="col-lg-4">
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
            options: [
                { name: "Vue.js", language: "JavaScript" },
                { name: "Rails", language: "Ruby" },
                { name: "Sinatra", language: "Ruby" },
                { name: "Laravel", language: "PHP", $isDisabled: true },
                { name: "Phoenix", language: "Elixir" }
            ],
            offer: {
                image_file: "",
                image_src: window.location.origin + "/files/offers/no_image.png"
            },
            errors: {}
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "createOffer");
        this.$store.commit("changeCurrentMenu", "settingsMenu");
    },
    mounted() {
        this.getCategories();
    },
    methods: {
        reset(){
            this.errors = {};
            this.offer = {};
        },
        getCategories(){
            axios.get("/categories").then(response => this.categories = response.data );
        },
        save() {
            let formData = new FormData();
            for (var key in this.offer) {
                formData.append(key, this.offer[key]);
            }
            axios
                .post("/offers", formData)
                .then(response => {
                    console.log(response.data)
                    // this.reset();
                    // this.$parent.addBtn = true;
                    // this.$parent.getOffers();
                    // showNotify("success", response.data);
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
