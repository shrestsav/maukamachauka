<template>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Offers</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                @click="addBtn = !addBtn"
                                v-if="addBtn"
                            >
                                Add
                            </button>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                @click="addBtn = !addBtn"
                                v-if="!addBtn"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <create v-if="!addBtn"></create>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>S.No.</th>
                                <th>Title</th>
                                <th>offer Image</th>
                                <th>Description</th>
                                <th>Expires In</th>
                                <th>Related Tags</th>
                                <th>Location</th>
                                <th>Source</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <tr v-for="(item, index) in offers" v-bind:key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.title }}</td>
                                <td>
                                    <img :src="item.image_src" height="100px" />
                                </td>
                                <td><p class="desc">{{ item.description }}</p></td>
                                <td>{{ dateDiff(item.expires_in) }}</td>
                                <td>{{ grabName(item.categories) }}</td>
                                <td>{{ grabName(item.location) }}</td>
                                <td>{{ item.source }}</td>
                                <td>
                                    <a
                                        href="javascript:;"
                                        class="table-action"
                                        title="Edit offer"
                                        @click="edit(index)"
                                    >
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                    <a
                                        href="javascript:;"
                                        class="table-action"
                                        title="Delete New offer"
                                        @click="deleteNew(item.id)"
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
import edit from "./edit.vue";

export default {
    components: {
        create,
        edit
    },
    data() {
        return {
            offer: {},
            modifyOffer: {
                id: null,
                edit: false
            },
            addBtn: true,
            offers: {},
            errors: {}
        };
    },
    mounted() {
        this.$store.commit("changeCurrentPage", "offers");
        this.$store.commit("changeCurrentMenu", "offersMenu");
        this.getOffers();
    },
    methods: {
        getOffers() {
            axios.get("/offers/").then(response => {
                this.offers = response.data;
            });
        },
        edit(key) {
            this.offer = this.offers[key];
            this.modifyOffer.id = this.offers[key].id;
            this.modifyOffer.edit = true;
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
                        .delete("/offers/" + id)
                        .then(response => {
                            this.$store.dispatch("getOffers");
                            showNotify("success", response.data.message);
                        })
                        .catch(error => {
                            showNotify("danger", error.response.data.message);
                        });
                }
            });
        },
        cancelEdit() {
            this.$store.dispatch("getOffers");
            this.offer = {};
            this.modifyOffer.id = "";
            this.modifyOffer.edit = false;
        },
        saveEdited() {
            let formData = new FormData();
            formData.append("_method", "PUT");
            for (var key in this.offer) {
                formData.append(key, this.offer[key]);
            }

            axios
                .post("/offers/" + this.offer.id, formData)
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
            this.$refs["image_file_" + key][0].click();
        },
        imageChange(e) {
            this.offer.image_file = e.target.files[0];
            this.offer.image_src = URL.createObjectURL(this.offer.image_file);
        },
        dateDiff(date){
            var date = new Date(date+' UTC') //lets js know the date is in UTC format so as to convert in respective timezones accordingly
            return this.$moment(date).fromNow() // a
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
    }
};
</script>

<style>
    p.desc {
        display: inline-block;
        white-space: pre-wrap;
        width: 200px;
    }
</style>
