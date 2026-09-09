<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()
const goKontakt = () => router.push({ name: 'kontakt' })
const goUeber = () => router.push({ name: 'ueber-uns' })
</script>

<template>
  <main class="home">
    <section class="hero">
      <img src="/images/catering.jpeg" alt="Buffet von Baischer Catering" class="hero__image">
      <div class="hero__overlay"></div>
      <div class="hero__content">
        <p class="eyebrow eyebrow--on-dark">Neukirchen an der Enknach · Oberösterreich</p>
        <h1 class="hero__title">Catering für jedes Event</h1>
        <div class="hero__row">
          <p class="hero__text">
            Kalte Platten, Buffets und Festküchen-Betreuung aus eigener Fleischerei. Vom
            Firmenfest bis zur Hochzeit — zubereitet, geliefert, angerichtet.
          </p>
          <button class="btn btn--light" @click="goKontakt">Angebot anfragen</button>
        </div>
      </div>
    </section>

    <section class="about">
      <div class="about__grid">
        <div>
          <p class="eyebrow">Hinter dem Namen</p>
          <h2 class="about__title">Clemens Baischer</h2>
          <p class="about__text">
            Fleischer aus Leidenschaft, für Österreich bei den EuroSkills am Start. Dieselbe
            Sorgfalt, die ein Wettbewerb verlangt, steckt in jeder Platte, die unser Haus
            verlässt.
          </p>
          <button class="link-btn" @click="goUeber">Mehr über uns</button>
        </div>
        <img src="/images/euroskills.jpeg" alt="Clemens Baischer bei den EuroSkills" class="about__image">
      </div>
    </section>
  </main>
</template>

<style scoped>
.home {
  animation: rise 420ms cubic-bezier(.23, 1, .32, 1) both;
}

.eyebrow {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 11px;
  letter-spacing: .22em;
  text-transform: uppercase;
  color: #5f5f5f;
  margin: 0 0 22px;
}

.eyebrow--on-dark {
  color: #ffffff;
  margin: 0 0 26px;
  animation: rise 500ms cubic-bezier(.23, 1, .32, 1) both;
}

.hero {
  position: relative;
  min-height: 78vh;
  display: flex;
  align-items: flex-end;
  overflow: hidden;
  background: #3a3a3a;
}

.hero__image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: .62;
}

.hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(45, 45, 45, .62) 0%, rgba(45, 45, 45, .28) 45%, rgba(38, 38, 38, .9) 100%);
}

.hero__content {
  position: relative;
  max-width: 1240px;
  margin: 0 auto;
  padding: 120px 20px 48px;
  width: 100%;
  box-sizing: border-box;
}

@media (min-width: 640px) {
  .hero__content {
    padding: 180px 32px 72px;
  }
}

.hero__title {
  font-family: 'Spectral', Georgia, serif;
  font-weight: 500;
  font-size: clamp(42px, 7vw, 92px);
  line-height: 1.02;
  letter-spacing: -.02em;
  color: #fff;
  margin: 0;
  max-width: 13ch;
  text-wrap: pretty;
  animation: rise 560ms 60ms cubic-bezier(.23, 1, .32, 1) both;
}

.hero__row {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  justify-content: space-between;
  gap: 32px;
  margin-top: 36px;
  animation: rise 560ms 120ms cubic-bezier(.23, 1, .32, 1) both;
}

.hero__text {
  font-size: clamp(17px, 1.7vw, 21px);
  line-height: 1.55;
  color: rgba(255, 255, 255, .9);
  max-width: 44ch;
  margin: 0;
}

.btn {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 14px;
  letter-spacing: .02em;
  padding: 17px 34px;
  border: none;
  cursor: pointer;
  transition: transform 160ms cubic-bezier(.23, 1, .32, 1), background 160ms ease;
}

.btn:active {
  transform: scale(.97);
}

.btn--light {
  background: #efece7;
  color: #2f2f2f;
}

.btn--light:hover {
  background: #fff;
}

.about {
  max-width: 1240px;
  margin: 0 auto;
  padding: 64px 20px;
}

@media (min-width: 640px) {
  .about {
    padding: 110px 32px;
  }
}

.about__grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
  gap: 40px;
  align-items: center;
}

@media (min-width: 640px) {
  .about__grid {
    gap: 64px;
  }
}

.about__title {
  font-family: 'Spectral', Georgia, serif;
  font-weight: 500;
  font-size: clamp(30px, 4vw, 52px);
  line-height: 1.08;
  letter-spacing: -.015em;
  margin: 0 0 24px;
  color: #3a3a3a;
  max-width: 14ch;
}

.about__text {
  font-size: 18px;
  line-height: 1.7;
  color: #5f5f5f;
  margin: 0 0 32px;
  max-width: 46ch;
}

.about__image {
  width: 100%;
  display: block;
}

.link-btn {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 13px;
  letter-spacing: .02em;
  padding: 0 0 6px;
  border: none;
  border-bottom: 1px solid #545454;
  background: transparent;
  color: #3a3a3a;
  cursor: pointer;
  transition: opacity 160ms ease;
}

.link-btn:hover {
  opacity: .6;
}
</style>
