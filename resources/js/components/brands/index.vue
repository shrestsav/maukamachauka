<template>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Brands</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                @click="addBtn = !addBtn"
                                v-if="addBtn"
                            >Add</button>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                @click="addBtn = !addBtn"
                                v-if="!addBtn"
                            >Cancel</button>
                        </div>
                    </div>
                </div>
                <create v-if="!addBtn"></create>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>S.No.</th>
                                <th>Brand Name</th>
                                <th>Brand Image</th>
                                <th>Description</th>
                                <th>Related Tags</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <tr v-for="(item,index) in brands">
                                <td>{{index+1}}</td>
                                <td>{{item.name}}</td>
                                <td>
                                    <template v-if="editExisting(item.id)">
                                        <img
                                            :src="brand.logo_src"
                                            class="rounded-circle"
                                            @click="triggerImageInput(index)"
                                            :class="{'img-not-validated':errors.logo_file}"
                                            height="100px"
                                        >
                                        <input
                                            type="file"
                                            class="custom-file-input"
                                            lang="en"
                                            v-on:change="imageChange"
                                            style="display: none;"
                                            :ref="'logo_file_'+index"
                                        >
                                        <div
                                            class="invalid-feedback"
                                            style="display: block;"
                                            v-if="errors.logo_file"
                                        >
                                            {{errors.logo_file[0]}}
                                        </div>
                                    </template>
                                    <img
                                        v-else
                                        :src="item.logo_src"
                                        height="100px"
                                    >
                                </td>
                                <td>
                                    <div
                                        class="form-group"
                                        v-if="editExisting(item.id)"
                                    >
                                        <textarea
                                            v-model="brand.description"
                                            :class="{'not-validated':errors.description}"
                                            class="form-control"
                                            rows="3"
                                            placeholder="BRIEF DESCRIPTION OF Brand"
                                        ></textarea>
                                        <div
                                            class="invalid-feedback"
                                            style="display: block;"
                                            v-if="errors.description"
                                        >
                                            {{errors.description[0]}}
                                        </div>
                                    </div>
                                    <div v-else><p class="desc">{{item.description}}</p></div>
                                </td>
                                <td>{{ grabName(item.categories) }}</td>
                                <td>
                                    <div v-if="editExisting(item.id)">
                                        <button
                                            type="button"
                                            class="btn btn-success btn-sm"
                                            @click="saveEdited"
                                        >Update</button>
                                        <button
                                            type="button"
                                            class="btn btn-info btn-sm"
                                            @click="cancelEdit"
                                        >Cancel</button>
                                    </div>
                                    <a
                                        href="javascript:;"
                                        class="table-action"
                                        title="Edit Brand"
                                        @click="edit(index)"
                                        v-if="!modifyBrand.edit"
                                    >
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                    <a
                                        href="javascript:;"
                                        class="table-action"
                                        title="View Offers"
                                        @click="viewOffers(item.id)"
                                    >
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                    <a
                                        href="javascript:;"
                                        class="table-action"
                                        title="Delete New Brand"
                                        @click="deleteNew(item.id)"
                                        v-if="!modifyBrand.edit && item.can_delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import create from "./create.vue";

export default {
    components: {
        create
    },
    data() {
        return {
            brand: {},
            modifyBrand: {
                id: null,
                edit: false
            },
            addBtn: true,
            brands: {},
            errors: {}
        };
    },
    created() {
        this.$store.commit("changeCurrentPage", "brands");
        this.$store.commit("changeCurrentMenu", "brandMenu");
        this.getBrands();
    },
    mounted() {},
    methods: {
        getBrands() {
            axios.get("/brands/").then(response => {
                this.brands = response.data;
            });
        },
        editExisting(edit_id) {
            if (this.modifyBrand.id == edit_id && this.modifyBrand.edit)
                return true;
            else return false;
        },
        edit(key) {
            this.brand = this.brands[key];
            this.modifyBrand.id = this.brands[key].id;
            this.modifyBrand.edit = true;
        },
        deleteNew(id) {
            this.$swal({
                title: "Are you sure?",
                text: "You may not undo this",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.value) {
                    axios
                        .delete("/brands/" + id)
                        .then(response => {
                            this.$store.dispatch("getBrands");
                            showNotify("success", response.data.message);
                        })
                        .catch(error => {
                            showNotify("danger", error.response.data.message);
                        });
                }
            });
        },
        cancelEdit() {
            this.$store.dispatch("getBrands");
            this.brand = {};
            this.modifyBrand.id = "";
            this.modifyBrand.edit = false;
        },
        saveEdited() {
            let formData = new FormData();
            formData.append("_method", "PUT");
            for (var key in this.brand) {
                formData.append(key, this.brand[key]);
            }

            axios
                .post("/brands/" + this.brand.id, formData)
                .then(response => {
                    console.log(response.data);
                    this.cancelEdit();
                    showNotify("success", response.data);
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    for (var prop in error.response.data.errors) {
                        showNotify("danger", error.response.data.errors[prop]);
                    }
                });
        },
        triggerImageInput(key) {
            this.$refs["logo_file_" + key][0].click();
        },
        imageChange(e) {
            this.brand.logo_file = e.target.files[0];
            this.brand.logo_src = URL.createObjectURL(this.brand.logo_file);
        },
        grabName(categories){
            var ret = '';

            if(categories){
                var count = 0;
                categories.forEach((data) => {
                    ret += data.name;
                    count!=(categories.length-1) ? ret += ', ' : ''
                    count++;
                });
            }
                
            return ret;
        },
        viewOffers(id){
            this.$router.push({ name: "offers", query:{brand_id:id}});
        }
    }
};
</script>
