import type { CreateEquipmentCategoryPayload, EquipmentCategoriesListResponse, PaginatedResponse, RawEquipmentCategory } from '../type'

export interface PaginateEquipmentCategoriesParams {
  search?: string
  page?: number
  per_page?: number
}

export function useEquipmentCategoryService() {
  const { apiFetch } = useApi()

  async function list() {
    const response = await apiFetch<EquipmentCategoriesListResponse<RawEquipmentCategory>>('/equipment-categories', {
      query: { per_page: 50 },
    })
    return response.categories
  }

  async function paginate(params: PaginateEquipmentCategoriesParams): Promise<PaginatedResponse<RawEquipmentCategory>> {
    const query: Record<string, unknown> = {}
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== null && value !== '') query[key] = value
    }

    const response = await apiFetch<EquipmentCategoriesListResponse<RawEquipmentCategory>>('/equipment-categories', { query })

    return {
      data: response.categories,
      current_page: response.pagination.current_page,
      last_page: response.pagination.last_page,
      per_page: response.pagination.per_page,
      total: response.pagination.total,
    }
  }

  async function create(payload: CreateEquipmentCategoryPayload) {
    const response = await apiFetch<{ message: string, category: RawEquipmentCategory }>('/equipment-categories', {
      method: 'POST',
      body: payload,
    })
    return response.category
  }

  async function update(id: number, payload: CreateEquipmentCategoryPayload) {
    const response = await apiFetch<{ message: string, category: RawEquipmentCategory }>(`/equipment-categories/${id}`, {
      method: 'PUT',
      body: payload,
    })
    return response.category
  }

  async function remove(id: number) {
    return apiFetch<{ message: string }>(`/equipment-categories/${id}`, {
      method: 'DELETE',
    })
  }

  return { list, paginate, create, update, remove }
}
