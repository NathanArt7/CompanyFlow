import type { CreateEquipmentPayload, EquipmentsListResponse, EquipmentStats, PaginatedResponse, RawEquipment } from '../type'

export interface PaginateEquipmentsParams {
  search?: string
  category_id?: number
  usage_type?: 'EMPRUNTABLE' | 'NON_EMPRUNTABLE'
  etat?: string
  page?: number
  per_page?: number
  sort?: 'nom' | 'code' | 'created_at'
  direction?: 'asc' | 'desc'
}

export function useEquipmentService() {
  const { apiFetch } = useApi()

  async function paginate(params: PaginateEquipmentsParams): Promise<PaginatedResponse<RawEquipment>> {
    const query: Record<string, unknown> = {}
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== null && value !== '') query[key] = value
    }

    const response = await apiFetch<EquipmentsListResponse<RawEquipment>>('/equipments', { query })

    return {
      data: response.equipments,
      current_page: response.pagination.current_page,
      last_page: response.pagination.last_page,
      per_page: response.pagination.per_page,
      total: response.pagination.total,
    }
  }

  async function getStats() {
    const response = await apiFetch<{ data: EquipmentStats }>('/equipments/stats')
    return response.data
  }

  async function create(payload: CreateEquipmentPayload) {
    const response = await apiFetch<{ message: string, equipment: RawEquipment }>('/equipments', {
      method: 'POST',
      body: payload,
    })
    return response.equipment
  }

  async function update(id: number, payload: CreateEquipmentPayload) {
    const response = await apiFetch<{ message: string, equipment: RawEquipment }>(`/equipments/${id}`, {
      method: 'PUT',
      body: payload,
    })
    return response.equipment
  }

  async function updateStatus(id: number, etat: string) {
    const response = await apiFetch<{ message: string, equipment: RawEquipment }>(`/equipments/${id}/status`, {
      method: 'PATCH',
      body: { etat },
    })
    return response.equipment
  }

  async function remove(id: number) {
    return apiFetch<{ message: string }>(`/equipments/${id}`, {
      method: 'DELETE',
    })
  }

  return { paginate, getStats, create, update, updateStatus, remove }
}
