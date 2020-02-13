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
                        <div class="col-md-8">
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
                                    for="input-email"
                                >Company Email</label>
                                <input
                                    :class="{'not-validated':errors.email}"
                                    type="text"
                                    id="input-email"
                                    placeholder="company@email.com"
                                    v-model="brand.email"
                                    class="form-control"
                                >
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.email"
                                >
                                    {{errors.email[0]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label
                                    class="form-control-label"
                                    for="input-url"
                                >Website</label>
                                <input
                                    :class="{'not-validated':errors.url}"
                                    type="text"
                                    id="input-url"
                                    placeholder="Eg: https://brandofficialwebsite.com"
                                    v-model="brand.url"
                                    class="form-control"
                                >
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.url"
                                >
                                    {{errors.url[0]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label
                                    class="form-control-label"
                                    for="input-cp_name"
                                >Contact Person Name</label>
                                <input
                                    :class="{'not-validated':errors.cp_name}"
                                    type="text"
                                    id="input-cp_name"
                                    placeholder="Enter contact person name"
                                    v-model="brand.cp_name"
                                    class="form-control"
                                >
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.cp_name"
                                >
                                    {{errors.cp_name[0]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label
                                    class="form-control-label"
                                    for="input-cp_designation"
                                >Contact Person Designation</label>
                                <input
                                    :class="{'not-validated':errors.cp_designation}"
                                    type="text"
                                    id="input-cp_designation"
                                    placeholder="Enter contact person designation"
                                    v-model="brand.cp_designation"
                                    class="form-control"
                                >
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.cp_designation"
                                >
                                    {{errors.cp_designation[0]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label
                                    class="form-control-label"
                                    for="input-cp_contact"
                                >Contact Person Contact</label>
                                <input
                                    :class="{'not-validated':errors.cp_contact}"
                                    type="text"
                                    id="input-cp_contact"
                                    placeholder="Enter contact person contact"
                                    v-model="brand.cp_contact"
                                    class="form-control"
                                >
                                <div
                                    class="invalid-feedback"
                                    style="display: block;"
                                    v-if="errors.cp_contact"
                                >
                                    {{errors.cp_contact[0]}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <label
                        class="form-control-label"
                        for="input-cp_designation"
                    >Banner Images</label>
                    <div class="row">

                        <input
                            type="file"
                            @change="onFilePicked"
                            ref="uploadinput"
                            style="display:none;"
                        />
                        <div
                            class="col-sm-2"
                            v-for="count in 5"
                            :key="count"
                        >
                            <div class="img-select-container">
                                <div
                                    class="img-select-box banner_image"
                                    @click.prevent="brand.browseFor='img'+count,$refs.uploadinput.click()"
                                >
                                    <img
                                        :src="brand['img'+count+'_src']"
                                        alt=""
                                    />
                                </div>
                                <a
                                    title="Click to remove image"
                                    href=""
                                    :class="'text-danger remove_image '+(brand['img'+count+'_src']!='' ?'remove_no':'')"
                                    @click.prevent="clearImg(count)"
                                >
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
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
            categories: [],
            brand: {},
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
        reset() {
            this.errors = {};
            this.brand = {
                img1_src: window.location.origin + "/files/brands/no_image.png",
                img2_src: window.location.origin + "/files/brands/no_image.png",
                img3_src: window.location.origin + "/files/brands/no_image.png",
                img4_src: window.location.origin + "/files/brands/no_image.png",
                img5_src: window.location.origin + "/files/brands/no_image.png",
                categories: [],
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
        // addBannerImage(){
        //     this.brand.banner_images.push({
        //         banner_file: "",
        //         banner_src: window.location.origin + "/files/brands/no_image.png"
        //     })
        // },
        getCategories() {
            axios.get("/categories").then(response => this.categories = response.data);
        },
        triggerLogoInput() {
            this.$refs.logo_file.click();
        },
        logoChange(e) {
            this.brand.logo_file = e.target.files[0];
            this.brand.logo_src = URL.createObjectURL(this.brand.logo_file);
        },
        onFilePicked(e) {
            const file = e.target.files[0];
            // console.log(file)
            this.processFile(file);
            e.target.value = '';
        },
        clearImg(count) {
            this.brand['img' + count + '_src'] = window.location.origin + "/files/brands/no_image.png"
            this.brand['img' + count + '_file'] = ''
        },
        processFile(file) {
            if (!file.type.includes("image/")) {
                return;
            }
            let $this = this;
            if (typeof FileReader === "function") {
                const reader = new FileReader();
                reader.onload = event => {
                    // let file = this.$refs.uploadinput.files[0];
                    try {
                        if ($this.brand.browseFor.length > 0) {
                            $this.brand[$this.brand.browseFor + '_src'] = URL.createObjectURL(file);
                            $this.brand[$this.brand.browseFor + '_file'] = file;
                        }
                        else {
                            // looping for 4 images
                            for (var j = 1; j <= 4; j++) {
                                if ($this.brand['img' + j + '_src'] == '') {
                                    $this.brand['img' + j + '_src'] = URL.createObjectURL(file);
                                    $this.brand['img' + j + '_file'] = file
                                    throw Error("Done");
                                }
                            }
                        }
                        $this.brand.browseFor = '';
                    } catch (e) {
                        console.log(e);
                    }
                };
                reader.readAsDataURL(file);
            }
        },
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
.banner_image img {
    height: 150px;
}
</style>