<script setup>
import { ref } from 'vue'
import AvailabilityCalendar from '@/components/AvailabilityCalendar.vue'
import BookingRequestForm from '@/components/BookingRequestForm.vue'

defineProps({
  trailer: { type: Object, required: true },
})

const range = ref({ start: '', end: '' })

function resetRange() {
  range.value = { start: '', end: '' }
}

function onImageError(event) {
  event.target.classList.add('trailer-section__image--fallback')
}
</script>

<template>
  <section class="trailer-section" :id="trailer.slug">
    <div class="trailer-section__header">
      <h2 class="trailer-section__title">{{ trailer.name }}</h2>
      <p v-if="trailer.description" class="trailer-section__description">{{ trailer.description }}</p>
    </div>

    <div class="trailer-section__media">
      <img
          v-if="trailer.imageUrl"
          :src="trailer.imageUrl"
          :alt="trailer.name"
          class="trailer-section__image"
          loading="lazy"
          @error="onImageError"
      >
      <div v-else class="trailer-section__placeholder">Foto folgt in Kürze</div>
    </div>

    <div class="trailer-section__grid">
      <div class="trailer-section__panel">
        <h3 class="trailer-section__panel-title">Verfügbarkeit</h3>
        <AvailabilityCalendar :trailer-slug="trailer.slug" v-model:range="range" />
      </div>

      <div class="trailer-section__panel">
        <h3 class="trailer-section__panel-title">Buchungsanfrage</h3>
        <BookingRequestForm
          :trailer-slug="trailer.slug"
          :trailer-name="trailer.name"
          :start-date="range.start"
          :end-date="range.end"
          @reset-range="resetRange"
        />
      </div>
    </div>
  </section>
</template>

<style scoped>
.trailer-section {
  max-width: 1240px;
  margin: 0 auto 64px;
  padding: 0 24px;
}

@media (min-width: 640px) {
  .trailer-section {
    padding: 0 32px;
  }
}

.trailer-section__media {
  aspect-ratio: 21 / 9;
  border-radius: 12px;
  overflow: hidden;
  background: #ddd7cb;
  margin-bottom: 24px;
}

.trailer-section__image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}

.trailer-section__image--fallback {
  visibility: hidden;
}

.trailer-section__placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #7a756c;
  font-size: 14px;
}

.trailer-section__header {
  margin-bottom: 20px;
}

.trailer-section__title {
  font-family: 'Spectral', Georgia, serif;
  font-weight: 500;
  font-size: 26px;
  color: #3a3a3a;
  margin: 0 0 8px;
}

.trailer-section__description {
  color: #5f5f5f;
  margin: 0;
  max-width: 640px;
  line-height: 1.6;
}

.trailer-section__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  align-items: start;
}

@media (max-width: 860px) {
  .trailer-section__grid {
    grid-template-columns: 1fr;
  }
}

.trailer-section__panel-title {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: #5f5f5f;
  margin: 0 0 12px;
}
</style>
