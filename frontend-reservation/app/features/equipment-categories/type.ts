export interface RawEquipmentCategory {
  id: number
  nom: string
  description: string | null
  equipments_count?: number
  created_at: string
  updated_at: string
}

export interface EquipmentCategoriesListResponse<T> {
  categories: T[]
  pagination: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface CreateEquipmentCategoryPayload {
  nom: string
  description?: string | null
}
