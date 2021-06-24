<template>
  <header>
    <div class="row">
      <div class="column small-2 info" @click="showInfo=showInfo?false:true">
        <font-awesome-icon icon="info-circle" size="lg" title="Infók mutatása / rejtése" />
        <p>Info</p>
      </div>
      <h1 class="column small-7 text-center">Spirituális zarándoklat</h1>
      <div class="column small-3">
        <img src="../assets/logo.png" />
      </div>
    </div>
    <div class="row">
      <div class="column-small-12" v-show="showInfo">
        <h1><font-awesome-icon icon="info-circle" size="lg" class="column small-2 info" /> Információk</h1>
        <h2>Túra jegy rendelés</h2>
        <p>Jegyet rendelni és ezzel együtt bejelentkezni úgy tudsz, hogy kiválasztod a látogatásod
          dátumát <strong>(jelenleg csak szerdánként és szombatonként)</strong> és idejét <strong>(jelenleg csak de 11 órát)</strong>, megadod a személyes adataidat, majd a &quot;Megrendelés&quot; gombra kattintás után bankkártyával kifizeted a jegy összegét.</p>
        <h2>A túra tartalma</h2>
        <p>Az exkluzív, 3 órás, Spirituális Zarándoklat Túra Krisna-völgy recepciójáról indul (az elefántoknál) szerdán és szombaton, 11 órakor (javasolt érkezés: 10:50). Vendégvezetővel kísért programunk során, Krisna-völgy főbb látnivalóit bejárva – díszparkok, dísztavak, közösségi iskola épülete (kívülről), tehénvédelmi központ, vegyszermentes zöldségkertészetek, indiai szentély – beszélgetünk, mind a helyi látnivalókról mind az aktuálisan meghirdetett tematikus spirituális témáról. A program regisztrációs díja magában foglalja a Krisna-völgyi belépőjegyet és egy ajurvédikus ebédet is, mely Krisna-völgy szezonális, vegyszermentes terményeiből készül.</p>
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
