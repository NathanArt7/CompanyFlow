export type TicketStatus = 'OUVERT' | 'EN_COURS' | 'RESOLU' | 'FERME'

export interface RawTicketUser {
  id: number
  nom: string
  prenom: string
  email: string
  deleted_at?: string | null
}

// Reflète le modèle Equipment brut (relation eager-loadée sans transformation
// Resource) : category_id/storage_room_id/assigned_to restent des identifiants,
// pas des objets imbriqués — contrairement à RawEquipment (features/equipments).
// category/storage_room sont en revanche bien chargés (eager-load explicite côté
// TicketService) pour les colonnes Type/Localisation du tableau technicien.
export interface RawTicketEquipment {
  id: number
  nom: string
  code: string
  marque: string
  modele: string
  localisation: string | null
  numero_serie: string | null
  usage_type: 'EMPRUNTABLE' | 'NON_EMPRUNTABLE'
  etat: string
  assigned_to: number | null
  category: { id: number, nom: string } | null
  storage_room: { id: number, nom: string } | null
  deleted_at?: string | null
}

export interface RawTicket {
  id: number
  entreprise_id: number
  equipment_id: number
  user_id: number
  description: string
  statut: TicketStatus
  created_at: string
  updated_at: string
  equipment: RawTicketEquipment | null
  user: RawTicketUser | null
}

export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  total: number
  per_page: number
  last_page: number
}

export interface TicketStats {
  total_ce_mois: number
  en_cours: number
  resolus: number
  restants: number
}

export interface CreateTicketPayload {
  equipment_id: number
  description: string
}
