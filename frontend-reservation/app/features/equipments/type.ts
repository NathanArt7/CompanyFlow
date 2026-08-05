export interface RawEquipmentCategory {
  id: number
  nom: string
  description: string | null
}

export interface RawEquipmentRoom {
  id: number
  nom: string
  code: string
  localisation: string
}

export interface RawEquipmentUser {
  id: number
  nom: string
  prenom: string
  email: string
}

export interface RawEquipment {
  id: number
  nom: string
  code: string
  marque: string
  modele: string
  numero_serie: string | null
  usage_type: 'EMPRUNTABLE' | 'NON_EMPRUNTABLE'
  etat: string
  category: RawEquipmentCategory | null
  storage_room: RawEquipmentRoom | null
  assigned_user: RawEquipmentUser | null
  localisation: string | null
  created_at: string
  updated_at: string
}

// EquipmentController::index() renvoie {equipments, pagination} — forme différente
// de /rooms ({data, meta}) et de /reservations (paginator brut à plat).
export interface EquipmentsListResponse<T> {
  equipments: T[]
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

export interface EquipmentStats {
  total: number
  borrowable: number
  non_borrowable: number
  borrowable_occupied: number
  broken: number
  in_maintenance: number
}

export interface CreateEquipmentPayload {
  category_id: number
  nom: string
  code: string
  marque: string
  modele: string
  numero_serie?: string | null
  usage_type: 'EMPRUNTABLE' | 'NON_EMPRUNTABLE'
  etat: string
  storage_room_id?: number | null
  localisation?: string | null
  assigned_to?: number | null
}

export interface EquipmentFilters {
  search: string
  categoryId: number | null
  usageType: 'EMPRUNTABLE' | 'NON_EMPRUNTABLE' | null
  etat: string | null
}
