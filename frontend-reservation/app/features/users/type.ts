export const ROLE_NAMES = [
  'Super_Administrateur',
  'Administrateur',
  'Super_Employe',
  'Employe',
  'Technicien',
] as const

export type RoleName = typeof ROLE_NAMES[number]

export interface RawUser {
  id: number
  nom: string
  prenom: string
  email: string
  photo: string | null
  actif: boolean
  role: RoleName
  created_at: string
}

export interface RawUserDetail extends RawUser {
  password_changed: boolean
  updated_at: string
}

export interface RawUsersPaginatedResponse {
  data: RawUser[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface RawRole {
  id: number
  nom: RoleName
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface UserStats {
  total: number
  admins: number
  super_employes: number
  employes: number
  techniciens: number
}

export interface CreateUserPayload {
  nom: string
  prenom: string
  email: string
  role_id: number
}

export type UpdateUserPayload = CreateUserPayload

export interface UserFilters {
  search: string
  roleId: number | null
  actif: boolean | null
}
