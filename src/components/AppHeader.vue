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
        <h2>Általános szerződési feltételek</h2>
        <h2>Adattovábbítási nyilatkozat</h2>
        <p>
          A rendelés leadásával a honlap használója tudomásul veszi, hogy a Vénu Kft (8699 Somogyvámos Fő u. 38.) adatkezelő által a jegy.krisnavolgy.hu felhasználói adatbázisában tárolt alábbi személyes adataim átadásra kerülnek az OTP Mobil Kft., mint adatfeldolgozó részére. <br>
          Az adatkezelő által továbbított adatok köre az alábbi: a vásárló email címe .<br>
          Az adatfeldolgozó által végzett adatfeldolgozási tevékenység jellege és célja a SimplePay Adatkezelési tájékoztatóban, az alábbi linken tekinthető meg: <a href="http://simplepay.hu/vasarlo-aff">http://simplepay.hu/vasarlo-aff</a>
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
