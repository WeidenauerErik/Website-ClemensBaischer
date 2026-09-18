<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { fetchAvailability } from '@/services/trailerApi'

const props = defineProps({
  trailerSlug: { type: String, required: true },
})

const range = defineModel('range', { default: () => ({ start: '', end: '' }) })

const WEEKDAY_LABELS = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
const MONTH_LABELS = [
  'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
  'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember',
]

const today = new Date()
today.setHours(0, 0, 0, 0)

const viewYear = ref(today.getFullYear())
const viewMonth = ref(today.getMonth()) // 0-based, first of the two displayed months

const loading = ref(false)
const error = ref('')
const busyDates = reactive(new Set()) // ISO date strings
const hoverDate = ref(null)

function toIso(date) {
  return [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), String(date.getDate()).padStart(2, '0')].join('-')
}

function parseIso(value) {
  const [y, m, d] = value.split('-').map(Number)
  return new Date(y, m - 1, d)
}

async function loadAvailability() {
  loading.value = true
  error.value = ''
  try {
    const from = new Date(viewYear.value, viewMonth.value, 1)
    const to = new Date(viewYear.value, viewMonth.value + 2, 0)
    const data = await fetchAvailability(props.trailerSlug, toIso(from), toIso(to))
    busyDates.clear()
    for (const r of data.busy) {
      let cursor = parseIso(r.start)
      const end = parseIso(r.end)
      while (cursor <= end) {
        busyDates.add(toIso(cursor))
        cursor = new Date(cursor.getFullYear(), cursor.getMonth(), cursor.getDate() + 1)
      }
    }
  } catch {
    error.value = 'Kalender konnte nicht geladen werden.'
  } finally {
    loading.value = false
  }
}

function buildMonth(year, month) {
  const firstOfMonth = new Date(year, month, 1)
  const firstWeekday = (firstOfMonth.getDay() + 6) % 7 // Monday-first
  const daysInMonth = new Date(year, month + 1, 0).getDate()

  const cells = []
  for (let i = 0; i < firstWeekday; i++) cells.push(null)
  for (let day = 1; day <= daysInMonth; day++) {
    const date = new Date(year, month, day)
    cells.push({
      date,
      iso: toIso(date),
      day,
      isPast: date < today,
    })
  }
  while (cells.length % 7 !== 0) cells.push(null)

  const weeks = []
  for (let i = 0; i < cells.length; i += 7) weeks.push(cells.slice(i, i + 7))

  return {
    label: `${MONTH_LABELS[month]} ${year}`,
    weeks,
  }
}

const months = computed(() => {
  const first = buildMonth(viewYear.value, viewMonth.value)
  const secondMonth = viewMonth.value === 11 ? 0 : viewMonth.value + 1
  const secondYear = viewMonth.value === 11 ? viewYear.value + 1 : viewYear.value
  const second = buildMonth(secondYear, secondMonth)
  return [first, second]
})

function goPrevMonth() {
  if (viewMonth.value === 0) {
    viewMonth.value = 11
    viewYear.value -= 1
  } else {
    viewMonth.value -= 1
  }
}

function goNextMonth() {
  if (viewMonth.value === 11) {
    viewMonth.value = 0
    viewYear.value += 1
  } else {
    viewMonth.value += 1
  }
}

function rangeHasBusyBetween(startIso, endIso) {
  let cursor = parseIso(startIso)
  const end = parseIso(endIso)
  while (cursor <= end) {
    if (busyDates.has(toIso(cursor))) return true
    cursor = new Date(cursor.getFullYear(), cursor.getMonth(), cursor.getDate() + 1)
  }
  return false
}

function cellState(cell) {
  if (!cell) return {}
  const isBusy = busyDates.has(cell.iso)
  const isDisabled = cell.isPast || isBusy
  const isStart = range.value.start === cell.iso
  const isEnd = range.value.end === cell.iso
  const hasRange = range.value.start && range.value.end
  const inSelectedRange = hasRange && cell.iso > range.value.start && cell.iso < range.value.end
  const inHoverPreview = !hasRange && range.value.start && hoverDate.value
    && cell.iso > range.value.start
    && cell.iso <= hoverDate.value
  return { isBusy, isDisabled, isStart, isEnd, inSelectedRange, inHoverPreview }
}

function onDayClick(cell) {
  if (!cell) return
  const state = cellState(cell)
  if (state.isDisabled) return

  const hasCompleteRange = range.value.start && range.value.end

  if (!range.value.start || hasCompleteRange) {
    range.value = { start: cell.iso, end: '' }
    return
  }

  if (cell.iso <= range.value.start) {
    range.value = { start: cell.iso, end: '' }
    return
  }

  if (rangeHasBusyBetween(range.value.start, cell.iso)) {
    error.value = 'Der gewählte Zeitraum enthält bereits belegte Tage. Bitte anderen Zeitraum wählen.'
    range.value = { start: cell.iso, end: '' }
    return
  }

  error.value = ''
  range.value = { start: range.value.start, end: cell.iso }
}

function resetSelection() {
  range.value = { start: '', end: '' }
}

watch([viewYear, viewMonth], loadAvailability)
onMounted(loadAvailability)
</script>

<template>
  <div class="cal">
    <div class="cal__header">
      <button type="button" class="cal__nav" aria-label="Vorheriger Monat" @click="goPrevMonth">‹</button>
      <div class="cal__titles">
        <span v-for="month in months" :key="month.label" class="cal__title">{{ month.label }}</span>
      </div>
      <button type="button" class="cal__nav" aria-label="Nächster Monat" @click="goNextMonth">›</button>
    </div>

    <p v-if="error" class="cal__error">{{ error }}</p>

    <div class="cal__months" :class="{ 'cal__months--loading': loading }">
      <div v-for="(month, mi) in months" :key="mi" class="cal__month">
        <div class="cal__weekdays">
          <span v-for="label in WEEKDAY_LABELS" :key="label">{{ label }}</span>
        </div>
        <div class="cal__grid">
          <template v-for="(week, wi) in month.weeks" :key="wi">
            <button
              v-for="(cell, ci) in week"
              :key="ci"
              type="button"
              class="cal__day"
              :class="{
                'cal__day--empty': !cell,
                ...(cell ? {
                  'cal__day--disabled': cellState(cell).isDisabled,
                  'cal__day--start': cellState(cell).isStart,
                  'cal__day--end': cellState(cell).isEnd,
                  'cal__day--in-range': cellState(cell).inSelectedRange,
                  'cal__day--hover-preview': cellState(cell).inHoverPreview,
                } : {}),
              }"
              :disabled="!cell || cellState(cell).isDisabled"
              :tabindex="cell ? 0 : -1"
              @click="onDayClick(cell)"
              @mouseenter="cell && (hoverDate = cell.iso)"
              @mouseleave="hoverDate = null"
            >
              {{ cell ? cell.day : '' }}
            </button>
          </template>
        </div>
      </div>
    </div>

    <div class="cal__footer">
      <div class="cal__legend">
        <span><span class="cal__swatch cal__swatch--free"></span> frei</span>
        <span><span class="cal__swatch cal__swatch--busy"></span> belegt</span>
      </div>
      <button v-if="range.start" type="button" class="cal__reset" @click="resetSelection">Zurücksetzen</button>
    </div>
  </div>
</template>

<style scoped>
.cal {
  background: #fff;
  border: 1px solid #e7e7e7;
  border-radius: 12px;
  padding: 20px;
}

.cal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.cal__titles {
  display: flex;
  gap: 24px;
  flex: 1;
  justify-content: center;
}

.cal__title {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 13px;
  letter-spacing: .04em;
  color: #003b95;
  text-align: center;
  flex: 1;
}

.cal__nav {
  border: 1px solid #e7e7e7;
  background: #fff;
  font-size: 16px;
  line-height: 1;
  color: #0071c2;
  cursor: pointer;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  flex: none;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cal__nav:hover {
  background: #e4f0fc;
  border-color: #0071c2;
}

.cal__error {
  color: #a4262c;
  background: #fdecea;
  border-radius: 6px;
  padding: 8px 10px;
  font-size: 12.5px;
  margin: 0 0 12px;
}

.cal__months {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  transition: opacity 150ms ease;
}

.cal__months--loading {
  opacity: .5;
}

@media (max-width: 620px) {
  .cal__months {
    grid-template-columns: 1fr;
  }

  .cal__month:nth-child(2) {
    display: none;
  }

  .cal__title:nth-child(2) {
    display: none;
  }
}

.cal__weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  margin-bottom: 4px;
}

.cal__weekdays span {
  text-align: center;
  font-family: 'Space Grotesk', sans-serif;
  font-size: 10px;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: #9a958c;
}

.cal__grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
}

.cal__day {
  aspect-ratio: 1;
  border: none;
  background: transparent;
  font: inherit;
  font-size: 12.5px;
  color: #262626;
  cursor: pointer;
  border-radius: 6px;
  transition: background 120ms ease, color 120ms ease;
}

.cal__day:hover:not(.cal__day--disabled):not(.cal__day--empty) {
  background: #e4f0fc;
}

.cal__day--empty {
  cursor: default;
}

.cal__day--disabled {
  color: #cfcac0;
  text-decoration: line-through;
  cursor: not-allowed;
}

.cal__day--in-range,
.cal__day--hover-preview {
  background: #e4f0fc;
  border-radius: 0;
}

.cal__day--start,
.cal__day--end {
  background: #0071c2;
  color: #fff;
  font-weight: 700;
}

.cal__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 16px;
}

.cal__legend {
  display: flex;
  gap: 16px;
  font-size: 12px;
  color: #6b6b6b;
}

.cal__legend span {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.cal__swatch {
  width: 10px;
  height: 10px;
  border-radius: 3px;
  display: inline-block;
}

.cal__swatch--free {
  background: #fff;
  border: 1px solid rgba(0, 0, 0, .2);
}

.cal__swatch--busy {
  background: #f0eee9;
}

.cal__reset {
  border: none;
  background: transparent;
  color: #0071c2;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  padding: 4px 6px;
}

.cal__reset:hover {
  text-decoration: underline;
}
</style>
