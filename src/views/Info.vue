<template>
  <div>
    <main>
      <div>
        <h1>Spirituális zarándoklat</h1>
        <h2>Túra jegy rendelés</h2>
        <p>
          Jegyet rendelni és ezzel együtt bejelentkezni úgy tudsz, hogy
          kiválasztod a látogatásod dátumát
          <strong>(jelenleg csak szombatonként)</strong> és idejét
          <strong>(jelenleg csak de 11 órát)</strong>, megadod a személyes
          adataidat, majd a &quot;Megrendelés&quot; gombra kattintás után
          bankkártyával kifizeted a jegy összegét.
        </p>
        <h2>A túra tartalma</h2>
        <p>
          A spirituális zarándoklat programra a templom épületében várunk
          szombaton, 11 órakor. Amikor megérkezel Krisna-völgy recepciójához
          autóval, két lehetőséged van. Vagy az elefántok előtti parkolóban
          hagyod az autódat és besétálsz a templomig Krisna-völgy térkövezett
          főútján, a parkok, dísztavak mentén egyenesen haladva, ez 20 perc
          gyaloglást jelent. Vagy behajtasz egyenesen a templomépület előtti
          Gauranga térig autóval. Februárban benti helyszíneinken várunk
          limitált létszámú programjainkkal: szentélylátogatással, egy
          gondolatébresztő, interaktív előadással, ajándékbolt látogatási
          lehetőséggel és egy elvitelre csomagolt, tradicionális indiai ebéddel.
          Az országos egészségvédelmi intézkedések szerint a templomépületben a
          program során orrot-szájat eltakaró maszk viselése szükséges.
          Kézfertőtlenítési lehetőséget és toalettet a templom épületében
          biztosítunk. A külső helyszínek a program előtt és után egyénileg
          látogathatóak.
        </p>
        <h2>Általános szerződési feltételek</h2>
        <p>
          A kiválasztott jegyek nem válthatóak vissza és csak a kiválasztott
          időpontban használhatóak fel. Kérjük pontosan érkezz a találkozási
          ponthoz, a templomépület elé, hogy időben tudjunk kezdeni.
        </p>
        <h2>Adattovábbítási nyilatkozat</h2>
        <p>
          A rendelés leadásával a honlap használója tudomásul veszi, hogy a Vénu
          Kft (8699 Somogyvámos Fő u. 38.) adatkezelő által a
          jegy.krisnavolgy.hu felhasználói adatbázisában tárolt alábbi személyes
          adataim átadásra kerülnek az OTP Mobil Kft., mint adatfeldolgozó
          részére. <br />
          Az adatkezelő által továbbított adatok köre az alábbi: a vásárló email
          címe .<br />
          Az adatfeldolgozó által végzett adatfeldolgozási tevékenység jellege
          és célja a SimplePay Adatkezelési tájékoztatóban, az alábbi linken
          tekinthető meg:
          <a href="http://simplepay.hu/vasarlo-aff"
            >http://simplepay.hu/vasarlo-aff</a
          >
        </p>
      </div>
    </main>
    <AppFooter />
  </div>
</template>

<script>
import AppFooter from '@/components/AppFooter'

export default {
  name: 'Info',
  components: { AppFooter },
}
</script>

<style scoped>
main div {
  padding: 1rem;
}
h1 {
  margin-top: 0;
}
</style>
