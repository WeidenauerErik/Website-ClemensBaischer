<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const goKontakt = () => router.push({ name: 'kontakt' })

const galleryImages = [
  {
    src: '/images/catering-event-1.jpeg',
    alt: 'Anrichten von Vorspeisen bei einem Baischer Catering-Event',
  },
  {
    src: '/images/catering-event-2.jpeg',
    alt: 'Baischer Catering-Team beim Anrichten der Buffetplatten',
  },
  {
    src: '/images/catering-platte.jpeg',
    alt: 'Geräuchertes Fleisch mit Krautsalat, serviert bei einem Baischer Catering-Event',
  },
  {
    src: '/images/catering-dessert.jpeg',
    alt: 'Dessert mit gegrillten Pfirsichen und Mascarpone von Baischer Catering',
  },
  {
    src: '/images/catering-grill-brisket.jpeg',
    alt: 'Fleisch auf dem Grill bei einem Baischer Catering-Event',
  },
  {
    src: '/images/catering-grill-sausages.jpeg',
    alt: 'Würstel auf dem Grill, zubereitet vom Baischer Catering-Team',
  },
  {
    src: '/images/catering-grill-vegetables.jpeg',
    alt: 'Gegrilltes Gemüse auf dem Grill bei einem Baischer Catering-Event',
  },
  {
    src: '/images/catering-plating.jpeg',
    alt: 'Baischer Catering-Team beim finalen Anrichten der Vorspeisenteller',
  },
  {
    src: '/images/catering-theke.jpeg',
    alt: 'Clemens Baischer an der Frischetheke der Fleischerei',
  },
  {
    src: '/images/catering-buffet-schloss.jpeg',
    alt: 'Elegantes Buffet mit kalten Platten bei einem Baischer Catering-Event im Gewölbe',
  },
  {
    src: '/images/catering-buffet-hero.jpeg',
    alt: 'Aufgebautes Buffet mit Vorspeisenplatten und Antipasti von Baischer Catering',
  },
  {
    src: '/images/catering-buffet-modern.jpeg',
    alt: 'Aufschnitt- und Antipastiplatten auf einem gedeckten Tisch bei einem Baischer Catering-Event',
  },
  {
    src: '/images/catering-vorbereitung.jpeg',
    alt: 'Baischer Catering-Mitarbeiter beim Anrichten von Vorspeisen am Grill',
  },
]

const lightboxIndex = ref(null)
const lightboxOpen = () => lightboxIndex.value !== null

const openLightbox = (index) => {
  lightboxIndex.value = index
}

const closeLightbox = () => {
  lightboxIndex.value = null
}

const showPrev = () => {
  lightboxIndex.value = (lightboxIndex.value - 1 + galleryImages.length) % galleryImages.length
}

const showNext = () => {
  lightboxIndex.value = (lightboxIndex.value + 1) % galleryImages.length
}

const onKeydown = (e) => {
  if (!lightboxOpen()) return
  if (e.key === 'Escape') closeLightbox()
  if (e.key === 'ArrowLeft') showPrev()
  if (e.key === 'ArrowRight') showNext()
}

onMounted(() => window.addEventListener('keydown', onKeydown))
onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown)
  document.body.style.overflow = ''
})

watch(lightboxIndex, (index) => {
  document.body.style.overflow = index === null ? '' : 'hidden'
})
</script>

<template>
  <main class="catering">
    <section class="intro">
      <p class="eyebrow">Unser Angebot</p>
      <div class="intro__grid">
        <h1 class="intro__title">Catering für jeden Anlass</h1>
        <p class="intro__text">
          Ob zwanzig Gäste im Garten oder vierhundert im Festzelt: Wir stellen das Menü
          zusammen, liefern frisch und richten vor Ort an.
        </p>
      </div>
    </section>

    <section class="gallery">
      <div class="gallery__grid">
        <button
          v-for="(image, index) in galleryImages"
          :key="image.src"
          type="button"
          class="gallery__item"
          :aria-label="`Foto vergrößern: ${image.alt}`"
          @click="openLightbox(index)"
        >
          <img :src="image.src" :alt="image.alt" class="gallery__image" loading="lazy">
        </button>
      </div>
    </section>

    <Teleport to="body">
      <div
        v-if="lightboxOpen()"
        class="lightbox"
        role="dialog"
        aria-modal="true"
        @click.self="closeLightbox"
      >
        <button type="button" class="lightbox__close" aria-label="Schließen" @click="closeLightbox">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M5 5l14 14M19 5L5 19" stroke-linecap="round" />
          </svg>
        </button>

        <button type="button" class="lightbox__nav lightbox__nav--prev" aria-label="Vorheriges Foto" @click="showPrev">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <img
          :src="galleryImages[lightboxIndex].src"
          :alt="galleryImages[lightboxIndex].alt"
          class="lightbox__image"
        >

        <button type="button" class="lightbox__nav lightbox__nav--next" aria-label="Nächstes Foto" @click="showNext">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <p class="lightbox__counter">{{ lightboxIndex + 1 }} / {{ galleryImages.length }}</p>
      </div>
    </Teleport>

    <section class="cta">
      <div class="cta__row">
        <p class="cta__text">
          Sagen Sie uns Anlass, Termin und Gästezahl. Wir rechnen ein Angebot durch.
        </p>
        <button class="btn btn--dark" @click="goKontakt">Kontakt aufnehmen</button>
      </div>
    </section>
  </main>
</template>

<style scoped>
.catering {
  animation: rise 420ms cubic-bezier(.23, 1, .32, 1) both;
}

.eyebrow {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 11px;
  letter-spacing: .22em;
  text-transform: uppercase;
  color: #5f5f5f;
  margin: 0 0 24px;
}

.intro {
  max-width: 1240px;
  margin: 0 auto;
  padding: 64px 20px 40px;
  border-bottom: 1px solid rgba(84, 84, 84, .16);
}

@media (min-width: 640px) {
  .intro {
    padding: 96px 32px 64px;
  }
}

.intro__grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
  gap: 48px;
  align-items: end;
}

.intro__title {
  font-family: 'Spectral', Georgia, serif;
  font-weight: 500;
  font-size: clamp(38px, 6vw, 76px);
  line-height: 1.03;
  letter-spacing: -.02em;
  margin: 0;
  color: #3a3a3a;
  max-width: 12ch;
}

.intro__text {
  font-size: 19px;
  line-height: 1.65;
  color: #5f5f5f;
  margin: 0;
  max-width: 46ch;
}

.hero-image {
  width: 100%;
  height: min(62vh, 620px);
  object-fit: cover;
  display: block;
}

.gallery {
  max-width: 1240px;
  margin: 0 auto;
  padding: 56px 20px 8px;
}

@media (min-width: 640px) {
  .gallery {
    padding: 88px 32px 8px;
  }
}

.gallery__grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

@media (min-width: 640px) {
  .gallery__grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  }
}

.gallery__item {
  display: block;
  width: 100%;
  padding: 0;
  border: none;
  background: transparent;
  cursor: pointer;
  overflow: hidden;
}

.gallery__image {
  width: 100%;
  aspect-ratio: 3 / 4;
  object-fit: cover;
  display: block;
  transition: transform 300ms cubic-bezier(.23, 1, .32, 1);
}

.gallery__item:hover .gallery__image {
  transform: scale(1.04);
}

.lightbox {
  position: fixed;
  inset: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(20, 19, 17, .92);
  padding: 24px;
  animation: lightbox-fade 200ms ease both;
}

@keyframes lightbox-fade {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@media (prefers-reduced-motion: reduce) {
  .lightbox {
    animation: none;
  }
}

.lightbox__image {
  max-width: min(90vw, 900px);
  max-height: 85vh;
  width: auto;
  height: auto;
  display: block;
  object-fit: contain;
}

.lightbox__close {
  position: absolute;
  top: 20px;
  right: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  padding: 0;
  border: none;
  background: transparent;
  color: #efece7;
  cursor: pointer;
  opacity: .85;
  transition: opacity 160ms ease;
}

.lightbox__close:hover {
  opacity: 1;
}

.lightbox__nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  padding: 0;
  border: none;
  background: transparent;
  color: #efece7;
  cursor: pointer;
  opacity: .85;
  transition: opacity 160ms ease;
}

.lightbox__nav:hover {
  opacity: 1;
}

.lightbox__nav--prev {
  left: 12px;
}

.lightbox__nav--next {
  right: 12px;
}

@media (min-width: 640px) {
  .lightbox__nav--prev {
    left: 24px;
  }

  .lightbox__nav--next {
    right: 24px;
  }
}

.lightbox__counter {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  margin: 0;
  font-family: 'Space Grotesk', sans-serif;
  font-size: 12px;
  letter-spacing: .12em;
  color: rgba(239, 236, 231, .8);
}

.cta {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 20px 64px;
}

@media (min-width: 640px) {
  .cta {
    padding: 0 32px 96px;
  }
}

.cta__row {
  padding-top: 56px;
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  align-items: center;
  justify-content: space-between;
}

.cta__text {
  font-family: 'Spectral', Georgia, serif;
  font-size: clamp(22px, 2.4vw, 30px);
  line-height: 1.3;
  color: #3a3a3a;
  margin: 0;
  max-width: 24ch;
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

.btn--dark {
  background: #545454;
  color: #fff;
}

.btn--dark:hover {
  background: #3a3a3a;
}
</style>
