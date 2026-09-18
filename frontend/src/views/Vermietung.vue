<script setup>
import { ref, onMounted } from 'vue'
import { fetchTrailers } from '@/services/trailerApi'
import TrailerSection from '@/components/TrailerSection.vue'

const trailers = ref([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    trailers.value = await fetchTrailers()
  } catch {
    error.value = 'Unsere Fahrzeuge konnten momentan nicht geladen werden. Bitte versuchen Sie es später erneut.'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <main class="trailers">
    <section class="intro">
      <p class="eyebrow">Vermietung</p>
      <div class="intro__grid">
        <h1 class="intro__title">Fahrzeuge mieten</h1>
        <p class="intro__text">
          Unsere Fahrzeuge für Feste, Feiern und die Gastronomie. Wählen Sie Ihren Wunschzeitraum
          direkt im Kalender und senden Sie uns unverbindlich Ihre Buchungsanfrage.
        </p>
      </div>
    </section>

    <p v-if="loading" class="status-text">Wird geladen…</p>
    <p v-else-if="error" class="status-text status-text--error">{{ error }}</p>

    <TrailerSection v-else v-for="trailer in trailers" :key="trailer.slug" :trailer="trailer" />
  </main>
</template>

<style scoped>
.trailers {
  background: #efece7;
  padding-bottom: 80px;
}

.eyebrow {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 11px;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: #5f5f5f;
  max-width: 1240px;
  margin: 0 auto;
  padding: 48px 24px 0;
}

@media (min-width: 640px) {
  .eyebrow {
    padding: 56px 32px 0;
  }
}

.intro__grid {
  max-width: 1240px;
  margin: 0 auto;
  padding: 16px 24px 40px;
}

@media (min-width: 640px) {
  .intro__grid {
    padding: 16px 32px 48px;
  }
}

.intro__title {
  font-family: 'Spectral', Georgia, serif;
  font-weight: 500;
  font-size: clamp(30px, 5vw, 44px);
  color: #3a3a3a;
  margin: 0 0 16px;
}

.intro__text {
  font-size: 16px;
  line-height: 1.6;
  color: #5f5f5f;
  max-width: 640px;
  margin: 0;
}

.status-text {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 24px;
  color: #5f5f5f;
}

.status-text--error {
  color: #b0413e;
}
</style>
