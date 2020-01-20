<template>
  <div class="vue_menus">

  </div>
</template>

<script type="text/javascript">
  
  export default{
    data(){
      return{
        errors:{},
        clients:{},
      }
    },
    computed: {
      currentMenu(){
        return this.$store.state.currentMenu;
      }
    },
    mounted(){
      
    },
    methods:{
    }
  }
</script>