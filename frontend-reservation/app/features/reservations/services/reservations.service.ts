import type {
  AvailableEquipment,
  CreateReservationPayload,
  PaginatedResponse,
  RawReservation,
  ReservationCounts,
  ReservationSummary,
  UpdateReservationPayload,
} from '../type'

export interface GetReservationsParams {
  date?: string
  room_id?: number
  status?: 'CONFIRMEE' | 'ANNULEE' | 'EN_COURS' | 'PASSEE'
  user_id?: number
  page?: number
  per_page?: number
  sort?: 'date_reservation'
  direction?: 'asc' | 'desc'
}

export function useReservationsService() {
  const { apiFetch } = useApi()

  async function getReservations(params: GetReservationsParams) {
    const query: Record<string, unknown> = {}
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== null) query[key] = value
    }
    return apiFetch<PaginatedResponse<RawReservation>>('/reservations', { query })
  }

  async function getCounts(from: string, to: string) {
    const response = await apiFetch<{ data: ReservationCounts }>('/reservations/counts', {
      query: { from, to },
    })
    return response.data
  }

  async function getDailySummary(date: string) {
    const response = await apiFetch<{ data: ReservationSummary }>('/reservations/summary', {
      query: { date },
    })
    return response.data
  }

  // Réservations confirmées de toute l'entreprise pour une journée (non filtrées par
  // utilisateur, contrairement à getReservations) : sert à la grille de disponibilité
  // des salles/matériels, qui doit refléter l'occupation réelle quel que soit le rôle.
  async function getDayReservations(date: string) {
    const response = await apiFetch<{ data: RawReservation[] }>('/availability/reservations', {
      query: { date },
    })
    return response.data
  }

  async function getAvailableEquipments(dateReservation: string, heureDebut: string, heureFin: string, excludeReservationId?: number) {
    const response = await apiFetch<{ data: AvailableEquipment[] }>('/availability/equipments', {
      query: {
        date_reservation: dateReservation,
        heure_debut: heureDebut,
        heure_fin: heureFin,
        exclude_reservation_id: excludeReservationId,
      },
    })
    return response.data
  }

  async function createReservation(payload: CreateReservationPayload) {
    const response = await apiFetch<{ message: string, data: RawReservation }>('/reservations', {
      method: 'POST',
      body: payload,
    })
    return response.data
  }

  async function updateReservation(id: number, payload: UpdateReservationPayload) {
    const response = await apiFetch<{ message: string, data: RawReservation }>(`/reservations/${id}`, {
      method: 'PUT',
      body: payload,
    })
    return response.data
  }

  async function cancelReservation(id: number) {
    const response = await apiFetch<{ message: string, data: RawReservation }>(`/reservations/${id}/cancel`, {
      method: 'PATCH',
      body: {},
    })
    return response.data
  }

  return {
    getReservations,
    getCounts,
    getDailySummary,
    getDayReservations,
    getAvailableEquipments,
    createReservation,
    updateReservation,
    cancelReservation,
  }
}
