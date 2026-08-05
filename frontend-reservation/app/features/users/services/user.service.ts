import type {
  CreateUserPayload,
  PaginatedResponse,
  RawRole,
  RawUser,
  RawUserDetail,
  RawUsersPaginatedResponse,
  UpdateUserPayload,
  UserStats,
} from '../type'

export interface PaginateUsersParams {
  search?: string
  role_id?: number
  actif?: boolean
  page?: number
  per_page?: number
  sort?: 'nom' | 'prenom' | 'email' | 'created_at'
  direction?: 'asc' | 'desc'
}

export function useUserService() {
  const { apiFetch } = useApi()

  async function search(query: string): Promise<RawUser[]> {
    const response = await apiFetch<RawUsersPaginatedResponse>('/users', {
      query: { search: query || undefined, per_page: 20 },
    })
    return response.data
  }

  async function paginate(params: PaginateUsersParams): Promise<PaginatedResponse<RawUser>> {
    const query: Record<string, unknown> = {}
    for (const [key, value] of Object.entries(params)) {
      if (value === undefined || value === null || value === '') continue
      // Laravel valide "actif" avec la règle `boolean`, qui accepte 1/0 ou "1"/"0"
      // mais pas les chaînes "true"/"false" produites par la sérialisation par défaut.
      query[key] = typeof value === 'boolean' ? (value ? 1 : 0) : value
    }

    const response = await apiFetch<RawUsersPaginatedResponse>('/users', { query })

    return {
      data: response.data,
      current_page: response.meta.current_page,
      last_page: response.meta.last_page,
      per_page: response.meta.per_page,
      total: response.meta.total,
    }
  }

  async function getStats() {
    const response = await apiFetch<{ data: UserStats }>('/users/stats')
    return response.data
  }

  async function listRoles() {
    const response = await apiFetch<{ data: RawRole[] }>('/roles')
    return response.data
  }

  async function create(payload: CreateUserPayload) {
    return apiFetch<{ message: string }>('/users', {
      method: 'POST',
      body: payload,
    })
  }

  async function update(id: number, payload: UpdateUserPayload) {
    return apiFetch<RawUserDetail>(`/users/${id}`, {
      method: 'PUT',
      body: payload,
    })
  }

  async function updateStatus(id: number, actif: boolean) {
    return apiFetch<RawUserDetail>(`/users/${id}/status`, {
      method: 'PATCH',
      body: { actif },
    })
  }

  async function remove(id: number) {
    return apiFetch<{ message: string }>(`/users/${id}`, {
      method: 'DELETE',
    })
  }

  return { search, paginate, getStats, listRoles, create, update, updateStatus, remove }
}
