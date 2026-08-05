import type { ActivityModule, PaginatedResponse, RawActivityLog } from '../type'

export interface GetActivityLogsParams {
  module: ActivityModule
  search?: string
  from_date?: string
  to_date?: string
  page?: number
  per_page?: number
}

export function useActivityLogService() {
  const { apiFetch } = useApi()

  async function getLogs(params: GetActivityLogsParams) {
    const query: Record<string, unknown> = {}
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== null && value !== '') query[key] = value
    }
    return apiFetch<PaginatedResponse<RawActivityLog>>('/activity-logs', { query })
  }

  return { getLogs }
}
