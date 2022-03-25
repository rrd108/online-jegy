<script>
  //import axios from 'axios'

  export default {
    name: 'Days',
  }
</script>
<template>
  <div>
    <h2>Napok</h2>
  </div>
</template>
<style scoped>
  ul {
    list-style: none;
  }
  div,
  li {
    grid-template-columns: 4fr 2fr 2fr 2fr 1fr;
  }
  input::placeholder {
    font-size: 1rem;
  }
</style>
