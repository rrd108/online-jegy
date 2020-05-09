<template>
  <header>
    <div class="row">
      <div class="column small-2 info" @click="showInfo=showInfo?false:true">
        <font-awesome-icon icon="info-circle" size="lg" title="Infók mutatása / rejtése" />
        <p>Info</p>
      </div>
      <h1 class="column small-7 text-center">Jegyrendelés</h1>
      <div class="column small-3">
        <img src="../assets/logo.png" />
      </div>
    </div>
    <div class="row">
      <div class="column-small-12" v-show="showInfo">
        <h1><font-awesome-icon icon="info-circle" size="lg" class="column small-2 info" /> Jegy információk</h1>
        <p>
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Earum suscipit, sint, ea saepe ex debitis dicta eius, magni nihil excepturi culpa. Atque asperiores iure, repudiandae aliquam incidunt quidem eaque nulla temporibus, facere voluptates exercitationem eveniet, repellat dolores inventore fugiat harum similique recusandae illum dicta ad doloribus alias tenetur. Est, vero.
        </p>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: "AppHeader",

  data() {
    return {
      showInfo : false
    }
  },
};
</script>

<style scoped>
header {
  padding-top: 2vh;
}
header .column {
  padding-left: 0;
  padding-right: 0;
}
h1 {
  font-family: "Quicksand", sans-serif;
}
.info {
  cursor: pointer;
}
p {
  margin: 0
}
</style>
