import type {
  DashboardAlerts,
  DashboardStats,
  PaginatedResponse,
  RawReservation,
  WeeklyAvailabilityDay,
  WeeklyAvailabilityResponse,
} from '../type'

export function useDashboardService() {
  const { apiFetch } = useApi()

  async function getStats() {
    const response = await apiFetch<{ data: DashboardStats }>('/dashboard/stats')
    return response.data
  }

  async function getAlerts() {
    const response = await apiFetch<{ data: DashboardAlerts }>('/dashboard/alerts')
    return response.data
  }

  async function getUpcomingReservations() {
    const today = new Date().toISOString().slice(0, 10)

    const response = await apiFetch<PaginatedResponse<RawReservation>>('/reservations', {
      query: {
        from_date: today,
        status: 'CONFIRMEE',
        sort: 'date_reservation',
        direction: 'asc',
        per_page: 10,
      },
    })

    return response.data
  }

  async function getTodayAvailability(): Promise<WeeklyAvailabilityDay | null> {
    const today = new Date().toISOString().slice(0, 10)

    const response = await apiFetch<{ data: WeeklyAvailabilityResponse }>('/availability/week', {
      query: { date: today },
    })

    return response.data.jours.find(jour => jour.date === today) ?? null
  }

  return { getStats, getAlerts, getUpcomingReservations, getTodayAvailability }
}
