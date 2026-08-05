import type { ActivateAccountPayload, LoginResponse, MeResponse, ResetPasswordPayload, UpdateProfileResponse } from '../type'

export function useAuthService() {
  const { apiFetch } = useApi()

  function registerSuperAdmin(payload: { lastName: string, firstName: string, email: string }) {
    return apiFetch<{ message: string }>('/entreprises', {
      method: 'POST',
      body: {
        nom: payload.lastName,
        prenom: payload.firstName,
        email: payload.email,
      },
    })
  }

  function login(email: string, password: string) {
    return apiFetch<LoginResponse>('/login', {
      method: 'POST',
      body: { email, password },
    })
  }

  function activateAccount(payload: ActivateAccountPayload) {
    return apiFetch<{ message: string }>('/account-activation', {
      method: 'POST',
      body: payload,
    })
  }

  function forgotPassword(email: string) {
    return apiFetch<{ message: string }>('/forgot-password', {
      method: 'POST',
      body: { email },
    })
  }

  function resetPassword(payload: ResetPasswordPayload) {
    return apiFetch<{ message: string }>('/reset-password', {
      method: 'POST',
      body: payload,
    })
  }

  function logout() {
    return apiFetch<{ message: string }>('/logout', { method: 'POST' })
  }

  function me() {
    return apiFetch<MeResponse>('/me')
  }

  function changePassword(currentPassword: string, password: string, passwordConfirmation: string) {
    return apiFetch<{ message: string }>('/change-password', {
      method: 'POST',
      body: { current_password: currentPassword, password, password_confirmation: passwordConfirmation },
    })
  }

  async function updateProfile(formData: FormData) {
    // POST + _method=PUT (spoofing Laravel) : un PUT multipart natif ne peuple pas
    // $_FILES côté PHP, nécessaire pour l'upload optionnel de la photo de profil.
    formData.append('_method', 'PUT')
    const response = await apiFetch<UpdateProfileResponse>('/me', {
      method: 'POST',
      body: formData,
    })
    return response.user
  }

  return { registerSuperAdmin, login, activateAccount, forgotPassword, resetPassword, logout, me, changePassword, updateProfile }
}
