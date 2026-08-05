export type ActivityModule = 'RESERVATION' | 'SALLE' | 'EQUIPEMENT' | 'UTILISATEUR' | 'TICKET'

export interface RawActivityLogUser {
  id: number
  nom: string
  prenom: string
  deleted_at?: string | null
}

export interface RawActivityLog {
  id: number
  module: ActivityModule
  action: string
  description: string
  user: RawActivityLogUser | null
  created_at: string
}

export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  total: number
  per_page: number
  last_page: number
}
