import type {
  CreateTicketPayload,
  PaginatedResponse,
  RawTicket,
  TicketStats,
  TicketStatus,
} from '../type'

export interface GetTicketsParams {
  statut?: TicketStatus
  user_id?: number
  page?: number
  per_page?: number
}

export function useTicketService() {
  const { apiFetch } = useApi()

  async function getTickets(params: GetTicketsParams = {}) {
    const query: Record<string, unknown> = {}
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== null) query[key] = value
    }
    return apiFetch<PaginatedResponse<RawTicket>>('/tickets', { query })
  }

  async function getStats() {
    const response = await apiFetch<{ data: TicketStats }>('/tickets/stats')
    return response.data
  }

  async function create(payload: CreateTicketPayload) {
    const response = await apiFetch<{ message: string, data: RawTicket }>('/tickets', {
      method: 'POST',
      body: payload,
    })
    return response.data
  }

  async function accept(id: number) {
    const response = await apiFetch<{ message: string, data: RawTicket }>(`/tickets/${id}/accept`, {
      method: 'PATCH',
    })
    return response.data
  }

  async function close(id: number, equipmentState: 'FONCTIONNEL' | 'HORS_SERVICE') {
    const response = await apiFetch<{ message: string, data: RawTicket }>(`/tickets/${id}/close`, {
      method: 'PATCH',
      body: { equipment_state: equipmentState },
    })
    return response.data
  }

  return { getTickets, getStats, create, accept, close }
}
