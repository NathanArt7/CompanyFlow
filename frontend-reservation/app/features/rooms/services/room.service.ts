import type { CreateRoomPayload, PaginatedResponse, RawRoom, RoomsResourceCollectionResponse, RoomStats } from '../type'

export interface ListRoomsParams {
  search?: string
  type?: 'MEETING' | 'STORAGE'
  statut?: string
  page?: number
  per_page?: number
  sort?: string
  direction?: 'asc' | 'desc'
}

export function useRoomService() {
  const { apiFetch } = useApi()

  async function list() {
    const response = await apiFetch<PaginatedResponse<RawRoom>>('/rooms', {
      query: { per_page: 100, sort: 'nom', direction: 'asc' },
    })
    return response.data
  }

  async function paginate(params: ListRoomsParams): Promise<PaginatedResponse<RawRoom>> {
    const query: Record<string, unknown> = {}
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== null && value !== '') query[key] = value
    }

    const response = await apiFetch<RoomsResourceCollectionResponse<RawRoom>>('/rooms', { query })

    return {
      data: response.data,
      current_page: response.meta.current_page,
      total: response.meta.total,
      per_page: response.meta.per_page,
      last_page: response.meta.last_page,
    }
  }

  async function getStats() {
    const response = await apiFetch<{ data: RoomStats }>('/rooms/stats')
    return response.data
  }

  async function create(payload: CreateRoomPayload) {
    const response = await apiFetch<{ message: string, room: RawRoom }>('/rooms', {
      method: 'POST',
      body: payload,
    })
    return response.room
  }

  async function update(id: number, payload: CreateRoomPayload) {
    const response = await apiFetch<{ message: string, room: RawRoom }>(`/rooms/${id}`, {
      method: 'PUT',
      body: payload,
    })
    return response.room
  }

  async function remove(id: number) {
    return apiFetch<{ message: string }>(`/rooms/${id}`, {
      method: 'DELETE',
    })
  }

  return { list, paginate, getStats, create, update, remove }
}
