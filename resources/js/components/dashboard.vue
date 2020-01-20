<template>
  <div class="vue-component-body">
    
  </div>
</template>


<script>

  export default{
    data(){
      return{
        errors:{},
      }
    },
    created(){
      this.$store.commit('changeCurrentPage', 'dashboard')
      this.$store.commit('changeCurrentMenu', 'dashboardMenu')
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
  .table td, .table th {
    padding: 0.8rem;
  }
  .nav-pills .nav-item:not(:last-child) {
    padding-right: unset;
  }
  .nav-pills .nav-link {
    border-radius: unset;
  }
  .nav-wrapper{
    padding: unset;
  }
  .status_count{
    padding: 2px 7px;
    border-radius: 17px;
    background: #F44336;
    position: relative;
    left: 25px;
    color: white;
  }
  .notification_count{
    padding: 1px 7px 6px 7px;
    border-radius: 17px;
    background: #7d8a92;
    position: relative;
    top: -7px;
  }
  .modal-fullscreen{
    max-width: unset;
    width: 90%;
  }
 /* body{
    zoom:90%;
  }*/
  .modal-backdrop {
      width: 100%;
      height: 100%;
  }
  .banner_images{
    width: 274px; 
    overflow: hidden;
  }

  .totalOrders .mx-calendar-icon, 
  .customerSigedup .mx-calendar-icon, 
  .totalSales .mx-calendar-icon, 
  .totalOrders .mx-input-wrapper input,
  .customerSigedup .mx-input-wrapper input, 
  .totalSales .mx-input-wrapper input,
  .totalOrders select, 
  .customerSigedup select, 
  .totalSales select
  {
    color: #fff;
  }
  .totalOrders select option, 
  .customerSigedup select option, 
  .totalSales select option
  {
    color: #000;
  }

</style>