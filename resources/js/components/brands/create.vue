<template>
    <div class="card-body">
        <div class="pl-lg-4">
            <br>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group featured_image">
                        <a href="#">
                            <img
                                id="featured_image"
                                :src="brand.logo_src"
                                @click="triggerLogoInput"
                                :class="{
                                    'img-not-validated': errors.logo_file
                                }"
                            />
                            <input
                                type="file"
                                class="custom-file-input"
                                lang="en"
                                v-on:change="logoChange"
                                style="display: none;"
                                ref="logo_file"
                            />
                            <div
                                class="invalid-feedback"
                                style="display: block;"
                                v-if="errors.logo_file"
                            >{{ errors.logo_file[0] }}</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label
                                    class="form-control-label"
                                    for="input-name"
                                >Brand Name</label>
                                <input
                                    :class="{'not-validated':errors.name}"
                                    type="text"
                                    id="input-name"
                                    placeholder="Enter Brand Name"
                                    v-model="brand.name"
                                    class="form-control"
                                >
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.name"
                                >
                                    {{errors.name[0]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label
                                    class="form-control-label"
                                    for="input-tag"
                                >Categories / Tags</label>
                                <multiselect
                                    v-model="brand.categories"
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
                                <label
                                    class="form-control-label"
                                    for="input-name"
                                >About</label>
                                <textarea
                                    rows="4"
                                    :class="{'not-validated':errors.description}"
                                    class="form-control"
                                    placeholder="Enter Description"
                                    v-model="brand.description"
                                >
								</textarea>
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.description"
                                >
                                    {{errors.description[0]}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button
                class="btn btn-outline-primary"
                @click="save"
            >Create</button>
        </div>
    </div>
</template>

<script>
import { fields } from "../../config/fields";
import Multiselect from "vue-multiselect";

export default {
    components: { Multiselect },
    data() {
        return {
            value: null,
            options: [
                { name: "Vue.js", language: "JavaScript" },
                { name: "Rails", language: "Ruby" },
                { name: "Sinatra", language: "Ruby" },
                { name: "Laravel", language: "PHP", $isDisabled: true },
                { name: "Phoenix", language: "Elixir" }
			],
			categories:[],
            brand: {
                logo_file: "",
                logo_src: window.location.origin + "/files/brands/no_image.png"
            },
            errors: {}
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "createBrand");
        this.$store.commit("changeCurrentMenu", "settingsMenu");
    },
    mounted() {
		this.getCategories();
        this.reset();
	},
    methods: {
		reset(){
            this.errors = {};
            this.brand = {
                categories:[],
                logo_file: "",
                logo_src: window.location.origin + "/files/brands/no_image.png"
            };
        },
        save() {
            let formData = new FormData();
            for (var key in this.brand) {
                formData.append(key, this.brand[key]);
			}
			formData.append('categories', JSON.stringify(this.brand.categories));
            axios
                .post("/brands", formData)
                .then(response => {
                    this.reset();
                    this.$parent.addBtn = true;
                    this.$parent.getBrands();
                    showNotify("success", response.data);
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    for (var prop in error.response.data.errors) {
                        showNotify("danger", error.response.data.errors[prop]);
                    }
                });
		},
		getCategories(){
            axios.get("/categories").then(response => this.categories = response.data );
        },
        triggerLogoInput() {
            this.$refs.logo_file.click();
        },
        logoChange(e) {
            this.brand.logo_file = e.target.files[0];
            this.brand.logo_src = URL.createObjectURL(this.brand.logo_file);
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