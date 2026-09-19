<script setup>
import { reactive, ref, computed } from 'vue'
import { submitBookingRequest } from '@/services/trailerApi'

const props = defineProps({
  trailerSlug: { type: String, required: true },
  trailerName: { type: String, required: true },
  startDate: { type: String, default: '' },
  endDate: { type: String, default: '' },
})

const emit = defineEmits(['reset-range'])

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  startTime: '',
  endTime: '',
  message: '',
  website: '', // honeypot; stays empty for real visitors
})

const hourOptions = Array.from({ length: 15 }, (_, i) => {
  const hour = String(i + 8).padStart(2, '0') // 08:00 – 22:00
  return `${hour}:00`
})

const status = ref('idle') // idle | submitting | success | error
const errorMessage = ref('')

const hasRange = computed(() => !!(props.startDate && props.endDate))

function formatDate(iso) {
  if (!iso) return ''
  const [y, m, d] = iso.split('-')
  return `${d}.${m}.${y}`
}

const nights = computed(() => {
  if (!hasRange.value) return 0
  const start = new Date(props.startDate)
  const end = new Date(props.endDate)
  return Math.round((end - start) / 86400000)
})

async function onSubmit() {
  if (!hasRange.value) {
    status.value = 'error'
    errorMessage.value = 'Bitte wählen Sie zuerst einen Zeitraum im Kalender aus.'
    return
  }

  status.value = 'submitting'
  errorMessage.value = ''

  try {
    await submitBookingRequest({
      trailerSlug: props.trailerSlug,
      firstName: form.firstName,
      lastName: form.lastName,
      email: form.email,
      phone: form.phone,
      message: form.message || null,
      startDate: props.startDate,
      endDate: props.endDate,
      startTime: form.startTime || null,
      endTime: form.endTime || null,
      website: form.website || null,
    })
    status.value = 'success'
  } catch (e) {
    status.value = 'error'
    errorMessage.value = e.message || 'Die Anfrage konnte nicht gesendet werden. Bitte versuchen Sie es erneut.'
  }
}
</script>

<template>
  <div class="booking-form">
    <div v-if="status === 'success'" class="booking-form__success">
      <p class="booking-form__success-title">Anfrage gesendet</p>
      <p>
        Vielen Dank für Ihre Anfrage für den {{ trailerName }} ({{ formatDate(startDate) }} –
        {{ formatDate(endDate) }}). Wir melden uns so schnell wie möglich bei Ihnen, um die
        Buchung zu bestätigen.
      </p>
    </div>

    <form v-else class="booking-form__fields" @submit.prevent="onSubmit">
      <div class="booking-form__range" :class="{ 'booking-form__range--empty': !hasRange }">
        <div>
          <span class="booking-form__range-label">Check-in</span>
          <strong>{{ startDate ? formatDate(startDate) : '— wählen —' }}</strong>
        </div>
        <div class="booking-form__range-arrow">→</div>
        <div>
          <span class="booking-form__range-label">Check-out</span>
          <strong>{{ endDate ? formatDate(endDate) : '— wählen —' }}</strong>
        </div>
        <button
          v-if="hasRange"
          type="button"
          class="booking-form__range-reset"
          @click="emit('reset-range')"
        >
          Ändern
        </button>
      </div>
      <p v-if="hasRange" class="booking-form__nights">{{ nights }} {{ nights === 1 ? 'Nacht' : 'Nächte' }}</p>
      <p v-else class="booking-form__nights booking-form__nights--hint">
        Bitte im Kalender links Anreise- und Abreisetag auswählen.
      </p>

      <div class="booking-form__row">
        <label class="booking-form__field">
          <span>Uhrzeit Abholung</span>
          <select v-model="form.startTime">
            <option value="">Beliebig</option>
            <option v-for="hour in hourOptions" :key="hour" :value="hour">{{ hour }} Uhr</option>
          </select>
        </label>
        <label class="booking-form__field">
          <span>Uhrzeit Rückgabe</span>
          <select v-model="form.endTime">
            <option value="">Beliebig</option>
            <option v-for="hour in hourOptions" :key="hour" :value="hour">{{ hour }} Uhr</option>
          </select>
        </label>
      </div>

      <div class="booking-form__row">
        <label class="booking-form__field">
          <span>Vorname*</span>
          <input v-model="form.firstName" type="text" required autocomplete="given-name">
        </label>
        <label class="booking-form__field">
          <span>Nachname*</span>
          <input v-model="form.lastName" type="text" required autocomplete="family-name">
        </label>
      </div>

      <div class="booking-form__row">
        <label class="booking-form__field">
          <span>E-Mail*</span>
          <input v-model="form.email" type="email" required autocomplete="email">
        </label>
        <label class="booking-form__field">
          <span>Telefon*</span>
          <input v-model="form.phone" type="tel" required autocomplete="tel">
        </label>
      </div>

      <label class="booking-form__field">
        <span>Nachricht</span>
        <textarea v-model="form.message" rows="3" placeholder="Kurze Info zu Ihrem Anlass (optional)"></textarea>
      </label>

      <!-- Honeypot: hidden from real visitors via CSS, bots often fill every field. -->
      <label class="booking-form__honeypot" aria-hidden="true">
        <span>Website</span>
        <input v-model="form.website" type="text" tabindex="-1" autocomplete="off">
      </label>

      <p v-if="status === 'error'" class="booking-form__error">{{ errorMessage }}</p>

      <button type="submit" class="booking-form__submit" :disabled="status === 'submitting' || !hasRange">
        {{ status === 'submitting' ? 'Wird gesendet…' : 'Anfrage senden' }}
      </button>

      <p class="booking-form__note">
        Dies ist eine unverbindliche Buchungsanfrage. Wir bestätigen den Termin nach Prüfung
        der Verfügbarkeit persönlich per E-Mail oder Telefon.
      </p>
    </form>
  </div>
</template>

<style scoped>
.booking-form {
  background: #fff;
  border: 1px solid #e7e7e7;
  border-radius: 12px;
  padding: 24px;
}

.booking-form__range {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #e4f0fc;
  border: 1px solid #b6d7f5;
  border-radius: 8px;
  padding: 12px 14px;
  margin-bottom: 4px;
}

.booking-form__range--empty {
  background: #f5f5f5;
  border-color: #e7e7e7;
}

.booking-form__range > div:not(.booking-form__range-arrow) {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.booking-form__range strong {
  font-size: 14px;
  color: #003b95;
}

.booking-form__range--empty strong {
  color: #6b6b6b;
  font-weight: 600;
}

.booking-form__range-label {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 10px;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: #6b6b6b;
}

.booking-form__range-arrow {
  color: #6b6b6b;
}

.booking-form__range-reset {
  margin-left: auto;
  border: none;
  background: transparent;
  color: #0071c2;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
}

.booking-form__range-reset:hover {
  text-decoration: underline;
}

.booking-form__nights {
  font-size: 12.5px;
  color: #6b6b6b;
  margin: 0 0 16px;
}

.booking-form__nights--hint {
  color: #9a958c;
}

.booking-form__fields {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.booking-form__row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

@media (max-width: 480px) {
  .booking-form__row {
    grid-template-columns: 1fr;
  }
}

.booking-form__field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
  color: #5f5f5f;
}

.booking-form__field input,
.booking-form__field select,
.booking-form__field textarea {
  font: inherit;
  padding: 10px 12px;
  border: 1px solid #d8d4cd;
  border-radius: 6px;
  color: #2f2f2f;
  resize: vertical;
  background: #fff;
}

.booking-form__field input:focus,
.booking-form__field select:focus,
.booking-form__field textarea:focus {
  outline: 2px solid #0071c2;
  outline-offset: 1px;
}

.booking-form__honeypot {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip: rect(0 0 0 0);
  white-space: nowrap;
}

.booking-form__error {
  color: #a4262c;
  background: #fdecea;
  border-radius: 6px;
  padding: 10px 12px;
  font-size: 13px;
  margin: 0;
}

.booking-form__submit {
  align-self: flex-start;
  background: #0071c2;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 12px 28px;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 13px;
  letter-spacing: .04em;
  cursor: pointer;
  transition: background 160ms ease;
}

.booking-form__submit:hover:not(:disabled) {
  background: #003b95;
}

.booking-form__submit:disabled {
  opacity: .5;
  cursor: not-allowed;
}

.booking-form__note {
  font-size: 12px;
  color: #9a958c;
  margin: 0;
}

.booking-form__success {
  padding: 8px;
}

.booking-form__success-title {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  color: #3a3a3a;
  margin: 0 0 8px;
}
</style>
