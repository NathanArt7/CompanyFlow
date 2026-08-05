export interface CreateSuperAdminPayload {
  lastName: string
  firstName: string
  email: string
}

export interface LoginResponseUser {
  id: number
  nom: string
  prenom: string
  email: string
  role_id: number
  entreprise_configured: boolean
}

export interface LoginResponse {
  message: string
  token: string
  user: LoginResponseUser
}

export interface MeResponse {
  id: number
  nom: string
  prenom: string
  email: string
  role: string
  photo: string | null
  actif: boolean
  password_changed: boolean
  permissions: string[]
  entreprise_configured: boolean
}

export interface UpdateProfileResponse {
  message: string
  user: MeResponse
}

export interface ActivateAccountPayload {
  token: string
  password: string
  password_confirmation: string
}

export interface ResetPasswordPayload {
  token: string
  password: string
  password_confirmation: string
}