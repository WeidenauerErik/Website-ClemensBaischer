// Thin fetch wrapper for the trailer-rental backend API.
// All calls are same-origin (`/api/...`) — the production Apache container
// proxies that path to the Symfony backend, and the Vite dev server does the
// same via its own proxy config (see vite.config.js), so no base URL or CORS
// handling is needed here.

async function request(path, options = {}) {
  const response = await fetch(path, {
    headers: { 'Content-Type': 'application/json', ...options.headers },
    ...options,
  })

  let body = null
  const text = await response.text()
  if (text) {
    try {
      body = JSON.parse(text)
    } catch {
      body = null
    }
  }

  if (!response.ok) {
    const message = body?.detail || body?.title || `Anfrage fehlgeschlagen (${response.status}).`
    const error = new Error(message)
    error.status = response.status
    throw error
  }

  return body
}

export function fetchTrailers() {
  return request('/api/trailers')
}

export function fetchTrailer(slug) {
  return request(`/api/trailers/${encodeURIComponent(slug)}`)
}

export function fetchAvailability(slug, from, to) {
  const params = new URLSearchParams({ from, to })
  return request(`/api/trailers/${encodeURIComponent(slug)}/availability?${params}`)
}

export function submitBookingRequest(payload) {
  return request('/api/booking-requests', {
    method: 'POST',
    body: JSON.stringify(payload),
  })
}
